<?php
class simpelZoeken{

  /**
  *The function matches the search term with the information in the titel, korteOmschrijving and langeOmschrijving columns in the Orchidee table.
  *The id of the products where at least one of the columns matches the search term are put in an array.

  *@param: The search term which will be matched
  *@return: An array with the ids of the products where at least one of the columns match with the search term.
  */
  public function zoekOpTrefwoord($zoekterm){
    function zoekDB($zoekterm){
      $stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE titel LIKE ? OR korteOmschrijving LIKE ? OR langeOmschrijving LIKE ?");
      $stmt->bind_param('sss', $zoekterm, $zoekterm, $zoekterm);
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

}
