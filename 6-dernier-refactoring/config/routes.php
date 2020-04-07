<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $configurator) {//3 on appelle le route configurator
    $configurator//4
        ->add('hello', '/hello/{name}')//on créé nos route d'une nouvelle façon + synthétique
        ->defaults(['name' => 'World'])
        ->controller('App\Controller\HelloController@sayHello')
        ->requirements([])
        ->options([])
        ->host('')
        ->schemes(['http', 'https'])
        ->methods(['POST', "GET"])

        ->add('list', '/')
        ->controller('App\Controller\TaskController@index')//autre façon + simple de désigner le controller en dehors des défaults

        ->add('create', '/create')
        ->controller('App\Controller\TaskController@create')
        ->host('')
        ->schemes(['http', 'https'])
        ->methods(['POST', "GET"])

        ->add('show', '/show/{id}')
        ->controller('App\Controller\TaskController@show')
        ->requirements(['id' => '\d+'])//j'indique que je veux un nombre numérique supp à un chiffre
    ;
};