<?php
class InscriptionModel {
    private $db;

    // Constructeur pour initialiser la base de données
    public function __construct(DbConnectModel $db) {
        $this->db = $db;
    }

    // Méthode pour enregistrer un nouvel utilisateur
    public function inscrireUtilisateur($nom, $email, $motDePasse) {
        // Vérifier si l'utilisateur existe déjà
        if ($this->emailExiste($email)) {
            throw new Exception("Cet e-mail est déjà utilisé.");
        }

        // Hachage du mot de passe pour la sécurité
        $motDePasseHashe = password_hash($motDePasse, PASSWORD_DEFAULT);

        // Requête SQL d'insertion
        $sql = "INSERT INTO utilisateurs (username  , email, password) VALUES ('$nom', '$email', '$motDePasseHashe')";

        // Exécution de la requête
        $result = $this->db->query($sql);

        if ($result) {
            return "Inscription réussie.";
        } else {
            throw new Exception("Erreur lors de l'inscription.");
        }
    }

    // Méthode pour vérifier si un e-mail existe déjà dans la base de données
    private function emailExiste($email) {
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = '$email'";
        $result = $this->db->query($sql);
        $row = pg_fetch_result($result, 0, 0);

        return $row > 0;
    }
}
?>
