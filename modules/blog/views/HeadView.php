<?php

namespace blog\views;

class HeadView
{
    public function __construct(private String $titre, private String $css){}

    function afficher(): void
    {
        $fd = fopen(__DIR__ . '/Fragments/head.html', 'r');
        $headpage = fread($fd, filesize(__DIR__ . '/Fragments/head.html'));
        fclose($fd);

        $headpage = str_replace('{{Title}}', $this->titre, $headpage);
        $headpage = str_replace('{{css}}', $this->css, $headpage);

        echo $headpage;
    }

}