<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Home::login');

$routes->group('employe', ['filter' => 'role:employe'], function($routes) {
    $routes->get('dashboard', 'EmployeController::index');
});

$routes->group('rh', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'RhController::index');
    $routes->post('approuver/(:num)', 'RhController::approuver/$1');
    $routes->post('refuser/(:num)', 'RhController::refuser/$1');
    $routes->get('soldes', 'RhController::soldesEmployes');
});

$routes->group('admin', ['filter' => 'role:admin'], function($routes) {

});