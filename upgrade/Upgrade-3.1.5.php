<?php

/* 
 * Upgrade the module from 3.1.4 to 3.1.5
 */
function upgrade_module_3_1_5($module)
{
	$upgrade = "3.1.5";
	
	// Execute the SQL upgrade
	$sql_file = Tools::file_get_contents('Upgrade-'.$upgrade.'.sql');
	$sql_file = str_replace('{PREFIXE}', _DB_PREFIX_, $sql_file);
	$query = explode('-- REQUEST --', $sql_file);
	Db::getInstance()->execute('START TRANSACTION;');
	foreach ($query as $q)
	{
		if (Db::getInstance()->execute($q) === false)
		{
			cancel_upgrade();
			return false;
		}
	}
	
	// Execute the files upgrade
	if (rcopy('Upgrade-'.$upgrade,'..') === false)
	{
		cancel_upgrade();
		return false;
	}
	
	// Add hooks if necessary
	
	// Remove upgrade files
	unlink('Upgrade-'.$upgrade.'.sql');
	rrmdir('Upgrade-'.$upgrade);
	
	// Validate upgrade
	Db::getInstance()->execute('COMMIT;');
	return true;
}

/*
 * Equivalent of rm -rf on the given folder
 * @param String $dir : folder name
 */
function rrmdir($dir)
{
	if (is_dir($dir))
	{
		$files = scandir($dir);
		foreach ($files as $file)
		if ($file != "." && $file != "..") rrmdir("$dir/$file");
		rmdir($dir);
	}
	else if (file_exists($dir)) unlink($dir);
}

/*
 * Copy and replace the folder into another folder
 * @param String $src : folder to copy
 * @param String $dst : folder target
 */
function rcopy($src, $dst)
{
	if (is_dir($src))
	{
		if (!is_dir($dst))
			mkdir($dst);
		$files = scandir($src);
		foreach ($files as $file)
		if ($file != "." && $file != "..")
			rcopy($src.'/'.$file, $dst.'/'.$file);
	}
	else if (file_exists($src)) 
		copy($src, $dst);
}

/*
 * Cancel the upgrade.
 */
function cancel_upgrade()
{
	Db::getInstance()->execute('ROLLBACK;');
}
?>