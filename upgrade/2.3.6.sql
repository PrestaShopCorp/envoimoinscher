ALTER TABLE `{PREFIXE}emc_documents` ADD COLUMN `{PREFIXE}cart_id_cart` int(10) unsigned NOT NULL;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_orders` ADD COLUMN `base_url_eor` VARCHAR(255) NOT NULL;