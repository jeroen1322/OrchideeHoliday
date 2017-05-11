<?php
class winkelmandLogin{

  /**
   * When a user that is not logged in wants to log in when completing an order, this function is called.
   * Their session shoppingcart will be transferred to the database.
   * @param  array $winkelmand: The user's shoppingcart
   * @param  string $email: The inserted email adres
   * @param  string $wachtwoord: The inserted password
   * @return [type]             [description]
   */
  public function login($winkelmand, $email, $wachtwoord){

    /**
     * Check if the inserted information aren't empty string
     * @param  string $email: The inserted email
     * @param  string $wachtwoord: The inserted wachtwoord
     * @return bool: True, if no errors occur
     */
    function controlleerInvullingVelden($email, $wachtwoord){
      if($email != '' && $wachtwoord != ''){
        return true;
      }
    }

    /**
     * Get the id of the account where the account's email match with the inserted email adres
     * @param  string $email: The inserted email adres
     * @return int $klantId: The id of the account where the emails match
     */
    function getAccountId($email){
      $stmt = DB::conn()->prepare("SELECT id FROM Persoon WHERE email=?");
      $stmt->bind_param("s", $email);
      $stmt->execute();

      $stmt->bind_result($klantId);
      $stmt->fetch();
      $stmt->close();

      return $klantId;
    }

    /**
     * Get the hashed password of the user's account
     * @param  int $id: The user's id
     * @return string $ww: The hashed password
     */
    function getWachtwoord($id){
      $ww_stmt = DB::conn()->prepare("SELECT wachtwoord FROM Wachtwoord WHERE persoon=?");
      $ww_stmt->bind_param("i", $id);
      $ww_stmt->execute();

      $ww_stmt->bind_result($ww);
      $ww_stmt->fetch();
      $ww_stmt->close();

      return $ww;
    }

    /**
     * Transfet the user's session shoppingcart to the database
     * @param  array $winkelmand: The user's shoppingcart
     * @param  int $id: The user's id
     */
    function plaatsSessionWinkelmandInDatabase($winkelmand, $id){

      /**
       * Check if the user already has an existing open order
       * @param  int $id: The user's id
       * @return int $bestaandeOrder: The id of the user's open order
       */
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
    }

    /**
     * Log the user in, in the 'login' session
     * @param  string $wachtwoord: The inserted password
     * @param  string $opgehaaldWachtwoord: The hashed password linked to the account
     * @param  int $id: The user's id
     */
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
