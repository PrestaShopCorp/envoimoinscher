ALTER TABLE `{PREFIXE}emc_services` CHANGE COLUMN `is_parcel_dropoff_point_es` `is_parcel_dropoff_point_es` int(1) NOT NULL AFTER `is_parcel_pickup_point_es`;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_services` CHANGE COLUMN `ref_carrier` `ref_carrier` int(11) NOT NULL DEFAULT 0 AFTER `id_carrier`;
-- REQUEST --
ALTER TABLE `{PREFIXE}emc_services` CHANGE COLUMN `pricing_es` `pricing_es` int(1) NOT NULL AFTER `type_es`;