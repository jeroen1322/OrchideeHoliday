<?php
class Artikel{

  /*
  Get all the information of a product where the product's id matches the artikelId parameter

  @param: The int to which the id's are matched
  @return: an array with all the products information
  */
  public function informatie($artikelId){
    $informatie = array();

    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, langeOmschrijving, prijs, img, soort FROM Orchidee WHERE id=? AND verwijderd=0');
    $stmt->bind_param('i', $artikelId);
    $stmt->execute();
    $stmt->bind_result($id, $titel, $korteOmschrijving, $langeOmschrijving, $prijs, $img, $soort);
    while($stmt->fetch()){
      $informatie['id'] = $id;
      $informatie['titel'] = $titel;
      $informatie['korteOmschrijving'] = $korteOmschrijving;
      $informatie['langeOmschrijving'] = $langeOmschrijving;
      $informatie['prijs'] = $prijs;
      $informatie['img'] = $img;
      $informatie['soort'] = $soort;
    }
    $stmt->close();

    return $informatie;
  }

  /*
  Get the omschrijving that matches with the parameter id in the artikelGroep table

  @param: The int to which the id's are matched
  @return: The omschrijving, if it matches
  */
  public function parseSoort($id){
    $stmt = DB::conn()->prepare('SELECT omschrijving FROM artikelGroep WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($omschrijving);
    $stmt->fetch();
    $stmt->close();

    return $omschrijving;
  }

  /*
  Get some information of a product that will be displayed in the product's thumbnail

  @param: The int to which the product's id is matched
  @return: An array with all the information that is relevant to the thumbnail of the product
  */
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

  /*
  Get the best selling products

  @return: If there are sold products, it will return an array with the id of the product
  and the number of times that that product is sold.
  */
  public function zoekBestVerkocht(){

    /*
    Get the id from orders that are finished

    @return: Returns an array with the id's of finished orders
    */
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

    /*
    Get all the ids from the products that are included in the orders

    @param: An array with the id's from the finished orders
    @return: An array with all the product id's that are related to the finished order
    */
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

    /*
    Count the amount of values in the array

    @param: An array
    @return: A number of the counted amount of values in the array
    */
    function telAantalVerkochtOp($orchideeen){
      return array_count_values($orchideeen);
    }

    $ids = getBesteldeOrders();
    $orchideeen = getOrderRegelOrchideeen($ids);

    if(!empty($orchideeen)){
      return TelAantalVerkochtOp($orchideeen);
    }

  }

  /*
  Get the products that will be displayed in the slider on Home.
  The function will return the best selling items

  @return: An array with product id's
  */
  public function getSliderArtikelen(){

    /*
    Get the id from orders that are finished

    @return: Returns an array with the id's of finished orders
    */
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

    /*
    Get all the ids from the products that are included in the orders

    @param: An array with the id's from the finished orders
    @return: An array with all the product id's that are related to the finished order
    */
    function getOrderRegelOrchideeen($ids){
      $orchideeen = array();
      if(!empty($ids)){
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
    }

    /*
    Filter the 'deleted' product. If a product is deleted, a.k.a set as unavailable, the product's verwijder column will be set to 1.
    A user can NOT have a deleted product in their Favorieten.

    @param: An array with all the product id's from getFavs();
    @return: An array with all the product id's that are NOT deleted
    */
    function filterDeleted($artikelen){
      if(!empty($artikelen)){
        foreach($artikelen as $a){
          $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE id=? AND verwijderd=0');
          $stmt->bind_param('i', $a);
          $stmt->execute();
          $stmt->bind_result($id);
          while($stmt->fetch()){
            $ids[] = $id;
          }
          $stmt->close();
        }

        if(!empty($ids)){
          return $ids;
        }
      }
    }

    $ids = getBesteldeOrders();
    $artikelen = getOrderRegelOrchideeen($ids);
    $filtered = filterDeleted($artikelen);

    if(!empty($filtered)){
      return $filtered;
    }
  }

  /*
  Get the id's of products (that are not deleted)

  @return: An array with the id's of products
  */
  public function getAllArtikelen(){
    $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE verwijderd=0');
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

  /*
  Mark a product as deleted by setting the verwijderd column to 1

  @param: The products id
  @return: True, if no errors occured
  */
  public function verwijderArtikel($artikel){

    $stmt = DB::conn()->prepare('UPDATE Orchidee SET verwijderd=1 WHERE id=?');
    $stmt->bind_param('i', $artikel);
    $stmt->execute();
    $stmt->close();

    return true;
  }

}
