<?php

namespace App\Controller;

use Exception;
use Symfony\Component\Routing\Annotation\Route;

class TaskController{

    /**
     * @param array $currentRoute
     * @Route("/", name="list")
     */
    public function index(array $currentRoute){
        $generator = $currentRoute['generator'];
        // On récupère les tâches
        $data = require_once __DIR__ . '/../../data.php';

        require __DIR__ . "/../../pages/list.html.php";
    }

    /**
     * @param $currentRoute
     * @throws Exception
     * @Route("/show/{id}", name="show", defaults={"id": "100"}, requirements={"id": "\d+"})
     */
    public function show($currentRoute){
        $generator = $currentRoute['generator'];
        $data = require_once "data.php";
        $id = $currentRoute['id'];

        if (!$id || !array_key_exists($id, $data)) {
            throw new Exception("La tâche demandée n'existe pas !");
        }
        $task = $data[$id];

        require __DIR__ . "/../../pages/show.html.php";
    }

    /**
     * @param $currentRoute
     * @Route("/create", name="create", host="", schemes={"http", "https"}, methods={"GET", "POST"})
     */
    public function create($currentRoute){
        $generator = $currentRoute['generator'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement à la con (enregistrement en base de données, redirection, envoi d'email, etc)...
            var_dump("Bravo, le formulaire est soumis (TODO : traiter les données)", $_POST);
            // Arrêt du script
            return;
        }

        require __DIR__ . "/../../pages/create.html.php";
    }
}