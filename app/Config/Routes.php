<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->options('(:any)', 'Home::options');

$routes->group('Auth', function (RouteCollection $routes) {
  $routes->get('Panitia', 'Auth::panitia_login');
  $routes->post('Panitia', 'Auth::panitia_login_auth');
  $routes->get('Panitia/s', 'Auth::panitia_logout');

  $routes->get('BPD', 'Auth::bpd');
  $routes->post('BPD', 'Auth::bpd_login_auth');
  $routes->get('BPD/s', 'Auth::bpd_logout');
});

$routes->group('BPDPanel', function (RouteCollection $routes) {
  $routes->get('/', 'BPDPanel::index');
  $routes->get('Panitia', 'BPDPanel::users');
  $routes->get('Panitia/(:num)', 'BPDPanel::hapus_user/$1');
  $routes->post('Panitia', 'BPDPanel::tambah_user');
  $routes->post('Panitia/Update', 'BPDPanel::edit_user');
  $routes->get('Calon', 'BPDPanel::calon');
  $routes->get('prosesPemilihan', 'BPDPanel::prosesPemilihan');
  $routes->get('getVotingData', 'BPDPanel::getVotingData');
  $routes->post('aturJadwal', 'BPDPanel::aturJadwal');
  $routes->get('ResetPemilhan', 'BPDPanel::reset_pemilihan');
  $routes->get('Laporan', 'BPDPanel::laporan');
  $routes->get('Laporan/Pemilih', 'BPDPanel::laporanPemilih');
});


$routes->group('PanitiaPanel', function (RouteCollection $routes) {
  $routes->get('/', 'PanitiaPanel::index');
  $routes->get('Pemilih/Validate/(:num)', 'PanitiaPanel::ubah_akses_kode/$1');
  $routes->get('Pemilih/TidakValid/(:num)', 'PanitiaPanel::pemilih_tidak_valid/$1');
  $routes->get('Pemilih/(:num)', 'PanitiaPanel::hapus_pemilih/$1');
  $routes->get('Calon', 'PanitiaPanel::calon');
  $routes->get('Calon/(:num)', 'PanitiaPanel::hapus_calon/$1');
  $routes->post('Calon', 'PanitiaPanel::tambah_calon');
  $routes->post('Calon/Update', 'PanitiaPanel::edit_calon');
  $routes->get('prosesPemilihan', 'PanitiaPanel::prosesPemilihan');
  $routes->get('getVotingData', 'PanitiaPanel::getVotingData');
  $routes->post('aturJadwal', 'PanitiaPanel::aturJadwal');
  $routes->get('ResetPemilhan', 'PanitiaPanel::reset_pemilihan');
  $routes->get('Laporan', 'PanitiaPanel::laporan');
});

$routes->group('API', function (RouteCollection $routes) {
  $routes->post('auth/login', 'API::login');
  $routes->post('auth/register-user', 'API::register');
  $routes->post('auth/register-pemilih', 'API::register_pemilih');

  $routes->get('candidates', 'API::candidates');
  $routes->get('candidates/(:num)', 'API::candidates_profile/$1');

  $routes->post('vote/status', 'API::checkVotingStatus');
  $routes->post('vote', 'API::vote');
  $routes->get('vote/schedule', 'API::getSchedule');
  $routes->get('vote/results', 'API::getResults');
});
