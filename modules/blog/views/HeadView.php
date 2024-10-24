<?php

namespace blog\views;

class HeadView
{
    public function __construct(private string $titre, private string $css){}

    function afficher(): void
    {
        $filePath = __DIR__ . '/Fragments/head.html';
        $fd = fopen($filePath, 'r');
        $headpage = fread($fd, filesize($filePath));
        fclose($fd);

        $headpage = str_replace('{{Title}}', $this->titre, $headpage);
        $headpage = str_replace('{{css}}', $this->css, $headpage);

        echo $headpage;
    }
}