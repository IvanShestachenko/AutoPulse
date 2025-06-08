-- Adminer 4.8.1 MySQL 8.0.42-0ubuntu0.22.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `shestiva` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `shestiva`;

CREATE TABLE `images` (
  `insertion_id` int NOT NULL,
  `order_number` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`insertion_id`,`order_number`),
  CONSTRAINT `image to insertion` FOREIGN KEY (`insertion_id`) REFERENCES `insertions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `images` (`insertion_id`, `order_number`, `image_path`) VALUES
(43,	1,	'photo1_6840337ac0b7f1.21235525.jpg'),
(43,	2,	'photo2_6840337acfe044.66773252.jpg'),
(43,	3,	'photo3_6840337adb7cb0.34874024.jpg'),
(43,	4,	'photo4_6840337ae73c21.29805656.jpg'),
(43,	5,	'photo5_6840337af2c9b2.22232243.jpg'),
(45,	1,	'photo1_68402424b12055.55291797.jpg'),
(45,	2,	'photo2_68402424c067d5.01269162.jpg'),
(45,	3,	'photo3_68402424cb6e74.68816575.jpg'),
(45,	4,	'photo4_68402424d675d3.75858388.jpg'),
(45,	5,	'photo5_68402424e152a5.35073111.jpg'),
(46,	1,	'photo1_684025b04fadc2.80174131.jpg'),
(46,	2,	'photo2_684025b05e47a7.61067527.jpg'),
(46,	3,	'photo3_684025b069bc14.98772106.jpg'),
(46,	4,	'photo4_684025b0755536.37643020.jpg'),
(46,	5,	'photo5_684025b08081d7.99258820.jpg'),
(47,	1,	'photo1_68402942d609b7.69471378.jpg'),
(47,	2,	'photo2_68402942e3e716.47762683.jpg'),
(47,	3,	'photo3_68402942eeabd0.88592554.jpg'),
(47,	4,	'photo4_68402943055099.54768850.jpg'),
(47,	5,	'photo5_68402943100d21.58616843.jpg'),
(48,	1,	'photo1_684031558ca8b6.33039035.jpg'),
(48,	2,	'photo2_684031559aba46.30105356.jpg'),
(48,	3,	'photo3_68403155a56255.90584025.jpg'),
(48,	4,	'photo4_68403155b05ed1.08887467.jpg'),
(48,	5,	'photo5_68403155bb4995.81088124.jpg'),
(50,	1,	'photo1_68403566e98127.57773211.jpg'),
(50,	2,	'photo2_68403567047482.47228714.jpg'),
(50,	3,	'photo3_684035670ffb86.92715387.jpg'),
(50,	4,	'photo4_684035671afbb9.99043022.jpg'),
(50,	5,	'photo5_6840356725d246.12315077.jpg'),
(51,	1,	'photo1_6840393c308797.34427916.jpg'),
(51,	2,	'photo2_6840393c3fd8c0.23157874.jpg'),
(51,	3,	'photo3_6840393c4b16d3.64835489.jpg'),
(51,	4,	'photo4_6840393c564dd2.22595715.jpg'),
(51,	5,	'photo5_6840393c614c57.95389468.jpg');

CREATE TABLE `insertions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `make` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `short_description` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` int NOT NULL,
  `year` int NOT NULL,
  `mileage` int NOT NULL,
  `power` int NOT NULL,
  `fuel` enum('Diesel','Petrol','Gas','Hybrid','EV') COLLATE utf8mb4_general_ci NOT NULL,
  `engine_capacity` int DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(600) COLLATE utf8mb4_general_ci NOT NULL,
  `insertion_status` enum('waiting','published') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'waiting',
  PRIMARY KEY (`id`),
  KEY `Insertion to seller` (`seller_id`),
  CONSTRAINT `Insertion to seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `insertions` (`id`, `seller_id`, `make`, `model`, `short_description`, `price`, `year`, `mileage`, `power`, `fuel`, `engine_capacity`, `avatar_path`, `description`, `insertion_status`) VALUES
(43,	33,	'Audi',	'A6',	'S-Line 3.0 TDI 210 kW',	1449900,	2024,	26366,	210,	'Petrol',	2967,	'avatar_6840337ac346c5.59181091.jpg',	'',	'published'),
(45,	33,	'Ford',	'Focus',	'ST, 206 KW, ČR',	589900,	2019,	91180,	206,	'Petrol',	2261,	'avatar_68402424b3d962.95824438.jpg',	'',	'published'),
(46,	33,	'Ford',	'Focus',	'RS 2.3 257 kW 4x4 1.majitel',	823000,	2018,	96900,	257,	'Petrol',	2261,	'avatar_684025b05242e8.00182115.jpg',	'Auto v perfektnim stavu,jsem první majitel,koupeno ve Ford Autopalace úplně nové.Servisováno pouze v autorizovaném servisu Ford,bez jskýchkoliv úprav motoru,tankován pouze premiový 100 oktanový benzin.K autu mohu přidat sportovní sání Eventuri ( nové stálo 23.000 Kč ).Zimní pneu 70% na autě a letní pneu k autu 40%,auto po výměně oleje. Výměny oleje jsem dělal po 10.000 km,měněn i olej i v diferenciálech.Nikdy nebylo bourané.',	'published'),
(47,	33,	'Ford',	'Mondeo',	'2,0 TDCi 110kW TITANIUM',	385000,	2018,	85200,	110,	'Diesel',	1997,	'avatar_68402942d89fd3.81992644.jpg',	'',	'published'),
(48,	33,	'Toyota',	'Corolla',	'2.0HEV 197k TS GR SPORT CVT',	749900,	2024,	23700,	145,	'Petrol',	1987,	'avatar_684031558f6cc8.20628345.jpg',	'Značková záruka 3roky/100.000km, GR SPORT',	'published'),
(50,	33,	'Ford',	'Mustang',	'Supercharger 5.0 V8, 750HP, GT',	1048000,	2018,	36300,	551,	'Petrol',	4951,	'avatar_68403566ec4ef3.24187100.jpg',	'',	'published'),
(51,	33,	'Audi',	'TT',	'2,0 TFSI-DSG-HEZKÝ STAV-SERVIS',	249000,	2008,	144409,	147,	'Petrol',	1984,	'avatar_6840393c339263.32833512.jpg',	'',	'waiting');

CREATE TABLE `models` (
  `make` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`make`,`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `models` (`make`, `model`) VALUES
('Audi',	'A5'),
('Audi',	'A6'),
('Audi',	'A7'),
('Audi',	'Q7'),
('Audi',	'TT'),
('Corvette',	'C7'),
('Dodge',	'Challenger'),
('Ford',	'Focus'),
('Ford',	'Mondeo'),
('Ford',	'Mustang'),
('Porsche',	'Panamera'),
('Toyota',	'Camry'),
('Toyota',	'Corolla'),
('Toyota',	'Land Cruiser'),
('Toyota',	'Yaris');

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `person_type` enum('private','company') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'private',
  `first_name` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `admin_requested` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `person_type`, `first_name`, `last_name`, `company_name`, `email`, `password`, `admin`, `admin_requested`) VALUES
(33,	'company',	NULL,	NULL,	'Apex Auto Group, a.s.',	'sales@apexautogroup.cz',	'$2y$10$2kUcqW5KXIAaqxMWZDz14e7EckR6EhJObPcKYBcgrFMuZp0JIU0zW',	0,	0);

-- 2025-06-08 11:07:00
