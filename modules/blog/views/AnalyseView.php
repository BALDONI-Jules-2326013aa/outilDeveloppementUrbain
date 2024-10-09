<?php

namespace blog\views;

class AnalyseView extends AbstractView
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function css(): string
    {
        return 'styles.css';
    }

    public function pageTitle(): string
    {
        return 'Analyse Page';
    }

    protected function body()
    {
        include __DIR__ . '/Fragments/analyse.html';
        echo "<section id='shapefile-data'><h2>Shapefile Data</h2><pre>{$this->data}</pre></section>";
    }
}
