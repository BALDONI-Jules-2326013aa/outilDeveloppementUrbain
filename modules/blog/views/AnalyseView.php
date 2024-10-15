<?php

namespace blog\views;

class AnalyseView extends AbstractView
{

    public function __construct()
    {
    }

    public function css(): string
    {
        return 'analyse.css';
    }

    public function pageTitle(): string
    {
        return 'Analyse Page';
    }

    protected function body()
    {
        include __DIR__ . '/Fragments/analyse.html';
    }
}
