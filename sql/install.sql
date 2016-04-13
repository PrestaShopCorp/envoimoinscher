SET FOREIGN_KEY_CHECKS = 0;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_categories`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_dimensions`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_documents`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_operators`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_orders`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_orders_tmp`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_points`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_services`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_operators_categories`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_tracking`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_cache`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_cart_tmp`;
-- REQUEST --
SET FOREIGN_KEY_CHECKS = 1;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_categories` (
	`id_eca` int(11) NOT NULL,
	`emc_categories_id_eca` int(11) NOT NULL,
	`name_eca` varchar(100) NOT NULL,
	PRIMARY KEY (`id_eca`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
INSERT INTO `{PREFIXE}emc_categories` (`id_eca`, `emc_categories_id_eca`, `name_eca`) VALUES
(10000, 0, "Livres et documents"),
(10100, 10000, "Documents sans valeur commerciale"),
(10120, 10000, "Journaux"),
(10130, 10000, "Magazines, revues"),
(10140, 10000, "Manuels techniques"),
(10150, 10000, "Livres"),
(10160, 10000, "Passeports"),
(10170, 10000, "Billets d\'avion"),
(10180, 10000, "Radiographies"),
(10190, 10000, "Photographies"),
(10200, 10000, "Courrier interne d\'entreprise"),
(10210, 10000, "Propositions commerciales"),
(10220, 10000, "Documents publicitaires"),
(10230, 10000, "Catalogues, rapports annuels"),
(10240, 10000, "Listings informatiques"),
(10250, 10000, "Plans, dessins"),
(10260, 10000, "Documents d\'impression"),
(10280, 10000, "Patrons"),
(10290, 10000, "Etiquettes, autocollants"),
(10300, 10000, "Documents d\'appels d\'offres"),
(20000, 0, "Alimentation et matières périssables"),
(20100, 20000, "Denrées alimentaires non périssables"),
(20102, 20000, "Produits frais et périssables"),
(20103, 20000, "Produits réfrigérés"),
(20105, 20000, "Produits surgelés"),
(20110, 20000, "Boissons non alcoolisées"),
(20120, 20000, "Boissons alcoolisées"),
(20130, 20000, "Plantes, fleurs, semences"),
(30000, 0, "Produits"),
(30100, 30000, "Cosmétiques, bien-être"),
(30200, 30000, "Pharmacie, médicaments"),
(30300, 30000, "Chimie, droguerie, produits d\'entretien"),
(50190, 30000, "Tabac"),
(50200, 30000, "Parfums"),
(40000, 0, "Habillement et accessoires"),
(40100, 40000, "Chaussures"),
(40110, 40000, "Tissus, vêtements neufs"),
(40120, 40000, "Vêtements usagés"),
(40125, 40000, "Accessoires vestimentaires, de mode"),
(40130, 40000, "Cuirs, peaux, maroquinerie"),
(40150, 40000, "Bijoux fantaisie"),
(50160, 40000, "Bijoux, objets précieux"),
(50000, 0, "Appareils et matériels"),
(50100, 50000, "Matériel médical"),
(50110, 50000, "Informatique, High tech, téléphonie fixe"),
(50113, 50000, "Téléphonie mobile et accessoires"),
(50114, 50000, "Téléviseurs, écrans d\'ordinateur"),
(50120, 50000, "Autres appareils et matériels"),
(50130, 50000, "Supports numériques, CD, DVD"),
(50140, 50000, "Pièces de rechange et accessoires (auto)"),
(50150, 50000, "Pièces de rechange et accessoires (autres)"),
(50170, 50000, "Montres, horlogerie (hors bijoux)"),
(50330, 50000, "Articles de camping, de pêche"),
(50350, 50000, "Articles de sport (hors vêtement)"),
(50360, 50000, "Instruments de musique et accessoires"),
(50380, 50000, "Matériel de chauffage, chaudronnerie"),
(50390, 50000, "Matériel de labo, optique, de mesure"),
(50395, 50000, "Matériel électrique, transfo., câbles"),
(50400, 50000, "Fournitures de bureau, papeterie, recharges"),
(50420, 50000, "Moteurs"),
(50430, 50000, "Motos, scooters"),
(50440, 50000, "Vélos, cycles sans moteur"),
(50450, 50000, "Outillage, outils, bricolage"),
(50490, 50000, "Plomberie, tubes plastiques"),
(50500, 50000, "Quincaillerie, robinetterie, serrurerie"),
(60000, 0, "Mobilier et décoration"),
(60100, 60000, "Mobilier d\'habitation"),
(60102, 60000, "Mobilier de bureau"),
(60105, 60000, "Mobilier démonté sous emballage"),
(60108, 60000, "Mobilier ancien (antiquité)"),
(60110, 60000, "Electroménager"),
(60112, 60000, "Petit électroménager, petits appareils ménagers"),
(60120, 60000, "Objets ou tableaux cotés, de collection, miroirs, vitres"),
(60122, 60000, "Objets et tableaux courants"),
(60124, 60000, "Lampes, luminaire"),
(60126, 60000, "Tapis"),
(60128, 60000, "Toiles, rideaux, draps"),
(60129, 60000, "Sanitaires, verres, cristallerie, bibelots"),
(60130, 60000, "Autres objets fragiles et sculptures"),
(70000, 0, "Effets personnels, cadeaux"),
(50180, 70000, "Cadeaux, cadeaux entreprise"),
(70100, 70000, "Bagages, valises, malles"),
(70200, 70000, "Petit déménagement, cartons, effets personnels");
(80000, 0, "Loisirs, produits d'agrément");
(80100, 80000, "Produits culturels : livres, jeux, CD, DVD etc");
(80200, 80000, "Appareils électroniques, Image et son etc");
(80300, 80000, "Bien-être et santé : crèmes, huiles, appareils etc");
(80400, 80000, "Puériculture, jouets, objets pour enfants etc");
(80500, 80000, "Loisirs créatifs, matériaux, art et artisanat etc");
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_dimensions` (
	`id_ed` int(3) NOT NULL AUTO_INCREMENT,
	`length_ed` int(3) NOT NULL,
	`width_ed` int(3) NOT NULL,
	`height_ed` int(3) NOT NULL,
	`weight_from_ed` float NOT NULL,
	`weight_ed` float NOT NULL,
	PRIMARY KEY (`id_ed`)
)  DEFAULT CHARSET=utf8;
-- REQUEST --
INSERT INTO {PREFIXE}emc_dimensions (`id_ed`, `length_ed`, `width_ed`, `height_ed`, `weight_from_ed`, `weight_ed`) VALUES
(1, 18, 18, 18, 0, 1),
(2, 22, 22, 22, 1, 2),
(3, 26, 26, 26, 2, 3),
(4, 28, 28, 28, 3, 4),
(5, 31, 31, 31, 4, 5),
(6, 33, 33, 33, 5, 6),
(7, 34, 34, 34, 6, 7),
(8, 36, 36, 36, 7, 8),
(9, 37, 37, 37, 8, 9),
(10, 39, 39, 39, 9, 10),
(11, 44, 44, 44, 10, 15),
(12, 56, 56, 56, 15, 20),
(13, 57, 57, 57, 20, 50);
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_documents` (
	`id_ed` int(11) NOT NULL AUTO_INCREMENT,
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`{PREFIXE}cart_id_cart` int(10) unsigned NOT NULL,
	`link_ed` varchar(255) NOT NULL,
	`generated_ed` int(1) NOT NULL DEFAULT 0,
	`type_ed` varchar(100) NOT NULL,
	PRIMARY KEY (`id_ed`),
	KEY `{PREFIXE}orders_id_order` (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_operators` (
	`id_eo` int(2) NOT NULL AUTO_INCREMENT,
	`name_eo` varchar(100) NOT NULL,
	`code_eo` char(4) NOT NULL,
	PRIMARY KEY (`id_eo`)
)  DEFAULT CHARSET=utf8;
-- REQUEST --
INSERT INTO `{PREFIXE}emc_operators` (`id_eo`, `name_eo`, `code_eo`) VALUES
(1, "DHL Freight", "DHLF"),
(2, "Distribike", "DTBK"),
(3, "FedEx", "FEDX"),
(4, "Guisnel", "GUIN"),
(5, "Premier Air Courier", "PACO"),
(6, "Sernam", "SERN"),
(7, "Sodexi", "SODX"),
(8, "Relais Colis", "SOGP"),
(9, "TNT Express", "TNTE"),
(10, "UPS", "UPSE"),
(11, "Chronopost", "CHRP"),
(12, "Aramex", "ARAM"),
(13, "Saga Express", "SAGA"),
(14, "Colis Privé", "COPR"),
(15, "Evertrans", "EVER"),
(16, "Kordex", "KODX"),
(17, "Top Chrono", "TOPC"),
(18, "SLS-GCI", "SLSP"),
(19, "Agediss", "AGED"),
(20, "Mondial Relay", "MONR"),
(21, "DHL Express", "DHLE"),
(22, "Low Cost Express", "LOCO"),
(23, "Colis Privé", "COPR"),
(24, "La Poste", "POFR");
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`emc_operators_code_eo` char(4) NOT NULL,
	`price_ht_eor` float NOT NULL,
	`price_ttc_eor` float NOT NULL,
	`ref_emc_eor` char(20) NOT NULL,
	`service_eor` varchar(256) NOT NULL,
	`date_order_eor` datetime NOT NULL,
	`ref_ope_eor` varchar(20) NOT NULL,
	`info_eor` varchar(20) NOT NULL,
	`date_collect_eor` datetime NOT NULL,
	`date_del_eor` datetime NOT NULL,
	`date_del_real_eor` datetime NOT NULL,
	`tracking_eor` CHAR(255) NOT NULL,
	`parcels_eor` INT(4) NOT NULL,
	`base_url_eor` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`),
	KEY `emc_operators_code_eo`(`emc_operators_code_eo`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders_errors` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`errors_eoe` TEXT NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders_parcels` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`number_eop` INT(10) unsigned NOT NULL,
	`weight_eop` DECIMAL(5,2) NOT NULL,
	`length_eop` INT(3) NOT NULL,
	`width_eop` INT(3) NOT NULL,
	`height_eop` INT(3) NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`, `number_eop`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders_plannings` (
	`id_eopl` INT (10) unsigned NOT NULL AUTO_INCREMENT,
	`orders_eopl` TEXT NOT NULL,
	`stats_eopl` VARCHAR(500) NOT NULL,
	`errors_eopl` TEXT NOT NULL,
	`date_eopl` DATETIME NOT NULL,
	`type_eopl` INT(1) NOT NULL,
	PRIMARY KEY (`id_eopl`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders_post` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`data_eopo` TEXT NOT NULL,
	`date_eopo` DATETIME NOT NULL,
	`type`  VARCHAR(20) NULL DEFAULT 'eem',
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders_tmp` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`data_eot` text NOT NULL,
	`date_eot` datetime NOT NULL,
	`errors_eot` text NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_points` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`point_ep` varchar(10) NOT NULL,
	`emc_operators_code_eo` char(4) NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_services` (
  `id_es` int(3) NOT NULL AUTO_INCREMENT,
  `id_carrier` int(11) NOT NULL DEFAULT 0,
  `ref_carrier` int(11) NOT NULL DEFAULT 0,
  `code_es` varchar(128) NOT NULL,
  `emc_operators_code_eo` char(4) NOT NULL,
  `label_es` TEXT NOT NULL,
  `desc_store_es` TEXT NOT NULL,
  `label_store_es` TEXT DEFAULT NULL,
  `srv_name_fo_es` TEXT NOT NULL,
  `family_es` int(1) NOT NULL,
  `zone_fr_es` int(1) NOT NULL,
  `zone_eu_es` int(1) NOT NULL,
  `zone_int_es` int(1) NOT NULL,
  `zone_restriction_es` TEXT DEFAULT NULL,
  `details_es` TEXT DEFAULT NULL,
  `delivery_due_time_es` TEXT NOT NULL,
  `delivery_type_es` int(1) NOT NULL,
  `is_parcel_pickup_point_es` int(1) NOT NULL,
  `is_parcel_dropoff_point_es` int(1) NOT NULL,
  `pickup_place_es` TEXT DEFAULT NULL,
  `dropoff_place_es` TEXT DEFAULT NULL,
  `pricing_es` int(1) NOT NULL,
  PRIMARY KEY (`id_es`),
  KEY `id_carrier` (`id_carrier`),
  KEY `emc_operators_code_eo` (`emc_operators_code_eo`),
  KEY `code_es` (`code_es`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
INSERT INTO `{PREFIXE}emc_services` (`id_es`, `id_carrier`, `ref_carrier`, `code_es`, `emc_operators_code_eo`, `label_es`, `desc_store_es`, `label_store_es`, `srv_name_fo_es`, `family_es`, `zone_fr_es`, `zone_eu_es`, `zone_int_es`, `zone_restriction_es`, `details_es`, `delivery_due_time_es`, `delivery_type_es`, `is_parcel_pickup_point_es`, `is_parcel_dropoff_point_es`, `pickup_place_es`, `dropoff_place_es`, `pricing_es`) VALUES
(1, 0, 0, 'ChronoRelais', 'CHRP', 'Chronopost (Chrono Relais)', 'a:4:{s:5:"en-us";s:35:"Next day delivery to a pickup point";s:5:"it-it";s:48:"Consegna in punto di ritiro il giorno successivo";s:5:"es-es";s:47:"Entrega en puntos de recogida al día siguiente";s:5:"fr-fr";s:26:"Livraison en relais en 24h";}', 'a:4:{s:5:"en-us";s:13:"Chrono Relais";s:5:"it-it";s:13:"Chrono Relais";s:5:"es-es";s:13:"Chrono Relais";s:5:"fr-fr";s:13:"Chrono Relais";}', 'a:4:{s:5:"en-us";s:10:"Chronopost";s:5:"it-it";s:10:"Chronopost";s:5:"es-es";s:10:"Chronopost";s:5:"fr-fr";s:10:"Chronopost";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 20";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 250, L ≤ 100";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 20";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 250, L ≤ 100";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 20";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 250, L ≤ 100";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 20";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 250, L ≤ 100";}}', 'a:4:{s:5:"en-us";s:3:"24h";s:5:"it-it";s:6:"24 ore";s:5:"es-es";s:3:"24h";s:5:"fr-fr";s:3:"24h";}', 3, 1, 2, 'a:4:{s:5:"en-us";s:13:"Chrono Relais";s:5:"it-it";s:13:"Chrono Relais";s:5:"es-es";s:13:"Chrono Relais";s:5:"fr-fr";s:16:"en Chrono Relais";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(2, 0, 0, 'ChronoRelaisEurope', 'CHRP', 'Chronopost (Chrono Relais Europe)', 'a:4:{s:5:"en-us";s:41:"Delivery to a pickup point in 2 to 3 days";s:5:"it-it";s:39:"Consegna in punto di ritiro, da 2 a 3gg";s:5:"es-es";s:46:"Entrega en punto de recogida entre 2 y 3 días";s:5:"fr-fr";s:35:"Livraison en relais en 2 à 3 jours";}', 'a:4:{s:5:"en-us";s:20:"Chrono Relais Europe";s:5:"it-it";s:20:"Chrono Relais Europa";s:5:"es-es";s:20:"Chrono Relais Europa";s:5:"fr-fr";s:20:"Chrono Relais Europe";}', 'a:4:{s:5:"en-us";s:10:"Chronopost";s:5:"it-it";s:10:"Chronopost";s:5:"es-es";s:10:"Chronopost";s:5:"fr-fr";s:10:"Chronopost";}', 1, 0, 1, 0, 'a:4:{s:5:"en-us";s:16:"Germany, Benelux";s:5:"it-it";s:17:"Germania, Benelux";s:5:"es-es";s:17:"Alemania, Benelux";s:5:"fr-fr";s:18:"Allemagne, Benelux";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 20";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 x H ≤ 250, L ≤ 100";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 20";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 250, L ≤ 100";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 20";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 250, L ≤ 100";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 20";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 250, L ≤ 100";}}', 'a:4:{s:5:"en-us";s:11:"2 to 3 days";s:5:"it-it";s:10:"da 2 a 3gg";s:5:"es-es";s:17:"entre 2 y 3 días";s:5:"fr-fr";s:12:"2 à 3 jours";}', 1, 1, 2, 'a:4:{s:5:"en-us";s:13:"Chrono Relais";s:5:"it-it";s:13:"Chrono Relais";s:5:"es-es";s:13:"Chrono Relais";s:5:"fr-fr";s:16:"en Chrono Relais";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(3, 0, 0, 'Chrono13', 'CHRP', 'Chronopost (Chrono13)', 'a:4:{s:5:"en-us";s:37:"Next day home delivery (before 13:00)";s:5:"it-it";s:65:"Consegna a domicilio, il giorno successivo (prima dell''ore 13.00)";s:5:"es-es";s:59:"Entrega a domicilio, al día siguiente (antes de las 13:00)";s:5:"fr-fr";s:40:"Livraison à domicile en 24h (avant 13h)";}', 'a:4:{s:5:"en-us";s:9:"Chrono 13";s:5:"it-it";s:9:"Chrono 13";s:5:"es-es";s:9:"Chrono 13";s:5:"fr-fr";s:9:"Chrono 13";}', 'a:4:{s:5:"en-us";s:10:"Chronopost";s:5:"it-it";s:10:"Chronopost";s:5:"es-es";s:10:"Chronopost";s:5:"fr-fr";s:10:"Chronopost";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 300, L ≤ 140";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 300, L ≤ 140";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 300, L ≤ 140";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 300, L ≤ 140";}}', 'a:4:{s:5:"en-us";s:18:"24h (before 13:00)";s:5:"it-it";s:29:"24 ore (prima dell''ore 13.00)";s:5:"es-es";s:24:"24h (antes de las 13:00)";s:5:"fr-fr";s:15:"24h (avant 13h)";}', 3, 0, 2, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(4, 0, 0, 'Chrono18', 'CHRP', 'Chronopost (Chrono18)', 'a:4:{s:5:"en-us";s:37:"Next day home delivery (before 18:00)";s:5:"it-it";s:65:"Consegna a domicilio, il giorno successivo (prima dell''ore 18.00)";s:5:"es-es";s:59:"Entrega a domicilio, al día siguiente (antes de las 18:00)";s:5:"fr-fr";s:40:"Livraison à domicile en 24h (avant 18h)";}', 'a:4:{s:5:"en-us";s:9:"Chrono 18";s:5:"it-it";s:9:"Chrono 18";s:5:"es-es";s:9:"Chrono 18";s:5:"fr-fr";s:9:"Chrono 18";}', 'a:4:{s:5:"en-us";s:10:"Chronopost";s:5:"it-it";s:10:"Chronopost";s:5:"es-es";s:10:"Chronopost";s:5:"fr-fr";s:10:"Chronopost";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 300, L ≤ 140";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 300, L ≤ 140";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 300, L ≤ 140";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 300, L ≤ 140";}}', 'a:4:{s:5:"en-us";s:18:"24h (before 18:00)";s:5:"it-it";s:29:"24 ore (prima dell''ore 18.00)";s:5:"es-es";s:24:"24h (antes de las 18:00)";s:5:"fr-fr";s:15:"24h (avant 18h)";}', 3, 0, 2, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(5, 0, 0, 'ChronoInternationalClassic', 'CHRP', 'Chronopost (Chrono Classic)', 'a:4:{s:5:"en-us";s:28:"Home delivery in 2 to 4 days";s:5:"it-it";s:32:"Consegna a domicilio, da 2 a 4gg";s:5:"es-es";s:37:"Entrega a domicilio entre 2 y 4 días";s:5:"fr-fr";s:37:"Livraison à domicile en 2 à 4 jours";}', 'a:4:{s:5:"en-us";s:14:"Chrono Classic";s:5:"it-it";s:14:"Chrono Classic";s:5:"es-es";s:14:"Chrono Classic";s:5:"fr-fr";s:14:"Chrono Classic";}', 'a:4:{s:5:"en-us";s:10:"Chronopost";s:5:"it-it";s:10:"Chronopost";s:5:"es-es";s:10:"Chronopost";s:5:"fr-fr";s:10:"Chronopost";}', 1, 0, 1, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 300, L ≤ 140";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 300, L ≤ 140";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 300, L ≤ 140";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 300, L ≤ 140";}}', 'a:4:{s:5:"en-us";s:11:"2 to 4 days";s:5:"it-it";s:10:"da 2 a 4gg";s:5:"es-es";s:17:"entre 2 y 4 días";s:5:"fr-fr";s:12:"2 à 4 jours";}', 2, 0, 2, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(6, 0, 0, 'DomesticExpress', 'DHLE', 'DHL (Domestic Express)', 'a:4:{s:5:"en-us";s:22:"Next day home delivery";s:5:"it-it";s:42:"Consegna a domicilio, il giorno successivo";s:5:"es-es";s:37:"Entrega a domicilio al día siguiente";s:5:"fr-fr";s:28:"Livraison à domicile en 24h";}', 'a:4:{s:5:"en-us";s:8:"Domestic";s:5:"it-it";s:8:"Domestic";s:5:"es-es";s:8:"Domestic";s:5:"fr-fr";s:8:"Domestic";}', 'a:4:{s:5:"en-us";s:11:"DHL Express";s:5:"it-it";s:11:"DHL Express";s:5:"es-es";s:11:"DHL Express";s:5:"fr-fr";s:11:"DHL Express";}', 2, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 65";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 310, L ≤ 115";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 65";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 310, L ≤ 115";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 65";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 310, L ≤ 115";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 65";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 310, L ≤ 115";}}', 'a:4:{s:5:"en-us";s:3:"24h";s:5:"it-it";s:6:"24 ore";s:5:"es-es";s:3:"24h";s:5:"fr-fr";s:3:"24h";}', 3, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(7, 0, 0, 'ExpressWorldwide', 'DHLE', 'DHL (Express Worldwide)', 'a:4:{s:5:"en-us";s:35:"Home delivery within 24 to 72 hours";s:5:"it-it";s:36:"Consegna a domicilio, da 24 a 72 ore";s:5:"es-es";s:36:"Entrega a domicilio, entre 24h y 72h";s:5:"fr-fr";s:35:"Livraison à domicile en 24h à 72h";}', 'a:4:{s:5:"en-us";s:9:"Worldwide";s:5:"it-it";s:9:"Worldwide";s:5:"es-es";s:9:"Worldwide";s:5:"fr-fr";s:9:"Worldwide";}', 'a:4:{s:5:"en-us";s:11:"DHL Express";s:5:"it-it";s:11:"DHL Express";s:5:"es-es";s:11:"DHL Express";s:5:"fr-fr";s:11:"DHL Express";}', 2, 0, 0, 1, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 270, L ≤ 120";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 270, L ≤ 120";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 270, L ≤ 120";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 270, L ≤ 120";}}', 'a:4:{s:5:"en-us";s:14:"24 to 72 hours";s:5:"it-it";s:14:"da 24 a 72 ore";s:5:"es-es";s:15:"entre 24h y 72h";s:5:"fr-fr";s:10:"24h à 72h";}', 3, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(8, 0, 0, 'InternationalEconomy', 'FEDX', 'FedEx (International Economy)', 'a:4:{s:5:"en-us";s:28:"Home delivery in 2 to 6 days";s:5:"it-it";s:56:"Consegna a domicilio da 2 a 6gg (tempi doganali esclusi)";s:5:"es-es";s:37:"Entrega a domicilio entre 2 y 6 días";s:5:"fr-fr";s:37:"Livraison à domicile en 2 à 6 jours";}', 'a:4:{s:5:"en-us";s:21:"International Economy";s:5:"it-it";s:21:"International Economy";s:5:"es-es";s:21:"International Economy";s:5:"fr-fr";s:21:"International Economy";}', 'a:4:{s:5:"en-us";s:5:"FedEx";s:5:"it-it";s:5:"FedEx";s:5:"es-es";s:5:"FedEx";s:5:"fr-fr";s:5:"FedEx";}', 2, 0, 0, 1, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 65";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 330, L ≤ 175";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 65";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 330, L ≤ 175";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 65";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 330, L ≤ 175";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 65";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 330, L ≤ 175";}}', 'a:4:{s:5:"en-us";s:11:"2 to 6 days";s:5:"it-it";s:10:"da 2 a 6gg";s:5:"es-es";s:17:"entre 2 y 6 días";s:5:"fr-fr";s:12:"2 à 6 jours";}', 2, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(9, 0, 0, 'InternationalPriorityCC', 'FEDX', 'FedEx (International Priority)', 'a:4:{s:5:"en-us";s:35:"Home delivery within 24 to 48 hours";s:5:"it-it";s:36:"Consegna a domicilio, da 24 a 48 ore";s:5:"es-es";s:36:"Entrega a domicilio, entre 24h y 48h";s:5:"fr-fr";s:35:"Livraison à domicile en 24h à 48h";}', 'a:4:{s:5:"en-us";s:28:"FedEx International Priority";s:5:"it-it";s:28:"FedEx International Priority";s:5:"es-es";s:28:"FedEx International Priority";s:5:"fr-fr";s:22:"International Priority";}', 'a:4:{s:5:"en-us";s:5:"FedEx";s:5:"it-it";s:5:"FedEx";s:5:"es-es";s:5:"FedEx";s:5:"fr-fr";s:5:"FedEx";}', 2, 0, 0, 1, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 65";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 330, L ≤ 175";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 65";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 330, L ≤ 175";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 65";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 300, L ≤ 175";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 65";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 330, L ≤ 175";}}', 'a:4:{s:5:"en-us";s:14:"24 to 48 hours";s:5:"it-it";s:14:"da 24 a 48 ore";s:5:"es-es";s:15:"entre 24h y 48h";s:5:"fr-fr";s:10:"24h à 48h";}', 3, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(10, 0, 0, 'CpourToi', 'MONR', 'Mondial Relay (C.pourToi®)', 'a:4:{s:5:"en-us";s:41:"Delivery to a pickup point in 3 to 5 days";s:5:"it-it";s:39:"Consegna in punto di ritiro, da 3 a 5gg";s:5:"es-es";s:46:"Entrega en punto de recogida entre 3 y 5 días";s:5:"fr-fr";s:35:"Livraison en relais en 3 à 5 jours";}', 'a:4:{s:5:"en-us";s:11:"C.pourToi®";s:5:"it-it";s:11:"C.pourToi®";s:5:"es-es";s:11:"C.pourToi®";s:5:"fr-fr";s:11:"C.pourToi®";}', 'a:4:{s:5:"en-us";s:13:"Mondial Relay";s:5:"it-it";s:13:"Mondial Relay";s:5:"es-es";s:13:"Mondial Relay";s:5:"fr-fr";s:13:"Mondial Relay";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 150, L ≤ 130";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 150, L ≤ 130";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 150, L ≤ 130";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 150, L ≤ 130";}}', 'a:4:{s:5:"en-us";s:11:"3 to 5 days";s:5:"it-it";s:10:"da 3 a 5gg";s:5:"es-es";s:17:"entre 3 y 5 días";s:5:"fr-fr";s:12:"3 à 5 jours";}', 1, 1, 1, 'a:4:{s:5:"en-us";s:12:"Point Relais";s:5:"it-it";s:12:"Point Relais";s:5:"es-es";s:12:"Point Relais";s:5:"fr-fr";s:15:"en Point Relais";}', 'a:4:{s:5:"en-us";s:12:"Point Relais";s:5:"it-it";s:12:"Point Relais";s:5:"es-es";s:12:"Point Relais";s:5:"fr-fr";s:12:"Point Relais";}', 1),
(11, 0, 0, 'CpourToiEurope', 'MONR', 'Mondial Relay (C.pourToi® - Europe)', 'a:4:{s:5:"en-us";s:41:"Delivery to a pickup point in 3 to 6 days";s:5:"it-it";s:39:"Consegna in punto di ritiro, da 3 a 6gg";s:5:"es-es";s:46:"Entrega en punto de recogida entre 3 y 6 días";s:5:"fr-fr";s:35:"Livraison en relais en 3 à 6 jours";}', 'a:4:{s:5:"en-us";s:20:"C.pourToi® - Europe";s:5:"it-it";s:11:"C.pourToi®";s:5:"es-es";s:11:"C.pourToi®";s:5:"fr-fr";s:20:"C.pourToi® - Europe";}', 'a:4:{s:5:"en-us";s:13:"Mondial Relay";s:5:"it-it";s:13:"Mondial Relay";s:5:"es-es";s:13:"Mondial Relay";s:5:"fr-fr";s:13:"Mondial Relay";}', 1, 0, 1, 0, 'a:4:{s:5:"en-us";s:26:"Belgium, Luxembourg, Spain";s:5:"it-it";s:27:"Belgio, Lussemburgo, Spagna";s:5:"es-es";s:29:"Bélgica, Luxemburgo, España";s:5:"fr-fr";s:29:"Belgique, Luxembourg, Espagne";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 150, L ≤ 130";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 150, L ≤ 130";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 150, L ≤ 130";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 150, L ≤ 130";}}', 'a:4:{s:5:"en-us";s:11:"3 to 6 days";s:5:"it-it";s:10:"da 3 a 6gg";s:5:"es-es";s:17:"entre 3 y 6 días";s:5:"fr-fr";s:12:"3 à 6 jours";}', 1, 1, 1, 'a:4:{s:5:"en-us";s:12:"Point Relais";s:5:"it-it";s:12:"Point Relais";s:5:"es-es";s:12:"Point Relais";s:5:"fr-fr";s:15:"en Point Relais";}', 'a:4:{s:5:"en-us";s:12:"Point Relais";s:5:"it-it";s:12:"Point Relais";s:5:"es-es";s:12:"Point Relais";s:5:"fr-fr";s:12:"Point Relais";}', 1),
(12, 0, 0, 'DomicileEurope', 'MONR', 'Mondial Relay (Domicile Europe)', 'a:4:{s:5:"en-us";s:28:"Home delivery in 3 to 6 days";s:5:"it-it";s:32:"Consegna a domicilio, da 3 a 6gg";s:5:"es-es";s:37:"Entrega a domicilio entre 3 y 6 días";s:5:"fr-fr";s:37:"Livraison à domicile en 3 à 6 jours";}', 'a:4:{s:5:"en-us";s:15:"Domicile Europe";s:5:"it-it";s:16:"Domicilio Europa";s:5:"es-es";s:16:"Domicilio Europa";s:5:"fr-fr";s:15:"Domicile Europe";}', 'a:4:{s:5:"en-us";s:13:"Mondial Relay";s:5:"it-it";s:13:"Mondial Relay";s:5:"es-es";s:13:"Mondial Relay";s:5:"fr-fr";s:13:"Mondial Relay";}', 1, 0, 1, 0, 'a:4:{s:5:"en-us";s:67:"Germany, Belgium, Luxembourg, UK, Spain, Austria, Ireland, Portugal";s:5:"it-it";s:80:"Germania, Belgio, Lussemburgo, Regno Unito, Spagna, Austria, Irlanda, Portogallo";s:5:"es-es";s:90:"Alemania, Bélgica, Luxemburgo, Reino Unido, España, Alemania, Austria, Irlanda, Portugal";s:5:"fr-fr";s:82:"Allemagne, Belgique, Luxembourg, Royaume-Uni, Espagne, Autriche, Irlande, Portugal";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 150, L ≤ 100";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 150, L ≤ 100";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 150, L ≤ 100";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 150, L ≤ 100";}}', 'a:4:{s:5:"en-us";s:11:"3 to 6 days";s:5:"it-it";s:10:"da 3 a 6gg";s:5:"es-es";s:17:"entre 3 y 6 días";s:5:"fr-fr";s:12:"3 à 6 jours";}', 2, 0, 1, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:12:"Point Relais";s:5:"it-it";s:12:"Point Relais";s:5:"es-es";s:12:"Point Relais";s:5:"fr-fr";s:12:"Point Relais";}', 1),
(13, 0, 0, 'ColissimoAccess', 'POFR', 'La Poste (Colissimo Access France)', 'a:4:{s:5:"en-us";s:43:"Home delivery without signature in 48 hours";s:5:"it-it";s:42:"Consegna a domicilio senza firma in 48 ore";s:5:"es-es";s:36:"Entrega a domicilio sin firma en 48h";s:5:"fr-fr";s:43:"Livraison à domicile sans signature en 48h";}', 'a:4:{s:5:"en-us";s:13:"Access France";s:5:"it-it";s:14:"Access Francia";s:5:"es-es";s:14:"Access Francia";s:5:"fr-fr";s:13:"Access France";}', 'a:4:{s:5:"en-us";s:9:"Colissimo";s:5:"it-it";s:9:"Colissimo";s:5:"es-es";s:9:"Colissimo";s:5:"fr-fr";s:9:"Colissimo";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:3:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 150, L ≤ 100";i:2;s:26:"Delivery without signature";}s:5:"it-it";a:3:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 150, L ≤ 100";i:2;s:20:"Consegna senza firma";}s:5:"es-es";a:3:{i:0;s:19:"Peso máximo (kg): ";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 150, L ≤ 100";i:2;s:17:"Entrega sin firma";}s:5:"fr-fr";a:3:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 150, L ≤ 100";i:2;s:21:"Remise sans signature";}}', 'a:4:{s:5:"en-us";s:8:"48 hours";s:5:"it-it";s:6:"48 ore";s:5:"es-es";s:3:"48h";s:5:"fr-fr";s:3:"48h";}', 2, 0, 2, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(14, 0, 0, 'ColissimoExpert', 'POFR', 'La Poste (Colissimo Expert France)', 'a:4:{s:5:"en-us";s:52:"Home delivery with a signature within 24 to 48 hours";s:5:"it-it";s:54:"Consegna a domicilio con necessità di firma in 48 ore";s:5:"es-es";s:37:"Entrega a domicilio bajo firma en 48h";s:5:"fr-fr";s:45:"Livraison à domicile contre signature en 48h";}', 'a:4:{s:5:"en-us";s:13:"Expert France";s:5:"it-it";s:14:"Expert Francia";s:5:"es-es";s:14:"Expert Francia";s:5:"fr-fr";s:13:"Expert France";}', 'a:4:{s:5:"en-us";s:9:"Colissimo";s:5:"it-it";s:9:"Colissimo";s:5:"es-es";s:9:"Colissimo";s:5:"fr-fr";s:9:"Colissimo";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:3:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 150, L ≤ 100";i:2;s:25:"Delivery with a signature";}s:5:"it-it";a:3:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 150, L ≤ 100";i:2;s:32:"Consegna con necessità di firma";}s:5:"es-es";a:3:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 150, L ≤ 100";i:2;s:18:"Entrega bajo firma";}s:5:"fr-fr";a:3:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 150, L ≤ 100";i:2;s:23:"Remise contre signature";}}', 'a:4:{s:5:"en-us";s:8:"48 hours";s:5:"it-it";s:6:"48 ore";s:5:"es-es";s:3:"48h";s:5:"fr-fr";s:3:"48h";}', 2, 0, 2, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:11:"Post Office";s:5:"it-it";s:15:"ufficio postale";s:5:"es-es";s:18:"oficina de correos";s:5:"fr-fr";s:15:"bureau de Poste";}', 1),
(15, 0, 0, 'EconomySelect', 'DHLE', 'DHL Express', 'a:4:{s:5:"en-us";s:28:"Home delivery in 2 to 5 days";s:5:"it-it";s:31:"Consegna a domicilio da 2 a 5gg";s:5:"es-es";s:48:"Entrega a domicilio entre 2 y 5 días, en Europa";s:5:"fr-fr";s:37:"Livraison à domicile en 2 à 5 jours";}', 'a:4:{s:5:"en-us";s:14:"Economy Select";s:5:"it-it";s:14:"Economy Select";s:5:"es-es";s:14:"Economy Select";s:5:"fr-fr";s:14:"Economy Select";}', 'a:4:{s:5:"en-us";s:11:"DHL Express";s:5:"it-it";s:11:"DHL Express";s:5:"es-es";s:11:"DHL Express";s:5:"fr-fr";s:11:"DHL Express";}', 2, 0, 1, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 65";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 270, L ≤ 120";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 65";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 270, L ≤ 120";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 65";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 270, L ≤ 120";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 65";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 270, L ≤ 120";}}', 'a:4:{s:5:"en-us";s:11:"2 to 6 days";s:5:"it-it";s:10:"da 2 a 5gg";s:5:"es-es";s:17:"entre 2 y 5 días";s:5:"fr-fr";s:12:"2 à 5 jours";}', 2, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(16, 0, 0, 'ExpressStandardInterColisMarch', 'SODX', 'Sodexi (Inter Express Standard)', 'a:4:{s:5:"en-us";s:29:"Home delivery in 7 to 10 days";s:5:"it-it";s:33:"Consegna a domicilio, da 7 a 10gg";s:5:"es-es";s:38:"Entrega a domicilio entre 7 y 10 días";s:5:"fr-fr";s:38:"Livraison à domicile en 7 à 10 jours";}', 'a:4:{s:5:"en-us";s:22:"Inter Express Standard";s:5:"it-it";s:22:"Inter Express Standard";s:5:"es-es";s:22:"Inter Express Standard";s:5:"fr-fr";s:22:"Inter Express Standard";}', 'a:4:{s:5:"en-us";s:6:"Sodexi";s:5:"it-it";s:6:"Sodexi";s:5:"es-es";s:6:"Sodexi";s:5:"fr-fr";s:6:"Sodexi";}', 2, 0, 0, 1, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:24:"Maximum weight (kg): 500";i:1;s:61:"Maximum dimensions (cm): L + 2 X W + 2 X H ≤ 300, L ≤ 175";}s:5:"it-it";a:2:{i:0;s:22:"Peso massimo (kg): 500";i:1;s:61:"Dimensioni massime (cm): L + 2 X l + 2 X A ≤ 300, L ≤ 175";}s:5:"es-es";a:2:{i:0;s:22:"Peso máximo (kg): 500";i:1;s:65:"Dimensiones máximas (cm): L + 2 X An + 2 X Al ≤ 300, L ≤ 175";}s:5:"fr-fr";a:2:{i:0;s:24:"Poids maximum (kg) : 500";i:1;s:64:"Dimensions maximales (cm) : L + 2 X l + 2 X h ≤ 300, L ≤ 175";}}', 'a:4:{s:5:"en-us";s:12:"7 to 10 days";s:5:"it-it";s:11:"da 7 a 10gg";s:5:"es-es";s:18:"entre 7 y 10 días";s:5:"fr-fr";s:13:"7 à 10 jours";}', 2, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(17, 0, 0, 'RelaisColis', 'SOGP', 'Relais Colis (Eco)', 'a:4:{s:5:"en-us";s:41:"Delivery to a pickup point in 4 to 6 days";s:5:"it-it";s:39:"Consegna in punto di ritiro, da 4 a 6gg";s:5:"es-es";s:46:"Entrega en punto de recogida entre 4 y 6 días";s:5:"fr-fr";s:35:"Livraison en relais en 4 à 6 jours";}', 'a:4:{s:5:"en-us";s:14:"Relais Colis®";s:5:"it-it";s:14:"Relais Colis®";s:5:"es-es";s:14:"Relais Colis®";s:5:"fr-fr";s:18:"Relais Colis® Eco";}', 'a:4:{s:5:"en-us";s:12:"Relais Colis";s:5:"it-it";s:12:"Relais Colis";s:5:"es-es";s:12:"Relais Colis";s:5:"fr-fr";s:12:"Relais Colis";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 15";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 170, L ≤ 130";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 15";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 170, L ≤ 130";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 15";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 170, L ≤ 130";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 15";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 170, L ≤ 130";}}', 'a:4:{s:5:"en-us";s:11:"4 to 6 days";s:5:"it-it";s:10:"da 4 a 6gg";s:5:"es-es";s:17:"entre 4 y 6 días";s:5:"fr-fr";s:12:"4 à 6 jours";}', 1, 1, 1, 'a:4:{s:5:"en-us";s:12:"Relais Colis";s:5:"it-it";s:12:"Relais Colis";s:5:"es-es";s:12:"Relais Colis";s:5:"fr-fr";s:17:"en Relais Colis®";}', 'a:4:{s:5:"en-us";s:12:"Relais Colis";s:5:"it-it";s:12:"Relais Colis";s:5:"es-es";s:12:"Relais Colis";s:5:"fr-fr";s:14:"Relais Colis®";}', 1),
(18, 0, 0, 'ExpressNational', 'TNTE', 'TNT (13:00 Express)', 'a:4:{s:5:"en-us";s:37:"Next day home delivery (before 13:00)";s:5:"it-it";s:65:"Consegna a domicilio, il giorno successivo (prima dell''ore 13.00)";s:5:"es-es";s:58:"Entrega a domicilio al día siguiente (antes de las 13:00)";s:5:"fr-fr";s:40:"Livraison à domicile en 24h (avant 13h)";}', 'a:4:{s:5:"en-us";s:13:"13:00 Express";s:5:"it-it";s:13:"13:00 Express";s:5:"es-es";s:13:"13:00 Express";s:5:"fr-fr";s:12:"National 13H";}', 'a:4:{s:5:"en-us";s:11:"TNT Express";s:5:"it-it";s:11:"TNT Express";s:5:"es-es";s:11:"TNT Express";s:5:"fr-fr";s:11:"TNT Express";}', 1, 1, 0, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 30";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 170, L ≤ 130";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 30";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 170, L ≤ 130";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 30";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 170, L ≤ 130";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 30";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 170, L ≤ 130";}}', 'a:4:{s:5:"en-us";s:18:"24h (before 13:00)";s:5:"it-it";s:29:"24 ore (prima dell''ore 13.00)";s:5:"es-es";s:24:"24h (antes de las 13:00)";s:5:"fr-fr";s:15:"24h (avant 13h)";}', 3, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(19, 0, 0, 'EconomyExpressInternational', 'TNTE', 'TNT (Economy Express)', 'a:4:{s:5:"en-us";s:28:"Home delivery in 2 to 5 days";s:5:"it-it";s:32:"Consegna a domicilio, da 2 a 5gg";s:5:"es-es";s:37:"Entrega a domicilio entre 2 y 5 días";s:5:"fr-fr";s:37:"Livraison à domicile en 2 à 5 jours";}', 'a:4:{s:5:"en-us";s:7:"Economy";s:5:"it-it";s:7:"Economy";s:5:"es-es";s:7:"Economy";s:5:"fr-fr";s:7:"Economy";}', 'a:4:{s:5:"en-us";s:11:"TNT Express";s:5:"it-it";s:11:"TNT Express";s:5:"es-es";s:11:"TNT Express";s:5:"fr-fr";s:11:"TNT Express";}', 2, 0, 0, 1, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 70";i:1;s:54:"Maximum dimensions (cm): L ≤ 100, W ≤ 70, H ≤ 60";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 70";i:1;s:54:"Dimensioni massime (cm): L ≤ 100, l ≤ 70, A ≤ 60";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 70";i:1;s:58:"Dimensiones máximas (cm): L ≤ 100, An ≤ 70, Al ≤ 60";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 70";i:1;s:57:"Dimensions maximales (cm) : L ≤ 100, l ≤ 70, h ≤ 60";}}', 'a:4:{s:5:"en-us";s:11:"2 to 5 days";s:5:"it-it";s:10:"da 2 a 5gg";s:5:"es-es";s:17:"entre 2 y 5 días";s:5:"fr-fr";s:12:"2 à 5 jours";}', 2, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(20, 0, 0, 'Standard', 'UPSE', 'UPS (Standard)', 'a:4:{s:5:"en-us";s:46:"Home delivery in 24 to 72 hours (before 19:00)";s:5:"it-it";s:59:"Consegna a domicilio, da 24 a 72 ore (prima dell''ore 19.00)";s:5:"es-es";s:56:"Entrega a domicilio entre 24h y 72h (antes de las 19:00)";s:5:"fr-fr";s:47:"Livraison à domicile en 24h à 72h (avant 19h)";}', 'a:4:{s:5:"en-us";s:8:"Standard";s:5:"it-it";s:8:"Standard";s:5:"es-es";s:8:"Standard";s:5:"fr-fr";s:8:"Standard";}', 'a:4:{s:5:"en-us";s:3:"UPS";s:5:"it-it";s:3:"UPS";s:5:"es-es";s:3:"UPS";s:5:"fr-fr";s:3:"UPS";}', 2, 1, 1, 0, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 70";i:1;s:59:"Maximum dimensions (cm): L + 2 X W + 2 X H < 419, L ≤ 270";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 70";i:1;s:59:"Dimensioni massime (cm): L + 2 X l + 2 X A < 419, L ≤ 270";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 70";i:1;s:63:"Dimensiones máximas (cm): L + 2 X An + 2 X Al < 419, L ≤ 270";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 70";i:1;s:62:"Dimensions maximales (cm) : L + 2 X l + 2 X h < 419, L ≤ 270";}}', 'a:4:{s:5:"en-us";s:14:"24 to 72 hours";s:5:"it-it";s:14:"da 24 a 72 ore";s:5:"es-es";s:15:"entre 24h y 72h";s:5:"fr-fr";s:22:"24h à 72h (avant 19h)";}', 3, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(21, 0, 0, 'ExpressSaver', 'UPSE', 'UPS (Express Saver)', 'a:4:{s:5:"en-us";s:28:"Home delivery in 1 to 5 days";s:5:"it-it";s:32:"Consegna a domicilio, da 1 a 5gg";s:5:"es-es";s:37:"Entrega a domicilio entre 1 y 5 días";s:5:"fr-fr";s:37:"Livraison à domicile en 1 à 5 jours";}', 'a:4:{s:5:"en-us";s:13:"Express Saver";s:5:"it-it";s:13:"Express Saver";s:5:"es-es";s:13:"Express Saver";s:5:"fr-fr";s:13:"Express Saver";}', 'a:4:{s:5:"en-us";s:3:"UPS";s:5:"it-it";s:3:"UPS";s:5:"es-es";s:3:"UPS";s:5:"fr-fr";s:3:"UPS";}', 2, 1, 0, 1, 'a:4:{s:5:"en-us";s:0:"";s:5:"it-it";s:0:"";s:5:"es-es";s:0:"";s:5:"fr-fr";s:0:"";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 70";i:1;s:59:"Maximum dimensions (cm): L + 2 X W + 2 X H < 419, L ≤ 270";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 70";i:1;s:59:"Dimensioni massime (cm): L + 2 X l + 2 X A < 419, L ≤ 270";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 70";i:1;s:63:"Dimensiones máximas (cm): L + 2 X An + 2 X Al < 419, L ≤ 270";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 70";i:1;s:62:"Dimensions maximales (cm) : L + 2 X l + 2 X h < 419, L ≤ 270";}}', 'a:4:{s:5:"en-us";s:11:"1 to 5 days";s:5:"it-it";s:10:"da 1 a 5gg";s:5:"es-es";s:17:"entre 1 y 5 días";s:5:"fr-fr";s:12:"1 à 5 jours";}', 3, 0, 0, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(22, 0, 0, 'StandardAP', 'UPSE', 'UPS (Standard Access Point)', 'a:4:{s:5:"en-us";s:59:"Delivery to a pickup point in 24 to 72 hours (before 19:00)";s:5:"it-it";s:66:"Consegna in punto di ritiro, da 24 a 72 ore (prima dell''ore 19.00)";s:5:"es-es";s:62:"Entrega en un Punto Kiala entre 24h y 72h (antes de las 19:00)";s:5:"fr-fr";s:45:"Livraison en relais en 24h à 72h (avant 19h)";}', 'a:4:{s:5:"en-us";s:21:"Standard Access Point";s:5:"it-it";s:20:"Standard Punto Kiala";s:5:"es-es";s:20:"Standard Punto Kiala";s:5:"fr-fr";s:21:"Standard Access Point";}', 'a:4:{s:5:"en-us";s:3:"UPS";s:5:"it-it";s:3:"UPS";s:5:"es-es";s:3:"UPS";s:5:"fr-fr";s:3:"UPS";}', 2, 1, 1, 0, 'a:4:{s:5:"en-us";s:30:"UK, Spain, Belgium, Luxembourg";s:5:"it-it";s:40:"Regno Unito, Spagna, Belgio, Lussemburgo";s:5:"es-es";s:42:"Reino Unido, España, Bélgica, Luxemburgo";s:5:"fr-fr";s:42:"Royaume-Uni, Espagne, Belgique, Luxembourg";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 20";i:1;s:33:"Maximum dimensions (cm): L ≤ 80";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 20";i:1;s:33:"Dimensioni massime (cm): L ≤ 80";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 20";i:1;s:35:"Dimensiones máximas (cm): L ≤ 80";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 20";i:1;s:36:"Dimensions maximales (cm) : L ≤ 80";}}', 'a:4:{s:5:"en-us";s:29:"24 to 72 hours (before 19:00)";s:5:"it-it";s:37:"da 24 a 72 ore (prima dell''ore 19.00)";s:5:"es-es";s:36:"entre 24h y 72h (antes de las 19:00)";s:5:"fr-fr";s:22:"24h à 72h (avant 19h)";}', 3, 1, 0, 'a:4:{s:5:"en-us";s:12:"Access Point";s:5:"it-it";s:11:"Punto Kiala";s:5:"es-es";s:11:"Punto Kiala";s:5:"fr-fr";s:12:"Access Point";}', 'a:4:{s:5:"en-us";s:18:"on-site collection";s:5:"it-it";s:17:"raccolta sul sito";s:5:"es-es";s:19:"recogida en destino";s:5:"fr-fr";s:20:"enlèvement sur site";}', 1),
(23, 0, 0, 'PackSuiviEurope', 'IMXE', 'Happy Post (PackSuiviEurope)', 'a:4:{s:5:"en-us";s:28:"Home delivery in 3 to 9 days";s:5:"it-it";s:32:"Consegna a domicilio, da 3 a 9gg";s:5:"es-es";s:37:"Entrega a domicilio entre 3 y 9 días";s:5:"fr-fr";s:37:"Livraison à domicile en 3 à 9 jours";}', 'a:4:{s:5:"en-us";s:17:"Pack suivi Europe";s:5:"it-it";s:29:"Pacchetto monitoraggio Europa";s:5:"es-es";s:29:"Seguimiento de paquete Europa";s:5:"fr-fr";s:15:"PackSuiviEurope";}', 'a:4:{s:5:"en-us";s:10:"Happy-Post";s:5:"it-it";s:10:"Happy-Post";s:5:"es-es";s:10:"Happy-Post";s:5:"fr-fr";s:10:"Happy-Post";}', 1, 0, 1, 0, 'a:4:{s:5:"en-us";s:48:"Germany (4kg), Italy (3kg), United Kingdom (5kg)";s:5:"it-it";s:47:"Germania (4kg), Italia (3kg), Regno Unito (5kg)";s:5:"es-es";s:47:"Alemania (4kg), Italia (3kg), Reino Unido (5kg)";s:5:"fr-fr";s:48:"Allemagne (4kg), Italie (3kg), Royaume-Uni (5kg)";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:22:"Maximum weight (kg): 5";i:1;s:53:"Maximum dimensions (cm): L + W + H ≤ 180, L ≤ 120";}s:5:"it-it";a:2:{i:0;s:20:"Peso massimo (kg): 5";i:1;s:53:"Dimensioni massime (cm): L + l + A ≤ 180, L ≤ 120";}s:5:"es-es";a:2:{i:0;s:20:"Peso máximo (kg): 5";i:1;s:57:"Dimensiones máximas (cm): L + An + Al ≤ 180, L ≤ 120";}s:5:"fr-fr";a:2:{i:0;s:22:"Poids maximum (kg) : 5";i:1;s:56:"Dimensions maximales (cm) : L + l + h ≤ 180, L ≤ 120";}}', 'a:4:{s:5:"en-us";s:11:"3 to 9 days";s:5:"it-it";s:10:"da 3 a 9gg";s:5:"es-es";s:17:"entre 3 y 9 días";s:5:"fr-fr";s:12:"3 à 9 jours";}', 2, 0, 1, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:12:"Point Relais";s:5:"it-it";s:12:"Point Relais";s:5:"es-es";s:12:"Point Relais";s:5:"fr-fr";s:12:"Point Relais";}', 1),
(24, 0, 0, 'StandardDepot', 'UPSE', 'UPS (Standard Dépôt)', 'a:4:{s:5:"en-us";s:46:"Home delivery in 24 to 72 hours (before 19:00)";s:5:"it-it";s:60:"Consegna a domicilio, da 24 a 72 ore (prima delle ore 19.00)";s:5:"es-es";s:56:"Entrega a domicilio entre 24h y 72h (antes de las 19:00)";s:5:"fr-fr";s:47:"Livraison à domicile en 24h à 72h (avant 19h)";}', 'a:4:{s:5:"en-us";s:16:"Standard Dépôt";s:5:"it-it";s:16:"Standard Dépôt";s:5:"es-es";s:16:"Standard Dépôt";s:5:"fr-fr";s:16:"Standard Dépôt";}', 'a:4:{s:5:"en-us";s:3:"UPS";s:5:"it-it";s:3:"UPS";s:5:"es-es";s:3:"UPS";s:5:"fr-fr";s:3:"UPS";}', 2, 1, 1, 0, 'a:4:{s:5:"en-us";s:62:"Belgium, Germany, Italy, Luxembourg, Netherlands, Spain and UK";s:5:"it-it";s:72:"Belgio, Germania, Italia, Lussemburgo, Paesi Bassi, Regno Unito e Spagna";s:5:"es-es";s:77:"Alemania, Bélgica, España, Italia, Luxembourgo, Países Bajos y Reino Unido";s:5:"fr-fr";s:73:"Allemagne, Belgique, Espagne, Italie, Luxembourg, Pays-Bas et Royaume-Uni";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 20";i:1;s:33:"Maximum dimensions (cm): L ≤ 80";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 20";i:1;s:33:"Dimensioni massime (cm): L ≤ 80";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 20";i:1;s:35:"Dimensiones máximas (cm): L ≤ 80";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 20";i:1;s:36:"Dimensions maximales (cm) : L ≤ 80";}}', 'a:4:{s:5:"en-us";s:29:"24 to 72 hours (before 19:00)";s:5:"it-it";s:38:"da 24 a 72 ore (prima delle ore 19.00)";s:5:"es-es";s:22:"24h à 72h (avant 19h)";s:5:"fr-fr";s:22:"24h à 72h (avant 19h)";}', 3, 0, 1, 'a:4:{s:5:"en-us";s:4:"home";s:5:"it-it";s:9:"domicilio";s:5:"es-es";s:9:"domicilio";s:5:"fr-fr";s:11:"à domicile";}', 'a:4:{s:5:"en-us";s:12:"Access Point";s:5:"it-it";s:12:"Access Point";s:5:"es-es";s:12:"Access Point";s:5:"fr-fr";s:12:"Access Point";}', 1);
(25, 0, 0, 'StandardAPDepot', 'UPSE', 'UPS (Standard Access Point Dépôt)', 'a:4:{s:5:"en-us";s:59:"Delivery to a pickup point in 24 to 72 hours (before 19:00)";s:5:"it-it";s:68:"Consegna in puntu de ritirio, da 24 a 72 ore (prima delle ore 19.00)";s:5:"es-es";s:69:"Entrega en un punto Access Point entre 24h y 72h (antes de las 19:00)";s:5:"fr-fr";s:45:"Livraison en relais en 24h à 72h (avant 19h)";}', 'a:4:{s:5:"en-us";s:29:"Standard Access Point Dépôt";s:5:"it-it";s:29:"Standard Access Point Dépôt";s:5:"es-es";s:29:"Standard Access Point Dépôt";s:5:"fr-fr";s:29:"Standard Access Point Dépôt";}', 'a:4:{s:5:"en-us";s:3:"UPS";s:5:"it-it";s:3:"UPS";s:5:"es-es";s:3:"UPS";s:5:"fr-fr";s:3:"UPS";}', 2, 1, 1, 0, 'a:4:{s:5:"en-us";s:62:"Belgium, Germany, Italy, Luxembourg, Netherlands, Spain and UK";s:5:"it-it";s:72:"Belgio, Germania, Italia, Lussemburgo, Paesi Bassi, Regno Unito e Spagna";s:5:"es-es";s:77:"Alemania, Bélgica, España, Italia, Luxembourgo, Países Bajos y Reino Unido";s:5:"fr-fr";s:73:"Allemagne, Belgique, Espagne, Italie, Luxembourg, Pays-Bas et Royaume-Uni";}', 'a:4:{s:5:"en-us";a:2:{i:0;s:23:"Maximum weight (kg): 21";i:1;s:33:"Maximum dimensions (cm): L ≤ 80";}s:5:"it-it";a:2:{i:0;s:21:"Peso massimo (kg): 21";i:1;s:33:"Dimensioni massime (cm): L ≤ 80";}s:5:"es-es";a:2:{i:0;s:21:"Peso máximo (kg): 21";i:1;s:35:"Dimensiones máximas (cm): L ≤ 80";}s:5:"fr-fr";a:2:{i:0;s:23:"Poids maximum (kg) : 21";i:1;s:36:"Dimensions maximales (cm) : L ≤ 80";}}', 'a:4:{s:5:"en-us";s:29:"24 to 72 hours (before 19:00)";s:5:"it-it";s:38:"da 24 a 72 ore (prima delle ore 19.00)";s:5:"es-es";s:36:"entre 24h y 72h (antes de las 19:00)";s:5:"fr-fr";s:22:"24h à 72h (avant 19h)";}', 3, 1, 1, 'a:4:{s:5:"en-us";s:12:"Access Point";s:5:"it-it";s:12:"Access Point";s:5:"es-es";s:12:"Access Point";s:5:"fr-fr";s:12:"Access Point";}', 'a:4:{s:5:"en-us";s:12:"Access Point";s:5:"it-it";s:12:"Access Point";s:5:"es-es";s:12:"Access Point";s:5:"fr-fr";s:12:"Access Point";}', 1),

-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_operators_categories` (
	`id_eoca` int(11) NOT NULL AUTO_INCREMENT,
	`id_eo` int(11) NOT NULL,
	`id_eca` int(11) NOT NULL,
	PRIMARY KEY (`id_eoca`),
	UNIQUE KEY `id_eo` (`id_eo`, `id_eca`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
INSERT INTO `{PREFIXE}emc_operators_categories` (id_eo, id_eca) VALUES
(11, 10300), (11, 20102), (11, 20103), (11, 20105), (11, 20130), (11, 30200), (11, 30300),
(11, 50114), (11, 50160), (11, 50190), (11, 50200), (11, 50420), (11, 60100), (11, 60102),
(11, 60108), (11, 60110), (11, 60120), (11, 70100), (8, 10160), (8, 10170), (8, 10300),
(8, 20100), (8, 20102), (8, 20103), (8, 20105), (8, 20110), (8, 20120), (8, 20130), (8, 30200),
(8, 30300), (8, 50114), (8, 50160), (8, 50190), (8, 50200), (8, 50430), (8, 60100), (8, 60102),
(8, 60108), (8, 60110), (8, 60120), (8, 60129), (8, 60130), (8, 70100), (20, 10300), (20, 20102),
(20, 20103), (20, 20105), (20, 20120), (20, 20130), (20, 30200), (20, 30300), (20, 50114), (20, 50160),
(20, 50190), (20, 50200), (20, 60100), (20, 60102), (20, 60108), (20, 60110), (20, 60120), (20, 60130),
(20, 70100), (20, 70200), (23, 10160), (23, 10170), (23, 10300), (23, 20102), (23, 20103), (23, 20105),
(23, 20130), (23, 30200), (23, 30300), (23, 50114), (23, 50160), (23, 50190), (23, 50200), (23, 50430),
(23, 60100), (23, 60102), (23, 60108), (23, 60110), (23, 60120), (23, 70100), (21, 10300), (21, 20100),
(21, 20102), (21, 20103), (21, 20105), (21, 20110), (21, 20120), (21, 20130), (21, 30200), (21, 30300),
(21, 50114), (21, 50160), (21, 50190), (21, 50200), (21, 50420), (21, 50430), (21, 60100), (21, 60102),
(21, 60108), (21, 60110), (21, 60120), (21, 60124), (21, 60129), (21, 60130), (21, 70100), (21, 70200),
(3, 10300), (3, 20102), (3, 20103), (3, 20105), (3, 20130), (3, 30200), (3, 30300), (3, 50114),
(3, 50160), (3, 50190), (3, 50200), (3, 60100), (3, 60102), (3, 60108), (3, 60110), (3, 60120),
(3, 70100), (3, 70200), (22, 10300), (22, 20102), (22, 20103), (22, 20105), (22, 20130), (22, 30200),
(22, 30300), (22, 50114), (22, 50160), (22, 50190), (22, 50200), (22, 50420), (22, 60100), (22, 60102),
(22, 60108), (22, 60110), (22, 60120), (22, 70100), (7, 10160), (7, 10170), (7, 10300), (7, 20102),
(7, 20103), (7, 20105), (7, 20120), (7, 20130), (7, 30200), (7, 30300), (7, 40120), (7, 50114),
(7, 50160), (7, 50190), (7, 50200), (7, 50430), (7, 60100), (7, 60102), (7, 60108), (7, 60110),
(7, 60120), (7, 60129), (7, 60130), (9, 10160), (9, 20102), (9, 20103), (9, 20105), (9, 20120),
(9, 20130), (9, 30200), (9, 30300), (9, 50114), (9, 50160), (9, 50190), (9, 50200), (9, 50430),
(9, 60100), (9, 60102), (9, 60108), (9, 60110),  (9, 60120),  (9, 60130), (9, 70100), (10, 10160),
(10, 20102), (10, 20103), (10, 20105), (10, 20110), (10, 20120), (10, 20130), (10, 30200), (10, 30300),
(10, 50100), (10, 50113), (10, 50114), (10, 50120), (10, 50160), (10, 50190), (10, 50200), (10, 50360),
(10, 50390), (10, 50420), (10, 50430), (10, 50450), (10, 60100), (10, 60102), (10, 60108), (10, 60110), 
(10, 60120), (10, 60124), (10, 60129), (10, 60130), (10, 70100), (10, 70200), (25, 60130);
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_tracking` (
	`id_et` int(11) NOT NULL AUTO_INCREMENT,
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`state_et` char(4) NOT NULL,
	`date_et` datetime NOT NULL,
	`text_et` text NOT NULL,
	`localisation_et` varchar(50) NOT NULL,
	PRIMARY KEY (`id_et`),
	KEY `{PREFIXE}orders_id_order` (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_cache` (
	`cache_key` VARCHAR(255) NOT NULL,
	`cache_data` longtext NOT NULL,
	`expiration_date` DATETIME NOT NULL,
	PRIMARY KEY (`cache_key`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_cart_tmp` (
	`id_cart` int(10) NOT NULL,
	`selected_point` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id_cart`)
) DEFAULT CHARSET=utf8;