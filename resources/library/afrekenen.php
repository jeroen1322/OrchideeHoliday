<?php
class Afrekenen{
  public function slaBezorgwijzeOpInSession($bezorgwijze){
    $_SESSION['bezorgwijze'] = array();
    $_SESSION['bezorgwijze'] = $bezorgwijze;
  }
}
