<?php
namespace blog\views;

class ConnexionView extends AbstractView {

    // Méthode pour afficher le formulaire de connexion
    protected function body(): void
    {
        include __DIR__ . '/Fragments/connexion.html';
    }

    function css(): string
    {
        return 'connexion.css';
    }

    function pageTitle(): string
    {
        return 'Page de connexion';
    }

    // Méthode pour vérifier les identifiants
    public function verifierConnexion(): bool
    {
        // Vérification si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les valeurs du formulaire
            $username = $_POST['user'];
            $password = $_POST['password'];

            // Connexion à la base de données (adapter selon vos configurations)
            try {
                $pdo = new \PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                // Requête préparée pour éviter les injections SQL
                $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE username = :username LIMIT 1');
                $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
                $stmt->execute();

                // Vérifier si l'utilisateur existe
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                // Si un utilisateur est trouvé et le mot de passe est correct
                if ($user && password_verify($password, $user['password'])) {
                    // Connexion réussie
                    return true;
                } else {
                    // Identifiants incorrects
                    return false;
                }

            } catch (\PDOException $e) {
                // Gérer les erreurs de connexion à la base de données
                echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
                return false;
            }
        }
        return false;
    }

    public function afficher(): void
    {
        if ($this->verifierConnexion()) {
            // Connexion réussie
            session_start();
            $_SESSION['username'] = $_POST['user'];  // Sauvegarder l'utilisateur dans la session
            
            header("Location: ");
            exit();  // Important : arrêter l'exécution après la redirection
        } else {
            // Affiche le formulaire en cas d'échec de connexion
            parent::afficher();
        }
    }
    
}
