<?php
$gebruiker = new Account;
?>
<h1>SITEMAP</h1>

<h2>ARTIKELEN</h2>
<ul>
  <li><a href="/aanbod">Aanbod</a></li>
  <li><a href="/best_verkocht">Best verkocht</a></li>
  <li><a href="/artikel_groepen">Artikel groepen</a></li>
</ul>

<h2>ACCOUNT</h2>
<ul>
  <?php
  if(!$gebruiker->isIngelogd()){
    ?>
    <li><a href="/login">Inloggen</a></li>
    <li><a href="/registreren">Registreren</a></li>
    <?php
  }

  if($gebruiker->isIngelogd()){
    ?>
    <li><a href="/favorieten">Favorieten</a></li>
    <?php
  }
  ?>
</ul>

<h2>ZOEKEN</h2>
<ul>
  <li><a href="/zoeken">Simpel zoeken</a></li>
  <li><a href="/uitgebreid_zoeken">Uitgebreid zoeken</a></li>
</ul>

<?php
if($gebruiker->isBeheerder()){
  ?>
  <h2>BEHEER</h2>
  <ul>
    <li><a href="/beheer/overzicht">Overzicht</a></li>
    <li><a href="/beheer/orchidee_toevoegen">Orchidee toevoegen</a></li>
    <li><a href="beheer/orchidee_verwijderen">Orchidee verwijderen</a></li>
  </ul>
  <?php
}
