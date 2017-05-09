<?php
class Contact{
  /**
  *Send the information that was entered in the contact form
  *
  *@param: the $_POST array.
  */
  public function verstuurContact($post){
    contactMail($post);
    echo '<div class="succes"><b>Uw bericht is verstuurd</b></div>';
  }
}
