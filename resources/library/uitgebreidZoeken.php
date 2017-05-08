<?php
class uitgebreidZoeken{

  public function zoekOpTrefwoord($zoekterm){
    echo 'trefwoord';
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

  public function zoekMetPrijs($zoekterm, $min, $max){

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


}
