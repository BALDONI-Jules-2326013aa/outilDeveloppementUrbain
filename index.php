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
        case 'historiqueC':
            $historiqueC = new HistoriqueCController();
            $historiqueC::affichePage();
            break;
            case 'historiqueS':
            $historiqueS = new HistoriqueSController();
            $historiqueS::affichePage();
            break;
        case 'inscription':
            $inscription = new inscriptionController();
            $inscription::affichePage();
            break;
        case 'verifInscription':
            if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $email = $_POST['email'];
                $inscriptionModel = new InscriptionModel();
                $success = $inscriptionModel->verifInscription($username, $password, $email);

                if ($success) {
                    echo "Inscription réussie !";
                    // Redirection ou autre action après inscription réussie
                    
                } else {
                    echo "Erreur lors de l'inscription.";
                    // Gérer les erreurs ici
                }
            } else {
                echo "Données d'inscription manquantes.";
            }
            break;


    }
