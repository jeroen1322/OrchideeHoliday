<?php
class Contact{
  public function verstuurContact($post){
    contactMail($post);
    echo '<div class="succes"><b>Uw bericht is verstuurd</b></div>';
  }
}
