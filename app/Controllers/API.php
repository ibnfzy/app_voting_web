<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class API extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    protected function getLatestSchedule(): ?array
    {
        return $this->db->table('schedules')
            ->orderBy('start_time', 'DESC')
            ->get(1)
            ->getRowArray();
    }

    protected function getPemilihVoteForSchedule(int $pemilihId, ?array $schedule = null): ?array
    {
        $schedule = $schedule ?? $this->getLatestSchedule();

        if (!$schedule) {
            return null;
        }

        return $this->db->table('votes')
            ->where('pemilih_id', $pemilihId)
            ->where('schedule_id', $schedule['id_schedule'])
            ->get()
            ->getRowArray();
    }

    protected function apiError(string $message, int $code = 400)
    {
        return \Config\Services::response()
            ->setStatusCode($code)
            ->setJSON([
                'status' => 'error',
                'message' => $message,
                'code' => $code
            ]);
    }

    public function login()
    {
        $json = $this->request->getJSON(true);
        $nik = $json['nik'] ?? '';

        if (!$nik) {
            return $this->apiError('NIK harus diisi.', 400);
        }

        $pemilih = $this->db->table('pemilih')
            ->where('nik', $nik)
            ->get()
            ->getRowArray();

        if (!$pemilih) {
            return $this->apiError('Data pemilih tidak ditemukan.', 404);
        }

        if ((int) ($pemilih['validate'] ?? 0) !== 1) {
            return $this->apiError('invalid account', 400);
        }

        $vote = $this->getPemilihVoteForSchedule((int) $pemilih['id_pemilih']);
        $pemilih['hasVoted'] = (bool) $vote;
        $pemilih['votedFor'] = $vote ? (int) $vote['candidate_id'] : null;

        return $this->response->setJSON([
            'status' => 'success',
            'user' => [
                'nik'        => $pemilih['nik'],
                'role'       => 'pemilih',
                'pemilih'    => $pemilih
            ]
        ]);
    }

    public function register()
    {
        try {
            $username = $this->request->getVar('username');

            if (!$username) {
                return $this->apiError('Username wajib diisi.', 400);
            }

            // Cek apakah username sudah ada
            $check = $this->db->table('users')
                ->where('username', $username)
                ->get()
                ->getRowArray();

            if ($check) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'username not available',
                    'code' => 400
                ]);
            }

            // Simpan user baru
            $this->db->table('users')->insert([
                'username' => $username,
                'role' => 'pemilih',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $userId = $this->db->insertID();

            return $this->response->setJSON([
                'status' => 'user created',
                'user_id' => $userId
            ]);
        } catch (\Throwable $e) {
            return $this->apiError('Terjadi kesalahan saat mendaftarkan pengguna.', 500);
        }
    }

    public function register_pemilih()
    {
        try {
            $email = $this->request->getVar('email');
            $nik = $this->request->getVar('nik');
            $checkEmail = $this->db->table('pemilih')->where('email', $email)->get()->getRowArray();
            $checkNik = $this->db->table('pemilih')->where('nik', $nik)->get()->getRowArray();

            if ($checkEmail) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'email not available',
                    'code' => 400
                ]);
            }

            if ($checkNik) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'nik not available',
                    'code' => 400
                ]);
            }

            $fileKtp = $this->request->getFile('file_ktp');
            $namaFileKtp = null;

            if ($fileKtp && $fileKtp->isValid() && !$fileKtp->hasMoved()) {
                $uploadPath = 'uploads';

                $namaFileKtp = $fileKtp->getRandomName();
                $fileKtp->move($uploadPath, $namaFileKtp);
            }

            $this->db->table('pemilih')->insert([
                'nik'           => $nik,
                'name'          => $this->request->getVar('name'),
                'tempat_lahir'  => $this->request->getVar('tempat_lahir'),
                'tanggal_lahir' => $this->request->getVar('tanggal_lahir'),
                'jenis_kelamin' => $this->request->getVar('jenis_kelamin'),
                'alamat'        => $this->request->getVar('alamat'),
                'rt'            => $this->request->getVar('rt'),
                'rw'            => $this->request->getVar('rw'),
                'kelurahan'     => $this->request->getVar('kelurahan'),
                'kecamatan'     => $this->request->getVar('kecamatan'),
                'kabupaten'     => $this->request->getVar('kabupaten'),
                'provinsi'      => $this->request->getVar('provinsi'),
                'email'         => $email,
                'nama_file_ktp' => $namaFileKtp,
            ]);

            $pemilihId = $this->db->insertID();
            $getPemilih = $this->db->table('pemilih')->where('id_pemilih', $pemilihId)->get()->getRowArray();
            $vote = $this->getPemilihVoteForSchedule((int) $getPemilih['id_pemilih']);
            $getPemilih['hasVoted'] = (bool) $vote;
            $getPemilih['votedFor'] = $vote ? (int) $vote['candidate_id'] : null;

            return $this->response->setJSON([
                'status' => 'registration successful',
                'pemilih' => $getPemilih
            ]);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Internal server error',
                'code' => 400
            ]);
        }
    }

    public function parseList($value)
    {
        if (is_array($value)) {
            // kalau sudah array, langsung kembalikan
            return $value;
        }

        if (is_string($value)) {
            // cari isi <li>... </li>
            preg_match_all('/<li>(.*?)<\/li>/i', $value, $matches);

            if (!empty($matches[1])) {
                // jika ada <li>, ambil isinya
                return array_map('trim', $matches[1]);
            } else {
                // kalau tidak ada <li>, tetap jadikan array
                return [$value];
            }
        }

        // fallback
        return [];
    }

    public function candidates()
    {
        $get = $this->db->table('candidates')->get()->getResultArray();

        $data = [];

        foreach ($get as $item) {
            $misi = $this->parseList($item['misi']);
            $visi = $this->parseList($item['visi']);

            $data[] = [
                'id_candidate' => $item['id_candidate'],
                'name' => $item['name'],
                'photo' => base_url('uploads/' . $item['photo']),
                'visi' => $visi,
                'misi' => $misi
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function candidates_profile($id)
    {
        $get = $this->db->table('candidates')->where('id_candidate', $id)->get()->getRowArray();

        $misi = $this->parseList($get['misi']);
        $visi = $this->parseList($get['visi']);

        $data = [
            'id_candidate' => $get['id_candidate'],
            'name' => $get['name'],
            'photo' => base_url('uploads/' . $get['photo']),
            'visi' => $visi,
            'misi' => $misi
        ];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function hasVoted($pemilih_id)
    {
        return $this->getPemilihVoteForSchedule((int) $pemilih_id) !== null;
    }

    public function checkVotingStatus()
    {
        $pemilihId = $this->request->getJSON(true)['pemilih_id'] ?? null;

        if (!$pemilihId) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'pemilih_id harus disertakan.',
                'code'    => 400
            ])->setStatusCode(400);
        }

        // Ambil jadwal pemilihan
        $schedule = $this->getLatestSchedule();

        if (!$schedule) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Jadwal pemilihan tidak ditemukan.',
                'code'    => 400
            ])->setStatusCode(400);
        }

        $now = date('Y-m-d H:i:s');
        $canVote = ($now >= $schedule['start_time'] && $now <= $schedule['end_time']);

        // Cek apakah sudah pernah voting
        $vote = $this->getPemilihVoteForSchedule((int) $pemilihId, $schedule);

        $hasVoted = $vote ? true : false;

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'canVote'  => $canVote,
                'hasVoted' => $hasVoted,
                'schedule' => [
                    'id_schedule' => (int) $schedule['id_schedule'],
                    'start_time'  => date(DATE_ATOM, strtotime($schedule['start_time'])),
                    'end_time'    => date(DATE_ATOM, strtotime($schedule['end_time'])),
                    'description' => $schedule['description']
                ],
                'message' => $canVote
                    ? ($hasVoted ? 'Anda sudah memilih' : 'Voting tersedia')
                    : 'Voting belum dibuka atau sudah ditutup'
            ]
        ]);
    }

    public function vote()
    {
        $pemilihId   = $this->request->getVar('pemilih_id');
        $candidateId = $this->request->getVar('candidate_id');
        $now         = time(); // pakai timestamp biar lebih aman

        // Ambil jadwal aktif terbaru
        $schedule = $this->getLatestSchedule();

        if (!$schedule) {
            return $this->response->setJSON([
                'status'  => 'no schedule',
                'message' => 'Tidak ada jadwal pemilihan yang tersedia.',
                'code'    => 400
            ]);
        }

        $start = strtotime($schedule['start_time']);
        $end   = strtotime($schedule['end_time']);

        // Cek apakah voting sedang aktif
        if ($now < $start || $now > $end) {
            return $this->response->setJSON([
                'status'  => 'voting closed',
                'message' => 'Sesi pemilihan belum dimulai atau telah berakhir.',
                'code'    => 400
            ]);
        }

        // Cek apakah pemilih sudah melakukan voting
        $existingVote = $this->getPemilihVoteForSchedule((int) $pemilihId, $schedule);

        if ($existingVote) {
            return $this->response->setJSON([
                'status'  => 'already voted',
                'message' => 'Anda sudah melakukan voting sebelumnya.',
                'code'    => 400
            ]);
        }

        // Simpan voting
        $saved = $this->db->table('votes')->insert([
            'pemilih_id'   => (int) $pemilihId,
            'candidate_id' => (int) $candidateId,
            'schedule_id'  => $schedule['id_schedule'],
            'voted_at'     => date('Y-m-d H:i:s', $now)
        ]);

        if ($saved) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Vote berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan voting.',
                'code'    => 400
            ]);
        }
    }

    public function getSchedule()
    {
        $schedule = $this->getLatestSchedule();

        if (!$schedule) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Jadwal pemilihan tidak ditemukan.',
                'code'    => 400
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id_schedule' => (int) $schedule['id_schedule'],
                'start_time'  => date(DATE_ATOM, strtotime($schedule['start_time'])),
                'end_time'    => date(DATE_ATOM, strtotime($schedule['end_time'])),
                'description' => $schedule['description']
            ]
        ]);
    }

    public function getResults()
    {
        $schedule = $this->getLatestSchedule();

        $joinCondition = 'v.candidate_id = c.id_candidate';
        if ($schedule) {
            $joinCondition .= ' AND v.schedule_id = ' . (int) $schedule['id_schedule'];
        } else {
            $joinCondition .= ' AND 1 = 0';
        }

        $results = $this->db->table('candidates c')
            ->select('c.id_candidate AS candidate_id, c.name AS candidate_name, COUNT(v.id_vote) AS vote_count')
            ->join('votes v', $joinCondition, 'left')
            ->groupBy('c.id_candidate')
            ->orderBy('vote_count', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $results
        ]);
    }

    public function getWinner()
    {
        $schedule = $this->getLatestSchedule();

        $joinCondition = 'v.candidate_id = c.id_candidate';
        if ($schedule) {
            $joinCondition .= ' AND v.schedule_id = ' . (int) $schedule['id_schedule'];
        } else {
            $joinCondition .= ' AND 1 = 0';
        }

        $results = $this->db->table('candidates c')
            ->select('c.id_candidate AS candidate_id, c.name AS candidate_name, c.photo, c.visi, c.misi, COUNT(v.id_vote) AS vote_count')
            ->join('votes v', $joinCondition, 'left')
            ->groupBy('c.id_candidate')
            ->orderBy('vote_count', 'DESC')
            ->get()
            ->getResultArray();

        if (!$results) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Belum ada kandidat yang terdaftar.',
                'data'    => []
            ]);
        }

        $highestVotes = (int) ($results[0]['vote_count'] ?? 0);

        $winners = array_map(function ($candidate) {
            $candidate['visi'] = $this->parseList($candidate['visi']);
            $candidate['misi'] = $this->parseList($candidate['misi']);
            $candidate['photo'] = $candidate['photo']
                ? base_url('uploads/' . $candidate['photo'])
                : null;

            return $candidate;
        }, array_filter($results, static function ($candidate) use ($highestVotes) {
            return (int) $candidate['vote_count'] === $highestVotes;
        }));

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => array_values($winners)
        ]);
    }
}