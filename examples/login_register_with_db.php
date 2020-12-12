<?php
require '../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Session\Session;

$Session = new Session();

$Rasba = new Rasba\Router([
    'html_attr' => ['lang' => 'en'],
    'database' => [
        'mysql:host=localhost;dbname=db',
        'root',
        ''    
    ]
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
        $Hello = $Rasba->h1('Welcome to RastaBaba, ' . $Session->get('username') . '!');
        $Rasba->addBody($Hello);
    } else {
        $Hello = $Rasba->h1('Login');
        $Form = $Rasba->form('', ['action' => '/', 'method' => 'post']);
        $Form->appendChild([
            $Rasba->__label__('Username:', ['for' => 'user']),
            $Rasba->__input__('', ['type' => 'text', 'name' => 'user']),
            $Rasba->br(),
            $Rasba->__label__('Password:', ['for' => 'pass']),
            $Rasba->__input__('', ['type' => 'password', 'name' => 'pass']),
            $Rasba->br(),
            $Rasba->__input__('', ['type' => 'submit', 'text' => 'Submit']),
            $Rasba->br(),
            $Rasba->__a__('Register', ['href' => '/register']),
        ]);
    
        $Rasba->addBody([$Hello, $Form]);    
    }
});

$Rasba->get('/register', function ($Request, $Rasba, $Match) use ($Session) {
    $Rasba->changeTitle('Register');

    $Logged = $Session->get('logged', false);
    if ($Logged) {
        $Hello = $Rasba->h1('Welcome to RastaBaba!');
        $Rasba->addBody($Hello);
    } else {
        $Hello = $Rasba->h1('Register');
        $Form = $Rasba->form('', ['action' => '/register', 'method' => 'post']);
        $Form->appendChild([
            $Rasba->__label__('Username:', ['for' => 'user']),
            $Rasba->__input__('', ['type' => 'text', 'name' => 'user']),
            $Rasba->br(),
            $Rasba->__label__('Password:', ['for' => 'pass']),
            $Rasba->__input__('', ['type' => 'password', 'name' => 'pass']),
            $Rasba->br(),
            $Rasba->__input__('', ['type' => 'submit', 'text' => 'Submit']),
        ]);
    
        $Rasba->addBody([$Hello, $Form]);    
    }
});

$Rasba->post('/register', function ($Request, $Rasba, $Match) use($Session) {
    if ($Request->request->get('user', NULL) !== NULL && $Request->request->get('pass', NULL) !== NULL) {
        $userData = $Rasba->Db->row(
            "SELECT * FROM uyeler WHERE username = ?",
            $Request->request->get('user')
        );
        
        if (empty($userData)) {
            $Rasba->db->insert('uyeler', ['username' => $Request->request->get('user'), 'password' => $Request->request->get('pass')]);
            $Hello = $Rasba->h1('Registered. Please Log In.');
            $Rasba->Response->headers->set('Refresh', '2; url=/');
            $Rasba->addBody($Hello);    
        } else {
            $Hello = $Rasba->h1('Invalid username (already registered)');
            $Rasba->Response->headers->set('Refresh', '2; url=/');
            $Rasba->addBody($Hello);    
        }
    } else {
        $Hello = $Rasba->h1('Invalid user or pass');
        $Rasba->addBody($Hello);    
    }
});

$Rasba->post('/', function ($Request, $Rasba, $Match) use($Session) {
    $userData = $Rasba->Db->row(
        "SELECT * FROM uyeler WHERE username = ? AND password = ?",
        $Request->request->get('user', NULL), $Request->request->get('pass', NULL)
    );

    if (!empty($userData)) {
        $Hello = $Rasba->h1('Welcome to RastaBaba, ' . $Request->request->get('user', NULL) . '!');
        $Rasba->addBody($Hello);

        $Session->set('logged', true);
        $Session->set('username', $Request->request->get('user', NULL));
    } else {
        $Hello = $Rasba->h1('Invalid user or pass');
        $Rasba->Response->headers->set('Refresh', '3; url=/');
        $Rasba->addBody($Hello);    
    }
});

$Rasba->run();