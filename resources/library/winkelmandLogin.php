<?php
class winkelmandLogin extends Login{
  /* ------------------------------------------------------------------ */
  public function login($winkelmand, $email, $wachtwoord){
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

    function plaatsSessionWinkelmandInDatabase($winkelmand, $id){
      function controlleerBestaandeOrder($id){
        $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND besteld=0');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($bestaandeOrder);
        $stmt->fetch();
        $stmt->close();

        if(!empty($bestaandeOrder)){
          return $bestaandeOrder;
        }
      }

      function controlleerRand(){
        $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE id=?');
        $stmt->bind_param('i', $rand);
        $stmt->execute();
        $stmt->bind_result($opgehaaldId);
        $stmt->fetch();
        $stmt->close();

        if(empty($opgehaaldId)){
          return true;
        }
      }

      function maakOrder($gebruikerId){

        $besteld = 0;
        $randId = rand(1, 999999);

        if(controlleerRand($randId)){
          $id = $randId;
        }else{
          $id = rand(1, 9999999);
        }

        $stmt = DB::conn()->prepare('INSERT INTO `Order`(id, persoon, besteld, orderdatum) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('iiss', $id, $gebruikerId, $besteld, $orderDatum);
        $stmt->execute();
        $stmt->close();
      }

      function plaatsInOrderRegel($winkelmand, $orderId){
        foreach($winkelmand as $item){
          $orderRegelId = rand(1, 999999);
          $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid) VALUES(?, ?, ?)');
          $stmt->bind_param('iii', $orderRegelId, $item, $orderId);
          $stmt->execute();
          $stmt->close();
        }
      }

      $bestaandeOrder = controlleerBestaandeOrder($id);
      if(empty($bestaandeOrder)){
        maakOrder($id);
      }else{
        plaatsInOrderRegel($winkelmand, $bestaandeOrder);
      }
    }

    function logInSession($wachtwoord, $opgehaaldWachtwoord, $id){
      if (password_verify($wachtwoord, $opgehaaldWachtwoord)) {
        $stmt = DB::conn()->prepare('SELECT rolid FROM TussenRol WHERE persoonid=?');
        $stmt->bind_param('i', $id);
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
        header("Refresh:0; url=/afrekenen");
      } else {
        echo '<div class="alert"><b>Deze email en wachtwoord combinatie is niet bij ons geregistreerd.</b></div>';
        return false;
      }
    }

    if(controlleerInvullingVelden($email, $wachtwoord)){
      $id = getAccountId($email);
      $opgehaaldWachtwoord = getWachtwoord($id);
      logInSession($wachtwoord, $opgehaaldWachtwoord, $id);
      plaatsSessionWinkelmandInDatabase($winkelmand, $id);
    }else{
      echo '<div class="alert"><b>Controleer of u alle velden correct heeft ingevuld.</b></div>';
    }
  }
}
