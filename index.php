<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\ComparaisonController;
use blog\controllers\HomePageController;
use blog\controllers\AnalyseController;


$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($request_uri == '' || $request_uri == 'index.php') {

    $homePage = new HomePageController();
    $homePage::affichePage();
    }

switch ($request_uri) {
    case 'comparaison':
        $comparaison = new ComparaisonController();
        $comparaison::affichePage();
        break;
    case 'comparaisonFichier':
        $ficher = new ComparaisonController();
        $ficher::afficheFichier();
        break;
    case 'Analyse':
        $analyse = new AnalyseController();
        $analyse::affichePage();
        break;
<<<<<<< HEAD
    case 'Analyse':
        $analyse = new \blog\controllers\AnalyseController();
        $analyse::affichePage();
        break;
}
=======
}
>>>>>>> origin/Ronan
