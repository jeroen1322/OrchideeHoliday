<?php
$groep = new ArtikelGroep;
$groepen = $groep->getGroepen();
?>
<h1>ARTIKEL GROEPEN</h1>
<table class="table winkelmand_table">
  <thead>
    <td></td>
  </thead>
  <tbody>
<?php
foreach($groepen as $key => $value){
  echo '<tr><td><a href="/groep/'.$key.'"><h4>'.$value.'</h4></a></td></tr>';
}
?>
  </tbod>
</table>
