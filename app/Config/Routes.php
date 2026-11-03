<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Simulation::index');
$routes->get('friendly', 'Simulation::friendly');
$routes->get('friendly/', 'Simulation::friendly');
$routes->post('action', 'Simulation::action');
