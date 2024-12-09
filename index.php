
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

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

$controller = null;

// Route par défaut pour la page d'accueil
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
    case 'connexion':
        $controller = new ConnexionController();
        $controller->afficherFormulaireConnexion();
        break;

    case 'verifConnexion':
        $controller = new ConnexionModel();
        $controller->verifConnexion();
        break;

    case 'inscription':
        $controller = new InscriptionController();
        $controller->afficherFormulaireInscription();
        break;

    case 'verifInscription':
        if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];

            $inscriptionModel = new InscriptionModel();
            $success = $inscriptionModel->verifInscription($username, $password, $email);

            if ($success) {
                header("Location: /?header=logged");
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