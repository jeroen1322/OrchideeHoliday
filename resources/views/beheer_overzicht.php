<div class="panel panel-default">
  <div class="panel-body">
    <div class="btn-group admin">
      <a href="/beheer/overzicht" class="btn btn-primary actief admin_menu">OVERZICHT</a>
      <a href="/beheer/orchidee_toevoegen" class="btn btn-primary admin_menu">ORCHIDEE TOEVOEGEN</a>
      <a href="/beheer/orchidee_verwijderen" class="btn btn-primary admin_menu">ORCHIDEE VERWIJDEREN</a>
    </div>
    <h1>ALLE GEPLAATSTE ORDERS</h1>
    <?php
    $account = new Account;
    $artikelGroep = new ArtikelGroep;
    $beheer = new Beheer;
    $artikel = new Artikel;

    if($account->isBeheerder()){
      $orders = $beheer->getAlleOrders();

      if(!empty($orders)){
        foreach($orders as $order){
          ?>
          <div class="order">
            <p class="order_info" data-toggle="collapse" data-target="#<?php echo $order ?>">ORDER | ID: <?php echo $order ?><i class="fa fa-arrow-down neer" aria-hidden="true"></i></p>
            <div id="<?php echo $order ?>" class="collapse order_collapse">
              <table class="table winkelmand_table">
                <thead>
                   <th>FOTO</th>
                   <th>TITEL</th>
                   <th>PRIJS</th>
                   <th>PAGINA</th>
                 </thead>
                 <tbody>
              <?php
              $producten = $beheer->getOrderProducten($order);
              $meta = $beheer->getOrderMeta($order);
              $totaal = array();
              foreach($producten as $p){
                $pagina = $beheer->getUsedPage($order, $p);
                $info = $artikel->informatie($p);
                $totaal[] = $info['prijs'];
                $page = strtoupper(str_replace('_', ' ', $pagina));
                $naam = $beheer->getPersoonConnectedToOrder($order);
                ?>
                <tr>
                  <td><a href="/artikel/<?php echo $a?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
                  <td><?php echo $info['titel'] ?></td>
                  <td>€<?php echo $info['prijs'] ?></td>
                  <td><?php echo $page ?></td>
                </tr>
                <?php
              }
              ?>
              </tbody>
            </table>
            <h4><b>TOTAAL PRIJS: </b>€<?php echo array_sum($totaal) ?></h4>
            <h4><b>GEBRUIKER: </b>
              <?php
              if($naam == ' '){ //For some reason, if no name is linked to an order it return " "
                echo 'Anoniem';
              }else{
                echo $naam;
              }
              ?>
            </b></h4>
            <h4><b>DATUM: </b><?php echo $meta['orderdatum'] ?></h4>
            </div>
          </div>
          <?php
        }
      }else{
        echo '<div class="warning"><b>ER ZIJN NOG GEEN ORDERS GEPLAATST</b></div>';
      }
    }
    ?>
