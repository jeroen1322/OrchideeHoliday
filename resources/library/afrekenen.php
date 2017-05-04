<?php
class Afrekenen{
  public function slaBezorgwijzeOpInSession($bezorgwijze){
    $_SESSION['bezorgwijze'] = array();
    $_SESSION['bezorgwijze'] = $bezorgwijze;
  }

  public function controlleerBezorgwijzeSession(){
    if(empty($_SESSION['bezorgwijze'])){
      return false;
    }else{
      return true;
    }
  }

  public function storeOpmerkingInSession($opmerking){
    $_SESSION['opmerking'] = array();
    $_SESSION['opmerking'] = $opmerking;
  }

  public function getSessionOpmerking(){
    if(!empty($_SESSION['opmerking'])){
      $opmerking = $_SESSION['opmerking'];
      return $opmerking;
    }else{
      return false;
    }
  }

}
