<?php
class winkelmandRegistreer{
  public function registreer($postArray, $winkelmand){
    $voornaam = $postArray['voornaam'];
    $achternaam = $postArray['achternaam'];
    $email = $postArray['email'];
    $woonplaats = $postArray['woonplaats'];
    $postcode = $postArray['postcode'];
    $straat = $postArray['straat'];
    $huisnummer = $postArray['huisnummer'];
    $wachtwoord = $postArray['wachtwoord'];
    $herhaalWachtwoord = $postArray['herhaalWachtwoord'];

    function controlleerEmailAlInGebruik($email){
      $stmt = DB::conn()->prepare('SELECT email FROM Persoon WHERE email=?');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->bind_result($opgehaaldeEmail);
      $stmt->fetch();
      $stmt->close();
      if(!empty($opgehaaldeEmail)){
        return true;
      }
    }

    function controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord){
      if($wachtwoord == $herhaalWachtwoord){
        return true;
      }
    }

    function voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord){

      function insertPersoon($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord){
        $stmt = DB::conn()->prepare('INSERT INTO Persoon(voornaam, achternaam, email, woonplaats, postcode, straat, huisnummer)
        VALUES(?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssss', $voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      function getGebruikerId($email){
        $stmt = DB::conn()->prepare('SELECT id FROM Persoon WHERE email=?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();

        return $id;
      }

      function insertTussenRol($gebruikerId, $defaultRolId){
        $stmt = DB::conn()->prepare('INSERT INTO TussenRol(rolid, persoonid) VALUES(?, ?)');
        $stmt->bind_param('ii', $defaultRolId, $gebruikerId);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      function insertWachtwoord($wachtwoord, $gebruikerId){
        $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);

        $stmt = DB::conn()->prepare('INSERT INTO Wachtwoord(wachtwoord, persoon) VALUES(?, ?)');
        $stmt->bind_param('ss', $hash, $gebruikerId);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      if(insertPersoon($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord)){
        $gebruikerId = getGebruikerId($email);
        $defaultRolId = 2;
        if(insertTussenRol($gebruikerId, $defaultRolId)){
          if(insertWachtwoord($wachtwoord, $gebruikerId)){
            return true;
          }else{
            return false;
          }
        }else{
          return false;
        }
      }
    }

    function plaatsSessionWinkelmandInDatabase($winkelmand, $email){
      $id = getGebruikerId($email);
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

      function maakOrder($gebruikerId, $winkelmand){

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

        foreach($winkelmand as $item){
          $orderRegelId = rand(1, 999999);
          $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid) VALUES(?, ?, ?)');
          $stmt->bind_param('iii', $orderRegelId, $item, $id);
          $stmt->execute();
          $stmt->close();
        }
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
        maakOrder($id, $winkelmand);
      }else{
        plaatsInOrderRegel($winkelmand, $bestaandeOrder);
      }

    }

    if(controlleerEmailAlInGebruik($email)){
      echo '<div class="warning"><b>Het door u opgegeven email adres is al in gebruik.</b></div>';
    }else{
      if(controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord)){
        if(voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord)){
          $gebruikerId = getGebruikerId($email);
          if(plaatsSessionWinkelmandInDatabase($winkelmand, $gebruikerId)){
            header("Refresh:0; url=/afrekenen");
          }
        }else{
          echo '<div class="error"><b>Er is een fout opgetreden tijdens het registreren. Probeer het later nog een keer</b></div>';
        }
      }else{
        echo '<div class="warning"><b>De door u ingevoerde wachtwoorden komen niet overeen.</b></div>';
      }
    }
  }
}
