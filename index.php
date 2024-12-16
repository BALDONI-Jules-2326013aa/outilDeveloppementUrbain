
<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\ConnexionController;
use blog\controllers\HomePageController;
use blog\controllers\SimulationController;
use blog\controllers\InscriptionController;
use blog\models\FileModel;
use blog\controllers\FileController;
use blog\controllers\HistoriqueCController;
use blog\controllers\HistoriqueSController;

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
        $controller = new SimulationController();
        $controller->affichePage();
        break;
    case 'afficheGetYears':
        SimulationController::afficheGetYears();
        break;
    case 'startSimulation':
        SimulationController::startSimulation();
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