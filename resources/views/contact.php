<?php
$contact = new Contact;
if(!empty($_POST)){
  $contact->verstuurContact($_POST);
  $contact->storeContact($_POST);
}

?>
<h1>CONTACT</h1>
<form method="post">
  <input type="email" name="email" placeholder="E-MAIL" class="form-control" autocomplete="off" required>
  <input type="text" name="naam" placeholder="NAAM" class="form-control" autocomplete="off" required>
  <input type="text" name="onderwerp" placeholder="ONDERWERP" class="form-control" autocomplete="off" required>
  <textarea placeholder="BERICHT" name="bericht" class="form-control" autocomplete="off" required></textarea>
  <input type="submit" class="btn btn-primary form-knop" value="VERSTUUR">
</form>
