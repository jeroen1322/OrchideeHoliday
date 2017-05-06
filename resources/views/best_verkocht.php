<?php
$artikel = new Artikel;

$resultaten = $artikel->zoekBestVerkocht();

if(!empty($resultaten)){
  ?>
  <h1>RESULTATEN:</h1>
  <table class="table winkelmand_table">
    <thead>
      <td></td>
      <td><b>Omschrijving</b></td>
      <td><b>Prijs</b></td>
      <td><b>Aantal keer verkocht</b></td>
    </thead>
    <tbody>
  <?php
  arsort($resultaten);
  foreach($resultaten as $orchidee => $aantal){
    $info = $artikel->thumbInfo($orchidee);
    ?>
    <tr>
      <td><a href="/artikel/<?php echo $orchidee?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
      <td><p><?php echo $info['titel'] ?></p></td>
      <td><p>€<?php echo $info['prijs'] ?></p></td>
      <td><p><b><?php echo $aantal ?></b></b></td>
    </tr>
  <?php
  }
  ?>
  </tbody>
</table>
<?php
}else{
  echo '<div class="warning"><b>GEEN VERKOCHTE ARTIKELEN GEVONDEN</b></div>';
}
