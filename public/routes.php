<?php

error_reporting(-1);
require_once(__dir__ . '/../vendor/autoload.php');
require(__dir__ . '/../resources/config.php');


$klein = new Klein\Klein;

$klein->respond(function ($request, $response, $service) {
    $service->layout('../resources/layouts/default.php');
});


$klein->respond('/', function ($request, $response, $service) {
    $service->pageTitle = 'Orchidee Holiday';
    $service->render(VIEWS.'/home.php');
});

$klein->respond('/cover/[:naam]', function ($request, $response, $service) {
    $naam = $request->naam;
    $path = FOTO . "/" . $naam;

    $filename = basename($path);
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpeg"; break;
        default:
    }

    header('Content-Type:'.$ctype);
    header('Content-Length: ' . filesize($path));
    readfile($path);
});


$klein->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:
            $router->response()->body(
                '<h1>404 - Ik kan niet vinden waar u naar zoekt.</h1>'
            );
            break;
        case 405:
            $router->response()->body(
                '<h1>405 - U heeft geen toestemming hier te komen.</h1>'
            );
            break;
        default:
            $router->response()->body(
                '<h1>Oh nee, er is iets ergs gebeurt! Errorcode:'. $code .'</h1>'
            );
    }
});

$klein->dispatch();
