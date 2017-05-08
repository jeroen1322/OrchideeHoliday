<h1>UITGEBREID ZOEKEN</h1>
<form method="post">
  <input type="text" class="form-control" name="zoekveld" placeholder="ZOEKEN" autocomplete="off">
</form>
<?php
$zoeken = new uitgebreidZoeken;
$artikel = new Artikel;

if(!empty($_POST)){
  $resultaat = $zoeken->zoekOpTrefwoord($_POST['zoekveld']);

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
