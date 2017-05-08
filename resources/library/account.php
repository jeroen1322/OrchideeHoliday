<?php
class Account{
  public function isBeheerder(){
    if(!empty($_SESSION['login'])){
      if($_SESSION['login'][2] === 1){
        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

  public function getLoginId(){
    if(!empty($_SESSION['login'])){
      return $_SESSION['login'][0];
    }else{
      return false;
    }
  }

  public function gebruikerGegevens($klantId){
    $stmt = DB::conn()->prepare('SELECT voornaam, achternaam, email, woonplaats, postcode, straat, huisnummer FROM Persoon WHERE id=?');
    $stmt->bind_param('i', $klantId);
    $stmt->execute();
    $stmt->bind_result($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer);
    while($stmt->fetch()){
      $gebruikerGegevens['voornaam'] = $voornaam;
      $gebruikerGegevens['achternaam'] = $achternaam;
      $gebruikerGegevens['email'] = $email;
      $gebruikerGegevens['woonplaats'] = $woonplaats;
      $gebruikerGegevens['postcode'] = $postcode;
      $gebruikerGegevens['straat'] = $straat;
      $gebruikerGegevens['huisnummer'] = $huisnummer;
    }
    $stmt->close();

    return $gebruikerGegevens;
  }

  public function isIngelogd(){
    if(!empty($_SESSION['login'])){
      return true;
    }
  }

  public function Registreren($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $herhaalWachtwoord, $betaalWijze){

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

    function voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $betaalWijze){

      function insertPersoon($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $betaalWijze){
        $anoniem = 0;
        $stmt = DB::conn()->prepare('INSERT INTO Persoon(voornaam, achternaam, email, woonplaats, postcode, straat, huisnummer, betaalWijze, anoniem)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssii', $voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $betaalWijze, $anoniem);
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

      if(insertPersoon($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $betaalWijze)){
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

    if(controlleerEmailAlInGebruik($email)){
      echo '<div class="warning"><b>Het door u opgegeven email adres is al in gebruik.</b></div>';
    }else{
      if(controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord)){
        if(voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $betaalWijze)){
          header("Refresh:0; url=/login");
        }else{
          echo '<div class="error"><b>Er is een fout opgetreden tijdens het registreren. Probeer het later nog een keer</b></div>';
        }
      }else{
        echo '<div class="warning"><b>De door u ingevoerde wachtwoorden komen niet overeen.</b></div>';
      }
    }
  }

  public function telOpenOrders($gebruikerId){
    $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
    $stmt->bind_param('i', $gebruikerId);
    $stmt->execute();
    $stmt->bind_result($orderId);
    $stmt->fetch();
    $stmt->close();

    if(!empty($orderId)){
      $stmt = DB::conn()->prepare('SELECT COUNT(id) FROM `OrderRegel` WHERE orderid=?');
      $stmt->bind_param('i', $orderId);
      $stmt->execute();
      $stmt->bind_result($aantalArtikelen);
      $stmt->fetch();
      $stmt->close();

      return $aantalArtikelen;
    }
  }

  public function voegToeAanFavotiet($orchidee, $gebruiker){
    function addToDatabase($orchidee, $gebruiker){
      $stmt = DB::conn()->prepare('INSERT INTO Favoriet(orchidee, persoon) VALUES (?, ?)');
      $stmt->bind_param('ii', $orchidee, $gebruiker);
      $stmt->execute();
      $stmt->close();

      return true;
    }

    if(addToDatabase($orchidee, $gebruiker)){
      echo '<div class="succes"><b>Orchidee toegevoegd aan uw Favorieten</b></div>';
    }
  }

  public function getFavorieten($gebruiker){
    $stmt = DB::conn()->prepare('SELECT orchidee FROM Favoriet WHERE persoon=?');
    $stmt->bind_param('i', $gebruiker);
    $stmt->execute();
    $stmt->bind_result($orchidee);
    while($stmt->fetch()){
      $orchideeen[] = $orchidee;
    }
    $stmt->close();

    if(!empty($orchideeen)){
      return $orchideeen;
    }
  }

  public function deleteFavoriet($artikel){
    $stmt = DB::conn()->prepare('DELETE FROM Favoriet WHERE orchidee=?');
    $stmt->bind_param('i', $artikel);
    $stmt->execute();
    $stmt->close();

    return true;
  }
}
