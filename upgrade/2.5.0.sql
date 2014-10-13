INSERT INTO {PREFIXE}emc_operators (id_eo, name_eo, code_eo) VALUES (24, 'La Poste', 'POFR');      
-- REQUEST --
INSERT INTO {PREFIXE}emc_services (code_es, emc_operators_code_eo, label_es, desc_es, desc_store_es, label_store_es, price_type_es, is_parcel_point_es, family_es) VALUES ('ColissimoAccess', 'POFR', 'La Poste Colissimo Access France','Délai indicatif de 48h en jours ouvrables pour les envois en France métropolitaine. Remise sans signature.','Livraison à domicile en 48h. Remise sans signature.', 'La Poste Colissimo Access France', 0, 0, 1), ('ColissimoExpert', 'POFR', 'La Poste Colissimo Expert France','Délai indicatif de 48h en jours ouvrables pour les envois en France métropolitaine. Remise contre signature.','Livraison à domicile en 48h. Remise contre signature.', 'La Poste Colissimo Expert France', 0, 0, 1);
-- REQUEST --
UPDATE {PREFIXE}emc_services SET family_es = 1 WHERE emc_operators_code_eo = "CHRP";
-- REQUEST --
INSERT INTO {PREFIXE}configuration(name,value,date_add,date_upd) VALUES	('EMC_WRAPPING','',curdate(),curdate());
-- REQUEST --
ALTER TABLE  `{PREFIXE}emc_api_pricing` ADD COLUMN `date_delivery` TEXT NOT NULL;
-- REQUEST --
DELETE FROM {PREFIXE}emc_api_pricing;