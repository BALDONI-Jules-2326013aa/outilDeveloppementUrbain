<?php

namespace blog\views;

class HeadView
{
    /**
     * Constructeur de la classe HeadView
     * Initialise les propriétés $titre et $css avec les valeurs fournies
     * @param string $titre Le titre de la page
     * @param string $css Le chemin vers le fichier CSS
     */
    public function __construct(private string $titre, private string $css){}

    /**
     * Affiche le contenu de la balise <head> de la page
     * @return void
     */
    function afficher(): void
    {
        $filePath = __DIR__ . '/Fragments/head.html'; // Chemin vers le fichier HTML du head
        $fd = fopen($filePath, 'r'); // Ouvre le fichier en lecture
        $headpage = fread($fd, filesize($filePath)); // Lit le contenu du fichier
        fclose($fd); // Ferme le fichier

        // Remplace les placeholders {{Title}} et {{css}} par les valeurs des propriétés $titre et $css
        $headpage = str_replace('{{Title}}', $this->titre, $headpage);
        $headpage = str_replace('{{css}}', $this->css, $headpage);

        echo $headpage; // Affiche le contenu du head avec les valeurs remplacées
    }
}