<?php
require '../vendor/autoload.php';

$rasbajs = false;
$Rasba = new Rasba\Router([
    'html_attr' => ['lang' => 'en'],
    'rasbajs' => $rasbajs ? [] : false
]);

$Rasba->get('/', function ($Request, $Rasba, $Match) use ($rasbajs) {
    $Ip = $Rasba->h1('Ip Address: ');
    $Time = $Rasba->h1('Unix Time: ');

    $Rasba->addBody([$Ip, $Time]);
    $js = '
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var json = JSON.parse(xhttp.responseText);
            document.getElementById("' . $Ip->id . '").appendChild(document.createTextNode(json.ip));
            document.getElementById("' . $Time->id . '").appendChild(document.createTextNode(json.time));
        }
    };
    xhttp.open("GET", "/json", true);
    xhttp.send();';

    $rasbajs ? $Rasba->RasbaJS->addRasbaJs($js) : $Rasba->addScript($js);
});

$Rasba->get('/json', function ($Request, $Rasba, $Match) {
    $Rasba->runAndReturnJson(['ip' => $Request->getClientIp(), 'time' => time()]);    
});

$Rasba->run();