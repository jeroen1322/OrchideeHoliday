<div class="panel panel-default">
  <div class="panel-body">
    <div class="btn-group admin">
      <a href="/beheer/orchidee_toevoegen" class="btn btn-primary actief admin_menu">ORCHIDEE TOEVOEGEN</a>
      <a href="/beheer/orchidee_verwijderen" class="btn btn-primary admin_menu">ORCHIDEE VERWIJDEREN</a>
    </div>
    <?php
    $account = new Account;
    $artikelGroep = new ArtikelGroep;

    if($account->isBeheerder()){
    ?>
    <h1>ORCHIDEE TOEVOEGEN</h1>
    <form method="post" enctype="multipart/form-data">
      <input type="text" name="titel" placeholder="Titel" class="form-control" autocomplete="off" required>
      <input type="text" name="korteOmschrijving" placeholder="Korte omschrijving" class="form-control" autocomplete="off" required>
      <textarea class="form-control" name="langeOmschrijving" placeholder="Lange omschrijving" required></textarea>
      <input type="number" name="prijs" min="1" step="0.01" class="form-control" placeholder="PRIJS IN EURO'S" required>
      <?php
      $artikelGroep->getArtikelGroepen();
      ?>
      <input type="file" name="img" placeholder="FOTO" class="form-control" accept="image/*">
      <input type="submit" class="btn btn-succes form-knop" name="submit" value="VOEG TOE">
    </form>
    <?php
    }
    ?>
  </div>
</div>
<?php
 if(!empty($_POST)){
   $titel = $_POST['titel'];
   $prijs = $_POST['prijs'];
   $artikelGroep = $_POST['artikelGroep'];
   $langeOmschrijving = $_POST['langeOmschrijving'];
   $korteOmschrijving = $_POST['korteOmschrijving'];
   $randId = rand(1, 999999);

   $stmt = DB::conn()->prepare("SELECT id FROM Orchidee WHERE id=?");
   $stmt->bind_param('i', $randId);
   $stmt->execute();
   $stmt->bind_result($opgehaaldId);
   $stmt->fetch();
   $stmt->close();

   $uploadName = $titel;
   $uploadName = str_replace(' ', '_', $uploadName);
   $uploadName = strtolower($uploadName);

   $target_dir = FOTO."/";
   $target_file = basename($_FILES["img"]["name"]);
   $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
   $isOk = false;
   switch($imageFileType){
     case 'jpg':
       $isOk = true;
       break;
     case 'JPG':
       $isOk = true;
       break;
     case 'jpeg':
       $isOk = true;
       break;
     case 'JPEG':
       $isOk = true;
       break;
     case 'png':
       $isOk = true;
       break;
     case 'PNG':
       $isOk = true;
       break;
   }
   if($isOk){
     $rand = rand(1, 9999);
     $name = $uploadName . "_" . $rand . "." . $imageFileType;
     $target_place = $target_dir . $name;

     if(move_uploaded_file($_FILES['img']['tmp_name'], $target_place)){
     }else{
       echo "Er was een fout tijdens het uploaden van de foto.";
     }
   if(!empty($opgehaaldId)){
     $newRandId = rand(1, 999999);
     $stmt = DB::conn()->prepare("INSERT INTO Orchidee(id, titel, langeOmschrijving, korteOmschrijving, prijs, img, soort) VALUES (?, ?, ?, ?, ?, ?, ?)");
     $stmt->bind_param('isssdsi', $newRandId, $titel, $langeOmschrijving, $korteOmschrijving, $prijs, $name, $artikelGroep);
     $stmt->execute();
     $stmt->close();

     header("Refresh:0; url=/artikel/$newRandId");
   }else{
     $stmt = DB::conn()->prepare("INSERT INTO Orchidee(id, titel, langeOmschrijving, korteOmschrijving, prijs, img, soort) VALUES (?, ?, ?, ?, ?, ?, ?)");
     $stmt->bind_param('isssdsi', $randId, $titel, $langeOmschrijving, $korteOmschrijving, $prijs, $name, $artikelGroep);
     $stmt->execute();
     $stmt->close();
     header("Refresh:0; url=/artikel/$randId");
   }
 }
}
?>
