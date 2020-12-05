# RasBa
Build fast, small, uncomplicated websites.

## ✨ Features 
> With RasbaHTML, the page is almost blank. All texts and variables are added while the page is loading (with RasbaJS)

- [X] Router
- [X] Easy, small
- [X] Advanced DOM
- [X] Anti-Scrap (like Vue)

## 📦 Install
You can easily install with [Composer](https://getcomposer.org/).
```sh
composer require quiec/rasba
```

## 🔎 Examples
You should check `examples` folder also simple a title:
```php
<?php
require './vendor/autoload.php';

$Rasba = new Rasba\Router([
    'html_attr' => ['lang' => 'en'],
    'minify' => true
]);


$Rasba->get('/', function ($Request, $Rasba, $Match) {
    $Rasba->Response->setStatusCode(302);
    $Rasba->Response->headers->set('Location', '/saymyname/Heisenberg');
});

$Rasba->post('/saymyname/(.*)', function ($Request, $Rasba, $Match) {
    $Hello = $Rasba->h1(rawurldecode($Match->group(1)));
    $Rasba->addBody($Hello);    
});

$Rasba->run();
```

## 👨‍💻 Author
[Yusuf Usta](https://github.com/quiec)
