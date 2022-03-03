<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('show', '/show')
        ->controller([\App\Controller\IndexController::class, 'show']);

    $routes->add('store', '/store')
        ->controller([\App\Controller\IndexController::class, 'store'])
        ->methods(['POST']);
};
