# RasBa
Build fast, small, uncomplicated websites.

## ✨ Features 
> With RasbaHTML, the page is almost blank. All texts and variables are added while the page is loading (with RasbaJS)

- [X] Router
- [X] Easy, small
- [X] Advanced DOM
- [X] Anti-Scrap (like Vue)
- [X] DataBase

## 📦 Install
You can easily install with [Composer](https://getcomposer.org/).
```sh
composer require quiec/rasba
```

## 🔎 Examples
You should check `[examples](https://github.com/Quiec/RasBa/tree/master/examples)` folder. Also simple a title:
```php
<?php
require './vendor/autoload.php';

$Rasba = new Rasba\Router();


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

## ✏ .htaccess
You need edit `.htaccess` file like this:
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

> index.php should be your rasba file.

## ✅ To-Do
- [ ]  Documantion

## 👨‍💻 Author
[Yusuf Usta](https://github.com/quiec)
