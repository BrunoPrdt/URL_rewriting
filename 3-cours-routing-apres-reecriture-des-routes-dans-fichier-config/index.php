<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\RequestContext;
use \Symfony\Component\Routing\Matcher\UrlMatcher;

require __DIR__ . '/vendor/autoload.php';

//pour virer la création des routes et collections du index dans le fichier config/routes.php
$loader = new PhpFileLoader(new FileLocator(__DIR__ . '/config'));// 1
$collection = $loader->load('routes.php');//2


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


