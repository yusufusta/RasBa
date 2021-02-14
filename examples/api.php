<?php
require '../vendor/autoload.php';

$Rasba = new Rasba\Router();

$Rasba->get('/api', function ($Request, $Rasba) {
    $Rasba->runAndReturnJson(['ip' => $Request->getClientIp(), 'time' => time()]);
});

$Rasba->run();
