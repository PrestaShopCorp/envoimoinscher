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
 * @copyright 2007-2014 PrestaShop SA / 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

function upgrade_module_3_3_4($module)
{
 
    // Register old hooks to be safe
    $module->registerHook('processCarrier');
    $module->registerHook('newOrder');
    $module->registerHook('orderDetail');
    $module->registerHook('displayCarrierList');
    $module->registerHook('displayBeforeCarrier');
    $module->registerHook('updateCarrier');
    $module->registerHook('header');
    $module->registerHook('adminOrder');
    $module->registerHook('DisplayBackOfficeHeader');
    
     // Execute the SQL upgrade
    $sql_file = Tools::file_get_contents(_PS_MODULE_DIR_.'/envoimoinscher/upgrade/3.3.4.sql');
    $sql_file = str_replace('{PREFIXE}', _DB_PREFIX_, $sql_file);
    $sql_file = str_replace('{DBNAME}', _DB_NAME_, $sql_file);

    // Because all merchants can't execute every sql queries in one execute, we have to explode them.
    $query = explode('-- REQUEST --', $sql_file);

    Db::getInstance()->execute('START TRANSACTION;');
    foreach ($query as $q) {
        if (trim($q) != '' && Db::getInstance()->execute($q) === false) {
            Db::getInstance()->execute('ROLLBACK;');
            return false;
        }
    }
    
    // Update config
    $split = EnvoimoinscherModel::getConfig('EMC_PICKUP_F2');
    EnvoimoinscherModel::updateConfig('EMC_PICKUP_SPLIT', $split);
    $languages = EnvoimoinscherModel::getLanguages();
    $delivery_label = EnvoimoinscherModel::getConfig('EMC_LABEL_DELIVERY_DATE');
    $emcLabelDeliveryDate = array();
    foreach ($languages as $language) {
        $emcLabelDeliveryDate[Tools::strtolower($language['language_code'])] = $delivery_label;
    }
    EnvoimoinscherModel::updateConfig('EMC_LABEL_DELIVERY_DATE', serialize($emcLabelDeliveryDate));
    
    // Validate upgrade
    Db::getInstance()->execute('COMMIT;');
    return true;
}
