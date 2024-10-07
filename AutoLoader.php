<?php

/**
 * Class Autoloader
 *
 * Cette classe gère l'enregistrement d'une fonction d'autoload pour charger automatiquement les classes.
 */
class AutoLoader
{
    /**
     * Enregistre la fonction d'autoload.
     *
     * Cette méthode enregistre une fonction d'autoload qui charge les fichiers de classe
     * en fonction de leur nom et de leur espace de noms.
     *
     * @return void
     */
    public static function register(): bool
    {
        spl_autoload_register(function ($class) {
            $file = __DIR__ . '/modules/' . str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoloader::register();