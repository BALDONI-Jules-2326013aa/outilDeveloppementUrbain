<?php
// Inclut l'autoloader pour le chargement des classes
include __DIR__ . '/AutoLoader.php';

// Utilise les espaces de noms nécessaires pour les contrôleurs et les modèles
use blog\controllers\ComparaisonController;
use blog\controllers\ConnexionController;
use blog\controllers\HomePageController;
use blog\controllers\SimulationController;
use blog\controllers\InscriptionController;
use blog\models\FileModel;
use blog\controllers\FileController;
use blog\controllers\HistoriqueCController;
use blog\controllers\HistoriqueSController;

// Récupère l'URI de la requête et supprime les slashes en début/fin
$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Si l'URI de la requête est vide ou 'index.php', affiche la page d'accueil
if ($request_uri == '' || $request_uri == 'index.php') {
    HomePageController::affichePage();
}

// Crée une nouvelle instance PDO pour la connexion à la base de données
$pdo = new PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Gère les différentes routes en fonction de l'URI de la requête
switch ($request_uri) {
    case 'fichier':
        // Affiche la page des fichiers
        FileController::affichePage();
        break;
    case 'telechargerFichier':
        // Gère la requête de téléchargement de fichier
        $model = new FileModel($pdo);
        $controller = new FileController($model);
        $controller->handleRequest();
        break;
    case 'supprimerFichier':
        // Gère la requête de suppression de fichier
        $model = new FileModel($pdo);
        $controller = new FileController($model);
        $controller->handleRequest();
        break;
    case 'comparaison':
        // Affiche la page de comparaison
        $comparaison = new ComparaisonController();
        $comparaison::afficheFichier();
        break;
    case 'comparaisonFichier':
        // Gère la requête de comparaison de fichier
        $comparaison = new ComparaisonController();
        $comparaison::ajouterFichier();
        break;
    case 'newMap':
        // Réinitialise la session et affiche la page de comparaison
        $comparaison = new ComparaisonController();
        $comparaison::resetSession();
        $comparaison::afficheFichier();
        break;
    case 'Simulation':
        // Affiche la page de simulation
        $controller = new SimulationController();
        $controller->affichePage();
        break;
    case 'afficheGetYears':
        // Affiche les années pour la simulation
        SimulationController::afficheGetYears();
        break;
    case 'startSimulation':
        // Démarre la simulation
        SimulationController::startSimulation();
        break;
    case 'historiqueC':
        // Affiche la page d'historique de comparaison
        $controller = new HistoriqueCController();
        $controller->affichePage();
        break;
    case 'historiqueS':
        // Affiche la page d'historique de simulation
        $controller = new HistoriqueSController();
        $controller->affichePage();
        break;
    case 'inscription':
        // Gère l'inscription de l'utilisateur
        $inscriptionPage = new InscriptionController();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $inscriptionPage::inscrire($_POST);
        }
        $inscriptionPage::affichePage();
        break;
    case 'connexion':
        // Gère la connexion de l'utilisateur
        $connexionPage = new ConnexionController();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $connexionPage::connecter($_POST);
        }
        $connexionPage::affichePage();
        break;
    case 'deconnexion':
        // Gère la déconnexion de l'utilisateur
        $deconnexionPage = new ConnexionController();
        $deconnexionPage::deconnecter();
        $homePage = new HomePageController();
        $homePage::affichePage();
        break;
}