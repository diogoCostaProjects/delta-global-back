<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// adiciona o recurso mapeando os verbos POST/GET/PUT/DELETE
// $routes->resource('aluno');

$routes->post('auth/register', 'AuthController::register', ['filter' => 'cors']);
$routes->post('auth/login', 'AuthController::login', ['filter' => 'cors']);

$routes->get('aluno/',           'AlunoController::index',      ['filter' => 'cors']);
$routes->post('aluno/',          'AlunoController::create',     ['filter' => 'cors']);
$routes->put('aluno/(:any)',     'AlunoController::update/$1',  ['filter' => 'cors']);
$routes->get('aluno/(:any)',     'AlunoController::show/$1',    ['filter' => 'cors']);
$routes->post('aluno/novafoto',  'AlunoController::newImage',   ['filter' => 'cors']);
$routes->delete('aluno/(:any)',  'AlunoController::delete/$1',  ['filter' => 'cors']);