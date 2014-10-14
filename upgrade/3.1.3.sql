-- ajouter les parametres sur service
ALTER TABLE `{PREFIXE}emc_services`
ADD `ref_carrier` int(11) NOT NULL DEFAULT 0 AFTER `id_carrier`,
ADD `pricing_es` int(1) NOT NULL AFTER `type_es`;
-- REQUEST --
-- deplacer les valeurs de carrier à services
UPDATE `{PREFIXE}emc_services` es
JOIN `{PREFIXE}carrier` c 
ON c.emc_services_id_es = es.id_es SET
es.id_carrier = c.id_carrier,
es.ref_carrier = c.id_reference,
es.pricing_es = c.emc_type;
-- REQUEST --
-- retirer attributs de carrier
ALTER TABLE `{PREFIXE}carrier`
DROP emc_services_id_es,
DROP emc_type;
-- REQUEST --
