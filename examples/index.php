<?php

use Rasba\JavascriptFunction;

require '../vendor/autoload.php';

$NotFound = function ($Request, $Rasba) {
    $Rasba->h1('Where Am I? Where Are You?')->toBody();
};

$Head = function ($Rasba) {
    return [
        $Rasba->__title('Test')
    ];
};

$Rasba = new Rasba\Router([
    'errors' => [
        404 => $NotFound
    ],
    'head' => $Head,
    'body_top' => [
        ['h1', "I'm always top", ['id' => 'top']]
    ],
    'body_bottom' => [
        ['h1', "Rasba is bad.", ['id' => 'rasba']]
    ],
    'random_id_len' => 3
]);

$Rasba->get('/', function ($Request, $Rasba) {
    $Function = new JavascriptFunction($Rasba->RasbaJS);
    $Rasba->RasbaJS->addRasbaJs($Function->Run(function ($Js) {
        $Js->Alert('Merhaba!');
    }));

    $Rasba->button('Click Me', ['onClick' => $Function->CallCode])->toBody();

    $Rasba->RasbaJS->addRasbaJs($Function->Run(function ($Js) {
        $Js->Document()->getElementById('rasba')->innerHtml('Rasba is good.');
    }));

    $Rasba->button('Click Me 2', ['onClick' => $Function->CallCode])->toBody();
});

$Rasba->get('/test', function ($Request, $Rasba) {
    $Div = $Rasba->div();

    $id = $Div->addChild($Rasba->h1()->setText('test'))[0]
        ->addChild($Rasba->h2('my id:', ['id' => 'asd']))[1]->get('id');
    $Div->toBody();
    $Rasba->h3($id)->toBody();
});

$Rasba->get('/api', function ($Request, $Rasba) {
    $Rasba->runAndReturnJson(['ip' => $Request->getClientIp(), 'time' => time()]);
});

$Rasba->run();
