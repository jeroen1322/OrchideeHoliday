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
}
