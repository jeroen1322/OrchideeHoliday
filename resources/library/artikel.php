<?php
class Artikel{

  /**
  * Get all the information of a product where the product's id matches the artikelId parameter
  * @param int $artikelId: The int to which the id's are matched
  * @return array $informatie: an array with all the products information
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

  /**
  * Get the omschrijving that matches with the parameter id in the artikelGroep table
  * @param int $id: The int to which the id's are matched
  * @return string $omschrijving: The omschrijving, if it matches
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

  /**
  * Get some information of a product that will be displayed in the product's thumbnail
  * @param int $artikelId: The int to which the product's id is matched
  * @return array $thumbInfo: An array with all the information that is relevant to the thumbnail of the product
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

  public function newThumbInfo($artikelId){
    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, newPrice, img FROM Orchidee WHERE id=?');
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

  /**
  * Get the best selling products
  * @return int: If there are sold products, it will return an array with the id of the product
  *          and the number of times that that product is sold.
  */
  public function zoekBestVerkocht(){

    /**
    * Get the id from orders that are finished
    * @return array $ids: Returns an array with the id's of finished orders
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

    /**
    * Get all the ids from the products that are included in the orders
    * @param array $ids: An array with the id's from the finished orders
    * @return array $orchideeen: An array with all the product id's that are related to the finished order
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
      }

      if(!empty($orchideeen)){
        return $orchideeen;
      }
    }

    /**
    * Count the amount of values in the array
    * @param array orchideeen: An array with ids of a product
    * @return int array_count_values(): A number of the counted amount of values in the array
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

  /**
  * Get the products that will be displayed in the slider on Home.
  * The function will return the best selling items
  * @return array $orchideeen: An array with product id's
  */
  public function getSliderArtikelen(){

    /**
    * Get the id from orders that are finished
    * @return array $ids: Returns an array with the id's of finished orders
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

    /**
    * Get all the ids from the products that are included in the orders
    * @param: An array with the id's from the finished orders
    * @return: An array with all the product id's that are related to the finished order
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
          return array_unique($orchideeen);
        }
      }
    }

    /**
    * Filter the 'deleted' product. If a product is deleted, a.k.a set as unavailable, the product's verwijder column will be set to 1.
    * A user can NOT have a deleted product in their Favorieten.
    * @param array $artikelen: An array with all the product id's from getFavs();
    * @return array $ids: An array with all the product id's that are NOT deleted
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

  /**
  * Get the id's of products (that are not deleted)
  * @return array $ids: An array with the id's of products
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

  /**
  * Mark a product as deleted by setting the verwijderd column to 1
  * @param int $artikel: The products id
  * @return bool: True, if no errors occured
  */
  public function verwijderArtikel($artikel){

    $stmt = DB::conn()->prepare('UPDATE Orchidee SET verwijderd=1 WHERE id=?');
    $stmt->bind_param('i', $artikel);
    $stmt->execute();
    $stmt->close();

    return true;
  }

  public function get_artikel_van_de_dag(){
    $stmt = DB::conn()->prepare('SELECT artikel_id, datum FROM artikel_van_de_dag WHERE id=1');
    $stmt->execute();
    $stmt->bind_result($id, $datum);
    while($stmt->fetch()){
      $test[] = $id;
      $test[] = $datum;
    }
    $stmt->close();

    if(!empty($test)){
      return $test;
    }
  }

  public function set_artikel_van_de_dag($artikel){
    $date = date('d-m-Y');
    $id = 1;

    function avdd_empty(){
      $stmt = DB::conn()->prepare('SELECT artikel_id FROM artikel_van_de_dag');
      $stmt->execute();
      $stmt->bind_result($id);
      $stmt->fetch();
      $stmt->close();

      if(empty($id)){
        return true;
      }
    }

    function getOriginalPrice($artikel){
      $stmt = DB::conn()->prepare('SELECT prijs FROM Orchidee WHERE id=?');
      $stmt->bind_param('i', $artikel);
      $stmt->execute();
      $stmt->bind_result($originalPrice);
      $stmt->fetch();
      $stmt->close();

      return $originalPrice;
    }
    $originalPrice = getOriginalPrice($artikel);
    $newPrice = $originalPrice / 2;

    if(avdd_empty()){
      $stmt = DB::conn()->prepare('INSERT INTO artikel_van_de_dag(id, artikel_id, datum) VALUES (?, ?, ?)');
      $stmt->bind_param('iis', $id, $artikel, $date);
      $stmt->execute();
      $stmt->close();

      $stmt = DB::conn()->prepare('UPDATE Orchidee SET newPrice=? WHERE id=?');
      $stmt->bind_param('di', $newPrice, $artikel);
      $stmt->execute();
      $stmt->close();

      header("Refresh:0; url=/artikel_van_de_dag");
    }else{
      $stmt = DB::conn()->prepare('UPDATE artikel_van_de_dag SET artikel_id=?, datum=? WHERE id=?');
      $stmt->bind_param('isi', $artikel, $date, $id);
      $stmt->execute();
      $stmt->close();

      $stmt = DB::conn()->prepare('UPDATE Orchidee SET newPrice=? WHERE id=?');
      $stmt->bind_param('di', $newPrice, $artikel);
      $stmt->execute();
      $stmt->close();

      header("Refresh:0; url=/artikel_van_de_dag");
    }

  }

  public function isNewPriceActive($artikel){
    if(date('H') >= 11 && date('H') < 13){
      $stmt = DB::conn()->prepare('SELECT artikel_id FROM artikel_van_de_dag WHERE id=1');
      $stmt->execute();
      $stmt->bind_result($artikel_id);
      $stmt->fetch();
      $stmt->close();

      if($artikel == $artikel_id){
        return true;
      }
    }
  }

  public function isActionDate(){
    $stmt = DB::conn()->prepare('SELECT datum FROM artikel_van_de_dag');
    $stmt->execute();
    $stmt->bind_result($datum);
    $stmt->fetch();
    $stmt->close();

    if($datum == date('d-m-Y')){
      return true;
    }
  }
}
