<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman publik
$routes->get('/', 'Home::index');
$routes->get('struktur', 'Usada::index');
$routes->get('cari-bahan', 'Usada::cariBahan');

// Autentikasi Admin
$routes->get('admin/login', 'Auth::login');
$routes->post('admin/login', 'Auth::attempt');

// Halaman Admin yang membutuhkan autentikasi
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
  $routes->get('/', 'Admin\Dashboard::index');
  $routes->post('logout', 'Auth::logout');
});
