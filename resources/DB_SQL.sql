create database orchideeholiday;
use orchideeholiday;

create table Persoon(
`id` int auto_increment,
`voornaam` varchar(255),
`achternaam` varchar(255),
`email` varchar(255),
`woonplaats` varchar(255),
`postcode` varchar(10),
`straat` varchar(255),
`huisnummer` varchar(255),
primary key(`id`)
);

create table Rol(
`id` int,
`omschrijving` varchar(255),
primary key(`id`)
);

create table TussenRol(
`rolid` INT,
`persoonid` INT,
PRIMARY KEY(`rolid`, `persoonid`),
FOREIGN KEY(`persoonid`) REFERENCES Persoon(`id`)
);

create table Wachtwoord(
`id` int auto_increment,
`wachtwoord` varchar(255),
`persoon` int,
primary key(`id`),
foreign key(`persoon`) references Persoon(`id`)
);

create table Orchidee(
`id` INT,
`titel` VARCHAR(255),
`langeOmschrijving` TEXT,
`korteOmschrijving` VARCHAR(45),
`prijs` FLOAT,
`img` VARCHAR(45),
PRIMARY KEY(`id`)
);

create table Exemplaar(
`id` INT,
`orchidee` INT,
`status` INT,
`aantalVerkocht` INT,
PRIMARY KEY(`id`),
FOREIGN KEY(`id`) REFERENCES Orchidee(`id`)
);

create table `Order`(
`id` INT,
`persoon` INT,
`besteld` BOOL,
`afleverdatum` VARCHAR(45),
`ophaaldatum` VARCHAR(45),
`orderdatum` VARCHAR(45),
PRIMARY KEY(`id`),
FOREIGN KEY(`persoon`) REFERENCES Persoon(`id`)
);

create table OrderRegel(
`exemplaarid` INT,
`orderid` INT,
PRIMARY KEY(`exemplaarid`, `orderid`),
FOREIGN KEY(`exemplaarid`) REFERENCES Exemplaar(`id`),
FOREIGN KEY(`orderid`) REFERENCES `Order`(`id`)
);

create table Favoriet(
`orchidee` INT,
`persoon` INT,
PRIMARY KEY(`orchidee`, `persoon`),
FOREIGN KEY(`orchidee`) REFERENCES Orchidee(`id`),
FOREIGN KEY(`persoon`) REFERENCES Persoon(`id`)
);


INSERT INTO Rol(id, omschrijving) VALUES(1, 'beheerder');
INSERT INTO Rol(id, omschrijving) VALUES(2, 'klant');
INSERT INTO Persoon(voornaam, achternaam, email, woonplaats, postcode, straat, huisnummer)
VALUES('Jeroen', 'Grooten', 'contact@jeroengrooten.nl', 'Wijk bij Duurstede', '3961AM', 'Oeverstraat', '21A');
INSERT INTO TussenRol(rolid, persoonid) VALUES(1, 1);
INSERT INTO Wachtwoord(wachtwoord, persoon) VALUES('$2y$10$ygURUwn2sI/6UcexSQlCn.CVyG//.WVdOCvbuVUhqaadCAuXGmHaS', 1);
