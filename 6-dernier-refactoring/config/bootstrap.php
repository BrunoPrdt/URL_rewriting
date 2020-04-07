<?php
use App\Loader\CustomAnnotationClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

require __DIR__ . '/../vendor/autoload.php';
$classLoader = require __DIR__ . '/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$classLoader, 'loadClass']);
$loader = new AnnotationDirectoryLoader(new FileLocator(__DIR__ . '/../src/Controller'), new CustomAnnotationClassLoader(new \Doctrine\Common\Annotations\AnnotationReader()));
//dd($loader);
$collection = $loader->load(__DIR__ . '/../src/Controller');

// faire en sorte que le request context soit le plus précis possible (host, method, scheme)
$matcher = new UrlMatcher($collection, new RequestContext('', $_SERVER['REQUEST_METHOD']));
$generator = new UrlGenerator($collection, new RequestContext());

$pathInfo = $_SERVER['PATH_INFO'] ?? '/';