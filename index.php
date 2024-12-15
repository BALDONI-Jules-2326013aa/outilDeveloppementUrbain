<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\ConnexionController;
use blog\controllers\HomePageController;
use blog\controllers\SimulationController;
use blog\controllers\InscriptionController;
use blog\models\FileModel;
use blog\controllers\FileController;

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($request_uri == '' || $request_uri == 'index.php') {
    HomePageController::affichePage();
}

try {
    $pdo = new \PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

switch ($request_uri) {
    case 'fichier':
        FileController::affichePage();
        break;
    case 'telechargerFichier':
        $model = new FileModel($pdo);
        $controller = new FileController($model);
        $controller->handleRequest();
        break;
    case 'supprimerFichier':
        $model = new FileModel($pdo);
        $controller = new FileController($model);
        $controller->handleRequest();
        break;
    case 'comparaison':
        ComparaisonController::afficheFichier();
        break;
    case 'comparaisonFichier':
        ComparaisonController::ajouterFichier();
        break;
    case 'newMap':
        ComparaisonController::resetSession();
        ComparaisonController::afficheFichier();
        break;
    case 'Simulation':
        SimulationController::affichePage();
        break;
    case 'afficheGetYears':
        SimulationController::afficheGetYears();
        break;
    case 'startSimulation':
        SimulationController::startSimulation();
        break;
    case 'connexion':
        ConnexionController::affichePage();
        break;
    case 'verifConnexion':
        ConnexionController::verifConnexion();
        break;
    case 'inscription':
        InscriptionController::affichePage();
        break;
    case 'verifInscription':
        InscriptionController::verifInscription();
        break;
}