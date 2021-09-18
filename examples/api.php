<?php
require '../vendor/autoload.php';

$Rasba = new Rasba\Router();

$Rasba->get('/api', function ($Rasba) {
    $Request = $Rasba->Request;
    $Rasba->returnJson(['ip' => $Request->getClientIp(), 'time' => time()]);
});

$Rasba->run();
