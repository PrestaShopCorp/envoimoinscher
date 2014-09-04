<?php

/* 
 * Upgrade the module from 3.1.4 to 3.1.5
 */
function upgrade_module_3_1_5($module)
{
	// Execute the SQL upgrade
	if (Db::getInstance()->execute(Tools::file_get_contents('Upgrade-3.1.5.sql')) === false)
	{
		return false;
	}
	
	// Add hooks if necessary
	
	// Validate upgrade
	return true;
}
?>