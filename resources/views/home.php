<?php
$winkelmand = new Winkelmand;
$account = new Account;

if(!empty($_GET)){
  if($_GET['ingelogd'] == 'true'){
    $gebruikerId = $account->getLoginId();
    if($_GET['afgerond'] == 'true'){
      $winkelmand->rondBestellingAf($gebruikerId);
    }elseif($_GET['afgerond'] == 'false'){
      $winkelmand->annuleerOrder($gebruikerId);
    }
  }else{
    if($_GET['afgerond'] == 'true'){
      $winkelmand->rondSessionBestellingAf($_POST, $_SESSION['winkelmand'], $_SESSION['bezorgwijze']);
    }elseif($_GET['afgerond'] == 'false'){
      $winkelmand->annuleerSessionOrder();
    }
  }
}
?>
<div class="top-home">
  <div class="slider">

  </div>
  <div class="slide-boxes">
    <a href="/aanbod">
      <div class="slide-box orchidee-slide-box-1">
        <h2>AANBOD</h2>
      </div>
    </a>
    <div class="slide-box">
      <a href="/best_verkocht">
        <div class="slide-box orchidee-slide-box-2">
          <h2>BEST VERKOCHT</h2>
        </div>
      </a>
    </div>
    <div class="slide-box">
      <a href="/contact">
        <div class="slide-box orchidee-slide-box-3">
          <h2>CONTACT</h2>
        </div>
      </a>
    </div>
  </div>
</div>
<hr>
<?php
DB::conn()->close();
