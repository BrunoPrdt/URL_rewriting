<?php

use App\Controller\HelloController;
use App\Controller\TaskController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use \Symfony\Component\Routing\RouteCollection;
use \Symfony\Component\Routing\Matcher\UrlMatcher;

require __DIR__ . '/vendor/autoload.php';

$listeRoute = new Route(
    '/',
    //['controller' => [new TaskController, 'index']]
    ['controller' => 'App\Controller\TaskController@index']//fait comme au dessus ms améliore les performances
);
$createRoute = new Route(
    'create',
    ['controller' => 'App\Controller\TaskController@create']

);
$showRoute = new Route(
    'show/{id}',
    ['controller' => 'App\Controller\TaskController@show']
);
// détail de la construction d'une route :
$helloRoute = new Route(
    'hello/{name}',//URL: La route en question
    ['name' => 'World', 'controller' => 'App\Controller\HelloController@sayHello'],//Default: Le tableau des paramètres par défaut
    [],//Requirements: Le tableau des contraintes liées à la route (les requirements)
    [],//options: Un tableau pour gérer des options de route
    '',//host: On peut définir ce qui se trouvera dans l'url devant la route afin d'ajouter des contraintes. (ex http://monsite.com ou www.monsite.com)
    ['http', 'https'],//schemes: indique si la route est disponible par ex que en http, ou https, ou ici les deux
    ['POST', "GET"]//methods: un tableau sur les méthodes ex: permet de dire que la route est dispo que en méthode POST, ici les deux
);

$collection = new RouteCollection();
$collection->add("list", $listeRoute);
$collection->add('create', $createRoute);
$collection->add("show", $showRoute);
$collection->add('hello', $helloRoute);


// faire en sorte que le request context soit le plus précis possible (host, method, scheme)
$matcher = new UrlMatcher($collection, new RequestContext('', $_SERVER['REQUEST_METHOD']));
$generator = new UrlGenerator($collection, new RequestContext());

//ex de création de route ensuite:
//$generator->generate('create');//on rentre en param le nom de la route
//$generator->generate('show', ['id' => 100]);//si besoin de variable on rentre un 2eme param qui est un tableau

$pathInfo = $_SERVER['PATH_INFO'] ?? '/';

try {
    $currentRoute = $matcher->match($pathInfo);
    //dump($currentRoute);

    $controller = $currentRoute['controller'];// callable
    $currentRoute['generator'] = $generator;

    $className = substr($controller, 0, strpos($controller, '@'));
    $methodName = substr($controller, strpos($controller,'@')+1);

    $instance = new $className;

    call_user_func([$instance, $methodName], $currentRoute); // = $controller()
    die();

} catch (ResourceNotFoundException $exception){
    require 'pages/404.html.php';
    return;
}


