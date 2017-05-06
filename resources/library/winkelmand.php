<?php
class Winkelmand{
  public function getArtikelen($gebruiker){

    function getOpenOrder($gebruiker){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE Persoon=? AND besteld=0');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

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

  public function plaatsInDatabaseWinkelmand($orchideeId, $gebruikerId){

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

    function getBetaalWijze($gebruiker){
      $stmt = DB::conn()->prepare('SELECT betaalwijze FROM Persoon WHERE id=?');
      $stmt->bind_param('i', $gebruiker);
      $stmt->execute();
      $stmt->bind_result($betaalwijze);
      $stmt->fetch();
      $stmt->close();

      return $betaalwijze;
    }

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

    $order = getOrder($gebruiker);
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
      $stmt = DB::conn()->prepare('SELECT id FROM Persoon WHERE email=? and anoniem=1');
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
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE persoon=? AND anoniem=1');
      $stmt->bind_param('i', $id);
      $stmt->execute();
      $stmt->bind_result($orderId);
      $stmt->fetch();
      $stmt->close();

      return $orderId;
    }

    function anoniemeOrderRegel($id, $winkelmand){
      $randId = rand(1, 99999);
      $orderId = getOrderId($id);

      foreach($winkelmand as $item){
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
        }else{
          anoniemeOrderMetOpmerking($id, $verzendWijze, $opmerking);
        }
      }

      return true;
    }

    if(registreer($postArray, $winkelmand, $verzendWijze)){
      session_unset($_SESSION);
      header("Refresh:0; url=/");
    }
  }

  public function annuleerSessionOrder(){
    session_unset($_SESSION);
    header("Refresh:0; url=/");
  }
}
