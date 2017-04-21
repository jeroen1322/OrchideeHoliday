<?php
$orchidee = new Artikel;
$info = $orchidee->informatie($this->orchideeId);

if(!empty($info)){
  $foto = '/foto/'.$info['img'];
  ?>
  <img src="<?php echo $foto ?>" class="img-responsive cover"/>
  <h1><b><?php echo $info['titel'] ?></b></h1>
  <?php
}else{
  echo '<div class="warning"><b>Artikel niet gevonden</b></div>';
}
