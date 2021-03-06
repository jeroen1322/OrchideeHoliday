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
$klein->respond('/en', function ($request, $response, $service) {
    $service->layout('../resources/layouts/default_en.php');
    $service->pageTitle = 'Orchidee Holiday';
    $service->render(VIEWS.'/home_en.php');
});
$klein->respond('/contact', function ($request, $response, $service) {
    $service->pageTitle = 'Contact | Orchidee Holiday';
    $service->render(VIEWS.'/contact.php');
});
$klein->respond('/voorwaarden', function ($request, $response, $service) {
    $service->pageTitle = 'Voorwaarden | Orchidee Holiday';
    $service->render(VIEWS.'/voorwaarden.php');
});
$klein->respond('/voorwaarden/en', function ($request, $response, $service) {
    $service->layout('../resources/layouts/default_en.php');
    $service->pageTitle = 'Terms and Conditions | Orchidee Holiday';
    $service->render(VIEWS.'/voorwaarden_en.php');
});
$klein->respond('/bestelprocedure', function ($request, $response, $service) {
    $service->pageTitle = 'Bestelprocedure | Orchidee Holiday';
    $service->render(VIEWS.'/bestelprocedure.php');
});
$klein->respond('/bestelprocedure/en', function ($request, $response, $service) {
    $service->layout('../resources/layouts/default_en.php');
    $service->pageTitle = 'Orderingprocedure | Orchidee Holiday';
    $service->render(VIEWS.'/bestelprocedure_en.php');
});
$klein->respond('/sitemap', function ($request, $response, $service) {
    $service->pageTitle = 'Sitemap | Orchidee Holiday';
    $service->render(VIEWS.'/sitemap.php');
});
$klein->respond('/aanbod', function ($request, $response, $service) {
    $service->pageTitle = 'Aanbod | Orchidee Holiday';
    $service->render(VIEWS.'/aanbod.php');
});
$klein->respond('/artikel_groepen', function ($request, $response, $service) {
    $service->pageTitle = 'Artikel Groepen | Orchidee Holiday';
    $service->render(VIEWS.'/artikel_groepen.php');
});
$klein->respond('/groep/[:id]', function ($request, $response, $service) {
    $service->groep = $request->id;
    $service->pageTitle = 'Artikel Groepen | Orchidee Holiday';
    $service->render(VIEWS.'/groep.php');
});
$klein->respond('/favorieten', function ($request, $response, $service) {
    $service->pageTitle = 'Favorieten | Orchidee Holiday';
    $service->render(VIEWS.'/favorieten.php');
});
$klein->respond('/login', function ($request, $response, $service) {
    $service->pageTitle = 'Login | Orchidee Holiday';
    $service->render(VIEWS.'/login.php');
});
$klein->respond('/registreren', function ($request, $response, $service) {
    $service->pageTitle = 'Registreren | Orchidee Holiday';
    $service->render(VIEWS.'/registreren.php');
});
$klein->respond('/uitloggen', function ($request, $response, $service) {
    $service->pageTitle = 'Uitloggen | Orchidee Holiday';
    $service->render(VIEWS.'/uitloggen.php');
});
$klein->respond('/beheer/overzicht', function ($request, $response, $service) {
    $service->pageTitle = 'Overzicht | Orchidee Holiday';
    $service->render(VIEWS.'/beheer_overzicht.php');
});
$klein->respond('/beheer/orchidee_toevoegen', function ($request, $response, $service) {
    $service->pageTitle = 'Orchidee toevoegen | Orchidee Holiday';
    $service->render(VIEWS.'/orchidee_toevoegen.php');
});
$klein->respond('/beheer/orchidee_verwijderen', function ($request, $response, $service) {
    $service->pageTitle = 'Orchidee Verwijderen | Orchidee Holiday';
    $service->render(VIEWS.'/orchidee_verwijderen.php');
});
$klein->respond('/beheer/artikel_van_de_dag', function ($request, $response, $service) {
    $service->pageTitle = 'Artikel van De Dag toevoegen | Orchidee Holiday';
    $service->render(VIEWS.'/beheer_artikel_van_de_dag.php');
});
$klein->respond('/beheer/contactformulier', function ($request, $response, $service) {
    $service->pageTitle = 'contactformulier | Orchidee Holiday';
    $service->render(VIEWS.'/beheer_contactformulier.php');
});
$klein->respond('/artikel/[:id]', function ($request, $response, $service) {
    $service->orchideeId = $request->id;
    $service->pageTitle = 'Artikel | Orchidee Holiday';
    $service->render(VIEWS.'/artikel.php');
});
$klein->respond('/artikel_van_de_dag', function ($request, $response, $service) {
    $service->pageTitle = 'Artikel van De Dag | Orchidee Holiday';
    $service->render(VIEWS.'/artikel_van_de_dag.php');
});
$klein->respond('/winkelmand', function ($request, $response, $service) {
    $service->pageTitle = 'Winkelmand | Orchidee Holiday';
    $service->render(VIEWS.'/winkelmand.php');
});
$klein->respond('/afrekenen', function ($request, $response, $service) {
    $service->pageTitle = 'Afrekenen | Orchidee Holiday';
    $service->render(VIEWS.'/afrekenen.php');
});
$klein->respond('/best_verkocht', function ($request, $response, $service) {
    $service->pageTitle = 'Best verkocht | Orchidee Holiday';
    $service->render(VIEWS.'/best_verkocht.php');
});
$klein->respond('/zoeken', function ($request, $response, $service) {
    $service->pageTitle = 'Zoeken | Orchidee Holiday';
    $service->render(VIEWS.'/simpelZoeken.php');
});
$klein->respond('/uitgebreid_zoeken', function ($request, $response, $service) {
    $service->pageTitle = 'Uitgebreid Zoeken | Orchidee Holiday';
    $service->render(VIEWS.'/uitgebreid_zoeken.php');
});

$klein->respond('/foto/[:naam]', function ($request, $response, $service) {
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
                '<h1>404 - Pagina niet gevonden.</h1>'
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
