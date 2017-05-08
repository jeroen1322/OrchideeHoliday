<?php
class Betalen{

  public function displayBetaalWijze(){
    function getAlleBetaalwijze(){
      $stmt = DB::conn()->prepare('SELECT id, omschrijving FROM betaalWijze');
      $stmt->execute();
      $stmt->bind_result($id, $omschrijving);
      while($stmt->fetch()){
        $betaalWijze[$id] = $omschrijving;
      }
      $stmt->close();

      return $betaalWijze;
    }
    $betaalwijze = getAlleBetaalwijze();

    echo '<select name="betaalWijze" class="form-control">';
    foreach($betaalwijze as $id => $omschrijving){
      echo '<option value='.$id.'>'.$omschrijving.'</option>';
    }
    echo '</select>';
  }

}
