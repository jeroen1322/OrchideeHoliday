create database orchideeholiday;
use orchideeholiday;

create table Persoon(
`id` int,
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
`id` int,
`wachtwoord` varchar(255),
`persoon` int,
primary key(`id`),
foreign key(`persoon`) references Persoon(`id`)
);

create table Orchidee(
`id` INT,
`titel` VARCHAR(255),
`langeOmschrijving` VARCHAR(255),
`korteOmschrijving` VARCHAR(45),
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
`bedrag` FLOAT,
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
