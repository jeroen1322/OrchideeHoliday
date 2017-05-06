<?php
class simpelZoeken{
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
