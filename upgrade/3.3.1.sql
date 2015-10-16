-- REQUEST --
USE `{DBNAME}`;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_orders_post` ADD `type` VARCHAR(20) NULL DEFAULT 'eem' AFTER `data_eopo`;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_orders_post` DROP PRIMARY KEY, ADD PRIMARY KEY( `{PREFIXE}orders_id_order`, `type`);
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_api_pricing`;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_cache`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_cache` (
	`cache_key` VARCHAR(255) NOT NULL,
	`cache_data` longtext NOT NULL,
	`expiration_date` DATETIME NOT NULL,
	PRIMARY KEY (`cache_key`)
) DEFAULT CHARSET=utf8;
-- REQUEST --
DROP TABLE IF EXISTS `{PREFIXE}emc_cart_tmp`;
-- REQUEST --
CREATE TABLE IF NOT EXISTS `{PREFIXE}emc_cart_tmp` (
	`id_cart` int(10) NOT NULL,
	`selected_point` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id_cart`)
) DEFAULT CHARSET=utf8;