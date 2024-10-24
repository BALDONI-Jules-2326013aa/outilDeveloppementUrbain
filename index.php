    <?php
    include __DIR__ . '/AutoLoader.php';

    use blog\controllers\ComparaisonController;
    use blog\controllers\ConnexionController;
    use blog\controllers\HomePageController;
    use blog\controllers\SimulationController;
    use blog\controllers\inscriptionController;
    use blog\models\InscriptionModel;
    use blog\models\ConnexionModel;


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
            $inscription = new inscriptionController();
            $inscription::affichePage();
            break;

        case 'verifInscription':
            $inscriptionModel = new InscriptionModel();
            $inscriptionModel-> verifInscription($username, $password,$id);
            break;
    }
