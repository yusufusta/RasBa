<?php
require '../vendor/autoload.php';
$Rasba = new Rasba\Router(['attrs' => ['html' => ['lang' => 'en']], 'rasbajs' => false]);

$Rasba->get('/', function ($Request, $Rasba) {
    $Rasba->Response->setStatusCode(302);
    $Rasba->Response->headers->set('Location', '/saymyname/Heisenberg');
});

$Rasba->get('/saymyname/(.*)', function ($Request, $Rasba) {
    $Rasba->addBody($Rasba->h1(rawurldecode($Rasba->Match->group(1))));
});

$Rasba->run();
