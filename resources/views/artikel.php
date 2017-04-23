<?php
$orchidee = new Artikel;
$info = $orchidee->informatie($this->orchideeId);

if(!empty($info)){
  $foto = '/foto/'.$info['img'];
  ?>
  <img src="<?php echo $foto ?>" class="img-responsive cover"/>
  <h1><b><?php echo $info['titel'] ?></b></h1>
  <p><i><?php echo $info['langeOmschrijving'] ?></i></p>

  <button class="btn btn-succes form-knop"><i class="fa fa-heart" aria-hidden="true"></i></button><br><br>
  <p><b>BESCHIKBAAR</b></p>
  <h2><b>€<?php echo $info['prijs'] ?></b></h2>
  <button class="btn btn-succes form-knop bestel-knop">IN WINKELMAND</button>
  <?php
}else{
  echo '<div class="warning"><b>Artikel niet gevonden</b></div>';
}
