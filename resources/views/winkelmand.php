<?php
$account = new Account;
$winkelmand = new Winkelmand;
$artikel = new Artikel;
if($account->isIngelogd()){
  ?>
  <h1>WINKELMAND</h1>
  <?php
  $artikelen = $winkelmand->getArtikelen($_SESSION['login'][0]);
  if(!empty($artikelen)){
    ?>
    <table class="table winkelmand_table">
      <thead>
        <tr>
          <th>Foto</th>
          <th class="table_titel">Titel</th>
          <th class="table_omschr">Prijs</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
    <?php
    foreach ($artikelen as $artikelId){
      $info = $artikel->thumbInfo($artikelId);
      ?>
      <tr>
        <td><a href="/artikel/<?php echo $artikelId?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
        <td><p><?php echo $info['titel'] ?></p></td>
        <td><h4>€<?php echo $info['prijs'] ?></h4></td>
        <td>
          <form method="post" action="?action=delete&code=<?php echo $artikelId ?>">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
            </button>
          </form>
        </td>
      </tr>
      <?php
    }
  }else{
    echo '<div class="warning"><b>Geen artikelen in winkelmand.</b></div>';
  }
}else{
  header("Refresh:0; url=/login");
}
