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
$Title = $Html->__title__('RasBa Login');
$Html->addHead([$Title, $ViewPort]);

if (!empty($_POST['username']) || !empty($_POST['password'])) {
    if ($_POST['username'] == 'rasta' && $_POST['password'] == 'baba') {
        $Html->addScript('alert("Successfully logged!"); location.href="https://www.youtube.com/watch?v=YqwgeVeOIpI"');
    } else {
        $Html->addScript('alert("Invalid username or password!");');
    }
}
$Jumbotron = $Html->div('', ['class' => 'jumbotron mt-3']);
$Form = $Html->__form__('', ['action' => "/form.php", 'method' => 'post']);
$FormGroup = $Html->__div__('', ['class' => 'form-group']);

$Form->appendChild([
    $FormGroup->appendChild($Html->input('', ['class' => 'form-control', 'placeholder' => 'Username', 'name' => 'username'])),
    $Html->br(),
    $FormGroup->appendChild($Html->input('', ['class' => 'form-control', 'placeholder' => 'Password', 'name' => 'password'])),
    $Html->br(),
    $Html->button('Submit', ['type' => 'submit', 'class' => 'btn btn-primary'])
]);
$Jumbotron->appendChild($Form);

$Main = $Html->__div__('', ['class' => 'container']);
$Main->appendChild($Jumbotron);

$Html->addBody($Main);
$Html->run(true, false);
?>
