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

function upgrade_module_3_2_3($module)
{
    // Set default configuration
    Configuration::updateValue('EMC_DISABLE_CART', 0);
    
    // Register new hook (+ old ones to be safe)
    $module->registerHook('processCarrier');
    $module->registerHook('newOrder');
    $module->registerHook('orderDetail');
    $module->registerHook('displayCarrierList');
    $module->registerHook('displayBeforeCarrier');
    $module->registerHook('updateCarrier');
    $module->registerHook('header');
    $module->registerHook('adminOrder');
    $module->registerHook('DisplayBackOfficeHeader');
    
    return true;
}
