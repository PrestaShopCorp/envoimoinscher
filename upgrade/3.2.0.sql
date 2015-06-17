INSERT IGNORE INTO `{PREFIXE}emc_operators_categories` (id_eo, id_eca) VALUES (20, 20120);
-- REQUEST --
INSERT IGNORE INTO `{PREFIXE}configuration` (name,value) VALUES ('EMC_KEY_PROD',( select IF(conf.value = 'PROD' , conf1.value , '' ) as emcKey from `{PREFIXE}configuration` conf  JOIN `{PREFIXE}configuration` conf1 on ( conf1.name = 'EMC_KEY') where conf.name = 'EMC_ENV'));
-- REQUEST --
INSERT IGNORE INTO `{PREFIXE}configuration` (name,value) VALUES ('EMC_KEY_TEST',( select IF(conf.value = 'TEST' , conf1.value , '' ) as emcKey from `{PREFIXE}configuration` conf  JOIN `{PREFIXE}configuration` conf1 on ( conf1.name = 'EMC_KEY') where conf.name = 'EMC_ENV'));
-- REQUEST --
INSERT IGNORE INTO `{PREFIXE}configuration` (name,value) VALUES ((SELECT concat( concat( 'EMC_KEY_', conf.value), '_DONOTCHECK') FROM `{PREFIXE}configuration` conf WHERE conf.name = 'EMC_ENV'), 1);
-- REQUEST --