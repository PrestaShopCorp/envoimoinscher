<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    EnvoiMoinsCher <informationapi@boxtale.com>
 * @copyright 2007-2015 PrestaShop SA / 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

function upgrade_module_3_1_7($module)
{
	// Remove files
	array_map('rrmdir', glob(_PS_MODULE_DIR_."\envoimoinscher\views\*.tpl"));
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\views\Admin');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\images');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\tpl');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\js\configuration.js');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\css\_backend_styles.css');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\css\back-office.css.bak');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\AdminEnvoiMoinsCher.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\envoimoinscher.png');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\EnvoiMoinsCherHelper.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\EnvoiMoinsCherModel.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\EnvoiMoinsCherOrder.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\get_points.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\get_cart_prices.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\get_offers_opc.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\mondial_relay_update.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\put_ope.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\lgpl.txt');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\set_point.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\suivi13.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\tracking.php');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\UPDATE_QUERY.txt');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\override');
	rrmdir(_PS_MODULE_DIR_.'\envoimoinscher\Env\_WebService.php');

	// Execute the SQL upgrade
	$sql_file = Tools::file_get_contents(_PS_MODULE_DIR_.'/envoimoinscher/upgrade/3.1.7.sql');
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
		if ($file != '.' && $file != '..') rrmdir("$dir/$file");
		rmdir($dir);
	}
	else if (file_exists($dir)) unlink($dir);
}
?>