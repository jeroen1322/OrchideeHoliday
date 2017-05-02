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
    print_r($_SESSION['opmerking']);
  }
}
