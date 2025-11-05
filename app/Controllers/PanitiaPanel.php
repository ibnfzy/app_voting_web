<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PanitiaPanel extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    protected function getLatestSchedule()
    {
        return $this->db->table('schedules')
            ->orderBy('start_time', 'DESC')
            ->get(1)
            ->getRow();
    }

    public function index()
    {
        return view('panitia/index', [
            'data' => $this->db->table('pemilih')->get()->getResultArray()
        ]);
    }

    public function hapus_pemilih($id)
    {
        $this->db->table('pemilih')->where('id_pemilih', $id)->delete();

        return redirect()->to(base_url('PanitiaPanel/'))->with('type-status', 'success')->with('message', 'Berhasil mengubah data');
    }

    public function ubah_akses_kode($id)
    {
        $pemilih = $this->db->table('pemilih')->where('id_pemilih', $id)->get()->getRowArray();

        if (!$pemilih) {
            return redirect()->to(base_url('PanitiaPanel/'))
                ->with('type-status', 'error')
                ->with('message', 'Data pemilih tidak ditemukan');
        }

        $this->db->table('pemilih')->where('id_pemilih', $id)->update([
            'validate' => 1
        ]);

        return redirect()->to(base_url('PanitiaPanel/'))->with('type-status', 'success')->with('message', 'Berhasil memvalidasi Pemilih');
    }

    public function pemilih_tidak_valid($id)
    {
        $pemilih = $this->db->table('pemilih')->where('id_pemilih', $id)->get()->getRowArray();

        if (!$pemilih) {
            return redirect()->to(base_url('PanitiaPanel/'))
                ->with('type-status', 'error')
                ->with('message', 'Data pemilih tidak ditemukan');
        }

        $this->db->table('pemilih')->where('id_pemilih', $id)->update([
            'validate' => 2
        ]);

        return redirect()->to(base_url('PanitiaPanel/'))->with('type-status', 'success')->with('message', 'Status pemilih diperbarui menjadi tidak valid');
    }

    public function calon()
    {
        return view('panitia/calon', [
            'data' => $this->db->table('candidates')->get()->getResultArray()
        ]);
    }

    public function tambah_calon()
    {
        $file = $this->request->getFile('photo');
        $fileName = $file->getRandomName();
        $file->move('uploads', $fileName);

        $this->db->table('candidates')->insert([
            'name' => $this->request->getVar('name'),
            'photo' => $fileName,
            'visi' => $this->request->getVar('visi'),
            'misi' => $this->request->getVar('misi')
        ]);

        return redirect()->to(route_to('PanitiaPanel::calon'))->with('type-status', 'success')->with('message', 'Berhasil menambahkan data');
    }

    public function edit_calon()
    {
        $file = $this->request->getFile('photo');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move('uploads', $fileName);

            $this->db->table('candidates')->where('id_candidate', $this->request->getVar('id_candidate'))->update([
                'photo' => $fileName
            ]);
        }

        $this->db->table('candidates')->where('id_candidate', $this->request->getVar('id_candidate'))->update([
            'name' => $this->request->getVar('name'),
            'visi' => $this->request->getVar('visi'),
            'misi' => $this->request->getVar('misi')
        ]);

        return redirect()->to(route_to('PanitiaPanel::calon'))->with('type-status', 'success')->with('message', 'Berhasil mengubah data');
    }

    public function hapus_calon($id)
    {
        $this->db->table('candidates')->where('id_candidate', $id)->delete();

        return redirect()->to(route_to('PanitiaPanel::calon'))->with('type-status', 'success')->with('message', 'Berhasil menghapus data');
    }

    public function reset_pemilihan()
    {
        $this->db->table('schedules')->truncate();
        $this->db->table('votes')->truncate();

        return redirect()->to(base_url('PanitiaPanel/prosesPemilihan'))->with('type-status', 'success')->with('message', 'Berhasil mereset data');
    }

    public function prosesPemilihan()
    {
        // Ambil jadwal aktif
        $jadwal = $this->getLatestSchedule();

        $now = time(); // timestamp sekarang (integer)
        $pemilihanAktif = false;
        $countdownTarget = null;

        if ($jadwal) {
            $start = strtotime($jadwal->start_time);
            $end   = strtotime($jadwal->end_time);

            if ($now >= $start && $now <= $end) {
                $pemilihanAktif = true;
            } elseif ($now < $start) {
                $countdownTarget = date('Y-m-d\TH:i:s', $start); // ISO 8601 biar JS aman
            }
        }

        $scheduleId = $jadwal ? $jadwal->id_schedule : null;
        $voteJoinCondition = 'votes.candidate_id = candidates.id_candidate';
        if ($scheduleId) {
            $voteJoinCondition .= ' AND votes.schedule_id = ' . (int) $scheduleId;
        } else {
            $voteJoinCondition .= ' AND 1 = 0';
        }

        // Data Chart: jumlah suara per calon
        $chartData = $this->db->table('candidates')
            ->select('candidates.id_candidate, candidates.name, candidates.photo, candidates.visi, candidates.misi, COUNT(votes.id_vote) AS jumlah')
            ->join('votes', $voteJoinCondition, 'left')
            ->groupBy('candidates.id_candidate')
            ->get()->getResultArray();

        // Data Voting Table: daftar pemilih yang sudah voting
        $votingQuery = $this->db->table('votes')
            ->select('pemilih.nik, pemilih.name AS nama, votes.voted_at, candidates.name AS nama_calon')
            ->join('pemilih', 'pemilih.id_pemilih = votes.pemilih_id', 'left')
            ->join('candidates', 'candidates.id_candidate = votes.candidate_id', 'left');

        if ($scheduleId) {
            $votingQuery->where('votes.schedule_id', $scheduleId);
        } else {
            $votingQuery->where('1 = 0');
        }

        $votingData = $votingQuery
            ->orderBy('votes.voted_at', 'DESC')
            ->get()->getResultArray();

        return view('panitia/proses_pemilihan', [
            'pemilihanAktif' => $pemilihanAktif,
            'chartData' => $chartData,
            'votingData' => $votingData,
            'countdownTarget' => $countdownTarget,
            'jadwal' => $jadwal
        ]);
    }

    // Endpoint untuk polling data Chart dan DataTable (AJAX)
    public function getVotingData()
    {
        // Data Chart: jumlah suara per calon
        $jadwal = $this->getLatestSchedule();
        $scheduleId = $jadwal ? $jadwal->id_schedule : null;

        $voteJoinCondition = 'votes.candidate_id = candidates.id_candidate';
        if ($scheduleId) {
            $voteJoinCondition .= ' AND votes.schedule_id = ' . (int) $scheduleId;
        } else {
            $voteJoinCondition .= ' AND 1 = 0';
        }

        $chartData = $this->db->table('candidates')
            ->select('candidates.id_candidate, candidates.name, candidates.photo, candidates.visi, candidates.misi, COUNT(votes.id_vote) AS jumlah')
            ->join('votes', $voteJoinCondition, 'left')
            ->groupBy('candidates.id_candidate')
            ->get()->getResultArray();

        // Data Voting Table: daftar pemilih yang sudah voting
        $votingQuery = $this->db->table('votes')
            ->select('pemilih.nik, pemilih.name AS nama, votes.voted_at, candidates.name AS nama_calon')
            ->join('pemilih', 'pemilih.id_pemilih = votes.pemilih_id', 'left')
            ->join('candidates', 'candidates.id_candidate = votes.candidate_id', 'left');

        if ($scheduleId) {
            $votingQuery->where('votes.schedule_id', $scheduleId);
        } else {
            $votingQuery->where('1 = 0');
        }

        $votingData = $votingQuery
            ->orderBy('votes.voted_at', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'chartData' => array_values($chartData),
            'votingData' => array_values($votingData)
        ]);
    }

    // Atur jadwal voting dari modal
    public function aturJadwal()
    {
        $start = $this->request->getPost('start_time');
        $end = $this->request->getPost('end_time');

        $db = \Config\Database::connect();
        $this->db->table('schedules')->insert([
            'start_time' => $start,
            'end_time'   => $end,
            'description' => 'Jadwal Pemilihan ditentukan oleh Panitia'
        ]);

        return redirect()->to('/PanitiaPanel/prosesPemilihan')->with('message', 'Jadwal pemilihan berhasil diatur.');
    }

    public function laporan()
    {
        $jadwal = $this->getLatestSchedule();
        $scheduleId = $jadwal ? $jadwal->id_schedule : null;

        $totalPemilih = $this->db->table('pemilih')->countAllResults();
        $totalVotes = $scheduleId
            ? $this->db->table('votes')->where('schedule_id', $scheduleId)->countAllResults()
            : 0;

        $voteJoinCondition = 'v.candidate_id = c.id_candidate';
        if ($scheduleId) {
            $voteJoinCondition .= ' AND v.schedule_id = ' . (int) $scheduleId;
        } else {
            $voteJoinCondition .= ' AND 1 = 0';
        }

        $hasil = $this->db->table('candidates c')
            ->select('c.id_candidate, c.name, c.visi, c.misi, c.photo, COUNT(v.id_vote) as jumlah')
            ->join('votes v', $voteJoinCondition, 'left')
            ->groupBy('c.id_candidate')
            ->get()
            ->getResult();

        return view('bpd/laporan_pemilihan', [
            'jadwal' => $jadwal,
            'totalPemilih' => $totalPemilih,
            'totalVotes' => $totalVotes,
            'hasil' => $hasil
        ]);
    }
}