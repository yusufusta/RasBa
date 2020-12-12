<?php
require '../vendor/autoload.php';

$Rasba = new Rasba\Router(['html_attr' => ['lang' => 'en']]);


$Rasba->get('/', function ($Request, $Rasba, $Match) {
    $Rasba->Response->setStatusCode(302);
    $Rasba->Response->headers->set('Location', '/saymyname/Heisenberg');
});

$Rasba->get('/saymyname/(.*)', function ($Request, $Rasba, $Match) {
    $Hello = $Rasba->h1(rawurldecode($Match->group(1)));
    $Rasba->addBody($Hello);    
});

$Rasba->run();