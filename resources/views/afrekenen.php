<?php
$afrekenen = new Afrekenen;
$account = new Account;
$betalen = new Betalen;
$login = new winkelmandLogin;
$registreer = new winkelmandRegistreer;
$winkelmand = new Winkelmand;
$artikel = new Artikel;

if(!empty($_POST)){

  if($account->isIngelogd()){

    switch($_GET['stap']){
      case 'opmerking':
      $afrekenen->slaBezorgwijzeOpInSession($_POST['bezorgwijze']);
      if($afrekenen->controlleerBezorgwijzeSession()){
        ?>
        <h1>AFREKENEN</h1>
        <hr>
        <h3>OPMERKING</h3>

        <form method="post" action="?stap=overzicht">
          <textarea name="opmerking" placeholder="OPMERKING (optioneel)" class="form-control"></textarea>
          <button class="btn btn-primary form-knop">VERDER</button>
        </form>
        <?php
      }else{
        echo '<div class="warning"><b>Er is een fout opgetreden. Probeer het later opnieuw</b></div>';
      }
      break;

      case 'overzicht':
      if(!empty($_POST['opmerking'])){
        $afrekenen->storeOpmerkingInSession($_POST['opmerking']);
      }else{
        $afrekenen->removePreviousOpmerkingSession();
      }
      $gebruikerId = $account->getLoginId();
      $artikelen = $winkelmand->getArtikelen($gebruikerId);

      if(!empty($artikelen)){
        ?>
        <button class="btn btn-succes form-knop form-knop-rechts" onClick="window.print()"><i class="fa fa-print" aria-hidden="true"></i></button>
        <h1>AFREKENEN</h1>
        <hr>
        <h4>OVERZICHT</h4>
          <table class="table winkelmand_table">
            <tbody>
              <?php
              $openOrders = $account->telOpenOrders($_SESSION['login'][0]);
              $totaal = array();
              foreach ($artikelen as $artikelId){
                if($artikel->isNewPriceActive($artikelId)){
                  if($openOrders < 3){
                    $nietGenoeg = true;
                  }else{
                    $nietGenoeg = false;
                  }

                  if($nietGenoeg){
                    $info = $artikel->thumbInfo($artikelId);
                  }else{
                    $info = $artikel->newThumbInfo($artikelId);
                  }
                }else{
                  $info = $artikel->thumbInfo($artikelId);
                }
                $totaal[] = $info['prijs'];
                ?>
                <tr>
                  <td><a href="/artikel/<?php echo $artikelId?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
                  <td><p><?php echo $info['titel'] ?></p></td>
                  <td><h4>€<?php echo $info['prijs'] ?></h4></td>
                </tr>
                <?php
              }
              ?>
            </tbody>
          </table>
          <?php
          echo '<h4><b>TOTAAL PRIJS: €'.array_sum($totaal).'</b></h4>';
          $opmerking = $afrekenen->getSessionOpmerking();
          if(!empty($opmerking)){
            $winkelmand->inputOpmerking($opmerking, $gebruikerId);
          ?>
          <hr>
          <h3>OPMERKING: </h3>"<?php echo $opmerking ?>"</h3>
          <hr>
          <?php
          }
          $winkelmand->inputVerzendWijze($_SESSION['bezorgwijze'], $gebruikerId);
          ?>
          <hr>
          <h4><b>BEZORGWIJZE:</b> <?php echo $_SESSION['bezorgwijze'] ?></h4>
          <div class="overzichtBottom">
            <a href="/?afgerond=false&ingelogd=true"><button class="btn btn-succes form-knop annuleren">BESTELLING ANNULEREN</button></a>
            <a href="/?afgerond=true&ingelogd=true"><button class="btn btn-succes form-knop form-knop-rechts">BESTELLING AFRONDEN</button></a>
          </div>
          <?php
        }
        break;

      }
  }else{
    switch($_GET['stap']){
      case 'inloggen':
        $login->login($_SESSION['winkelmand'], $_POST['email'], $_POST['wachtwoord']);
        break;

      case 'registreren':
        $registreer->registreer($_POST, $_SESSION['winkelmand']);
        break;

      case 'anoniemBestellen':
        ?>
        <h1>AFREKENEN</h1>
        <hr>
        <h3>VERZENDWIJZE</h3>
        <form method="post" action="?stap=opmerking">
          <div class="left">
            <div class="panel panel-default bezorger">
              <div class="panel-body">
                <input type="radio" id="bezorger" name="bezorgwijze" value="Koerierdienst">
                <label class="verzendwijze" for="bezorger">
                  <i class="fa fa-truck" aria-hidden="true"></i>
                  <h3><b>LATEN BEZORGEN</b></h3>
                </label>
              </div>
            </div>
          </div>
          <div class="right">
            <div class="panel panel-default ophalen">
              <div class="panel-body">
                <input type="radio" id="ophalen" name="bezorgwijze" value="Ophalen op locatie">
                <label class="verzendwijze" for="ophalen">
                  <i class="fa fa-user" aria-hidden="true"></i>
                  <h3><b>ZELF OPHALEN</b></h3>
                </label>
              </div>
            </div>
          </div>
          <br>
          <div></div>
          <button type="submit" class="btn btn-primary form-knop verzendButton">VERDER</button>
        </form>
        <?php
        break;

      case 'opmerking':
        $afrekenen->slaBezorgwijzeOpInSession($_POST['bezorgwijze']);
        if($afrekenen->controlleerBezorgwijzeSession()){
          ?>
          <h1>AFREKENEN</h1>
          <hr>
          <h3>OPMERKING</h3>

          <form method="post" action="?stap=overzicht">
            <textarea name="opmerking" placeholder="OPMERKING (optioneel)" class="form-control"></textarea>
            <button class="btn btn-primary form-knop">VERDER</button>
          </form>
          <?php
        }else{
          echo '<div class="warning"><b>Er is een fout opgetreden. Probeer het later opnieuw</b></div>';
        }
        break;

      case 'overzicht':
        if(!empty($_POST['opmerking'])){
          $afrekenen->storeOpmerkingInSession($_POST['opmerking']);
        }

        if(!empty($_SESSION['winkelmand'])){
          ?>
          <button class="btn btn-succes form-knop form-knop-rechts" onClick="window.print()"><i class="fa fa-print" aria-hidden="true"></i></button>
          <h1>AFREKENEN</h1>
          <hr>
          <h4>OVERZICHT</h4>
            <table class="table winkelmand_table">
              <tbody>
                <?php
                $totaal = array();
                foreach ($_SESSION['winkelmand'] as $artikelId){
                  $info = $artikel->thumbInfo($artikelId);
                  $totaal[] = $info['prijs'];
                  ?>
                  <tr>
                    <td><a href="/artikel/<?php echo $artikelId?>"><img src="/foto/<?php echo $info['img']?>" class="winkelmand_img"></a></td>
                    <td><p><?php echo $info['titel'] ?></p></td>
                    <td><h4>€<?php echo $info['prijs'] ?></h4></td>
                  </tr>
                  <?php
                }
                ?>
              </tbody>
            </table>
            <?php
            echo '<h4><b>TOTAAL PRIJS: €'.array_sum($totaal).'</b></h4>';
            $opmerking = $afrekenen->getSessionOpmerking();
            if(!empty($opmerking)){
            ?>
            <hr>
            <h3>OPMERKING: </h3>"<?php echo $opmerking ?>"</h3>
            <hr>
            <?php
            }
            ?>
            <hr>
            <h4><b>BEZORGWIJZE:</b> <?php echo $_SESSION['bezorgwijze'] ?></h4>
            <hr>
            <h4>Na het afronden van de bestelling, ontvangt u een factuur via email waar u eerst aan moet voldoen voordat de bestelling word verzonden</h4>
            <h4><b>Bezorg-gegevens:</b></h4>
            <form method="post" action="/?afgerond=true&ingelogd=false">
              <input type="email" name="email" placeholder="Email" class="form-control" autocomplete="off" required>
              <input type="text" name="woonplaats" placeholder="Woonplaats" class="form-control" autocomplete="off" required>
              <input type="text" name="postcode" placeholder="Postcode" class="form-control" autocomplete="off" required>
              <input type="text" name="straat" placeholder="Straat" autocomplete="off" class="form-control" autocomplete="off" required>
              <input type="text" name="huisnummer" placeholder="Huisnummer" class="form-control" autocomplete="off" required>
              <?php
              if(!empty($opmerking)){
                ?>
                <input type="hidden" name="opmerking" value="<?php echo $opmerking?>">
                <?php
              }
              ?>
              <input type="submit" class="btn btn-succes form-knop form-knop-rechts" value="BESTELLING AFRONDEN">
            </form>
            <a href="/?afgerond=false&ingelogd=false"><button class="btn btn-succes form-knop annuleren">BESTELLING ANNULEREN</button></a>
            <?php
          }else{
            echo '<div class="warning"><b>U lijkt geen artikelen te hebben om af te rekenen</b></div>';
          }
          break;
    }
  }

}else{

  if($account->isIngelogd()){
    ?>
    <h1>AFREKENEN</h1>
    <hr>
    <h3>VERZENDWIJZE</h3>
    <form method="post" action="?stap=opmerking">
      <div class="left">
        <div class="panel panel-default bezorger">
          <div class="panel-body">
            <input type="radio" id="bezorger" name="bezorgwijze" value="Koerierdienst">
            <label class="verzendwijze" for="bezorger">
              <i class="fa fa-truck" aria-hidden="true"></i>
              <h3><b>LATEN BEZORGEN</b></h3>
            </label>
          </div>
        </div>
      </div>
      <div class="right">
        <div class="panel panel-default ophalen">
          <div class="panel-body">
            <input type="radio" id="ophalen" name="bezorgwijze" value="Ophalen op locatie">
            <label class="verzendwijze" for="ophalen">
              <i class="fa fa-user" aria-hidden="true"></i>
              <h3><b>ZELF OPHALEN</b></h3>
            </label>
          </div>
        </div>
      </div>
      <br>
      <div></div>
      <button type="submit" class="btn btn-primary form-knop verzendButton">VERDER</button>
    </form>
  <?php
  }else{
    ?>
    <h1>INLOGGEN OF REGISTREREN</h1>
    <h4>Als u anoniem besteld, zal u een factuur moeten voldoen voordat de bestelling word verzonden.</h4>
    <div class="left">
      <div class="panel panel-default">
        <div class="panel-body login-panel">
          <h1>LOGIN</h1>
          <form method="post" action="?stap=inloggen">
            <input type="email" name="email" placeholder="Email" class="form-control">
            <input type="password" name="wachtwoord" placeholder="Wachtwoord" class="form-control">
            <input type="submit" name="submit" class="btn btn-primary form-knop" value="LOGIN">
          </form>
        </div>
      </div>
      <hr>
      <form method="post" action="?stap=anoniemBestellen">
        <button type="submit" name="submit" class="btn btn-primary form-knop anoniem-bestel-knop">ANONIEM BESTELLEN</button>
      </form>
    </div>
    <div class="right">
      <div class="panel panel-default">
        <div class="panel-body login-panel">
          <h1>REGISTREER</h1>
          <form method="post" action=?stap=registreren>
            <input type="text" name="voornaam" placeholder="Voornaam" class="form-control" autocomplete="off" required>
            <input type="text" name="achternaam" placeholder="Achternaam" class="form-control" autocomplete="off" required>
            <input type="email" name="email" placeholder="Email" class="form-control" autocomplete="off" required>
            <input type="text" name="woonplaats" placeholder="Woonplaats" class="form-control" autocomplete="off" required>
            <input type="text" name="postcode" placeholder="Postcode" class="form-control" autocomplete="off" required>
            <input type="text" name="straat" placeholder="Straat" autocomplete="off" class="form-control" autocomplete="off" required>
            <input type="text" name="huisnummer" placeholder="Huisnummer" class="form-control" autocomplete="off" required>
            <input type="password" name="wachtwoord" placeholder="Wachtwoord" autocomplete="off" class="form-control" autocomplete="off" required>
            <input type="password" name="herhaalWachtwoord" placeholder="Herhaal wachtwoord" autocomplete="off" class="form-control" autocomplete="off" required>
            <?php
              $betaalwijze = $betalen->displayBetaalWijze();
            ?>
            <input type="submit" name="submit" class="btn btn-primary form-knop" value="REGISTREER">
          </form>
        </div>
      </div>
    </div>
    <?php
  }
}
