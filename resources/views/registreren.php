<?php
$gebruiker = new Account;
$betalen = new Betalen;
if(!empty($_POST)){
  $voornaam = $_POST['voornaam'];
  $achternaam = $_POST['achternaam'];
  $email = $_POST['email'];
  $woonplaats = $_POST['woonplaats'];
  $postcode = $_POST['postcode'];
  $straat = $_POST['straat'];
  $huisnummer = $_POST['huisnummer'];
  $wachtwoord = $_POST['wachtwoord'];
  $herhaalWachtwoord = $_POST['herhaalWachtwoord'];
  $betaalWijze = $_POST['betaalWijze'];
  if(!empty($voornaam || $achternaam || $email || $woonplaats || $postcode || $straat || $huisnummer || $wachtwoord || $herhaalWachtwoord)){
    $gebruiker->Registreren($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $herhaalWachtwoord, $betaalWijze);
  }
}

if(!$gebruiker->isIngelogd()){
?>
  <div class="panel panel-default">
    <div class="panel-body registreer-panel">
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
        <?php
          $betaalwijze = $betalen->displayBetaalWijze();
        ?>
        <input type="submit" name="submit" class="btn btn-primary form-knop" value="REGISTREER">
      </form>
    </div>
  </div>
<?php
}else{
  echo '<div class="warning"><b>U bent al ingelogd</b></div>';
}
