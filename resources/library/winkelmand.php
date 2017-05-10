<?php
class Winkelmand{

  /**
  *Get all the products that a user added to their shoppingcart
  *
  *@param: The user's id
  *@return: An array of the id's of products that the user has in the shoppingcart
  */
  public function getArtikelen($gebruiker){

    /**
    *Get the id of the Order that is assigned to the user and is not completed
    *
    *@param: The user's id
    *@return: The id of the user's open order
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
    *Get the ids of the products that are linked to the user's order
    *
    *@param: The user's id
    *@return: An array of the product id's that are linked to the user's order
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
  *This function contains the entire procedure to place a product in the shoppingcart of the user.
  *
  *@param orchideeId: Product's id
  *@param gebruikerId: User's id
  */
  public function plaatsInDatabaseWinkelmand($orchideeId, $gebruikerId){

    /**
    *Get the id of the user's open order
    *
    *@param: User's id
    *@return: If there is an open order, it returns the id of the open order
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
    *Check if the random id already exists in the Order table
    *
    *@param: The id that should be checked
    *@return: True, if the id does NOT already exist in the Order table
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
    *Get the betaalwijze that is linked to the users account
    *
    *@param: The user's id
    *@return: The id of the linked betaalwijze
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
    *Create an Order by inserting data in to the Order table.
    *After the Order is created the product that the user put in their shoppingcart
    *
    *@param orchideeId: The product's id that will be put in the OrderRegel
    *@param gebruikerId: The user's id that of the user to which the shoppingcart is linked
    */
    function maakOrder($orchideeId, $gebruikerId){
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
      $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid) VALUES(?, ?, ?)');
      $stmt->bind_param('iii', $orderRegelId, $orchideeId, $id);
      $stmt->execute();
      $stmt->close();
    }

    /**
    *If there already exists an open order, put a product in an orderregel and link that to the user's open order
    *
    *@param orchideeId: The product's id
    *@param id: The user's id to which the order is linked to
    */
    function insertBestaandeOrderRegel($orchideeId, $id){
      $orderRegelId = rand(1, 999999);
      $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(id, orchideeid, orderid) VALUES(?, ?, ?)');
      $stmt->bind_param('iii', $orderRegelId, $orchideeId, $id);
      $stmt->execute();
      $stmt->close();
    }

    $bestaandeOrder = controlleerBestaandeOrder($gebruikerId);
    if(empty($bestaandeOrder)){
      maakOrder($orchideeId, $gebruikerId);
    }else{
      insertBestaandeOrderRegel($orchideeId, $bestaandeOrder);
    }
  }

  public function inputOpmerking($opmerking, $gebruiker){
    function getOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($bestaandeOrder);
      $stmt->fetch();
      $stmt->close();

      return $bestaandeOrder;
    }

    function inputOpmerkingInOrder($opmerking, $id){
      $stmt = DB::conn()->prepare('UPDATE`Order` SET opmerking=? WHERE id=?');
      $stmt->bind_param('si', $opmerking, $id);
      $stmt->execute();
      $stmt->close();
    }

    $order = getOrder($gebruiker);
    inputOpmerkingInOrder($opmerking, $order);
  }

  public function inputVerzendWijze($verzendWijze, $gebruiker){
    function getVerzendWijzeId($verzendWijze){
      $stmt = DB::conn()->prepare('SELECT id FROM verzendWijze WHERE omschrijving=?');
      $stmt->bind_param('s', $verzendWijze);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      return $id;
    }
    function getVerzendWijzeOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($bestaandeOrder);
      $stmt->fetch();
      $stmt->close();

      return $bestaandeOrder;
    }
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

  public function plaatsInSessionWinkelmand($artikelId){

    function maakWinkelmandSessionAan(){
      $_SESSION['winkelmand'] = array();
    }

    function plaatsArtikelInSession($artikelId){
      array_push($_SESSION['winkelmand'], $artikelId);
    }

    if(empty($_SESSION['winkelmand'])){
      maakWinkelmandSessionAan();
    }
    plaatsArtikelInSession($artikelId);

  }

  public function deleteFromSessionWinkelmand($artikelId){
    $key = array_search($artikelId, $_SESSION['winkelmand']);
    if($key !== false){
      unset($_SESSION['winkelmand'][$key]);
      header("Refresh:0; url=/winkelmand");
    }
  }

  public function deleteFromDatabaseWinkelmand($artikelId, $gebruikerId){

    function getOrder($gebruikerId){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruikerId);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    function getOrderRegelId($artikelId, $orderid){
      $stmt = DB::conn()->prepare('SELECT id FROM `OrderRegel` WHERE orchideeid=? AND orderid=?');
      $stmt->bind_param('ii', $artikelId, $orderid);
      $stmt->execute();
      $stmt->bind_result($orderRegelId);
      $stmt->fetch();
      $stmt->close();

      return $orderRegelId;
    }

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

  public function rondBestellingAf($gebruiker){
    function getOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    function rondOrderAf($order){
      $stmt = DB::conn()->prepare('UPDATE `Order` SET besteld=1 WHERE id=?');
      $stmt->bind_param('i', $order);
      $stmt->execute();
      $stmt->close();
    }

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

  public function annuleerOrder($gebruiker){
    function getOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    function verwijderArtikelen($order){
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

  public function rondSessionBestellingAf($postArray, $winkelmand, $verzendWijze){

    function getId($email){
      $stmt = DB::conn()->prepare('SELECT id FROM Persoon WHERE email=? AND anoniem=1');
      $stmt->bind_param('s', $email);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      return $id;
    }

    function getVerzendWijzeId($verzendWijze){
      $stmt = DB::conn()->prepare('SELECT id FROM verzendWijze WHERE omschrijving=?');
      $stmt->bind_param('s', $verzendWijze);
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      return $id;
    }

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

    function getOrderId($id){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND anoniem=1 AND besteld=1');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

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

  public function annuleerSessionOrder(){
    session_unset($_SESSION);
    header("Refresh:0; url=/");
  }
}
