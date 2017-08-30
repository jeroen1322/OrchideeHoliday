<?php
class Contact{
  /**
  * Send the information that was entered in the contact form
  *@param array $post: the $_POST array.
  */
  public function verstuurContact($post){
    contactMail($post);
    echo '<div class="succes"><b>Uw bericht is verstuurd</b></div>';
  }

  /**
   * Store the form data in the Contact table in the database
   * @param  array $post The $_POST array
   */
  public function storeContact($post){
    $stmt = DB::conn()->prepare('INSERT INTO `Contact`(email, naam, onderwerp, bericht) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $post['email'], $post['naam'], $post['onderwerp'], $post['bericht']);
    $stmt->execute();
    $stmt->close();
  }


  public function getContactId(){
    $stmt = DB::conn()->prepare('SELECT id FROM Contact');
    $stmt->execute();
    $stmt->bind_result($id);
    while($stmt->fetch()){
      $contactId[] = $id;
    }
    $stmt->close();

    return $contactId;
  }

  public function getContactInfo($id){
    $result = array();
    $stmt = DB::conn()->prepare('SELECT email, naam, onderwerp, bericht FROM Contact WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($email, $naam, $onderwerp, $bericht);
    while($stmt->fetch()){
      $result['email'] = $email;
      $result['naam'] = $naam;
      $result['onderwerp'] = $onderwerp;
      $result['bericht'] = $bericht;
    }
    $stmt->close();

    if(!empty($result)){
      return $result;
    }
  }
}
