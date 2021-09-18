<?php
require '../vendor/autoload.php';
$Rasba = new Rasba\Router(['attrs' => ['html' => ['lang' => 'en']]]);

$Rasba->get('/', function ($Rasba) {
    $Rasba->Response->setStatusCode(302);
    $Rasba->Response->headers->set('Location', '/saymyname/Heisenberg');
});

$Rasba->get('/saymyname/{name}', function ($Rasba) {
    $Match = $Rasba->Vars;
    $Rasba->addBody($Rasba->h1($Match['name']));
});

$Rasba->run();
