<?php
class uitgebreidZoeken{

  /**
  *The function matches the search term with the information in the titel, korteOmschrijving, langeOmschrijving and id columns in the Orchidee table.
  *The id of the products where at least one of the columns matches the search term are put in an array.

  *@param: The search term which will be matched
  *@return: An array with the ids of the products where at least one of the columns match with the search term.
  */
  public function zoekOpTrefwoord($zoekterm){
    function zoekDB($zoekterm){

      $stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE titel LIKE ?
        OR korteOmschrijving LIKE ? OR langeOmschrijving LIKE ? OR id LIKE ?");
      $stmt->bind_param('ssss', $zoekterm, $zoekterm, $zoekterm, $zoekterm);
      $stmt->execute();
      $stmt->bind_result($resultaat);
      while($stmt->fetch()){
        $resultaten[] = $resultaat;
      }
      $stmt->close();

      if(!empty($resultaten)){
        return $resultaten;
      }
    }

    return zoekDB($zoekterm);
  }

  /**
  *Function that searches for matches with the search term and is in the margin of the specified minimum and maximum price.
  *The id's of the products that match the specifications are put in an array.
  *
  *@param zoekterm: The search term to which the columns will be matched.
  *@param min: The minimum price
  *@param max: The maximum price
  *
  *@return: An array with the ids of the product where all the specifitions match
  */
  public function zoekMetPrijs($zoekterm, $min, $max){

    /**
    *The function matches the search term with the information in the titel, korteOmschrijving, langeOmschrijving and id columns in the Orchidee table.
    *@param: The search term to which the information in the columns are compared.
    *@return: An array with the id's of the products where at least one of the columns match
    */
    function zoekDB($zoekterm){
      $stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE titel LIKE ?
        OR korteOmschrijving LIKE ? OR langeOmschrijving LIKE ? OR id LIKE ?");
      $stmt->bind_param('ssss', $zoekterm, $zoekterm, $zoekterm, $zoekterm);
      $stmt->execute();
      $stmt->bind_result($resultaat);
      while($stmt->fetch()){
        $resultaten[] = $resultaat;
      }
      $stmt->close();

      if(!empty($resultaten)){
        return $resultaten;
      }
    }

    /**
    *The function go's through the array of results and checks if the price of the product is equal to
    *or more than the specified minimum, and equal to or less then the specified maximum price.
    *
    *@param resultaat: The array of the results from zoekDB().
    *@param min: The minimum specified price
    *@param max: The maximum specified price
    *@return: An array with the id's of products that match the specifications
    */
    function filterOpPrijs($resultaat, $min, $max){
      foreach($resultaat as $key => $value){
        $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE id = ? AND prijs >= ? AND prijs <= ?');
        $stmt->bind_param('iii', $value, $min, $max);
        $stmt->execute();
        $stmt->bind_result($id);
        while($stmt->fetch()){
          $ids[] = $id;
        }
        $stmt->close();
      }

      return $ids;
    }

    $resultaat = zoekDB($zoekterm);
    $returning = filterOpPrijs($resultaat, $min, $max);

    if(!empty($returning)){
      return $returning;
    }

  }

  /**
  *The function matches the search term within the specified artikelGroep with the information in the titel, korteOmschrijving, langeOmschrijving and id columns in the Orchidee table.
  *The id of the products where at least one of the columns matches the search term are put in an array.

  *@param zoekterm: The search term which will be matched.
  *@param groep: The artikelGroep to which the product is linked to.
  *@return: An array with the ids of the products where at least one of the columns match with the search term.
  */
  public function ZoekBinnenArtikelGroep($zoekterm, $groep){

    /**
    *The function matches the search term with the information in the titel, korteOmschrijving, langeOmschrijving and id columns in the Orchidee table.
    *@param: The search term to which the information in the columns are compared.
    *@return: An array with the id's of the products where at least one of the columns match
    */
    function zoekDB($zoekterm){
      $stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE titel LIKE ?
        OR korteOmschrijving LIKE ? OR langeOmschrijving LIKE ? OR id LIKE ?");
      $stmt->bind_param('ssss', $zoekterm, $zoekterm, $zoekterm, $zoekterm);
      $stmt->execute();
      $stmt->bind_result($resultaat);
      while($stmt->fetch()){
        $resultaten[] = $resultaat;
      }
      $stmt->close();

      if(!empty($resultaten)){
        return $resultaten;
      }
    }

    /**
    *The function receives an array of product ids that match in some way with the search term.
    *This function then checks if any of the products are linked to the specified artikelGroep
    *If it is, the id of the product will be put in an array.
    *
    *@param resultaat: An array with product id's received from zoekDB().
    *@param groep: The specified artikelGroep to which the search results should be linked.
    *@return: An array of id's of the products that match the specifications.
    */
    function filterOpGroep($resultaat, $groep){
      foreach($resultaat as $r){
        $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE soort=? AND id=?');
        $stmt->bind_param('ii', $groep, $r);
        $stmt->execute();
        $stmt->bind_result($id);
        while($stmt->fetch()){
          $ids[] = $id;
        }
        $stmt->close();
      }

      return $ids;
    }

    $resultaat = zoekDB($zoekterm);
    $returning = filterOpGroep($resultaat, $groep);

    if(!empty($returning)){
      return $returning;
    }
  }

  /**
  *The function matches the search term within the specified artikelGroep and the specified price range
  *with the information in the titel, korteOmschrijving, langeOmschrijving and id columns in the Orchidee table.
  *The id of the products where at least one of the columns matches the search term are put in an array.

  *@param zoekterm: The search term which will be matched.
  *@param min: The minimum specified price.
  *@param max: The maximum specified price.
  *@param groep: The artikelGroep to which the product is linked to.
  *@return: An array with the ids of the products where at least one of the columns match with the search term.
  */
  public function ZoekBinnenArtikelGroepMetPrijs($zoekterm, $min, $max, $groep){

    /**
    *The function matches the search term with the information in the titel, korteOmschrijving, langeOmschrijving and id columns in the Orchidee table.
    *@param: The search term to which the information in the columns are compared.
    *@return: An array with the id's of the products where at least one of the columns match
    */
    function zoekDB($zoekterm){
      $stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE titel LIKE ?
        OR korteOmschrijving LIKE ? OR langeOmschrijving LIKE ? OR id LIKE ?");
      $stmt->bind_param('ssss', $zoekterm, $zoekterm, $zoekterm, $zoekterm);
      $stmt->execute();
      $stmt->bind_result($resultaat);
      while($stmt->fetch()){
        $resultaten[] = $resultaat;
      }
      $stmt->close();

      if(!empty($resultaten)){
        return $resultaten;
      }
    }

    /**
    *The function go's through the array of results and checks if the price of the product is equal to
    *or more than the specified minimum, and equal to or less then the specified maximum price.
    *
    *@param resultaat: The array of the results from zoekDB().
    *@param min: The minimum specified price
    *@param max: The maximum specified price
    *@return: An array with the id's of products that match the specifications
    */
    function filterOpPrijs($resultaat, $min, $max){
      foreach($resultaat as $key => $value){
        $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE id = ? AND prijs >= ? AND prijs <= ?');
        $stmt->bind_param('iii', $value, $min, $max);
        $stmt->execute();
        $stmt->bind_result($id);
        while($stmt->fetch()){
          $ids[] = $id;
        }
        $stmt->close();
      }

      return $ids;
    }

    /**
    *The function receives an array of product ids that match in some way with the search term.
    *This function then checks if any of the products are linked to the specified artikelGroep
    *If it is, the id of the product will be put in an array.
    *
    *@param resultaat: An array with product id's received from filterOpPrijs().
    *@param groep: The specified artikelGroep to which the search results should be linked.
    *@return: An array of id's of the products that match the specifications.
    */
    function filterOpGroep($resultaat, $groep){
      foreach($resultaat as $r){
        $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE soort=? AND id=?');
        $stmt->bind_param('ii', $groep, $r);
        $stmt->execute();
        $stmt->bind_result($id);
        while($stmt->fetch()){
          $ids[] = $id;
        }
        $stmt->close();
      }

      return $ids;
    }

    $resultaat = zoekDB($zoekterm);
    $result = filterOpPrijs($resultaat, $min, $max);
    $res = filterOpGroep($result, $groep);

    if(!empty($res)){
      return $res;
    }
  }


}
