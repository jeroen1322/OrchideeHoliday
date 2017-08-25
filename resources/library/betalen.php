<?php
class Betalen{
  /**
  * Get all the rows from BetaalWijze and display them in a <select> tag with the data in the <option> tags
  */
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
