<?php
namespace blog\views;

class HeaderView
{
    public function __construct(private bool $loggedin)
    {
    }

    public function afficher(): string
    {
        if($this->loggedin) {
            return $this->menuLogged();
        }
        else {
            return $this->menu();
        }
    }

    private function menuLogged(): string
    {
        $fd = fopen(__DIR__ . '/../Fragments/header-logged.html', 'r');
        $headerHtml = fread($fd, filesize(__DIR__ . '/../Fragments/header-logged.html'));

        return $headerHtml;
    }

    private function menu(): string
    {
        $fd = fopen(__DIR__ . '/../Fragments/header.html', 'r');
        $headerHtml = fread($fd, filesize(__DIR__ . '/../Fragments/header.html'));

        return $headerHtml;
    }
}