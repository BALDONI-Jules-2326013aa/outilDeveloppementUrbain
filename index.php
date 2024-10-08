<?php
include __DIR__ . '/AutoLoader.php';

use blog\controllers\HomePageController;

include __DIR__ . "/AutoLoader.php";

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($request_uri == '' || $request_uri == 'index.php' || $request_uri == 'http://localhost:8080/') {

    $homePage = new HomePageController();
    $homePage::affichePage();
    }