<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('index', '/index')
        ->controller([\App\Controller\IndexController::class, 'index']);

    $routes->add('get', '/get')
        ->controller([\App\Controller\IndexController::class, 'get']);

    $routes->add('show', '/show')
        ->controller([\App\Controller\IndexController::class, 'show']);

    $routes->add('store', '/store')
        ->controller([\App\Controller\IndexController::class, 'store']);
};
