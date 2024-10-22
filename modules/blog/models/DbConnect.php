<?php
class DbConnectModel {
    private $conn;
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;

    // Constructeur pour initialiser les paramètres de connexion
    public function __construct($host, $port, $dbname, $user, $password) {
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
    }

    // Méthode pour établir la connexion à la base de données
    public function connect() {
        $conn_string = "host=$this->host port=$this->port dbname=$this->dbname user=$this->user password=$this->password";
        $this->conn = pg_connect($conn_string);

        if (!$this->conn) {
            throw new Exception("Erreur de connexion à la base de données PostgreSQL.");
        }
        return $this->conn;
    }

    // Méthode pour exécuter une requête SQL
    public function query($sql) {
        if (!$this->conn) {
            throw new Exception("Pas de connexion active. Veuillez d'abord vous connecter.");
        }

        $result = pg_query($this->conn, $sql);

        if (!$result) {
            throw new Exception("Erreur dans l'exécution de la requête SQL : " . pg_last_error($this->conn));
        }

        return $result;
    }

    // Méthode pour récupérer les résultats sous forme de tableau associatif
    public function fetchAll($result) {
        return pg_fetch_all($result);
    }

    // Méthode pour fermer la connexion à la base de données
    public function close() {
        if ($this->conn) {
            pg_close($this->conn);
        }
    }
}


?>
