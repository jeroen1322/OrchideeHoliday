<?php
class Afrekenen{

  /*
  Store the selected Bezorgwijze in the 'bezorgwijze' session

  @param: The user's selected Bezorgwijze
  */
  public function slaBezorgwijzeOpInSession($bezorgwijze){
    $_SESSION['bezorgwijze'] = array();
    $_SESSION['bezorgwijze'] = $bezorgwijze;
  }

  /*
  Check if the 'bezorgwijze' session is empty or not.

  @return: True, if it is not empty
  */
  public function controlleerBezorgwijzeSession(){
    if(!empty($_SESSION['bezorgwijze'])){
      return true;
    }
  }

  /*
  Store the user's Opmerking in the 'opmerking' session

  @param: The user's Opmerking
  */
  public function storeOpmerkingInSession($opmerking){
    $_SESSION['opmerking'] = array();
    $_SESSION['opmerking'] = $opmerking;
  }

  /*
  Get the user's Opmerking

  @return: The user's Operking
  */
  public function getSessionOpmerking(){
    if(!empty($_SESSION['opmerking'])){
      $opmerking = $_SESSION['opmerking'];
      return $opmerking;
    }
  }

}
