<?php
$gebruiker = new Account;
$artikel = new Artikel;
$gebruikerId = $gebruiker->getLoginId();
$favorieten = $gebruiker->getFavorieten($gebruikerId);

if(!empty($_GET)){
  if($_GET['action'] == 'delete'){
    if($gebruiker->deleteFavoriet($_GET['code'])){
      header("Refresh:0; url=/favorieten");
    }
  }
}

if(!empty($favorieten)){
  ?>
  <h1>FAVORIETEN</h1>
  <table class="table winkelmand_table">
    <tbody>
  <?php
  foreach($favorieten as $favoriet){
    $info = $artikel->thumbInfo($favoriet);
    ?>
    <tr>
      <td><a href="/artikel/<?php echo $favoriet?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
      <td><p><?php echo $info['titel'] ?></p></td>
      <td><h4>€<?php echo $info['prijs'] ?></h4></td>
      <td>
        <form method="post" action="?action=delete&code=<?php echo $favoriet ?>">
          <button type="submit" class="btn btn-success">
              <i class="fa fa-trash-o" aria-hidden="true"></i>
          </button>
        </form>
      </td>
    </tr>
    <?php
  }
}else{
  echo '<div class="warning"><b>U HEEFT NOG GEEN FAVORIETEN</b></div>';
}
