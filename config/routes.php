<?php

use App\Controller\BlogController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('user_index', '/')
        ->controller([MainController::class, 'index'])
    ;

};

?>