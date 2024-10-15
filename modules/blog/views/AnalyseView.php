<?php

class AnalyseView {
    public function displayMap() {
        echo '<div id="map" class="carte"></div>';
    }
}

$view = new AnalyseView();
$view->displayMap();
