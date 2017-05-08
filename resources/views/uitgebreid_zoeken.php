<h1>UITGEBREID ZOEKEN</h1>
<form method="post">
  <input type="text" class="form-control" name="zoekveld" placeholder="ZOEK TERM" autocomplete="off" required>
  <input type="number" name="min" placeholder="MINIMAAL" autocomplete="off">
  <input type="number" name="max" placeholder="MAXIMAAL" autocomplete="off">
  <input type="submit" class="btn btn-primary form-knop fullWidth" value="ZOEK">
</form>
<?php
$zoeken = new uitgebreidZoeken;
$artikel = new Artikel;

if(!empty($_POST)){

  if(empty($_POST['min']) && empty($_POST['max'])){
    $resultaat = $zoeken->zoekOpTrefwoord($_POST['zoekveld']);
  }else{
    $resultaat = $zoeken->zoekMetPrijs($_POST['zoekveld'], $_POST['min'], $_POST['max']);
  }

  if(!empty($resultaat)){
    ?>
    <hr>
    <h2>RESULTATEN</h2>
    <table class="table winkelmand_table">
      <tbody>
    <?php
    foreach($resultaat as $key => $value){
      $info = $artikel->thumbInfo($value);
      ?>
      <tr>
        <td><a href="/artikel/<?php echo $value?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
        <td><p><?php echo $info['titel'] ?></p></td>
        <td><h4>€<?php echo $info['prijs'] ?></h4></td>
      </tr>
    <?php
    }
    ?>
      </tbody>
    </table>
    <?php
  }else{
    echo '<div class="warning"><b>GEEN RESULTATEN GEVONDEN</b></div>';
  }
}
