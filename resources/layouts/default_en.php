<?php
include(__DIR__ . '/../db.php');
// include(MAIL . '/mailLib.php');
foreach(glob(MAIL.'/*.php') as $file){
  include $file;
}
foreach(glob(__DIR__.'/../library/*.php') as $file){
  include $file;
}
session_start();

if(!empty($_SESSION['login'])){
  $klantId = $_SESSION['login'][0];
  $klantNaam = $_SESSION['login'][1];
  $klantRolId = $_SESSION['login'][2];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Material Design fonts -->
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">
  <title><?= $this->escape($this->pageTitle); ?></title>

  <!-- Bootstrap -->
  <link href="/public/bootstrap/css/bootstrap.min.css " rel="stylesheet">

  <!-- Bootstrap Material Design -->
  <link rel="stylesheet" type="text/css" href="/public/css/slick.css">
  <link rel="stylesheet" type="text/css" href="/public/css/slick-theme.css">
  <link rel="stylesheet" type="text/css" href="/public/bootstrap-material/css/bootstrap-material-design.css">
  <link rel="stylesheet" type="text/css" href="/public/bootstrap-material/css/ripples.css">
  <link rel="stylesheet" type="text/css" href="/public/css/style.css">
  <!-- <link rel="stylesheet" type="text/css" href="dist/css/ripples.min.css"> -->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
  <body>
    <!--   ____           _     _     _           _    _       _ _     _
  / __ \         | |   (_)   | |         | |  | |     | (_)   | |
 | |  | |_ __ ___| |__  _  __| | ___  ___| |__| | ___ | |_  __| | __ _ _   _
 | |  | | '__/ __| '_ \| |/ _` |/ _ \/ _ \  __  |/ _ \| | |/ _` |/ _` | | | |
 | |__| | | | (__| | | | | (_| |  __/  __/ |  | | (_) | | | (_| | (_| | |_| |
  \____/|_|  \___|_| |_|_|\__,_|\___|\___|_|  |_|\___/|_|_|\__,_|\__,_|\__, |
                                                                        __/ |
                                                                       |___/ -->
    <div class="navtop container">
      <a href="/"><h1 class="orchideeHoliday">OrchideeHoliday</h1></a>
      <form method="post" action="/zoeken">
        <input type="text" class="form-control zoek-balk" name="zoekterm" placeholder="SEARCH" autocomplete="off" required>
      </form>
      <a href="/uitgebreid_zoeken"><button class="btn btn-primary form-knop zoeken_knop">ADVANCED SEARCH</button></a>
      <?php
      $account = new Account;
      if($account->isIngelogd()){
        $openOrders = $account->telOpenOrders($_SESSION['login'][0]);

        if(!empty($openOrders)){
          ?>
          <a href="/winkelmand"><div class="artikelCount"><h3><b><?php echo $openOrders ?></b></h3></div></a>
          <?php
        }
      }else{
        if(!empty($_SESSION['winkelmand'])){
          $aantal = count($_SESSION['winkelmand']);
          ?>
          <a href="/winkelmand"><div class="artikelCount"><h3><b><?php echo $aantal ?></b></h3></div></a>
          <?php
        }
      }
      ?>
    </div>
    <div class="navbar navbar-default">
      <div class="container-fluid container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse navbar-responsive-collapse">
          <ul class="nav navbar-nav nav-links">
            <li><a href="/aanbod">SHOP OFFER</a></li>
            <li><a href="/artikel_groepen">PRODUCT GROUPS</a></li>
            <li><a href="/best_verkocht">BEST SOLD</a></li>
            <li><a href="/contact">CONTACT</a></li>
            <li><a href="/voorwaarden">TERMS AND CONDITIONS</a></li>
            <li><a href="/bestelprocedure">ORDERPROCEDURE</a></li>
            <li><a href="/sitemap">SITEMAP</a></li>
          </ul>
            <?php
            if(!empty($_SESSION['login'])){
              ?>
              <ul class="nav navbar-nav nav-rechts">
                <li class="dropdown">
                   <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $klantNaam ?>
                   <span class="caret"></span></a>
                   <ul class="dropdown-menu">
                     <?php
                     if($_SESSION['login'][2] === 1){
                       ?>
                         <li><a href="/beheer/overzicht" class="naam">OVERVIEW</a></li>
                         <li><a href="/favorieten" class="naam">FAVORITES</a></li>
                       <?php
                     }elseif($_SESSION['login'][2] === 2){
                         ?>
                         <li><a href="/favorieten" class="naam">FAVORITES</a></li>
                         <?php
                     }
                     ?>
                     <li><a href="/uitloggen">LOG OUT</a></li>
                   </ul>
                 </li>
              </ul>
              <?php
            }else{
              ?>
              <ul class="nav navbar-nav nav-rechts">
                <li><a href="/login">LOGIN</a></li>
                <li><a href="/registreren">REGISTER</a></li>
              </ul>
              <?php
            }
            ?>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="content">
            <?= $this->yieldView(); ?>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
     <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="../js/slick.min.js"></script>
     <script src="/js/js.js"></script>
     <script src="/bootstrap/js/bootstrap.min.js"></script>
     <script src="/bootstrap-material/js/material.js"></script>
     <script src="/bootstrap-material/js/ripples.js"></script>
  </body>
</html>
