<?php

namespace blog\models;

class TifModel
{
    public static function visualisationHillShade($file): string {
        $convertedFile = tempnam(sys_get_temp_dir(), 'converted_') . '.tif';
        $outputFile = tempnam(sys_get_temp_dir(), 'output_hillshade_') . '.png';

        $convertCommand = "gdal_translate -ot Byte -scale $file $convertedFile";
        shell_exec($convertCommand);

        $hillshadeCommand = "gdaldem hillshade $convertedFile $outputFile  -alg Horn|ZevenbergenThorne -z 5 -s 3 -az 315 -alt 45 -combined -multidirectional -igor";
        shell_exec($hillshadeCommand);

        if (file_exists($outputFile)) {
            $imgTag = "<h3>Visualisation 3D (Hillshade)</h3><img src='data:image/png;base64," . base64_encode(file_get_contents($outputFile)) . "' alt='Visualisation Hillshade' style='max-width: 50%; height: auto;'>";
            unlink($convertedFile);
            unlink($outputFile);
            return $imgTag;
        }

        unlink($convertedFile);
        return "<h3>Erreur lors de la création de la visualisation Hillshade.</h3>";
    }

}