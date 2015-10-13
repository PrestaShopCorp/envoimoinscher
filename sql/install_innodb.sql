ALTER TABLE `{PREFIXE}emc_documents`
ADD CONSTRAINT `emc_documents_ibfk_1` FOREIGN KEY (`{PREFIXE}orders_id_order`)
REFERENCES `{PREFIXE}orders` (`id_order`) ON DELETE CASCADE;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_orders`
ADD CONSTRAINT `emc_orders_ibfk_3` FOREIGN KEY (`{PREFIXE}orders_id_order`)
REFERENCES `{PREFIXE}orders` (`id_order`) ON DELETE CASCADE;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_orders_tmp`
ADD CONSTRAINT `emc_orders_tmp_ibfk_1` FOREIGN KEY (`{PREFIXE}orders_id_order`)
REFERENCES `{PREFIXE}orders` (`id_order`) ON DELETE CASCADE;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_points`
ADD FOREIGN KEY (`{PREFIXE}orders_id_order`)
REFERENCES `{PREFIXE}orders` (`id_order`) ON DELETE CASCADE;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_tracking`
ADD FOREIGN KEY (`{PREFIXE}orders_id_order`)
REFERENCES `{PREFIXE}orders` (`id_order`) ON DELETE CASCADE ;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_cart_tmp`
ADD FOREIGN KEY (`{PREFIXE}cart_id_cart`)
REFERENCES `{PREFIXE}cart` (`id_cart`) ON DELETE CASCADE ;