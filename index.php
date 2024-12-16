<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\ConnexionController;
use blog\controllers\HomePageController;
use blog\controllers\HistoriqueCController;
use blog\controllers\HistoriqueSController;
use blog\controllers\SimulationController;
use blog\controllers\inscriptionController;
use blog\models\InscriptionModel;
use blog\models\ConnexionModel;

session_start();

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$controller = null;

if ($request_uri == '' || $request_uri == 'index.php') {
    $homePage = new HomePageController();
    $homePage->affichePage();
    exit;
}

switch ($request_uri) {
    case 'comparaison':
        $controller = new ComparaisonController();
        $controller->affichePage();
        break;

    case 'comparaisonFichier':
        $controller = new ComparaisonController();
        $controller->afficheFichier();
        break;

    case 'simulation':
        $controller = new SimulationController();
        $controller->affichePage();
        break;

    case 'afficheGetYears':
        $controller = new SimulationController();
        $controller->afficheGetYears();
        break;

    case 'startSimulation':
        $controller = new SimulationController();
        $controller->startSimulation();
        break;
    case 'historiqueC':
        $controller = new HistoriqueCController();
        $controller->affichePage();
        break;

    case 'historiqueS':
        $controller = new HistoriqueSController();
        $controller->affichePage();
        break;

    case 'inscription':
        $inscriptionPage = new InscriptionController();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $inscriptionPage::inscrire($_POST);
        }
        $inscriptionPage::affichePage();
        break;
    case 'connexion':
        $connexionPage = new ConnexionController();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $connexionPage::connecter($_POST);
        }
        $connexionPage::affichePage();
        break;
    case 'deconnexion':
        // On se déconnecte via la méthode deconnecter
        $deconnexionPage = new ConnexionController();
        $deconnexionPage::deconnecter();
        // Puis on affiche une page d'acceuil
        $homePage = new HomePageController();
        $homePage::affichePage();
        break;


}