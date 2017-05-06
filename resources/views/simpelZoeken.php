<?php
$zoeken = new simpelZoeken;
$artikel = new Artikel;

if(!empty($_POST['zoekterm'])){
  $zoekterm = $_POST['zoekterm'];

  $resultaat = $zoeken->zoekOpTrefwoord($zoekterm);
  if(empty($resultaat)){
    echo '<div class="warning"><b>GEEN RESULTATEN GEVONDEN</b></div>';
  }else{
    ?>
    <h1>RESULTATEN:</h1>
    <table class="table winkelmand_table">
      <tbody>
    <?php
    foreach($resultaat as $r){
      $info = $artikel->thumbInfo($r);
      ?>
      <tr>
        <td><a href="/artikel/<?php echo $r?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
        <td><p><?php echo $info['titel'] ?></p></td>
        <td><h4>€<?php echo $info['prijs'] ?></h4></td>
      </tr>
    <?php
    }
    ?>
    </tbody>
  </table>
  <?php
  }
}
