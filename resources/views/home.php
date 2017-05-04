<?php
$winkelmand = new Winkelmand;
$account = new Account;

if(!empty($_GET)){
  if($_GET['afgerond'] == true){
    $gebruikerId = $account->getLoginId();
    $winkelmand->rondBestellingAf($gebruikerId);
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
