<div class="panel panel-default">
  <div class="panel-body">
    <div class="btn-group admin">
      <a href="/beheer/overzicht" class="btn btn-primary admin_menu">OVERZICHT</a>
      <a href="/beheer/orchidee_toevoegen" class="btn btn-primary admin_menu">ORCHIDEE TOEVOEGEN</a>
      <a href="/beheer/orchidee_verwijderen" class="btn btn-primary actief admin_menu">ORCHIDEE VERWIJDEREN</a>
      <a href="/beheer/artikel_van_de_dag" class="btn btn-primary admin_menu">ARTIKEL VAN DE DAG</a>
      <a href="/beheer/contactformulier" class="btn btn-primary admin_menu">CONTACT FORMULIER</a>
    </div>
<?php
$artikel = new Artikel;
$account = new Account;

if(!empty($_GET)){
  if($_GET['action'] == 'delete'){
    if($artikel->verwijderArtikel($_GET['code'])){
      header("Refresh:0; url=/beheer/orchidee_verwijderen");
    }
  }
}

if($account->isBeheerder()){

  $artikelen = $artikel->getAllArtikelen();
  if(!empty($artikelen)){
    ?>
    <table class="table winkelmand_table">
      <tbody>
    <?php
    foreach($artikelen as $a){
      $info = $artikel->thumbInfo($a);
      ?>
      <tr>
        <td><a href="/artikel/<?php echo $a?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
        <td><p><?php echo $info['titel'] ?></p></td>
        <td><h4>€<?php echo $info['prijs'] ?></h4></td>
        <td>
          <form method="post" action="?action=delete&code=<?php echo $a ?>">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
            </button>
          </form>
        </td>
      </tr>
      <?php
    }
  }else{
    echo '<div class="warning"><b>GEEN ARTIKELEN</b></div>';
  }
}else{
  header("Refresh:0; url=/");
}
