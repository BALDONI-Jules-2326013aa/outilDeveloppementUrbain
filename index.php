<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\ConnexionController;
use blog\controllers\HomePageController;
use blog\controllers\HistoriqueCController;
use blog\controllers\HistoriqueSController;
use blog\controllers\SimulationController;
use blog\controllers\InscriptionController;
use blog\models\InscriptionModel;
use blog\models\FileModel;
use blog\controllers\FileController;
session_start();

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$controller = null;

if ($request_uri == '' || $request_uri == 'index.php') {
    HomePageController::affichePage();
}

try {
    $pdo = new PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

switch ($request_uri) {
    case 'fichier':
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
        $controller = new ComparaisonController();
        $controller->affichePage();
        break;

    case 'comparaisonFichier':
        ComparaisonController::ajouterFichier();
        break;
    case 'newMap':
        ComparaisonController::resetSession();
        ComparaisonController::afficheFichier();
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
    case 'connexion':
        $connexionPage = new ConnexionController();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $connexionPage::connecter($_POST);
        }
        $connexionPage::affichePage();
        break;
    case 'inscription':
        $controller = new inscriptionController();
        $controller->Inscription();
        break;

    case 'verifInscription':
        if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];

            $inscriptionModel = new InscriptionModel();
            $success = $inscriptionModel->verifInscription($username, $password, $email);

            if ($success) {
                $_SESSION['logged'] = true;
                header("Location: /");
                exit();
            } else {
                echo "Erreur lors de l'inscription.";
            }
        } else {
            echo "Données d'inscription manquantes.";
        }
        break;

    default:
        echo "Page non trouvée";
        break;
}