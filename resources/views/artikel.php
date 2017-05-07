<?php
$orchidee = new Artikel;
$gebruiker = new Account;
$winkelmand = new Winkelmand;
$info = $orchidee->informatie($this->orchideeId);

if(!empty($_GET)){
  if($_GET['action'] == 'bestel'){
    if($gebruiker->isIngelogd()){
      $winkelmand->plaatsInDatabaseWinkelmand($_GET['id'], $_SESSION['login'][0]);
      header("Refresh:0; url=/artikel/".$_GET['id']);
    }else{
      $winkelmand->plaatsInSessionWinkelmand($_GET['id']);
      header("Refresh:0; url=/artikel/".$_GET['id']);
    }
  }elseif($_GET['action'] == 'favoriet'){
    $gebruiker->voegToeAanFavotiet($_GET['id'], $gebruiker->getLoginId());
  }
}

if(!empty($info)){
  $foto = '/foto/'.$info['img'];
  ?>
  <img src="<?php echo $foto ?>" class="img-responsive cover"/>
  <h1><b><?php echo $info['titel'] ?></b></h1>
  <p><i><?php echo $info['langeOmschrijving'] ?></i></p>

  <?php
  if($gebruiker->isIngelogd()){
    ?>
    <form method="post" action="?action=favoriet&id=<?php echo $info['id']?>">
      <button class="btn btn-succes form-knop"><i class="fa fa-heart" aria-hidden="true"></i></button><br><br>
    </form>
    <?php
  }
  ?>
  <p><b>BESCHIKBAAR</b></p>
  <h2><b>€<?php echo $info['prijs'] ?></b></h2>
    <form method="post" action="?action=bestel&id=<?php echo $info['id']?>">
      <button class="btn btn-succes form-knop bestel-knop">IN WINKELMAND</button>
    </form>
    <?php
}else{
  echo '<div class="warning"><b>Artikel niet gevonden</b></div>';
}
