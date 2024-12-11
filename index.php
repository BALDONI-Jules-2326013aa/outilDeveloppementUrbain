<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\ConnexionController;
use blog\controllers\HomePageController;
use blog\controllers\SimulationController;
use blog\models\ConnexionModel;
use blog\models\FileModel;
use blog\controllers\FileController;


$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($request_uri == '' || $request_uri == 'index.php') {

    $homePage = new HomePageController();
    $homePage::affichePage();
    }

try {
    $pdo = new PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=', 'siti', 'motdepassesitia1');
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

switch ($request_uri) {
    case 'fichier':
        $model = new FileModel($pdo);
        $controller = new FileController($model);
        $controller->handleRequest();
        break;
    case 'comparaison':
        $comparaison = new ComparaisonController();
        $comparaison::afficheFichier();
        break;
    case 'comparaisonFichier':
        $comparaison = new ComparaisonController();
        $comparaison::ajouterFichier();
        break;
    case 'newMap':
        $comparaison = new ComparaisonController();
        $comparaison::resetSession();
        $comparaison::afficheFichier();
        break;
    case 'Simulation':
        $simulation = new SimulationController();
        $simulation::affichePage();
        break;
    case 'afficheGetYears':
        $ficher = new SimulationController();
        $ficher::afficheGetYears();
        break;
    case 'startSimulation':
        $ficher = new SimulationController();
        $ficher::startSimulation();
        break;
    case 'connexion':
        $connexion = new ConnexionController();
        $connexion::affichePage();
        break;
    case 'verifConnexion':
        $verification = new ConnexionModel();
        $verification::verifConnexion();
        break;
    case 'inscription':
        $connexion = new \blog\controllers\inscriptionController();
        $connexion::affichePage();
        break;
}
