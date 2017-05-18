<?php
class Winkelmand{

  /**
  * Get all the products that a user added to their shoppingcart
  *
  * @param int $gebruiker: The user's id
  * @return array $orderID: An array of the id's of products that the user has in the shoppingcart
  */
  public function getArtikelen($gebruiker){

    /**
    * Get the id of the Order that is assigned to the user and is not completed
    *
    * @param int $gebruiker: The user's id
    * @return int $orderId: The id of the user's open order
    */
    function getOpenOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    /**
    * Get the ids of the products that are linked to the user's order
    *
    * @param int $gebruiker: The user's id
    * @return array $artikelen: An array of the product id's that are linked to the user's order
    */
    function getArtikelIds($gebruiker){

      $order = getOpenOrder($gebruiker);
      $stmt = DB::conn()->prepare('SELECT id FROM `OrderRegel` WHERE orderid=?');
      $stmt->bind_param('i', $order);
      $stmt->execute();
      $stmt->bind_result($orderRegel);
      while($stmt->fetch()){
        $orderRegels[] = $orderRegel;
      }
      $stmt->close();

      if(!empty($orderRegels)){
        $orchideeen = array();
        foreach($orderRegels as $o){
          $stmt = DB::conn()->prepare('SELECT orchideeid FROM `OrderRegel` WHERE id=?');
          $stmt->bind_param('i', $o);
          $stmt->execute();
          $stmt->bind_result($orchidee);
          while($stmt->fetch()){
            $orchideeen[] = $orchidee;
          }
          $stmt->close();
        }
        return $orchideeen;
      }
    }
    $artikelen = getArtikelIds($gebruiker);
    return $artikelen;

  }

  /**
  * This function contains the entire procedure to place a product in the shoppingcart of the user.
  *
  * @param int $orchideeId: Product's id
  * @param int $gebruikerId: User's id
  */
  public function plaatsInDatabaseWinkelmand($orchideeId, $gebruikerId, $pagina){

    /**
    *Get the id of the user's open order
    *
    *@param int $gebruikerId: User's id
    *@return int $bestaandeOrder: If there is an open order, it returns the id of the open order
    */
    function controlleerBestaandeOrder($gebruikerId){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruikerId);
      $stmt->execute();
      $stmt->bind_result($bestaandeOrder);
      $stmt->fetch();
      $stmt->close();

      if(!empty($bestaandeOrder)){
        return $bestaandeOrder;
      }
    }

    /**
    * Check if the random id already exists in the Order table
    *
    * @param int $rand: The id that should be checked
    * @return bool: True, if the id does NOT already exist in the Order table
    */
    function controlleerRand($rand){
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
    * Get the betaalwijze that is linked to the users account
    *
    * @param int $gebruiker: The user's id
    * @return int $betaalwijze: The id of the linked betaalwijze
    */
    function getBetaalWijze($gebruiker){
      $stmt = DB::conn()->prepare('SELECT betaalwijze FROM Persoon WHERE id=?');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($betaalwijze);
      $stmt->fetch();
      $stmt->close();

      return $betaalwijze;
    }

    /**
    * Create an Order by inserting data in to the Order table.
    * After the Order is created the product that the user put in their shoppingcart
    *
    * @param int $orchideeId: The product's id that will be put in the OrderRegel
    * @param ing $gebruikerId: The user's id that of the user to which the shoppingcart is linked
    */
    function maakOrder($orchideeId, $gebruikerId, $pagina){
      $anoniem = 0;
      $besteld = 0;
      $randId = rand(1, 999999);
      $orderDatum = date('d-m-Y');
      $betaalwijze = getBetaalWijze($gebruikerId);
      if(controlleerRand($randId)){
        $id = $randId;
      }else{
        $id = rand(1, 9999999);
      }

      $stmt = DB::conn()->prepare('INSERT INTO `Order`(id, persoon, besteld, orderdatum, betaalWijze, anoniem) VALUES(?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('iissii', $id, $gebruikerId, $besteld, $orderDatum, $betaalwijze, $anoniem);
      $stmt->execute();
      $stmt->close();

      $orderRegelId = rand(1, 999999);
      $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid, pagina) VALUES(?, ?, ?, ?)');
      $stmt->bind_param('iiis', $orderRegelId, $orchideeId, $id, $pagina);
      $stmt->execute();
      $stmt->close();
    }

    /**
    * If there already exists an open order, put a product in an orderregel and link that to the user's open order
    *
    * @param int $orchideeId: The product's id
    * @param int $id: The user's id to which the order is linked to
    */
    function insertBestaandeOrderRegel($orchideeId, $id, $pagina){
      $orderRegelId = rand(1, 999999);
      $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid, pagina) VALUES(?, ?, ?, ?)');
      $stmt->bind_param('iiis', $orderRegelId, $orchideeId, $id, $pagina);
      $stmt->execute();
      $stmt->close();
    }

    $bestaandeOrder = controlleerBestaandeOrder($gebruikerId);
    if(empty($bestaandeOrder)){
      maakOrder($orchideeId, $gebruikerId, $pagina);
    }else{
      insertBestaandeOrderRegel($orchideeId, $bestaandeOrder, $pagina);
    }
  }

  /**
  * If the user has written a remark in the Afrekenen page, it will be inserted in to the order
  *
  * @param string $opmerking: The user's remark
  * @param int $gebruiker: The user's id
  */
  public function inputOpmerking($opmerking, $gebruiker){

    /**
    * The the id of the order where the remark will be inserted to
    *
    * @param int gebruiker: The user's id
    * @return int $betaandeOrder: The id of the open order that is linked to the user
    */
    function getOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($bestaandeOrder);
      $stmt->fetch();
      $stmt->close();

      return $bestaandeOrder;
    }

    /**
    * Insert the remark in the order
    *
    * @param string $opmerking: The user's remark
    * @param int $id: The id of the order that the remark should be inserted in to
    */
    function inputOpmerkingInOrder($opmerking, $id){
      $stmt = DB::conn()->prepare('UPDATE`Order` SET opmerking=? WHERE id=?');
      $stmt->bind_param('si', $opmerking, $id);
      $stmt->execute();
      $stmt->close();
    }

    $order = getOrder($gebruiker);
    inputOpmerkingInOrder($opmerking, $order);
  }

  /**
  * Inserts the selected verzendwijze in to the user's Order
  *
  * @param string $verzendwijze: The user's verzendwijze
  * @param int $gebruiker: The user's id
  */
  public function inputVerzendWijze($verzendWijze, $gebruiker){

    /**
    * Get the id of the user's selected verzendwijze
    * @param string $verzendWijze: The user's selected verzendwijze
    * @return int $id: The if the of the verzendwijze where the omschrijving matches the selected verzendwijze
    */
    function getVerzendWijzeId($verzendWijze){
      $stmt = DB::conn()->prepare('SELECT id FROM verzendWijze WHERE omschrijving=?');
      $stmt->bind_param('s', $verzendWijze);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      return $id;
    }

    /**
    * Get the order in to which the verzendwijze should be inserted
    * @param int $gebruiker: The user's id
    * @param int $id: The if of the user's open order
    */
    function getVerzendWijzeOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($bestaandeOrder);
      $stmt->fetch();
      $stmt->close();

      return $bestaandeOrder;
    }

    /**
    * Insert the verzendwijze in the user's order
    *
    * @param int $verzendId: The id of the user's selected verzendwijze
    * @param int $id: The id of the user's open order
    */
    function inputVerzendWijzeInOrder($verzendId, $id){
      $stmt = DB::conn()->prepare('UPDATE`Order` SET verzendWijze=? WHERE id=?');
      $stmt->bind_param('ii', $verzendId, $id);
      $stmt->execute();
      $stmt->close();
    }


    $order = getVerzendWijzeOrder($gebruiker);
    $verzendId = getVerzendWijzeId($verzendWijze);
    inputVerzendWijzeInOrder($verzendId, $order);
  }

  /**
  * If the user is not logged in, their shoppingcart will exist in the session 'winkelmand'
  *
  * @param int $id: The id of the product that should be placed in the order
  */
  public function plaatsInSessionWinkelmand($artikelId){

    /**
    *Create the session
    */
    function maakWinkelmandSessionAan(){
      $_SESSION['winkelmand'] = array();
    }

    /**
    *Place the id of the selected product in the 'winkelmand' session
    *
    *@param int $artikelId: The product's id
    */
    function plaatsArtikelInSession($artikelId){
      array_push($_SESSION['winkelmand'], $artikelId);
    }

    if(empty($_SESSION['winkelmand'])){
      maakWinkelmandSessionAan();
    }
    plaatsArtikelInSession($artikelId);

  }

  /**
  * Delete a product from the 'winkelmand' session
  *
  * @param int $artikelId: The product's id
  */
  public function deleteFromSessionWinkelmand($artikelId){
    $key = array_search($artikelId, $_SESSION['winkelmand']);
    if($key !== false){
      unset($_SESSION['winkelmand'][$key]);
      header("Refresh:0; url=/winkelmand");
    }
  }

  /**
  * Delete a product from a user's shopppingcart
  *
  * @param int $artikelId: The product's id
  * @param int $gebruikerId: The user's id
  */
  public function deleteFromDatabaseWinkelmand($artikelId, $gebruikerId){

    /**
    * Get the id of the user's order
    *
    * @param int $gebruikerId: The user's id
    * @return int $orderId: The id of the user's order
    */
    function getOrder($gebruikerId){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruikerId);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    /**
     * Get the id of the orderregel that is linked to the user's order and the selected product
     * @param  int $artikelId: The user's id
     * @param  int $orderid: The id of the user's order
     * @return int $orderRegelId: The id of the orderregel
     */
    function getOrderRegelId($artikelId, $orderid){
      $stmt = DB::conn()->prepare('SELECT id FROM `OrderRegel` WHERE orchideeid=? AND orderid=?');
      $stmt->bind_param('ii', $artikelId, $orderid);
      $stmt->execute();
      $stmt->bind_result($orderRegelId);
      $stmt->fetch();
      $stmt->close();

      return $orderRegelId;
    }
    /**
     * Delete the orderregel in the OrderRegel table
     * @param  int $artikelId: The product id
     * @param  int $orderRegelId: The id of the orderRegel that should be deleted
     */
    function delete($artikelId, $orderRegelId){
      $stmt = DB::conn()->prepare('DELETE FROM `OrderRegel` WHERE orchideeid=? AND id=?');
      $stmt->bind_param('ii', $artikelId, $orderRegelId);
      $stmt->execute();
      $stmt->close();
    }

    $order = getOrder($gebruikerId);
    $orderRegelId = getOrderRegelId($artikelId, $order);
    delete($artikelId, $orderRegelId);

    return true;
  }

  /**
   * The rondBestellingAf function contains everything to complete the user's order
   * After it completed the order, the user will receive an email with the order details
   * @param int $gebruiker: The user's id
   */
  public function rondBestellingAf($gebruiker){

    /**
     * Get the user's order that should be completed
     * @param  int $gebruiker: The user's id
     * @return int $orderId: The id of the user's order
     */
    function getOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    /**
     * Set the column besteld to 1, which indicates that the order is completed
     * @param  int $order: The order that should be marked as completed
     */
    function rondOrderAf($order){
      $stmt = DB::conn()->prepare('UPDATE `Order` SET besteld=1 WHERE id=?');
      $stmt->bind_param('i', $order);
      $stmt->execute();
      $stmt->close();
    }

    /**
     * Get the id's of the products that are linked to the order that has just been completed
     * @param  int $id: The id of the order to which the products are linked
     * @return array $ids: An array of all the products linked to the order
     */
    function getWinkelmand($order){
      $stmt = DB::conn()->prepare('SELECT orchideeid FROM `OrderRegel` WHERE orderid=?');
      $stmt->bind_param('i', $order);
      $stmt->execute();
      $stmt->bind_result($id);
      while($stmt->fetch()){
        $ids[] = $id;
      }
      $stmt->close();

      return $ids;
    }
    /**
     * Get the user's information like the name and email adres
     * @param  int $gebruiker: The user's id
     * @return array $gebruikerInfo: Array with the user's information
     */
    function getGebruikerInfo($gebruiker){
      $stmt = DB::conn()->prepare('SELECT voornaam, achternaam, email FROM Persoon WHERE id=?');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($voornaam, $achternaam, $email);
      while($stmt->fetch()){
        $gebruikerInfo['voornaam'] = $voornaam;
        $gebruikerInfo['achternaam'] = $achternaam;
        $gebruikerInfo['email'] = $email;
      }
      $stmt->close();

      return $gebruikerInfo;
    }

    $order = getOrder($gebruiker);
    $winkelmand =getWinkelmand($order);
    $gebruikerInfo = getGebruikerInfo($gebruiker);

    bestellingAfronden($winkelmand, $gebruikerInfo);
    rondOrderAf($order);
    header("Refresh:0; url=/");
  }

  /**
   * Cancel an order and unlink all the products to that order
   * @param  int $gebruiker: The user's id that is linked to the order
   */
  public function annuleerOrder($gebruiker){

    /**
     * Get the id of the user's order
     * @param  int $gebruiker: The user's id
     * @return int $orderId: The id of the user's order
     */
    function getOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    /**
     * Delete the product-link to the order
     * @param  int $order: The user's order id
     */
    function verwijderArtikelen($order){

      /**
       * Get the id's of the orderregels that are linked to the user's order
       * @param  int $order: The id of the user's order
       * @return array $regels: An array with the id's of orderregels that are linked to the user's order
       */
      function getOrderRegelArtikelen($order){
        $stmt = DB::conn()->prepare('SELECT id FROM `OrderRegel` WHERE orderid=?');
        $stmt->bind_param('i', $order);
        $stmt->execute();
        $stmt->bind_result($orderRegelId);
        while($stmt->fetch()){
          $regels[] = $orderRegelId;
        }
        $stmt->close();

        return $regels;
      }

      /**
       * Delete the orderregels from the OrderRegel table
       * @param  array $orderRegelArtikelen: An array with the id's of the orderregels that should be deleted
       */
      function verwijderOrderRegelArtikelen($orderRegelArtikelen){
        foreach($orderRegelArtikelen as $o){
          $stmt = DB::conn()->prepare('DELETE FROM `OrderRegel` WHERE id=?');
          $stmt->bind_param('i', $o);
          $stmt->execute();
          $stmt->close();
        }
      }

      $orderRegels = getOrderRegelArtikelen($order);
      verwijderOrderRegelArtikelen($orderRegels);

    }

    $order = getOrder($gebruiker);
    verwijderArtikelen($order);
    header("Refresh:0; url=/");
  }

  /**
   * This function encapuses the entire procedure to complete an order of a non-registered user
   * @param  array $postarray: An array with POST data of the user's inserted information
   * @param  array $winkelmand: Array with product id's that the user has in the shoppingcart
   * @param  string $verzendWijze: The id of the user's selected verzendwijze
   */
  public function rondSessionBestellingAf($postArray, $winkelmand, $verzendWijze){

    /**
     * Get the id of a registered user by matching the email
     * @param  string $email: The email which will be compared
     * @return int $id: The id of the user where the emails match
     */
    function getId($email){
      $stmt = DB::conn()->prepare('SELECT id FROM Persoon WHERE email=? AND anoniem=1');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      return $id;
    }

    /**
     * Get the id of the verzendwijze where the omschrijving matches the parameter
     * @param  string $verzendWijze: The user selected verzendwijze
     * @return int $id: The id of the verzendwijze
     */
    function getVerzendWijzeId($verzendWijze){
      $stmt = DB::conn()->prepare('SELECT id FROM verzendWijze WHERE omschrijving=?');
      $stmt->bind_param('s', $verzendWijze);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      return $id;
    }

    /**
     * Insert the order information in the Order table
     * @param  int $id: The (anonymously-registered) user's id
     * @param  int $verzendWijze: The id of the user selected verzendwijze
     */
    function anoniemeOrder($id, $verzendWijze){
      $randId = rand(1, 99999);
      $besteld = 1;
      $anoniem = 1;
      $verzendId = getVerzendWijzeId($verzendWijze);
      $orderdatum = date('d-m-Y');

      $stmt = DB::conn()->prepare('INSERT INTO `Order`(id, persoon, besteld, verzendWijze, orderdatum, anoniem) VALUES(?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('iiiisi', $randId, $id, $besteld, $verzendId, $orderdatum, $anoniem);
      $stmt->execute();
      $stmt->close();
    }

    /**
     * Insert the order information including the user's remark
     * @param  int $id: The (anonymously-registered) user's email
     * @param  int $verzendWijze: The id of the user selected verzendwijze
     * @param  string $opmerking: The user's order remark
     */
    function anoniemeOrderMetOpmerking($id, $verzendWijze, $opmerking){
      $randId = rand(1, 99999);
      $besteld = 1;
      $anoniem = 1;
      $verzendId = getVerzendWijzeId($verzendWijze);
      $orderdatum = date('d-m-Y');

      $stmt = DB::conn()->prepare('INSERT INTO `Order`(id, persoon, besteld, verzendWijze, orderdatum, opmerking, anoniem) VALUES(?, ?, ?, ?, ?, ?, ?)');
      $stmt->bind_param('iiiissi', $randId, $id, $besteld, $verzendId, $orderdatum, $opmerking, $anoniem);
      $stmt->execute();
      $stmt->close();
    }

    /**
     * Get the id of the just created order
     * @param  int $id: The user's id
     * @return int $orderId: The id of the user's order
     */
    function getOrderId($id){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND anoniem=1 AND besteld=1');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    /**
     * Link the product of the user's shoppingcart to the user's order
     * @param  int $id: The user's id
     * @param  array $winkelmand: The user's shoppingcart
     */
    function anoniemeOrderRegel($id, $winkelmand){
      $orderId = getOrderId($id);

      foreach($winkelmand as $item){
        $randId = rand(1, 99999);
        $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid) VALUES (?, ?, ?)');
        $stmt->bind_param('iii', $randId, $item, $orderId);
        $stmt->execute();
        $stmt->close();
      }
    }

    /**
     * Creating a anonymous account but with the shipping information entered by the user
     * @param  array $postArray: The array of the user's shipping information
     * @param  array $winkelmand: The user's shoppingcart
     * @param  int $verzendWijze: The id of the user's selected verzendwijze
     * @return bool: True, if no error occures
     */
    function registreer($postArray, $winkelmand, $verzendWijze){
      $email = $postArray['email'];
      $woonplaats = $postArray['woonplaats'];
      $postcode = $postArray['postcode'];
      $straat = $postArray['straat'];
      $huisnummer = $postArray['huisnummer'];
      $anoniemeGebruikerHerbruik = getId($email);
      $anoniem = 1;

      if(!empty($postArray['opmerking'])){
        $opmerking = $postArray['opmerking'];
      }

      if(empty($anoniemeGebruikerHerbruik)){
        $stmt = DB::conn()->prepare('INSERT INTO Persoon(email, woonplaats, postcode, straat, huisnummer, anoniem) VALUES(?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssi', $email, $woonplaats, $postcode, $straat, $huisnummer, $anoniem);
        $stmt->execute();
        $stmt->close();

        $id = getId($email);
        if(empty($opmerking)){
          anoniemeOrder($id, $verzendWijze);
        }else{
          anoniemeOrderMetOpmerking($id, $verzendWijze, $opmerking);
        }
        anoniemeOrderRegel($id, $winkelmand);
      }else{
        $id = getId($email);
        if(empty($opmerking)){
          anoniemeOrder($id, $verzendWijze);
          anoniemeOrderRegel($id, $winkelmand);
        }else{
          anoniemeOrderMetOpmerking($id, $verzendWijze, $opmerking);
          anoniemeOrderRegel($id, $winkelmand);
        }
      }

      return true;
    }

    if(registreer($postArray, $winkelmand, $verzendWijze)){
      anoniemeGebruikerMail($postArray['email'], $winkelmand);
      header("Refresh:0; url=/");
      session_unset($_SESSION);
    }
  }

  /**
   * Delete the user's session and hence destroying their shoppingcart
   */
  public function annuleerSessionOrder(){
    session_unset($_SESSION);
    header("Refresh:0; url=/");
  }
}
