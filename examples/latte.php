<?php

require '../vendor/autoload.php';

$Rasba = new Rasba\Router();

$Rasba->get('/', function ($Rasba) {
    $Rasba->returnView('template.latte', ['title' => 'Coffees', 'items' => ['latte', 'espresso', 'macchiato']]);
});

$Rasba->run();
