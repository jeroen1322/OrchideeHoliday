<div class="panel panel-default">

<?php
$login = new Login;
if(!empty($_POST)){
  $email = $_POST['email'];
  $wachtwoord = $_POST['wachtwoord'];
  $login->voegLoginUit($email, $wachtwoord);
}

if(!empty($_SESSION['login'])){
  echo "<div class='warning'><b>U BENT AL INGELOGD</b></div>";
}else{
?>
  <div class="panel-body login-panel">
    <h1>LOGIN</h1>
    <form method="post">
      <input type="email" name="email" placeholder="Email" class="form-control">
      <input type="password" name="wachtwoord" placeholder="Wachtwoord" class="form-control">
      <input type="submit" name="submit" class="btn btn-primary form-knop" value="LOGIN">
    </form>
  </div>
</div>
<?php
}
