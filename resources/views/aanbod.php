<?php
$stmt = DB::conn()->prepare("SELECT id FROM Orchidee");
$stmt->execute();
$stmt->bind_result($id);
while($stmt->fetch()){
  $ids[] = $id;
}
$stmt->close();

$thumb = new Artikel;

if(!empty($ids)){
  foreach($ids as $id){
    $info = $thumb->thumbInfo($id);
    ?>
    <div class="filmThumbnail filmAanbodFilm col-md-3 filmAanbodItem">
      <div class="thumb">
          <a href=<?php echo '/artikel/'.$info['id'] ?>>
            <img src=<?php echo '/foto/'.$info['img'] ?> class="thumb_img filmaanbod_img"/></a>
            <h2 class="textfilmaanbod"><?php echo $info['titel'] ?> </h2>
            <a href="/artikel/<?php echo $info['id']?>"><button class="btn btn-succes form-knop bekijk-knop">BEKIJK</button></a>
          </a>
        </div>
      </div>
    <?php
  }
}else{
  echo '<div class="warning"><b>Er zijn nog geen artikelen toegevoegd.</b></div>';
}
?>

<?php
DB::conn()->close();
