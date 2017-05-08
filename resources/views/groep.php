<?php
$groep = $this->groep;
$artikel = new Artikel;
$artikelGroep = new ArtikelGroep;

$naam = $artikel->parseSoort($groep);
?>
<h1><?php echo $naam ?></h1>
<?php
$artikelen = $artikelGroep->getGroepArtikelen($groep);

if(empty($artikelen)){
  echo '<div class="warning"><b>GEEN ARTIKELEN IN DEZE GROEP GEVONDEN</b></di>';
}else{
  ?>
  <table class="table winkelmand_table">
    <tbody>
    <?php
    foreach ($artikelen as $artikelId){
      $info = $artikel->thumbInfo($artikelId);
      ?>
      <tr>
        <td><a href="/artikel/<?php echo $artikelId?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
        <td><p><?php echo $info['titel'] ?></p></td>
        <td><h4>€<?php echo $info['prijs'] ?></h4></td>
      </tr>
      <?php
    }

}
