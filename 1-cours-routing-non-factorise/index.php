<?php

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use \Symfony\Component\Routing\RouteCollection;
use \Symfony\Component\Routing\Matcher\UrlMatcher;

require __DIR__ . '/vendor/autoload.php';

$listeRoute = new Route('/');
$createRoute = new Route('create');
$showRoute = new Route('show/{id}');
// détail de la construction d'une route :
$helloRoute = new Route(
    'hello/{name}', // La route en question
    ['name' => 'World'], // Le tableau des paramètres par défaut
    [], // Le tableau des contraintes liées à la route (les requirements)
    [], // Un tableau pour gérer des options de route
    '', // On peut définir ce qui se trouvera dans l'url devant la route afin d'ajouter des contraintes. (ex http://monsite.com ou www.monsite.com)
    ['http', 'https'],// le scheme: indique si la route est disponible par ex que en http, ou https, ou ici les deux
    ['POST', "GET"] // un tableau sur les méthodes ex: permet de dire que la route est dispo que en méthode POST, ici les deux
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
    //dd($resultat);

    $page = $currentRoute['_route'];

    require_once "pages/$page.php";
} catch (ResourceNotFoundException $exception){
    require 'pages/404.php';
    return;
}


/**
 * LES PAGES DISPONIBLES
 * ---------
 * Afin de pouvoir être sur que le visiteur souhaite voir une page existante, on maintient ici une liste des pages existantes
 */
//$availablePages =  [
//    'list', 'show', 'create'
//];

// Par défaut, la page qu'on voudra voir si on ne précise pas (par exemple sur /index.php) sera "list"
//$page = 'list';

// Si on nous envoi une page en GET, on la prend en compte (exemple : /index.php?page=create)
//if (isset($_GET['page'])) {
//    $page = $_GET['page'];
//}

// Si la page demandée n'existe pas (n'est pas dans le tableau $availablePages)
// On affiche la page 404
//if (!in_array($page, $availablePages)) {
//    require 'pages/404.php';
//    return;
//}

/**
 * ❌ ATTENTION DEMANDEE !
 * -----------
 * Ici, un moyen simple d'obeir au visiteur et de lui présenter ce qu'il demande c'est d'inclure le fichier qui porte le même nom que la 
 * variable $page. 
 * 
 * => EXTREMENT DANGEREUX ! Ca veut dire que le visiteur pilote l'inclusion de scripts PHP, quelqu'un de malin pourrait s'en servir pour inclure 
 * un script non prévu ou voulu. On est un peu protégé par la condition juste au dessus, mais c'est quand même HYPER LIMITE.
 * 
 * Comment allons nous réparer ça dans les prochaines sections ?
 * 
 * ❌ AUTRE PROBLEME DE TAILLE ICI : LE COUPLAGE DE L'URL ET DES NOMS DE FICHIERS
 * ------------
 * Le fichier que l'on va inclure porte le même nom que le paramètre $_GET['page']. C'est à dire que si on appelle /index.php?page=create
 * c'est le fichier pages/create.php qui va être inclus.
 * 
 * La conséquence, c'est que si demain je décide que le formulaire de création devrait se trouver sur /index.php?page=new il faudra que je
 * renomme forcément le fichier pages/create.php en pages/new.php et inversement (l'enfer)
 */
//require_once "pages/$page.php";
