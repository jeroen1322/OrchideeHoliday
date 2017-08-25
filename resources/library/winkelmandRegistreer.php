<?php
class winkelmandRegistreer{

  /**
   * When a user wants to register an account while they are completing an order, this function will be called
   * The user's session shoppingcart will be transferred to the database and linked to their new account
   * @param  array $postArray: The array with account information that the user entered
   * @param  array $winkelmand: The user's session shoppingcart
   */
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
    $betaalWijze = $postArray['betaalWijze'];

    /**
     * Check if the inserted email adres is not already in use
     * @param  string $email: The inserted email
     * @return bool: Returns true if the email is already in use
     */
    function controlleerEmailAlInGebruik($email){
      $stmt = DB::conn()->prepare('SELECT email FROM Persoon WHERE email=? AND anoniem=0');
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
     * Check if the two inserted password are the same
     * @param  string $wachtwoord: The information submitted in the Wachtwoord field
     * @param  string $herhaalWachtwoord: The information submitted in the Herhaal Wachtwoord field
     * @return bool: Return true if they match
     */
    function controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord){
      if($wachtwoord === $herhaalWachtwoord){
        return true;
      }
    }

    /**
     * Insert the user's information in the Persoon table in the database and the password in the Wachtwoord table.
     * It also links the password to the new user's account
     * @param  string $voornaam    The information filled in the Voornaam field
     * @param  string $achternaam  The information filled in the Achternaam field
     * @param  string $email       The information filled in the Email field
     * @param  string $woonplaats  The information filled in the Woonplaats field
     * @param  string $postcode    The information filled in the Postcode field
     * @param  string $straat      The information filled in the Straat field
     * @param  string $huisnummer  The information filled in the Huisnummer field
     * @param  string $wachtwoord  The information filled in the Wachtwoord field
     * @param  int    $betaalWijze The selected betaalwijze
     */
    function voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $betaalWijze){

      /**
       * Insert the personal information in to the persoon table
       * @param  string $voornaam    The information filled in the Voornaam field
       * @param  string $achternaam  The information filled in the Achternaam field
       * @param  string $email       The information filled in the Email field
       * @param  string $woonplaats  The information filled in the Woonplaats field
       * @param  string $postcode    The information filled in the Postcode field
       * @param  string $straat      The information filled in the Straat field
       * @param  string $huisnummer  The information filled in the Huisnummer field
       * @param  int    $betaalWijze The selected betaalwijze
       * @return bool                Return true, if no errors occur
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
      * Get the id from the newly added user from the Persoon table
      * @param string $email: The email adres from the newly added user
      * @return int $id: The newly added user's id
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
      * Insert the default rol (2), and the id from the newly added user in the TussenRol table
      * @param int $gebruikerId: The user's id
      * @param int $defaultRolId: The default rol
      * @return bool: True, if no errors occur
      */
      function insertTussenRol($gebruikerId, $defaultRolId){
        $stmt = DB::conn()->prepare('INSERT INTO TussenRol(rolid, persoonid) VALUES(?, ?)');
        $stmt->bind_param('ii', $defaultRolId, $gebruikerId);
        $stmt->execute();
        $stmt->close();

        return true;
      }

      /**
      * Insert the hashed password in to the database
      * @param string wachtwoord: The users cleartext-password
      * @param int gebruikerId: The users id
      * @return bool: True, if no errors occur
      */
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

    /**
     * Transfet the user's session shoppingcart to the database
     * @param  array $winkelmand: The user's shoppingcart
     * @param  int $id: The user's id
     */
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

      /**
       * Check if the random id is already the id of an existing order
       * @return bool: The id of the existing order, if it extists.
       */
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

      /**
       * Create a new order in the Order table and link the user's shoppingcart to the order
       * @param  int $gebruikerId: The user's id
       * @param  array $winkelmand: The user's shoppingcart
       */
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

      /**
       * Link the user shoppingcart to an order
       * @param  array $winkelmand: The user's shoppingcart
       * @param  int $id: The id of the order to which the products of the shoppingcart should be linked
       */
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

      return true;
    }

    /**
     * Log the user in, in the 'login' session
     * @param  string $wachtwoord: The inserted password
     * @param  string $opgehaaldWachtwoord: The hashed password linked to the account
     * @param  int $id: The user's id
     */
    function logInSession($id){
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
      $_SESSION['login'][] = $id;
      $_SESSION['login'][] = $naam;
      $_SESSION['login'][] = $klantRolId;

      return true;
    }

    if(controlleerEmailAlInGebruik($email)){
      echo '<div class="warning"><b>Het door u opgegeven email adres is al in gebruik.</b></div>';
    }else{
      if(controlleerOvereenkomstWachtwoorden($wachtwoord, $herhaalWachtwoord)){
        if(voegToeAanDatabase($voornaam, $achternaam, $email, $woonplaats, $postcode, $straat, $huisnummer, $wachtwoord, $betaalWijze)){
          $gebruikerId = getGebruikerId($email);
          if(plaatsSessionWinkelmandInDatabase($winkelmand, $email)){
            if(logInSession($gebruikerId)){
              header("Refresh:0; url=/afrekenen");
            }
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
