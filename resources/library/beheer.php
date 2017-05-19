<?php

class Beheer{
  public function getAlleOrders(){

    $stmt = DB::conn()->prepare('SELECT id FROM `Order`');
    $stmt->execute();
    $stmt->bind_result($id);
    while($stmt->fetch()){
      $ids[] = $id;
    }
    $stmt->close();

    if(!empty($ids)){
      return $ids;
    }
  }

  public function getOrderProducten($order){
    $stmt = DB::conn()->prepare('SELECT orchideeid FROM `OrderRegel` WHERE orderid=?');
    $stmt->bind_param('i', $order);
    $stmt->execute();
    $stmt->bind_result($id);
    while($stmt->fetch()){
      $ids[] = $id;
    }
    $stmt->close();

    if(!empty($ids)){
      return $ids;
    }
  }

  public function getUsedPage($order, $product){
    $stmt = DB::conn()->prepare('SELECT pagina FROM `OrderRegel` WHERE orderid=? AND orchideeid=?');
    $stmt->bind_param('ii', $order, $product);
    $stmt->execute();
    $stmt->bind_result($pagina);
    $stmt->fetch();
    $stmt->close();

    return $pagina;
  }

   public function getPersoonConnectedToOrder($order){
    $stmt = DB::conn()->prepare('SELECT persoon FROM `Order` WHERE id=?');
    $stmt->bind_param('i', $order);
    $stmt->execute();
    $stmt->bind_result($persoon);
    $stmt->fetch();
    $stmt->close();

    $stmt = DB::conn()->prepare('SELECT voornaam, achternaam FROM Persoon WHERE id=?');
    $stmt->bind_param('i', $persoon);
    $stmt->execute();
    $stmt->bind_result($voornaam, $achternaam);
    $stmt->fetch();
    $stmt->close();

    $naam = $voornaam . ' ' . $achternaam;
    return $naam;
  }

  public function getOrderMeta($order){
    $stmt = DB::conn()->prepare("SELECT id, persoon, verzendWijze, betaalWijze, orderdatum FROM `Order` WHERE id=?");
    $stmt->bind_param('i', $order);
    $stmt->execute();
    $stmt->bind_result($id, $persoon, $verzendWijze, $betaalWijze, $orderdatum);
    while($stmt->fetch()){
      $meta['id'] = $id;
      $meta['persoon'] = $persoon;
      $meta['verzendWijze'] = $verzendWijze;
      $meta['betaalWijze'] = $betaalWijze;
      $meta['orderdatum'] = $orderdatum;
    }

    return $meta;
  }
}
