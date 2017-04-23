<?php
class Bestel{
  public function plaatsInWinkelmand($orchideeId, $gebruikerId){

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
      $stmt->bind_param('iii', $orchideeId, $id);
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
}
