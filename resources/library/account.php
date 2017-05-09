<?php
class Account{

  /**
  *Check if the third index in the 'login' session (the rolId) is 1, which represents the administrator.
  *
  *@return true of it is 1.
  */
  public function isBeheerder(){
    if(!empty($_SESSION['login'])){
      if($_SESSION['login'][2] === 1){
        return true;
      }
    }
  }


  /**
  *Get the value of the first index in the 'login' session, which represents the user's id
  *
  *@return: the value of the first index if the 'login' session is not empty, else return false
  */
  public function getLoginId(){
    if(!empty($_SESSION['login'])){
      return $_SESSION['login'][0];
    }else{
      return false;
    }
  }

  /**
  *Get all the information from the Persoon table colums, where the id column matches the $klantId parameter
  *
  *@param: The int to which the id column from Persoon will be matched
  *@return: If it matches, return an array with all the data from the Persoon columns
  */
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

  /**
  *Function to check if the current user is logged in
  *
  *@return: true, if the user is logged in
  */
  public function isIngelogd(){
    if(!empty($_SESSION['login'])){
      return true;
    }
  }

  /**
  *Function that contains the whole procedure to register a user.
  *It checks if the inserted email adres is not already registered, and if the inserted passwords match.
  *
  *The function adds all the personal data to the Persoon table.
  *It then assigns the user account to the correct rol and after that it hashes that passwords and inserts that with the user's id in to the Wachtwoord table.
  *
  *If there is an error, it will display the error by echo'ing a div containing the error.
  *When everything works just fine, it redirects to the Login page.
  */
  public function Registreren($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $herhaalWachtwoord, $betaalWijze){

    /**
    *Function that checks if the inserted email matches any email adres already registered
    *
    *@param: The inserted email adres
    *@return: Return true if the inserted email matches.
    */
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

    /**
    *Check if the thwo inserted passwords match
    *
    *@param wachtwoord: The information from the Wachtwoord field
    *@param herhaalWachtwoord: The information from the Herhaal Wachtwoord field
    */
    function controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord){
      if($wachtwoord == $herhaalWachtwoord){
        return true;
      }
    }

    /**
    *Function that handles the actual inserting in to the database
    *
    *@param voornaam: The information from the Voornaam field
    *@param achternaam: The information from the Achternaam field
    *@param email: The information inserted in the Email field, if it does NOT match any already registered email adresses
    *@param woonplaats: the information from the Woonplaats field
    *@param postcode: The information inserted in the Postcode field
    *@param straat: Information inserted in the Straat field
    *@param huisnummer: Information inserted in the Huisnummer field
    *@param Wachtwoord: Information inserted in the Wachtwoord field, if it DOES match the information from the Herhaal Wachtwoord field
    *@param betaalWijze: The selected Betaalwijze from the Register form
    *
    *@return: If everything works and all is inserted correctly, the function returns True
    */
    function voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $betaalWijze){

      /**
      *Insert the personal details in the Persoon table
      *
      *@param: See above..
      *@return: true, if no error occurs
      */
      function insertPersoon($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $betaalWijze){
        $anoniem = 0;
        $stmt = DB::conn()->prepare('INSERT INTO Persoon(voornaam, achternaam, email, woonplaats, postcode, straat, huisnummer, betaalWijze, anoniem)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssssii', $voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $betaalWijze, $anoniem);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      /**
      *Get the id from the newly added user from the Persoon table
      *
      *@param: The email adres from the newly added user
      *@return: The newly added user's id
      */
      function getGebruikerId($email){
        $stmt = DB::conn()->prepare('SELECT id FROM Persoon WHERE email=?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();

        return $id;
      }

      /**
      *Insert the default rol (2), and the id from the newly added user in the TussenRol table
      *
      *@param gebruikerId: The user's id
      *@param defaultRolId: The default rol
      *@return: True, if no errors occur
      */
      function insertTussenRol($gebruikerId, $defaultRolId){
        $stmt = DB::conn()->prepare('INSERT INTO TussenRol(rolid, persoonid) VALUES(?, ?)');
        $stmt->bind_param('ii', $defaultRolId, $gebruikerId);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      /**
      *Insert the hashed password in to the database
      *
      *@param wachtwoord: The users cleartext-password
      *@param gebruikerId: The users id
      *@return: True, if no errors occur
      */
      function insertWachtwoord($wachtwoord, $gebruikerId){
        $hash = password_hash($wachtwoord, PASSWORD_DEFAULT); //Hash the password

        $stmt = DB::conn()->prepare('INSERT INTO Wachtwoord(wachtwoord, persoon) VALUES(?, ?)');
        $stmt->bind_param('ss', $hash, $gebruikerId);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      /**
      *Run the above defined functions
      *
      *@param: See above defined functions
      *@return: True if all the functions return true, else return false
      */
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

    /**
    *Run the complete register process by executing the functions
    *
    *@param: See above defined functions
    *@return: If everything returns true, redirect to the login page
    */
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

  /**
  *Count the amount of items a user has in the shoppingcart.
  *It matches the parameter gebruikerId to the Persoon column in the Order table
  *
  *@param: The user's id
  *@return: Return an in that represents the amount of items a user has in the shoppingcart, if there are any
  */
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

  /**
  *Add a product and the user's id to the Favoriet table.
  *
  *@param orchidee: The product's id
  *@param gebruiker: The user's id
  */
  public function voegToeAanFavotiet($orchidee, $gebruiker){
    /**
    *Insert the data to the Favoriet table
    *
    *@param orchidee: The product's id
    *@param gebruiker: The user's id
    *@return: true, if no errors occur
    */
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

  /**
  *Get all the products that a user has added to their Favorieten lijst (Favoriet table)
  *
  *@param gebruiker: The user's id
  *@return: An array with the ids of all the products that the user has added to their Favorieten lijst
  */
  public function getFavorieten($gebruiker){

    /**
    *Get the product ids from the Favoriet table where the persoon columns matches the user id parameter
    *
    *@param: The user's id
    *@return: An array with all the product ids
    */
    function getFavs($gebruiker){
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

    /**
    *Filter the 'deleted' product. If a product is deleted, a.k.a set as unavailable, the product's verwijder column will be set to 1.
    *A user can NOT have a deleted product in their Favorieten.
    *
    *@param: An array with all the product ids from getFavs();
    *@return: An array with all the product ids that are NOT deleted
    */
    function filterDeleted($artikelen){
      foreach($artikelen as $a){
        $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE id=? AND verwijderd=0');
        $stmt->bind_param('i', $a);
        $stmt->execute();
        $stmt->bind_result($id);
        while($stmt->fetch()){
          $ids[] = $id;
        }
        $stmt->close();
      }

      if(!empty($ids)){
        return $ids;
      }
    }

    $artikelen = getFavs($gebruiker);
    $filtered = filterDeleted($artikelen);

    if(!empty($filtered)){
      return $filtered;
    }

  }

  /**
  *Delete a product from a Favorieten list of a user.
  *
  *TODO: Add user's id as parameter
  *
  *@param artikel: The product's id
  *@param gebruiker: The users id;
  *@return: Returns true, if everything works
  */
  public function deleteFavoriet($artikel){
    $stmt = DB::conn()->prepare('DELETE FROM Favoriet WHERE orchidee=?');
    $stmt->bind_param('i', $artikel);
    $stmt->execute();
    $stmt->close();

    return true;
  }
}
