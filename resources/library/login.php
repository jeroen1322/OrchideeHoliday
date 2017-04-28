<?php

class Login{
  public function voegLoginUit($email, $wachtwoord){

    function controlleerInvullingVelden($email, $wachtwoord){
      if($email != '' && $wachtwoord != ''){
        return true;
      }else{
        return false;
      }
    }
    function getAccountId($email){
      $stmt = DB::conn()->prepare("SELECT id FROM Persoon WHERE email=?");
      $stmt->bind_param("s", $email);
      $stmt->execute();

      $stmt->bind_result($klantId);
      $stmt->fetch();
      $stmt->close();

      return $klantId;
    }

    function getWachtwoord($id){
      $ww_stmt = DB::conn()->prepare("SELECT wachtwoord FROM Wachtwoord WHERE persoon=?");
      $ww_stmt->bind_param("i", $id);
      $ww_stmt->execute();

      $ww_stmt->bind_result($ww);
      $ww_stmt->fetch();
      $ww_stmt->close();

      return $ww;
    }

    function logInSession($wachtwoord, $opgehaaldWachtwoord, $id){
      if (password_verify($wachtwoord, $opgehaaldWachtwoord)) {
        $stmt = DB::conn()->prepare('SELECT rolid FROM TussenRol WHERE persoonid=?');
        $stmt->bind_param('i', $klantId);
        $stmt->execute();
        $stmt->bind_result($klantRolId);
        $stmt->fetch();
        $stmt->close();

        $stmt = DB::conn()->prepare('SELECT voornaam, achternaam FROM Persoon WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($voornaam, $achternaam);
        $stmt->fetch();
        $stmt->close();

        $naam = $voornaam.' '.$achternaam;

        $_SESSION['login'] = array();
        $_SESSION['login'][] = $id; //Zet de session die we kunnen checken in de functie isIngelogd();
        $_SESSION['login'][] = $naam;
        $_SESSION['login'][] = $klantRolId;
        header("Refresh:0; url=/");
      } else {
        echo '<div class="alert"><b>Deze email en wachtwoord combinatie is niet bij ons geregistreerd.</b></div>';
        return false;
      }
    }

    if(controlleerInvullingVelden($email, $wachtwoord)){
      $id = getAccountId($email);
      $opgehaaldWachtwoord = getWachtwoord($id);
      logInSession($wachtwoord, $opgehaaldWachtwoord, $id);

    }else{
      echo '<div class="alert"><b>Controleer of u alle velden correct heeft ingevuld.</b></div>';
    }
  }
}
