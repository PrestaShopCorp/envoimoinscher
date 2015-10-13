DROP TABLE IF EXISTS `{PREFIXE}emc_api_pricing`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_api_pricing` (
		`id_ap` VARCHAR(255) NOT NULL,
		`ps_cart_id_cart` int(10) unsigned NOT NULL,
		`prices_eap` text NOT NULL,
		`price_eap` float NOT NULL DEFAULT 0,
		`date_eap` DATETIME NOT NULL,
		`carriers_eap` TEXT NOT NULL,
		`treated_eap` TEXT,
		`free_shipping_eap` INT(1) NOT NULL COMMENT '0 - no, 1 - yes',
		`point_eap` VARCHAR( 40 ),
		`order_done_eap` INT(1) NOT NULL COMMENT '0 - not done, 1 - done' DEFAULT 0,
		`points_eap` TEXT NOT NULL,
		`date_delivery` TEXT NOT NULL,
		PRIMARY KEY (`id_ap`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_services` MODIFY COLUMN `id_carrier` int(11) NOT NULL DEFAULT 0;
-- REQUEST --