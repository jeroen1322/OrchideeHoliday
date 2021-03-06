<?php
$winkelmand = new Winkelmand;
$account = new Account;
$artikel = new Artikel;

if(!empty($_GET)){
  if($_GET['ingelogd'] == 'true'){
    $gebruikerId = $account->getLoginId();
    if($_GET['afgerond'] == 'true'){
      $winkelmand->rondBestellingAf($gebruikerId);
    }elseif($_GET['afgerond'] == 'false'){
      $winkelmand->annuleerOrder($gebruikerId);
    }
  }else{
    if($_GET['afgerond'] == 'true'){
      $winkelmand->rondSessionBestellingAf($_POST, $_SESSION['winkelmand'], $_SESSION['bezorgwijze']);
    }elseif($_GET['afgerond'] == 'false'){
      $winkelmand->annuleerSessionOrder();
    }
  }
}
?>
<div class="flags">
  <a href="/"><img src="/foto/dutch_flag.jpg" class="flag"></a>
  <a href="/en"><img src="/foto/english_flag.png" class="flag"></a>
</div>
<div class="top-home">
  <h2>BEST SELLING</h2>
  <div class="slider">
    <?php
    $artikelen = $artikel->getSliderArtikelen();
    if(!empty($artikelen)){
      foreach($artikelen as $a => $id){
        $info = $artikel->thumbInfo($id);
        ?>
        <div class="col-md-3 sliderThumb">
          <div class="thumb">
            <a href=<?php echo '/artikel/'.$info['id'] ?>>
              <img src=<?php echo '/foto/'.$info['img'] ?> class="sliderImg"/></a>
            </a>
          </div>
        </div>
        <?php
      }
    }
    ?>
  </div>
  <div class="slide-boxes">
    <a href="/aanbod">
      <div class="slide-box orchidee-slide-box-1">
        <h2>BROWSE</h2>
      </div>
    </a>
    <div class="slide-box">
      <a href="/best_verkocht">
        <div class="slide-box orchidee-slide-box-2">
          <h2>BEST SELLING</h2>
        </div>
      </a>
    </div>
    <div class="slide-box">
      <a href="/contact">
        <div class="slide-box orchidee-slide-box-3">
          <h2>CONTACT</h2>
        </div>
      </a>
    </div>
  </div>
</div>
<hr>
<br>
<div class="artikelen">
<hr></hr><br>
<h2>RECENTLY ADDED</h2>
<?php

$stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE verwijderd=0 LIMIT 4");
$stmt->execute();
$stmt->bind_result($id);
while($stmt->fetch()){
  $ids[] = $id;
}
$stmt->close();



if(!empty($ids)){
  foreach($ids as $id){
    $info = $artikel->thumbInfo($id);
    ?>
    <div class="filmThumbnail filmAanbodFilm col-md-3 filmAanbodItem recentToegevoegd">
      <div class="thumb">
          <a href=<?php echo '/artikel/'.$info['id'] ?>>
            <img src=<?php echo '/foto/'.$info['img'] ?> class="thumb_img filmaanbod_img"/></a>
            <h2 class="textfilmaanbod"><?php echo $info['titel'] ?> </h2>
            <a href="/artikel/<?php echo $info['id']?>"><button class="btn btn-succes form-knop bekijk-knop">SEE MORE</button></a>
          </a>
        </div>
      </div>
    <?php
  }
}else{
  echo '<div class="warning"><b>There are no items added yet</b></div>';
}
?>
</div>

<?php
DB::conn()->close();
