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
 * @license   http://opensource.org/licenses/afl-3.0.php	Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_.'/envoimoinscher/includes/EnvoimoinscherModel.php');
require_once(_PS_MODULE_DIR_.'/envoimoinscher/includes/EnvoimoinscherHelper.php');
require_once(_PS_MODULE_DIR_.'/envoimoinscher/includes/EnvoimoinscherOrder.php');

class Envoimoinscher extends CarrierModule
{

	private static $cache = array();

	/**
	 * Array with environments of envoimoinscher web service.
	 * @var array
	 * @access protected
	 */
	protected $environments = array();

	/**
	 * Shipping types.
	 * @var array
	 * @access protected
	 */
	protected $ship_types = array();

	/**
	 * List with civilities used on quotation and shippment order.
	 * @var array
	 * @access protected
	 */
	protected $civilities = array();

	/**
	 * List with order modes (automatic or manually)
	 * @var array
	 * @access protected
	 */
	protected $modes = array();

	/**
	 * Pricing types.
	 * @var array
	 * @access protected
	 */
	protected $pricing = array();

	/**
	 * Database instance.
	 * @access private
	 * @var Db
	 */
	private $model = null;

	/**
	 * Limit for EMC's API calls when multishipping is activated.
	 * Limit is here to avoid the timeouts.
	 * @var int
	 */

	const MAX_LIMIT_FOR_MULTISHIPPING = 2;

	public $id_carrier;

	private static $api_results = false;

	private $link = null;

	private $website_url = null;

	public function __construct()
	{
		// local parameters initialisation
		$this->environments = array(
			'TEST' => array(
				'link' => 'https://test.envoimoinscher.com',
				'alias' => $this->l('of test')
			),
			'PROD' => array(
				'link' => 'https://www.envoimoinscher.com',
				'alias' => $this->l('of production')
			)
		);
		$this->ship_types = array(
			'colis',
			'encombrant',
			'palette',
			'pli'
		);
		$this->civilities = array(
			'M' => $this->l('M'),
			'Mme' => $this->l('Mme'),
			'Mlle' => $this->l('Miss')
		);
		$this->modes = array(
			$this->l('automatic'),
			$this->l('manual')
		);
		$this->pricing = array(
			'real',
			'scale'
		);
		$this->name = 'envoimoinscher';
		$this->tab = 'shipping_logistics';
		$this->version = '3.1.10';
		$this->author = 'EnvoiMoinsCher';
		$this->local_version = '3.1.10';
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		$this->displayName = 'EnvoiMoinsCher';
		$this->ws_name = 'Prestashop';
		//'Module de livraison : 21 transporteurs à tarifs négociés';
		$this->description = $this->l('Shipping module : 15 carriers with negotiated prices');
		$this->model = new EnvoimoinscherModel(Db::getInstance(), $this->name);
		$this->link = new Link();
		$this->website_url = 'https://www.envoimoinscher.com';
	}

	/**
	 * Install function.
	 * @access public
	 * @return boolean True if correct installation, false if not
	 */
	public function install()
	{
		// the module no longer compatible under PS1.5
		if (version_compare(_PS_VERSION_, '1.5', '<'))
		{
			$this->_errors[] = $this->l('ENVOIMOINSCHER is not compatible with PrestaShop lower than 1.5.');
			$error	= '[ENVOIMOINSCHER]['.time().'] '.$this->l('ENVOIMOINSCHER is not compatible with PrestaShop lower than 1.5.');
			Logger::addLog($error);
			return false;
		}

		// if curl's not avaliable, the module wont work
		if (!extension_loaded('curl'))
		{
			$error	= '[ENVOIMOINSCHER]['.time().'] '.$this->l('installation : cannot install the module, curl is not available');
			Logger::addLog($error);
			return false;
		}

		// Get default configuration state
		$states = OrderState::getOrderStates((int)$this->getContext()->language->id);
		$states_array = array();
		foreach ($states as $state)
		{
			$states_array[] = $state['id_order_state'];
			if ($state['template'] === 'preparation')
				$emc_cmd = (int)$state['id_order_state'];
			else if ($state['template'] === 'shipped')
				$emc_envo = (int)$state['id_order_state'];
			else if ($state['template'] === 'order_canceled')
				$emc_ann = (int)$state['id_order_state'];
			else if ($state['template'] == '')
				$emc_liv = (int)$state['id_order_state'];
		}

		// Set default configuration
		Configuration::updateValue('EMC_CMD', (int)$emc_cmd);
		Configuration::updateValue('EMC_ENVO', (int)$emc_envo);
		Configuration::updateValue('EMC_ANN', (int)$emc_ann);
		Configuration::updateValue('EMC_LIV', (int)$emc_liv);
		Configuration::updateValue('EMC_USER', -1);
		Configuration::updateValue('EMC_MSG', $this->l('Platform\'s shipments is currently unavailable'));
		Configuration::updateValue('EMC_SRV_MODE', EnvoimoinscherModel::MODE_CONFIG);
		Configuration::updateValue('EMC_MASS', EnvoimoinscherModel::WITH_CHECK);
		Configuration::updateValue('EMC_ASSU', '');
		Configuration::updateValue('EMC_INDI', true);
		Configuration::updateValue('EMC_MULTIPARCEL', false);
		Configuration::updateValue('EMC_PICKUP_J1', '2');
		Configuration::updateValue('EMC_PICKUP_F1', '0');
		Configuration::updateValue('EMC_PICKUP_T1', '17');
		Configuration::updateValue('EMC_PICKUP_J2', '3');
		Configuration::updateValue('EMC_PICKUP_F2', '17');
		Configuration::updateValue('EMC_PICKUP_T2', '24');
		Configuration::updateValue('EMC_NATURE', false);
		Configuration::updateValue('EMC_ENV', 'TEST');
		Configuration::updateValue('EMC_TYPE', 'colis');
		Configuration::updateValue('EMC_ORDER', 0);
		Configuration::updateValue('EMC_WRAPPING', '');
		Configuration::updateValue('EMC_LABEL_DELIVERY_DATE', $this->l('Delivery scheduled : {DATE}'));
		Configuration::updateValue('EMC_TRACK_MODE', '2');
		Configuration::updateValue('EMC_LAST_CARRIER_UPDATE', '');
		Configuration::updateValue('EMC_ENABLED_LOGS', 0);
		Configuration::updateValue('EMC_FILTER_TYPE_ORDER', 'all');
		Configuration::updateValue('EMC_FILTER_STATUS', implode(';', $states_array));
		Configuration::updateValue('EMC_FILTER_CARRIERS', 'all');
		Configuration::updateValue('EMC_FILTER_START_DATE', 'all');

		// Execute queries
		$sql_file = Tools::file_get_contents(_PS_MODULE_DIR_.'/envoimoinscher/sql/install.sql');
		$sql_file = str_replace('{PREFIXE}', _DB_PREFIX_, $sql_file);
		$query = explode('-- REQUEST --', $sql_file);
		foreach ($query as $q)
		{
			if (Db::getInstance()->execute($q) === false)
			{
				Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$this->l('installation :	An error occured on the query : ').$q);
				$this->tablesRollback();
				return false;
			}
		}

		if (parent::install() === false)
		{
			$this->tablesRollback();
			return false;
		}

		// for this version of module, the hooks are only registered for, at least, Prestashop 1.4
		$this->registerHook('processCarrier');
		$this->registerHook('newOrder');
		$this->registerHook('orderDetail');
		$this->registerHook('displayCarrierList');
		$this->registerHook('updateCarrier');
		$this->registerHook('header');
		$this->registerHook('adminOrder');
		$this->registerHook('DisplayBackOfficeHeader');

		// add the new tab
		$tab = new Tab();
		$tab->class_name = 'AdminEnvoiMoinsCher';
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentShipping');
		$tab->module = 'envoimoinscher';
		$tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = 'EnvoiMoinsCher';
		if ($tab->add() === false)
		{
			if ((int)Configuration::get('EMC_ENABLED_LOGS') == 1)
				Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$this->l('installation : Impossible to add the EnvoiMoinsCher button in the menu'));
			$this->tablesRollback();
			return false;
		}

		return true;
	}

	/**
	 * Uninstall function.
	 * @access public
	 * @return void
	 */
	public function uninstall()
	{
		// Delete tab
		$id_tab = Tab::getIdFromClassName('AdminEnvoiMoinsCher'); // Get ID for delete
		$tab = new Tab($id_tab); // Instanciation of tab

		// remove column in table
		//$columns = DB::getInstance()->executeS('DESCRIBE `'._DB_PREFIX_.'carrier`');
		//$column_to_remove = array('emc_services_id_es', 'emc_type'); // Column to remove

		// remove emc carriers
		$remove_emc_carriers = 'UPDATE `'._DB_PREFIX_.'carrier` set deleted = 1 where external_module_name = "'.$this->name.'"';

		// If execution doesn't work
		if ($this->tablesRollback() === false ||
				parent::uninstall() === false ||
				$tab->delete() === false ||
				DB::getInstance()->execute($remove_emc_carriers) === false)
			return false;
		return true;
	}

	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Rollback SQL queries.
	 * @access private
	 * @return boolean
	 */
	private function tablesRollback()
	{
		$helper = new EnvoimoinscherHelper();

		$tables = array();
		foreach ($helper->getTablesNames() as $table)
			$tables[] = '`'._DB_PREFIX_.$table.'`';
		$remove_tables = 'DROP TABLE IF EXISTS '.implode(',', $tables);

		$remove_configs = 'DELETE FROM '._DB_PREFIX_.'configuration WHERE name LIKE "EMC_%"';

		return DB::getInstance()->execute($remove_tables) && DB::getInstance()->execute($remove_configs);
	}

	/**
	 * Return the default address
	 * @access private
	 * @return array
	 */
	private function getDefaultAddress()
	{
		return array
		(
			'country' => 'FR',
			'postcode' =>'75002',
			'city' => 'Paris',
			'type' => 'particulier',
			'street' => 'Rue de la paix'
		);
	}

	public function useLocalFancybox()
	{
		return _PS_VERSION_ < '1.5.3';
	}

	public function useLocalBootstrap()
	{
		return _PS_VERSION_ < '1.6.0';
	}

	/**
	 * Configuration method.
	 * @access public
	 * @return void
	 */
	public function getContent()
	{
		$smarty = $this->getContext()->smarty;
		$content = $this->postProcess();

		$helper = new EnvoimoinscherHelper();
		$config = $helper->configArray($this->model->getConfigData());

		$emc_user = isset($config['EMC_USER'])?(int)$config['EMC_USER']: - 1;

		// pass the module offline if we are in the installation process (avoid updates bug)
		if ($emc_user <= 2)
			Configuration::updateValue('EMC_SRV_MODE', EnvoimoinscherModel::MODE_CONFIG);

		// we load all the carriers if the configuration allow it
		if (Configuration::get('EMC_LAST_CARRIER_UPDATE') == '')
			$this->loadAllCarriers(false);

		require_once('Env/WebService.php');
		require_once('Env/User.php');

		// get default contact data
		$address_if_filled = array(
			'EMC_COMPANY'		=> false,
			'EMC_ADDRESS'		=> false,
			'EMC_POSTALCODE' => false,
			'EMC_CITY'			 => false,
			'EMC_TEL'				=> false,
			'EMC_MAIL'			 => false
		);

		// array with obligatory fields (must be filled up to make work this module)
		$obligatory = array(
			'EMC_KEY',
			'EMC_LOGIN',
				'EMC_PASS',
				'EMC_FNAME',
				'EMC_LNAME',
				'EMC_COMPANY',
				'EMC_ADDRESS',
				'EMC_POSTALCODE',
				'EMC_CITY',
			'EMC_TEL',
			'EMC_MAIL',
			'EMC_PICKUP_J1'
		);

		// default configuration values
		$config['EMC_SERVICES'] = explode(',', $config['EMC_SERVICES']);

		$config['wsName'] = $this->ws_name; // ok
		$config['localVersion'] = $this->local_version; // ok

		foreach ($address_if_filled as $a => $address)
		{
			if ((!isset($config[$a]) || $config[$a] == '') && $address != false && $address != '')
			{
				$config[$a] = $address;
				Configuration::updateValue($a, $address);
			}
		}

		$datas = array(
			'local_bootstrap'=> $this->useLocalBootstrap(),
			'introduction'	=> $this->getContentIntroduction(),
			'missedValues' 	=> ($emc_user > 2 ? $this->makeMissedList($obligatory, $config) : array()),
			'EMC_config'	 	=> $config,
			'link'				 	=> new Link(),
			'envUrl'			 	=> (!empty($config['EMC_ENV']) ? $this->environments[Tools::strtoupper($config['EMC_ENV'])]['link'] : null)
		);

		if ($emc_user <= 2)
		{
			if ($emc_user === -1)
			{
				$datas['content'] = $content.$this->getContentIntroduction();
				$content = '';
			}
			else if ($emc_user === 0)
			{
				$datas['content'] = $content.$this->getContentMerchant();
				$content = '';
			}
			else if ($emc_user === 1)
			{
				$datas['content'] = $content.$this->getContentSends(false);
				$content = '';
			}
			else if ($emc_user === 2)
			{
				$datas['content'] = $content.$this->getContentCarriers('Simple');
				$content = '';
			}
			else
			{
				$this->adminDisplayWarning($this->l('One error was encountered, this step will not work'));
				$datas['content'] = $content;
				$content = '';
			}
		}
		$datas['emcBaseDir'] = _MODULE_DIR_.'/envoimoinscher/';
		$smarty->assign($datas);

		$content .= $this->getContentBody();

		return $content;
	}

	private function getContentBody()
	{
		$smarty = $this->getContext()->smarty;
		$cookie = $this->getContext()->cookie;

		$helper = new EnvoimoinscherHelper();

		$api_params = $this->model->getApiParams($this->ws_name, $this->version);

		$ver = explode('.', _PS_VERSION_);

		// on verifie si les offres ont ete mises a jour recement
		$last_update = Configuration::get('EMC_LAST_CARRIER_UPDATE');
		$send_offers_update_warning = true;

		if ($last_update != '')
		{
			$date_limit = time() - (60 * 60 * 24 * 30);
			$date_update = strtotime($last_update);
			$send_offers_update_warning = $date_update < $date_limit;
		}
		$datas = array(
			'default_tab'	 => count($this->getUpgrades()) > 0?'help':(Tools::isSubmit('EMC_tabs')?Tools::getValue('EMC_tabs'):'merchant'),
			'tab_news'			 => $this->model->getApiNews($this->ws_name, $this->version),
			'tpl_news'			 => _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/news.tpl',
			'local_fancybox' => $this->useLocalFancybox(),
			'need_update'	 => $send_offers_update_warning,
			'PS_ver'		 => $ver[0],
			'PS_subver'		 => $ver[1],
			'module_version' => $this->version,
			'API_errors'	 => $api_params['error_code'],
			'EMC_config'		 => $helper->configArray($this->model->getConfigData()),
			'multiShipping'	=> Configuration::get('PS_ALLOW_MULTISHIPPING'),
			'successForm'		=> (int)$cookie->success_form,
			'modulePath'		 => $this->_path,
			'website_url'		=> $this->website_url
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentBody.tpl');
	}

	public function getContentHelp()
	{
		$smarty = $this->getContext()->smarty;

		//$helper = new EnvoimoinscherHelper();
		$datas = array(
			'emcBaseDir'	=> _MODULE_DIR_.'/envoimoinscher/',
			'link'		 		=> new Link(),
			'upgrades' 		=> $this->parseUpgradeXml(_PS_MODULE_DIR_.'envoimoinscher/sql/upgrades/upgrades.xml')
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentHelp.tpl');
	}

	/**
	 * Get ajax introduction content
	 * @return template Smarty Template
	 */
	private function getContentIntroduction()
	{
		$smarty = $this->getContext()->smarty;
		$datas = array(
			'emcImgDir'	=> _PS_MODULE_DIR_.'envoimoinscher/img/'
		);

		$smarty->assign($datas);
		return $this->display(__FILE__, '/views/templates/admin/getContentIntroduction.tpl');
	}

	/**
	 * Get ajax settings content
	 * @return template Smarty Template
	 */
	private function getContentMerchant()
	{
		$smarty = $this->getContext()->smarty;
		//$id_lang = (int)$this->getContext()->language->id;

		$helper = new EnvoimoinscherHelper();
		$datas = array(
			// Genders
			'genders' => $this->civilities,
			// Time to pickup
			'dispoStart' => $helper->getDispo(array('START')),
			'dispoEnd' => $helper->getDispo(array('END')),
			// Configuration
			'EMC_config' => $helper->configArray($this->model->getConfigData())
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentMerchant.tpl');
	}

	/**
	 * Get ajax carriers content
	 * @return template Smarty Template
	 */
	private function getContentCarriers($type)
	{
		$smarty = $this->getContext()->smarty;

		$helper = new EnvoimoinscherHelper();

		$config = $helper->configArray($this->model->getConfigData());

		$datas = array(
			// Configuration
			'EMC_config'			=> $config,
			'srvModes'				=> array(
									'config' => EnvoimoinscherModel::MODE_CONFIG,
									'online' => EnvoimoinscherModel::MODE_ONLINE
								),
			'families'				=> $this->model->getOffersFamilies(),
			'familTableTpl'	 => $this->getTemplatePath('views/templates/admin/familyTpl.tpl'),
			'disableServices' => isset($config['EMC_SRV_MODE']) && $config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_ONLINE,
			'pricing'				 => $this->pricing,
			'operators'			 => EnvoimoinscherModel::getOperatorsForType($config['EMC_NATURE']),
			'nameCategory'		=> EnvoimoinscherModel::getNameCategory($config['EMC_NATURE'])
		);

		$smarty->assign($datas);

		if ($type === 'Simple')
			return $this->getContentCarriersSimple($smarty);
		else if ($type === 'Advanced')
			return $this->getContentCarriersAdvanced($smarty);
	}

	/**
	 * Simple Carrier
	 * @param	Smarty $smarty Smarty
	 * @return string				 Template parsed
	 */
	private function getContentCarriersSimple(Smarty $smarty)
	{
		$datas = array(
			'simpleEconomicCarriers' => $this->model->getOffersByFamily(EnvoimoinscherModel::FAM_ECONOMIQUE)
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentCarriersSimple.tpl');
	}

	/**
	 * Simple Carrier
	 * @param	Smarty $smarty Smarty
	 * @return string				 Template parsed
	 */
	private function getContentCarriersAdvanced(Smarty $smarty)
	{
		$rows = $this->model->getDimensions();

		$datas = array(
			'dims'										 => $rows,
			'advancedExpressCarriers'	=> $this->model->getOffersByFamily(EnvoimoinscherModel::FAM_EXPRESSISTE)
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentCarriersAdvanced.tpl');
	}

	/**
	 * Get ajax sends content
	 * @return template Smarty Template
	 */
	private function getContentSends($all = true)
	{
		$smarty = $this->getContext()->smarty;

		$api_params = $this->model->getApiParams($this->ws_name, $this->version);

		// we build the array $wrapping_types (wrapping type for POFR)
		$wrapping_types = array();

		$wrapping_types[] = array(
						'id' => '',
						'name' => $this->l('Please choose a wrapping type')
		);
		if (isset($api_params['POFR']))
		{
			foreach ($api_params['POFR']['services'] as $api_param)
			{
				if (isset($api_param['parameters']['emballage.type_emballage']))
				{
					foreach ($api_param['parameters']['emballage.type_emballage']['values'] as $type)
					{
						$wrapping_types[] = array(
							'id' => $type,
							'name' => Tools::substr($type, strpos($type, '-') + 1)
						);
					}
					break;
				}
			}
		}

		$helper = new EnvoimoinscherHelper();

		$config = $helper->configArray($this->model->getConfigData()); // Get configs
		$config['wsName'] = $this->ws_name; // add wsName to config
		$config['localVersion'] = $this->local_version; // Add localVersionto config
		// Get pickup conf
		if (!isset($config['EMC_PICKUP_J1']) ||
			!isset($config['EMC_PICKUP_F1']) ||
			!isset($config['EMC_PICKUP_T1']))
			$pick_up_conf = array(
				array(
					'j'		=> 0,
					'from' => 0,
					'to'	 => 0
				),
				array(
					'j'		=> 0,
					'from' => 0,
					'to'	 => 0)
			);
		else
			$pick_up_conf = array(
				array(
					'j'		=> $config['EMC_PICKUP_J1'],
					'from' => $config['EMC_PICKUP_F1'],
					'to'	 => $config['EMC_PICKUP_T1']
					),
				array(
					'j'		=> $config['EMC_PICKUP_J2'],
					'from' => $config['EMC_PICKUP_F2'],
					'to'	 => $config['EMC_PICKUP_T2']
					)
			);

		$datas = array(
			// Configuration
			'local_fancybox'				=> $this->useLocalFancybox(),
			'emcBaseDir'						=> _MODULE_DIR_.'/envoimoinscher/',
			'EMC_config'					 	=> $config,
			'shipTypes'							=> $this->ship_types,
			'shipNature'					 	=> $this->model->getCategoriesTree($config),
			'shipWrappingAvailable'	=> count($wrapping_types) > 1,
			'shipWrapping'					=> $wrapping_types,
			'pickupConf'					 	=> $pick_up_conf,
			'withoutMass'						=> EnvoimoinscherModel::WITHOUT_CHECK,
			'withMass'						 	=> EnvoimoinscherModel::WITH_CHECK,
			'link'								 	=> new Link(),
			'disableServices'				=> isset($config['EMC_SRV_MODE']) && $config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_ONLINE,
			'families'						 	=> $this->model->getOffersFamilies(),
			'familTableTpl'					=> $this->getTemplatePath('views/templates/admin/familyTpl.tpl'),
			'all'										=> $all
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentSends.tpl');
	}

	private function getContentSettings()
	{
		$smarty = $this->getContext()->smarty;
		$id_lang = (int)$this->getContext()->language->id;
		$helper = new EnvoimoinscherHelper();

		$config = $helper->configArray($this->model->getConfigData()); // Get configs

		require_once dirname(__FILE__).'/Env/WebService.php';
		require_once dirname(__FILE__).'/Env/User.php';

		$user_class = new Env_User(array('user' => $config['EMC_LOGIN'], 'pass' => $config['EMC_PASS'], 'key' => $config['EMC_KEY']));
		$user_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$user_class->setEnv(Tools::strtolower($config['EMC_ENV']));
		$user_class->getEmailConfiguration();

		//get enabled carriers
		$sql = 'SELECT id_carrier, name FROM '._DB_PREFIX_.'carrier WHERE deleted=0';
		$enabled_carriers = Db::getInstance()->ExecuteS($sql);

		$datas = array(
			'EMC_config' => $config,
			'states'		 => OrderState::getOrderStates($id_lang),
			'modes' => $this->model->getTrackingModes(),
			'mailConfig' => $user_class->user_configuration['emails'],
			'enabledCarriers' => $enabled_carriers,
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentSettings.tpl');

	}

	/**
	 * Add CSS
	 */
	public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('controller') === 'AdminModules')
			$this->getContext()->controller->addCSS($this->_path.'/css/back-office.css?version='.$this->version, 'all');
	}

	/**
	 * Compute the price and weight of a product
	 * Used by tests()
	 * @access public
	 * @return array
	 */
	public function priceWeightProduct($id, $attributes)
	{
		$sql = 'SELECT DISTINCT p.weight, p.price, pa.price AS `declin_price`, pa.weight AS `declin_weight`, pac.id_attribute
			 FROM '._DB_PREFIX_.'product p, '.
							_DB_PREFIX_.'product_attribute pa, '.
							_DB_PREFIX_.'product_attribute_combination pac
			 WHERE p.id_product = pa.id_product
			 and pa.id_product_attribute = pac.id_product_attribute
			 and p.id_product = '.(int)$id;
		if (count($attributes) > 0)
		{
			$sql .= ' and (';
			$count = count($attributes);
			for ($i = 0; $i < $count; $i++)
			{
				if ($i > 0)
					$sql .= ' or ';
				$sql .= 'pac.id_attribute = '.(int)$attributes[$i];
			}
			$sql .= ')';
		}
		$sql .= ' UNION SELECT DISTINCT p.weight, p.price, 0, 0, 0
			 FROM '._DB_PREFIX_.'product p
			 WHERE p.id_product = '.(int)$id;

		$attributes = Db::getInstance()->ExecuteS($sql);

		/* We compute the right weight with attributes */
		$result = Array(
			'weight' => $attributes[0]['weight'],
			'price' => $attributes[0]['price']
			);
		foreach ($attributes as $attribute)
		{
			$result['weight'] += (float)$attribute['declin_weight'];
			$result['price'] += (float)$attribute['declin_price'];
		}

		if ($result['weight'] <= 0)
			$result['weight'] = Configuration::get('EMC_AVERAGE_WEIGHT');

		return $result;
	}

	/**
	 * Tests if the web service of EnvoiMoinsCher is available. It allows user to estimate
	 * shipping costs.
	 * @access public
	 * @return void
	 */
	public function tests()
	{
		$smarty = $this->getContext()->smarty;
		$cookie = $this->getContext()->cookie;
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());

		$bd = Db::getInstance();
		/* we first get every product with attributes */
		$sql = 'SELECT distinct
					 pl.name,
					 p.id_product,
					 al.name AS `attribute_name`,
					 a.id_attribute_group,
					 al.id_attribute
					 FROM
					 '._DB_PREFIX_.'product p,
					 '._DB_PREFIX_.'product_lang pl,
					 '._DB_PREFIX_.'product_attribute pa,
					 '._DB_PREFIX_.'attribute a,
					 '._DB_PREFIX_.'product_attribute_combination pac,
					 '._DB_PREFIX_.'attribute_lang al
					 WHERE
					 pa.id_product = p.id_product and
					 pl.id_product = p.id_product and
					 pa.id_product_attribute = pac.id_product_attribute and
					 pac.id_attribute = al.id_attribute and
					 pac.id_attribute = a.id_attribute and
					 pl.id_lang = '.(int)$cookie->id_lang.'
					 ORDER BY pl.name';
		$products_list = $bd->ExecuteS($sql);
		$products = Array();

		foreach ($products_list as $p)
		{
			if (!isset($products[$p['id_product']]))
			{
				$products[$p['id_product']] = Array();
				$products[$p['id_product']]['name'] = $p['name'];
				$products[$p['id_product']]['id_product'] = $p['id_product'];
				$products[$p['id_product']]['attributes'] = Array();
			}
			if (!isset($products[$p['id_product']]['attributes'][$p['id_attribute_group']]))
				$products[$p['id_product']]['attributes'][$p['id_attribute_group']] = Array();
			$products[$p['id_product']]['attributes'][$p['id_attribute_group']][$p['id_attribute']] = Array();
			$products[$p['id_product']]['attributes'][$p['id_attribute_group']][$p['id_attribute']]['id_attribute'] = $p['id_attribute'];
			$products[$p['id_product']]['attributes'][$p['id_attribute_group']][$p['id_attribute']]['attribute_name'] = $p['attribute_name'];
		}
		/* we now get every product without attributes */
		$sql = 'SELECT
			 pl.name,
			 p.id_product
			 FROM
			 '._DB_PREFIX_.'product p,
			 '._DB_PREFIX_.'product_lang pl
			 WHERE
			 pl.id_product = p.id_product and
			 p.id_product NOT IN (SELECT id_product FROM '._DB_PREFIX_.'product_attribute) and
			 pl.id_lang = '.(int)$cookie->id_lang.'
			 ORDER BY pl.name';
		$products_list = $bd->ExecuteS($sql);
		foreach ($products_list as $p)
		{
			if (!isset($products[$p['id_product']]))
			{
				$products[$p['id_product']] = Array();
				$products[$p['id_product']]['name'] = $p['name'];
				$products[$p['id_product']]['id_product'] = $p['id_product'];
				$products[$p['id_product']]['attributes'] = Array();
			}
		}
		ksort($products);

		/* We start now the generation of products with their attributes */
		$products_smarty = Array();
		//$i = 0;
		foreach ($products as $product)
		{
			$product_names = Array();
			$product_names[0]['name'] = $product['name'];
			$product_names[0]['value'] = $product['id_product'];
			foreach ($product['attributes'] as $attribute_group)
			{
				$tmp_product_names = $product_names;
				$product_names = Array();
				foreach ($attribute_group as $attribute)
					foreach ($tmp_product_names as $tmp_product_name)
						$product_names[count($product_names)] = Array(
							'name' => ($tmp_product_name['name'].' - '.$attribute['attribute_name']),
							'value' =>	($tmp_product_name['value'].'_'.$attribute['id_attribute'])
						);
			}
			$products_smarty = array_merge($products_smarty, $product_names);
		}

		$smarty->assign('configEmc', $config);
		$smarty->assign('products', $products_smarty);
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('countries', Db::getInstance()->ExecuteS('SELECT c.iso_code, cl.name
			 FROM '._DB_PREFIX_.'country c
			 JOIN '._DB_PREFIX_.'country_lang cl
			 ON cl.id_country = c.id_country
			 WHERE cl.id_lang = '.(int)$cookie->id_lang.'
			 ORDER BY cl.name ASC'));
		$smarty->assign('successForm', (int)$cookie->form_success);
		$smarty->assign('adminImg', _PS_ADMIN_IMG_);
		$smarty->assign('baseDir', __PS_BASE_URI__);

		$error = -1;
		$error_msg = '';
		$cookie->form_success = 0;

		if (!empty($_POST) && Tools::isSubmit('submitForm'))
		{
			require_once('Env/WebService.php');
			require_once('Env/Quotation.php');
			$product_ids = explode('_', Tools::getValue('product'));
			$attributes = $product_ids;
			$id = array_shift($attributes);
			$item = array();
			$item[0] = $this->priceWeightProduct($id, $attributes);

			$weight_item = EnvoimoinscherHelper::normalizeToKg(Configuration::get('PS_WEIGHT_UNIT'), (float)$item[0]['weight']);

			// option < 100g
			if ($weight_item < 0.1 && $weight_item >= 0 && (int)Configuration::get('EMC_WEIGHTMIN') == 1)
				$weight_item = 0.1;
			// get dimensions
			$dimensions = $this->model->getDimensionsByWeight($weight_item);
			$currency = Currency::getDefaultCurrency();
			$cart_object = new Cart();
			$cart_object->id_currency = $currency->id;
			$cart_object->id_lang = $cookie->id_lang;
			// get zone_id by country id
			$country_iso = Tools::getValue('toCountry');
			$zone_row = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'country
				 WHERE iso_code = "'.pSQL($country_iso).'"');

			$offers = $this->makeApiCall(array(
				'testPage' => true,
				'weightConverted' => true,
				'address' => array(
					'postcode' => Tools::getValue('toPostalCode'),
					'city' => Tools::getValue('toCity'),
					'street' => Tools::getValue('toAddr'),
					'type' => 'particulier',
					'country' => $country_iso,
					'id_zone' => $zone_row[0]['id_zone']
				),
				'addressShipper' => array(
					'code_postal' => Tools::getValue('fromPostalCode'),
					'ville' => Tools::getValue('fromCity'),
					'adresse' => Tools::getValue('fromAddr'),
					'pays' => 'FR',
					'type' => 'entreprise'
				),
				'weight' => $weight_item,
				'dimensions' => isset($dimensions[0]) ? $dimensions[0] : false,
				'cart' => null,
				'cartValue' => $item[0]['price'],
				'currency' => $currency,
				'id_lang' => $cookie->id_lang,
				'cartObject' => $cart_object,
				'additionalCost' => 0,
				'dbCart' => array('delivery_option' => Tools::jsonEncode(array()))
			));
			if (isset($offers['isError']) && $offers['isError'] == 1)
			{
				$error = 1;
				$error_msg = $offers['message'];
			}
			elseif (count($offers) == 0)
			{
				$error = 1;
				$error_msg = $this->l('No offers found for your research');
			}
			else
			{
				$error = 0;
				$out_offers = array();
				foreach ($offers as $offer)
				{
					$offer['characteristics'] = '<b>-</b>'.implode('<br /><b>-</b>	', $offer['characteristics']);
					$out_offers[] = $offer;
				}
				$smarty->assign('offers', $out_offers);
			}
			$smarty->assign('postData', $_POST);
		}
		$smarty->assign('errorMsg', $error_msg);
		$smarty->assign('isError', $error);
		return $this->display(__FILE__, '/views/templates/admin/tests.tpl');
	}

	/**
	 * Displays table with EnvoiMoinsCher orders on the backoffice.
	 * @access public
	 * @return Smarty Display Smarty template.
	 */
	public function ordersTable()
	{
		$smarty = $this->getContext()->smarty;
		$cookie = $this->getContext()->cookie;
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		$params = array();

		// init pagers
		require_once('lib/Pager.php');

		//add language to params
		$params['lang'] = $cookie->id_lang;

		//add filters
		if (Tools::isSubmit('type_order'))
			$params['filterBy']['type_order'] = Tools::getValue('type_order');
		else
			$params['filterBy']['type_order'] = $config['EMC_FILTER_TYPE_ORDER'];
			
		if (Tools::isSubmit('filter_id_order'))
			$params['filterBy']['filter_id_order'] = (int)Tools::getValue('filter_id_order');
			
		if (Tools::isSubmit('status'))
			$params['filterBy']['status'] = Tools::getValue('status');
		else
			$params['filterBy']['status'] = explode(';', $config['EMC_FILTER_STATUS']);
			
		if (Tools::isSubmit('carriers'))
			$params['filterBy']['carriers'] = Tools::getValue('carriers');
		else
			$params['filterBy']['carriers'] = $config['EMC_FILTER_CARRIERS'];

		if (Tools::isSubmit('start_order_date'))
			$params['filterBy']['start_order_date'] = Tools::getValue('start_order_date');
		else
			if ($config['EMC_FILTER_START_DATE'] != 'all')
				$params['filterBy']['start_order_date'] = date('Y-m-d', strtotime('-1 '.$config['EMC_FILTER_START_DATE']));
			
		if (Tools::isSubmit('end_order_date'))
			$params['filterBy']['end_order_date'] = Tools::getValue('end_order_date');
		
		if (Tools::isSubmit('recipient'))
		{
			$words = explode(' ', trim(Tools::getValue('recipient')));
			foreach ($words as $key => $value)
				$params['filterBy']['recipient'][$key] = $value;
		}

		// generate filter url
		$filter_url = '&type_order='.$params['filterBy']['type_order']
								 .(isset($params['filterBy']['filter_id_order'])?'&filter_id_order='.$params['filterBy']['filter_id_order']:'')
								 .'&carriers='.$params['filterBy']['carriers']
								 .(isset($params['filterBy']['start_order_date'])?'&start_order_date='.$params['filterBy']['start_order_date']:'')
								 .(isset($params['filterBy']['end_order_date'])?'&end_order_date='.$params['filterBy']['end_order_date']:'')
								 .(isset($params['filterBy']['recipient'])?'&recipient='.implode('+',$params['filterBy']['recipient']):'');
		if (isset($params['filterBy']['status']) && is_array($params['filterBy']['status']))
			foreach ($params['filterBy']['status'] as $key => $value)
				$filter_url .= '&status[]='.$value;

		// get orders
		$orders_count = $this->model->getEligibleOrdersCount($params);

		$page = 1;
		if (Tools::isSubmit('p'))
			$page = (int)Tools::getValue('p');

		$per_page = 20;
		$limits = array();

		$pager = new Pager(array(
			'url' => 'index.php?controller=AdminEnvoiMoinsCher',
			'tag' => 'p',
			'before' => 5,
			'after' => 5,
			'all' => $orders_count,
			'page' => $page,
			'perPage' => $per_page
		));
		$start = ($page - 1) * $per_page;
		$limits = 'LIMIT '.(int)$start.','.(int)$per_page;
		$smarty->assign('pager', $pager->setPages());

		$orders = $this->model->getEligibleOrders($params, $limits);
		
		//get enabled carriers
		$sql = 'SELECT id_carrier, name FROM '._DB_PREFIX_.'carrier WHERE deleted=0';
		$enabled_carriers = Db::getInstance()->ExecuteS($sql);
		
		// all orders to send
		$planning = $this->model->getLastPlanning();
		$orders_to_send = Tools::jsonDecode($planning['orders_eopl'], true);
		$param_config = '&configure=envoimoinscher&tab_module=shipping_logistics&module_name=envoimoinscher&EMC_tabs=settings';
		$smarty->assign('filters', $params['filterBy']);
		$smarty->assign('filterUrl', $filter_url);
		$smarty->assign('tab_news', $this->model->getApiNews($this->ws_name, $this->version));
		$smarty->assign('tpl_news', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/news.tpl');
		$smarty->assign('local_fancybox', $this->useLocalFancybox());
		$smarty->assign('local_bootstrap', $this->useLocalBootstrap());
		$smarty->assign('emcBaseDir', _MODULE_DIR_.'/envoimoinscher/');
		$smarty->assign('tokenOrder', Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$cookie->id_employee));
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('orders', $orders);
		$smarty->assign('ordersCount', count($orders));
		$smarty->assign('ordersTodo', count($orders_to_send['todo']));
		$smarty->assign('defaultStatus', $config['EMC_CMD']);
		$smarty->assign('withCheck', $config['EMC_MASS'] == EnvoimoinscherModel::WITH_CHECK);
		$smarty->assign('showTable', count($orders) > 0);
		$smarty->assign('successSend', (int)$cookie->success_send);
		$smarty->assign('errorLabels', (int)$cookie->error_labels);
		$smarty->assign('pagerTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/pager_template.tpl');
		$smarty->assign('submenuTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/order_submenu_template.tpl');
		$smarty->assign('ordersTableTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/orders_table_template.tpl');
		$smarty->assign('massTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/massOrders.tpl');
		$smarty->assign('ordersSendTop', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/table_send.tpl');
		$smarty->assign('ordersSendBottom', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/table_send.tpl');
		$smarty->assign('states', OrderState::getOrderStates((int)$this->getContext()->language->id));
		$smarty->assign('enabledCarriers', $enabled_carriers);
		$smarty->assign('actual', '');
		$smarty->assign('actual', '');
		$smarty->assign('baseDir', __PS_BASE_URI__);
		$smarty->assign('configPage', $this->link->getAdminLink('AdminModules').$param_config);
		$smarty->assign('normalOrderPassed', $cookie->normal_order_passed);
		$smarty->assign('massOrderPassed', $cookie->mass_order_passed);
		$cookie->normal_order_passed = -1;
		$cookie->mass_order_passed = -1;
		return $this->display(__FILE__, '/views/templates/admin/orders.tpl');
	}

	/**
	 * Initializes mass order process.
	 * @access public
	 * @return void
	 */
	public function initOrder()
	{
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/WebService.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/Quotation.php');
		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
		$helper = new EnvoimoinscherHelper();
		$config = $helper->configArray($this->model->getConfigData());
		$emc_order = new EnvoimoinscherOrder($this->model);
		
		// check if any order has been selected
		if (!Tools::isSubmit('orders'))
			Tools::redirectAdmin($admin_link_base);
			
		if (!Tools::getValue('do') && !Tools::getValue('results') && !Tools::getValue('mide'))
		{
			$orders = Tools::getValue('orders'); // Get orders

			$emc_order->constructOrdersLists($orders, Tools::getValue('typeDb'));

			// redirect to first order to do
			if ($config['EMC_MASS'] == EnvoimoinscherModel::WITH_CHECK || Tools::getValue('type') != 'withEmc')
				Tools::redirectAdmin($admin_link_base.'&option=send&id_order='.(int)$orders[0].'');
			// redirect to orders main page
			Tools::redirectAdmin($admin_link_base);
		}
		elseif ((int)Tools::getValue('do') == 1)
		{
			$result = array(
				'result'	 => 1,
				'doOthers' => 1
			);
			// do order actions
			$emc_order->setOrderId(0);
			$id_order = (int)$emc_order->getOrderId();
			$result['id'] = $id_order;
			if ($id_order > 0)
			{
				$data = $this->model->prepareOrderInfo($emc_order->getOrderId(), $config);
				/* Not needed anymore
				if ($data['order'][0]['shipping_number'] != '')
				{
					$result['result'] = 0;
					$result['shipping_number'] = $data['order'][0]['shipping_number'];
				}
				else
				{*/
					$emc_order->setOrderData($data);
					$emc_order->setPrestashopConfig($this->getModuleConfig());
					$emc_order->setOfferData($this->getOfferToSendPage($data, $helper));
					$result['result'] = (int)$emc_order->doOrder();
				//}
				if (!$emc_order->doOtherOrders())
					$result['doOthers'] = 0;
			}
			else
				$result = array(
					'result'	 => 0,
					'doOthers' => 0
				);
		}
		elseif (Tools::getValue('mode') == 'skip')
		{
			$emc_order->skipOrder((int)Tools::getValue('previous'));
			$emc_order->incrementSkipped();
			$emc_order->updateOrdersLsit();
			Tools::redirectAdmin($admin_link_base.'&id_order='.(int)Tools::getValue('id_order').'&option=send');
			die();
		}
		elseif (Tools::getValue('results') == 1)
		{
			$result = $emc_order->getFinalResult('array');
			$emc_order->cleanOrders(true);
		}
		ob_end_clean();
		echo Tools::jsonEncode($result);
		die();
	}

	/**
	 * Cancels mass order.
	 * @access public
	 * @return void
	 */
	public function cancelOrder()
	{
		$emc_order = new EnvoimoinscherOrder($this->model);
		$emc_order->cleanOrders();
		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
		Tools::redirectAdmin($admin_link_base);
	}

	/**
	 * Displays table with done EnvoiMoinsCher orders.
	 * @access public
	 * @return Smarty Display Smarty template.
	 */
	public function ordersHistoryTable()
	{
		$smarty = $this->getContext()->smarty;
		$cookie = $this->getContext()->cookie;

		// get filters
		$filters = '';
		if (Tools::isSubmit('filter_id_order'))
			$filters['filterBy']['filter_id_order'] = (int)Tools::getValue('filter_id_order');
		if (Tools::isSubmit('carriers'))
			$filters['filterBy']['carriers'] = Tools::getValue('carriers');
		if (Tools::isSubmit('start_order_date'))
			$filters['filterBy']['start_order_date'] = Tools::getValue('start_order_date');
		if (Tools::isSubmit('end_order_date'))
			$filters['filterBy']['end_order_date'] = Tools::getValue('end_order_date');
		if (Tools::isSubmit('start_creation_date'))
			$filters['filterBy']['start_creation_date'] = Tools::getValue('start_creation_date');
		if (Tools::isSubmit('end_creation_date'))
			$filters['filterBy']['end_creation_date'] = Tools::getValue('end_creation_date');
		if (Tools::isSubmit('recipient'))
		{
			$words = explode(' ', trim(Tools::getValue('recipient')));
			foreach ($words as $key => $value)
				$filters['filterBy']['recipient'][$key] = $value;
		}

		// construct filter request
		$sql = '';
		if (!empty($filters['filterBy']))
		{
			//by order id
			if (isset($filters['filterBy']['filter_id_order']))
				$sql .= ' AND o.id_order = '.(int)$filters['filterBy']['filter_id_order'];

			//by carrier
			if (isset($filters['filterBy']['carriers']))
			{
				if ($filters['filterBy']['carriers'] == 'del')
					$sql .= ' AND c.name NOT IN (SELECT name FROM '._DB_PREFIX_.'carrier WHERE deleted=0)';
				else if ($filters['filterBy']['carriers'] != 'all')
					$sql .= ' AND c.name LIKE "'.pSQL($filters['filterBy']['carriers']).'"';
			}

			//by order date
			if (isset($filters['filterBy']['start_order_date']))
				$sql .= " AND eo.date_order_eor >= STR_TO_DATE('".pSQL($filters['filterBy']['start_order_date'])."', '%Y-%m-%d')";

			if (isset($filters['filterBy']['end_order_date']))
				$sql .= " AND eo.date_order_eor < DATE_ADD(STR_TO_DATE('".pSQL($filters['filterBy']['end_order_date'])."', '%Y-%m-%d'), INTERVAL 1 DAY)";

			//by creation date
			if (isset($filters['filterBy']['start_creation_date']))
				$sql .= " AND o.date_add >= STR_TO_DATE('".pSQL($filters['filterBy']['start_creation_date'])."', '%Y-%m-%d')";

			if (isset($filters['filterBy']['end_creation_date']))
				$sql .= " AND o.date_add < DATE_ADD(STR_TO_DATE('".pSQL($filters['filterBy']['end_creation_date'])."', '%Y-%m-%d'), INTERVAL 1 DAY)";

			//by recipient (string contained in company, first name, last name or email)
			if (isset($filters['filterBy']['recipient']) && !empty($filters['filterBy']['recipient']))
			{
				foreach ($filters['filterBy']['recipient'] as $key => $value)
				{
					$sql .= ' AND (INSTR(a.firstname, "'.pSQL($value).'") > 0
						OR INSTR(a.lastname, "'.pSQL($value).'") > 0
						OR INSTR(cr.email, "'.pSQL($value).'") > 0)';
				}
			}
		}

		$smarty->assign('tokenOrder', Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$cookie->id_employee));
		$count_query = Db::getInstance()->ExecuteS(
			'SELECT COUNT(eo.'._DB_PREFIX_.'orders_id_order) AS allCmd
			 FROM '._DB_PREFIX_.'emc_orders eo
			 JOIN '._DB_PREFIX_.'orders o ON eo.'._DB_PREFIX_.'orders_id_order = o.id_order
			 JOIN '._DB_PREFIX_.'address a ON a.id_address = o.id_address_delivery
			 JOIN '._DB_PREFIX_.'carrier c ON c.id_carrier = o.id_carrier
			 JOIN '._DB_PREFIX_.'customer cr ON cr.id_customer = a.id_customer
			 WHERE eo.ref_emc_eor != ""'.$sql);

		// set pager
		$page = 1;
		$per_page = 20;
		$all_pages = $count_query[0]['allCmd'];
		if (Tools::isSubmit('p'))
			$page = (int)Tools::getValue('p');
		require_once('lib/Pager.php');
		$pager = new Pager(array(
			'url' => 'index.php?controller=AdminEnvoiMoinsCher&option=history',
			'tag' => 'p',
			'before' => 5,
			'after' => 5,
			'all' => $all_pages,
			'page' => $page,
			'perPage' => $per_page
		));
		$start = ($page - 1) * $per_page;
		$smarty->assign('pager', $pager->setPages());
		$smarty->assign('page', $page);

		// get EnvoiMoinsCher orders
		$orders = $this->model->getDoneOrders(array('lang' => $cookie->id_lang, 'start' => $start, 'limit' => $per_page, 'filters' => $sql));

		// get additional sql to limit following requests
		$limit = '';
		if (is_array($orders) && !empty($orders))
		{
			$limit .= ' AND '._DB_PREFIX_.'orders_id_order IN (';
			$i = 0;
			foreach ($orders as $key => $value)
			{
				if ($i != 0) $limit .= ',';
				$limit .= '"'.$value[_DB_PREFIX_.'orders_id_order'].'"';
				$i++;
			}
			$limit .= ')';
		}

		// get order ids with generated documents
		//$ordersWithGeneratedDocuments = Db::getInstance()->ExecuteS('SELECT '._DB_PREFIX_.'orders_id_order AS orderIds FROM '._DB_PREFIX_.'emc_documents
		//	 WHERE type_ed = "label" AND generated_ed = 1'.$limit);

		$orders_with_documents = array();
		foreach ($orders as $key => $value)
			if (!in_array($value['id_order'], $orders_with_documents))
				$orders_with_documents[] = $value['id_order'];

		// check if any document is generated
		$order_documents = array();
		if (count($orders_with_documents) > 0)
		{
			$generated_documents = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'emc_documents
				WHERE generated_ed = 1 AND '._DB_PREFIX_.'orders_id_order in ('.implode(',', $orders_with_documents).')');

			// get all documents for each order
			foreach ($generated_documents as $document)
			{
				$id = $document[_DB_PREFIX_.'orders_id_order'];
				if (!isset($order_documents[$id]))
					$order_documents[$id] = array();
				$order_documents[$id][$document['type_ed']] = $document['link_ed'];
			}
		}

		// get order ids to exclude from ajax process
		$no_ajax_order_ids_query = Db::getInstance()->ExecuteS('SELECT eo.'._DB_PREFIX_.'orders_id_order AS orderIds FROM '._DB_PREFIX_.'emc_orders eo
			 JOIN '._DB_PREFIX_.'orders o ON eo.'._DB_PREFIX_.'orders_id_order = o.id_order
			 JOIN '._DB_PREFIX_.'carrier c ON c.id_carrier = o.id_carrier
			 JOIN '._DB_PREFIX_.'emc_services es ON es.ref_carrier = c.id_reference
			 JOIN '._DB_PREFIX_.'emc_operators eop ON eop.code_eo = es.emc_operators_code_eo
			 WHERE eo.ref_emc_eor != ""
			 AND (eo.'._DB_PREFIX_.'orders_id_order IN (
						SELECT '._DB_PREFIX_.'orders_id_order FROM '._DB_PREFIX_.'emc_documents d
							JOIN '._DB_PREFIX_.'orders o ON d.'._DB_PREFIX_.'orders_id_order = o.id_order
							JOIN '._DB_PREFIX_.'carrier c ON c.id_carrier = o.id_carrier
							JOIN '._DB_PREFIX_.'emc_services es ON es.ref_carrier = c.id_reference
							WHERE es.emc_operators_code_eo LIKE "POFR" AND d.type_ed = "remise")
			 OR	eo.'._DB_PREFIX_.'orders_id_order IN (
						SELECT '._DB_PREFIX_.'orders_id_order FROM '._DB_PREFIX_.'emc_documents d
							JOIN '._DB_PREFIX_.'orders o ON d.'._DB_PREFIX_.'orders_id_order = o.id_order
							JOIN '._DB_PREFIX_.'carrier c ON c.id_carrier = o.id_carrier
							JOIN '._DB_PREFIX_.'emc_services es ON es.ref_carrier = c.id_reference
							WHERE es.emc_operators_code_eo NOT LIKE "POFR" AND d.type_ed = "label" AND d.generated_ed = 1)
			 )'.$limit);

		$no_ajax_order_ids = array();
		foreach ($no_ajax_order_ids_query as $key => $value)
			array_push($no_ajax_order_ids, $value['orderIds']);

		//send filter settings back
		if (isset($filters['filterBy']))
		{
			$smarty->assign('filters', $filters['filterBy']);
			$filter_url = '';
			if (isset($filters['filterBy']['filter_id_order']))
				$filter_url .= '&filter_id_order='.$filters['filterBy']['filter_id_order'];
			if (isset($filters['filterBy']['carriers']))
				$filter_url .= '&carriers='.$filters['filterBy']['carriers'];
			if (isset($filters['filterBy']['start_order_date']))
				$filter_url .= '&start_order_date='.$filters['filterBy']['start_order_date'];
			if (isset($filters['filterBy']['end_order_date']))
				$filter_url .= '&end_order_date='.$filters['filterBy']['end_order_date'];
			if (isset($filters['filterBy']['start_order_date']))
				$filter_url .= '&start_creation_date='.$filters['filterBy']['start_creation_date'];
			if (isset($filters['filterBy']['end_order_date']))
				$filter_url .= '&end_creation_date='.$filters['filterBy']['end_creation_date'];
			if (isset($filters['filterBy']['recipient']))
			{
				$filter_url .= '&recipient=';
				$i = 0;
				foreach ($filters['filterBy']['recipient'] as $key => $value)
				{
					if ($i == 0)
						$filter_url .= $value;
					else
						$filter_url .= '+'.$value;
					$i++;
				}
			}
			$smarty->assign('filterUrl', $filter_url);
		}
		
		//get EMC enabled carriers
		$rq = 'SELECT id_carrier, name FROM '._DB_PREFIX_.'carrier WHERE deleted=0 AND external_module_name = "envoimoinscher"';
		$enabled_carriers = Db::getInstance()->ExecuteS($rq);
		$smarty->assign('enabledCarriers', $enabled_carriers);

		$smarty->assign('tab_news', $this->model->getApiNews($this->ws_name, $this->version));
		$smarty->assign('tpl_news', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/news.tpl');
		$smarty->assign('local_fancybox', $this->useLocalFancybox());
		$smarty->assign('local_bootstrap', $this->useLocalBootstrap());
		$smarty->assign('emcBaseDir', _MODULE_DIR_.'/envoimoinscher/');
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('orders', $orders);
		$smarty->assign('allOrders', count($orders));
		$smarty->assign('noAjaxOrderIds', $no_ajax_order_ids);
		//$smarty->assign('labelGeneratedOrderIds', $labelGeneratedOrderIds);
		//$smarty->assign('remiseGeneratedOrderIds', $remiseGeneratedOrderIds);
		$smarty->assign('orderDocuments', $order_documents);
		$smarty->assign('successSend', (int)$cookie->success_send);
		$smarty->assign('errorLabels', (int)$cookie->error_labels);
		$smarty->assign('pagerTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/pager_template.tpl');
		$smarty->assign('ordersTableTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/orders_history_table_template.tpl');
		$smarty->assign('submenuTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/order_submenu_template.tpl');
		$smarty->assign('actual', 'history');
		$smarty->assign('baseDir', __PS_BASE_URI__);
		$cookie->success_send = 0;
		$cookie->error_labels = 0;
		return $this->display(__FILE__, '/views/templates/admin/orders_history.tpl');
	}

	/**
	 * Prepares the page to send a EnvoiMoinsCher shippment command.
	 * @access public
	 * @return Smarty Displays Smarty template.
	 */
	public function send()
	{
		$smarty = $this->getContext()->smarty;
		$cookie = $this->getContext()->cookie;

		$order_id = (int)Tools::getValue('id_order');
		$post_data = $this->model->getPostData($order_id);
		$emc_order = new EnvoimoinscherOrder($this->model);
		$order_stats = $emc_order->getStats();
		$helper = new EnvoimoinscherHelper;
		$data = $this->model->prepareOrderInfo($order_id, $helper->configArray($this->model->getConfigData()));
		if ($data['is_dp'] == 1)
		{
			$url = Envoimoinscher::getMapByOpe($data['code_eo'], Tools::substr($data['order'][0]['offerCode'], 5),
				$data['config']['EMC_CITY'], $data['config']['EMC_POSTALCODE'], $data['config']['EMC_ADDRESS'], 'FR');
			$helper->setFields('depot.pointrelais',
				array('helper' => '<p class="note"><a data-fancybox-type="iframe" target="_blank" href="'.$url.
				'" style="width:1000px;height:1000px;" class="getParcelPoint action_module fancybox">'.$this->l('Get parcel point').'</a><br/>'.
				$this->l('If the popup do not show up : ').'<a target="_blank" href="'.$url.'">'.$this->l('clic here').'</a></p>'));
		}
		else if ($data['is_dp'] == 2)
			$helper->setFields('depot.pointrelais',
				array(
					'type'	 => 'input',
					'helper' => '',
					'hidden' => true
				)
			);
		$url = Envoimoinscher::getMapByOpe(
			$data['code_eo'],
			Tools::substr($data['order'][0]['offerCode'], 5),
			urlencode($data['delivery']['ville']),
			$data['delivery']['code_postal'],
			urlencode($data['delivery']['adresse']), $data['order'][0]['iso_code']);
		$helper->setFields('retrait.pointrelais',
			array('helper' => '<p class="note"><a data-fancybox-type="iframe" target="_blank" href="'.$url.
				'" class="getParcelPoint fancybox action_module">'.$this->l('Get parcel point').'</a><br/>'.
				$this->l('If the popup do not show up : ').'<a target="_blank" href="'.$url.'">'.$this->l('clic here').'</a></p>'));

		// Check if we have data from previous sending try
		//$show_dst_block = false;
		$delivery_info = $post_data['delivery'];
		if (count($delivery_info) > 1)
		{
			//$show_dst_block = true;
			$data['delivery'] = $delivery_info;
		}
		$emc_carrier = isset($data['order'][0]['external_module_name']) && $data['order'][0]['external_module_name'] == $this->name;
		$offer_data = $this->getOfferToSendPage($data, $helper, $post_data);
		$smarty->assign('local_fancybox', $this->useLocalFancybox());
		$smarty->assign('proforma', $offer_data['isProforma']);
		$smarty->assign('proformaData', $offer_data['proforma']);
		$smarty->assign('offer', $offer_data['offer']);
		$smarty->assign('offers', $offer_data['allOffers']);
		$smarty->assign('offersNb', count($offer_data['allOffers']));
		$smarty->assign('installedServices', $offer_data['installedSrv']);
		$smarty->assign('isFound', $offer_data['isFound']);
		$smarty->assign('isEMCCarrier', $emc_carrier);
		$smarty->assign('insuranceHtml', isset($offer_data['insuranceHtml']) ? $offer_data['insuranceHtml'] : '');
		if (!$emc_carrier)
		{
			$data['order'][0]['label_es'] = $data['order'][0]['name'];
			$data['order'][0]['name_eo'] = $data['order'][0]['name'];
		}
		$smarty->assign('orderInfo', $data['order'][0]);
		$smarty->assign('deliveryInfo', $data['delivery']);
		$smarty->assign('shipperInfo', array(
			'country' => 'FR',
			'postalcode' => $data['config']['EMC_POSTALCODE'],
			'city' => $data['config']['EMC_CITY']
		));
		$smarty->assign('orderId', $order_id);
		$smarty->assign('envUrl', $this->environments[$data['config']['EMC_ENV']]['link']);
		$smarty->assign('multiParcel', $data['config']['EMC_MULTIPARCEL'] == 'on');
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('alreadyPassed', $this->isPassed($order_id));
		$smarty->assign('emcBaseDir', _MODULE_DIR_.'/envoimoinscher/');
		$weight = $data['productWeight'];
		$parcels = $post_data['parcels'];
		$parcels_length = count($parcels);
		if ($parcels_length < 2)
		{
			$parcels_length = '';
			$parcels = array();
		}
		else
		{
			$weight = 0;
			foreach ($parcels as $parcel)
				$weight += (float)$parcel;
		}
		$smarty->assign('parcels', $parcels);
		$smarty->assign('baseJs', _PS_JS_DIR_);
		$smarty->assign('dimensions', $data['dimensions']);
		$smarty->assign('parcelsLength', $parcels_length);
		unset($cookie->emc_order_parcels);
		$smarty->assign('adminImg', _PS_ADMIN_IMG_);
		$smarty->assign('baseDirCss', __PS_BASE_URI__);
		$smarty->assign('moduleBaseDir', _PS_MODULE_DIR_.'envoimoinscher/');
		$smarty->assign('showDstBlock', count($delivery_info) > 1 || (!$emc_carrier && !$offer_data['isFound']));
		$smarty->assign('weight', $weight);
		$smarty->assign('tableTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/offersTable.tpl');
		$smarty->assign('notFoundTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/offersNotFound.tpl');
		$smarty->assign('ordersAll', $order_stats['total']);
		$smarty->assign('ordersDone', $order_stats['skipped'] + $order_stats['ok'] + $order_stats['errors']);
		$smarty->assign('orderTodo', $order_stats['total'] - ($order_stats['skipped'] + $order_stats['ok'] + $order_stats['errors']));
		$smarty->assign('nextOrderId', $emc_order->getNextOrderId());
		$smarty->assign('massTemplate', _PS_MODULE_DIR_.'envoimoinscher/views/templates/admin/massOrders.tpl');
		$smarty->assign('checkAssu', ((int)Configuration::get('EMC_ASSU') == 1));
		if ($post_data['emcErrorSend'] == 1 && ($order_stats['total'] == 0 || $emc_order->isErrorType()))
		{
			$smarty->assign('errorMessage', $post_data['emcErrorTxt']);
			$smarty->assign('showErrorMessage', 1);
			$smarty->assign('errorType', 'order');
		}
		elseif (count($offer_data['errors']) > 0)
		{
			$smarty->assign('errorMessage', implode('<br />', $offer_data['errors']));
			$smarty->assign('showErrorMessage', 1);
			$smarty->assign('errorType', 'quote');
		}
		$this->model->removeTemporaryPost($order_id); // Delete post values
		$cookie->normal_order_passed = -1; // show nothing on table page

		$html = '<script type="text/javascript">
								$(function(){
									$(".fancybox").fancybox({
										"width"			: 1000,
										"height"		: 760,
										"autoDimensions": false,
										"autoScale"		: false
									});
								});
							</script>';
		if ((float)$weight == 0)
			$html .= parent::adminDisplayWarning('Your order weight are empty, please check products or enable min weight in the module settings.');

		return $html.$this->display(__FILE__, '/views/templates/admin/send.tpl');
	}

	/**
	 * Gets offer for changed weight.
	 * @access public
	 * @return Displayed template.
	 */
	public function getOffersNewWeight()
	{
		$smarty = $this->getContext()->smarty;
		$order_id = (int)Tools::getValue('id_order');
		$helper = new EnvoimoinscherHelper;
		$data = $this->model->prepareOrderInfo(
			$order_id,
			$helper->configArray($this->model->getConfigData()),
			(float)str_replace(',', '.', Tools::getValue('weight')));
		$data['productWeight'] = (float)str_replace(',', '.', Tools::getValue('weight'));
		// If option 'use content as parcel description' is checked
		if ((int)$data['config']['EMC_CONTENT_AS_DESC'] == 1)
		{
			$category_row = EnvoimoinscherModel::getNameCategory($data['config']['EMC_NATURE']);
			$data['default']['colis.description'] = $category_row;
		}
		$offer_data = $this->getOfferToSendPage($data, $helper, false);
		$smarty->assign('offer', $offer_data['offer']);
		$smarty->assign('isFound', $offer_data['isFound']);
		$smarty->assign('installedServices', $offer_data['installedSrv']);
		$smarty->assign('offers', $offer_data['allOffers']);
		$smarty->assign('offersNb', count($offer_data['allOffers']));
		$smarty->assign('isajax', 1);
		$smarty->assign('modifPrice', 1); // tamplate was reloaded after weight or multi parcel change
		$smarty->assign('adminImg', _PS_ADMIN_IMG_);
		$smarty->assign('orderid', $order_id);
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('isEMCCarrier', $data['order'][0]['external_module_name'] == $this->name);
		if ($offer_data['isFound'])
			return $this->display(__FILE__, '/views/templates/admin/offersTable.tpl');
		else
			return $this->display(__FILE__, '/views/templates/admin/offersNotFound.tpl');
	}

	/**
	 * Commands the shipping offer.
	 * @access public
	 * @return void
	 */
	public function command()
	{
		$cookie = $this->getContext()->cookie;

		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		$order_id = (int)Tools::getValue('id_order');
		$emc_order = new EnvoimoinscherOrder($this->model);
		$stats = $emc_order->getStats();
		$is_mass_order = $stats['total'] > 0;
		$emc_order->setOrderId($order_id);
		$data = $this->model->prepareOrderInfo($emc_order->getOrderId(), $config);
		$emc_order->setPrestashopConfig($this->getModuleConfig());
		$emc_order->setOrderData($data);
		$emc_order->setOfferData($this->getOfferToSendPage($data, $helper));
		$result = $emc_order->doOrder(false);
		if ($is_mass_order && ($result || (!$result && !$emc_order->isErrorType())))
		{
			$emc_order->skipOrder($order_id);
			$emc_order->updateOrdersList();
		}
		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');

		if ($is_mass_order && $emc_order->doOtherOrders())
		{
			if (!$result && $emc_order->isErrorType())
			{
				Tools::redirectAdmin($admin_link_base.'&id_order='.$order_id.'&option=send');
				die();
			}
			// make next order
			$this->model->removeTemporaryPost($order_id);
			$emc_order->setOrderId(0);
			Tools::redirectAdmin($admin_link_base.'&id_order='.$emc_order->getOrderId().'&option=send');
			die();
		}
		elseif ($is_mass_order && !$emc_order->doOtherOrders())
		{
			$this->model->removeTemporaryPost($order_id);
			$cookie->mass_order_passed = 1;
			Tools::redirectAdmin($admin_link_base);
			die();
		}
		elseif (!$is_mass_order)
		{
			$cookie->normal_order_passed = (int)$result;
			if ($result)
			{
				Tools::redirectAdmin($admin_link_base);
				die();
			}
		}
		Tools::redirectAdmin($admin_link_base.'&id_order='.$order_id.'&option=send');
		die();
	}

	/**
	 * Replaces old offer by the new one.
	 * @return Smarty Smarty template.
	 */
	public function replaceOffer()
	{
		$cookie = $this->getContext()->cookie;

		require_once('Env/WebService.php');
		require_once('Env/Quotation.php');
		$order_id = (int)Tools::getValue('id_order');
		$code = explode('_', Tools::getValue('code'));
		if (ctype_alnum($code[0]) && ctype_alnum($code[1]))
		{
			$rows = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'emc_services es
				 JOIN '._DB_PREFIX_.'emc_operators eo ON eo.code_eo = es.emc_operators_code_eo
				 LEFT JOIN '._DB_PREFIX_.'carrier c ON c.id_carrier = es.id_carrier
				 WHERE es.code_es = "'.$code[1].'" AND es.emc_operators_code_eo = "'.$code[0].'"');
			if (count($rows) == 0 || (int)$rows[0]['id_carrier'] == 0)
			{
				// carrier was not found, insert a new carrier (which is deleted)
				$data = array(
					'name'								 => pSQL($rows[0]['label_es'].' ('.$rows[0]['name_eo'].')'),
					'active'							 => 0,
					'is_module'						=> 1,
					'need_range'					 => 1,
					'deleted'							=> 1,
					'range_behavior'			 => 1,
					'shipping_external'		=> 1,
					'external_module_name' => pSQL($this->name)
				);
				$lang_data = array(
					'id_lang' => (int)$cookie->id_lang,
					'delay'	 => pSQL($rows[0]['desc_store_es'])
				);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier', $data, 'INSERT');
				$lang_data['id_carrier'] = (int)Db::getInstance()->Insert_ID();
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_lang', $lang_data, 'INSERT');
				// prestashop standard ...
				$carrier = array('id_carrier' => (int)$lang_data['id_carrier'], 'id_group' => 0);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', $carrier, 'INSERT');
				$rows[0]['id_carrier'] = $lang_data['id_carrier'];

				DB::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services
				SET id_carrier = '.(int)$lang_data['id_carrier'].'
				WHERE id_es = '.(int)$rows[0]['id_es'].'');

			}
			// update carrier for this order
			Db::getInstance()->autoExecute(_DB_PREFIX_.'orders', array('id_carrier' => (int)$rows[0]['id_carrier']), 'UPDATE', 'id_order = '.(int)$order_id);
		}

		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
		Tools::redirectAdmin($admin_link_base.'&option=send&id_order='.$order_id);
	}

	/**
	 * Tracking function.
	 * @return Smarty Smarty template.
	 */
	public function getTracking()
	{
		$smarty = $this->getContext()->smarty;
		$order_id = (int)Tools::getValue('id_order');
		// get tracking informations
		$order = $this->model->getOrderData($order_id);
		$smarty->assign('rows', $this->model->getTrackingInfos($order_id));
		$smarty->assign('order', $order[0]);
		$smarty->assign('isAdmin', true);
		return $this->display(__FILE__, '/views/templates/admin/tracking.tpl');
	}

	/**
	 * Gets offer choosen by customer.
	 * @access public
	 * @param arrat $data Configuration data.
	 * @return array
	 */
	public function getOfferToSendPage($data, $helper, $session_data = array())
	{
		$cookie = $this->getContext()->cookie;

		if (isset($session_data['quote']) && count($session_data['quote']) > 0)
			$quote_data = $session_data['quote'];
		if (isset($session_data['parcels']) && count($session_data['parcels']) > 0)
			$data['parcels'] = $session_data['parcels'];
		require_once('Env/WebService.php');
		require_once('Env/Quotation.php');
		$offers_orders = $this->model->getOffersOrder();
		// EnvoiMoinsCher library
		$cot_cl = new Env_Quotation(
			array(
				'user' => $data['config']['EMC_LOGIN'],
				'pass' => $data['config']['EMC_PASS'],
				'key'	=> $data['config']['EMC_KEY']
			)
		);
		$cot_cl->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$quot_info = array(
			'collecte'		 => $this->setCollectDate(
				array(
					array(
						'j'		=> $data['config']['EMC_PICKUP_J1'],
						'from' => $data['config']['EMC_PICKUP_F1'],
						'to'	 => $data['config']['EMC_PICKUP_T1']
					),
					array(
						'j'		=> $data['config']['EMC_PICKUP_J2'],
						'from' => $data['config']['EMC_PICKUP_F2'],
						'to'	 => $data['config']['EMC_PICKUP_T2']
					)
				)
			),
			'type_emballage.emballage' 	=> Configuration::get('EMC_WRAPPING'),
			'delai'											=> $offers_orders[0]['emcValue'],
			'code_contenu' 							=> $data['config']['EMC_NATURE'],
			'valeur'			 							=> (float)$data['order'][0]['total_products'],
			'module'			 							=> $this->ws_name,
			'version' 									=> $this->local_version
		);

		$cot_cl->setEnv(Tools::strtolower($data['config']['EMC_ENV']));
		$cot_cl->setPerson(
			'expediteur',
			array(
				'pays'				=> 'FR',
				'code_postal' => $data['config']['EMC_POSTALCODE'],
				'ville'			 => $data['config']['EMC_CITY'],
				'type'				=> 'entreprise',
				'adresse'		 => $data['config']['EMC_ADDRESS']
			)
		);
		$cot_cl->setPerson(
			'destinataire',
			array(
				'pays'				=> $data['delivery']['pays'],
				'code_postal' => $data['delivery']['code_postal'],
				'ville'			 => $data['delivery']['ville'],
				'type'				=> $data['delivery']['type'],
				'adresse'		 => $data['delivery']['adresse']
			)
		);
		$cot_cl->setType($data['config']['EMC_TYPE'], $data['parcels']);
		@$cot_cl->getQuotation($quot_info); // Init params for Quotation
		$cot_cl->getOffers(false); // Get Offers
		$is_found = false;
		$final_offer = $proforma_data = array();
		$is_proforma = false;
		// $helper = new EnvoimoinscherHelper;
		foreach ($cot_cl->offers as $o => $offer)
		{
			if ($offer['operator']['code'] == $data['order'][0]['emc_operators_code_eo'] &&
					EnvoimoinscherHelper::constructServiceCodeFromApi($offer['service']['code']) == $data['order'][0]['code_es'])
			{
				// handle session data
				$offer['priceIns'] = (float)isset($offer['insurance']) ? $offer['insurance']['tax-exclusive'] : 0;
				$offer['priceHTNoIns']	= $offer['price']['tax-exclusive'];
				$offer['priceTTCNoIns'] = $offer['price']['tax-inclusive'];
				$offer['priceHT']			 = $offer['price']['tax-exclusive'] + $offer['priceIns'];
				$offer['priceTTC']			= $offer['price']['tax-inclusive'] + $offer['priceIns'];
				$offer['insurance']		 = $data['default']['assurance.selection'];

				$offer['collect']			 = date('d-m-Y', strtotime($offer['collection']['date']));
				$offer['delivery']			= date('d-m-Y', strtotime($offer['delivery']['date']));
				foreach ($offer['mandatory'] as $mandatory)
				{
					// special case : Chronopost (we have to pass an parameter for parcel point)
					$default_info = '';
					if (isset($quote_data[$mandatory['code']]))
						$default_info = $quote_data[$mandatory['code']];
					elseif (isset($data['default'][$mandatory['code']]))
						$default_info = $data['default'][$mandatory['code']];
					$field_type = 'text';
					if ($mandatory['code'] == 'depot.pointrelais' || $mandatory['code'] == 'retrait.pointrelais')
					{
						if (strpos($default_info, '-') !== false)
						{
							$data_def = explode('-', $default_info);
							if (count($data_def) > 1)
								$info = $data_def[count($data_def) - 1];
							else
								$info = $data_def[1];
							$default_info = $info;
						}
						if (!isset($mandatory['array'][0]) || preg_match('/POST/i', $mandatory['array'][0]))
							$field_type = 'hidden';
					}

					$offer['output'][] = $helper->prepareMandatory($mandatory, $default_info, $field_type, true);
				}
				if (isset($offer['mandatory']['proforma.description_en']) && count($offer['mandatory']['proforma.description_en']) > 0)
				{
					$is_proforma = true;
					$proforma_data = $data['proforma'];
					$session_proforma = Tools::jsonDecode($cookie->emc_order_proforma, true);
					if (isset($session_proforma[1]) && count($session_proforma[1]) > 1)
						$proforma_data = $session_proforma;
				}
				$assurance_html = array();
				if (isset($offer['options']['assurance']['parameters']))
				{
					foreach ($offer['options']['assurance']['parameters'] as $a => $assurance)
					{
						$default = '';
						if (isset($session_data['quote'][$a]))
							$default = $session_data['quote'][$a];
						$helper->putNewInsuranceChoice($a, $assurance['values']);
						$mandatory = array('code' => $a, 'label' => $assurance['label']);
						$assurance_html[] = $helper->prepareMandatory($mandatory, $default, 'select');
					}
				}
				$offer['insuranceHtml'] = $assurance_html;
				$is_found = true;
				$final_offer = $offer;
				break;
			}
			$cot_cl->offers[$o]['code'] = $offer['operator']['code'].'_'.$offer['service']['code'];
		}
		$all_offers = array();
		$installed_srv = array();
		if (!$is_found)
		{
			$all_offers = $cot_cl->offers;
			$services = Db::getInstance()->ExecuteS('SELECT CONCAT_WS("_", emc_operators_code_eo, code_es) AS offerCode FROM '._DB_PREFIX_.'emc_services');
			foreach ($services as $service)
				$installed_srv[] = $service['offerCode'];
		}
		$errors = array();
		if ($cot_cl->curl_error || $cot_cl->resp_error)
		{
			if ($cot_cl->curl_error_text != '')
				$errors[] = $cot_cl->curl_error_text;
			foreach ($cot_cl->resp_errors_list as $error)
				$errors[] = $error['message'];
		}
		unset($cookie->emc_order_data);
		unset($cookie->emc_order_proforma);
		unset($cookie->emc_delivery_contact);
		return array(
			'offer' => $final_offer,
			'allOffers' => $all_offers,
			'errors' => $errors,
			'installedSrv' => $installed_srv,
			'isFound' => (bool)$is_found,
			'isProforma' => $is_proforma,
			'proforma' => $proforma_data);
	}

	/**
	 * Make collect date. We can't collect on Sunday.
	 * @access public
	 * @var array $delays Delays array.
	 * @return String Collect date.
	 */
	public function setCollectDate($delays)
	{
		$today = strtotime('Today');
		$time = strtotime(date('Y-m-d H:i'));

		foreach ($delays as $delay)
		{
			if ((int)$delay['to'] != 24)
				$time_to = strtotime(date('Y-m-d', $today).' '.(int)$delay['to'].':00');
			else
				$time_to = strtotime('Tomorrow');

			if ($time >= strtotime(date('Y-m-d', $today).' '.(int)$delay['from'].':00') && $time < $time_to)
			{
				$days_delay = $delay['j'];
				break;
			}
		}

		if (!isset($days_delay))
			$days_delay = (int)$delays[1] + 1;

		$result = strtotime('+'.$days_delay.'days', $time);

		if (date('N', $result) == 7)
			$result = strtotime('+1 day', $result);

		return date('Y-m-d', $result);
	}

	/**
	 * Returns missed fields list.
	 * @param array $obligatory List with obligatory fields.
	 * @param array $values Values to check.
	 * @return array Empty array or with labels of missed fields.
	 */
	private function makeMissedList($obligatory, $values)
	{
		$missed = array();
		$dictionnary = array(
			'EMC_KEY' => $this->l('the API key'),
			'EMC_LOGIN' => $this->l('the EnvoiMoinsCher login'),
			'EMC_PASS' => $this->l('the EnvoiMoinsCher password'),
			'EMC_FNAME' => $this->l('the first name'),
			'EMC_LNAME' => $this->l('the name'),
			'EMC_COMPANY' => $this->l('the company'),
			'EMC_ADDRESS' => $this->l('the address'),
			'EMC_POSTALCODE' => $this->l('the postal code'),
			'EMC_CITY' => $this->l('the town'),
			'EMC_TEL' => $this->l('the phone number'),
			'EMC_MAIL' => $this->l('the email address'),
			'EMC_PICKUP' => $this->l('the pickup day'));
		foreach ($values as $k => $value)
			if (in_array($k, $obligatory) && trim($value) == '')
				$missed[] = $dictionnary[$k];
		return $missed;
	}

	/**
	 * Returns false to not display carrier like a Prestashop carrier. These carriers are displayed by
	 * hookExtraCarrier method.
	 * @param array $ref List with cart data.
	 * @param float $shipping_cost Cost of shipping.
	 * @access public
	 * @return false
	 */
	public function getOrderShippingCost($ref, $shipping_cost)
	{
		$cookie = $this->getContext()->cookie;
		$cart_context = $this->getContext()->cart;

		$cache_code = 'cache_'.$this->id_carrier.spl_object_hash($ref);

		// cache of shipping cost
		if (isset(Envoimoinscher::$cache[$cache_code]))
			return Envoimoinscher::$cache[$cache_code];

		if (Configuration::get('EMC_SRV_MODE') == EnvoimoinscherModel::MODE_CONFIG)
			return false;

		// for backoffice orders
		$pricing_code = '';
		if (isset($cart_context))
			$pricing_code = EnvoimoinscherHelper::getPricingCode($cart_context);
		else
			return;

		// ajax page : get carrier pricing from database
		$price_row = Db::getInstance()->getRow('SELECT *
			 FROM `'._DB_PREFIX_.'emc_api_pricing`
			WHERE `id_ap` = "'.pSQL($pricing_code).'" ');
		$update = false;

		if ($price_row !== 'false')
		{
			$date_eap_timestamp = strtotime($price_row['date_eap']);

			$query = 'SELECT `date_upd` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "PS_SHIPPING_HANDLING" ';
			$date_upd_cfg_timestamp = strtotime(DB::getInstance()->getValue($query));

			$update = $date_eap_timestamp < $date_upd_cfg_timestamp || $date_upd_cfg_timestamp === false;
		}

		//if prices not found in emc prices table
		if ($price_row === false || $update === true)
		{
			$addresses_array = array();
			$addresses_array[] = (int)$ref->id_address_delivery;
			require_once('Env/WebService.php');
			require_once('Env/Quotation.php');
			// get informations about the current order
			$cart_data = $this->prepareQuotationCartData($ref->id, $ref->id_address_delivery);

			if (empty($cart_data['address']) && $ref->id_address_delivery != '')
			{
				$address = new Address($ref->id_address_delivery);
				$country = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'country WHERE id_country = "'.$address->id_country.'"');

				$cart_data['address']['type'] = 'particulier';
				$cart_data['address']['country'] = $country['iso_code'];
				$cart_data['address']['postcode'] = $address->postcode;
				$cart_data['address']['street'] = $address->address1;
				$cart_data['address']['city'] = $address->city;
				$cart_data['address']['id_zone'] = $country['id_zone'];
			}

			// get dimensions
			$dimensions = $this->model->getDimensionsByWeight($cart_data['weight']);

			if (count($dimensions) === 0)
				return false;

			// call API
			$db_cart = $this->model->prepareCart($cart_context->id);
			$db_cart['current_address'] = $ref->id_address_delivery;
			// if singleton is not loaded
			if (self::$api_results === false)
			{
				$api_results = $this->makeApiCall(array(
					'weightConverted' => true,
					'address'				 => $cart_data['address'],
					'weight'					=> $cart_data['weight'],
					'dimensions'			=> $dimensions[0],
					'cart'						=> (int)$ref->id,
					'cartValue'			 => $ref->getOrderTotal(true, $cart_data['typeCart']),
					'currency'				=> $cart_data['idCurrency'],
					'id_lang'				 => isset($cookie->id_lang) ? (int)$cookie->id_lang : '',
					'cartObject'			=> $ref,
					'additionalCost'	=> $cart_data['additionalCost'],
					'dbCart'					=> $db_cart,
					'pricingCode'		 => $pricing_code,
				));
				self::$api_results = $api_results;
			}

			$offers = self::$api_results;

			if (isset($offers[$this->id_carrier]))
			{
				$sql_offers = array_map('pSQL', $offers[$this->id_carrier]['pricingData']);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_api_pricing', $sql_offers, 'INSERT IGNORE');
				$return = $offers[$this->id_carrier]['priceHT_db'];
			}
			// for relay points if free delivery
			elseif (isset($offers[$ref->id_carrier]))
			{
				$sql_offers = array_map('pSQL', $offers[$ref->id_carrier]['pricingData']);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_api_pricing', $sql_offers, 'INSERT IGNORE');
				$return = $offers[$ref->id_carrier]['priceHT_db'];
			}
			elseif (count($offers) === 0)
			{
				$error_message = sprintf($this->l('No offers found for the address %1$s %2$s, %3$s %4$s %5$s, the cart weighs %6$s kg'),
					$cookie->customer_firstname,
					$cookie->customer_lastname,
					$cart_data['address']['street'],
					$cart_data['address']['postcode'],
					$cart_data['address']['country'],
					$cart_data['weight']);
				if ((int)Configuration::get('EMC_ENABLED_LOGS') == 1)
					Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$error_message, 4);
				$return = false;
			}
			else
				$return = false;
		}
		else
		{
			$prices = Tools::jsonDecode($price_row['carriers_eap'], true);
			if (!isset($prices[$ref->id_address_delivery]) || !isset($prices[$ref->id_address_delivery][$this->id_carrier]))
				$return = false;
			else
				$return = $prices[$ref->id_address_delivery][$this->id_carrier]['price_ht'];
		}
		Envoimoinscher::$cache[$cache_code] = $return;

		return $return;
	}

	public function getPackageShippingCost($r, $s, $products = null)
	{
		return $this->getOrderShippingCost($r, 322);
	}

	/**
	 * Returns false to not display carrier like a Prestashop carrier. These carriers are displayed by
	 * hookExtraCarrier method.
	 * @param array $ref List with cart data.
	 * @param float $shipping_cost Cost of shipping.
	 * @access public
	 * @return false
	 */
	public function getOrderShippingCostExternal($ref)
	{
		return $this->getOrderShippingCost($ref, 322);
	}

	/**
	 * Checks user credentials.
	 * @param array $data List of parameters.
	 * @return int 1 if the credentials are valid, 0 if not.
	 */
	private function checkCredentials($data)
	{
		// always check the credentials and notify user if they aren't valid
		$options = array(
			CURLOPT_URL => $this->environments[$data['apiEnv']]['link'].'/verifier_utilisateur.html',
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => http_build_query(array(
				'login' => $data['apiLogin'],
				'key' => $data['apiKey'],
				'store' => $_SERVER['HTTP_HOST'],
				'platform' => 'Prestashop',
				'tested' => date('Y-m-d H:i:s'))),
				CURLOPT_RETURNTRANSFER => 1
		);
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = 0;
		$req = curl_init();
		curl_setopt_array($req, $options);
		$result = curl_exec($req);
		curl_close($req);
		$return = 1;
		// api connection problem was detected (wrong credentials)
		if (trim($result) != 1)
			$return = 0;
		return $return;
	}

	/**
	 * Load all available carriers
	 */
	public function loadAllCarriers($ajax = true)
	{
		$result = array();
		// on verifie qu'on est bien en mode de configuration
		if (Configuration::get('EMC_SRV_MODE') != EnvoimoinscherModel::MODE_CONFIG)
		{
			if ($ajax)
			{
				ob_end_clean();
				echo $this->l('Your module must be in offline mode.');
				die();
			}
			else
				return false;
		}

		// on recupere les services present
		$bd = Db::getInstance();
		$sql_srv = 'SELECT * FROM '._DB_PREFIX_.'emc_services';
		$sql_ope = 'SELECT * FROM '._DB_PREFIX_.'emc_operators';
		$services = $bd->ExecuteS($sql_srv);
		$operators = $bd->ExecuteS($sql_ope);
		//$srv_save = $services;
		//$ope_save = $operators;

		// on recupere les services depuis le serveur envoimoinscher
		require_once('Env/WebService.php');
		require_once('Env/CarriersList.php');
		$login = Configuration::get('EMC_LOGIN');
		$pass = Configuration::get('EMC_PASS');
		$key = Configuration::get('EMC_KEY');
		$env = Configuration::get('EMC_ENV');
		$lib = new Env_CarriersList(array('user' => $login, 'pass' => $pass, 'key' => $key));
		$lib->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$lib->setEnv(Tools::strtolower($env));
		$lib->getCarriersList($this->ws_name, $this->version);

		
		if ($lib->curl_error)
		{
			if ($ajax)
			{
				ob_end_clean();
				echo $this->l('Error while updating your offers : ');
				foreach ($lib->resp_errors_list as $message)
					echo '<br />'.$message['message'];
				die();
			}
			else
				return false;
		}
		else if ($lib->resp_error)
		{
			if ($ajax)
			{
				ob_end_clean();
				echo $this->l('Error while updating your offers : ');
				foreach ($lib->resp_errors_list as $message)
					echo '<br />'.$message['message'];
				die();
			}
			else
				return false;
		}
		
		$ope_no_change = array();
		$ope_to_delete = array();
		$ope_to_update = array();
		$ope_to_insert = array();
		$srv_no_change = array();
		$srv_to_delete = array();
		$srv_to_update = array();
		$srv_to_insert = array();

		$op_found = -1;
		$srv_found = -1;

		// on tri les transporteurs a rajouter, supprimer, modifier et ceux restant identique
		$last_ope_seen = ''; // on evite les doublons
		foreach ($lib->carriers as $carrier)
		{

			// operateur trouve, on regarde s'il est different
			if ($last_ope_seen != $carrier['ope_code'])
			{
				$last_ope_seen = $carrier['ope_code'];
				// on compare l'operateur avec celui de la liste
				$op_found = -1;
				foreach ($operators as $id => $operator)
				{
					if ($operator['code_eo'] == $carrier['ope_code'])
					{
						$op_found = $id;
						if ($operator['name_eo'] != $carrier['ope_name'])
							$ope_to_update[count($ope_to_update)] = $carrier;
						else
							$ope_no_change[count($ope_no_change)] = $carrier;
						break;
					}
				}
				if ($op_found == -1)
					$ope_to_insert[count($ope_to_insert)] = $carrier;
				else
					unset($operators[$op_found]);
			}

			// on compare le service avec celui de la liste
			$srv_found = -1;
			foreach ($services as $id => $service)
			{
				if ($service['emc_operators_code_eo'] == $carrier['ope_code'] && $service['code_es'] == $carrier['srv_code'])
				{
					$srv_found = $id;
					// service trouve, on regarde s'il est different
					if ($service['label_es'] != $carrier['srv_name'] ||
							$service['desc_es'] != $carrier['label_store'] ||
							$service['desc_store_es'] != $carrier['description'] ||
							$service['label_store_es'] != $carrier['description_store'] ||
							$service['is_parcel_pickup_point_es'] != $carrier['parcel_pickup_point'] ||
							$service['is_parcel_dropoff_point_es'] != $carrier['parcel_dropoff_point'] ||
							$service['family_es'] != $carrier['family'] ||
							$service['type_es'] != $carrier['zone'])
						$srv_to_update[] = $carrier;
					else
						$srv_no_change[] = $carrier;
					break;
				}
			}
			if ($srv_found == -1)
				$srv_to_insert[] = $carrier;
			else
				unset($services[$srv_found]);
		}

		$srv_to_delete = $services;
		$ope_to_delete = $operators;
		
		// On met à jour la base
		// Requête insert services
		$query = array();
		$sql = '';
		$first_line = true;
		if (count($srv_to_insert) > 0)
		{
			
			$sql = 'INSERT INTO '._DB_PREFIX_.'emc_services VALUES';
			foreach ($srv_to_insert as $service)
			{
				if (!$first_line)
					$sql .= ',';
				$first_line = false;
				$sql .= '(null,0,0,"'.pSQL($service['srv_code']).
															'","'.pSQL($service['ope_code']).
															'","'.pSQL($service['srv_name']).
															'","'.pSQL($service['label_store']).
															'","'.pSQL($service['description']).
															'","'.pSQL($service['description_store']).
															'",0,'.(int)$service['parcel_pickup_point'].
															','.(int)$service['parcel_dropoff_point'].
															','.(int)$service['family'].
															','.(int)$service['zone'].
															',1)';
			}
			$sql .= ';';
			$query[] = $sql;
		}

		// Requête insert opeateurs
		if (count($ope_to_insert) > 0)
		{
			$sql = 'INSERT INTO '._DB_PREFIX_.'emc_operators VALUES';
			$first_line = true;
			foreach ($ope_to_insert as $operator)
			{
				if (!$first_line)
					$sql .= ',';
				$first_line = false;
				$sql .= '(null,"'.pSQL($operator['ope_name']).'","'.pSQL($operator['ope_code']).'")';
			}
			$sql .= ';';
			$query[] = $sql;
		}

		// Requête update services
		foreach ($srv_to_update as $service)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'emc_services SET
										 label_es = "'.pSQL($service['srv_name']).'"
										 ,desc_es = "'.pSQL($service['label_store']).'"
										 ,desc_store_es = "'.pSQL($service['description']).'"
										 ,label_store_es = "'.pSQL($service['description_store']).'"
										 ,price_type_es = 0
										 ,is_parcel_pickup_point_es = '.(int)$service['parcel_pickup_point'].'
										 ,is_parcel_dropoff_point_es = '.(int)$service['parcel_dropoff_point'].'
										 ,family_es = '.(int)$service['family'].'
										 ,type_es = '.(int)$service['zone'].'
										 WHERE code_es = "'.pSQL($service['srv_code']).'"
										 AND emc_operators_code_eo = "'.pSQL($service['ope_code']).'";';
			$query[] = $sql;
		}
		// Requête update operateurs
		foreach ($ope_to_update as $operator)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'emc_operators SET
				 name_eo = "'.pSQL($operator['ope_name']).'" WHERE code_eo = "'.pSQL($operator['ope_code']).'";';
			$query[] = $sql;
		}

		// Requête delete services
		if (count($srv_to_delete) > 0)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'carrier SET deleted = 1 WHERE ';
			$first_line = true;
			foreach ($srv_to_delete as $service)
			{
				if (!$first_line)
					$sql .= ' OR ';
				$first_line = false;
				$sql .= 'id_carrier = '.(int)$service['id_carrier'];
			}
			$sql .= ';';
			$query[] = $sql;
			$sql = 'DELETE FROM '._DB_PREFIX_.'emc_services WHERE ';
			$first_line = true;
			foreach ($srv_to_delete as $service)
			{
				if (!$first_line)
					$sql .= ' OR ';
				$first_line = false;
				$sql .= 'id_es = '.(int)$service['id_es'];
			}
			$sql .= ';';
			$query[] = $sql;
		}
		// Requête delete operateurs
		$first_line = true;
		if (count($ope_to_delete) > 0)
		{
			$sql = 'DELETE FROM '._DB_PREFIX_.'emc_operators WHERE ';
			foreach ($ope_to_delete as $operator)
			{
				if (!$first_line)
					$sql .= ' OR ';
				$first_line = false;
				$sql .= 'id_eo = '.(int)$operator['id_eo'];
			}
			$sql .= ';';
			$query[] = $sql;
		}

		Db::getInstance()->execute('START TRANSACTION;');
		foreach ($query as $q)
		{
			if ($q != '' && Db::getInstance()->execute($q) === false)
			{
				if ((int)Configuration::get('EMC_ENABLED_LOGS') == 1)
					Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$this->l('Update : Error while updating your offers : ').$q);

				if ($ajax)
				{
					Db::getInstance()->execute('ROLLBACK;');
					ob_end_clean();
					echo $this->l('Error while updating your offers : ').$q;
					die();
				}
				else
					return false;
			}
		}
		Db::getInstance()->execute('COMMIT;');

		$result = array();
		$result['offers_added'] = array();
		$result['offers_updated'] = array();
		$result['offers_deleted'] = array();
		foreach ($srv_to_insert as $service)
			$result['offers_added'][count($result['offers_added'])] = $service['srv_name'];
		foreach ($srv_to_update as $service)
			$result['offers_updated'][count($result['offers_updated'])] = $service['srv_name'];
		foreach ($srv_to_delete as $service)
			$result['offers_deleted'][count($result['offers_deleted'])] = $service['label_es'];

		$date = new DateTime();
		Configuration::updateValue('EMC_LAST_CARRIER_UPDATE', $date->format('Y-m-d'));

		if ($ajax)
		{
			ob_end_clean();
			echo Tools::jsonEncode($result);
			die();
		}
		else
			return true;
	}

	/**
	 * Convert a date into string in the selected language
	 * @param $date : date to convert
	 * @param $dateTranslation : array of translation
	 * @return the date in string format
	 */
	public function dateToString($date)
	{
		$date_data = explode('/', strftime('%w/%d/%m/%Y', strtotime($date)));

		return $this->l('day'.$date_data[0]).' '.$date_data[1].' '.$this->l('month'.$date_data[2]).' '.$date_data[3];
	}

	/**
	 * All hooks are grouped here.
	 */

	/**
	 * Show parcel point informations on backoffice.
	 * @access public
	 * @param array $params List of params.
	 * @return mixed Displayed template or nothing.
	 */
	public function hookAdminOrder($params)
	{
		$smarty = $this->getContext()->smarty;
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/includes/EnvoimoinscherHelper.php');
		$helper = new EnvoimoinscherHelper;
		// check if order belongs to module and if the carrier handles parcel points
		$point = $this->model->getPointInfos($params['id_order']);
		if (isset($point['code']))
		{
			$smarty->assign('point', $point);
			$smarty->assign('schedule', $helper->setSchedule($point['schedule']));
		}
		$smarty->assign('multiParcels', ($parcels = $this->model->getParcelsInfos($params['id_order'])));
		$smarty->assign('multiSize', count($parcels));
		return $this->display(__FILE__, '/views/templates/hook/hookAdminOrder.tpl');
	}

	/**
	 * Handle carrier choice. If the carrier must have a parcel points, put the point in the session.
	 * If the point wasn't specified, returns a error. It's called only for standard order mode.
	 * @access public
	 * @param array $params List of order params.
	 * @param bool $redirect Redirect boolean.
	 * @return void
	 */
	public function hookProcessCarrier($params, $redirect = true)
	{
		$cookie = $this->getContext()->cookie;

		$price = 0.0;

		if ($redirect || !$redirect)$price = 0.0;
		$cart_id = (int)$params['cart']->id;
		$error_occured = false;
		$correct_code = true;
		$options = unserialize($params['cart']->delivery_option);

		foreach ($options as $o => $option)
		{
			$ope = $this->model->getCarrierWithPricing($cart_id, $option);
			if (isset($ope) && count($ope) > 0)
			{
				if (isset($ope[0]['point_eap']))
				{
					$codes = explode('-', $ope[0]['point_eap']);
					if ((!isset($codes[0]) || !isset($codes[1])) ||
						(isset($codes[0]) && isset($codes[1]) && (trim($codes[0]) != $ope[0]['emc_operators_code_eo'] || !ctype_alnum(trim($codes[1])))))
						$correct_code = false;
				}
				if (isset($ope[0]) && $ope[0]['is_parcel_pickup_point_es'] == 1 &&
					!$correct_code && Tools::getValue('ajax') != 'true' &&
					Tools::getValue('ajax') != 'true')
				{
					$error_occured = true;
					$error_message = sprintf($this->l('The mandatory parcel point has not been chosen for the cart %s'), $cart_id);
					if ((int)Configuration::get('EMC_ENABLED_LOGS') == 1)
						Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$error_message, 4);
					$variable = 'choosePoint'.$ope[0]['emc_operators_code_eo'].$o;
					$cookie->$variable = 1;
				}
				$prices = Tools::jsonDecode($ope[0]['prices_eap'], true);
				$price = $price + $prices[$o][$ope[0]['id_carrier']];
			}
		}
		if ($error_occured)
		{
			Tools::redirect('order.php?step=2');
			return false;
		}
		Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_api_pricing',
				array('price_eap' => (float)$price),
				'UPDATE', _DB_PREFIX_.'cart_id_cart = '.(int)$cart_id.' ');
		return true;
	}

	/**
	 * Page of order confirmation. If the shipper choosen by user has a parcel point, we need to
	 * insert it into emc_points table.
	 * @access public
	 * @param $params array List of order params.
	 * @return void
	 */
	public function hooknewOrder($params)
	{
		$cookie = $this->getContext()->cookie;

		$cookie->emc_carrier = '';
		// Get cart carrier (if EnvoiMoinsCher, make some supplementary operations)
		$row = $this->model->getCarrierByCartPricing($params['cart']->id);
		// if it's not our carrier, nothing to do here
		if ($row[0]['external_module_name'] !== $this->name)
			return;

		$cookie = $this->getContext()->cookie;
		$cookie->emc_carrier = '';

		if (isset($row[0]['point_eap']) && $row[0]['point_eap'] != '')
		{
			// Insert parcel point informations
			$point = explode('-', $row[0]['point_eap']);
			if (ctype_alnum(trim($point[0])) && ctype_alnum(trim($point[1])) && strpos(trim($point[0]), $row[0]['emc_operators_code_eo']) !== false)
			{
				$data = array(
					_DB_PREFIX_.'orders_id_order' => (int)$params['order']->id,
					'point_ep' => pSQL(trim($point[1])),
					'emc_operators_code_eo' => pSQL(trim($point[0]))
				);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_points', $data, 'INSERT');
			}
		}
		
		Configuration::updateValue('ENVOIMOINSCHER_CONFIGURATION_OK', true);
		return true;
	}

	/**
	 * Handles tracking informations.
	 * @param array $params List of params.
	 * @return void
	 */
	public function hookOrderDetail($params)
	{
		$smarty = $this->getContext()->smarty;
		$cookie = $this->getContext()->cookie;

		require_once(_PS_MODULE_DIR_.'/envoimoinscher/includes/EnvoimoinscherHelper.php');
		$helper = new EnvoimoinscherHelper;
		// get tracking informations
		$rows = $this->model->getTrackingByOrderAndCustomer(Tools::getValue('id_order'), $cookie->id_customer);
		$smarty->assign('rows', $rows);
		$smarty->assign('isAdmin', false);
		$point = $this->model->getPointInfos(Tools::getValue('id_order'));
		$is_point = 0;

		if (is_array($params)) $is_point = 0;
		if (isset($point['code']))
		{
			$is_point = 1;
			$smarty->assign('point', $point);
			$smarty->assign('schedule', $helper->setSchedule($point['schedule']));
		}
		$smarty->assign('showPoint', $is_point);
		echo $this->display(__FILE__, '/views/templates/admin/tracking.tpl');
	}

	/**
	 * When an EnvoiMoinsCher carrier is updated, call this method.
	 * It inserts a new relation between the carrier of Prestashop and the service of
	 * EnvoiMoinsCher.
	 * @param array $params List of params used in the operation.
	 * @return void
	 */
	public function hookupdateCarrier($params)
	{
		// if it's not our carrier, nothing to do here
		if ($params['carrier']->external_module_name !== $this->name)
			return;

		$data = array('id_carrier' => (int)$params['carrier']->id);
		Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_services', $data, 'UPDATE', 'ref_carrier = '.(int)$params['carrier']->id_reference);
	}

	/**
	 * Header's hook. It displays included JavaScript for GoogleMaps API.
	 * @access public
	 * @return Displayed Smarty template.
	 */
	public function hookHeader()
	{
		$smarty = $this->getContext()->smarty;
		$smarty->assign('emcBaseDir', _MODULE_DIR_.'/envoimoinscher/');
		return $this->display(__FILE__, '/views/templates/hook/header_hook.tpl');
	}

	/**
	 * Since Prestashop 1.5, this hook is used to display list of available carriers for each
	 * of order addresses.
	 * @access public
	 * @param array $params Parameters array (cart object, address informations)
	 * @return Display template.
	 */
	public function hookDisplayCarrierList($params)
	{
		$smarty = $this->getContext()->smarty;
		$points = array();
		$point = '';
		$pricing = $this->model->getLastPrices(EnvoimoinscherHelper::getPricingCode($params['cart']));
		if (empty($pricing))
		{
			$this->getOrderShippingCost($params['cart'], 311);
			$pricing = $this->model->getLastPrices(EnvoimoinscherHelper::getPricingCode($params['cart']));
		}
		if (isset($pricing['points_eap']))
		{
			$points = Tools::jsonDecode($pricing['points_eap'], true);
			$point = $pricing['point_eap'];
		}
		$delivery = ($pricing['date_delivery'] == '')?array():Tools::jsonDecode($pricing['date_delivery'], true);
		$cart_data = $this->prepareQuotationCartData($params['cart']->id, $params['address']);

		foreach ($delivery as $key => $value)
			$delivery[$key] = $this->dateToString($value);

		$smarty->assign('point', $point);
		$smarty->assign('points', $points);
		$smarty->assign('delivery', $delivery);
		$smarty->assign('deliveryLabel', Configuration::get('EMC_LABEL_DELIVERY_DATE'));
		$smarty->assign('id_address', $params['address']->id);
		$smarty->assign('destCountry', $cart_data['address']['country']);
		$smarty->assign('offersCount', count($points));
		$smarty->assign('loaderSrc', __PS_BASE_URI__.'img/loader.gif');
		$smarty->assign('baseDir', __PS_BASE_URI__);
		return $this->display(__FILE__, '/views/templates/hook/envoimoinscher_carrier.tpl');
	}

	/**
	 * Checks if EnvoiMoinsCher order was already passed for this prestashop command.
	 * @access private
	 * @param int $order_id Id of order.
	 * @return boolean True if passed, false if not.
	 */
	private function isPassed($order_id)
	{
		$row = Db::getInstance()->ExecuteS('SELECT '._DB_PREFIX_.'orders_id_order FROM '._DB_PREFIX_.'emc_orders
			 WHERE '._DB_PREFIX_.'orders_id_order = '.(int)$order_id.'');
		$result = false;
		if (isset($row[0]) && $row[0][_DB_PREFIX_.'orders_id_order'] != '')
			$result = true;
		return $result;
	}

	/**
	 * Get parcel points from a carrer and address (passed in get values)
	 * @access public
	 * @return void
	 */
	public function getPoints()
	{
		require_once(_PS_MODULE_DIR_.$this->name.'/Env/WebService.php');
		require_once(_PS_MODULE_DIR_.$this->name.'/Env/ParcelPoint.php');
		$smarty = $this->getContext()->smarty;

		// Load the parcel points for the chosen carrier and address
		$helper = new EnvoimoinscherHelper;
		$carrier = (int)Tools::getValue('carrier');
		$address_id = (int)Tools::getValue('addressId');
		$env_cl = new Envoimoinscher;
		$config = $helper->configArray(Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'configuration
			 WHERE name LIKE "EMC_%"'));
		$poi_cl = new Env_ParcelPoint(array(
			'user' => $config['EMC_LOGIN'],
			'pass' => $config['EMC_PASS'],
			'key' => $config['EMC_KEY']));
		$poi_cl->setPlatformParams($env_cl->ws_name, _PS_VERSION_, $env_cl->version);
		$poi_cl->setEnv(Tools::strtolower($config['EMC_ENV']));

		$poi_cl->construct_list = true;
		foreach (explode(',', Tools::getValue('points')) as $point)
			if (Tools::getValue('country') == '' || ctype_alnum(Tools::getValue('country')))
				$poi_cl->getParcelPoint('dropoff_point', $point, Tools::getValue('country'));

		// add the inputs
		$inputs = array();
		$inputs['address'] = array();
		$inputs['address']['name'] = 'parcelPoints'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['address']['id'] = 'parcelPoints'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['address']['value'] = array();
		$inputs['info'] = array();
		$inputs['info']['name'] = 'parcelInfos'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['info']['id'] = 'parcelInfos'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['info']['value'] = array();
		$inputs['name'] = array();
		$inputs['name']['name'] = 'parcelNames'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['name']['id'] = 'parcelNames'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['name']['value'] = array();
		$inputs['id'] = array();
		$inputs['id']['name'] = 'parcelIds'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['id']['id'] = 'parcelIds'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['id']['value'] = array();
		$inputs['count'] = array();
		$inputs['count']['name'] = 'counter'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['count']['id'] = 'counter'.$carrier.Tools::getValue('ope').$address_id;
		$inputs['count']['value'] = 0;

		// add parcel points
		$points = array();
		$i = 0;
		foreach ($poi_cl->points['dropoff_point'] as $point)
		{
			if ($point['name'] != '')
			{
				$point['checked'] = $point['code'] == Tools::getValue('pointValue');
				$point['js'] = 'selectPr(\''.$point['code'].'\', \''.(int)Tools::getValue('carrier').'\', \''.$address_id.'\');';
				$point['class'] = 'point'.$carrier.$address_id;
				$point['input_name'] = 'point'.$carrier.Tools::getValue('ope').$address_id;
				$point['id'] = 'point'.$carrier.$point['code'].$address_id;

				$inputs['address']['value'][] = $point['address'].', '.$point['zipcode'].' '.$point['city'];
				$inputs['info']['value'][] = implode('<br />', $helper->setSchedule($point['schedule']));
				$inputs['name']['value'][] = $point['name'];
				$inputs['id']['value'][] = $point['code'];
				$points[] = $point;
			}
			$i++;
		}
		if ($i == 0)
			die('noPoint');

		$inputs['address']['value'] = implode('|', $inputs['address']['value']);
		$inputs['info']['value'] = implode('|', $inputs['info']['value']);
		$inputs['name']['value'] = implode('|', $inputs['name']['value']);
		$inputs['id']['value'] = implode('|', $inputs['id']['value']);

		$smarty->assign('points', $points);
		$smarty->assign('inputs', $inputs);

		echo $this->display(__FILE__, '/views/templates/front/get_points.tpl');
		die();
	}

	/**
	 * Sets parcel point in the database.
	 * @access public
	 * @param string $post_point Id of choosen parcel point.
	 * @return void
	 */
	public function setPoint($post_point)
	{
		$cookie = $this->getContext()->cookie;

		$point = explode('-', $post_point);
		if (ctype_alnum(trim($point[0])) && ctype_alnum(trim($point[1])))
		{
			$data = array(
				'point_eap' => pSQL(trim($post_point))
			);
			Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_api_pricing', $data, 'UPDATE', _DB_PREFIX_.'cart_id_cart = '.(int)$cookie->id_cart.'');
		}
	}

	/**
	 * Makes API call and returns transport offers (function used by command in normal [5 steps] and
	 * one page checkout mode).
	 * @access public
	 * @params array $params Array of params used to obtain the cotation.
	 * @return array List with offers.
	 */
	public function makeApiCall($params)
	{
		$cookie = $this->getContext()->cookie;
		$currency = new Currency((int)$params['cartObject']->id_currency);
		$carrier_tax = 0;

		// If no address selected, we set the default one
		if ((empty($params['address']) || count($params['address']) === 0) ||
				((empty($params['address']['city']) || count($params['address']['city']) === 0 || $params['address']['city'] == '') &&
				(empty($params['address']['postcode']) || count($params['address']['postcode']) === 0 || $params['address']['postcode'] == '')))
			$params['address'] = $this->getDefaultAddress();

		// check if call comes from tests()
		if (!isset($params['testPage']))
			$params['testPage'] = false;

		// get configuration data
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		if ((!$params['testPage'] && (isset($config['EMC_SRV_MODE']) &&
				$config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_CONFIG)) ||
				(int)Configuration::get('PS_ALLOW_MULTISHIPPING') == 1)
			return array();
		// check if free delivery
		$configuration = Configuration::getMultiple(array(
			'PS_SHIPPING_FREE_PRICE',
			'PS_SHIPPING_HANDLING',
			'PS_SHIPPING_METHOD',
			'PS_SHIPPING_FREE_WEIGHT'
		));
		// Multi-shipping : get current carrier id
		$options = (array)Tools::jsonDecode($params['dbCart']['delivery_option'], true);
		$default_carrier = 0;
		foreach ($options as $o => $option)
			if ($o == $params['dbCart']['current_address'])
				$default_carrier = $option;
		// Free fees
		$free_delivery = false;
		$free_fees_price = 0;
		if (isset($configuration['PS_SHIPPING_FREE_PRICE']))
			$free_fees_price = Tools::convertPrice(
				(float)$configuration['PS_SHIPPING_FREE_PRICE'],
				$currency
			);
		// Free delivery by cart price
		if ($params['cartValue'] >= (float)$free_fees_price && (float)$free_fees_price > 0)
			$free_delivery = true;
		// Free delivery by cart weight
		if ((isset($params['weightConverted']) && !$params['weightConverted']) || !isset($params['weightConverted']))
			$params['weight'] = EnvoimoinscherHelper::normalizeToKg(Configuration::get('PS_WEIGHT_UNIT'), $params['weight']);
		if (isset($configuration['PS_SHIPPING_FREE_WEIGHT']) &&
			$params['weight'] >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] &&
			(float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0)
			$free_delivery = true;
		$real_weight = $params['weight'];
		if (Configuration::get('PS_WEIGHT_UNIT') == 'g')
			$real_weight = $real_weight * 1000;
		// Supplementary fees
		$shipping_handling = (float)Configuration::get('PS_SHIPPING_HANDLING');

		$dest_type = $params['address']['type'];

		if ((int)Configuration::get('EMC_INDI') == 1)
			$dest_type = 'particulier';

		$all_points_list = $carriers_info = $prices_array = array();
		$all_delivery_dates = array();
		// EnvoiMoinsCher library
		$offers_orders = $this->model->getOffersOrder();
		$cot_cl = new Env_Quotation(
			array(
				'user' => $config['EMC_LOGIN'],
				'pass' => $config['EMC_PASS'],
				'key'	=> $config['EMC_KEY']
			)
		);
		$cot_cl->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$quot_info = array(
			'collecte'		 => $this->setCollectDate(
				array(
					array(
						'j'		=> $config['EMC_PICKUP_J1'],
						'from' => $config['EMC_PICKUP_F1'],
						'to'	 => $config['EMC_PICKUP_T1']
					),
					array(
						'j'		=> $config['EMC_PICKUP_J2'],
						'from' => $config['EMC_PICKUP_F2'],
						'to'	 => $config['EMC_PICKUP_T2']
					)
				)
			),
			'delai'				=> $offers_orders[0]['emcValue'],
			'code_contenu' => $config['EMC_NATURE'],
			'valeur'			 => $params['cartValue'],
			'module'			 => $this->ws_name,
			'version'			=> $this->local_version
		);

		$cot_cl->setEnv(Tools::strtolower($config['EMC_ENV']));

		if (!$params['testPage'])
			$cot_cl->setPerson(
				'expediteur',
				array(
					'pays'				=> 'FR',
					'code_postal' => $config['EMC_POSTALCODE'],
					'ville'			 => $config['EMC_CITY'],
					'type'				=> 'entreprise',
					'adresse'		 => $config['EMC_ADDRESS']
				)
			);
		else
			$cot_cl->setPerson('expediteur', $params['addressShipper']);

		$cot_cl->setPerson(
			'destinataire',
			array(
				'pays'				=> $params['address']['country'],
				'code_postal' => $params['address']['postcode'],
				'ville'			 => $params['address']['city'],
				'type'				=> $dest_type,
				'adresse'		 => $params['address']['street']
			)
		);

		$cot_cl->setType(
			$config['EMC_TYPE'],
			array(
				1 => array(
					'poids'		=> $params['weight'],
					'longueur' => (float)$params['dimensions']['length_ed'],
					'largeur'	=> (float)$params['dimensions']['width_ed'],
					'hauteur'	=> (float)$params['dimensions']['height_ed']
				)
			)
		);

		// get offers with API pricing
		$carriers_zone = array();
		if (isset($params['address']['id_zone']))
			$carriers_zone = $this->model->getEmcCarriersByZone($params['address']['id_zone'], $params['cartObject']->id_lang);
		else
		{
			$id_zone = Db::getInstance()->ExecuteS('SELECT id_zone FROM '._DB_PREFIX_.'country WHERE iso_code = "FR"');
			$params['address']['id_zone'] = $id_zone[0]['id_zone'];
			$carriers_zone = $this->model->getEmcCarriersWithoutZone($params['cartObject']->id_lang);
		}
		$offers_db = $helper->makeCodeKeys($carriers_zone);
		$offers = array();

		// if there is more than 1 operators activated we do a curl multi request
		$offers_quotation = array();
		if (count($offers_db) == 1) // correction needed for quotation
		{
			@$cot_cl->getQuotation($quot_info);
			$cot_cl->getOffers(false);
			$offers_quotation = $cot_cl->offers;
		}
		else
		{
			foreach ($offers_db as $offer_db)
			{
				$quot_info['operateur'] = $offer_db['emc_operators_code_eo'];
				$quot_info['service'] = $offer_db['code_es'];
				$cot_cl->setParamMulti($quot_info);
			}

			@$cot_cl->getQuotationMulti();
			$cot_cl->getOffersMulti();
			$offers_quotation = $cot_cl->offers;
		}

		if ($cot_cl->curl_error)
		{
			if ($params['testPage'])
				$offers = array('isError' => 1, 'message' => $config['EMC_MSG']);
			//$error = 1;
			$error_msg = $config['EMC_MSG']; //$cot_cl->curl_error_text;
		}
		elseif ($cot_cl->resp_error)
		{
			if ($params['testPage'])
				$offers = array('isError' => 1, 'message' => $cot_cl->resp_errors_list[0]['message']);

			//$error = 1;
			$error_msg = $cot_cl->resp_errors_list[0]['message'];
			if ((int)Configuration::get('EMC_ENABLED_LOGS') == 1)
				Logger::addLog('[ENVOIMOINSCHER]['.time().'] '.$this->l('Error while recovering offers : ').$error_msg, 4);
		}
		else
		{
			if (count($offers_quotation) == 0)
			{
				//$error = 1;
				$error_msg = 'Pas d\'offres correspondant à votre recherche';
			}
			else
			{
				//$error = 0;
				$presented = array();

				$classes_css = 0;

				foreach ($offers_quotation as $offer)
				{
					$code = EnvoimoinscherHelper::constructServiceCodeFromApi($offer['operator']['code'].'_'.$offer['service']['code']);
					if (!in_array($code, $presented) && array_key_exists($code, $offers_db))
					{
						$offer_db = $offers_db[$code];
						// %2 == 0 = alnternate_ <= la première ligne grisée avec la bordure
						$classes_alias = '';
						if ($classes_css % 2 == 0)
							$classes_alias = 'alternate_';
						$offer['class'] = $classes_alias;
						// styles : the first line hasn't border-top
						$style = '';
						if ($classes_css == 0)
							$style = 'border-top:none;';
						$offer['style'] = $style;
						$classes_css++;
						// radio checked : get carrier directly from cart table and compare with API response
						$offer['checked'] = '';
						if (isset($params['dbCart']) && $offer_db['id_carrier'] == $default_carrier)
							$offer['checked'] = 'checked="checked"';
						if (($free_delivery && !in_array($offer_db['id_es'], $config['EMC_NO_FREESHIP'])))
						{
							$offer['priceHT_db'] = 0;
							$offer['priceHT'] = 0;
							$offer['priceTTC_db'] = 0;
							$offer['priceTTC_client'] = 0;
						}
						else
						{
							$offer['priceHT_db'] = $offer['price']['tax-exclusive'];
							$offer['priceHT'] = $offer['price']['tax-exclusive'];
							$offer['priceTTC_db'] = $offer['price']['tax-inclusive'];
							$use_taxes = $offer['price']['tax-exclusive'] != $offer['price']['tax-inclusive'];
						}
						$offer['descriptionLocal'] = $offer_db['delay'];
						$offer['offerTitle'] = $offer_db['name'];
						$offer['offerLogo'] = '';
						$offer['pointsList'] = '';
						if (file_exists(_PS_IMG_DIR_.'s/'.$offer_db['id_carrier'].'.jpg'))
							$offer['offerLogo'] = '<img src="'.__PS_BASE_URI__.'img/s/'.$offer_db['id_carrier'].'.jpg" alt="" />';
						if ($offer['delivery']['type'] == 'PICKUP_POINT')
						{
							$offer['isParcelPoint'] = true;
							$offer['pointsList'] = implode(',', $offer['mandatory']['retrait.pointrelais']['array']);
						}
						// If not free delivery, make all pricing operations
						if (!$free_delivery)
						{
							$carrier = new Carrier($offer_db['id_carrier']);
							$carrier_tax = Tax::getCarrierTaxRate((int)$offer_db['id_carrier'], (int)$params['cartObject']->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
							if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
								$is_weight_method = true;
							else
								$is_weight_method = false;
							// For rating price
							// Use Prestashop methods to generate the prices
							// if ($offer_db['emc_type'] == 0)
							if ($offer_db['pricing_es'] == EnvoimoinscherModel::RATE_PRICE)
							{
								// for weight range
								if ($is_weight_method)
								{
									if (Carrier::checkDeliveryPriceByWeight($offer_db['id_carrier'], $real_weight, $params['address']['id_zone']))
									{
										$offer['priceTTC_db'] = $carrier->getDeliveryPriceByWeight($real_weight, $params['address']['id_zone']);
										$offer['priceHT_db'] = $offer['priceTTC_db'];
									}
									else
										continue;
								}
								else
								{
									if (Carrier::checkDeliveryPriceByPrice(
											$offer_db['id_carrier'],
											$params['cartValue'],
											$params['address']['id_zone'],
											$params['cartObject']->id_currency))
									{
										$offer['priceTTC_db'] = $carrier->getDeliveryPriceByPrice($params['cartValue'], $params['address']['id_zone']);
										$offer['priceHT_db'] = $offer['priceTTC_db'];
									}
									else
										continue;
								}
								$offer['priceHT'] = $offer['priceTTC_db'];
							}
							// we add taxes only for French (France - France) expedition
							if ($offer['priceTTC_db'] != '' && $carrier_tax > 0 && $use_taxes)
							{
								// got from classes/Cart.php
								$offer['priceTTC_db'] = $offer['priceTTC_db'] * (1 + ($carrier_tax / 100));
								$offer['priceTTC_client'] = $offer['priceHT_db'] * (1 + ($carrier_tax / 100));
							}
							// add supplementary fees to final price
							$additional_cost = $params['additionalCost'];
							if ($params['additionalCost'] > 0 && $carrier_tax > 0)
								$additional_cost += ($carrier_tax * $params['additionalCost']) / 100;
							$offer['priceHT'] += $params['additionalCost'];
							$offer['priceHT_db'] += $params['additionalCost'];
							$offer['priceTTC_db'] += $additional_cost;
							// if carrier has shipping handling fees
							if ($carrier->shipping_handling)
							{
								$offer['priceTTC_db'] += $shipping_handling * (1 + ($carrier_tax / 100));
								$offer['priceHT'] += $shipping_handling;
								$offer['priceHT_db'] += $shipping_handling;
								$offer['priceTTC_client'] = $offer['priceHT_db'] * (1 + ($carrier_tax / 100));
							}
						}
						// finally, convert the prices
						$offer['priceTTC_db'] = Tools::convertPrice(
							(float)$offer['priceTTC_db'],
							$currency);
						$offer['priceHT'] = Tools::convertPrice(
							(float)$offer['priceHT'],
							$currency);
						$offer['priceHT_db'] = Tools::convertPrice(
							(float)$offer['priceHT_db'],
							$currency);
						$offer['priceTTC_client'] = Tools::convertPrice(
							(float)$offer['priceTTC_client'],
							$currency);
						// add carrier only when price is defined
						$variable = 'choosePoint'.
							$offer['operator']['code'].
							(isset($params['dbCart']['current_address']) ? $params['dbCart']['current_address'] : null);
						$offer['isParcelPointError'] = $cookie->$variable;
						$cookie->$variable = 0;
						$offer['id_carrier'] = $offer_db['id_carrier'];
						// delivery date (indicative !)
						$offer['deliveryDate'] = '';
						$all_points_list[$offer['id_carrier']] = $offer['pointsList'];
						$all_delivery_dates[$offer['id_carrier']] = $offer['delivery']['date'];
						if (trim($offer['priceTTC_db']) != '')
						{
							$old = array(',');
							$new = array('.');
							$offers[$offer['id_carrier']] = $offer;
							$presented[] = $code;
							$price_ht_db = Tools::displayPrice($offer['priceHT']);
							// make the first replacing
							$price_ht_db = trim(str_replace($old, $new, $price_ht_db));
							// make the final replacing : save only the numbers and points
							$price_ht_db = preg_replace(array('/[^0-9\.]/'), array(''), $price_ht_db);
							$prices_array[$offer['id_carrier']] = $price_ht_db;
							$offer['priceHT_db'] = Tools::displayPrice($offer['priceHT_db']);
							$offer['priceTTC_db'] = Tools::displayPrice($offer['priceTTC_db']);
							$offer['priceHT_db'] = trim(str_replace($old, $new, $offer['priceHT_db']));
							$offer['priceTTC_db'] = trim(str_replace($old, $new, $offer['priceTTC_db']));
							$offer['priceHT_db'] = preg_replace(array('/[^0-9\.]/'), array(''), $offer['priceHT_db']);
							$offer['priceTTC_db'] = preg_replace(array('/[^0-9\.]/'), array(''), $offer['priceTTC_db']);
							$carriers_info[$offer['id_carrier']] = array(
								'price_ht'	=> $offer['priceHT_db'],
								'price_ttc' => $offer['priceTTC_db'],
								'tax'			 => $carrier_tax
							);
						}
					}
				}
			}
		}
		$prices_eap = (isset($params['dbCart']['current_address']) ?
			Tools::jsonEncode(array($params['dbCart']['current_address'] => $prices_array)) : null);
		$carriers_eap = (isset($params['dbCart']['current_address']) ?
			Tools::jsonEncode(array($params['dbCart']['current_address'] => $carriers_info)) : null);
		$pricing_data = array(
			_DB_PREFIX_.'cart_id_cart' => $params['cart'],
			'date_eap'								 => date('Y-m-d H:i:s'),
			'date_delivery' 					 => Tools::jsonEncode($all_delivery_dates),
			'free_shipping_eap'				=> $free_delivery,
			'points_eap'							 => Tools::jsonEncode($all_points_list),
			'id_ap'										=> (isset($params['pricingCode']) ? $params['pricingCode'] : false),
			'prices_eap'							 => $prices_eap,
			'carriers_eap'						 => $carriers_eap
		);

		if (!isset($offers['isError']) || $offers['isError'] != 1)
			foreach ($offers as $i => $offer)
				$offers[$i]['pricingData'] = $pricing_data;

		if (isset($offers) && count($offers) > 0)
			return $offers;
	}

	/**
	 * Gets environment's url.
	 * @access public
	 * @param string $env Environment key.
	 * @return string Environment's url.
	 */
	public function getEnvironment($env)
	{
		return $this->environments[$env]['link'];
	}

	/**
	 * Get the upgrade list of the module
	 */
	public function getUpgrades()
	{
		return $this->parseUpgradeXml(_PS_MODULE_DIR_.$this->name.'/sql/upgrades/upgrades.xml');
	}

	/**
	 * Executes upgrade queries.
	 * @access public
	 * @return Displayed template
	 */
	public function makeUpgrade()
	{
		$cookie = $this->getContext()->cookie;
		$smarty = $this->getContext()->smarty;

		$error = false;
		$list = $this->getUpgrades();
		$id = (int)Tools::getValue('up_id');
		$queries = explode('-- REQUEST --', Tools::file_get_contents(_PS_MODULE_DIR_.$this->name.'/sql/upgrades/'.$list[$id]['file']));
		foreach ($queries as $q => $query)
		{
			$query = str_replace('{PREFIXE}', _DB_PREFIX_, $query);
			if (trim($query) != '' && !Db::getInstance()->Execute($query))
			{
				$error = true;
				break;
			}
			unset($queries[$q]);
		}
		if (count($queries) > 0)
			file_put_contents(_PS_MODULE_DIR_.$this->name.'/sql/upgrades/'.$list[$id]['file'], implode('-- REQUEST --', $queries));
		else
		{
			$error = $this->removeUpgradeItem($id, _PS_MODULE_DIR_.$this->name.'/sql/upgrades/upgrades.xml');
			unlink(_PS_MODULE_DIR_.$this->name.'/sql/upgrades/'.$list[$id]['file']);
		}
		$smarty->assign('error', $error);
		$smarty->assign('adminImg', _PS_ADMIN_IMG_);
		$smarty->assign('token', Tools::getAdminToken('AdminModules'.(int)Tab::getIdFromClassName('AdminModules').(int)$cookie->id_employee));
		return $this->display(__FILE__, '/views/templates/admin/upgrade.tpl');
	}

	/**
	 * Parse upgraded XML file and get list of upgrades.
	 * @access private
	 * @param string $file File name.
	 * @return array List with upgrades.
	 */
	private function parseUpgradeXml($file)
	{
		if (!file_exists($file))
			return array();
		$result = array();
		$dom_cl = new DOMDocument();
		$dom_cl->load($file);
		$xpath = new DOMXPath($dom_cl);
		$upgrades = $xpath->evaluate('/upgrades/upgrade');
		foreach ($upgrades as $upgrade)
		{
			$date = strtotime($upgrade->getElementsByTagName('date')->item(0)->nodeValue);
			$result[$upgrade->getElementsByTagName('id')->item(0)->nodeValue] = array(
				'from' => $upgrade->getElementsByTagName('from')->item(0)->nodeValue,
				'to' => $upgrade->getElementsByTagName('to')->item(0)->nodeValue,
				'date' => date('d-m-Y', $date),
				'description' => $upgrade->getElementsByTagName('description')->item(0)->nodeValue,
				'file' => $upgrade->getElementsByTagName('file')->item(0)->nodeValue);
		}
		return $result;
	}

	/**
	 * Removes upgraded item from XML file.
	 * @access private
	 * @param int $id Id of upgraded item.
	 * @param string $file File name.
	 * @return boolean True if executed correctly, false otherwise.
	 */
	private function removeUpgradeItem($id, $file)
	{
		$error = true;
		$dom_cl = new DOMDocument();
		$dom_cl->load($file);
		$xpath = new DOMXPath($dom_cl);
		$main = $xpath->evaluate('/upgrades')->item(0);
		$upgrades = $xpath->evaluate('/upgrades/upgrade');
		foreach ($upgrades as $upgrade)
			if ($upgrade->getElementsByTagName('id')->item(0)->nodeValue == $id)
			{
				$main->removeChild($upgrade);
				$error = false;
			}
		file_put_contents($file, $dom_cl->saveXML());
		return $error;
	}

	/**
	 * Calculates parcel weight. Uses average weight option for products.
	 * It returns delivery address too beceause we want to avoid to make too many requests.
	 * @access private
	 * @param int $cart Id of current cart.
	 * @param array $address_param Array with address (from 1.5 and multi-shipping delivery option)
	 * @return array List with cart weight, delivery address, cart type.
	 */
	private function prepareQuotationCartData($cart, $address_param = array())
	{
		$rows = $this->model->getCartInformations($cart, $address_param); // Get cart informations
		// sets products weight

		/* We compute the weight and price of the products with their attributes */
		$count = count($rows);
		for ($i = 0; $i < $count; $i++)
		{
			$attributes = $this->model->getProductAttributes($rows[$i]['id_product_attribute']);
			$weigt = isset($attributes[0])?$attributes[0]['weight']:0;
			$price = isset($attributes[0])?$attributes[0]['price']:0;
			$rows[$i]['real_weight'] = ((float)$rows[$i]['weight'] + (float)$weigt);
			$rows[$i]['real_price'] = ((float)$rows[$i]['price'] + (float)$price);
		}

		$weight = 0;
		$avg_weight = (float)Configuration::get('EMC_AVERAGE_WEIGHT');

		$additional_cost = 0;
		if ($rows && count($rows) > 0)
		{
			$current = current($rows);

				foreach ($rows as $row)
				{
					$row['real_weight'] = EnvoimoinscherHelper::normalizeToKg(Configuration::get('PS_WEIGHT_UNIT'), $row['real_weight']);
					$weight += $row['productQuantity'] * $row['real_weight'];
					// if we haven't product weight, take average weight option
					if ($row['productQuantity'] * $row['real_weight'] == 0)
						$weight += $avg_weight * $row['productQuantity'];
					// if product has some mandatory fees, add it into ship price
					if ($row['additional_shipping_cost'] > 0)
						$additional_cost += (float)$row['additional_shipping_cost'];
				}

			if ($current['id_address_delivery'] != '0')
			{

				// delivery address
				$street = $current['address1'];

				if ($current['address2'] != '')
					$street .= $current['address2'];

				$type = 'particulier';

				if ($current['company'] != '')
					$type = 'entreprise';
				$address = array(
					'id_zone'	=> $current['id_zone'],
					'type'		 => $type,
					'country'	=> $current['iso_code'],
					'city'		 => $current['city'],
					'postcode' => $current['postcode'],
					'street'	 => $street);
				$id_currency = $current['id_currency'];

			}
			else
			{
				$address = array();
				$id_currency = false;
			}
		}

		// option < 100g
		if ($weight < 0.1 && $weight >= 0 && (int)Configuration::get('EMC_WEIGHTMIN') == 1)
			$weight = 0.1;

		// cart's type (to calculate order total amount)
		$type_cart = Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING;
		return array(
			'weight'				 => (float)$weight,
			'address'				=> $address,
			'idCurrency'		 => (int)$id_currency,
			'typeCart'			 => $type_cart,
			'additionalCost' => $additional_cost
		);
	}

	/**
	 * Downloads one or more EnvoiMoinsCher's labels.
	 * @access public
	 * @return void
	 */
	public function downloadLabels()
	{
		$cookie = $this->getContext()->cookie;
		$orders_to_get = array();
		$refs = array();

		$url	 		= '';
		$base_url = '';
		$type			= '';
		$envoi 		= '';

		if (Tools::isSubmit('orders'))
			foreach (Tools::getValue('orders') as $ord)
				$orders_to_get[] = (int)$ord;
		elseif (Tools::isSubmit('order'))
			$orders_to_get[] = (int)Tools::getValue('order');

		// We need order's infos for the document's url and references
		if (count($orders_to_get) > 0)
		{
			$references = $this->model->getReferencesToLabels($orders_to_get);

			foreach ($references as $reference)
				$refs[] = $reference['ref_emc_eor'];
		}

		// We now query the document
		if (count($refs) > 0)
		{
			// Get the document's base url
			$base_url = explode('?', $references[0]['link_ed']);
			$base_url = $base_url[0];

			// Set url's params
			if (Tools::getValue('sendValueRemises'))
				$type = 'remise';
			else
				$type = 'bordereau';
			$envoi = implode(',', $refs);

			//	Set url
			$url = $base_url.'?type='.$type.'&envoi='.$envoi;

			// Send the pdf request
			$cookie->error_labels = 0;
			$helper = new EnvoimoinscherHelper;
			$config = $helper->configArray($this->model->getConfigData());
			$options = array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => array('Authorization: '.$helper->encode($config['EMC_LOGIN'].':'.$config['EMC_PASS'])),
				CURLOPT_CAINFO => dirname(__FILE__).'/ca/ca-bundle.crt',
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2
			);
			$req = curl_init();
			curl_setopt_array($req, $options);
			$result = curl_exec($req);
			curl_close($req);

			// We now display the pdf
			header('Content-type: application/pdf');
			if ($type == 'remise')
				header('Content-Disposition: attachment; filename="remises.pdf"');
			else
				header('Content-Disposition: attachment; filename="bordereaux.pdf"');
			echo $result;
			die();
		}
		$cookie->error_labels = 1;
		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
		Tools::redirectAdmin($admin_link_base.'&option=history');
	}

	public function checkUpdates()
	{
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		$result = array();
		$filename = $this->getEnvironment($config['EMC_ENV']).'/api/check_updates.html?module='.$this->ws_name.'&version='.$this->local_version;
		$updates = (array)Tools::jsonDecode(Tools::file_get_contents($filename));
		foreach ($updates as $u => $update)
		{
			$info = (array)$update;
			$result[] = array(
				'version' => $u,
				'name' => $info['name'],
				'description' => $info['description'],
				'url' => $this->getEnvironment($config['EMC_ENV']).$info['url']);
		}
		ob_end_clean();
		echo Tools::jsonEncode($result);
		die();
	}

	public function lookForCarrierUpdates()
	{
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/WebService.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/Carrier.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/Service.php');
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		$ser_class = new Env_Service(array(
			'user' => $config['EMC_LOGIN'],
			'pass' =>	$config['EMC_PASS'],
			'key' => $config['EMC_KEY']));
		$ser_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$ser_class->setEnv(Tools::strtolower($config['EMC_ENV']));
		$ser_class->setParam(array('module' => $this->ws_name, 'version' => $this->local_version));
		$ser_class->setGetParams();
		$ser_class->getServices();
		$offers = $this->model->getOffers;
		$installed = array();
		$checksums = array();
		$offers_json = array('added' => array(), 'updated' => array(), 'deleted' => array());
		$deleted = 0;
		$added = 0;
		$updated = 0;
		foreach ($offers as $offer)
		{
			$installed[$offer['emc_operators_code_eo'].'_'.$offer['code_es']] = $offer;
			$checksums[$offer['emc_operators_code_eo'].'_'.$offer['code_es']] = sha1($offer['desc_es'].$offer['desc_store_es'].$offer['family_es']);
		}
		foreach ($ser_class->carriers as $c => $carrier)
		{
			foreach ($carrier['services'] as $service)
			{
				$code = $c.'_'.$service['code'];
				$exists = isset($installed[$code]);
				$service_infos = array('label_backoffice' => '', 'label_store' => '');
				if (isset($service['apiOptions']['prestashop']))
				{
					$service_infos = array(
						'label_backoffice' => html_entity_decode($service['apiOptions']['prestashop']['label_backoffice']),
						'label_store' => html_entity_decode($service['apiOptions']['prestashop']['label_store']),
						'offer_family' => (int)$service['apiOptions']['prestashop']['offer_family']);
				}
				$srv_checksum = sha1($service_infos['label_backoffice'].$service_infos['label_store'].$service_infos['offer_family']);
				if (!$exists && $service['is_pluggable'])
				{
					// install new service and remove from $installed array
					$service['srvInfos'] = $service_infos;
					if ($this->model->insertService($service, $carrier))
					{
						$carrier_lower = Tools::strtolower($carrier['code']);
						file_put_contents(_PS_MODULE_DIR_.'/envoimoinscher/img/detail_'.$carrier_lower.'.jpg', Tools::file_get_contents($carrier['logo_modules']));

						$added++;
						$offers_json['added'][] = $service['label'];
					}
				}
				elseif ($exists && !$service['is_pluggable'])
				{
					// uninstall carrier
					if ($this->model->uninstallService($code))
					{
						$deleted++;
						$offers_json['deleted'][] = $service['label'];
					}
				}
				elseif ($exists && $checksums[$code] != $srv_checksum)
				{
					// new data available, must update
					$parts = explode('_', $code);
					$up_data = array('desc_es' => pSQL($service_infos['label_backoffice']),
						'desc_store_es' => pSQL($service_infos['label_store']), 'family_es' => (int)$service_infos['offer_family']);
					if ($this->model->updateService($up_data, $parts[0], $parts[1]))
					{
						$updated++;
						$offers_json['updated'][] = $service['label'];
					}
					unset($installed[$code]);
				}
				elseif ($exists && isset($installed[$code]))
					unset($installed[$code]);
			}
		}
		// clean up old services in Prestashop database
		foreach ($installed as $code => $offer)
			if ($this->model->uninstallService($code))
			{
				$deleted++;
				$offers_json['deleted'][] = $offer['label_es'];
			}
		ob_end_clean();
		echo Tools::jsonEncode(array(
			'added' => $added,
			'updated' => $updated,
			'deleted' => $deleted,
			'addedOffers' => implode(',', $offers_json['added']),
			'updatedOffers' => implode(',', $offers_json['updated']),
			'deletedOffers' => implode(',', $offers_json['deleted']))
		);
		die();
	}

	public function checkLabelsAvailability()
	{
		$orders_id = explode(';', Tools::getValue('orders'));
		if (count($orders_id) > 0)
		{
			$documents = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'emc_documents
				WHERE generated_ed = 1 AND '._DB_PREFIX_.'orders_id_order in ('.implode(',', array_map('intval', $orders_id)).')');

			// get all documents for each order
			$order_documents = array();
			foreach ($documents as $document)
			{
				$id = $document['ps_orders_id_order'];
				if (!isset($order_documents[$id]))
					$order_documents[$id] = array();
				$order_documents[$id][] = array(
					'type'	=> $document['type_ed'],
					'name' 	=> $this->l('download '.$document['type_ed']),
					'url'		=> $document['link_ed']
				);
			}
		}

		echo Tools::jsonEncode($order_documents);
		die();
	}

	/**
	 * Puts EnvoiMoinsCher's choosen carrier
	 * @access public
	 * @param int $ope Operator's id.
	 * @param boolean $is_discount True if discount is applied, false otherwise.
	 * @return void
	 */
	public function putOpe($ope, $is_discount)
	{
		$cookie = $this->getContext()->cookie;

		$cookie->emc_carrier = (int)$ope;
		$cookie->emc_discount = $is_discount;
	}

	private function getModuleConfig()
	{
		return array('wsName' => $this->ws_name, 'version' => $this->local_version, 'is13' => false);
	}

	/**
	 * Edit delivery address when order hasn't EMC carrier and the order address is incorrect (bad postal code etc.)
	 * @access public
	 * @return void
	 */
	public function editAddress()
	{
		$order_id = (int)Tools::getValue('id_order');
		// get old address data
		$address = $this->model->getOrderData($order_id);
		$company = $address[0]['company'];
		if (Tools::getValue('dest_company'))
			$company = Tools::getValue('dest_company');
		$perso_alias = '_perso_'.$order_id;
		// insert new address row
		$insert_row = array(
			'id_country' => (int)$address[0]['id_country'],
			'id_state' => (int)$address[0]['id_state'],
			'id_customer' => (int)$address[0]['id_customer'],
			'id_manufacturer' => (int)$address[0]['id_manufacturer'],
			'id_supplier' => (int)$address[0]['id_supplier'],
			'alias' => pSQL(str_replace($perso_alias, '', $address[0]['alias']).$perso_alias),
			'company' => pSQL($company),
			'lastname' => pSQL(Tools::getValue('dest_lname')),
			'firstname' => pSQL(Tools::getValue('dest_fname')),
			'address1' => pSQL(Tools::getValue('dest_add')),
			'address2' => pSQL($address[0]['address2']),
			'postcode' => pSQL(Tools::getValue('dest_code')),
			'city' => pSQL(Tools::getValue('dest_city')),
			'other' => pSQL($address[0]['other']),
			'phone' => pSQL(Tools::getValue('dest_tel')),
			'phone_mobile' => pSQL($address[0]['phone_mobile']),
			'vat_number' => pSQL($address[0]['vat_number']),
			'dni' => pSQL($address[0]['dni']),
			'date_add' => date('Y-m-d H:i:s'),
			'date_upd' => date('Y-m-d H:i:s'),
			'active' => 0,
			'deleted' => 1
		);
		// update id_address_delivery field on orders table
		$this->model->putNewAddress($order_id, $insert_row, $address[0]);

		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
		Tools::redirectAdmin($admin_link_base.'&option=send&id_order='.$order_id);
		die();
	}

	/**
	 * Cleans pricing cache. It deletes only pricing older than 2 days.
	 * @access public
	 * @return void
	 */
	public function cleanCache()
	{
		$result = array('error' => 1);
		if ($this->model->cleanCache())
			$result['error'] = 0;
		ob_end_clean();
		echo Tools::jsonEncode($result);
		die();
	}


	private function postValidation()
	{
	}

	/**
	 * Process
	 * @return mixed
	 */
	private function postProcess()
	{
		$this->link = 'index.php?controller='.Tools::getValue('controller').
									'&token='.Tools::getValue('token').
									'&configure='.$this->name.
									'&tab_module='.$this->tab.
									'&module_name='.$this->name;
		$this->getContext()->smarty->assign('EMC_link', $this->link);

		// Get level configuration
		$emc_user = (int)Configuration::get('EMC_USER');

		if ($emc_user < 3)
		{
			// If we need to previous configuration
			if (Tools::isSubmit('previous'))
			{
				Configuration::updateValue('EMC_USER', $emc_user - 1);
				Tools::redirectAdmin($this->link);
				return;
			}

			// Introduction
			if (Tools::getValue('btnIntro'))
				return $this->postProcessIntroduction();
			// Merchant configuration
			if (Tools::getValue('btnMerchant'))
				return $this->postProcessMerchant();
			// Sends configuration
			else if (Tools::getValue('btnSends'))
				return $this->postProcessSends(false);
			else if (Tools::getValue('btnCarriersSimple'))
				return $this->postProcessCarriersSimple();

			return;
		}

		// Save status
		if (Tools::getValue('EMC_Status') && Tools::getValue('ajax'))
		{
			$status = Tools::getValue('EMC_Status') === 'true' ? EnvoimoinscherModel::MODE_ONLINE : EnvoimoinscherModel::MODE_CONFIG;
			Configuration::updateValue('EMC_SRV_MODE', $status);

			if ($status == EnvoimoinscherModel::MODE_ONLINE)
				$this->model->passToOnlineMode();

			Tools::jsonEncode(true);
			exit;
		}
		// Save environment
		else if (Tools::getValue('EMC_Env') && Tools::getValue('ajax'))
		{
			Configuration::updateValue('EMC_ENV', Tools::getValue('EMC_Env'));
			Tools::jsonEncode(true);
			exit;
		}
		// Load tabs
		else if (Tools::getValue('ajax'))
		{
			$tab = Tools::ucfirst(Tools::strtolower(Tools::getValue('EMC_tab')));
			/** Merchant Account **/
			if ($tab === 'Merchant')
				echo $this->getContentMerchant();
			/** Sends **/
			else if ($tab === 'Sends')
				echo $this->getContentSends();
			/** Settings **/
			else if ($tab === 'Settings')
				echo $this->getContentSettings();
			/** Settings **/
			else if ($tab === 'Simple_carriers')
				echo $this->getContentCarriers('Simple');
			/** Settings **/
			else if ($tab === 'Advanced_carriers')
				echo $this->getContentCarriers('Advanced');
			/** Settings **/
			else if ($tab === 'Help')
				echo $this->getContentHelp();
			exit;
		}
		// Settings
		else if (Tools::getValue('btnMerchant'))
			return $this->postProcessMerchant();
		// Carriers
		else if (Tools::getValue('btnCarriersSimple'))
			return $this->postProcessCarriersSimple();
		// Carriers
		else if (Tools::getValue('btnCarriersAdvanced'))
			return $this->postProcessCarriersAdvanced();
		// Send
		else if (Tools::getValue('btnSends'))
			return $this->postProcessSends();
		// Settings
		else if (Tools::getValue('btnSettings'))
		{
			if (Tools::getValue('EMC_track_mode') &&
				Tools::getValue('EMC_ann') &&
				Tools::getValue('EMC_envo') &&
				Tools::getValue('EMC_cmd') &&
				Tools::getValue('EMC_liv'))
			{
				// Track mode
				Configuration::updateValue('EMC_TRACK_MODE', (int)Tools::getValue('EMC_track_mode'));
				// Status
				Configuration::updateValue('EMC_ANN', Tools::getValue('EMC_ann'));
				Configuration::updateValue('EMC_ENVO', Tools::getValue('EMC_envo'));
				Configuration::updateValue('EMC_CMD', Tools::getValue('EMC_cmd'));
				Configuration::updateValue('EMC_LIV', Tools::getValue('EMC_liv'));
				Configuration::updateValue('EMC_ENABLED_LOGS', Tools::getValue('EMC_enabled_logs'));
				Configuration::updateValue('EMC_FILTER_TYPE_ORDER', Tools::getValue('EMC_filter_type_order'));
				Configuration::updateValue('EMC_FILTER_STATUS', implode(';', Tools::getValue('EMC_filter_status')));
				Configuration::updateValue('EMC_FILTER_CARRIERS', Tools::getValue('EMC_filter_carriers'));
				Configuration::updateValue('EMC_FILTER_START_DATE', Tools::getValue('EMC_filter_start_order_date'));

				require_once dirname(__FILE__).'/Env/WebService.php';
				require_once dirname(__FILE__).'/Env/User.php';

				$api_login = Configuration::get('EMC_LOGIN');
				$api_pass = Configuration::get('EMC_PASS');
				$api_key = Configuration::get('EMC_KEY');
				$api_env = Configuration::get('EMC_ENV');

				// update e-mail configuration
				$user_class = new Env_User(array('user' => $api_login, 'pass' => $api_pass, 'key' => $api_key));
				$user_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
				$user_class->setEnv(Tools::strtolower($api_env));

				$user_class->postEmailConfiguration(
					array(
						'label'				=> Tools::getValue('EMC_mail_label', ''),
						'notification' => Tools::getValue('EMC_mail_notif', ''),
						'bill'				 => Tools::getValue('EMC_mail_bill', '')
					)
				);

				Tools::redirectAdmin($this->link.'&EMC_tabs=settings&conf=6');
			}
			else
				return $this->displayError($this->l('Please check your form, some fields are requried'));
		}
	}

	private function postProcessIntroduction()
	{
		Configuration::updateValue('EMC_USER', 0);
	}

	/**
	 * Set Merchant configuration
	 * @return string Error message
	 */
	private function postProcessMerchant()
	{
		// Check form
		if (Tools::getValue('EMC_login') &&
			Tools::getValue('EMC_pass') &&
			Tools::getValue('EMC_api') &&
			Tools::getValue('EMC_gender') &&
			Tools::getValue('EMC_exp_firstname') &&
			Tools::getValue('EMC_exp_lastname') &&
			Tools::getValue('EMC_exp_address') &&
			Tools::getValue('EMC_exp_postcode') &&
			Tools::getValue('EMC_exp_town') &&
			Tools::getValue('EMC_exp_phone') &&
			Tools::getValue('EMC_exp_email'))
		{
			// Update Value settings
			Configuration::updateValue('EMC_LOGIN', Tools::getValue('EMC_login'));
			Configuration::updateValue('EMC_PASS', Tools::getValue('EMC_pass'));
			Configuration::updateValue('EMC_KEY', Tools::getValue('EMC_api'));
			Configuration::updateValue('EMC_CIV', Tools::getValue('EMC_gender'));
			Configuration::updateValue('EMC_FNAME', Tools::getValue('EMC_exp_firstname'));
			Configuration::updateValue('EMC_LNAME', Tools::getValue('EMC_exp_lastname'));
			Configuration::updateValue('EMC_COMPANY', Tools::getValue('EMC_exp_company'));
			Configuration::updateValue('EMC_ADDRESS', Tools::getValue('EMC_exp_address'));
			Configuration::updateValue('EMC_COMPL', Tools::getValue('EMC_exp_more_infos'));
			Configuration::updateValue('EMC_POSTALCODE', Tools::getValue('EMC_exp_postcode'));
			Configuration::updateValue('EMC_CITY', Tools::getValue('EMC_exp_town'));
			Configuration::updateValue('EMC_TEL', Tools::getValue('EMC_exp_phone'));
			Configuration::updateValue('EMC_MAIL', Tools::getValue('EMC_exp_email'));

			if (Tools::isSubmit('EMC_exp_start_pickup'))
				Configuration::updateValue('EMC_DISPO_HDE', Tools::getValue('EMC_exp_start_pickup'));

			if (Tools::isSubmit('EMC_exp_end_pickup'))
				Configuration::updateValue('EMC_DISPO_HLE', Tools::getValue('EMC_exp_end_pickup'));

			// If no first time
			if (Configuration::get('EMC_USER') >= 3)
				Tools::redirectAdmin($this->link.'&EMC_tabs=merchant&conf=6');
			else
			{
				Configuration::updateValue('EMC_USER', 1);
				return $this->displayConfirmation($this->l('The first step has been enabled'));
			}

		}
		else
			return $this->displayError($this->l('Please check your form, some fields are requried'));
	}

	private function postProcessCarriersParcelPoints()
	{
		$parcel_points = Tools::getValue('parcel_point');

		// fetch the 32 last char -- PS config field accept <= 32 char in PS 1.5
		if (count($parcel_points) > 0)
			foreach ($parcel_points as $carrier => $code)
				Configuration::updateValue('EMC_PP_'.Tools::strtoupper(Tools::substr($carrier, -25)), $code);
	}

	/**
	 * Set Carriers configuration
	 * @return string Error message
	 */
	private function postProcessCarriersAdvanced()
	{
		$helper = new EnvoimoinscherHelper();
		$config = $helper->configArray($this->model->getConfigData());
		// do operation on offers only when "configuration" is checked
		if ($config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_CONFIG)
		{
			if (Tools::getValue('btnCarriersAdvanced'))
			{
				$this->postProcessCarriersParcelPoints();

				$from_weight = 0; // Initialize
				//update dimensions
				for ($i = 1; $i <= Tools::getValue('countDims'); $i++)
				{
					$data = array(
						'length_ed'			=> (int)Tools::getValue('length'.$i),
						'width_ed'			 => (int)Tools::getValue('width'.$i),
						'height_ed'			=> (int)Tools::getValue('height'.$i),
						'weight_from_ed' => (float)$from_weight,
						'weight_ed'			=> (float)Tools::getValue('weight'.$i)
					);
					$from_weight = $data['weight_ed'];
					$this->model->updateDimensions($data, (int)Tools::getValue('id'.$i));
				}

				// handle services (insert only new services; delete only not choosen ones)
				$all_ser = (array)Tools::getValue('offers');
				Configuration::updateValue('EMC_SERVICES', implode(',', $all_ser));
				$full_list = array();
				foreach ($all_ser as $serv)
					$full_list[] = '\''.pSQL($serv).'\'';
				$not_in = array();
				$srv_list = $helper->makeCodeKeys($this->model->getOffers(
					false,
					EnvoimoinscherModel::FAM_EXPRESSISTE,
					' AND CONCAT_WS("_", es.`emc_operators_code_eo` , es.`code_es` ) IN ('.implode(',', $full_list).') ')
				);
				foreach ($srv_list as $service)
				{
					$pricing = (Tools::getValue($service['emc_operators_code_eo'].'_'.$service['code_es'].'_emc') == 'real')?
						(EnvoimoinscherModel::REAL_PRICE):(EnvoimoinscherModel::RATE_PRICE);

					// EMC column
					$data = array(
						'id_es'					 =>(int)$service['id_es'],
						'pricing_es'					 => $pricing,
						'name'								 => $service['label_es'].' ('.$service['name_eo'].')',
						'active'							 => 1,
						'is_module'						=> 1,
						'need_range'					 => 1,
						'range_behavior'			 => 1,
						'shipping_external'		=> 1,
						'external_module_name' => $this->name
					);

					$carrier_id = $this->model->saveCarrier($data, $service);

					if ($carrier_id === false)
						return false;
					$not_in[]	 = (int)$carrier_id;

					DB::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services
						SET id_carrier = '.(int)$carrier_id.', pricing_es = '.$pricing.'
						WHERE id_es = '.(int)$service['id_es'].'');
				}

				// Carriers have been saved
				$not_in_carrier = '';
				if (count($not_in) > 0)
					$not_in_carrier = 'AND c.`id_carrier` NOT IN ('.implode(',', $not_in).')';

				// get all EnvoiMoinsCher services (to remove images)
				$image_rmv = array();

				$sql = 'SELECT * FROM `'._DB_PREFIX_.'carrier` AS c
					 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
					 ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_EXPRESSISTE.'"
					 WHERE c.`external_module_name` = "envoimoinscher" AND c.`deleted` = 0 '.$not_in_carrier.'';

				$services_emc = Db::getInstance()->ExecuteS($sql);
				foreach ($services_emc as $service_emc)
					$image_rmv[] = (int)$service_emc['id_carrier'];

				$delete_sql = 'UPDATE `'._DB_PREFIX_.'carrier` AS c
					 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
					 ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_EXPRESSISTE.'"
					 SET c.`deleted` = 1 WHERE c.`external_module_name` = "envoimoinscher" '.$not_in_carrier.'';

				Db::getInstance()->Execute($delete_sql);
				// remove images too
				foreach ($image_rmv as $image)
					unlink(_PS_IMG_DIR_.'s/'.$image.'.jpg');

				Tools::redirectAdmin($this->link.'&EMC_tabs=advanced_carriers&conf=6');
			}
		}
		else
			return $this->displayError($this->l('Please set the module in config mode'));
	}

	private function postProcessCarriersSimple()
	{
		$helper = new EnvoimoinscherHelper();
		$config = $helper->configArray($this->model->getConfigData());
		// do operation on offers only when 'configuration' is checked
		if ($config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_CONFIG)
		{
			$this->postProcessCarriersParcelPoints();

			//$langs = Language::getLanguages(true);
			//$zones = Zone::getZones(true);

			// handle services (insert only new services; delete only not choosen ones)
			$all_ser = (array)Tools::getValue('offers');
			Configuration::updateValue('EMC_SERVICES', implode(',', $all_ser));
			$full_list = array();
			foreach ($all_ser as $serv)
				$full_list[] = '\''.pSQL($serv).'\'';
			$not_in = array();
			$srv_list = $helper->makeCodeKeys($this->model->getOffers(
				false,
				EnvoimoinscherModel::FAM_ECONOMIQUE,
				' AND CONCAT_WS("_", es.`emc_operators_code_eo` , es.`code_es` ) IN ('.implode(',', $full_list).') ')
			);
			foreach ($srv_list as $service)
			{
				$pricing = (Tools::getValue($service['emc_operators_code_eo'].'_'.$service['code_es'].'_emc') == 'real')?
						(EnvoimoinscherModel::REAL_PRICE):(EnvoimoinscherModel::RATE_PRICE);

				// EMC column
				$data = array(
					'id_es'					 =>(int)$service['id_es'],
					'pricing_es'					 => $pricing,
					'name'								 => $service['label_es'].' ('.$service['name_eo'].')',
					'active'							 => 1,
					'is_module'						=> 1,
					'need_range'					 => 1,
					'range_behavior'			 => 1,
					'shipping_external'		=> 1,
					'external_module_name' => $this->name
				);
				/*$lang_data = array(
					// 'id_lang' => 2,
					'delay' => addslashes($service['desc_store_es'])
				);*/

				$carrier_id = $this->model->saveCarrier($data, $service);
				if ($carrier_id === false)
					return false;
				$not_in[]	 = (int)$carrier_id;

			}

			// Carriers have been saved
			$not_in_carrier = '';
			if (count($not_in) > 0)
				$not_in_carrier = 'AND c.`id_carrier` NOT IN ('.implode(',', $not_in).')';

			// get all EnvoiMoinsCher services (to remove images)
			$image_rmv = array();

			$sql = 'SELECT * FROM `'._DB_PREFIX_.'carrier` AS c
				 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
				 ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_ECONOMIQUE.'"
				 WHERE c.`external_module_name` = "envoimoinscher" AND c.`deleted` = 0 '.$not_in_carrier.'';

			$services_emc = Db::getInstance()->ExecuteS($sql);
			foreach ($services_emc as $service_emc)
				$image_rmv[] = (int)$service_emc['id_carrier'];

			$delete_sql = 'UPDATE `'._DB_PREFIX_.'carrier` AS c
				 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
				 ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_ECONOMIQUE.'"
				 SET c.`deleted` = 1 WHERE c.`external_module_name` = "envoimoinscher" '.$not_in_carrier.'';

			Db::getInstance()->Execute($delete_sql);

			// remove images too
			foreach ($image_rmv as $image)
				unlink(_PS_IMG_DIR_.'s/'.$image.'.jpg');

			$step = Configuration::get('EMC_USER');
			Configuration::updateValue('EMC_USER', 3);

			Tools::redirectAdmin($this->link.'&EMC_tabs='.($step == '1' ? 'merchant' : 'simple_carriers').'&conf=6');
		}
		else
			return $this->displayError($this->l('Please set the module in config mode'));
	}

	/**
	 * Set Sends configuration
	 * @param	boolean $all If is the full configuration to save
	 * @return mixed			 Error message
	 */
	private function postProcessSends($all = true)
	{
		if (Tools::getValue('EMC_type') && Tools::getValue('EMC_nature'))
		{
			// Update configuration
			Configuration::updateValue('EMC_TYPE', Tools::getValue('EMC_type'));
			Configuration::updateValue('EMC_NATURE', Tools::getValue('EMC_nature'));
			Configuration::updateValue('EMC_WRAPPING', Tools::getValue('EMC_wrapping'));
			Configuration::updateValue('EMC_CONTENT_AS_DESC', (int)Tools::getValue('contentAsDesc'));
			if ($all === false)
			{
				Configuration::updateValue('EMC_USER', 2);
				return $this->displayConfirmation($this->l('The second step has been enabled'));
			}
		}
		else
			return $this->displayError($this->l('Thanks to choose type and nature of your picks.'));

		if (Tools::isSubmit('pickupDay0') &&
			(Tools::getValue('pickupFrom0') || (Tools::isSubmit('pickupFrom0') && Tools::getValue('pickupFrom0') == '0')) &&
			(Tools::getValue('pickupTo0') || (Tools::isSubmit('pickupTo0') && Tools::getValue('pickupTo0') == '0')) &&
			Tools::isSubmit('pickupDay1') &&
			(Tools::getValue('pickupTo1') || (Tools::isSubmit('pickupTo1') && Tools::getValue('pickupTo1') == '0')) &&
			(Tools::getValue('pickupFrom1') || (Tools::isSubmit('pickupFrom1') && Tools::getValue('pickupFrom1') == '0')))
		{
			// Update CFG
			// News
			Configuration::updateValue('EMC_INDI', Tools::getValue('EMC_indiv'));
			Configuration::updateValue('EMC_MULTIPARCEL', Tools::getValue('EMC_multiparcel'));
			Configuration::updateValue('EMC_WEIGHTMIN', (int)Tools::getValue('EMC_min_weight'));
			Configuration::updateValue('EMC_AVERAGE_WEIGHT', str_replace(',', '.', Tools::getValue('EMC_default_weight')));
			Configuration::updateValue('EMC_ASSU', Tools::isSubmit('EMC_use_axa') ? Tools::getValue('EMC_use_axa') : 0);
			// Old
			Configuration::updateValue('EMC_PICKUP_J1', Tools::getValue('pickupDay0'));
			Configuration::updateValue('EMC_PICKUP_F1', Tools::getValue('pickupFrom0'));
			Configuration::updateValue('EMC_PICKUP_T1', Tools::getValue('pickupTo0'));
			Configuration::updateValue('EMC_PICKUP_J2', Tools::getValue('pickupDay1'));
			Configuration::updateValue('EMC_PICKUP_F2', Tools::getValue('pickupFrom1'));
			Configuration::updateValue('EMC_PICKUP_T2', Tools::getValue('pickupTo1'));
			Configuration::updateValue('EMC_MASS', Tools::getValue('EMC_mass'));
			Configuration::updateValue('EMC_LABEL_DELIVERY_DATE', Tools::getValue('labelDeliveryDate'));

			Tools::redirectAdmin($this->link.'&EMC_tabs=sends&conf=6');
		}
		else
			return $this->displayError($this->l('Please check your form, some fields are required'));
	}

	/**
	 * Create carrier
	 */
	public static function createEnvoimoinscherCarrier($config)
	{
		return $config;
	}

	/**
	 * Mondial Relay plugin does that :
	 * $carrier = $params['carrier'];
	 * $order = $params['order'];
	 * if ($carrier->is_module AND $order->shipping_number)
	 * {
	 * $module = $carrier->external_module_name;
	 * include_once(_PS_MODULE_DIR_.$module.'/'.$module.'.php');
	 * $module_carrier = new $module();
	 * $smarty->assign('followup', $module_carrier->getFollowup($order->shipping_number));
	 * }
	 * else if ($carrier->url AND $order->shipping_number)
	 * $smarty->assign('followup', str_replace('@', $order->shipping_number, $carrier->url));
	 * ...
	 * We need to add this method to avoid the problems with displaying of order detail.
	 */
	public function getFollowup()
	{
		return '';
	}

	public static function getMapByOpe($ope, $srv = false, $city = false, $postalcode = false, $address = false, $country = false)
	{
		$link = '//www.envoimoinscher.com/choix-relais.html?cp='.($postalcode ? $postalcode : Configuration::get('EMC_POSTALCODE')).
				'&ville='.urlencode(($city ? $city : Configuration::get('EMC_CITY'))).'&country='.($country ? $country : 'FR').'&srv='.$srv.'&ope='.$ope;

		return $link;
	}

	public function handlePush()
	{
		return $this->model->handlePush();
	}
}