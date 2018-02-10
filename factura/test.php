<?php

require __DIR__ . '/vendor/autoload.php';




$app = new \Slim\App();



$app->get('/foooo', function () {
    echo "Hello";
});

// Asociamos una URL a una función deduciendo el parámetro name
$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

$app->run();
?>