<?php
/**
 * 2007-2014 PrestaShop
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
 * @copyright 2007-2014 PrestaShop SA / 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

function upgrade_module_3_0_0($module)
{
	// hook update
	$module->registerHook('DisplayBackOfficeHeader');

	// configuration update
	if (Configuration::get('EMC_USER') == '') 			Configuration::updateValue('EMC_USER', 0);
	if (Configuration::get('EMC_MSG') == '') 				Configuration::updateValue('EMC_MSG', 'La plate-forme d\'expéditions est actuellement indisponible');
	if (Configuration::get('EMC_MASS') == '') 			Configuration::updateValue('EMC_MASS', EnvoimoinscherModel::WITH_CHECK);
	if (Configuration::get('EMC_TRACK_MODE') == '') Configuration::updateValue('EMC_TRACK_MODE', false);
	if (Configuration::get('EMC_ASSU') == '') 			Configuration::updateValue('EMC_ASSU', '');
	if (Configuration::get('EMC_INDI') == '') 			Configuration::updateValue('EMC_INDI', true);
	if (Configuration::get('EMC_MULTIPARCEL') == '')Configuration::updateValue('EMC_MULTIPARCEL', false);
	if (Configuration::get('EMC_PICKUP_J1') == '')	Configuration::updateValue('EMC_PICKUP_J1', '2');
	if (Configuration::get('EMC_PICKUP_F1') == '') 	Configuration::updateValue('EMC_PICKUP_F1', '0');
	if (Configuration::get('EMC_PICKUP_T1') == '') 	Configuration::updateValue('EMC_PICKUP_T1', '17');
	if (Configuration::get('EMC_PICKUP_J2') == '') 	Configuration::updateValue('EMC_PICKUP_J2', '3');
	if (Configuration::get('EMC_PICKUP_F2') == '') 	Configuration::updateValue('EMC_PICKUP_F2', '17');
	if (Configuration::get('EMC_PICKUP_T2') == '') 	Configuration::updateValue('EMC_PICKUP_T2', '24');
	if (Configuration::get('EMC_NATURE') == '') 		Configuration::updateValue('EMC_NATURE', false);
	if (Configuration::get('EMC_ENV') == '') 				Configuration::updateValue('EMC_ENV', 'TEST');
	if (Configuration::get('EMC_TYPE') == '') 			Configuration::updateValue('EMC_TYPE', 'colis');
	if (Configuration::get('EMC_ORDER') == '') 			Configuration::updateValue('EMC_ORDER', 0);
	if (Configuration::get('EMC_TRACK_MODE') == '') Configuration::updateValue('EMC_TRACK_MODE', 2);
	Configuration::updateValue('EMC_SRV_MODE', 'config');
	Configuration::updateValue('EMC_LABEL_DELIVERY_DATE', 'Livraison prévue : {DATE}');
	
	// Execute the SQL upgrade
	$sql_file = Tools::file_get_contents(_PS_MODULE_DIR_.'/envoimoinscher/upgrade/3.0.0.sql');
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