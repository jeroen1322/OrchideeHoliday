# OrchideeHoliday
Bron van het eindexamenproject voor opleiding Applicatie- en mediaontwikkelaar

Het project gaat onder de naam "OrchideeHoliday".


# Installatie
Kleine instructie over hoe je het project op een (nieuwe) GNU/Linux machine moet installeren. 

__Installeer Apache__
```
sudo apt-get update
sudo apt-get install apache2
```

__Installeer MySQL en dergelijke pakketten__
```
sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql
```

__Activeer MySQL__
```
sudo mysql_install_db
```

__Run de MySQL setup (script)__
```
sudo /usr/bin/mysql_secure_installation
```

__Installeer PHP__
```
sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt
```

Het is handig om index.php aan je Directory Index lijst toe te voegen
```
sudo nano /etc/apache2/mods-enabled/dir.conf
```
Het bestand moet er nu zo uit zien:
```
<IfModule mod_dir.c>

          DirectoryIndex index.php index.html index.cgi index.pl index.php index.xhtml index.htm

</IfModule>
```

__Activeer URL Rewriting__
```
sudo a2enmod rewrite
```

__Clone de repository__ 
Ga eerst naar */usr/www/html*
```
cd /usr/www/html
```

Installeer Git
```
sudo apt-get install git
```

Voer dan het volgende Git command uit
```
git clone https://github.com/jeroen1322/tempovideo-basis.git
``` 

__Verplaats de files naar */html*__
```
cd tempovideo-basis
```
```
mv * ../
```

__Pas 000-default.conf aan__
```
sudo nano /etc/apache2/sites-available/000-default.conf
```
Verander de DocumentRoot naar
```
/var/www/html/public
```
Voeg dit onder de DocumentRoot toe
```
        <Directory />
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^ routes.php [L]
            AllowOverride None
            Order allow,deny
            allow from all
        </Directory>
```

__Start Apache opnieuw op__
```
sudo service apache2 restart
```

__Voer de Database in__
```
mysql -u root -p
```
Kopieer de SQL uit het bestand /resources/DB_SQL.sql 
Plak de SQL in de terminal en voer het uit.

__Verander de DB inloggegevens in /resources/db.php__
```
sudo nano /var/www/html/resources/db.php
```
