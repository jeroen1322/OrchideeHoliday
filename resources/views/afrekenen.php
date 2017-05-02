<?php
$afrekenen = new Afrekenen;
$account = new Account;
$login = new winkelmandLogin;
$registreer = new winkelmandRegistreer;

if(!empty($_POST)){
  if(!$account->isIngelogd()){
    if($_POST['submit'] == 'LOGIN'){
      $login->login($_SESSION['winkelmand'], $_POST['email'], $_POST['wachtwoord']);
    }elseif($_POST['submit'] == 'REGISTREER'){
      $registreer->registreer($_POST, $_SESSION['winkelmand']);
    }elseif($_GET['bestellen'] == 'anoniem'){
      echo '<u><b>TODO:</b> Anoniem bestellen</u>';
    }
  }
}
if($account->isIngelogd()){
  ?>
  <h1>AFREKENEN</h1>
<?php
}else{
  ?>
  <h1>INLOGGEN OF REGISTREREN</h1>
  <h4>Als u anoniem besteld, zal u een factuur moeten voldoen voordat de bestelling word verzonden.</h4>
  <div class="left">
    <div class="panel panel-default">
      <div class="panel-body login-panel">
        <h1>LOGIN</h1>
        <form method="post" name="TEST">
          <input type="email" name="email" placeholder="Email" class="form-control">
          <input type="password" name="wachtwoord" placeholder="Wachtwoord" class="form-control">
          <input type="submit" name="submit" class="btn btn-primary form-knop" value="LOGIN">
        </form>
      </div>
    </div>
    <hr>
    <form method="post" action="?bestellen=anoniem">
      <button type="submit" name="submit" class="btn btn-primary form-knop anoniem-bestel-knop">ANONIEM BESTELLEN</button>
    </form>
  </div>
  <div class="right">
    <div class="panel panel-default">
      <div class="panel-body login-panel">
        <h1>REGISTREER</h1>
        <form method="post">
          <input type="text" name="voornaam" placeholder="Voornaam" class="form-control" autocomplete="off" required>
          <input type="text" name="achternaam" placeholder="Achternaam" class="form-control" autocomplete="off" required>
          <input type="email" name="email" placeholder="Email" class="form-control" autocomplete="off" required>
          <input type="text" name="woonplaats" placeholder="Woonplaats" class="form-control" autocomplete="off" required>
          <input type="text" name="postcode" placeholder="Postcode" class="form-control" autocomplete="off" required>
          <input type="text" name="straat" placeholder="Straat" autocomplete="off" class="form-control" autocomplete="off" required>
          <input type="text" name="huisnummer" placeholder="Huisnummer" class="form-control" autocomplete="off" required>
          <input type="password" name="wachtwoord" placeholder="Wachtwoord" autocomplete="off" class="form-control" autocomplete="off" required>
          <input type="password" name="herhaalWachtwoord" placeholder="Herhaal wachtwoord" autocomplete="off" class="form-control" autocomplete="off" required>
          <input type="submit" name="submit" class="btn btn-primary form-knop" value="REGISTREER">
        </form>
      </div>
    </div>
  </div>
  <?php
}
