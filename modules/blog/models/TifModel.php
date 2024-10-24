<?php

namespace blog\models;

class TifModel
{
    public static function visualisationHillShade($file): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_dem_') . '.png';
        $command = "gdaldem hillshade $file $outputFile -z 5 -s 3 -az 315 -alt 45";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Visualisation 3D (Hillshade)</h3><img src='data:image/png;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Visualisation Hillshade' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de la visualisation Hillshade.</h3>";
    }


    public static function visualisationSlope($file): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_slope_') . '.jpg';
        $command = "gdaldem slope $file $outputFile -p -s 1";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Carte de Pente</h3><img src='data:image/jpg;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Carte de Pente' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de la carte de pente.</h3>";
    }



    public static function visualisationAspect($file): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_aspect_') . '.jpg';
        $command = "gdaldem aspect $file $outputFile -trigonometric -zero_for_flat";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Carte d'Aspect</h3><img src='data:image/jpg;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Carte d\'Aspect' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de la carte d'aspect.</h3>";
    }


    public static function visualisationColorRelief($file, $colorFile): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_color_relief_') . '.jpg';
        $command = "gdaldem color-relief $file $colorFile $outputFile -alpha";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Carte de Relief en Couleur</h3><img src='data:image/jpg;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Carte de Relief en Couleur' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de la carte de relief en couleur.</h3>";
    }

    public static function visualisationTRI($file): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_tri_') . '.jpg';
        $command = "gdaldem TRI $file $outputFile -alg ZevenbergenThorne";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Indice de Rugosité (TRI)</h3><img src='data:image/jpg;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Indice de Rugosité (TRI)' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de l'indice de rugosité (TRI).</h3>";
    }

    public static function visualisationTPI($file): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_tpi_') . '.jpg';
        $command = "gdaldem TPI $file $outputFile -compute_edges";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Indice de Position Topographique (TPI)</h3><img src='data:image/jpg;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Indice de Position Topographique (TPI)' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de l'indice de position topographique (TPI).</h3>";
    }

    public static function visualisationRoughness($file): string {
        $outputFile = tempnam(sys_get_temp_dir(), 'output_roughness_') . '.jpg';
        $command = "gdaldem roughness $file $outputFile -compute_edges";
        shell_exec($command);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Carte de Rugosité</h3><img src='data:image/jpg;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Carte de Rugosité' style='max-width: 50%; height: auto;'>";
            unlink($outputFile);
            return $imgTag;
        }

        return "<h3>Erreur lors de la création de la carte de rugosité.</h3>";
    }


}
