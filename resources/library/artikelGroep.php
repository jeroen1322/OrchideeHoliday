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

  public function getGroepen(){

    $stmt = DB::conn()->prepare('SELECT id, omschrijving FROM artikelGroep');
    $stmt->execute();
    $stmt->bind_result($id, $omschrijving);
    while($stmt->fetch()){
      $groepen[$id] = $omschrijving;
    }
    $stmt->close();

    return $groepen;

  }

  public function groepenRadioButtons(){
    function getGroups(){
      $stmt = DB::conn()->prepare('SELECT id, omschrijving FROM artikelGroep');
      $stmt->execute();
      $stmt->bind_result($id, $omschrijving);
      while($stmt->fetch()){
        $groepen[$id] = $omschrijving;
      }
      $stmt->close();

      return $groepen;
    }

    $groups = getGroups();

    foreach($groups as $key => $value){
      echo '<input type="radio" name="groep" value="'.$key.'" class="groepRadioButton">'.$value.'<br>';
    }
  }

  public function getGroepArtikelen($groep){
    $stmt = DB::conn()->prepare('SELECT id FROM Orchidee WHERE soort=? AND verwijderd=0');
    $stmt->bind_param('i', $groep);
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

}
