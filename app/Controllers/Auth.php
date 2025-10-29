<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function panitia_login()
    {
        return view('auth/panitia');
    }

    public function panitia_login_auth()
    {
        $check = $this->db->table('users')->where('username', $this->request->getVar('username'))->get()->getRowArray();

        if (!$check) {
            return redirect()->to(route_to('Auth/Panitia'))->with('type-status', 'error')->with('message', 'Username tidak ditemukan');
        }

        $verify = password_verify($this->request->getVar('password'), $check['password']);

        if (!$verify) {
            return redirect()->to(route_to('Auth/Panitia'))->with('type-status', 'error')->with('message', 'Password salah');
        }

        session()->set([
            'dataPanitia' => $check,
            'panitia_logged_in' => true
        ]);

        return redirect()->to(route_to('PanitiaPanel'))->with('type-status', 'success')->with('message', 'Berhasil Login');
    }

    public function panitia_logout()
    {
        session()->remove('panitia_logged_in');

        return redirect()->to(route_to('Auth/Panitia'))->with('type-status', 'success')->with('message', 'Berhasil Keluar');
    }

    public function bpd()
    {
        return view('auth/bpd');
    }

    public function bpd_login_auth()
    {
        $check = $this->db->table('users')->where('username', $this->request->getVar('username'))->get()->getRowArray();

        if (!$check) {
            return redirect()->to(route_to('Auth/BPD'))->with('type-status', 'error')->with('message', 'Username tidak ditemukan');
        }

        $verify = password_verify($this->request->getVar('password'), $check['password']);

        if (!$verify) {
            return redirect()->to(route_to('Auth/BPD'))->with('type-status', 'error')->with('message', 'Password salah');
        }

        session()->set([
            'dataBPD' => $check,
            'bpd_logged_in' => true
        ]);

        return redirect()->to(route_to('BPDPanel'))->with('type-status', 'success')->with('message', 'Berhasil Login');
    }

    public function bpd_logout()
    {
        session()->remove('bpd_logged_in');

        return redirect()->to(route_to('Auth/bpd'))->with('type-status', 'success')->with('message', 'Berhasil Keluar');
    }
}
