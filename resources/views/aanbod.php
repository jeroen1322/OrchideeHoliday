<?php
$stmt = DB::conn()->prepare("SELECT titel FROM Orchidee");
$stmt->execute();
$stmt->bind_result($titel);
while($stmt->fetch()){
  $titels[] = $titel;
}
$stmt->close();

if(!empty($titels)){
  print_r($titels);
}else{
  echo 'geen titels';
}
?>

<?php
DB::conn()->close();
