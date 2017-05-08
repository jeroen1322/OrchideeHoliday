<?php
class Artikel{
  public function informatie($artikelId){
    $informatie = array();

    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, langeOmschrijving, prijs, img FROM Orchidee WHERE id=?');
    $stmt->bind_param('i', $artikelId);
    $stmt->execute();
    $stmt->bind_result($id, $titel, $korteOmschrijving, $langeOmschrijving, $prijs, $img);
    while($stmt->fetch()){
      $informatie['id'] = $id;
      $informatie['titel'] = $titel;
      $informatie['korteOmschrijving'] = $korteOmschrijving;
      $informatie['langeOmschrijving'] = $langeOmschrijving;
      $informatie['prijs'] = $prijs;
      $informatie['img'] = $img;
    }
    $stmt->close();

    return $informatie;
  }

  public function thumbInfo($artikelId){
    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, prijs, img FROM Orchidee WHERE id=?');
    $stmt->bind_param('i', $artikelId);
    $stmt->execute();
    $stmt->bind_result($id, $titel, $korteOmschrijving, $prijs, $img);
    while($stmt->fetch()){
      $thumbInfo['id'] = $id;
      $thumbInfo['titel'] = $titel;
      $thumbInfo['korteOmschrijving'] = $korteOmschrijving;
      $thumbInfo['prijs'] = $prijs;
      $thumbInfo['img'] = $img;
    }
    $stmt->close();

    return $thumbInfo;
  }

  public function zoekBestVerkocht(){

    function getBesteldeOrders(){
      $stmt = DB::conn()->prepare('SELECT id FROM `Order` WHERE besteld=1');
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
    function getOrderRegelOrchideeen($ids){
      $orchideeen = array();
      foreach($ids as $id){
        $stmt = DB::conn()->prepare('SELECT orchideeid FROM `OrderRegel` WHERE orderid=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($orchidee);
        while($stmt->fetch()){
          $orchideeen[] = $orchidee;
        }
        $stmt->close();
      }

      if(!empty($orchideeen)){
        return $orchideeen;
      }
    }

    function telAantalVerkochtOp($orchideeen){
      return array_count_values($orchideeen);
    }

    $ids = getBesteldeOrders();
    $orchideeen = getOrderRegelOrchideeen($ids);

    if(!empty($orchideeen)){
      return TelAantalVerkochtOp($orchideeen);
    }

  }
}
