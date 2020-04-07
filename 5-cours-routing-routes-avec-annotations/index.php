<?php

use App\Loader\CustomAnnotationClassLoader;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use \Symfony\Component\Routing\Matcher\UrlMatcher;

require __DIR__ . '/vendor/autoload.php';

//pour virer la création des routes et collections du index dans le fichier config/routes.php
//$loader = new PhpFileLoader(new FileLocator(__DIR__ . '/config'));// 1
//$collection = $loader->load('routes.php');//2
//$loader = new YamlFileLoader(new FileLocator(__DIR__ . '/config'));
//$collection = $loader->load('routes.yaml');
$classLoader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$classLoader, 'loadClass']);
$loader = new AnnotationDirectoryLoader(new FileLocator(__DIR__ . '/src/Controller'), new CustomAnnotationClassLoader(new \Doctrine\Common\Annotations\AnnotationReader()));
//dd($loader);
$collection = $loader->load(__DIR__ . '/src/Controller');

// faire en sorte que le request context soit le plus précis possible (host, method, scheme)
$matcher = new UrlMatcher($collection, new RequestContext('', $_SERVER['REQUEST_METHOD']));
$generator = new UrlGenerator($collection, new RequestContext());

$pathInfo = $_SERVER['PATH_INFO'] ?? '/';

try {
    $currentRoute = $matcher->match($pathInfo);
    //dump($currentRoute);

    $controller = $currentRoute['_controller'];// callable
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


