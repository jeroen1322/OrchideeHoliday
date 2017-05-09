<?php

class Login{

  /*
  Function contains the whole login procedure.
  The function checks if the fields are actually filled in and
  if the submitted password mashes the hash of the stored password that corresponds with the submitted email adres.
  When the the information is correct, it will create the 'login' session and fill in some account information.
  The session values are [0] = the user's id, [1] = The user's name and [2] = The id of the rol that is attached to this account.

  @param email: The value of the Email input field
  @param wachtwoord: The value of the Wachtwoord input field
  */
  public function voegLoginUit($email, $wachtwoord){

    /*
    Checks if the parameters actually contain data

    @param email: The value of the Email input field
    @param wachtwoord: The value of the Wachtwoord input field
    @return: True, if there are actually strings send
    */
    function controlleerInvullingVelden($email, $wachtwoord){
      if($email != '' && $wachtwoord != ''){
        return true;
      }
    }

    /*
    Get het id from the row in Persoon where the email column matches the parameter

    @param: The email adres which is matched to the email column in the Persoon table
    @return: The id form the account where the email column matches the parameter
    */
    function getAccountId($email){
      $stmt = DB::conn()->prepare("SELECT id FROM Persoon WHERE email=?");
      $stmt->bind_param("s", $email);
      $stmt->execute();

      $stmt->bind_result($klantId);
      $stmt->fetch();
      $stmt->close();

      return $klantId;
    }

    /*
    Get the hashed password from the Wachtwoord table where the persoon column corresponds with the parameter.

    @param: The user's id that will be matched with the persoon column
    @return: The user's hashed password
    */
    function getWachtwoord($id){
      $ww_stmt = DB::conn()->prepare("SELECT wachtwoord FROM Wachtwoord WHERE persoon=?");
      $ww_stmt->bind_param("i", $id);
      $ww_stmt->execute();

      $ww_stmt->bind_result($ww);
      $ww_stmt->fetch();
      $ww_stmt->close();

      return $ww;
    }

    /*
    Get the user's information, like the rolid that is tied to the account and the name of the user.

    when the the information is correct, it will create the 'login' session and fill in some account information.
    The session values are [0] = the user's id, [1] = The user's name and [2] = The id of the rol that is attached to this account.

    The hash of the filled in password and the hashed password that is taken from the database will be machted in password_verify().

    @param wachtwoord: The information that is submitted in the Wachtwoord field
    @param opgehaaldWachtwoord: The hash of the password that is fetched from the database
    @param id: The id of the user that is trying to log in
    */
    function logInSession($wachtwoord, $opgehaaldWachtwoord, $id){
      if (password_verify($wachtwoord, $opgehaaldWachtwoord)) {
        $stmt = DB::conn()->prepare('SELECT rolid FROM TussenRol WHERE persoonid=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($klantRolId);
        $stmt->fetch();
        $stmt->close();

        $stmt = DB::conn()->prepare('SELECT voornaam, achternaam FROM Persoon WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($voornaam, $achternaam);
        $stmt->fetch();
        $stmt->close();

        $naam = $voornaam.' '.$achternaam;

        $_SESSION['login'] = array();
        $_SESSION['login'][] = $id; //Zet de session die we kunnen checken in de functie isIngelogd();
        $_SESSION['login'][] = $naam;
        $_SESSION['login'][] = $klantRolId;
        header("Refresh:0; url=/");
      } else {
        echo '<div class="alert"><b>Deze email en wachtwoord combinatie is niet bij ons geregistreerd.</b></div>';
        return false;
      }
    }

    if(controlleerInvullingVelden($email, $wachtwoord)){
      $id = getAccountId($email);
      $opgehaaldWachtwoord = getWachtwoord($id);
      logInSession($wachtwoord, $opgehaaldWachtwoord, $id);

    }else{
      echo '<div class="alert"><b>Controleer of u alle velden correct heeft ingevuld.</b></div>';
    }
  }

}
