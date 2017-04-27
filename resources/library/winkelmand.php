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

  public function plaatsDatabaseInWinkelmand($orchideeId, $gebruikerId){

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
      $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(orchideeid, orderid) VALUES(?m ?, ?)');
      $stmt->bind_param('iii', $orchideeId, $id);
      $stmt->execute();
      $stmt->close();
    }

    function insertBestaandeOrderRegel($orchideeId, $id){
      $orderRegelId = rand(1, 999999);
      $stmt = DB::conn()->prepare('INSERT INTO `OrderRegel`(orchideeid, orderid) VALUES(?, ?)');
      $stmt->bind_param('ii', $orchideeId, $id);
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

    function plaatsArtikelInWinkelmand($artikelId){
      array_push($_SESSION['winkelmand'], 'TEST');
      print_r($_SESSION['winkelmand']);
    }

    if(empty($_SESSION['winkelmand'])){
      maakWinkelmandSessionAan();
    }
    plaatsArtikelInWinkelmand($artikelId);

  }

}
