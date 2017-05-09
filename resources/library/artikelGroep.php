<?php

class ArtikelGroep{

  /*
  Get all the records in the ArtikelGroep table in the Database
  And display the data in a <select> element with the data in in <option> tag
  */
  public function getArtikelGroepen(){
    /*
    Get an array of the ArtikelGroep data.

    @return: an array containing all the column data for each row. The array key is the row id, the value is the row omschrijving.
    */
    function getAlleGroepen(){
      $stmt = DB::conn()->prepare('SELECT id, omschrijving FROM artikelGroep');
      $stmt->execute();
      $stmt->bind_result($id, $omschrijving);
      while($stmt->fetch()){
        $groepen[$id] = $omschrijving;
      }
      $stmt->close();

      return $groepen;
    }
    $groep = getAlleGroepen();

    /*
    Display the array as an select with the array data in tne <option>
    */
    echo '<select name="artikelGroep" class="form-control">';
    foreach($groep as $id => $omschrijving){
      echo '<option value='.$id.'>'.$omschrijving.'</option>';
    }
    echo '</select>';
  }


  /*
  Get all the data in the artikelGroep table in the Database

  @return: An array containing the data. The array key is the artikelGroep id, the value is the artikelGroep omschrijving
  */
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

  /*
  Display the rows in artikelGroep in the Database as radio buttons.
  */
  public function groepenRadioButtons(){

    /*
    Get all the data from the artikelGroep table and store it in an array.
    The array key is the id from the row, and the value is the omschrijving
    */
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

  /*
  Get all the ids from Orchidee where the soort column matches with the groep parameter

  @parameter $groep: An Int which comes from the artikelGroep table id column.
  @return: An array with data from the id column in Orchidee where soort matches $groep
  */
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
