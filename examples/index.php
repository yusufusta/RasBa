<?php
require './vendor/autoload.php';
use Rasba\Html;

$Html = new Html(true, ['lang' => 'en']);

$Html->addScript(JQUERY_SLIM);
$Html->addStyle(BOOTSTRAP_MIN);
$Html->addStyle("body {
  min-height: 75rem;
}");
$ViewPort = $Html->__meta__('', ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$Title = $Html->__title__('RasBa HTML + Bootstrap4');
$Html->addHead([$Title, $ViewPort]);

$Nav = $Html->nav('', ['class' => 'navbar navbar-expand-md navbar-dark bg-dark mb-4']);
$Menu_Ul = $Html->ul('', ['class' => 'navbar-nav mr-auto']);
$Menu_Ul->appendChild([
    $Html->li($Html->__a__('Home', ['class' => 'nav-link']), ['class' => 'nav-item active']),
    $Html->li($Html->__a__('Github', ['class' => 'nav-link']), ['class' => 'nav-item'])
]);

$Nav->appendChild([
    $Html->a('<b>Ras</b>ta<b>Ba</b>ba', ['class' => 'navbar-brand']),
    $Html->button(
        $Html->__span__('', ['class' => 'navbar-toggler-icon']),
        ["class" => "navbar-toggler", "type" => "button", "data-toggle" => "collapse", "data-target" => "#navbarCollapse", "aria-controls" => "navbarCollapse", "aria-expanded" => "false"]
    ),
    $Menu_Ul
]);

$Main = $Html->main('', ['role' => 'main', 'class' => 'container']);
$Jumbotron = $Html->div('', ['class' => 'jumbotron']);
$Jumbotron->appendChild([
    $Html->h1('RasBa HTML + Bootstrap'),
    $Html->p('This example is a RasBa + Bootstrap.', ['class' => 'lead']),
    $Html->a('RasBa Github', ['class' => 'btn btn-lg btn-primary', 'href' => 'https://www.github.com/quiec/RasBa'])
]);

$Main->appendChild($Jumbotron);

$Html->addBody([$Nav, $Main]);
$Html->run(true, false);
?>
