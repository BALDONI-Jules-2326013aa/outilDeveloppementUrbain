<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\HomePageController;
use blog\controllers\SimulationController;

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($request_uri == '' || $request_uri == 'index.php') {
    $homePage = new HomePageController();
    $homePage::affichePage();
}

switch ($request_uri) {
    case 'comparaison':
        $comparaisonController = new ComparaisonController();
        $comparaisonController::affichePage();
        break;

    case 'comparaisonFichier':
        $comparaisonController = new ComparaisonController();
        $comparaisonController::afficheFichier();
        break;

    case 'Simulation':
        $simulationController = new SimulationController();
        $simulationController::affichePage();
        break;

    case 'afficheGetYears':
        $simulationController = new SimulationController();
        $simulationController::afficheGetYears();
        break;

    case 'startSimulation':
        $simulationController = new SimulationController();
        $simulationController::startSimulation();
        break;

    case 'downloadSimulationFiles':
        $simulationController = new SimulationController();
        $simulationController::downloadSimulationFiles();
        break;

    default:
        $homePage = new HomePageController();
        $homePage::affichePage();
        break;
}
