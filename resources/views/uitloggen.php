<h1>UITLOGGEN</h1>
<?php
  if(!empty($_SESSION['login'])){
    session_unset($_SESSION['login']);
  }
  if(!empty($_SESSION['winkelmand'])){
    session_unset($_SESSION['winkelmand']);
  }
  header("Refresh:0; url=/");
