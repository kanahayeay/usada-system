<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman publik
$routes->get('/', 'Home::index');
$routes->get('struktur', 'Usada::index');
$routes->get('statistik', 'Statistik::index');
$routes->get('glosarium', 'Glosarium::index');
$routes->get('cari-bahan', 'Usada::cariBahan');

// Autentikasi Admin
$routes->get('admin/login', 'Auth::login');
$routes->post('admin/login', 'Auth::attempt');

// Halaman Admin yang membutuhkan autentikasi
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->post('logout', 'Auth::logout');

    // Pengelolaan Glosarium
    $routes->get('glosarium', 'Admin\Glosarium::index');
    $routes->get('glosarium/tambah', 'Admin\Glosarium::tambah');
    $routes->post('glosarium/simpan', 'Admin\Glosarium::simpan');
    $routes->get('glosarium/edit/(:num)', 'Admin\Glosarium::edit/$1');
    $routes->post('glosarium/update/(:num)', 'Admin\Glosarium::update/$1');
    $routes->post('glosarium/hapus/(:num)', 'Admin\Glosarium::hapus/$1');

    
});