<?php
namespace blog\views;

class HeaderView
{
    public function __construct(private bool $loggedin)
    {
    }

    public function afficher(): string
    {
        if ($this->loggedin) {
            return $this->menuLogged();
        } else {
            return $this->menu();
        }
    }

    private function menuLogged(): string
    {
        $filePath = __DIR__ . '/Fragments/header-logged.html';
        if (file_exists($filePath) && is_readable($filePath)) {
            $fd = fopen($filePath, 'r');
            $headerHtml = fread($fd, filesize($filePath));
            fclose($fd);
            return $headerHtml;
        } else {
            return "Error: Unable to open header-logged.html at $filePath";
        }
    }

    private function menu(): string
    {
        $filePath = __DIR__ . '/Fragments/header.html';
        if (file_exists($filePath) && is_readable($filePath)) {
            $fd = fopen($filePath, 'r');
            $headerHtml = fread($fd, filesize($filePath));
            fclose($fd);
            return $headerHtml;
        } else {
            return "Error: Unable to open header.html at $filePath";
        }
    }
}