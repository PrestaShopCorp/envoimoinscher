<?php

/* 
 * Upgrade the module from 3.1.4 to 3.1.5
 */
function upgrade_module_3_1_5($module)
{
	// Execute the SQL upgrade
	$sql_file = Tools::file_get_contents('Upgrade-3.1.5.sql');
	$sql_file = str_replace('{PREFIXE}', _DB_PREFIX_, $sql_file);
	
	// Because any merchant can't execute every sql queries in one execute, we have to explode them.
	$query = explode('-- REQUEST --', $sql_file);
	
	Db::getInstance()->execute('START TRANSACTION;');
	foreach ($query as $q)
	{
		if (trim($q) != '' && Db::getInstance()->execute($q) === false)
		{
			Db::getInstance()->execute('ROLLBACK;');
			return false;
		}
	}
	
	// Validate upgrade
	Db::getInstance()->execute('COMMIT;');
	return true;
}

?>