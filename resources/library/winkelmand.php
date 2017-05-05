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

    function maakOrder($orchideeId, $gebruikerId){

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

  public function rondSessionBestellingAf(){
    echo 'TODO AFRONDEN';
  }

  public function annuleerSessionOrder(){
    echo 'TODO ANNULEREN';
  }
}
