<div class="panel panel-default">
  <div class="panel-body">
    <div class="btn-group admin">
      <a href="/beheer/overzicht" class="btn btn-primary actief admin_menu">OVERZICHT</a>
      <a href="/beheer/orchidee_toevoegen" class="btn btn-primary admin_menu">ORCHIDEE TOEVOEGEN</a>
      <a href="/beheer/orchidee_verwijderen" class="btn btn-primary admin_menu">ORCHIDEE VERWIJDEREN</a>
    </div>
    <?php
    $account = new Account;
    $artikelGroep = new ArtikelGroep;

    if($account->isBeheerder()){
      //TODO: Add overview of all the orders 
    }
    ?>
