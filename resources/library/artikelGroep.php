<?php

class ArtikelGroep{

  public function getArtikelGroepen(){
    function getAlleGroepen(){
      $stmt = DB::conn()->prepare('SELECT id, omschrijving FROM artikelGroep');
      $stmt->execute();
      $stmt->bind_result($id, $omschrijving);
      while($stmt->fetch()){
        $betaalWijze[$id] = $omschrijving;
      }
      $stmt->close();

      return $betaalWijze;
    }
    $betaalwijze = getAlleGroepen();

    echo '<select name="artikelGroep" class="form-control">';
    foreach($betaalwijze as $id => $omschrijving){
      echo '<option value='.$id.'>'.$omschrijving.'</option>';
    }
    echo '</select>';
  }

}
