<?php

namespace blog\models;

class TifModel
{
    /**
     * Fonction pour générer une visualisation Hillshade à partir d'un fichier TIFF.
     * @param string $file Le chemin du fichier TIFF.
     * @return string La balise img contenant l'image Hillshade encodée en base64 ou un message d'erreur.
     */
    public static function visualisationHillShade($file): string {
        // Crée un fichier temporaire pour le fichier TIFF converti
        $convertedFile = tempnam(sys_get_temp_dir(), 'converted_') . '.tif';
        // Crée un fichier temporaire pour le fichier PNG de sortie
        $outputFile = tempnam(sys_get_temp_dir(), 'output_hillshade_') . '.png';

        // Commande pour convertir le fichier TIFF en utilisant gdal_translate
        $convertCommand = "gdal_translate -ot Byte -scale $file $convertedFile";
        shell_exec($convertCommand); // Exécute la commande

        // Commande pour générer la visualisation Hillshade en utilisant gdaldem
        $hillshadeCommand = "gdaldem hillshade $convertedFile $outputFile  -alg Horn|ZevenbergenThorne -z 5 -s 3 -az 315 -alt 45 -combined -multidirectional -igor";
        shell_exec($hillshadeCommand); // Exécute la commande

        // Vérifie si le fichier de sortie existe
        if (file_exists($outputFile)) {
            // Génère une balise image avec l'image encodée en base64
            $imgTag = "<h3>Visualisation 3D (Hillshade)</h3><img src='data:image/png;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Visualisation Hillshade' style='max-width: 50%; height: auto;'>";
            unlink($convertedFile); // Supprime le fichier TIFF converti
            unlink($outputFile); // Supprime le fichier PNG de sortie
            return $imgTag; // Retourne la balise img
        }

        unlink($convertedFile); // Supprime le fichier TIFF converti en cas d'erreur
        return "<h3>Erreur lors de la création de la visualisation Hillshade.</h3>"; // Retourne un message d'erreur
    }
}