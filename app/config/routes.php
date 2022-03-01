<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('index', '/index')
        ->controller([\App\Controller\IndexController::class, 'index']);
};
