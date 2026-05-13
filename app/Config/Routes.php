<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->get('/auth/login', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

$routes->group('employe', ['filter' => 'role:employe'], function($routes) {
    $routes->get('dashboard', 'Employe::dashboard');
    $routes->get('conge/create', 'Employe::create');
    $routes->post('conge/create', 'Employe::store');
    $routes->get('conges', 'Employe::demandes');
    $routes->post('conges/cancel', 'Employe::cancelDemande');
});

$routes->group('rh', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'RhController::index');
    $routes->post('approuver/(:num)', 'RhController::approuver/$1');
    $routes->post('refuser/(:num)', 'RhController::refuser/$1');
    $routes->get('soldes', 'RhController::soldesEmployes');
});

$routes->group('admin', ['filter' => 'role:admin'], function($routes) {
    $routes->get('dashboard', 'Auth::adminDashboard');
});