<?php

namespace blog\controllers;

class HomePageController
{
    public function affichePage()
    {
        $headerType = isset($_GET['header']) && $_GET['header'] === 'logged' ? 'header-logged' : 'header';
        include __DIR__ . '/../views/Fragments/' . $headerType . '.html';

        // Include the main content of the homepage
        include __DIR__ . '/../views/HomePageContent.html';
    }
}