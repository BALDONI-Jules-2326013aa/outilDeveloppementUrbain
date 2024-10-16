<?php

namespace blog\controllers;

use blog\models\FileModel;
use blog\views\TelechargerView;

class TelechargerController
{
    public static function affichePage(): void
    {
        session_start();
        $files = FileModel::getFiles();
        $view = new TelechargerView($files);
        $view->afficher();
    }
}