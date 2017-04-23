<?php
/**
 * @author http://netters.nl/nederlandse-datum-in-php
 */
function nlDate($datum){
  /*
   // AM of PM doen we niet aan
   $parameters = str_replace("A", "", $parameters);
   $parameters = str_replace("a", "", $parameters);

  $datum = date($parameters);
 */
   // Vervang de maand, klein
   $datum = str_replace("january",     "januari",         $datum);
   $datum = str_replace("february",     "februari",     $datum);
   $datum = str_replace("march",         "maart",         $datum);
   $datum = str_replace("april",         "april",         $datum);
   $datum = str_replace("may",         "mei",             $datum);
   $datum = str_replace("june",         "juni",         $datum);
   $datum = str_replace("july",         "juli",         $datum);
   $datum = str_replace("august",         "augustus",     $datum);
   $datum = str_replace("september",     "september",     $datum);
   $datum = str_replace("october",     "oktober",         $datum);
   $datum = str_replace("november",     "november",     $datum);
   $datum = str_replace("december",     "december",     $datum);

  // Vervang de maand, hoofdletters
   $datum = str_replace("January",     "Januari",         $datum);
   $datum = str_replace("February",     "Februari",     $datum);
   $datum = str_replace("March",         "Maart",         $datum);
   $datum = str_replace("April",         "April",         $datum);
   $datum = str_replace("May",         "Mei",             $datum);
   $datum = str_replace("June",         "Juni",         $datum);
   $datum = str_replace("July",         "Juli",         $datum);
   $datum = str_replace("August",         "Augustus",     $datum);
   $datum = str_replace("September",     "September",     $datum);
   $datum = str_replace("October",     "Oktober",         $datum);
   $datum = str_replace("November",     "November",     $datum);
   $datum = str_replace("December",     "December",     $datum);

  // Vervang de maand, kort
   $datum = str_replace("Jan",         "Jan",             $datum);
   $datum = str_replace("Feb",         "Feb",             $datum);
   $datum = str_replace("Mar",         "Maa",             $datum);
   $datum = str_replace("Apr",         "Apr",             $datum);
   $datum = str_replace("May",         "Mei",             $datum);
   $datum = str_replace("Jun",         "Jun",             $datum);
   $datum = str_replace("Jul",         "Jul",             $datum);
   $datum = str_replace("Aug",         "Aug",             $datum);
   $datum = str_replace("Sep",         "Sep",             $datum);
   $datum = str_replace("Oct",         "Ok",             $datum);
   $datum = str_replace("Nov",         "Nov",             $datum);
   $datum = str_replace("Dec",         "Dec",             $datum);

  // Vervang de dag, klein
   $datum = str_replace("monday",         "maandag",         $datum);
   $datum = str_replace("tuesday",     "dinsdag",         $datum);
   $datum = str_replace("wednesday",     "woensdag",     $datum);
   $datum = str_replace("thursday",     "donderdag",     $datum);
   $datum = str_replace("friday",         "vrijdag",         $datum);
   $datum = str_replace("saturday",     "zaterdag",     $datum);
   $datum = str_replace("sunday",         "zondag",         $datum);

  // Vervang de dag, hoofdletters
   $datum = str_replace("Monday",         "Maandag",         $datum);
   $datum = str_replace("Tuesday",     "Dinsdag",         $datum);
   $datum = str_replace("Wednesday",     "Woensdag",     $datum);
   $datum = str_replace("Thursday",     "Donderdag",     $datum);
   $datum = str_replace("Friday",         "Vrijdag",         $datum);
   $datum = str_replace("Saturday",     "Zaterdag",     $datum);
   $datum = str_replace("Sunday",         "Zondag",         $datum);

  // Vervang de verkorting van de dag, hoofdletters
   $datum = str_replace("Mon",            "Maa",             $datum);
   $datum = str_replace("Tue",         "Din",             $datum);
   $datum = str_replace("Wed",         "Woe",             $datum);
   $datum = str_replace("Thu",         "Don",             $datum);
   $datum = str_replace("Fri",         "Vri",             $datum);
   $datum = str_replace("Sat",         "Zat",             $datum);
   $datum = str_replace("Sun",         "Zon",             $datum);

  return $datum;
}

class Account{
  public function isBeheerder(){
    if(!empty($_SESSION['login'])){
      if($_SESSION['login'][2] === 1){
        return true;
      }else{
        header("Refresh:0; url=/");
      }
    }else{
      header("Refresh:0; url=/login");
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

  public function Registreren($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $herhaalWachtwoord){

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
        $stmt->bind_result($e);
        $stmt->fetch();
        $stmt->close();

        return $e;
      }

      function insertTussenRol($gebruikerId, $defaultRolId){
        $stmt = DB::conn()->prepare('INSERT INTO TussenRol(rolid, persoonid) VALUES(?, ?)');
        $stmt->bind_param('ii', $gebruikerId, $defaultRolId);
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

    if(controlleerEmailAlInGebruik($email)){
      echo '<div class="warning"><b>Het door u opgegeven email adres is al in gebruik.</b></div>';
    }else{
      if(controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord)){
        if(voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord)){
          header("Refresh:0; url=/login");
        }else{
          echo '<div class="error"><b>Er is een fout opgetreden tijdens het registreren. Probeer het later nog een keer</b></div>';
        }
      }else{
        echo '<div class="warning"><b>De door u ingevoerde wachtwoorden komen niet overeen.</b></div>';
      }
    }
  }
}

class Artikel{
  public function informatie($artikelId){
    $informatie = array();

    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, langeOmschrijving, prijs, img FROM Orchidee WHERE id=?');
    $stmt->bind_param('i', $artikelId);
    $stmt->execute();
    $stmt->bind_result($id, $titel, $korteOmschrijving, $langeOmschrijving, $prijs, $img);
    while($stmt->fetch()){
      $informatie['id'] = $id;
      $informatie['titel'] = $titel;
      $informatie['korteOmschrijving'] = $korteOmschrijving;
      $informatie['langeOmschrijving'] = $langeOmschrijving;
      $informatie['prijs'] = $prijs;
      $informatie['img'] = $img;
    }
    $stmt->close();

    return $informatie;
  }

  public function thumbInfo($artikelId){
    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, img FROM Orchidee WHERE id=?');
    $stmt->bind_param('i', $artikelId);
    $stmt->execute();
    $stmt->bind_result($id, $titel, $korteOmschrijving, $img);
    while($stmt->fetch()){
      $thumbInfo['id'] = $id;
      $thumbInfo['titel'] = $titel;
      $thumbInfo['korteOmschrijving'] = $korteOmschrijving;
      $thumbInfo['img'] = $img;
    }
    $stmt->close();

    return $thumbInfo;
  }
}
