<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->get('/', 'AuthController::showLogin');
$routes->get('/login', 'AuthController::showLogin');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

$routes->group('', ['filter' => 'authGuard'], function($routes){
    $routes->get('/dashboard', 'Home::index'); // dashboard page
    $routes->get('/staffs', 'StaffController::index');
    $routes->get('/admissions', 'AdmissionController::index');

});

// API endpoints (AJAX) — protect with JWTAuthFilter
$routes->group('api', ['filter' => 'jwtAuth'], function($routes){
    $routes->get('staffs', 'StaffController::apiList');
    $routes->post('staffs', 'StaffController::apiCreate');
    $routes->put('staffs/(:num)', 'StaffController::apiUpdate/$1');
    $routes->delete('staffs/(:num)', 'StaffController::apiDelete/$1');

    $routes->get('admissions', 'AdmissionController::apiList'); // for DataTable
    $routes->post('admissions', 'AdmissionController::apiCreate');
    $routes->put('admissions/(:num)', 'AdmissionController::apiUpdate/$1');
    $routes->patch('admissions/(:num)/status', 'AdmissionController::apiUpdateStatus/$1');
});

