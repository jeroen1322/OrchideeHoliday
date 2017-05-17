<?php
$artikel = new Artikel;
$gebruiker = new Account;
$winkelmand = new Winkelmand;
$resultaten = $artikel->zoekBestVerkocht();


if(!empty($_GET)){
  if($_GET['action'] == 'bestel'){
    if($gebruiker->isIngelogd()){
      $winkelmand->plaatsInDatabaseWinkelmand($_GET['id'], $_SESSION['login'][0], 'best_verkocht');
      // header("Refresh:0; url=/best_verkocht");
    }else{
      $winkelmand->plaatsInSessionWinkelmand($_GET['id'], 'best_verkocht');
      header("Refresh:0; url=/best_verkocht");
    }
  }
}

if(!empty($resultaten)){
  ?>
  <h1>RESULTATEN:</h1>
  <table class="table winkelmand_table">
    <thead>
      <td></td>
      <td><b>Omschrijving</b></td>
      <td><b>Prijs</b></td>
      <td><b>Aantal keer verkocht</b></td>
      <td></td>
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
      <td>
        <form method="post" action="?action=bestel&id=<?php echo $info['id']?>">
          <button class="btn btn-succes form-knop bestel-knop">IN WINKELMAND</button>
        </form>
      </td>
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
