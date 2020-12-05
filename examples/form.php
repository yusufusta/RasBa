<?php
require '../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Session\Session;

$Session = new Session();

$Rasba = new Rasba\Router([
    'html_attr' => ['lang' => 'en'],
    'minify' => true
]);

$Rasba->setHead(function ($Html) {
    return [
        $Html->createElement('title', 'RastaBaba')
    ];
});

$Rasba->setNotFound(function ($Request, $Rasba) {
    $Hello = $Rasba->h1('Aradığınız sayfayı bulamadık!');
    $Rasba->addBody($Hello);
});


$Rasba->get('/', function ($Request, $Rasba, $Match) use ($Session) {
    $Rasba->changeTitle('Home');

    $Logged = $Session->get('logged', false);
    if ($Logged) {
        $Hello = $Rasba->h1('Welcome to RastaBaba!');
        $Rasba->addBody($Hello);
    } else {
        $Hello = $Rasba->h1('Login');
        $Form = $Rasba->form('', ['action' => '/login', 'method' => 'post']);
        $Form->appendChild([
            $Rasba->__label__('Username:', ['for' => 'user']),
            $Rasba->__input__('', ['type' => 'text', 'name' => 'user']),
            $Rasba->br(),
            $Rasba->__label__('Password:', ['for' => 'pass']),
            $Rasba->__input__('', ['type' => 'text', 'name' => 'pass']),
            $Rasba->br(),
            $Rasba->__input__('', ['type' => 'submit', 'text' => 'Submit']),
        ]);
    
        $Rasba->addBody([$Hello, $Form]);    
    }
});

$Rasba->post('/login', function ($Request, $Rasba, $Match) use($Session) {
    if ($Request->request->get('user') == 'rasta' && $Request->request->get('pass') == 'baba') {
        $Hello = $Rasba->h1('Welcome to RastaBaba!');
        $Rasba->addBody($Hello);
        $Session->set('logged', true);
    } else {
        $Hello = $Rasba->h1('Invalid user or pass');
        $Rasba->addBody($Hello);    
    }
});

$Rasba->run();