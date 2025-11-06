<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\SQLite3\Table;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class BPDPanel extends BaseController
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

    protected function getScheduleById(int $scheduleId)
    {
        return $this->db->table('schedules')
            ->where('id_schedule', $scheduleId)
            ->get()
            ->getRow();
    }

    public function index()
    {
        return view('bpd/index', [
            'data' => $this->db->table('pemilih')->get()->getResultArray()
        ]);
    }

    public function users()
    {
        return view('bpd/panitia', [
            'data' => $this->db->table('users')->whereIn('role', ['panitia', 'bpd'])->get()->getResultArray()
        ]);
    }

    public function tambah_user()
    {
        $this->db->table('users')->insert([
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'role' => $this->request->getVar('role')
        ]);

        return redirect()->to(base_url('BPDPanel/Panitia'))->with('type-status', 'success')->with('message', 'Berhasil menambah data');
    }

    public function edit_user()
    {
        $password = $this->request->getVar('password');

        if (!is_null($password)) {
            $this->db->table('users')->where('id_user', $this->request->getVar('id_user'))->update(['password' => password_hash($password, PASSWORD_BCRYPT)]);
        }

        $this->db->table('users')->where('id_user', $this->request->getVar('id_user'))->update([
            'username' => $this->request->getVar('username'),
            'role' => $this->request->getVar('role')
        ]);

        return redirect()->to(base_url('BPDPanel/Panitia'))->with('type-status', 'success')->with('message', 'Berhasil menambah data');
    }

    public function hapus_user($id)
    {
        $this->db->table('users')->where('id_user', $id)->delete();

        return redirect()->to(base_url('BPDPanel/Panitia'))->with('type-status', 'success')->with('message', 'Berhasil menghapus data');
    }

    public function calon()
    {
        return view('bpd/calon', [
            'data' => $this->db->table('candidates')->get()->getResultArray()
        ]);
    }

    public function reset_pemilihan()
    {
        $this->db->table('schedules')->truncate();
        $this->db->table('votes')->truncate();

        return redirect()->to(base_url('BPDPanel/prosesPemilihan'))->with('type-status', 'success')->with('message', 'Berhasil mereset data');
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

        return view('bpd/proses_pemilihan', [
            'pemilihanAktif' => $pemilihanAktif,
            'chartData' => $chartData,
            'votingData' => $votingData,
            'countdownTarget' => $countdownTarget,
            'jadwal' => $jadwal
        ]);
    }

    public function jadwal()
    {
        $schedules = $this->db->table('schedules')
            ->orderBy('start_time', 'DESC')
            ->get()
            ->getResultArray();

        return view('bpd/jadwal_pemilihan', [
            'schedules' => $schedules,
            'currentTime' => time()
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

        return redirect()->to('/BPDPanel/prosesPemilihan')->with('message', 'Jadwal pemilihan berhasil diatur.');
    }

    public function laporan($scheduleId = null)
    {
        if ($scheduleId !== null) {
            $jadwal = $this->getScheduleById((int) $scheduleId);

            if (!$jadwal) {
                throw PageNotFoundException::forPageNotFound('Jadwal tidak ditemukan.');
            }
        } else {
            $jadwal = $this->getLatestSchedule();
        }

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

    public function laporanPemilih()
    {
        $totalPemilih = $this->db->table('pemilih')->countAllResults();
        $totalValidated = $this->db->table('pemilih')->where('validate', 1)->countAllResults();
        $totalNotValidated = $this->db->table('pemilih')->where('validate', 0)->countAllResults();
        $totalInvalid = $this->db->table('pemilih')->where('validate', 2)->countAllResults();

        $pemilih = $this->db->table('pemilih')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return view('reports/pemilih', [
            'totalPemilih' => $totalPemilih,
            'totalValidated' => $totalValidated,
            'totalNotValidated' => $totalNotValidated,
            'totalInvalid' => $totalInvalid,
            'pemilih' => $pemilih,
        ]);
    }
}
