<div class="panel panel-default">
  <div class="panel-body">
    <div class="btn-group admin">
      <a href="/beheer/overzicht" class="btn btn-primary admin_menu">OVERZICHT</a>
      <a href="/beheer/orchidee_toevoegen" class="btn btn-primary admin_menu">ORCHIDEE TOEVOEGEN</a>
      <a href="/beheer/orchidee_verwijderen" class="btn btn-primary admin_menu">ORCHIDEE VERWIJDEREN</a>
      <a href="/beheer/artikel_van_de_dag" class="btn btn-primary admin_menu">ARTIKEL VAN DE DAG</a>
      <a href="/beheer/contactformulier" class="btn btn-primary admin_menu actief">CONTACT FORMULIER</a>
    </div>
    <h1>CONTACTFORMULIER</h1>
    <?php
    $account = new Account;
    $contact = new Contact;

    if($account->isBeheerder()){
      $contactId = $contact->getContactId();
      rsort($contactId);
      
      foreach($contactId as $id){
        $contactInfo = $contact->getContactInfo($id);

        echo '<b>Email:</b> ' . $contactInfo['email'] . '<br>';
        echo '<b>Naam:</b> ' . $contactInfo['naam'] . '<br>';
        echo '<b>Onderwerp:</b> ' . $contactInfo['onderwerp'] . '<br>';
        echo '<b>Bericht:</b><br><i> ' .  $contactInfo['bericht'] . '</i><br>';
        echo '<br><hr></hr>';
      }

    }else{
      header("Refresh:0; url=/");
    }
    ?>
