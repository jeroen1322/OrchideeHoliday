<?php
function contactMail($post){

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
  $mail->addAddress('contact@jeroengrooten.nl', 'Jeroen Grooten');     // Add a recipient

  $mail->isHTML(true);                                  // Set email format to HTML

  $mail->Subject = 'Bericht via contactformulier';
  $mail->Body    = 'E-mail: '.$post['email'].'<br>
                    Naam: '.$post['naam'].'<br><br>
                    Onderwerp: '.$post['onderwerp'].'<br><br>
                    Bericht: '.$post['bericht'].'<br>';

  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  if(!$mail->send()) {
      echo 'ERROR TIJDENS HET VERSTUREN VAN DE EMAIL <br>';
      echo 'Mail Error: ' . $mail->ErrorInfo;
  }
}
