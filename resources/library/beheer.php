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
}
