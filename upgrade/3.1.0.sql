update {PREFIXE}tab as tab, (select id_tab from {PREFIXE}tab where class_name = 'AdminParentShipping') as id set id_parent = id.id_tab where class_name='AdminEnvoiMoinsCher';
-- REQUEST --
