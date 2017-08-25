<?php
$account = new Account;
$winkelmand = new Winkelmand;
$artikel = new Artikel;
$afrekenen = new Afrekenen;

if(!empty($_GET)){
  if($account->isIngelogd()){
    if($_GET['action'] == 'delete'){
      $gebruikerId = $account->getLoginId();
      if($winkelmand->deleteFromDatabaseWinkelmand($_GET['code'], $gebruikerId)){
        header("Refresh:0; url=/winkelmand");
      }
    }
  }else{
    if($_GET['action'] == 'delete'){
      $winkelmand->deleteFromSessionWinkelmand($_GET['code']);
    }
  }
}
if($account->isIngelogd()){
  ?>
  <h1>WINKELMAND</h1>
  <?php
  $artikelen = $winkelmand->getArtikelen($_SESSION['login'][0]);
  $openOrders = $account->telOpenOrders($_SESSION['login'][0]);
  if(!empty($artikelen)){
    ?>
    <table class="table winkelmand_table">
      <tbody>
    <?php
    $totaal = array();
    foreach ($artikelen as $artikelId){
      if($artikel->isNewPriceActive($artikelId)){
        if($openOrders < 3){
          $nietGenoeg = true;
        }else{
          $nietGenoeg = false;
        }

        if($nietGenoeg){
          $info = $artikel->thumbInfo($artikelId);
        }else{
          $info = $artikel->newThumbInfo($artikelId);
        }
      }else{
        $info = $artikel->thumbInfo($artikelId);
      }
      $totaal[] = $info['prijs'];
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
    ?>
      </tbody>
    </table>
    <?php
      echo '<h4><b>TOTAAL PRIJS: €'.array_sum($totaal).'</b></h4>';
      if($nietGenoeg){
        echo '<div class="warning">U moet in totaal minimaal drie artikelen kopen om korting te krijgen op het Artikel Van De Dag.</div>';
      }
    ?>
    <a href="/afrekenen"><button class="btn btn-succes form-knop">AFREKENEN</button></a>
    <?php
  }else{
    echo '<div class="warning"><b>Geen artikelen in winkelmand.</b></div>';
  }
}else{
  if(!empty($_SESSION['winkelmand'])){
    ?>
    <table class="table winkelmand_table">
      <tbody>
    <?php
    $totaal = array();
    foreach($_SESSION['winkelmand'] as $artikelId){
      $info = $artikel->thumbInfo($artikelId);
      $totaal[] = $info['prijs'];
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
    ?>
      </tbody>
    </table>
    <?php
      echo '<h4><b>TOTAAL PRIJS: €'.array_sum($totaal).'</b></h4>';
    ?>
    <a href="/afrekenen"><button class="btn btn-succes form-knop">AFREKENEN</button></a>
    <?php
  }else{
    echo '<div class="warning"><b>U HEEFT NOG NIETS IN UW WINKELMAND</b></div>';
  }
}
