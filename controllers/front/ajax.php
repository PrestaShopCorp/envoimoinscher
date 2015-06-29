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
class envoimoinscherajaxModuleFrontController extends ModuleFrontController
{
	protected $result;

	public function __construct()
	{
		//$this->bootstrap = true;
		$this->className = 'FrontEnvoiMoinsCherController';
		parent::__construct();
		$this->result = '';
	}

	public function postProcess()
	{
		$emc = new Envoimoinscher();
		$option = Tools::getValue('option'); // Get option

		// getModuleLink
		//$link = new Link();
		//$admin_link_base = $link->getAdminLink($emc->name,'getModuleLink');

		switch ($option)
		{
			case 'get_point' :
				$this->result = $emc->getPoints();
				break;
			case 'set_point' :
				$this->result = $emc->setPoint(Tools::getValue('point'));
				break;
			case 'push' :
				$this->result = (int)$emc->handlePush();
				break;
		}
	}

	public function display()
	{
		//ob_end_clean();
		echo $this->result;
		//die();
	}
}