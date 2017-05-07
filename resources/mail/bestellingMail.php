<?php
function bestellingAfronden($winkelmand, $gebruikerInfo){
  function thumbInfo($artikelId){
    $stmt = DB::conn()->prepare('SELECT id, titel, korteOmschrijving, prijs, img FROM Orchidee WHERE id=?');
    $stmt->bind_param('i', $artikelId);
    $stmt->execute();
    $stmt->bind_result($id, $titel, $korteOmschrijving, $prijs, $img);
    while($stmt->fetch()){
      $thumbInfo['id'] = $id;
      $thumbInfo['titel'] = $titel;
      $thumbInfo['korteOmschrijving'] = $korteOmschrijving;
      $thumbInfo['prijs'] = $prijs;
      $thumbInfo['img'] = $img;
    }
    $stmt->close();

    return $thumbInfo;
  }

  $mail = new PHPMailer;

  // $mail->SMTPDebug = 3;                               // Enable verbose debug output

  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = 'mbotempovideo@gmail.com';                 // SMTP username
  $mail->Password = 'JeroenSara7!';                           // SMTP password
  $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 587;                                    // TCP port to connect to

  $mail->setFrom('noreply@orchideeholiday.nl', 'orchideeholiday');
  $mail->addAddress($gebruikerInfo['email'], 'Jeroen Grooten');     // Add a recipient

  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = 'Factuur bestelling';
  $mail->Body    = 'Geachte '.$gebruikerInfo['voornaam'].' '.$gebruikerInfo['achternaam'].',<br><br>
                    <p>Dank u wel voor uw bestelling bij OrchideeHoliday. Hieronder vind u een overzicht van uw bestelling.</p>
                    <table style="width:50%">
                      <tr>
                        <th style="border-bottom:1px solid #000; text-align:left;">Titel</th>
                        <th style="border-bottom:1px solid #000; text-align:left;">Prijs</th>
                      </tr>';
  $totaal = array();
  foreach($winkelmand as $w){
    $info = thumbInfo($w);
    $mail->Body  .= '<tr>
                      <td style="border-bottom:1px solid #696969">'.$info['titel'].'</td>
                      <td style="border-bottom:1px solid #696969"><b>&euro;'.$info['prijs'].'</b></td>
                     </tr>';
    $totaal[] = $info['prijs'];
  }
  $totaalPrijs = array_sum($totaal);
  $mail->Body    .=  '</table>';
  $mail->Body    .= '<b>TOTAAL: &euro;'.$totaalPrijs.'</b><br><br>';
  $mail->Body    .= 'Met vriendelijke groet, <br> Het OrchideeHoliday team';
  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  if(!$mail->send()) {
      echo 'ERROR TIJDENS HET VERSTUREN VAN DE EMAIL <br>';
      echo 'Mail Error: ' . $mail->ErrorInfo;
  }
}
