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

require_once(realpath(dirname(__FILE__).'/../../../config/defines.inc.php'));
require_once(_PS_MODULE_DIR_.'/../config/config.inc.php');
require_once(_PS_MODULE_DIR_.'/../init.php');

$text_tracking = urldecode(Tools::getValue('text'));
$local_tracking = urldecode(Tools::getValue('localisation'));

require_once(_PS_MODULE_DIR_.'/envoimoinscher/envoimoinscher.php');

$emc = new Envoimoinscher();
$cookie = $emc->getContext()->cookie;
/* init error data */

$ip_address = $emc->l('Unknown address');
if (preg_match('/^([A-Za-z0-9.]+)$/', Tools::getRemoteAddr()))
	$ip_address = Tools::getRemoteAddr();
$error_msg = sprintf($emc->l('Error during the insertion of tracking information for order %1$s. Order not found. Caller IP address : %2$s'),
		(int)Tools::getValue('order'),
		$ip_address);
/* check order in the database */
$order_id = (int)Tools::getValue('order');
if (ctype_alnum(Tools::getValue('key')) && $order_id > 0)
{
	$order_info = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'emc_orders eo
		 JOIN '._DB_PREFIX_.'orders o ON o.id_order = eo.'._DB_PREFIX_.'orders_id_order
		 WHERE eo.'._DB_PREFIX_.'orders_id_order = '.$order_id.' AND eo.tracking_eor = "'.Tools::getValue('key').'" ');
	if (count($order_info) > 0)
	{
		/* get last order state (prevent to repeat the same state) */
		$history_row = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'order_history
			 WHERE id_order = '.$order_id.' ORDER BY id_order_history DESC LIMIT 1');
		$confs = Configuration::getMultiple(array('EMC_ANN', 'EMC_ENVO', 'EMC_CMD', 'EMC_LIV'));
		/* if EMC_LIV, do not accept other tracking infos */
		if ($history_row[0]['id_order_state'] == $confs['EMC_LIV'])
			die();
		$customer = new Customer((int)$order_info[0]['id_customer']);
		switch (Tools::getValue('etat'))
		{
			case 'CMD':
				$new_order_atate = $confs['EMC_CMD'];
			break;
			case 'ENV':
				$new_order_atate = $confs['EMC_ENVO'];
			break;
			case 'ANN':
				$message = new Message();
				$texte = $emc->l('EnvoiMoinsCher : Dispatch cancelled');
				$message->message = htmlentities($texte, ENT_COMPAT, 'UTF-8');
				$message->id_order = $order_id;
				$message->private = 1;
				$message->add();
				$new_order_atate = $confs['EMC_ANN'];
			break;
			case 'LIV':
				$new_order_atate = $confs['EMC_LIV'];
			break;
			default:
				die();
		}
		if ((int)$new_order_atate > 0 && $new_order_atate != $history_row[0]['id_order_state'])
		{
			$history = new OrderHistory();
			$history->id_order = $order_id;
			$history->changeIdOrderState($new_order_atate, $order_id);
			$history->id_employee = (int)$cookie->id_employee;
			$history->addWithemail();
		}
		/* only when all informations */
		if ($text_tracking == '')
		{
			$cmd_row = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'order_state_lang
				 WHERE id_order_state = '.(int)$new_order_atate.' AND id_lang = '.(int)$emc->language->id);
			$text_tracking = 'Etat de votre commande : '.$cmd_row[0]['name'];
		}
		$date_get = date('Y-m-d H:i:s', strtotime(Tools::getValue('date')));
		if (!Tools::getIsset('date'))
			$date_get = date('Y-m-d H:i:s', time());
		/* insert tracking infos to EnvoiMoinsCher table */
		Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'emc_tracking
			 ('._DB_PREFIX_.'orders_id_order, state_et, date_et, text_et, localisation_et)
			 VALUES
			 ('.$order_id.', "'.Tools::getValue('etat').'", "'.$date_get.'", "'.pSQL($text_tracking).'", "'.pSQL($local_tracking).'")
			 ');
	}
	else
	{
		/* log incorrect values */
		Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$error_msg, 4, 1);
	}
}
else
{
	/* log incorrect values */
	Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$error_msg, 4, 1);
}