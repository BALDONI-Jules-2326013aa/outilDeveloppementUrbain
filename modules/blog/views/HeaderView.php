<?php
namespace blog\views;

class HeaderView
{
    // Constructeur de la classe HeaderView
    public function __construct(private bool $loggedin)
    {
    }

    // Affiche le menu en fonction de l'état de connexion
    public function afficher(): string
    {
        if ($this->loggedin) {
            return $this->menuLogged(); // Affiche le menu pour les utilisateurs connectés
        } else {
            return $this->menu(); // Affiche le menu pour les utilisateurs non connectés
        }
    }

    // Affiche le menu pour les utilisateurs connectés
    private function menuLogged(): string
    {
        $filePath = __DIR__ . '/Fragments/header-logged.html';
        if (file_exists($filePath) && is_readable($filePath)) {
            $fd = fopen($filePath, 'r'); // Ouvre le fichier en lecture
            $headerHtml = fread($fd, filesize($filePath)); // Lit le contenu du fichier
            fclose($fd); // Ferme le fichier
            return $headerHtml; // Retourne le contenu du fichier
        } else {
            return "Error: Unable to open header-logged.html at $filePath"; // Retourne un message d'erreur si le fichier n'est pas accessible
        }
    }

    // Affiche le menu pour les utilisateurs non connectés
    private function menu(): string
    {
        $filePath = __DIR__ . '/Fragments/header.html';
        if (file_exists($filePath) && is_readable($filePath)) {
            $fd = fopen($filePath, 'r'); // Ouvre le fichier en lecture
            $headerHtml = fread($fd, filesize($filePath)); // Lit le contenu du fichier
            fclose($fd); // Ferme le fichier
            return $headerHtml; // Retourne le contenu du fichier
        } else {
            return "Error: Unable to open header.html at $filePath"; // Retourne un message d'erreur si le fichier n'est pas accessible
        }
    }
}