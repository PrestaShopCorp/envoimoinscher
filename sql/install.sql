DROP TABLE IF EXISTS `{PREFIXE}emc_categories`;
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
(60110, 60000, "Electroménager "),
(60112, 60000, "Petit électroménager, petits appareils ménagers"),
(60120, 60000, "Objets ou tableaux cotés, de collection, miroirs, vitres"),
(60122, 60000, "Objets et tableaux courants "),
(60124, 60000, "Lampes, luminaire"),
(60126, 60000, "Tapis"),
(60128, 60000, "Toiles, rideaux, draps"),
(60129, 60000, "Sanitaires, verres, cristallerie, bibelots"),
(60130, 60000, "Autres objets fragiles et sculptures"),
(70000, 0, "Effets personnels, cadeaux"),
(50180, 70000, "Cadeaux, cadeaux entreprise"),
(70100, 70000, "Bagages, valises, malles"),
(70200, 70000, "Petit déménagement, cartons, effets personnels");
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_dimensions`;
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
DROP TABLE IF EXISTS `{PREFIXE}emc_documents`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_documents` (
	`id_ed` int(11) NOT NULL AUTO_INCREMENT,
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`{PREFIXE}cart_id_cart` int(10) unsigned NOT NULL,
	`link_ed` varchar(255) NOT NULL,
	`generated_ed` int(1) NOT NULL DEFAULT 0,
	`type_ed` enum("label","proforma") NOT NULL,
	PRIMARY KEY (`id_ed`),
	KEY `{PREFIXE}orders_id_order` (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_operators`;
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
(9, "TNT", "TNTE"),
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
DROP TABLE IF EXISTS `{PREFIXE}emc_orders`;
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
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_orders_tmp`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_orders_tmp` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`data_eot` text NOT NULL,
	`date_eot` datetime NOT NULL,
	`errors_eot` text NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_points`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_points` (
	`{PREFIXE}orders_id_order` int(10) unsigned NOT NULL,
	`point_ep` varchar(10) NOT NULL,
	`emc_operators_code_eo` char(4) NOT NULL,
	PRIMARY KEY (`{PREFIXE}orders_id_order`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_services`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_services` (
	`id_es` int(3) NOT NULL AUTO_INCREMENT,
	`id_carrier` int(11) NOT NULL DEFAULT 0,
	`ref_carrier` int(11) NOT NULL DEFAULT 0,
	`code_es` varchar(40) NOT NULL,
	`emc_operators_code_eo` char(4) NOT NULL,
	`label_es` varchar(100) NOT NULL,
	`desc_es` varchar(150) NOT NULL,
	`desc_store_es` varchar(150) NOT NULL,
	`label_store_es` varchar(100) NOT NULL,
	`price_type_es` int(1) NOT NULL,
	`is_parcel_pickup_point_es` int(1) NOT NULL,
	`is_parcel_dropoff_point_es` int(1) NOT NULL,
	`family_es` int(1) NOT NULL,
	`type_es` int(1) NOT NULL,
	`pricing_es` int(1) NOT NULL,
	PRIMARY KEY (`id_es`),
	KEY (`id_carrier`),
	KEY `emc_operators_code_eo` (`emc_operators_code_eo`),
	KEY `code_es` (`code_es`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
INSERT INTO `{PREFIXE}emc_services` (`id_es`, `code_es`, `emc_operators_code_eo`, `label_es`, `desc_es`, `desc_store_es`, `label_store_es`, `price_type_es`, `is_parcel_pickup_point_es`, `is_parcel_dropoff_point_es`, `family_es`, `type_es`, `pricing_es`) VALUES
(1, "RelaisColis", "SOGP", "Relais Colis eco", "Dépôt en Relais Colis - Livraison en Relais Colis en 10 jours, en France", "Livraison en Relais Colis en 10 jours", "Relais Colis®", 0, 1, 1, 1, 1, 1),
(2, "Standard", "UPSE", "UPS Standard", "Livraison à domicile en 24h à 72h (avant 19h), en France et dans les pays européens", "Livraison à domicile en 24h à 72h (avant 19h)", "UPS Standard", 0, 0, 0, 2, 2, 1),
(3, "ExpressSaver", "UPSE", "UPS Express Saver", "Livraison à domicile en 72h  (avant 19h), à l\'international  (hors délai de douanes)", "Livraison à domicile en 72h (avant 19h, hors délai de douanes)", "UPS Express Saver", 0, 0, 0, 2, 2, 1),
(4, "InternationalEconomy", "FEDX", "FedEx International Economy", "Livraison à domicile en 5 jours à l\'international (hors délai de douanes)", "Livraison à domicile en 5 jours (hors délai de douanes)", "FedEx International Economy", 0, 0, 0, 2, 2, 1),
(5, "InternationalPriority", "FEDX", "FedEx International Priority", "Livraison express à domicile, en 24h à 48h (hors délai de douanes)", "Livraison express à domicile en 24h à 48h (hors délai de douanes)", "FedEx International Priority", 0, 0, 0, 2, 2, 1),
(6, "ExpressNational", "TNTE", "13:00 Express", "Livraison express à domicile le lendemain (avant 13h), en France", "Livraison express à domicile le lendemain (avant 13h)", "13:00 Express", 1, 0, 0, 2, 2, 1),
(7, "Chrono13", "CHRP", "Chrono13", "Dépôt en bureau de poste - Livraison express à domicile, le lendemain (avant 13h), en France.Dépôt en bureau de poste si la livraison rate.", "Livraison express à domicile, le lendemain (avant 13h). Si la livraison rate, dépôt en bureau de poste", "Chrono13", 0, 0, 0, 1, 1, 1),
(8, "ChronoInternationalClassic", "CHRP", "Chrono Classic", "Dépôt en bureau de poste - Livraison à domicile en 2 à 4 jours, à l\'international (hors délai de douanes)", "Livraison à domicile en 2 à 4 jours (hors délai de douanes)", "Chrono Classic", 0, 0, 0, 1, 1, 1),
(9, "ExpressStandard", "SODX", "Express Standard", "Livraison à domicile en 2 à 3 jours, en France", "Livraison à domicile en 2 à 3 jours", "Express Standard", 0, 0, 0, 2, 2, 1),
(10, "ExpressStandardInterColisMarch", "SODX", "Inter Express Standard", "Livraison à domicile en 7 à 10 jours, à l\'international (hors délai de douanes)", "Livraison à domicile en 7 à 10 jours (hors délai de douanes)", "Inter Express Standard", 0, 0, 0, 2, 2, 1),
(11, "ExpressStandardInterPlisDSVC", "SODX", "Inter Express Standard doc", "Livraison à domicile en 7 à 10 jours, à l\'international (hors délai de douanes)", "Livraison à domicile en 7 à 10 jours (hors délai de douanes)", "Inter Express Standard doc", 0, 0, 0, 2, 2, 1),
(12, "CpourToi", "MONR", "C.pourToi®", "Dépôt en point relais - Livraison en point relais en 3 à 5 jours, en France", "Livraison en point relais en 3 à 5 jours", "C.pourToi®", 0, 1, 1, 1, 1, 1),
(13, "CpourToiEurope", "MONR", "C.pourToi® - Europe", "Dépôt en point relais - Livraison en point relais en 4 à 6 jours, dans certains pays d\'Europe", "Livraison en point relais en 4 à 6 jours", "C.pourToi®", 0, 1, 1, 1, 1, 1),
(14, "ExpressWorldwide", "DHLE", "DHL Express Worldwide", "Livraison express à domicile en 24h à 72h, à l\'international (hors délai de douanes)", "Livraison express à domicile en 24h à 72h (hors délai de douanes)", "DHL Express Worldwide", 0, 0, 0, 2, 2, 1),
(15, "EconomyExpressInternational", "TNTE", "Economy Express", "Livraison à domicile en 2 à 5 jours, à l\'international (hors délai de douanes)", "Livraison à domicile en 2 à 5 jours (hors délai de douanes)", "Economy Express", 0, 0, 0, 2, 2, 1),
(16, "DepotexpressEurope", "LOCO", "Dépôt Express Europe", "Dépôt en bureau de poste - Livraison à domicile en 2 à 4 jours, en Europe (hors délai de douanes)", "Livraison à domicile en 2 à 4 jours (hors délai de douanes)", "Dépôt Express Europe", 0, 0, 0, 2, 2, 1),
(17, "Depotexpress", "LOCO", "Dépôt Express", "Dépôt en bureau de poste - Livraison express à domicile, le lendemain (avant 13h), en France. Dépôt en bureau de poste si la livraison rate.", "Livraison express à domicile, le lendemain (avant 13h). Si la livraison rate, dépôt en bureau de poste", "Dépôt Express", 0, 0, 0, 2, 2, 1),
(18, "ChronoRelais", "CHRP", "Chrono Relais", "Livraison en points relais Chronopost", "Livraison en points relais Chronopost", "Chrono Relais", 0, 1, 0, 1, 1, 1),
(19, "ExpressInternationalColis", "TNTE", "Express International", "Livraison à domicile en 1 à 7 jours, à l\'international (hors délai de douanes)", "Livraison à domicile en 1 à 7 jours (hors délai de douanes)", "Express International", 0, 0, 0, 2, 2, 1),
(20, "EASY", "COPR", "Colis Privé EASY", "Livraison à domicile en 2 à 3 jours. En cas d\'absence, 2nde présentation ou dépôt en relais. <b>Offre sous conditions de volume.</b>", "Livraison à domicile en 2 à 3 jours. En cas d\'absence, 2nde présentation ou dépôt en relais Kiala", "Colis Privé EASY", 0, 0, 0, 1, 1, 1),
(21, "Chrono18", "CHRP", "Chrono18", "Dépôt en bureau de poste - Livraison express à domicile, le lendemain (avant 18h), en France. Dépôt en bureau de poste si la livraison rate.", "Livraison express à domicile, le lendemain (avant 18h). Si la livraison rate, dépôt en bureau de poste", "Chrono18", 0, 0, 0, 1, 1, 1),
(22, "ColissimoAccess", "POFR", "La Poste Colissimo Access France", "Délai indicatif de 48h en jours ouvrables pour les envois en France métropolitaine. Remise sans signature.", "Livraison à domicile en 48h", "La Poste Colissimo Access France. Remise sans signature.", 0, 0, 0, 1, 1, 1),
(23, "ColissimoExpert", "POFR", "La Poste Colissimo Expert France", "Délai indicatif de 48h en jours ouvrables pour les envois en France métropolitaine. Remise contre signature.", "Livraison à domicile en 48h", "La Poste Colissimo Expert France. Remise contre signature.", 0, 0, 0, 1, 1, 1),
(25, 'DomicileEurope', 'MONR', 'Domicile Europe', 'Livraison à domicile en 4 à 6 jours, dans certains pays d\'Europe', 'Livraison à domicile en 4 à 6 jours', 'Mondial Relay Domicile Europe', 0, 0, 1, 2, 2, 1),
(26,	'StandardAP',	'UPSE',	'UPS Standard Access Point',	'Livraison en point relais en 24h à 72h (avant 19h), en France et dans les pays européens',	'Livraison en point relais en 24h à 72h (avant 19h)',	'UPS Standard Access Point',	0,	1,	0,	2,	2,	1);

;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_operators_categories`;
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
(8, 60108), (8, 60110), (8, 60120), (8, 60129), (8, 70100), (20, 10300), (20, 20102), (20, 20103),
(20, 20105), (20, 20130), (20, 30200), (20, 30300), (20, 50114), (20, 50160), (20, 50190), (20, 50200),
(20, 60100), (20, 60102), (20, 60108), (20, 60110), (20, 60120), (20, 70100), (20, 70200), (23, 10160),
(23, 10170), (23, 10300), (23, 20102), (23, 20103), (23, 20105), (23, 20130), (23, 30200), (23, 30300),
(23, 50114), (23, 50160), (23, 50190), (23, 50200), (23, 50430), (23, 60100), (23, 60102), (23, 60108),
(23, 60110), (23, 60120), (23, 70100), (21, 10300), (21, 20100), (21, 20102), (21, 20103), (21, 20105),
(21, 20110), (21, 20120), (21, 20130), (21, 30200), (21, 30300), (21, 50114), (21, 50160), (21, 50190),
(21, 50200), (21, 50420), (21, 50430), (21, 60100), (21, 60102), (21, 60108), (21, 60110), (21, 60120),
(21, 60124), (21, 60129), (21, 60130), (21, 70100), (21, 70200), (3, 10300), (3, 20102), (3, 20103),
(3, 20105), (3, 20130), (3, 30200), (3, 30300), (3, 50114), (3, 50160), (3, 50190), (3, 50200),
(3, 60100), (3, 60102), (3, 60108), (3, 60110), (3, 60120), (3, 70100), (3, 70200), (22, 10300),
(22, 20102), (22, 20103), (22, 20105), (22, 20130), (22, 30200), (22, 30300), (22, 50114),
(22, 50160), (22, 50190), (22, 50200), (22, 50420), (22, 60100), (22, 60102), (22, 60108),
(22, 60110), (22, 60120), (22, 70100), (7, 10160), (7, 10170), (7, 10300), (7, 20102), (7, 20103),
(7, 20105), (7, 20120), (7, 20130), (7, 30200), (7, 30300), (7, 40120), (7, 50114), (7, 50160),
(7, 50190), (7, 50200), (7, 50430), (7, 60100), (7, 60102), (7, 60108), (7, 60110), (7, 60120),
(7, 60129), (7, 60130), (9, 10160), (9, 20102), (9, 20103), (9, 20105), (9, 20120), (9, 20130),
(9, 30200), (9, 30300), (9, 50114), (9, 50160), (9, 50190), (9, 50200), (9, 50430), (9, 60100),
(9, 60102), (9, 60108), (9, 60110),  (9, 60120), (9, 70100), (10, 10160), (10, 20102), (10, 20103),
(10, 20105), (10, 20110), (10, 20120), (10, 20130), (10, 30200), (10, 30300), (10, 50100), (10, 50110),
(10, 50113), (10, 50114), (10, 50120), (10, 50160), (10, 50190), (10, 50200), (10, 50360), (10, 50390),
(10, 50420), (10, 50430), (10, 50450), (10, 60100), (10, 60102), (10, 60108), (10, 60110),  (10, 60120),
(10, 60124), (10, 60129), (10, 60130), (10, 70100), (10, 70200);
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_tracking`;
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
DROP TABLE IF EXISTS `{PREFIXE}emc_api_pricing`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_api_pricing` (
	`id_ap` VARCHAR(255) NOT NULL,
	`{PREFIXE}cart_id_cart` int(10) unsigned NOT NULL,
	`prices_eap` text NOT NULL,
	`price_eap` float NOT NULL DEFAULT 0,
	`date_eap` DATETIME NOT NULL,
	`carriers_eap` TEXT NOT NULL,
	`treated_eap` TEXT,
	`free_shipping_eap` INT(1) NOT NULL COMMENT "0 - no, 1 - yes",
	`point_eap` VARCHAR( 40 ),
	`order_done_eap` INT(1) NOT NULL COMMENT "0 - not done, 1 - done" DEFAULT 0,
	`points_eap` TEXT NOT NULL,
	`date_delivery` TEXT NOT NULL,
	PRIMARY KEY (`id_ap`)
) DEFAULT CHARSET=utf8;

