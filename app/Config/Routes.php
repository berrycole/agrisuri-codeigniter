<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Simulation::index');
$routes->get('friendly', 'Simulation::friendly');
$routes->get('friendly/', 'Simulation::friendly');
$routes->get('workflow', 'Workflow::index');
$routes->get('workflow/', 'Workflow::index');
$routes->post('action', 'Simulation::action');
