<?php
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

require __DIR__ . "/config/bootstrap.php";

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
