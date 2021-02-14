# RasBa

Build fast, small, uncomplicated websites. Bridge with PHP and JS.

## ✨ Features

> With RasbaHTML, the page is almost blank. All texts and variables are added while the page is loading (with RasbaJS). You can disable it.

- [x] Router
- [x] Easy, small
- [x] Anti-Scrap
- [x] DataBase
- [ ] More JS function

## 📦 Install

You can easily install with [Composer](https://getcomposer.org/).

```sh
composer require yusufusta/rasba
```

## 🔎 Examples

You should check [examples](https://github.com/Quiec/RasBa/tree/master/examples) folder. Also simple a title:

```php
<?php
require './vendor/autoload.php';

$Rasba = new Rasba\Router();

$Rasba->get('/', function ($Request, $Rasba) {
    $Rasba->h1('Hello World!')->toBody();
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

- [ ] Documantion

## 👨‍💻 Author

[Yusuf Usta](https://github.com/quiec)
