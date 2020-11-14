# RasBa
Build fast, small, uncomplicated websites.

## ✨ Features 
> With RasbaHTML, the page is almost blank. All texts and variables are added while the page is loading (with RasbaJS)

- [X] Easy, small
- [X] Advanced DOM
- [X] Anti-Scrap (like Vue)

## 📦 Install
You can easily install with [Composer](https://getcomposer.org/).
```sh
composer require quiec/rasba
```

### ❌  Disable RasbaJS
When creating the HTML class, simply type false.
```php
$Html = new Html(false, ['lang' => 'en']);
```

## 🛠 Functions
|Function|Parameters|Desc|Return
|--|--|--|--|
|`__call__`|`$tag`, `$in`|Creates an HTML tag. If the tag starts with __, RasbaJs is disabled. If the tag ends with __, the id value is disabled.|`Element`|
|`randomName`|`$len`|Generate a random and unique id.|`String`|
|`addScript`|`$script : URL/local/str`, $local = false : bool|Adds JS script.|`Element`|
|`addStyle`|`$style : URL/local/str`, $local = false : bool|Adds CSS style.|`Element`|
|`addBody`|`$element`|Adds an element to the body tag.|`Element`|
|`addHead`|`$element`|Adds an element to the head tag.|`Element`|
|`run`|`$echo = true`, `$minify = true`|Run.|`String`|

## 🔎 Examples
You should check `examples` folder or [here](https://quiec.github.io/index.html) also simple a title:
```php
<?php
require './vendor/autoload.php';
use Rasba\Html;

$Html = new Html(true, ['lang' => 'en']);

$Main = $Html->h1('Rasta Baba');
$Html->addBody($Main);
$Html->run();
?>
```

## 👨‍💻 Author
[Yusuf Usta](https://github.com/quiec)
