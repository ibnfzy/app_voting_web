<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        // Ambil data calon
        $dataCalon = $db->table('candidates')->get()->getResultArray();

        // Ambil jadwal (hanya ambil 1, misal pakai jadwal terbaru)
        $jadwal = $db->table('schedules')->orderBy('id_schedule', 'DESC')->get()->getRowArray();

        $now = date('Y-m-d H:i:s');
        $jadwalStatus = [];

        if (!$jadwal) {
            // Belum ada jadwal
            $jadwalStatus = [
                'status' => 'no_schedule'
            ];
        } elseif ($now < $jadwal['start_time']) {
            // Belum mulai → countdown
            $jadwalStatus = [
                'status'     => 'upcoming',
                'start_time' => $jadwal['start_time'],
                'end_time'   => $jadwal['end_time'],
            ];
        } elseif ($now >= $jadwal['start_time'] && $now <= $jadwal['end_time']) {
            // Sedang aktif
            $jadwalStatus = [
                'status'     => 'active',
                'start_time' => $jadwal['start_time'],
                'end_time'   => $jadwal['end_time'],
            ];
        } else {
            // Sudah selesai
            $jadwalStatus = [
                'status'     => 'finished',
                'start_time' => $jadwal['start_time'],
                'end_time'   => $jadwal['end_time'],
            ];
        }

        return view('main_page', [
            'dataCalon'     => $dataCalon,
            'jadwalStatus'  => $jadwalStatus
        ]);
    }

    public function options()
    {
        return $this->response
        ->setStatusCode(200)
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
