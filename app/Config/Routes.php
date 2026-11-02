<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Simulation::index');
$routes->post('action', 'Simulation::action');
