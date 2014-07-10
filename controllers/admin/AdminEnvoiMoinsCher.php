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

include_once(_PS_MODULE_DIR_.'/envoimoinscher/envoimoinscher.php');
class AdminEnvoiMoinsCherController extends ModuleAdminController
{

	public function __construct()
	{
		$this->className = 'AdminEnvoiMoinsCherController';
		parent::__construct();
	}

	public function init()
	{
		parent::init();
		$emc = new Envoimoinscher();
		$cookie = $emc->getContext()->cookie;

		if (Tools::getValue('sendValueWithCheck'))
			Configuration::updateValue('EMC_MASS', EnvoimoinscherModel::WITH_CHECK);
		else
			Configuration::updateValue('EMC_MASS', EnvoimoinscherModel::WITHOUT_CHECK);

		$html = null;
		$option = '';

		$option = Tools::getValue('option'); // Get option
		$link = new Link();
		$admin_link_base = $link->getAdminLink('AdminEnvoiMoinsCher');

		$emc = new Envoimoinscher();
		switch ($option)
		{
			case 'dimensions':
				$html .= $emc->dimensions();
			break;

			case 'tests':
				$html .= $emc->tests();
			break;

			case 'send':
				$html .= $emc->send();
			break;

			case 'command':
				if ($emc->command())
					Tools::redirectAdmin($admin_link_base);
				else
					Tools::redirectAdmin($admin_link_base.'&id_order='.$cookie->id_order.'&option=send');
			break;

			case 'tracking':
				$html .= $emc->getTracking();
			break;

			case 'replace':
				$html .= $emc->replaceOffer();
			break;

			case 'upgrade':
				$html .= $emc->makeUpgrade();
			break;

			case 'cleanCache':
				$html .= $emc->cleanCache();
			break;

			case 'loadAllCarriers':
				$html .= $emc->loadAllCarriers();
			break;

			case 'download':
			// to display labels correctly, we have to clean buffor
			ob_end_clean();
			$emc->downloadLabels();
			break;

			case 'getOffersNewWeight':
				$html .= $emc->getOffersNewWeight();
			break;

			case 'lookForCarrierUpdates':
				$html .= $emc->lookForCarrierUpdates();
			break;

			case 'checkLabelsAvailability':
				$html .= $emc->checkLabelsAvailability();
			break;

			case 'checkUpdates':
				$html .= $emc->checkUpdates();
			break;

			case 'history':
				$html .= $emc->ordersHistoryTable();
			break;

			case 'initOrder':
				$html .= $emc->initOrder();
			break;

			case 'cancelOrder':
				$html .= $emc->cancelOrder();
			break;

			case 'editAddress':
				$html .= $emc->editAddress();
			break;

			default:
				$html .= $emc->ordersTable();
			break;
		}
		$this->content = $html;
	}
}