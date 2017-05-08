create database orchideeholiday;
use orchideeholiday;

create table betaalWijze(
`id` INT,
`omschrijving` VARCHAR(100),
PRIMARY KEY(`id`)
);

create table artikelGroep(
`id` INT,
`omschrijving` VARCHAR(100),
PRIMARY KEY(`id`)
);

create table Persoon(
`id` int auto_increment,
`voornaam` varchar(255),
`achternaam` varchar(255),
`email` varchar(255),
`woonplaats` varchar(255),
`postcode` varchar(10),
`straat` varchar(255),
`huisnummer` varchar(255),
`betaalWijze` INT,
`anoniem` INT,
primary key(`id`),
FOREIGN KEY(`betaalWijze`) REFERENCES betaalWijze(`id`)
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
`soort` INT,
PRIMARY KEY(`id`),
FOREIGN KEY(`soort`) REFERENCES artikelGroep(`id`)
);

create table verzendWijze(
`id` INT,
`omschrijving` VARCHAR(100),
PRIMARY KEY(`id`)
);

create table `Order`(
`id` INT,
`persoon` INT,
`besteld` BOOL,
`verzendWijze` INT,
`betaalWijze` INT,
`orderdatum` VARCHAR(45),
`opmerking` TEXT,
`anoniem` INT,
PRIMARY KEY(`id`),
FOREIGN KEY(`persoon`) REFERENCES Persoon(`id`),
FOREIGN KEY(`verzendWijze`) REFERENCES verzendWijze(`id`),
FOREIGN KEY(`betaalWijze`) REFERENCES betaalWijze(`id`)
);

create table OrderRegel(
`id` INT,
`orchideeid` INT,
`orderid` INT,
PRIMARY KEY(`id`, `orchideeid`, `orderid`),
FOREIGN KEY(`orchideeid`) REFERENCES Orchidee(`id`),
FOREIGN KEY(`orderid`) REFERENCES `Order`(`id`)
);

create table Favoriet(
`orchidee` INT,
`persoon` INT,
PRIMARY KEY(`orchidee`, `persoon`),
FOREIGN KEY(`orchidee`) REFERENCES Orchidee(`id`),
FOREIGN KEY(`persoon`) REFERENCES Persoon(`id`)
);

INSERT INTO verzendWijze(id, omschrijving) VALUES (1, 'Koerierdienst');
INSERT INTO verzendWijze(id, omschrijving) VALUES (2, 'Ophalen op locatie');

INSERT INTO betaalWijze(id, omschrijving) VALUES (1, 'ING');
INSERT INTO betaalWijze(id, omschrijving) VALUES (2, 'RABOBANK');
INSERT INTO betaalWijze(id, omschrijving) VALUES (3, 'ABN AMRO');
INSERT INTO betaalWijze(id, omschrijving) VALUES (4, 'SNS BANK');
INSERT INTO betaalWijze(id, omschrijving) VALUES (5, 'BITCOIN');

INSERT INTO artikelGroep(id, omschrijving) VALUES (1, 'Angraecum longicalcar');
INSERT INTO artikelGroep(id, omschrijving) VALUES (2, 'Chloraea gavilu');
INSERT INTO artikelGroep(id, omschrijving) VALUES (3, 'Chondroscaphe chestertonii');
INSERT INTO artikelGroep(id, omschrijving) VALUES (4, 'Dendrobium nobile');
INSERT INTO artikelGroep(id, omschrijving) VALUES (5, 'Maanorchidee');
INSERT INTO artikelGroep(id, omschrijving) VALUES (6, 'Kerstorchidee');
INSERT INTO artikelGroep(id, omschrijving) VALUES (7, 'Paphinia seegeri');
INSERT INTO artikelGroep(id, omschrijving) VALUES (8, 'Paphiopedilum sukhakulii');
INSERT INTO artikelGroep(id, omschrijving) VALUES (9, 'Vanille-orchidee');
INSERT INTO artikelGroep(id, omschrijving) VALUES (10, 'Lange spinorchidee');

INSERT INTO Rol(id, omschrijving) VALUES(1, 'beheerder');
INSERT INTO Rol(id, omschrijving) VALUES(2, 'klant');
INSERT INTO Persoon(voornaam, achternaam, email, woonplaats, postcode, straat, huisnummer, betaalwijze)
VALUES('Jeroen', 'Grooten', 'contact@jeroengrooten.nl', 'Wijk bij Duurstede', '3961AM', 'Oeverstraat', '21A', 1);
INSERT INTO TussenRol(rolid, persoonid) VALUES(1, 1);
INSERT INTO Wachtwoord(wachtwoord, persoon) VALUES('$2y$10$ygURUwn2sI/6UcexSQlCn.CVyG//.WVdOCvbuVUhqaadCAuXGmHaS', 1);

