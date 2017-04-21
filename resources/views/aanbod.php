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
          </a>
        </div>
      </div>
    <?php
  }
}else{
  echo 'geen titels';
}
?>

<?php
DB::conn()->close();
