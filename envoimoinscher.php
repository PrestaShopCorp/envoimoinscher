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
* @copyright 2007-2015 PrestaShop SA / 2011-2015 EnvoiMoinsCher
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registred Trademark & Property of PrestaShop SA
*/

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
    private $model;

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

        if (!defined('_PS_VERSION_')) {
            exit;
        }

        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/includes/EnvoimoinscherModel.php');
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/includes/EnvoimoinscherHelper.php');
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/includes/EnvoimoinscherOrder.php');

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
        $this->version = '3.3.3';
        $this->author = 'EnvoiMoinsCher';
        $this->local_version = '3.3.3';
        parent::__construct();
        $this->page = basename(__FILE__, '.php');
        $this->displayName = 'EnvoiMoinsCher';
        $this->ws_name = 'Prestashop';
        $this->description = $this->l('Offer your customers a choice of delivery methods to increase your sales');
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
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $this->_errors[] = $this->l('ENVOIMOINSCHER is not compatible with PrestaShop lower than 1.5.');
            $error = '[ENVOIMOINSCHER][' . time() . '] ' .
              $this->l('ENVOIMOINSCHER is not compatible with PrestaShop lower than 1.5.');
            Logger::addLog($error);
            return false;
        }

        // if curl's not avaliable, the module wont work
        if (!extension_loaded('curl')) {
            $error = '[ENVOIMOINSCHER][' . time() . '] ' .
              $this->l('installation : cannot install the module, curl is not available');
            Logger::addLog($error);
            return false;
        }

        // Get default configuration state
        $states = OrderState::getOrderStates((int)$this->getContext()->language->id);
        $states_array = array();
        foreach ($states as $state) {
            $states_array[] = $state['id_order_state'];
            if ($state['template'] === 'preparation') {
                $emc_cmd = (int)$state['id_order_state'];
            } elseif ($state['template'] === 'shipped') {
                $emc_envo = (int)$state['id_order_state'];
            } elseif ($state['template'] === 'order_canceled') {
                $emc_ann = (int)$state['id_order_state'];
            } elseif ($state['template'] == '') {
                $emc_liv = (int)$state['id_order_state'];
            }
        }

        // Set default configuration
        EnvoimoinscherModel::updateConfig('EMC_CMD', (int)$emc_cmd);
        EnvoimoinscherModel::updateConfig('EMC_ENVO', (int)$emc_envo);
        EnvoimoinscherModel::updateConfig('EMC_ANN', (int)$emc_ann);
        EnvoimoinscherModel::updateConfig('EMC_LIV', (int)$emc_liv);
        EnvoimoinscherModel::updateConfig('EMC_USER', '-2');
        EnvoimoinscherModel::updateConfig('EMC_SRV_MODE', EnvoimoinscherModel::MODE_CONFIG);
        EnvoimoinscherModel::updateConfig('EMC_MASS', EnvoimoinscherModel::WITH_CHECK);
        EnvoimoinscherModel::updateConfig('EMC_ASSU', '');
        EnvoimoinscherModel::updateConfig('EMC_INDI', 1);
        EnvoimoinscherModel::updateConfig('EMC_MULTIPARCEL', false);
        EnvoimoinscherModel::updateConfig('EMC_PICKUP_J1', '2');
        EnvoimoinscherModel::updateConfig('EMC_PICKUP_F1', '0');
        EnvoimoinscherModel::updateConfig('EMC_PICKUP_T1', '17');
        EnvoimoinscherModel::updateConfig('EMC_PICKUP_J2', '3');
        EnvoimoinscherModel::updateConfig('EMC_PICKUP_F2', '17');
        EnvoimoinscherModel::updateConfig('EMC_PICKUP_T2', '24');
        EnvoimoinscherModel::updateConfig('EMC_NATURE', false);
        EnvoimoinscherModel::updateConfig('EMC_ENV', 'TEST');
        EnvoimoinscherModel::updateConfig('EMC_TYPE', 'colis');
        EnvoimoinscherModel::updateConfig('EMC_ORDER', 0);
        EnvoimoinscherModel::updateConfig('EMC_WRAPPING', '');
        EnvoimoinscherModel::updateConfig('EMC_LABEL_DELIVERY_DATE', $this->l('Delivery scheduled : {DATE}'));
        EnvoimoinscherModel::updateConfig('EMC_TRACK_MODE', '2');
        EnvoimoinscherModel::updateConfig('EMC_LAST_CARRIER_UPDATE', '');
        EnvoimoinscherModel::updateConfig('EMC_ENABLED_LOGS', 0);
        EnvoimoinscherModel::updateConfig('EMC_FILTER_TYPE_ORDER', 'all');
        EnvoimoinscherModel::updateConfig('EMC_FILTER_STATUS', implode(';', $states_array));
        EnvoimoinscherModel::updateConfig('EMC_FILTER_CARRIERS', 'all');
        EnvoimoinscherModel::updateConfig('EMC_FILTER_START_DATE', 'all');
        EnvoimoinscherModel::updateConfig('EMC_DISABLE_CART', 0);

        // Execute queries
        $sql_file = Tools::file_get_contents(_PS_MODULE_DIR_ . '/envoimoinscher/sql/install.sql');
        $sql_file = str_replace('{PREFIXE}', _DB_PREFIX_, $sql_file);
        $query = explode('-- REQUEST --', $sql_file);
        foreach ($query as $q) {
            if (Db::getInstance()->execute($q) === false) {
                Logger::addLog(
                    '[ENVOIMOINSCHER][' . time() . '] ' .
                    $this->l('installation :  An error occured on the query : ') . $q
                );
                $this->tablesRollback();
                return false;
            }
        }

        if (parent::install() === false) {
            $this->tablesRollback();
            return false;
        }
        //enable module for all shop
        $this->enable(true);

        // for this version of module, the hooks are only registered for, at least, Prestashop 1.4
        $this->registerHook('processCarrier');
        $this->registerHook('newOrder');
        $this->registerHook('orderDetail');
        $this->registerHook('displayCarrierList');
        $this->registerHook('displayBeforeCarrier');
        $this->registerHook('updateCarrier');
        $this->registerHook('header');
        $this->registerHook('adminOrder');
        $this->registerHook('DisplayBackOfficeHeader');

        // add the new tab
        $tab = new Tab();
        $tab->class_name = 'AdminEnvoiMoinsCher';
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentShipping');
        $tab->module = 'envoimoinscher';
        $tab->name[(int)EnvoimoinscherModel::getConfig('PS_LANG_DEFAULT')] = 'EnvoiMoinsCher';
        if ($tab->add() === false) {
            if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                Logger::addLog(
                    '[ENVOIMOINSCHER][' . time() . '] ' .
                    $this->l('installation : Impossible to add the EnvoiMoinsCher button in the menu')
                );
            }
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
        // remove column in table
        //$columns = DB::getInstance()->executeS('DESCRIBE `'._DB_PREFIX_.'carrier`');
        //$column_to_remove = array('emc_services_id_es', 'emc_type'); // Column to remove

        // remove emc carriers
        $remove_emc_carriers = 'UPDATE `' . _DB_PREFIX_ . 'carrier` set deleted = 1 where external_module_name = "' .
          $this->name . '"';
        // remove envoimoinscher admin tab
        $remove_emc_tab = 'DELETE  FROM '. _DB_PREFIX_ .'tab WHERE class_name = "AdminEnvoiMoinsCher"';

        // If execution doesn't work
        if ($this->tablesRollback() === false ||
            parent::uninstall() === false ||
            Db::getInstance()->Execute($remove_emc_tab) === false ||
            DB::getInstance()->Execute($remove_emc_carriers) === false
        ) {
            return false;
        }
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
        foreach ($helper->getTablesNames() as $table) {
            $tables[] = '`' . _DB_PREFIX_ . $table . '`';
        }
        $remove_tables = 'SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS ' . implode(',', $tables);

        $remove_configs = 'DELETE FROM ' . _DB_PREFIX_ . 'configuration
        WHERE name LIKE "EMC_%"; SET FOREIGN_KEY_CHECKS = 1';

        return DB::getInstance()->execute($remove_tables) && DB::getInstance()->execute($remove_configs);
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
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());

        $emc_user = isset($config['EMC_USER']) ? (int)$config['EMC_USER'] : '-2';

        // pass the module offline if we are in the installation process (avoid updates bug)
        if ((int)$emc_user <= 2) {
            EnvoimoinscherModel::updateConfig('EMC_SRV_MODE', EnvoimoinscherModel::MODE_CONFIG);
        }

        // we load all the carriers if the configuration allow it
        if (EnvoimoinscherModel::getConfig('EMC_LAST_CARRIER_UPDATE') == '') {
            $this->loadAllCarriers(false);
        }

        require_once('Env/WebService.php');
        require_once('Env/User.php');

        // get default contact data
        $address_if_filled = array(
            'EMC_COMPANY' => false,
            'EMC_ADDRESS' => false,
            'EMC_POSTALCODE' => false,
            'EMC_CITY' => false,
            'EMC_TEL' => false,
            'EMC_MAIL' => false
        );

        // array with obligatory fields (must be filled up to make work this module)
        $obligatory = array(
            'EMC_KEY_TEST',
            'EMC_KEY_PROD',
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

        // Avoid display error message about EMC_KEY_TEST or EMC_KEY_PROD
        $emc_env_key_api_name = 'EMC_KEY_' . EnvoimoinscherModel::getConfig('EMC_ENV');
        if (EnvoimoinscherModel::getConfig($emc_env_key_api_name . '_DONOTCHECK') == 1) {
            $emc_env_key_api_name_to_discard =
              'EMC_KEY_' . (EnvoimoinscherModel::getConfig('EMC_ENV') == 'TEST' ? 'PROD' : 'TEST');
            $key = array_search($emc_env_key_api_name_to_discard, $obligatory);
            unset($obligatory[$key]);
        }

        // default configuration values
        $config['EMC_SERVICES'] = explode(',', $config['EMC_SERVICES']);

        $config['wsName'] = $this->ws_name; // ok
        $config['localVersion'] = $this->local_version; // ok

        foreach ($address_if_filled as $a => $address) {
            if ((!isset($config[$a]) || $config[$a] == '') && $address != false && $address != '') {
                $config[$a] = $address;
                EnvoimoinscherModel::updateConfig($a, $address);
            }
        }

        $datas = array(
            'local_bootstrap' => $this->useLocalBootstrap(),
            'introduction' => $this->getContentIntroduction(),
            'missedValues' => ((int)$emc_user > 2 ? $this->makeMissedList($obligatory, $config) : array()),
            'EMC_config' => $config,
            'link' => new Link(),
            'envUrl' => (!empty($config['EMC_ENV']) ?
              $this->environments[Tools::strtoupper($config['EMC_ENV'])]['link'] : null)
        );

        if ((int)$emc_user <= 2) {
            if ((int)$emc_user === -2) {
                $datas['content'] = $content . $this->getContentIntroduction();
                $content = '';
            } elseif ((int)$emc_user === -1) {
                $datas['content'] = $content . $this->getContentEmc();
                $content = '';
            } elseif ((int)$emc_user === 0) {
                $datas['content'] = $content . $this->getContentMerchant();
                $content = '';
            } elseif ((int)$emc_user === 1) {
                $datas['content'] = $content . $this->getContentSends(false);
                $content = '';
            } elseif ((int)$emc_user === 2) {
                $datas['content'] = $content . $this->getContentCarriers('Simple');
                $content = '';
            } else {
                $this->adminDisplayWarning($this->l('One error was encountered, this step will not work'));
                $datas['content'] = $content;
                $content = '';
            }
        }
        $datas['emcBaseDir'] = _MODULE_DIR_ . '/envoimoinscher/';
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
        $last_update = EnvoimoinscherModel::getConfig('EMC_LAST_CARRIER_UPDATE');
        $send_offers_update_warning = true;

        if ($last_update != '') {
            $date_limit = time() - (60 * 60 * 24 * 30);
            $date_update = strtotime($last_update);
            $send_offers_update_warning = $date_update < $date_limit;
        }
        $datas = array(
            'default_tab' => count($this->getUpgrades()) > 0 ? 'help' : (Tools::isSubmit('EMC_tabs') ?
              Tools::getValue('EMC_tabs') : 'merchant'),
            'tab_news' => $this->model->getApiNews($this->ws_name, $this->version),
            'tpl_news' => _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/news.tpl',
            'local_fancybox' => $this->useLocalFancybox(),
            'need_update' => $send_offers_update_warning,
            'PS_ver' => $ver[0],
            'PS_subver' => $ver[1],
            'module_version' => $this->version,
            'API_errors' => $api_params['error_code'],
            'EMC_config' => $helper->configArray(EnvoimoinscherModel::getConfigData()),
            'multiShipping' => EnvoimoinscherModel::getConfig('PS_ALLOW_MULTISHIPPING'),
            'successForm' => (int)$cookie->success_form,
            'modulePath' => $this->_path,
            'website_url' => $this->website_url
        );

        $smarty->assign($datas);

        return $this->display(__FILE__, '/views/templates/admin/getContentBody.tpl');
    }

    public function getContentHelp()
    {
        $smarty = $this->getContext()->smarty;

        //$helper = new EnvoimoinscherHelper();
        $datas = array(
            'emcBaseDir' => _MODULE_DIR_ . '/envoimoinscher/',
            'link' => new Link(),
            'upgrades' => $this->parseUpgradeXml(_PS_MODULE_DIR_ . 'envoimoinscher/sql/upgrades/upgrades.xml')
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
            'emcImgDir' => _PS_MODULE_DIR_ . 'envoimoinscher/img/'
        );

        $smarty->assign($datas);
        return $this->display(__FILE__, '/views/templates/admin/getContentIntroduction.tpl');
    }

    /**
     * Get ajax EMC content
     * @return template Smarty Template
     */
    private function getContentEmc()
    {
        $smarty = $this->getContext()->smarty;
        $cookie = $this->getContext()->cookie;

        $countries = Db::getInstance()->ExecuteS('SELECT c.iso_code, c.id_zone, cl.name
       FROM ' . _DB_PREFIX_ . 'country c
       JOIN ' . _DB_PREFIX_ . 'country_lang cl
       ON cl.id_country = c.id_country
       WHERE cl.id_lang = ' . (int)$cookie->id_lang . '
       ORDER BY cl.name ASC');

        $datas = array(
            // Country list
            'countries' => $countries,
            'baseDir' => __PS_BASE_URI__,
            'lang' => $this->context->language->iso_code,
            'token' => Tools::getValue('token')
        );

        if (Tools::getValue('choice')) {
            $datas['choice'] = Tools::getValue('choice');
        }

        $smarty->assign($datas);

        return $this->display(__FILE__, '/views/templates/admin/getContentEmc.tpl');
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
            'EMC_config' => $helper->configArray(EnvoimoinscherModel::getConfigData())
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

        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());

        $datas = array(
            // Configuration
            'EMC_config' => $config,
            'srvModes' => array(
                'config' => EnvoimoinscherModel::MODE_CONFIG,
                'online' => EnvoimoinscherModel::MODE_ONLINE
            ),
            'families' => $this->model->getOffersFamilies(),
            'familTableTpl' => $this->getTemplatePath('views/templates/admin/familyTpl.tpl'),
            'disableServices' =>
              isset($config['EMC_SRV_MODE']) && $config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_ONLINE,
            'pricing' => $this->pricing,
            'operators' => EnvoimoinscherModel::getOperatorsForType($config['EMC_NATURE']),
            'nameCategory' => EnvoimoinscherModel::getNameCategory($config['EMC_NATURE'])
        );

        $smarty->assign($datas);

        if ($type === 'Simple') {
            return $this->getContentCarriersSimple($smarty);
        } elseif ($type === 'Advanced') {
            return $this->getContentCarriersAdvanced($smarty);
        }
    }

    /**
     * Get ajax states
     * @return option list
     */
    public function returnStates()
    {
        $states = Db::getInstance()->executeS('
    SELECT s.iso_code, s.name
    FROM ' . _DB_PREFIX_ . 'state s
    LEFT JOIN ' . _DB_PREFIX_ . 'country c ON (s.`id_country` = c.`id_country`)
    WHERE c.iso_code = "' . pSQL(Tools::getValue('id_country')) . '" AND s.active = 1 AND c.`contains_states` = 1
    ORDER BY s.`name` ASC');

        if (is_array($states) && !empty($states)) {
            $list = '';
            foreach ($states as $state) {
                $list .= '<option value="' . $state['iso_code'] . '">' . $state['name'] . '</option>' . "\n";
            }
        } else {
            $list = 'false';
        }

        die($list);
    }

    /**
     * Simple Carrier
     * @param    Smarty $smarty Smarty
     * @return string                 Template parsed
     */
    private function getContentCarriersSimple(Smarty $smarty)
    {
        $datas = array(
            'simpleEconomicCarriers' => $this->model->getOffersByFamily(EnvoimoinscherModel::FAM_ECONOMIQUE),
            'link' => new Link()
        );

        $smarty->assign($datas);

        return $this->display(__FILE__, '/views/templates/admin/getContentCarriersSimple.tpl');
    }

    /**
     * Simple Carrier
     * @param    Smarty $smarty Smarty
     * @return string                 Template parsed
     */
    private function getContentCarriersAdvanced(Smarty $smarty)
    {
        $rows = $this->model->getDimensions();

        $datas = array(
            'dims' => $rows,
            'advancedExpressCarriers' => $this->model->getOffersByFamily(EnvoimoinscherModel::FAM_EXPRESSISTE),
            'link' => new Link()
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
        if (isset($api_params['POFR'])) {
            foreach ($api_params['POFR']['services'] as $api_param) {
                if (isset($api_param['parameters']['emballage.type_emballage'])) {
                    foreach ($api_param['parameters']['emballage.type_emballage']['values'] as $type) {
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

        $config = $helper->configArray(EnvoimoinscherModel::getConfigData()); // Get configs
        $config['wsName'] = $this->ws_name; // add wsName to config
        $config['localVersion'] = $this->local_version; // Add localVersionto config
        // Get pickup conf
        if (!isset($config['EMC_PICKUP_J1']) ||
            !isset($config['EMC_PICKUP_F1']) ||
            !isset($config['EMC_PICKUP_T1'])
        ) {
            $pick_up_conf = array(
                array(
                    'j' => 0,
                    'from' => 0,
                    'to' => 0
                ),
                array(
                    'j' => 0,
                    'from' => 0,
                    'to' => 0)
            );
        } else {
            $pick_up_conf = array(
                array(
                    'j' => $config['EMC_PICKUP_J1'],
                    'from' => $config['EMC_PICKUP_F1'],
                    'to' => $config['EMC_PICKUP_T1']
                ),
                array(
                    'j' => $config['EMC_PICKUP_J2'],
                    'from' => $config['EMC_PICKUP_F2'],
                    'to' => $config['EMC_PICKUP_T2']
                )
            );
        }

        $datas = array(
            // Configuration
            'local_fancybox' => $this->useLocalFancybox(),
            'emcBaseDir' => _MODULE_DIR_ . '/envoimoinscher/',
            'EMC_config' => $config,
            'shipTypes' => $this->ship_types,
            'shipNature' => $this->model->getCategoriesTree($config),
            'shipWrappingAvailable' => count($wrapping_types) > 1,
            'shipWrapping' => $wrapping_types,
            'pickupConf' => $pick_up_conf,
            'withoutMass' => EnvoimoinscherModel::WITHOUT_CHECK,
            'withMass' => EnvoimoinscherModel::WITH_CHECK,
            'weightUnit' => EnvoimoinscherModel::getConfig('PS_WEIGHT_UNIT'),
            'link' => new Link(),
            'disableServices' =>
              isset($config['EMC_SRV_MODE']) && $config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_ONLINE,
            'families' => $this->model->getOffersFamilies(),
            'familTableTpl' => $this->getTemplatePath('views/templates/admin/familyTpl.tpl'),
            'all' => $all
        );

        $smarty->assign($datas);

        return $this->display(__FILE__, '/views/templates/admin/getContentSends.tpl');
    }

    private function getContentSettings()
    {
        $smarty = $this->getContext()->smarty;
        $id_lang = (int)$this->getContext()->language->id;
        $helper = new EnvoimoinscherHelper();

        $config = $helper->configArray(EnvoimoinscherModel::getConfigData()); // Get configs

        require_once dirname(__FILE__) . '/Env/WebService.php';
        require_once dirname(__FILE__) . '/Env/User.php';

        $user_class = new EnvUser(
            array(
                'user' => $config['EMC_LOGIN'],
                'pass' => $config['EMC_PASS'],
                'key' => $config['EMC_KEY_' . $config['EMC_ENV']]
            )
        );
        $user_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
        $user_class->setEnv(Tools::strtolower($config['EMC_ENV']));
        $user_class->getEmailConfiguration();

        //get enabled carriers
        $sql = 'SELECT id_carrier, name FROM ' . _DB_PREFIX_ . 'carrier WHERE deleted=0';
        $enabled_carriers = Db::getInstance()->ExecuteS($sql);

        $datas = array(
            'EMC_config' => $config,
            'states' => OrderState::getOrderStates($id_lang),
            'modes' => $this->model->getTrackingModes(),
            'mailConfig' => $user_class->user_configuration['emails'],
            'enabledCarriers' => $enabled_carriers,
            'link' => new Link(),
        );
        $smarty->assign($datas);
        return $this->display(__FILE__, '/views/templates/admin/getContentSettings.tpl');

    }

    /**
     * Add CSS
     */
    public function hookDisplayBackOfficeHeader()
    {
        if ((Tools::strtolower(Tools::getValue('controller')) === 'AdminModules'
            && Tools::getValue('configure') === 'envoimoinscher')
            || Tools::strtolower(Tools::getValue('controller')) === 'adminenvoimoinscher'
        ) {
            $this->getContext()->controller->addJquery();
            $this->getContext()->controller->addJqueryUI('ui.datepicker');
            $this->getContext()->controller->addCSS(
                $this->_path . '/views/css/back-office.css?version=' . $this->version,
                'all'
            );
        }
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
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());

        $bd = Db::getInstance();
        /* we first get every product with attributes */
        $sql = 'SELECT distinct
           pl.name, p.id_product, al.name AS `attribute_name`, a.id_attribute_group, al.id_attribute
           FROM
           ' . _DB_PREFIX_ . 'product p,
           ' . _DB_PREFIX_ . 'product_lang pl,
           ' . _DB_PREFIX_ . 'product_attribute pa,
           ' . _DB_PREFIX_ . 'attribute a,
           ' . _DB_PREFIX_ . 'product_attribute_combination pac,
           ' . _DB_PREFIX_ . 'attribute_lang al
           WHERE
           pa.id_product = p.id_product AND
           pl.id_product = p.id_product AND
           pa.id_product_attribute = pac.id_product_attribute AND
           pac.id_attribute = al.id_attribute AND
           pac.id_attribute = a.id_attribute AND
           pl.id_lang = ' . (int)$cookie->id_lang . '
           ORDER BY pl.name';
        $products_list = $bd->ExecuteS($sql);
        $products = array();
        foreach ($products_list as $p) {
            if (!isset($products[$p['id_product']])) {
                $products[$p['id_product']] = array();
                $products[$p['id_product']]['name'] = $p['name'];
                $products[$p['id_product']]['id_product'] = $p['id_product'];
                $products[$p['id_product']]['attributes'] = array();
            }
            if (!isset($products[$p['id_product']]['attributes'][$p['id_attribute_group']])) {
                $products[$p['id_product']]['attributes'][$p['id_attribute_group']] = array();
            }
            $products[$p['id_product']]['attributes'][$p['id_attribute_group']][$p['id_attribute']] = array();
            $products[$p['id_product']]['attributes'][$p['id_attribute_group']][$p['id_attribute']]['id_attribute'] =
              $p['id_attribute'];
            $products[$p['id_product']]['attributes'][$p['id_attribute_group']][$p['id_attribute']]['attribute_name'] =
              $p['attribute_name'];
        }
        /* we now get every product without attributes */
        $sql = 'SELECT pl.name, p.id_product
        FROM ' . _DB_PREFIX_ . 'product p, ' . _DB_PREFIX_ . 'product_lang pl
        WHERE pl.id_product = p.id_product
        AND p.id_product NOT IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute)
        AND pl.id_lang = ' . (int)$cookie->id_lang . '
        ORDER BY pl.name';
        $products_list = $bd->ExecuteS($sql);

        foreach ($products_list as $p) {
            if (!isset($products[$p['id_product']])) {
                $products[$p['id_product']] = array();
                $products[$p['id_product']]['name'] = $p['name'];
                $products[$p['id_product']]['id_product'] = $p['id_product'];
                $products[$p['id_product']]['attributes'] = array();
            }
        }
        ksort($products);

        /* We start now the generation of products with their attributes */
        $products_smarty = array();
        //$i = 0;
        foreach ($products as $product) {
            $product_names = array();
            $product_names[0]['name'] = $product['name'];
            $product_names[0]['value'] = $product['id_product'];
            foreach ($product['attributes'] as $attribute_group) {
                $tmp_product_names = $product_names;
                $product_names = array();
                foreach ($attribute_group as $attribute) {
                    foreach ($tmp_product_names as $tmp_product_name) {
                        $product_names[count($product_names)] = array(
                            'name' => ($tmp_product_name['name'] . ' - ' . $attribute['attribute_name']),
                            'value' => ($tmp_product_name['value'] . '_' . $attribute['id_attribute'])
                        );
                    }
                }
            }
            $products_smarty = array_merge($products_smarty, $product_names);
        }

        $smarty->assign('configEmc', $config);
        $smarty->assign('products', $products_smarty);
        $smarty->assign('token', Tools::getValue('token'));
        $smarty->assign('countries', Db::getInstance()->ExecuteS(
            'SELECT c.iso_code, cl.name
            FROM ' . _DB_PREFIX_ . 'country c
            JOIN ' . _DB_PREFIX_ . 'country_lang cl
            ON cl.id_country = c.id_country
            WHERE cl.id_lang = ' . (int)$cookie->id_lang . '
            ORDER BY cl.name ASC'
        ));
        $smarty->assign('successForm', (int)$cookie->form_success);
        $smarty->assign('adminImg', _PS_ADMIN_IMG_);
        $smarty->assign('baseDir', __PS_BASE_URI__);

        $error = -1;
        $error_msg = '';
        $cookie->form_success = 0;

        if (!empty($_POST) && Tools::isSubmit('submitForm')) {
            if (!Tools::getValue('fromPostalCode') || !Tools::getValue('fromCity')
              || !Tools::getValue('toPostalCode') || !Tools::getValue('toCity')) {
                $error = 1;
                $error_msg = $this->l('Please fill in zip code and city fields in order to make a simulation');
            } else {
                // Get sender
                $from = array(
                    'pays' => 'FR',
                    'code_postal' => Tools::getValue('fromPostalCode'),
                    'ville' => Tools::getValue('fromCity'),
                    'type' => 'entreprise',
                    'societe' => $config['EMC_COMPANY'],
                    'adresse' => Tools::getValue('fromAddr') == false ? ' ' : Tools::getValue('fromAddr'),
                    'civilite' => $config['EMC_CIV'],
                    'prenom' => $config['EMC_FNAME'],
                    'nom' => $config['EMC_LNAME'],
                    'email' => $config['EMC_MAIL'],
                    'tel' => EnvoimoinscherHelper::normalizeTelephone($config['EMC_TEL']),
                    'infos' => $config['EMC_COMPL']
                );

                // Get recipient
                $country_iso = Tools::getValue('toCountry');
                $to = array(
                    'pays' => $country_iso,
                    'code_postal' => Tools::getValue('toPostalCode'),
                    'ville' => Tools::getValue('toCity'),
                    'type' => 'particulier',
                    'adresse' => Tools::getValue('toAddr') == false ? ' ' : Tools::getValue('toAddr'),
                    'civilite' => $config['EMC_CIV'],
                    'prenom' => $config['EMC_FNAME'],
                    'nom' => $config['EMC_LNAME'],
                    'email' => $config['EMC_MAIL'],
                    'tel' => EnvoimoinscherHelper::normalizeTelephone($config['EMC_TEL']),
                    'infos' => $config['EMC_COMPL']
                );

                /* Create fake cart to calculate costs */
                // Create fake customer
                $customer_tmp = new Customer();
                $customer_tmp->firstname = $to['prenom'];
                $customer_tmp->lastname = $to['nom'];
                $customer_tmp->email = $to['email'];
                $customer_tmp->passwd = '26c1bba483c9b88321ce97df511539c6';
                $customer_tmp->add();

                // Create fake address
                $addr_tmp = new Address();
                $addr_tmp->id_customer = $customer_tmp->id;
                $addr_tmp->id_country = $this->model->getCountryIdFromIso($to['pays']);
                $addr_tmp->postcode = $to['code_postal'];
                $addr_tmp->firstname = $to['prenom'];
                $addr_tmp->lastname = $to['nom'];
                $addr_tmp->address1 = $to['adresse'];
                $addr_tmp->city = $to['ville'];
                // required fieds to new customers
                $requiredAddressFields = $addr_tmp->getFieldsRequiredDatabase();
                if (!empty($requiredAddressFields)) {
                    foreach ($requiredAddressFields as $requiredAddressField) {
                        if ($requiredAddressField["field_name"] == "company") {
                            $addr_tmp->company = 'placeholderCompany';
                        }
                        if ($requiredAddressField["field_name"] == "address2") {
                            $addr_tmp->address2 = 'placeholderAddress2';
                        }
                        if ($requiredAddressField["field_name"] == "other") {
                            $addr_tmp->other = 'placeholderOther';
                        }
                        if ($requiredAddressField["field_name"] == "phone") {
                            $addr_tmp->phone = '0102030405';
                        }
                        if ($requiredAddressField["field_name"] == "phone_mobile") {
                            $addr_tmp->phone_mobile = '0605040302';
                        }
                        if ($requiredAddressField["field_name"] == "vat_number") {
                            $addr_tmp->vat_number = '01234567891011';
                        }
                        if ($requiredAddressField["field_name"] == "dni") {
                            $addr_tmp->dni = '11111111-A';
                        }
                    }
                }
                $addr_tmp->alias = "TEMPORARY_ADDRESS_TO_DELETE";
                $addr_tmp->save();

                // Create fake cart
                $cart_tmp = new Cart();
                $cart_tmp->id_currency = Currency::getDefaultCurrency()->id;
                $cart_tmp->id_customer = $customer_tmp->id;
                $cart_tmp->id_lang = $cookie->id_lang;
                $cart_tmp->id_address_delivery = $addr_tmp->id;
                $cart_tmp->add();
                $shop = Context::getContext()->shop;

                // Get parcel info
                $attributes = explode('_', Tools::getValue('product'));
                $productId = array_shift($attributes);
                $productAttributeId = $this->model->getProductAttributeId($productId, $attributes);

                // add product to fake cart
                Db::getInstance()->insert('cart_product', array(
                  'id_product' => (int)$productId,
                  'id_product_attribute' => (int)$productAttributeId,
                  'id_cart' => (int)$cart_tmp->id,
                  'id_address_delivery' => (int)$addr_tmp->id,
                  'id_shop' => $shop->id,
                  'quantity' => 1,
                  'date_add' => date('Y-m-d H:i:s')
                ));

                $weight = $this->model->getCartWeight($cart_tmp->id);
                $dimensions = $this->model->getDimensionsByWeight($weight);

                $parcels = array(
                    1 => array(
                        'poids' => $weight,
                        'longueur' => isset($dimensions[0]['length_ed']) ? $dimensions[0]['length_ed'] : 0,
                        'largeur' => isset($dimensions[0]['width_ed']) ? $dimensions[0]['width_ed'] : 0,
                        'hauteur' => isset($dimensions[0]['height_ed']) ? $dimensions[0]['height_ed'] : 0
                    )
                );

                // additional parameters
                $params = array(
                  'collecte' => $this->setCollectDate(
                      array(
                          array(
                              'j' => $config['EMC_PICKUP_J1'],
                              'from' => $config['EMC_PICKUP_F1'],
                              'to' => $config['EMC_PICKUP_T1']
                          ),
                          array(
                              'j' => $config['EMC_PICKUP_J2'],
                              'from' => $config['EMC_PICKUP_F2'],
                              'to' => $config['EMC_PICKUP_T2']
                          )
                      )
                  ),
                  'delai' => 'aucun',
                  'code_contenu' => $config['EMC_NATURE'],
                  'valeur' => $cart_tmp->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
                  'module' => $this->ws_name,
                  'version' => $this->local_version,
                  'emc_type' => $config['EMC_TYPE'],
                  'testPage' => true
                );

                $offers = $this->getQuote($from, $to, $parcels, $params, false, false);

                if (isset($offers['isError']) && $offers['isError'] == 1) {
                    $error = 1;
                    $error_msg = $offers['message'];
                } elseif (count($offers) == 0) {
                    $error = 1;
                    $error_msg = $this->l('No offers found for your search');
                } else {
                    $error = 0;
                    $out_offers = array();

                    // convert price from euro to default currency if necessary
                    $defaultCurrency = new Currency((int)$cart_tmp->id_currency);
                    if ($defaultCurrency->iso_code != 'EUR') {
                        $euro = $this->model->getEuro();
                        foreach ($offers as $key => $offer) {
                            $convertedPrice = Tools::convertPrice($offer['price']['tax-exclusive'], $euro, false);
                            if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                                $message = sprintf(
                                    $this->l('Quotation - converting price for carrier %1$s to %2$s: %3$s%2$s'),
                                    $offer['operator']['code'] . '_' .$offer['service']['code'],
                                    $defaultCurrency->sign,
                                    $convertedPrice
                                );
                                Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                            }
                            $offers[$key]['price']['tax-exclusive'] = $convertedPrice;
                        }
                    }

                    // exclude from display carriers not in PS ranges/zones configuration
                    $offers = $this->psCarriersExclude($offers, $cart_tmp->id);

                    if (count($offers) == 0) {
                        $error = 1;
                        $error_msg = $this->l('No offers found for your search');
                    }

                    // apply rate price if needed
                    $offers = $this->applyRatePrice($offers, $cart_tmp->id);

                    // set first carrier as default carrier for cart rule calculation
                    $firstOffer = current($offers);
                    $firstOfferCarrierId = $this->model->getCarrierIdByCode(
                        $firstOffer['service']['code'],
                        $firstOffer['operator']['code']
                    );
                    $cart_tmp->id_carrier = $firstOfferCarrierId;
                    $cart_tmp->update();

                    // apply Prestashop configured extra charges
                    $offers = $this->psPriceOverride($offers, $cart_tmp->id);

                    // set carrier free according to Prestashop configuration
                    $offers = $this->applyFree($offers, $cart_tmp->id);

                    foreach ($offers as $offer) {
                        $carrierId = $this->model->getCarrierIdByCode(
                            $offer['service']['code'],
                            $offer['operator']['code']
                        );
                        $carrier_tax = Tax::getCarrierTaxRate(
                            (int)$carrierId,
                            (int)$cart_tmp->{EnvoimoinscherModel::getConfig('PS_TAX_ADDRESS_TYPE')}
                        );

                        $taxInclusive = (float)Tools::ps_round(
                            (float)$offer['price']['tax-exclusive']*(1 + ($carrier_tax / 100)),
                            2
                        );

                        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                            $message = sprintf(
                                $this->l('Quotation - adding carrier tax %1$s%2$s to carrier %3$s: %4$s%5$s'),
                                $carrier_tax,
                                "%",
                                $offer['operator']['code'] . '_' .$offer['service']['code'],
                                $taxInclusive,
                                $defaultCurrency->sign
                            );
                            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                        }

                        $out_offers[] = array(
                            'service' => $offer['service']['label'],
                            'operator' => $offer['operator']['label'],
                            'priceHT' => (float)Tools::ps_round((float)$offer['price']['tax-exclusive'], 2),
                            'priceTTC' => $taxInclusive,
                            'characteristics' => '<b>-</b>' . implode('<br /><b>-</b>  ', $offer['characteristics']),
                            'currencySign' => $defaultCurrency->sign
                        );
                    }

                    $smarty->assign('offers', $out_offers);

                    // delete temporary objects
                    $addr_tmp->delete();
                    $customer_tmp->delete();
                    $cart_tmp->delete();
                }
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
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $params = array();

        // init pagers
        require_once('lib/Pager.php');

        //add language to params
        $params['lang'] = $cookie->id_lang;

        //add filters
        if (Tools::isSubmit('type_order')) {
            $params['filterBy']['type_order'] = Tools::getValue('type_order');
        } else {
            $params['filterBy']['type_order'] = $config['EMC_FILTER_TYPE_ORDER'];
        }

        if (Tools::isSubmit('filter_id_order')) {
            $params['filterBy']['filter_id_order'] = (int)Tools::getValue('filter_id_order');
        }

        if (Tools::isSubmit('status')) {
            $params['filterBy']['status'] = Tools::getValue('status');
        } else {
            $params['filterBy']['status'] = explode(';', $config['EMC_FILTER_STATUS']);
        }

        if (Tools::isSubmit('carriers')) {
            $params['filterBy']['carriers'] = Tools::getValue('carriers');
        } else {
            $params['filterBy']['carriers'] = $config['EMC_FILTER_CARRIERS'];
        }

        if (Tools::isSubmit('start_order_date')) {
            if ('all' != Tools::getValue('start_order_date')) {
                $params['filterBy']['start_order_date'] = Tools::getValue('start_order_date');
            }
        } else {
            if ($config['EMC_FILTER_START_DATE'] != 'all') {
                $params['filterBy']['start_order_date'] =
                  date('Y-m-d', strtotime('-1 ' . $config['EMC_FILTER_START_DATE']));
            }
        }

        if (Tools::isSubmit('end_order_date')) {
            if ('all' != Tools::getValue('end_order_date')) {
                $params['filterBy']['end_order_date'] = Tools::getValue('end_order_date');
            }
        }

        if (Tools::isSubmit('recipient')) {
            $words = explode(' ', trim(Tools::getValue('recipient')));
            foreach ($words as $key => $value) {
                $params['filterBy']['recipient'][$key] = $value;
            }
        }

        // generate filter url
        $filter_url = '&type_order=' . $params['filterBy']['type_order']
            . (isset($params['filterBy']['filter_id_order']) ?
              '&filter_id_order=' . $params['filterBy']['filter_id_order'] : '')
            . '&carriers=' . $params['filterBy']['carriers']
            . (isset($params['filterBy']['start_order_date']) ?
              '&start_order_date=' . $params['filterBy']['start_order_date'] : '')
            . (isset($params['filterBy']['end_order_date']) ?
              '&end_order_date=' . $params['filterBy']['end_order_date'] : '')
            . (isset($params['filterBy']['recipient']) ?
              '&recipient=' . implode('+', $params['filterBy']['recipient']) : '');
        if (isset($params['filterBy']['status']) && is_array($params['filterBy']['status'])) {
            foreach ($params['filterBy']['status'] as $key => $value) {
                $filter_url .= '&status%5B%5D=' . $value;
            }
        }

        // get orders
        $orders_count = $this->model->getEligibleOrdersCount($params);

        $page = 1;
        if (Tools::isSubmit('p')) {
            $page = (int)Tools::getValue('p');
        }

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
        $limits = 'LIMIT ' . (int)$start . ',' . (int)$per_page;
        $smarty->assign('pager', $pager->setPages());

        $orders = $this->model->getEligibleOrders($params, $limits);

        //get enabled carriers
        $sql = 'SELECT id_carrier, name FROM ' . _DB_PREFIX_ . 'carrier WHERE deleted=0';
        $enabled_carriers = Db::getInstance()->ExecuteS($sql);

        // all orders to send
        $planning = $this->model->getLastPlanning();
        $orders_to_send = Tools::jsonDecode($planning['orders_eopl'], true);
        $param_config =
          '&configure=envoimoinscher&tab_module=shipping_logistics&module_name=envoimoinscher&EMC_tabs=settings';
        $smarty->assign('filters', $params['filterBy']);
        $smarty->assign('filterUrl', $filter_url);
        $smarty->assign('tab_news', $this->model->getApiNews($this->ws_name, $this->version));
        $smarty->assign('tpl_news', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/news.tpl');
        $smarty->assign('local_fancybox', $this->useLocalFancybox());
        $smarty->assign('local_bootstrap', $this->useLocalBootstrap());
        $smarty->assign('emcBaseDir', _MODULE_DIR_ . '/envoimoinscher/');
        $smarty->assign(
            'tokenOrder',
            Tools::getAdminToken(
                'AdminOrders' . (int)Tab::getIdFromClassName('AdminOrders') . (int)$cookie->id_employee
            )
        );
        $smarty->assign('token', Tools::getValue('token'));
        $smarty->assign('orders', $orders);
        $smarty->assign('ordersCount', count($orders));
        $smarty->assign('ordersTodo', count($orders_to_send['todo']));
        $smarty->assign('defaultStatus', $config['EMC_CMD']);
        $smarty->assign('withCheck', $config['EMC_MASS'] == EnvoimoinscherModel::WITH_CHECK);
        $smarty->assign('showTable', count($orders) > 0);
        $smarty->assign('successSend', (int)$cookie->success_send);
        $smarty->assign('errorLabels', (int)$cookie->error_labels);
        $smarty->assign('pagerTemplate', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/pager_template.tpl');
        $smarty->assign(
            'submenuTemplate',
            _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/order_submenu_template.tpl'
        );
        $smarty->assign(
            'ordersTableTemplate',
            _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/orders_table_template.tpl'
        );
        $smarty->assign('massTemplate', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/massOrders.tpl');
        $smarty->assign('ordersSendTop', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/table_send.tpl');
        $smarty->assign('ordersSendBottom', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/table_send.tpl');
        $smarty->assign('states', OrderState::getOrderStates((int)$this->getContext()->language->id));
        $smarty->assign('enabledCarriers', $enabled_carriers);
        $smarty->assign('actual', '');
        $smarty->assign('actual', '');
        $smarty->assign('baseDir', __PS_BASE_URI__);
        $smarty->assign('configPage', $this->link->getAdminLink('AdminModules') . $param_config);
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
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/WebService.php');
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/Quotation.php');
        $admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
        $helper = new EnvoimoinscherHelper(EnvoimoinscherModel::getConfig('EMC_TYPE'));
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $emc_order = new EnvoimoinscherOrder($this->model);

        // check if any order has been selected
        if (!Tools::getValue('do') && !Tools::getValue('results') && !Tools::getValue('mide')) {
            // check if any order has been selected
            if (!Tools::isSubmit('orders')) {
                Tools::redirectAdmin($admin_link_base);
            }

            $orders = Tools::getValue('orders'); // Get orders

            $emc_order->constructOrdersLists($orders, Tools::getValue('typeDb'));

            // redirect to first order to do
            if ($config['EMC_MASS'] == EnvoimoinscherModel::WITH_CHECK || Tools::getValue('type') != 'withEmc') {
                Tools::redirectAdmin($admin_link_base . '&option=send&id_order=' . (int)$orders[0] . '');
            }
            // redirect to orders main page
            Tools::redirectAdmin($admin_link_base);
        } elseif ((int)Tools::getValue('do') == 1) {
            $result = array(
                'result' => 1,
                'doOthers' => 1
            );
            // do order actions
            $emc_order->setOrderId(0);
            $id_order = (int)$emc_order->getOrderId();
            $result['id'] = $id_order;
            if ($id_order > 0) {
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
                if (!$emc_order->doOtherOrders()) {
                    $result['doOthers'] = 0;
                }
            } else {
                $result = array(
                    'result' => 0,
                    'doOthers' => 0
                );
            }
        } elseif (Tools::getValue('mode') == 'skip') {
            $emc_order->skipOrder((int)Tools::getValue('previous'));
            $emc_order->incrementSkipped();
            $emc_order->updateOrdersList();
            Tools::redirectAdmin($admin_link_base . '&id_order=' . (int)Tools::getValue('id_order') . '&option=send');
            die();
        } elseif (Tools::getValue('results') == 1) {
            $result = $emc_order->getFinalResult('array');
            $emc_order->cleanOrders(true);
        }
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
        if (Tools::isSubmit('filter_id_order')) {
            $filters['filterBy']['filter_id_order'] = (int)Tools::getValue('filter_id_order');
        }
        if (Tools::isSubmit('carriers')) {
            $filters['filterBy']['carriers'] = Tools::getValue('carriers');
        }
        if (Tools::isSubmit('start_order_date')) {
            $filters['filterBy']['start_order_date'] = Tools::getValue('start_order_date');
        }
        if (Tools::isSubmit('end_order_date')) {
            $filters['filterBy']['end_order_date'] = Tools::getValue('end_order_date');
        }
        if (Tools::isSubmit('start_creation_date')) {
            $filters['filterBy']['start_creation_date'] = Tools::getValue('start_creation_date');
        }
        if (Tools::isSubmit('end_creation_date')) {
            $filters['filterBy']['end_creation_date'] = Tools::getValue('end_creation_date');
        }
        if (Tools::isSubmit('recipient')) {
            $words = explode(' ', trim(Tools::getValue('recipient')));
            foreach ($words as $key => $value) {
                $filters['filterBy']['recipient'][$key] = $value;
            }
        }

        // construct filter request
        $sql = '';
        if (!empty($filters['filterBy'])) {
            //by order id
            if (isset($filters['filterBy']['filter_id_order'])) {
                $sql .= ' AND o.id_order = ' . (int)$filters['filterBy']['filter_id_order'];
            }

            //by carrier
            if (isset($filters['filterBy']['carriers'])) {
                if ($filters['filterBy']['carriers'] == 'del') {
                    $sql .= ' AND c.name NOT IN (SELECT name FROM ' . _DB_PREFIX_ . 'carrier WHERE deleted=0)';
                } elseif ($filters['filterBy']['carriers'] != 'all') {
                    $sql .= ' AND c.name LIKE "' . pSQL($filters['filterBy']['carriers']) . '"';
                }
            }

            //by order date
            if (isset($filters['filterBy']['start_order_date'])) {
                $sql .= " AND eo.date_order_eor >= STR_TO_DATE('" . pSQL($filters['filterBy']['start_order_date']) .
                  "', '%Y-%m-%d')";
            }

            if (isset($filters['filterBy']['end_order_date'])) {
                $sql .= " AND eo.date_order_eor < DATE_ADD(STR_TO_DATE('" .
                  pSQL($filters['filterBy']['end_order_date']) . "', '%Y-%m-%d'), INTERVAL 1 DAY)";
            }

            //by creation date
            if (isset($filters['filterBy']['start_creation_date'])) {
                $sql .= " AND o.date_add >= STR_TO_DATE('" . pSQL($filters['filterBy']['start_creation_date']) .
                  "', '%Y-%m-%d')";
            }

            if (isset($filters['filterBy']['end_creation_date'])) {
                $sql .= " AND o.date_add < DATE_ADD(STR_TO_DATE('" . pSQL($filters['filterBy']['end_creation_date']) .
                  "', '%Y-%m-%d'), INTERVAL 1 DAY)";
            }

            //by recipient (string contained in company, first name, last name or email)
            if (isset($filters['filterBy']['recipient']) && !empty($filters['filterBy']['recipient'])) {
                foreach ($filters['filterBy']['recipient'] as $key => $value) {
                    $sql .= ' AND (INSTR(a.firstname, "' . pSQL($value) . '") > 0
                    OR INSTR(a.lastname, "' . pSQL($value) . '") > 0
                    OR INSTR(cr.email, "' . pSQL($value) . '") > 0)';
                }
            }
        }

        $smarty->assign(
            'tokenOrder',
            Tools::getAdminToken(
                'AdminOrders' . (int)Tab::getIdFromClassName('AdminOrders') . (int)$cookie->id_employee
            )
        );
        $count_query = Db::getInstance()->ExecuteS(
            'SELECT COUNT(eo.' . _DB_PREFIX_ . 'orders_id_order) AS allCmd
            FROM ' . _DB_PREFIX_ . 'emc_orders eo
            JOIN ' . _DB_PREFIX_ . 'orders o ON eo.' . _DB_PREFIX_ . 'orders_id_order = o.id_order
            JOIN ' . _DB_PREFIX_ . 'address a ON a.id_address = o.id_address_delivery
            JOIN ' . _DB_PREFIX_ . 'carrier c ON c.id_carrier = o.id_carrier
            JOIN ' . _DB_PREFIX_ . 'customer cr ON cr.id_customer = a.id_customer
            WHERE eo.ref_emc_eor != ""' . $sql
        );

        // set pager
        $page = 1;
        $per_page = 20;
        $all_pages = $count_query[0]['allCmd'];
        if (Tools::isSubmit('p')) {
            $page = (int)Tools::getValue('p');
        }
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
        $orders = $this->model->getDoneOrders(
            array(
                'lang' => $cookie->id_lang,
                'start' => $start,
                'limit' => $per_page,
                'filters' => $sql
            )
        );

        // get additional sql to limit following requests
        $limit = '';
        if (is_array($orders) && !empty($orders)) {
            $limit .= ' AND ' . _DB_PREFIX_ . 'orders_id_order IN (';
            $i = 0;
            foreach ($orders as $key => $value) {
                if ($i != 0 && $value['PsOrderId'] != "") {
                    $limit .= ',';
                }
                if ($value['PsOrderId'] != "") {
                    $limit .= '"' . $value['PsOrderId'] . '"';
                }
                $i++;
            }
            $limit .= ')';
        }

        // get order ids with generated documents
        /*$ordersWithGeneratedDocuments =
        Db::getInstance()->ExecuteS(
            'SELECT '._DB_PREFIX_.'orders_id_order AS orderIds FROM '._DB_PREFIX_.'emc_documents
            WHERE type_ed = "label" AND generated_ed = 1'.$limit
        );*/

        $orders_with_documents = array();
        foreach ($orders as $key => $value) {
            if (!in_array($value['id_order'], $orders_with_documents)) {
                $orders_with_documents[] = $value['id_order'];
            }
        }

        // check if any document is generated
        $order_documents = array();
        if (count($orders_with_documents) > 0) {
            $generated_documents =
                Db::getInstance()->ExecuteS(
                    'SELECT * FROM ' . _DB_PREFIX_ .
                    'emc_documents  WHERE generated_ed = 1 AND ' . _DB_PREFIX_ .
                    'orders_id_order in (' . implode(',', $orders_with_documents) . ')'
                );

            // get all documents for each order
            foreach ($generated_documents as $document) {
                $id = $document[_DB_PREFIX_ . 'orders_id_order'];
                if (!isset($order_documents[$id])) {
                    $order_documents[$id] = array();
                }
                $order_documents[$id][$document['type_ed']] = $document['link_ed'];
            }
        }

        // get order ids to exclude from ajax process
        $no_ajax_order_ids_query = Db::getInstance()->ExecuteS(
            'SELECT eo.' . _DB_PREFIX_ . 'orders_id_order AS orderIds FROM ' . _DB_PREFIX_ . 'emc_orders eo
            JOIN ' . _DB_PREFIX_ . 'orders o ON eo.' . _DB_PREFIX_ . 'orders_id_order = o.id_order
            JOIN ' . _DB_PREFIX_ . 'carrier c ON c.id_carrier = o.id_carrier
            JOIN ' . _DB_PREFIX_ . 'emc_services es ON es.ref_carrier = c.id_reference
            JOIN ' . _DB_PREFIX_ . 'emc_operators eop ON eop.code_eo = es.emc_operators_code_eo
            WHERE eo.ref_emc_eor != ""
            AND (eo.' . _DB_PREFIX_ . 'orders_id_order IN (
                SELECT ' . _DB_PREFIX_ . 'orders_id_order FROM ' . _DB_PREFIX_ . 'emc_documents d
                  JOIN ' . _DB_PREFIX_ . 'orders o ON d.' . _DB_PREFIX_ . 'orders_id_order = o.id_order
                  JOIN ' . _DB_PREFIX_ . 'carrier c ON c.id_carrier = o.id_carrier
                  JOIN ' . _DB_PREFIX_ . 'emc_services es ON es.ref_carrier = c.id_reference
                  WHERE es.emc_operators_code_eo LIKE "POFR" AND d.type_ed = "remise")
            OR eo.' . _DB_PREFIX_ . 'orders_id_order IN (
                SELECT ' . _DB_PREFIX_ . 'orders_id_order FROM ' . _DB_PREFIX_ . 'emc_documents d
                  JOIN ' . _DB_PREFIX_ . 'orders o ON d.' . _DB_PREFIX_ . 'orders_id_order = o.id_order
                  JOIN ' . _DB_PREFIX_ . 'carrier c ON c.id_carrier = o.id_carrier
                  JOIN ' . _DB_PREFIX_ . 'emc_services es ON es.ref_carrier = c.id_reference
                  WHERE es.emc_operators_code_eo NOT LIKE "POFR" AND d.type_ed = "label" AND d.generated_ed = 1)
            )' . $limit
        );

        $no_ajax_order_ids = array();
        foreach ($no_ajax_order_ids_query as $key => $value) {
            array_push($no_ajax_order_ids, $value['orderIds']);
        }

        //send filter settings back
        if (isset($filters['filterBy'])) {
            $smarty->assign('filters', $filters['filterBy']);
            $filter_url = '';
            if (isset($filters['filterBy']['filter_id_order'])) {
                $filter_url .= '&filter_id_order=' . $filters['filterBy']['filter_id_order'];
            }
            if (isset($filters['filterBy']['carriers'])) {
                $filter_url .= '&carriers=' . $filters['filterBy']['carriers'];
            }
            if (isset($filters['filterBy']['start_order_date'])) {
                $filter_url .= '&start_order_date=' . $filters['filterBy']['start_order_date'];
            }
            if (isset($filters['filterBy']['end_order_date'])) {
                $filter_url .= '&end_order_date=' . $filters['filterBy']['end_order_date'];
            }
            if (isset($filters['filterBy']['start_order_date'])) {
                $filter_url .= '&start_creation_date=' . $filters['filterBy']['start_creation_date'];
            }
            if (isset($filters['filterBy']['end_order_date'])) {
                $filter_url .= '&end_creation_date=' . $filters['filterBy']['end_creation_date'];
            }
            if (isset($filters['filterBy']['recipient'])) {
                $filter_url .= '&recipient=';
                $i = 0;
                foreach ($filters['filterBy']['recipient'] as $key => $value) {
                    if ($i == 0) {
                        $filter_url .= $value;
                    } else {
                        $filter_url .= '+' . $value;
                    }
                    $i++;
                }
            }
            $smarty->assign('filterUrl', $filter_url);
        }

        //get EMC enabled carriers
        $rq = 'SELECT id_carrier, name FROM ' . _DB_PREFIX_ .
          'carrier WHERE deleted=0 AND external_module_name = "envoimoinscher"';
        $enabled_carriers = Db::getInstance()->ExecuteS($rq);
        $smarty->assign('enabledCarriers', $enabled_carriers);

        $smarty->assign('tab_news', $this->model->getApiNews($this->ws_name, $this->version));
        $smarty->assign('tpl_news', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/news.tpl');
        $smarty->assign('local_fancybox', $this->useLocalFancybox());
        $smarty->assign('local_bootstrap', $this->useLocalBootstrap());
        $smarty->assign('emcBaseDir', _MODULE_DIR_ . '/envoimoinscher/');
        $smarty->assign('token', Tools::getValue('token'));
        $smarty->assign('orders', $orders);
        $smarty->assign('allOrders', count($orders));
        $smarty->assign('noAjaxOrderIds', $no_ajax_order_ids);
        //$smarty->assign('labelGeneratedOrderIds', $labelGeneratedOrderIds);
        //$smarty->assign('remiseGeneratedOrderIds', $remiseGeneratedOrderIds);
        $smarty->assign('orderDocuments', $order_documents);
        $smarty->assign('successSend', (int)$cookie->success_send);
        $smarty->assign('errorLabels', (int)$cookie->error_labels);
        $smarty->assign('pagerTemplate', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/pager_template.tpl');
        $smarty->assign(
            'ordersTableTemplate',
            _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/orders_history_table_template.tpl'
        );
        $smarty->assign(
            'submenuTemplate',
            _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/order_submenu_template.tpl'
        );
        $smarty->assign('actual', 'history');
        $smarty->assign('baseDir', __PS_BASE_URI__);
        $cookie->success_send = 0;
        $cookie->error_labels = 0;
        return $this->display(__FILE__, '/views/templates/admin/orders_history.tpl');
    }

    /**
     * Prepares the page to send a EnvoiMoinsCher shipment command.
     * @access public
     * @return Smarty Displays Smarty template.
     */
    public function send()
    {
        $smarty = $this->getContext()->smarty;
        $cookie = $this->getContext()->cookie;
        $html = '';

        $order_id = (int)Tools::getValue('id_order');
        $post_data = $this->model->getPostData($order_id);
        $emc_order = new EnvoimoinscherOrder($this->model);
        $order_stats = $emc_order->getStats();
        $helper = new EnvoimoinscherHelper(EnvoimoinscherModel::getConfig('EMC_TYPE'));
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $data = $this->model->prepareOrderInfo($order_id, $config, true, false);

        if ($data['is_dp'] == 1) {
            $url = Envoimoinscher::getMapByOpe(
                $data['code_eo'],
                Tools::substr($data['order'][0]['offerCode'], 5),
                $config['EMC_CITY'],
                $config['EMC_POSTALCODE'],
                $config['EMC_ADDRESS'],
                'FR'
            );
            $helper->setFields(
                'depot.pointrelais',
                array('helper' => '<p class="note"><a data-fancybox-type="iframe" target="_blank" href="' . $url .
                    '" class="getParcelPoint action_module fancybox">' . $this->l('Get parcel point') . '</a><br/>' .
                    $this->l('If the popup do not show up : ') . '<a target="_blank" href="' . $url . '">' .
                      $this->l('clic here') . '</a></p>')
            );
        } elseif ($data['is_dp'] == 2) {
            $helper->setFields(
                'depot.pointrelais',
                array(
                    'type' => 'input',
                    'helper' => '',
                    'hidden' => true
                )
            );
        }
        $url = Envoimoinscher::getMapByOpe(
            $data['code_eo'],
            Tools::substr($data['order'][0]['offerCode'], 5),
            urlencode($data['delivery']['ville']),
            $data['delivery']['code_postal'],
            urlencode($data['delivery']['adresse']),
            $data['order'][0]['iso_code']
        );
        $helper->setFields(
            'retrait.pointrelais',
            array('helper' => '<p class="note"><a data-fancybox-type="iframe" target="_blank" href="' . $url .
                '" class="getParcelPoint fancybox action_module">' . $this->l('Get parcel point') . '</a><br/>' .
                $this->l('If the popup do not show up : ') . '<a target="_blank" href="' . $url . '">' .
                  $this->l('clic here') . '</a></p>')
        );

        // Check if we have data from previous sending try
        //$show_dst_block = false;
        $delivery_info = $post_data['delivery'];
        if (count($delivery_info) > 1) {
            //$show_dst_block = true;
            $data['delivery'] = $delivery_info;
        }
        $emc_carrier =
          isset($data['order'][0]['external_module_name']) && $data['order'][0]['external_module_name'] == $this->name;
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
        if (!$emc_carrier) {
            $data['order'][0]['label_es'] = $data['order'][0]['name'];
            $data['order'][0]['name_eo'] = $data['order'][0]['name'];
        }
        $smarty->assign('orderInfo', $data['order'][0]);
        $smarty->assign('deliveryInfo', $data['delivery']);
        $smarty->assign('shipperInfo', array(
            'country' => 'FR',
            'postalcode' => $config['EMC_POSTALCODE'],
            'city' => $config['EMC_CITY']
        ));
        $smarty->assign('orderId', $order_id);
        $smarty->assign('envUrl', $this->environments[$config['EMC_ENV']]['link']);
        $smarty->assign('multiParcel', $config['EMC_MULTIPARCEL'] == 'on');
        $smarty->assign('token', Tools::getValue('token'));
        $smarty->assign('alreadyPassed', $this->isPassed($order_id));
        $smarty->assign('isSendLocked', $this->isSendLocked($order_id));
        $smarty->assign('emcBaseDir', _MODULE_DIR_ . '/envoimoinscher/');
        $weight = $data['productWeight'];
        $parcels = $post_data['parcels'];
        $parcels_length = count($parcels);
        if ($parcels_length < 2) {
            $parcels_length = '';
            $parcels = array();
        } else {
            $weight = 0;
            foreach ($parcels as $parcel) {
                $weight += (float)$parcel;
            }
        }
        $smarty->assign('parcels', $parcels);
        $smarty->assign('baseJs', _PS_JS_DIR_);
        $smarty->assign('dimensions', $data['dimensions']);
        $smarty->assign('parcelsLength', $parcels_length);
        unset($cookie->emc_order_parcels);
        $smarty->assign('adminImg', _PS_ADMIN_IMG_);
        $smarty->assign('baseDirCss', __PS_BASE_URI__);
        $smarty->assign('moduleBaseDir', _PS_MODULE_DIR_ . 'envoimoinscher/');
        $smarty->assign('showDstBlock', count($delivery_info) > 1 || (!$emc_carrier && !$offer_data['isFound']));
        $smarty->assign('weight', $weight);
        $smarty->assign('tableTemplate', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/offersTable.tpl');
        $smarty->assign(
            'notFoundTemplate',
            _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/offersNotFound.tpl'
        );
        if (!empty($order_stats)) {
            $smarty->assign('ordersAll', $order_stats['total']);
            $smarty->assign('ordersDone', $order_stats['skipped'] + $order_stats['ok'] + $order_stats['errors']);
            $smarty->assign(
                'orderTodo',
                $order_stats['total'] - ($order_stats['skipped'] + $order_stats['ok'] + $order_stats['errors'])
            );
        } else {
            $smarty->assign('orderTodo', 0);
            $order_stats['total'] = 0;
        }
        $smarty->assign('nextOrderId', $emc_order->getNextOrderId());
        $smarty->assign('massTemplate', _PS_MODULE_DIR_ . 'envoimoinscher/views/templates/admin/massOrders.tpl');
        $smarty->assign('checkAssu', ((int)EnvoimoinscherModel::getConfig('EMC_ASSU') == 1));
        if ($post_data['emcErrorSend'] == 1 && ($order_stats['total'] == 0 || $emc_order->isErrorType())) {
            $smarty->assign('errorMessage', $post_data['emcErrorTxt']);
            $smarty->assign('showErrorMessage', 1);
            $smarty->assign('errorType', 'order');
        } elseif (count($offer_data['errors']) > 0) {
            $smarty->assign('errorMessage', implode('<br />', $offer_data['errors']));
            $smarty->assign('showErrorMessage', 1);
            $smarty->assign('errorType', 'quote');
        }
        $this->model->removeTemporaryPost($order_id); // Delete post values
        $cookie->normal_order_passed = -1; // show nothing on table page
        if ((float)$weight == 0) {
            $html .= parent::adminDisplayWarning(
                'Your order weight are empty, please check products or enable min weight in the module settings.'
            );
        }

        return $html . $this->display(__FILE__, '/views/templates/admin/send.tpl');
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
        $helper = new EnvoimoinscherHelper(EnvoimoinscherModel::getConfig('EMC_TYPE'));
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $data = $this->model->prepareOrderInfo($order_id, $config);
        $data['productWeight'] = (float)str_replace(',', '.', Tools::getValue('weight'));
        // If option 'use content as parcel description' is checked
        if ((int)$config['EMC_CONTENT_AS_DESC'] == 1) {
            $category_row = EnvoimoinscherModel::getNameCategory($config['EMC_NATURE']);
            $data['default']['colis.description'] = $category_row;
        }
        $offer_data = $this->getOfferToSendPage($data, $helper, false);
        $smarty->assign('offer', $offer_data['offer']);
        $smarty->assign('isFound', $offer_data['isFound']);
        $smarty->assign('installedServices', $offer_data['installedSrv']);
        $smarty->assign('offers', $offer_data['allOffers']);
        $smarty->assign('offersNb', count($offer_data['allOffers']));
        $smarty->assign('isajax', 1);
        $smarty->assign('modifPrice', 1); // template was reloaded after weight or multi parcel change
        $smarty->assign('adminImg', _PS_ADMIN_IMG_);
        $smarty->assign('orderid', $order_id);
        $smarty->assign('token', Tools::getValue('token'));
        $smarty->assign('isEMCCarrier', $data['order'][0]['external_module_name'] == $this->name);
        if ($offer_data['isFound']) {
            return $this->display(__FILE__, '/views/templates/admin/offersTable.tpl');
        } else {
            return $this->display(__FILE__, '/views/templates/admin/offersNotFound.tpl');
        }
    }

    /**
     * Commands the shipping offer.
     * @access public
     * @return void
     */
    public function command()
    {
        $cookie = $this->getContext()->cookie;

        $helper = new EnvoimoinscherHelper(EnvoimoinscherModel::getConfig('EMC_TYPE'));
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $order_id = (int)Tools::getValue('id_order');
        $emc_order = new EnvoimoinscherOrder($this->model);
        $stats = $emc_order->getStats();
        $is_mass_order = (isset($stats['total']) && $stats['total'] > 0);
        $emc_order->setOrderId($order_id);
        $data = $this->model->prepareOrderInfo($emc_order->getOrderId(), $config);
        $emc_order->setPrestashopConfig($this->getModuleConfig());
        $emc_order->setOrderData($data);
        $emc_order->setOfferData($this->getOfferToSendPage($data, $helper));
        $result = $emc_order->doOrder(false);
        if ($is_mass_order && ($result || (!$result && !$emc_order->isErrorType()))) {
            $emc_order->skipOrder($order_id);
            $emc_order->updateOrdersList();
        }
        $admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');

        if ($is_mass_order && $emc_order->doOtherOrders()) {
            if (!$result && $emc_order->isErrorType()) {
                Tools::redirectAdmin($admin_link_base . '&id_order=' . $order_id . '&option=send');
                die();
            }
            // make next order
            $this->model->removeTemporaryPost($order_id);
            $emc_order->setOrderId(0);
            Tools::redirectAdmin($admin_link_base . '&id_order=' . $emc_order->getOrderId() . '&option=send');
            die();
        } elseif ($is_mass_order && !$emc_order->doOtherOrders()) {
            $this->model->removeTemporaryPost($order_id);
            $cookie->mass_order_passed = 1;
            Tools::redirectAdmin($admin_link_base);
            die();
        } elseif (!$is_mass_order) {
            $cookie->normal_order_passed = (int)$result;
            if ($result) {
                Tools::redirectAdmin($admin_link_base);
                die();
            }
        }
        Tools::redirectAdmin($admin_link_base . '&id_order=' . $order_id . '&option=send');
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
        if (ctype_alnum($code[0]) && ctype_alnum($code[1])) {
            $rows = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'emc_services es
         JOIN ' . _DB_PREFIX_ . 'emc_operators eo ON eo.code_eo = es.emc_operators_code_eo
         LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON c.id_carrier = es.id_carrier
         WHERE es.code_es = "' . $code[1] . '" AND es.emc_operators_code_eo = "' . $code[0] . '"');
            if (count($rows) == 0 || (int)$rows[0]['id_carrier'] == 0) {
                // carrier was not found, insert a new carrier (which is deleted)
                $data = array(
                    'name' => pSQL($rows[0]['label_es'] . ' (' . $rows[0]['name_eo'] . ')'),
                    'active' => 0,
                    'is_module' => 1,
                    'need_range' => 1,
                    'deleted' => 1,
                    'range_behavior' => 1,
                    'shipping_external' => 1,
                    'external_module_name' => pSQL($this->name)
                );
                $lang_data = array(
                    'id_lang' => (int)$cookie->id_lang,
                    'delay' => pSQL($rows[0]['desc_store_es'])
                );
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier', $data, 'INSERT');
                $lang_data['id_carrier'] = (int)Db::getInstance()->Insert_ID();
                DB::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'carrier SET
                id_reference = '. $lang_data['id_carrier'] .' WHERE id_carrier = '.$lang_data['id_carrier']);
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_lang', $lang_data, 'INSERT');
                // prestashop standard ...
                $carrier = array('id_carrier' => (int)$lang_data['id_carrier'], 'id_group' => 0);
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_group', $carrier, 'INSERT');
                $rows[0]['id_carrier'] = $lang_data['id_carrier'];

                DB::getInstance()->Execute(
                    'UPDATE ' . _DB_PREFIX_ . 'emc_services
                    SET id_carrier = ' . (int)$lang_data['id_carrier'] . ',
                    ref_carrier = ' . (int)$lang_data['id_carrier'] . '
                    WHERE id_es = ' . (int)$rows[0]['id_es'] . ''
                );

            }
            // update carrier for this order
            Db::getInstance()->autoExecute(
                _DB_PREFIX_ . 'orders',
                array('id_carrier' => (int)$rows[0]['id_carrier']),
                'UPDATE',
                'id_order = ' . (int)$order_id
            );
        }
        $admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
        Tools::redirectAdmin($admin_link_base . '&option=send&id_order=' . $order_id);
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
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());

        if (isset($session_data['quote']) && count($session_data['quote']) > 0) {
            $quote_data = $session_data['quote'];
        }
        if (isset($session_data['parcels']) && count($session_data['parcels']) > 0) {
            $data['parcels'] = $session_data['parcels'];
        }
        require_once('Env/WebService.php');
        require_once('Env/Quotation.php');
        // EnvoiMoinsCher library
        $cot_cl = new EnvQuotation(
            array(
                'user' => $config['EMC_LOGIN'],
                'pass' => $config['EMC_PASS'],
                'key' => $config['EMC_KEY_' . $config['EMC_ENV']]
            )
        );
        $cot_cl->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
        $quot_info = array(
            'collecte' => $this->setCollectDate(
                array(
                    array(
                        'j' => $config['EMC_PICKUP_J1'],
                        'from' => $config['EMC_PICKUP_F1'],
                        'to' => $config['EMC_PICKUP_T1']
                    ),
                    array(
                        'j' => $config['EMC_PICKUP_J2'],
                        'from' => $config['EMC_PICKUP_F2'],
                        'to' => $config['EMC_PICKUP_T2']
                    )
                )
            ),
            'type_emballage.emballage' => EnvoimoinscherModel::getConfig('EMC_WRAPPING'),
            'delai' => 'aucun',
            'code_contenu' => $config['EMC_NATURE'],
            'valeur' => (float)$data['order'][0]['total_products'],
            'module' => $this->ws_name,
            'version' => $this->local_version
        );

        $cot_cl->setEnv(Tools::strtolower($config['EMC_ENV']));
        $cot_cl->setPerson(
            'expediteur',
            array(
                'pays' => 'FR',
                'code_postal' => $config['EMC_POSTALCODE'],
                'ville' => $config['EMC_CITY'],
                'type' => 'entreprise',
                'adresse' => $config['EMC_ADDRESS']
            )
        );
        $cot_cl->setPerson(
            'destinataire',
            array(
                'pays' => $data['delivery']['pays'],
                'code_postal' => $data['delivery']['code_postal'],
                'ville' => $data['delivery']['ville'],
                'type' => $data['delivery']['type'],
                'adresse' => $data['delivery']['adresse']
            )
        );
        $cot_cl->setType($config['EMC_TYPE'], $data['parcels']);
        @$cot_cl->getQuotation($quot_info); // Init params for Quotation
        $cot_cl->getOffers(false); // Get Offers
        $is_found = false;
        $final_offer = $proforma_data = array();
        $is_proforma = false;
        // $helper = new EnvoimoinscherHelper;
        foreach ($cot_cl->offers as $o => $offer) {
            if ($offer['operator']['code'] == $data['order'][0]['emc_operators_code_eo'] &&
                $offer['service']['code'] == $data['order'][0]['code_es']) {
                // handle session data
                $offer['priceInsHT'] = (float)isset($offer['insurance']) ? $offer['insurance']['tax-exclusive'] : 0;
                $offer['priceInsTTC'] = (float)isset($offer['insurance']) ? $offer['insurance']['tax-inclusive'] : 0;
                $offer['priceHTNoIns'] = $offer['price']['tax-exclusive'];
                $offer['priceTTCNoIns'] = $offer['price']['tax-inclusive'];
                $offer['priceHT'] = $offer['price']['tax-exclusive'] + $offer['priceInsHT'];
                $offer['priceTTC'] = $offer['price']['tax-inclusive'] + $offer['priceInsTTC'];
                $offer['insurance'] = $data['default']['assurance.selection'];

                $offer['collect'] = date('d-m-Y', strtotime($offer['collection']['date']));
                $offer['delivery'] = date('d-m-Y', strtotime($offer['delivery']['date']));
                foreach ($offer['mandatory'] as $mandatory) {
                    // special case : Chronopost (we have to pass an parameter for parcel point)
                    $default_info = '';
                    if (isset($quote_data[$mandatory['code']])) {
                        $default_info = $quote_data[$mandatory['code']];
                    } elseif (isset($data['default'][$mandatory['code']])) {
                        $default_info = $data['default'][$mandatory['code']];
                    }
                    $field_type = 'text';
                    if ($mandatory['code'] == 'depot.pointrelais' || $mandatory['code'] == 'retrait.pointrelais') {
                        if (strpos($default_info, '-') !== false) {
                            $data_def = explode('-', $default_info);
                            if (count($data_def) > 1) {
                                $info = $data_def[count($data_def) - 1];

                            } else {
                                $info = $data_def[1];
                            }
                            $default_info = $info;
                        }
                        if (preg_match('/POST/i', $mandatory['array'][0])) {
                            $field_type = 'hidden';
                        }
                    }

                    $offer['output'][] = $helper->prepareMandatory($mandatory, $default_info, $field_type, true);
                }
                if (isset($offer['mandatory']['proforma.description_en'])
                  && count($offer['mandatory']['proforma.description_en']) > 0) {
                    $is_proforma = true;
                    $proforma_data = $data['proforma'];
                    $session_proforma = Tools::jsonDecode($cookie->emc_order_proforma, true);
                    if (isset($session_proforma[1]) && count($session_proforma[1]) > 1) {
                        $proforma_data = $session_proforma;
                    }
                }
                $assurance_html = array();
                if (isset($offer['options']['assurance']['parameters'])) {
                    foreach ($offer['options']['assurance']['parameters'] as $a => $assurance) {
                        $default = '';
                        if (isset($session_data['quote'][$a])) {
                            $default = $session_data['quote'][$a];
                        }
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
            $cot_cl->offers[$o]['code'] = $offer['operator']['code'] . '_' . $offer['service']['code'];
        }
        $all_offers = array();
        $installed_srv = array();
        if (!$is_found) {
            $all_offers = $cot_cl->offers;
            $services = Db::getInstance()->ExecuteS(
                'SELECT CONCAT_WS("_", emc_operators_code_eo, code_es) AS offerCode FROM ' . _DB_PREFIX_ .
                'emc_services'
            );
            foreach ($services as $service) {
                $installed_srv[] = $service['offerCode'];
            }
        }
        $errors = array();
        if ($cot_cl->curl_error || $cot_cl->resp_error) {
            if ($cot_cl->curl_error_text != '') {
                $errors[] = $cot_cl->curl_error_text;
            }
            foreach ($cot_cl->resp_errors_list as $error) {
                $errors[] = $error['message'];
            }
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

        foreach ($delays as $delay) {
            if ((int)$delay['to'] != 24) {
                $time_to = strtotime(date('Y-m-d', $today) . ' ' . (int)$delay['to'] . ':00');
            } else {
                $time_to = strtotime('Tomorrow');
            }

            if ($time >= strtotime(date('Y-m-d', $today) . ' ' . (int)$delay['from'] . ':00') && $time < $time_to) {
                $days_delay = $delay['j'];
                break;
            }
        }

        if (!isset($days_delay)) {
            $days_delay = (int)$delays[1] + 1;
        }

        $result = strtotime('+' . $days_delay . 'days', $time);

        if (date('N', $result) == 7) {
            $result = strtotime('+1 day', $result);
        }

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
            'EMC_KEY_TEST' => $this->l('the test API key'),
            'EMC_KEY_PROD' => $this->l('the production API key'),
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
            'EMC_PICKUP_J1' => $this->l('the pickup day'));
        foreach ($values as $k => $value) {
            if (in_array($k, $obligatory) && trim($value) == '') {
                $missed[] = $dictionnary[$k];
            }
        }
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

        return $this->getOrderShippingCostSubFunction($ref, $this->id_carrier);

    }

    /**
     * Sub Function to getOrderShippingCost to be used from hookDisplayBeforeCarrier as well.
     * Gets results from cache or api call and stores results in cache
     * @param array $cart List with cart data.
     * @param int $carrierId The carrier id.
     * @access public
     * @return rate or false if carrier shouldn't be displayed
     */
    public function getOrderShippingCostSubFunction($cart, $carrierId)
    {

        // exit if module is offline or multishipping is enabled
        if (EnvoimoinscherModel::getConfig('EMC_SRV_MODE') == EnvoimoinscherModel::MODE_CONFIG ||
            (int) EnvoimoinscherModel::getConfig('PS_ALLOW_MULTISHIPPING') == 1) {
            return false;
        }

        // if option disable cart is enabled and address is not set, return default rate
        if ($cart->id_address_delivery == 0 && (int)EnvoimoinscherModel::getConfig('EMC_DISABLE_CART') == 1) {
            return $this->getPsRate($cart->id, $carrierId);
        }

        // if option disable cart is enabled and address is not set, return default rate
        $controller = $this->getContext()->controller;

        if ((int)EnvoimoinscherModel::getConfig('EMC_DISABLE_CART') == 2 &&
          ((property_exists($controller, 'php_self') && $controller->php_self == "index") ||
          (property_exists($controller, 'php_self') && $controller->php_self == "cart") ||
          (property_exists($controller, 'php_self') && $controller->php_self == "authentication") ||
          (property_exists($controller, 'php_self') && $controller->php_self == "order"
            && $cart->id_address_delivery == 0) ||
          (property_exists($controller, 'php_self') && $controller->php_self == "order-opc"
            && $cart->id_address_delivery == 0))) {
            return $this->getPsRate($cart->id, $carrierId);
        }

        // Get sender
        $from = $this->model->getSender();

        // Get recipient
        $to = $this->model->getRecipient($cart->id, $cart->id_address_delivery);
        /* if address is not set and EMC_DISABLE_CART is set to 0, the default address is used */
        if (empty($to) && (int)EnvoimoinscherModel::getConfig('EMC_DISABLE_CART') == 0) {
            $to = $this->model->getDefaultAddress();
        }

        // Get cart dimensions
        $weight = $this->model->getCartWeight($cart->id);
        $dimensions = $this->model->getDimensionsByWeight($weight);

        $parcels = array(
            1 => array(
                'poids' => $weight,
                'longueur' => isset($dimensions[0]['length_ed']) ? $dimensions[0]['length_ed'] : 0,
                'largeur' => isset($dimensions[0]['width_ed']) ? $dimensions[0]['width_ed'] : 0,
                'hauteur' => isset($dimensions[0]['height_ed']) ? $dimensions[0]['height_ed'] : 0
            )
        );

        $params = array(
          'collecte' => $this->setCollectDate(
              array(
                  array(
                      'j' => EnvoimoinscherModel::getConfig('EMC_PICKUP_J1'),
                      'from' => EnvoimoinscherModel::getConfig('EMC_PICKUP_F1'),
                      'to' => EnvoimoinscherModel::getConfig('EMC_PICKUP_T1')
                  ),
                  array(
                      'j' => EnvoimoinscherModel::getConfig('EMC_PICKUP_J2'),
                      'from' => EnvoimoinscherModel::getConfig('EMC_PICKUP_F2'),
                      'to' => EnvoimoinscherModel::getConfig('EMC_PICKUP_T2')
                  )
              )
          ),
          'delai' => 'aucun',
          'code_contenu' => EnvoimoinscherModel::getConfig('EMC_NATURE'),
          'valeur' => $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
          'module' => $this->ws_name,
          'version' => $this->local_version,
          'emc_type' => EnvoimoinscherModel::getConfig('EMC_TYPE')
        );


        $offers = $this->getQuote($from, $to, $parcels, $params, false, true);


        // Store relay points and delivery date for display
        $points = array();
        $delivery_dates = array();
        $helper = new EnvoimoinscherHelper;

        if (count($offers) != 0) {
            foreach ($offers as $offer) {
                $tmpCarrierId = $this->model->getCarrierIdByCode($offer['service']['code'], $offer['operator']['code']);

                if ($tmpCarrierId != 0) {
                    // Store relay points
                    if (isset($offer['mandatory']['retrait.pointrelais'])) {
                        $points[$tmpCarrierId] = implode(',', $offer['mandatory']['retrait.pointrelais']['array']);
                    }

                    // Store delivery date
                    if (isset($offer['delivery']['date'])) {
                        $delivery_dates[$tmpCarrierId] = $offer['delivery']['date'];
                    }
                }
            }
        }

        // put parcel points in cache
        $pointCode = $helper->getPointCode($cart->id);
        $this->model->setCache($pointCode, $points);
        // put delivery dates in cache
        $deliveryCode = $helper->getDeliveryDateCode($cart->id);
        $this->model->setCache($deliveryCode, $delivery_dates);

        // get cart rules (which use a code) if there is any
        $cart_rules_in_cart = $helper->getCartRules($cart->id);

        // cache offers override for a few seconds because PS calls this function for each carrier separately
        $offerProcessedCode = $helper->getOfferProcessedCode($offers, $cart->id, $cart_rules_in_cart);
        if (!$this->model->getCache($offerProcessedCode)) {
            if (count($offers) != 0) {
                // uncomment to see in logs why carriers don't show in front
                // (fonction is useless here because PS excludes carriers beforehand)
                // $offers = $this->psCarriersExclude($offers, $cart->id);

                // convert price from euro to default currency if necessary
                $defaultCurrency = new Currency(Currency::getDefaultCurrency()->id);
                if ($defaultCurrency->iso_code != 'EUR') {
                    $euro = $this->model->getEuro();
                    foreach ($offers as $key => $offer) {
                        $convertedPrice = Tools::convertPrice($offer['price']['tax-exclusive'], $euro, false);
                        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                            $message = sprintf(
                                $this->l('Quotation - converting price for carrier %1$s to %2$s: %3$s%2$s'),
                                $offer['operator']['code'] . '_' .$offer['service']['code'],
                                $defaultCurrency->sign,
                                $convertedPrice
                            );
                            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                        }
                        $offers[$key]['price']['tax-exclusive'] = $convertedPrice;
                    }
                }

                // apply rate price if needed
                $offers = $this->applyRatePrice($offers, $cart->id);

                // apply Prestashop price configuration
                $offers = $this->psPriceOverride($offers, $cart->id);

                // convert price from default currency to cart currency if necessary
                $cartCurrency = new Currency($cart->id_currency);
                if ($cartCurrency->iso_code != $defaultCurrency->iso_code) {
                    foreach ($offers as $key => $offer) {
                        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                            $message = sprintf(
                                $this->l('Quotation - converting price for carrier %1$s to %2$s: %3$s%2$s'),
                                $offer['operator']['code'] . '_' .$offer['service']['code'],
                                $cartCurrency->sign,
                                Tools::convertPrice($offer['price']['tax-exclusive'], $cartCurrency, true)
                            );
                            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                        }
                        $offers[$key]['price']['tax-exclusive'] = Tools::convertPrice(
                            $offer['price']['tax-exclusive'],
                            $cartCurrency,
                            true
                        );
                    }
                }

                $offers = $this->model->makeCarrierIdKeys($offers);
            }

            $this->model->setCache($offerProcessedCode, $offers, 30);
        } else {
            $offers = $this->model->getCache($offerProcessedCode);
        }

        if (isset($offers[$carrierId])) {
            $return = $offers[$carrierId]['price']['tax-exclusive'];
        } else {
            $return = false;
        }

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
     * Get rates as defined by carrier ranges for quotation bypass if EMC_DISABLE_CART is on.
     * @param int $cartId the cart id.
     * @param int $id_carrier The carrier id.
     * @access public
     * @return false or float
     */
    public function getPsRate($cartId, $id_carrier)
    {
        $cart = new Cart($cartId);

        // check carrier is active for France
        $zoneId = Db::getInstance()->ExecuteS('SELECT id_zone FROM ' . _DB_PREFIX_ . 'country WHERE iso_code = "FR"');
        $zoneId = $zoneId[0]['id_zone'];
        $carriers_zone = $this->model->getEmcCarriersByZone($zoneId, $cart->id_lang);
        $is_available = false;
        foreach ($carriers_zone as $carrier) {
            if ($carrier['id_carrier'] = $id_carrier) {
                $is_available = true;
            }
        }
        if (!$is_available) {
            return false;
        }

        $weight = $this->model->getCartWeightRaw($cartId);

        $rate = 0;
        $carrier = new Carrier($id_carrier);

        $is_weight_method =
          $carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT ? true : false;

        if ($is_weight_method) {
            if (Carrier::checkDeliveryPriceByWeight(
                $id_carrier,
                $weight,
                $zoneId
            )) {
                $rate = $carrier->getDeliveryPriceByWeight(
                    $weight,
                    $zoneId
                );
            }
        } else {
            if (Carrier::checkDeliveryPriceByPrice(
                $id_carrier,
                $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                $zoneId,
                $cart->id_currency
            )
            ) {
                $rate = $carrier->getDeliveryPriceByPrice(
                    $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                    $zoneId
                );
            }
        }

        // check if there are additional costs (configured on products, calculated in cart)
        $additionalCost = 0;
        foreach ($cart->getProducts() as $product) {
            $additionalCost += $product['additional_shipping_cost'];
        }
        $rate += $additionalCost;

        // Handling cost
        if ($carrier->shipping_handling) {
            $shipping_handling = (float)EnvoimoinscherModel::getConfig('PS_SHIPPING_HANDLING');
            $rate += $shipping_handling;
        }

        return $rate;
    }

    /**
     * Adds PS configuration additional costs to offers.
     * @param array $offers offers returned by API.
     * @returns array $offers with prices override.
     */
    private function psPriceOverride($offers, $cartId)
    {
        $cart = new Cart($cartId);
        $defaultCurrency = new Currency(Currency::getDefaultCurrency()->id);
        $configuration = EnvoimoinscherModel::getConfigMultiple(array(
          'PS_SHIPPING_HANDLING',
          'EMC_ENABLED_LOGS'
        ));

        // check if there are additional costs (configured on products, calculated in cart)
        $additionalCost = 0;
        foreach ($cart->getProducts() as $product) {
            $additionalCost += $product['additional_shipping_cost'] * $product['cart_quantity'];
        }

        foreach ($offers as $key => $offer) {
            $carrierId = $this->model->getCarrierIdByCode($offer['service']['code'], $offer['operator']['code']);

            // add additional costs
            if ($additionalCost != 0) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = sprintf(
                        $this->l('Quotation - adding to carrier %1$s additional shipping fees %2$s%3$s'),
                        $offer['operator']['code'] . '_' .$offer['service']['code'],
                        $additionalCost,
                        $defaultCurrency->sign
                    );
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                }

                $offers[$key]['price']['tax-exclusive'] += $additionalCost;
            }

            // add shipping handling costs
            $carrier = new Carrier($carrierId);
            if (isset($configuration['PS_SHIPPING_HANDLING']) && $carrier->shipping_handling
              && $configuration['PS_SHIPPING_HANDLING'] > 0) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = sprintf(
                        $this->l('Quotation - adding to carrier %1$s handling charges %2$s%3$s'),
                        $offer['operator']['code'] . '_' .$offer['service']['code'],
                        (float)$configuration['PS_SHIPPING_HANDLING'],
                        $defaultCurrency->sign
                    );
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                }
                $offers[$key]['price']['tax-exclusive'] += (float)$configuration['PS_SHIPPING_HANDLING'];
            }
        }

        return $offers;
    }

    /**
     * Set carriers free according to Prestashop configuration.
     * @param array $offers offers returned by API.
     * @returns array $offers with prices override.
     */
    private function applyFree($offers, $cartId)
    {
        $cart = new Cart($cartId);
        $configuration = EnvoimoinscherModel::getConfigMultiple(array(
          'PS_SHIPPING_FREE_PRICE',
          'PS_SHIPPING_FREE_WEIGHT',
          'EMC_ENABLED_LOGS'
        ));
        $helper = new EnvoimoinscherHelper;

        // get carriers set free because of cart rules
        // code taken from Prestashop Cart.php class. May differ depending on PS version...
        $cart_rules = CartRule::getCustomerCartRules($cart->id_lang, $cart->id_customer, true, true, false, $cart);
        $cart_rules_in_cart = $helper->getCartRules($cart->id);

        $total_products_wt = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $free_carriers_rules = array();
        foreach ($cart_rules as $cart_rule) {
            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;

            // if cart rule is "Shipping included", exit function
            if ($cart_rule['minimum_amount_shipping']) {
                // if cart rule involves making shipping free, send warning in logs
                if ($cart_rule['free_shipping']) {
                    $message = sprintf(
                        $this->l(
                            'Cart rules must be set to Shipping excluded in order to work properly. '.
                            'Check cart rule %1$s'
                        ),
                        $cart_rule['id_cart_rule']
                    );
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 4);
                }
                continue;
            }

            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction']
              && $cart_rule['minimum_amount'] <= $total_price &&
              (in_array((int)$cart_rule['id_cart_rule'], $cart_rules_in_cart) || $cart_rule['code'] == "")) {
                $cr = new CartRule((int)$cart_rule['id_cart_rule']);
                $context = Context::getContext();
                if (!$context->cart) {
                    $context = $context->cloneContext();
                    $context->cart = $cart;
                }
                // for the simulator we need to add a default carrier in cart
                if (!$context->cart->id_carrier) {
                    $firstCarrier = current($offers);
                    $context->cart->id_carrier = $this->model->getCarrierIdByCode(
                        $firstCarrier['service']['code'],
                        $firstCarrier['operator']['code']
                    );
                }
                if (Validate::isLoadedObject($cr) && $cr->checkValidity(
                    $context,
                    in_array((int)$cart_rule['id_cart_rule'], $cart_rules_in_cart),
                    false,
                    false
                )) {
                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
                        foreach ($carriers['selected'] as $carrier) {
                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
                                $free_carriers_rules[] = array('id_carrier' => (int)$carrier['id_carrier'],
                                                                'id_cart_rule' => $cart_rule['id_cart_rule']
                                                              );
                            }
                        }
                    }
                }
            } elseif ($cart_rule['free_shipping'] && $cart_rule['minimum_amount'] <= $total_price
              && (in_array((int)$cart_rule['id_cart_rule'], $cart_rules_in_cart) || $cart_rule['code'] == "")) {
                // in case no carrier has been selected, all must be set free
                foreach ($offers as $key => $offer) {
                    $carrierId = $this->model->getCarrierIdByCode(
                        $offer['service']['code'],
                        $offer['operator']['code']
                    );
                    $free_carriers_rules[] = array(
                                                    'id_carrier'   => (int)$carrierId,
                                                    'id_cart_rule' => $cart_rule['id_cart_rule']
                                                  );
                }
            }
        }

        foreach ($offers as $key => $offer) {
            $carrierId = $this->model->getCarrierIdByCode($offer['service']['code'], $offer['operator']['code']);
            $carrier = new Carrier($carrierId);

            // check if carrier is set free
            if (isset($carrier->is_free) && $carrier->is_free) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = sprintf(
                        $this->l('Quotation - carrier %1$s is configured as a free carrier'),
                        $offer['operator']['code'] . '_' .$offer['service']['code']
                    );
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                }
                $offers[$key]['price']['tax-exclusive'] = 0;
            }
            // check if carrier is free because of cart rules
            foreach ($free_carriers_rules as $free_carriers_rule) {
                if (array_search($carrierId, $free_carriers_rule) !== false) {
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l('Quotation - carrier %1$s is set free by cart rule %2$s'),
                            $offer['operator']['code'] . '_' .$offer['service']['code'],
                            $free_carriers_rule['id_cart_rule']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    $offers[$key]['price']['tax-exclusive'] = 0;
                }
            }
        }

        // check if free because of cart price
        $free_fees_price = 0;
        if (isset($configuration['PS_SHIPPING_FREE_PRICE'])) {
            $free_fees_price = (float)$configuration['PS_SHIPPING_FREE_PRICE'];
        }

        if ($cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING) >= (float)$free_fees_price
          && (float)$free_fees_price > 0) {
            if ((int)$configuration['EMC_ENABLED_LOGS'] == 1) {
                $message = $this->l('Quotation - all carriers free because of cart price');
                Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
            }
            foreach ($offers as $key => $offer) {
                $offers[$key]['price']['tax-exclusive'] = 0;
            }
            return $offers;
        }

        // check if free because of cart weight
        if (isset($configuration['PS_SHIPPING_FREE_WEIGHT'])) {
            $weight = $this->model->getCartWeightRaw($cartId);

            if ($weight >= (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] &&
              (float)$configuration['PS_SHIPPING_FREE_WEIGHT'] > 0) {
                if ((int)$configuration['EMC_ENABLED_LOGS'] == 1) {
                    $message = $this->l('Quotation - all carriers free because of cart weight');
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                }
                foreach ($offers as $key => $offer) {
                    $offers[$key]['price']['tax-exclusive'] = 0;
                }
                return $offers;
            }
        }

        return $offers;
    }

    /**
     * Apply rate price if needed.
     * @param array $offers offers returned by API.
     * @returns array $offers with prices override.
     */
    private function applyRatePrice($offers, $cartId)
    {

        $cart = new Cart($cartId);
        $recipient = $this->model->getRecipient($cartId, $cart->id_address_delivery);
        if (empty($recipient)) {
            $recipient = $this->model->getDefaultAddress();
        }
        // get zone id for country
        $zoneId = Db::getInstance()->ExecuteS(
            'SELECT id_zone FROM ' . _DB_PREFIX_ . 'country WHERE iso_code = "'.pSQL($recipient['pays']).'"'
        );
        if (isset($zoneId[0]['id_zone'])) {
            $zoneId = $zoneId[0]['id_zone'];
        } else {
            $zoneId = 0;
        }
        $weight = $this->model->getCartWeightRaw($cartId);

        foreach ($offers as $key => $offer) {
            $carrierId = $this->model->getCarrierIdByCode($offer['service']['code'], $offer['operator']['code']);

            switch ($this->model->isRatePrice($carrierId)) {
                case 0:
                    break;

                case 1:
                    $carrier = new Carrier($carrierId);
                    if (Carrier::checkDeliveryPriceByWeight(
                        $carrierId,
                        $weight,
                        $zoneId
                    )
                    ) {
                        $offers[$key]['price']['tax-exclusive'] = (float)$carrier->getDeliveryPriceByWeight(
                            $weight,
                            $zoneId
                        );
                        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                            $message = sprintf(
                                $this->l('Quotation - price range applied to %1$s'),
                                $offer['operator']['code'] . '_' .$offer['service']['code']
                            );
                            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                        }
                    }
                    break;

                case 2:
                    $carrier = new Carrier($carrierId);
                    if (Carrier::checkDeliveryPriceByPrice(
                        $carrierId,
                        $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                        $zoneId,
                        $cart->id_currency
                    )) {
                        $offers[$key]['price']['tax-exclusive'] = (float)$carrier->getDeliveryPriceByPrice(
                            $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                            $zoneId
                        );
                        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                            $message = sprintf(
                                $this->l('Quotation - price range applied to %1$s'),
                                $offer['operator']['code'] . '_' .$offer['service']['code']
                            );
                            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                        }
                    }
                    break;
            }
        }

        return $offers;
    }
    /**
     * Excludes carriers if they aren't in PS configured ranges/zones. Used in tests().
     * @param array $offers offers array.
     * @param object $cartId cart id.
     * @returns boolean. Returns true if the carrier needs to be excluded, false if not.
     */
    private function psCarriersExclude($offers, $cartId)
    {
        $cart = new Cart($cartId);
        $recipient = $this->model->getRecipient($cartId, $cart->id_address_delivery);
        $helper = new EnvoimoinscherHelper;

        // get zone id for country
        $zoneId = Db::getInstance()->ExecuteS(
            'SELECT id_zone FROM ' . _DB_PREFIX_ . 'country WHERE iso_code = "'.pSQL($recipient['pays']).'"'
        );
        if (isset($zoneId[0]['id_zone'])) {
            $zoneId = $zoneId[0]['id_zone'];
        } else {
            $zoneId = 0;
        }

        // available carriers for this zone
        $carriersZone = $helper->makeCodeKeys($this->model->getEmcCarriersByZone($zoneId, $cart->id_lang));

        // exclude carriers from offers if they are not active for this zone
        $weight = $this->model->getCartWeightRaw($cartId);
        foreach ($offers as $key => $offer) {
            // because is not active in this zone
            if (!isset($carriersZone[$offer['operator']['code'] . '_' . $offer['service']['code']])) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = sprintf(
                        $this->l('Quotation - carrier %1$s not active for this zone'),
                        $offer['operator']['code'] . '_' .$offer['service']['code']
                    );
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                }
                unset($offers[$key]);
            }

            $carrierId = $this->model->getCarrierIdByCode($offer['service']['code'], $offer['operator']['code']);
            $carrier = new Carrier($carrierId);
            if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) {
                // because weight is out of range
                if (!Carrier::checkDeliveryPriceByWeight(
                    $carrierId,
                    $weight,
                    $zoneId
                )) {
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l('Quotation - %1$s is removed because of price range configuration'),
                            $offer['operator']['code'] . '_' .$offer['service']['code']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    unset($offers[$key]);
                }
            } else {
                // because price is out of range
                if (!Carrier::checkDeliveryPriceByPrice(
                    $carrierId,
                    $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                    $zoneId,
                    $cart->id_currency
                )) {
                    $offers[$key]['price']['tax-exclusive'] = $carrier->getDeliveryPriceByPrice(
                        $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
                        $zoneId
                    );
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l('Quotation - %1$s is removed because of price range configuration'),
                            $offer['operator']['code'] . '_' .$offer['service']['code']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    unset($offers[$key]);
                }
            }
        }

        // Get cart dimensions
        $shop = Context::getContext()->shop;

        foreach ($offers as $key => $offer) {
            $carrierId = $this->model->getCarrierIdByCode($offer['service']['code'], $offer['operator']['code']);
            $carrier = new Carrier($carrierId);

            // Product attributes at this time (PS 1.6.0.9) are not taken into account by PS
            foreach ($cart->getProducts() as $product) {
                // check weight
                if ((float)$carrier->max_weight > 0 && (float)$carrier->max_weight < (float)$product['weight']) {
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l(
                                'Quotation - carrier %1$s is removed because product %2$s '
                                .'is over its configured max weight'
                            ),
                            $offer['operator']['code'] . '_' .$offer['service']['code'],
                            $product['id_product']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    unset($offers[$key]);
                }

                // check width
                if ((float)$carrier->max_width > 0 && (float)$carrier->max_width < (float)$product['width']) {
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l(
                                'Quotation - carrier %1$s is removed because product %2$s '
                                .'is over its configured max width'
                            ),
                            $offer['operator']['code'] . '_' .$offer['service']['code'],
                            $product['id_product']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    unset($offers[$key]);
                }

                // check length
                if ((float)$carrier->max_depth > 0 && (float)$carrier->max_depth < (float)$product['depth']) {
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l(
                                'Quotation - carrier %1$s is removed because product %2$s '
                                .'is over its configured max depth'
                            ),
                            $offer['operator']['code'] . '_' .$offer['service']['code'],
                            $product['id_product']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    unset($offers[$key]);
                }

                // check height
                if ((float)$carrier->max_height > 0 && (float)$carrier->max_height < (float)$product['height']) {
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        $message = sprintf(
                            $this->l(
                                'Quotation - carrier %1$s is removed because product %2$s '
                                .'is over its configured max height'
                            ),
                            $offer['operator']['code'] . '_' .$offer['service']['code'],
                            $product['id_product']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                    unset($offers[$key]);
                }

                // exclude because of product configuration
                $cacheKey = 'carrier_list_by_product'.(int)$product['id_product'].'-'.(int)$shop->id;
                if (!$this->model->getCache($cacheKey)) {
                    $productCarriers = Db::getInstance()->executeS(
                        'SELECT c.id_carrier FROM ' . _DB_PREFIX_ . 'product_carrier pc
                        JOIN ' . _DB_PREFIX_ . 'carrier c
                        ON c.id_reference = pc.id_carrier_reference AND c.deleted = 0
                        WHERE pc.id_product = '.(int)$product['id_product'].'
                        AND pc.id_shop = '.(int)$shop->id
                    );
                    $this->model->setCache($cacheKey, $productCarriers, 30);
                }
                $productCarriers = $this->model->getCache($cacheKey);
                $availableCarriers = $helper->getProductCarrierIds($productCarriers);
                if ($availableCarriers) {
                    $carrierId = $this->model->getCarrierIdByCode(
                        $offer['service']['code'],
                        $offer['operator']['code']
                    );
                    if (array_search($carrierId, $availableCarriers) === false) {
                        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                            $message = sprintf(
                                $this->l(
                                    'Quotation - carrier %1$s is removed because at least one product '
                                    .'has it deactivated'
                                ),
                                $offer['operator']['code'] . '_' .$offer['service']['code']
                            );
                            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                        }
                        unset($offers[$key]);
                    }
                }
            }
        }

        // TO DO exclude because of stock management

        return $offers;
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
            CURLOPT_URL => $this->environments[$data['apiEnv']]['link'] . '/verifier_utilisateur.html',
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
        if (trim($result) != 1) {
            $return = 0;
        }
        return $return;
    }

    /**
     * Load all available carriers
     */
    public function loadAllCarriers($ajax = true)
    {
        $result = array();
        // on verifie qu'on est bien en mode de configuration
        if (EnvoimoinscherModel::getConfig('EMC_SRV_MODE') != EnvoimoinscherModel::MODE_CONFIG) {
            if ($ajax) {
                echo $this->l('Your module must be in offline mode.');
                die();
            } else {
                return false;
            }
        }

        // on recupere les services present
        $bd = Db::getInstance();
        $sql_srv = 'SELECT * FROM ' . _DB_PREFIX_ . 'emc_services';
        $sql_ope = 'SELECT * FROM ' . _DB_PREFIX_ . 'emc_operators';
        $services = $bd->ExecuteS($sql_srv);
        $operators = $bd->ExecuteS($sql_ope);
        //$srv_save = $services;
        //$ope_save = $operators;

        // on recupere les services depuis le serveur envoimoinscher
        require_once('Env/WebService.php');
        require_once('Env/CarriersList.php');
        $login = EnvoimoinscherModel::getConfig('EMC_LOGIN');
        $pass = EnvoimoinscherModel::getConfig('EMC_PASS');
        $env = EnvoimoinscherModel::getConfig('EMC_ENV');
        $key = EnvoimoinscherModel::getConfig('EMC_KEY_' . $env);
        $lib = new EnvCarriersList(array('user' => $login, 'pass' => $pass, 'key' => $key));
        $lib->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
        $lib->setEnv(Tools::strtolower($env));
        $lib->getCarriersList($this->ws_name, $this->version);

        if ($lib->curl_error) {
            if ($ajax) {
                echo $this->l('Error while updating your offers : ');
                foreach ($lib->resp_errors_list as $message) {
                    echo '<br />' . $message['message'];
                }
                die();
            } else {
                return false;
            }
        } elseif ($lib->resp_error) {
            if ($ajax) {
                echo $this->l('Error while updating your offers : ');
                foreach ($lib->resp_errors_list as $message) {
                    echo '<br />' . $message['message'];
                }
                die();
            } else {
                return false;
            }
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
        foreach ($lib->carriers as $carrier) {
            // operateur trouve, on regarde s'il est different
            if ($last_ope_seen != $carrier['ope_code']) {
                $last_ope_seen = $carrier['ope_code'];
                // on compare l'operateur avec celui de la liste
                $op_found = -1;
                foreach ($operators as $id => $operator) {
                    if ($operator['code_eo'] == $carrier['ope_code']) {
                        $op_found = $id;
                        if ($operator['name_eo'] != $carrier['ope_name']) {
                            $ope_to_update[count($ope_to_update)] = $carrier;
                        } else {
                            $ope_no_change[count($ope_no_change)] = $carrier;
                        }
                        break;
                    }
                }
                if ($op_found == -1) {
                    $ope_to_insert[count($ope_to_insert)] = $carrier;
                } else {
                    unset($operators[$op_found]);
                }
            }

            // on compare le service avec celui de la liste
            $srv_found = -1;
            foreach ($services as $id => $service) {
                if ($service['emc_operators_code_eo'] == $carrier['ope_code']
                  && $service['code_es'] == $carrier['srv_code']) {
                    $srv_found = $id;
                    // service trouve, on regarde s'il est different
                    if ($service['label_es'] != $carrier['srv_name'] ||
                        $service['desc_es'] != $carrier['label_store'] ||
                        $service['desc_store_es'] != $carrier['description'] ||
                        $service['label_store_es'] != $carrier['description_store'] ||
                        $service['is_parcel_pickup_point_es'] != $carrier['parcel_pickup_point'] ||
                        $service['is_parcel_dropoff_point_es'] != $carrier['parcel_dropoff_point'] ||
                        $service['family_es'] != $carrier['family'] ||
                        $service['type_es'] != $carrier['zone']
                    ) {
                        $srv_to_update[] = $carrier;
                    } else {
                        $srv_no_change[] = $carrier;
                    }
                    break;
                }
            }
            if ($srv_found == -1) {
                $srv_to_insert[] = $carrier;
            } else {
                unset($services[$srv_found]);
            }
        }

        $srv_to_delete = $services;
        $ope_to_delete = $operators;

        // On met à jour la base
        // Requête insert services
        $query = array();
        $sql = '';
        $first_line = true;
        if (count($srv_to_insert) > 0) {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'emc_services VALUES';
            foreach ($srv_to_insert as $service) {
                if (!$first_line) {
                    $sql .= ',';
                }
                $first_line = false;
                $sql .= '(null,0,0,"' . pSQL($service['srv_code']) .
                    '","' . pSQL($service['ope_code']) .
                    '","' . pSQL($service['srv_name']) .
                    '","' . pSQL($service['label_store']) .
                    '","' . pSQL($service['description']) .
                    '","' . pSQL($service['description_store']) .
                    '",0,' . (int)$service['parcel_pickup_point'] .
                    ',' . (int)$service['parcel_dropoff_point'] .
                    ',' . (int)$service['family'] .
                    ',' . (int)$service['zone'] .
                    ',1)';
            }
            $sql .= ';';
            $query[] = $sql;
        }

        // Requête insert opeateurs
        if (count($ope_to_insert) > 0) {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'emc_operators VALUES';
            $first_line = true;
            foreach ($ope_to_insert as $operator) {
                if (!$first_line) {
                    $sql .= ',';
                }
                $first_line = false;
                $sql .= '(null,"' . pSQL($operator['ope_name']) . '","' . pSQL($operator['ope_code']) . '")';
            }
            $sql .= ';';
            $query[] = $sql;
        }

        // Requête update services
        foreach ($srv_to_update as $service) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'emc_services SET
                     label_es = "' . pSQL($service['srv_name']) . '"
                     ,desc_es = "' . pSQL($service['label_store']) . '"
                     ,desc_store_es = "' . pSQL($service['description']) . '"
                     ,label_store_es = "' . pSQL($service['description_store']) . '"
                     ,price_type_es = 0
                     ,is_parcel_pickup_point_es = ' . (int)$service['parcel_pickup_point'] . '
                     ,is_parcel_dropoff_point_es = ' . (int)$service['parcel_dropoff_point'] . '
                     ,family_es = ' . (int)$service['family'] . '
                     ,type_es = ' . (int)$service['zone'] . '
                     WHERE code_es = "' . pSQL($service['srv_code']) . '"
                     AND emc_operators_code_eo = "' . pSQL($service['ope_code']) . '";';
            $query[] = $sql;
        }
        // Requête update operateurs
        foreach ($ope_to_update as $operator) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'emc_operators SET
         name_eo = "' . pSQL($operator['ope_name']) . '" WHERE code_eo = "' . pSQL($operator['ope_code']) . '";';
            $query[] = $sql;
        }

        // Requête delete services
        if (count($srv_to_delete) > 0) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'carrier SET deleted = 1 WHERE ';
            $first_line = true;
            foreach ($srv_to_delete as $service) {
                if (!$first_line) {
                    $sql .= ' OR ';
                }
                $first_line = false;
                $sql .= 'id_carrier = ' . (int)$service['id_carrier'];
            }
            $sql .= ';';
            $query[] = $sql;
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'emc_services WHERE ';
            $first_line = true;
            foreach ($srv_to_delete as $service) {
                if (!$first_line) {
                    $sql .= ' OR ';
                }
                $first_line = false;
                $sql .= 'id_es = ' . (int)$service['id_es'];
            }
            $sql .= ';';
            $query[] = $sql;
        }
        // Requête delete operateurs
        $first_line = true;
        if (count($ope_to_delete) > 0) {
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'emc_operators WHERE ';
            foreach ($ope_to_delete as $operator) {
                if (!$first_line) {
                    $sql .= ' OR ';
                }
                $first_line = false;
                $sql .= 'id_eo = ' . (int)$operator['id_eo'];
            }
            $sql .= ';';
            $query[] = $sql;
        }

        Db::getInstance()->execute('START TRANSACTION;');
        foreach ($query as $q) {
            if ($q != '' && Db::getInstance()->execute($q) === false) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    Logger::addLog(
                        '[ENVOIMOINSCHER][' . time() . '] ' .
                        $this->l('Update : Error while updating your offers : ') . $q
                    );
                }

                if ($ajax) {
                    Db::getInstance()->execute('ROLLBACK;');
                    echo $this->l('Error while updating your offers : ') . $q;
                    die();
                } else {
                    return false;
                }
            }
        }
        Db::getInstance()->execute('COMMIT;');

        $result = array();
        $result['offers_added'] = array();
        $result['offers_updated'] = array();
        $result['offers_deleted'] = array();
        foreach ($srv_to_insert as $service) {
            $result['offers_added'][count($result['offers_added'])] = $service['srv_name'];
        }
        foreach ($srv_to_update as $service) {
            $result['offers_updated'][count($result['offers_updated'])] = $service['srv_name'];
        }
        foreach ($srv_to_delete as $service) {
            $result['offers_deleted'][count($result['offers_deleted'])] = $service['label_es'];
        }

        $date = new DateTime();
        EnvoimoinscherModel::updateConfig('EMC_LAST_CARRIER_UPDATE', $date->format('Y-m-d'));

        if ($ajax) {
            echo Tools::jsonEncode($result);
            die();
        } else {
            return true;
        }
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

        return $this->l('day' . $date_data[0]) . ' ' . $date_data[1] . ' ' .
          $this->l('month' . $date_data[2]) . ' ' . $date_data[3];
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
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/includes/EnvoimoinscherHelper.php');
        $helper = new EnvoimoinscherHelper;
        // check if order belongs to module and if the carrier handles parcel points
        $point = $this->model->getPointInfos($params['id_order']);
        if (isset($point['code'])) {
            $smarty->assign('point', $point);
            $smarty->assign('schedule', $helper->setSchedule($point['schedule']));
        }
        $smarty->assign('multiParcels', ($parcels = $this->model->getParcelsInfos($params['id_order'])));
        $smarty->assign('multiSize', count($parcels));
        return $this->display(__FILE__, '/views/templates/hook/hookAdminOrder.tpl');
    }

    /**
     * Handle carrier choice. If the carrier must have a parcel point, put the point in the session.
     * If the point wasn't specified, returns a error. It's called only for standard order mode.
     * @access public
     * @param array $params List of order params.
     * @param bool $redirect Redirect boolean.
     * @return void
     */
    public function hookProcessCarrier($params, $redirect = true)
    {
        $cookie = $this->getContext()->cookie;

        /*$price = 0.0;

        if ($redirect || !$redirect) {
            $price = 0.0;
        }*/
        $cartId = (int)$params['cart']->id;
        $error_occured = false;
        $correct_code = true;
        $options = unserialize($params['cart']->delivery_option);

        foreach ($options as $o => $option) {
            $ope = $this->model->getCartCarrier($cartId, (int)str_replace(',', '', $option));
            if (isset($ope) && count($ope) > 0) {
                if ($ope[0]['selected_point']) {
                    $codes = explode('-', $ope[0]['selected_point']);
                    if ((!isset($codes[0]) || !isset($codes[1])) ||
                        (isset($codes[0]) && isset($codes[1]) && (trim($codes[0]) != $ope[0]['emc_operators_code_eo']
                          || !ctype_alnum(trim($codes[1]))))
                    ) {
                        $correct_code = false;
                    }
                }
                if (isset($ope[0]) && $ope[0]['is_parcel_pickup_point_es'] == 1 &&
                    !$correct_code && Tools::getValue('ajax') != 'true' &&
                    Tools::getValue('ajax') != 'true'
                ) {
                    $error_occured = true;
                    $error_message =
                      sprintf($this->l('The mandatory parcel point has not been chosen for the cart %s'), $cartId);
                    if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $error_message, 4);
                    }
                    $variable = 'choosePoint' . $ope[0]['emc_operators_code_eo'] . $o;
                    $cookie->$variable = 1;
                }
                /*$prices = Tools::jsonDecode($ope[0]['prices_eap'], true);
                if (isset($prices[$o]) && isset($prices[$o][$ope[0]['id_carrier']])) {
                    $price = $price + $prices[$o][$ope[0]['id_carrier']];
                }*/
            }
        }

        if ($error_occured) {
            Tools::redirect('order.php?step=2');
            return false;
        }
        /*Db::getInstance()->autoExecute(
            _DB_PREFIX_ . 'emc_api_pricing',
            array('price_eap' => (float)$price),
            'UPDATE',
            _DB_PREFIX_ . 'cart_id_cart = ' . (int)$cartId . ' '
        );*/
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
        $helper = new EnvoimoinscherHelper;

        $cookie->emc_carrier = '';
        // Get cart carrier (if EnvoiMoinsCher, make some additional operations)
        $row = $this->model->getCartCarrierByCart($params['cart']->id);

        // if it's not our carrier, nothing to do here
        if ($row[0]['external_module_name'] !== $this->name) {
            return;
        }

        $cookie = $this->getContext()->cookie;
        $cookie->emc_carrier = '';

        if ($row[0]['selected_point']) {
            // Insert parcel point informations
            $point = explode('-', $row[0]['selected_point']);
            if (ctype_alnum(trim($point[0])) && ctype_alnum(trim($point[1]))
                && strpos(trim($point[0]), $row[0]['emc_operators_code_eo']) !== false) {
                $data = array(
                    _DB_PREFIX_ . 'orders_id_order' => (int)$params['order']->id,
                    'point_ep' => pSQL(trim($point[1])),
                    'emc_operators_code_eo' => pSQL(trim($point[0]))
                );
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'emc_points', $data, 'INSERT');
            }
        }

        // deleted tmp data from cache
        $pointCode = $helper->getPointCode($params['cart']->id);
        $this->model->deleteFromCache($pointCode);
        $deliveryCode = $helper->getDeliveryDateCode($params['cart']->id);
        $this->model->deleteFromCache($deliveryCode);

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

        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/includes/EnvoimoinscherHelper.php');
        $helper = new EnvoimoinscherHelper;
        // get tracking informations
        $rows = $this->model->getTrackingByOrderAndCustomer(Tools::getValue('id_order'), $cookie->id_customer);
        $smarty->assign('rows', $rows);
        $smarty->assign('isAdmin', false);
        $point = $this->model->getPointInfos(Tools::getValue('id_order'));
        $is_point = 0;

        if (is_array($params)) {
            $is_point = 0;
        }
        if (isset($point['code'])) {
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
        if ($params['carrier']->external_module_name !== $this->name) {
            return;
        }

        $data = array('id_carrier' => (int)$params['carrier']->id);
        Db::getInstance()->autoExecute(
            _DB_PREFIX_ . 'emc_services',
            $data,
            'UPDATE',
            'ref_carrier = ' . (int)$params['carrier']->id_reference
        );
    }

    /**
     * Header's hook. It displays included JavaScript for GoogleMaps API.
     * @access public
     * @return Displayed Smarty template.
     */
    public function hookHeader()
    {
        $smarty = $this->getContext()->smarty;
        $smarty->assign('emcBaseDir', _MODULE_DIR_ . '/envoimoinscher/');
        $controller = $this->getContext()->controller;
        if (property_exists($controller, 'php_self') &&
        ($controller->php_self == "order-opc" || ($controller->php_self == "order" && $controller->step == 2) )) {
            $this->getContext()->controller->addJs('https://maps.google.com/maps/api/js');
        }
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
        // exit if module is offline or multishipping is enabled
        if (EnvoimoinscherModel::getConfig('EMC_SRV_MODE') == EnvoimoinscherModel::MODE_CONFIG ||
            (int)EnvoimoinscherModel::getConfig('PS_ALLOW_MULTISHIPPING') == 1) {
            return;
        }

        $smarty = $this->getContext()->smarty;
        $helper = new EnvoimoinscherHelper;
        $cart = $params['cart'];

        $pointCode = $helper->getPointCode($cart->id);
        $points = $this->model->getCache($pointCode);
        if ($points) {
            $smarty->assign('points', $points);
        } else {
            $smarty->assign('points', array());
        }

        $point = $this->model->getSelectedPoint($cart->id);

        $deliveryCode = $helper->getDeliveryDateCode($cart->id);
        $delivery = $this->model->getCache($deliveryCode);
        if ($delivery) {
            foreach ($delivery as $key => $value) {
                // we need to update carrier id in case there has been a change in the carriers
                $carrierId = $this->model->getActiveCarrierId($key);
                if ($carrierId) {
                    unset($delivery[$key]);
                    $delivery[$carrierId] = $this->dateToString($value);
                }
            }
            $smarty->assign('delivery', $delivery);
        }
        $smarty->assign('point', $point);
        $smarty->assign('deliveryLabel', EnvoimoinscherModel::getConfig('EMC_LABEL_DELIVERY_DATE'));
        $smarty->assign('id_address_params', $params['address']->id);
        $smarty->assign('id_address', $this->model->id_address);
        $smarty->assign('destCountry', $this->model->address['pays']);
        $smarty->assign('loaderSrc', __PS_BASE_URI__ . 'img/loader.gif');
        $smarty->assign('baseDir', __PS_BASE_URI__);
        return $this->display(__FILE__, '/views/templates/hook/envoimoinscher_carrier.tpl');
    }

    /**
     * Hook to remove free emc carriers when they are not suitable for the order
     * @access public
     * @param array $params Parameters array (cart object, carriers, ...)
     * @return $params.
     */
    public function hookDisplayBeforeCarrier(&$params)
    {
        $removeCarriers = array();
        if (isset($params['delivery_option_list'])) {
            foreach ($params['delivery_option_list'] as $carrier_list_raw) {
                foreach ($carrier_list_raw as $key => $carrier_list) {
                    // for some reason, $key has a comma at the end of it
                    $carrier_id = str_replace(',', '', $key);

                    // this test is used for Prestashop 1.5
                    $price_without_tax = '';
                    if (isset($carrier_list['carrier_list'])) {
                        foreach ($carrier_list['carrier_list'] as $v) {
                            $price_without_tax = $v['price_without_tax'];
                        }
                    }

                    // check if carrier is set to free
                    if ((isset($carrier_list['is_free']) && $carrier_list['is_free']) || $price_without_tax == 0) {
                        // check if carrier is an emc carrier
                        $emc_carrier_test = Db::getInstance()->ExecuteS(
                            'SELECT * FROM ' . _DB_PREFIX_ . 'carrier
                            WHERE id_carrier = ' . (int)$carrier_id . ' AND external_module_name = "envoimoinscher"'
                        );

                        if (!empty($emc_carrier_test)) {
                            $rate = $this->getOrderShippingCostSubFunction($params['cart'], $carrier_id);

                            // if $rate is false, add carrier to carriers to be removed
                            if ($rate === false) {
                                $removeCarriers[] = $key;
                            }
                        }
                    }
                }
            }
        }

        // if $removeCarriers is not empty, remove unwanted carrier
        if (!empty($removeCarriers)) {
            $smarty = $this->getContext()->smarty;
            $smarty->assign('removeCarriers', $removeCarriers);
            return $this->display(__FILE__, '/views/templates/hook/hookDisplayBeforeCarrier.tpl');
        }
    }

    /**
     * Checks if EnvoiMoinsCher order was already passed for this prestashop command.
     * @access private
     * @param int $order_id Id of order.
     * @return boolean True if passed, false if not.
     */
    private function isPassed($order_id)
    {
        $row = Db::getInstance()->ExecuteS(
            'SELECT ' . _DB_PREFIX_ . 'orders_id_order FROM ' .
            _DB_PREFIX_ . 'emc_orders
            WHERE ' . _DB_PREFIX_ . 'orders_id_order = ' . (int)$order_id . ''
        );
        $result = false;
        if (isset($row[0]) && $row[0][_DB_PREFIX_ . 'orders_id_order'] != '') {
            $result = true;
        }
        return $result;
    }

     /**
     * Checks if the order is timed out.
     * @access private
     * @param int $order_id Id of order.
     * @return boolean True if passed, false if not.
     */
    private function isSendLocked($order_id)
    {
        $row = Db::getInstance()->ExecuteS(
            'SELECT eopt.' . _DB_PREFIX_ . 'orders_id_order FROM '._DB_PREFIX_ . 'emc_orders_post eopt
            WHERE eopt.' . _DB_PREFIX_ . 'orders_id_order =  ' . (int)$order_id . ' AND eopt.type = "timeout"
            AND DATE_ADD(eopt.date_eopo, INTERVAL 5 MINUTE) > NOW()'
        );
        $result = false;
        if (isset($row[0]) && $row[0][_DB_PREFIX_ . 'orders_id_order'] != '') {
            $result = true;
        }
        return $result;
    }

    /**
     * Get parcel points from a carrier and address (passed in get values)
     * @access public
     * @return void
     */
    public function getPoints()
    {
        require_once(_PS_MODULE_DIR_ . $this->name . '/Env/WebService.php');
        require_once(_PS_MODULE_DIR_ . $this->name . '/Env/ParcelPoint.php');
        $smarty = $this->getContext()->smarty;

        // Load the parcel points for the chosen carrier and address
        $helper = new EnvoimoinscherHelper;
        $carrier = (int)Tools::getValue('carrier');
        $address_id = (int)Tools::getValue('addressId');
        $env_cl = new Envoimoinscher;
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $poi_cl = new EnvParcelPoint(array(
            'user' => $config['EMC_LOGIN'],
            'pass' => $config['EMC_PASS'],
            'key' => $config['EMC_KEY_' . $config['EMC_ENV']]));
        $poi_cl->setPlatformParams($env_cl->ws_name, _PS_VERSION_, $env_cl->version);
        $poi_cl->setEnv(Tools::strtolower($config['EMC_ENV']));
        $poi_cl->construct_list = true;
        foreach (explode(',', Tools::getValue('points')) as $point) {
            if (Tools::getValue('country') == '' || ctype_alnum(Tools::getValue('country'))) {
                $poi_cl->getParcelPoint('dropoff_point', $point, Tools::getValue('country'));
            }
        }

        // add the inputs
        $inputs = array();
        $inputs['address'] = array();
        $inputs['address']['name'] = 'parcelPoints' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['address']['id'] = 'parcelPoints' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['address']['value'] = array();
        $inputs['info'] = array();
        $inputs['info']['name'] = 'parcelInfos' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['info']['id'] = 'parcelInfos' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['info']['value'] = array();
        $inputs['name'] = array();
        $inputs['name']['name'] = 'parcelNames' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['name']['id'] = 'parcelNames' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['name']['value'] = array();
        $inputs['id'] = array();
        $inputs['id']['name'] = 'parcelIds' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['id']['id'] = 'parcelIds' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['id']['value'] = array();
        $inputs['count'] = array();
        $inputs['count']['name'] = 'counter' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['count']['id'] = 'counter' . $carrier . Tools::getValue('ope') . $address_id;
        $inputs['count']['value'] = 0;

        // add parcel points
        $points = array();
        $i = 0;
        foreach ($poi_cl->points['dropoff_point'] as $point) {
            if ($point['name'] != '') {
                $point['checked'] = $point['code'] == Tools::getValue('pointValue');
                $point['js'] = 'selectPr(\'' . $point['code'] . '\', \'' . (int)Tools::getValue('carrier') . '\', \'' .
                  $address_id . '\');';
                $point['class'] = 'point' . $carrier . $address_id;
                $point['input_name'] = 'point' . $carrier . Tools::getValue('ope') . $address_id;
                $point['id'] = 'point' . $carrier . $point['code'] . $address_id;

                $inputs['address']['value'][] = $point['address'] . ', ' . $point['zipcode'] . ' ' . $point['city'];
                $inputs['info']['value'][] = implode('<br />', $helper->setSchedule($point['schedule']));
                $inputs['name']['value'][] = $point['name'];
                $inputs['id']['value'][] = $point['code'];
                $points[] = $point;
            }
            $i++;
        }
        if ($i == 0) {
            return 'noPoint';
        }

        $inputs['address']['value'] = implode('|', $inputs['address']['value']);
        $inputs['info']['value'] = implode('|', $inputs['info']['value']);
        $inputs['name']['value'] = implode('|', $inputs['name']['value']);
        $inputs['id']['value'] = implode('|', $inputs['id']['value']);

        $smarty->assign('points', $points);
        $smarty->assign('inputs', $inputs);

        return $this->display(__FILE__, '/views/templates/front/get_points.tpl');
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
        if (ctype_alnum(trim($point[0])) && ctype_alnum(trim($point[1]))) {
            $data = array(
                'id_cart' => (int)$cookie->id_cart,
                'selected_point' => pSQL(trim($post_point))
            );
            return Db::getInstance()->autoExecute(
                _DB_PREFIX_ . 'emc_cart_tmp',
                $data,
                'REPLACE'
            );
        }
        return 0;
    }
    /**
     * Return the quotation for the informations given.
     * This function SHOULD NOT include any PS configuration modifications on shipping, as this is what we store
     * in cache and we shouldn't have to empty the offers cache for any PS configuration modification
     * @access public
     * @param $from    : departure address
     * @param $to      : delivery address
     * @param $parcels : parcel configuration
     * @param $params  : additional parameters
     * @param $curlMulti : true : the function will do a multirequest on all activated services,
     * else it will do a simple request
     * @param $cache : true : if cache should be used
     * @return array List with offers.
     */
    public function getQuote($from, $to, $parcels, $params, $curlMulti = true, $cache = true)
    {
        if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
            $message = sprintf(
                $this->l('Quotation - from %1$s %2$s to %3$s %4$s'),
                $from['code_postal'],
                $from['ville'],
                $to['code_postal'],
                $to['ville']
            );
            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
        }

        // Get cache
        $pricingCode = EnvoimoinscherHelper::getPricingCode($from, $to, $parcels, $params, $curlMulti);

        if ($cache) {
            $offers = $this->model->getCache($pricingCode);
            if ($offers !== false) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = $this->l('Quotation - Cache used');
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                }
                return $offers;
            }
        }
        $helper = new EnvoimoinscherHelper;
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());

        // check if call comes from tests()
        if (!isset($params['testPage'])) {
            $params['testPage'] = false;
        }

        // Create quotation object
        require_once('Env/WebService.php');
        require_once('Env/Quotation.php');
        $lib = new EnvQuotation(
            array(
                'user' => $config['EMC_LOGIN'],
                'pass' => $config['EMC_PASS'],
                'key' => $config['EMC_KEY_' . $config['EMC_ENV']]
            )
        );
        $lib->setEnv(Tools::strtolower($config['EMC_ENV']));
        $lib->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);

        // Initialize the quotation
        $lib->setPerson('expediteur', $from);
        $lib->setPerson('destinataire', $to);
        $lib->setType($params['emc_type'], $parcels);

        // Curl multi or single request on all activated carriers
        if (!$curlMulti) {
            // get all activated services
            $services = $helper->makeCodeKeys($this->model->getEmcCarriers());

            if (count($services) == 0) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = $this->l('Quotation - No carrier activated');
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    if ($params['testPage']) {
                        $error_msg = $this->l(
                            'No carrier activated, please activate carriers before making a simulation'
                        );
                        return array('isError' => 1, 'message' => $error_msg);
                    }
                }
                return false;
            }

            $params['offers'] = array();
            foreach ($services as $carrier_code => $carrier) {
                list($operator, $service) = explode('_', $carrier_code);
                array_push($params['offers'], $operator.$service);
            }

            $lib->getQuotation($params);
            $lib->getOffers(false);
        } else {
            // get all activated services
            $services = $helper->makeCodeKeys($this->model->getEmcCarriers());

            if (count($services) == 0) {
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    $message = $this->l('Quotation - No carrier activated');
                    Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    if ($params['testPage']) {
                        $error_msg = $this->l(
                            'No carrier activated, please activate carriers before making a simulation'
                        );
                        return array('isError' => 1, 'message' => $error_msg);
                    }
                }
                return false;
            }

            foreach ($services as $carrier_code => $carrier) {
                list($operator, $service) = explode('_', $carrier_code);
                $params['operator'] = $operator;
                $params['service'] = $service;
                $lib->setParamMulti($params);
            }

            $lib->getQuotationMulti();
            $lib->getOffersMulti();
        }

        $offers = array();
        if ($lib->curl_error) {
            $error_msg = $this->l('The shipment platform is currently unavailable');
            if ($params['testPage']) {
                $offers = array('isError' => 1, 'message' => $error_msg);
            }
            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $error_msg, 3);
        } elseif ($lib->resp_error) {
            if ($params['testPage']) {
                $offers = array('isError' => 1, 'message' => $lib->resp_errors_list[0]['message']);
            }
            $message = sprintf(
                $this->l('Error while recovering offers: %1$s'),
                $lib->resp_errors_list[0]['message']
            );
            Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 4);
        } else {
            $offers = $lib->offers;
            if (count($offers) != 0) {
                // If several offers have the same operator and service (Chrono18...), keep only the cheapest
                $checkOffers = array();
                foreach ($offers as $key => $offer) {
                    if (isset($checkOffers[$offer['operator']['code'].'_'.$offer['service']['code']])) {
                        if ($offer['price']['tax-exclusive'] <
                          $checkOffers[$offer['operator']['code'].'_'.$offer['service']['code']]['price']) {
                            $checkOffers[$offer['operator']['code'].'_'.$offer['service']['code']] = array(
                                'key' => $key,
                                'price' => $offer['price']['tax-exclusive']
                            );
                            unset(
                                $offers[$checkOffers[$offer['operator']['code'].'_'.$offer['service']['code']]['key']]
                            );
                        } else {
                            unset($offers[$key]);
                        }
                    } else {
                        $checkOffers[$offer['operator']['code'].'_'.$offer['service']['code']] = array(
                            'key' => $key,
                            'price' => $offer['price']['tax-exclusive']
                        );
                    }
                }

                // Log API base prices
                if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                    foreach ($offers as $offer) {
                        $message = sprintf(
                            $this->l('Quotation - API tax-exclusive price for %1$s is %2$s€'),
                            $offer['operator']['code']  . '_' . $offer['service']['code'],
                            $offer['price']['tax-exclusive']
                        );
                        Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
                    }
                }
            }
        }

        if (count($offers) == 0) {
            if ((int)EnvoimoinscherModel::getConfig('EMC_ENABLED_LOGS') == 1) {
                $message = sprintf(
                    $this->l(
                        'Quotation - no offer found for shipping from %1$s %2$s to %3$s %4$s %5$s, '
                        .'the cart weighs %6$skg'
                    ),
                    $from['code_postal'],
                    $from['ville'],
                    $to['code_postal'],
                    $to['ville'],
                    $to['pays'],
                    $parcels[1]['poids']
                );
                Logger::addLog('[ENVOIMOINSCHER][' . time() . '] ' . $message, 1);
            }
        }

        // Cache API results
        $this->model->setCache($pricingCode, $offers, 86400);

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
        return $this->parseUpgradeXml(_PS_MODULE_DIR_ . $this->name . '/sql/upgrades/upgrades.xml');
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
        $queries = explode(
            '-- REQUEST --',
            Tools::file_get_contents(_PS_MODULE_DIR_ . $this->name . '/sql/upgrades/' . $list[$id]['file'])
        );
        foreach ($queries as $q => $query) {
            $query = str_replace('{PREFIXE}', _DB_PREFIX_, $query);
            if (trim($query) != '' && !Db::getInstance()->Execute($query)) {
                $error = true;
                break;
            }
            unset($queries[$q]);
        }
        if (count($queries) > 0) {
            file_put_contents(
                _PS_MODULE_DIR_ . $this->name . '/sql/upgrades/' . $list[$id]['file'],
                implode('-- REQUEST --', $queries)
            );
        } else {
            $error = $this->removeUpgradeItem($id, _PS_MODULE_DIR_ . $this->name . '/sql/upgrades/upgrades.xml');
            unlink(_PS_MODULE_DIR_ . $this->name . '/sql/upgrades/' . $list[$id]['file']);
        }
        $smarty->assign('error', $error);
        $smarty->assign('adminImg', _PS_ADMIN_IMG_);
        $smarty->assign(
            'token',
            Tools::getAdminToken(
                'AdminModules' . (int)Tab::getIdFromClassName('AdminModules') . (int)$cookie->id_employee
            )
        );
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
        if (!file_exists($file)) {
            return array();
        }
        $result = array();
        $dom_cl = new DOMDocument();
        $dom_cl->load($file);
        $xpath = new DOMXPath($dom_cl);
        $upgrades = $xpath->evaluate('/upgrades/upgrade');
        foreach ($upgrades as $upgrade) {
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
        foreach ($upgrades as $upgrade) {
            if ($upgrade->getElementsByTagName('id')->item(0)->nodeValue == $id) {
                $main->removeChild($upgrade);
                $error = false;
            }
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
    /*private function prepareQuotationCartData($cart, $address_param = array())
    {
        $rows = $this->model->getCartInformations($cart, $address_param); // Get cart informations
        // sets products weight

        /* We compute the weight and price of the products with their attributes */
        /*$count = count($rows);
        for ($i = 0; $i < $count; $i++) {
            $attributes = $this->model->getProductAttributes($rows[$i]['id_product_attribute']);
            $weigt = isset($attributes[0]) ? $attributes[0]['weight'] : 0;
            $price = isset($attributes[0]) ? $attributes[0]['price'] : 0;
            $rows[$i]['real_weight'] = ((float)$rows[$i]['weight'] + (float)$weigt);
            $rows[$i]['real_price'] = ((float)$rows[$i]['price'] + (float)$price);
        }

        $weight = 0;
        $avg_weight = (float)EnvoimoinscherModel::getConfig('EMC_AVERAGE_WEIGHT');

        $additional_cost = 0;
        if ($rows && count($rows) > 0) {
            $current = current($rows);

            foreach ($rows as $row) {
                $row['real_weight'] = EnvoimoinscherHelper::normalizeToKg(
                  EnvoimoinscherModel::getConfig('PS_WEIGHT_UNIT'),
                  $row['real_weight']
                );
                $weight += $row['productQuantity'] * $row['real_weight'];
                // if we haven't product weight, take average weight option
                if ($row['productQuantity'] * $row['real_weight'] == 0) {
                    $weight += $avg_weight * $row['productQuantity'];
                }
                // if product has some mandatory fees, add it into ship price
                if ($row['additional_shipping_cost'] > 0) {
                    $additional_cost += (float)$row['additional_shipping_cost'];
                }
            }

            if ($current['id_address_delivery'] != '0') {
                // delivery address
                $street = $current['address1'];

                if ($current['address2'] != '') {
                    $street .= $current['address2'];
                }

                $type = 'particulier';

                if ($current['company'] != '') {
                    $type = 'entreprise';
                }
                $address = array(
                    'id_zone' => $current['id_zone'],
                    'type' => $type,
                    'country' => $current['iso_code'],
                    'city' => $current['city'],
                    'postcode' => $current['postcode'],
                    'street' => $street);
                $id_currency = $current['id_currency'];

            } else {
                $address = array();
                $id_currency = false;
            }
        }

        // option < 100g
        if ($weight < 0.1 && $weight >= 0 && (int)EnvoimoinscherModel::getConfig('EMC_WEIGHTMIN') == 1) {
            $weight = 0.1;
        }

        // cart's type (to calculate order total amount)
        $type_cart = Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING;
        return array(
            'weight' => (float)$weight,
            'address' => $address,
            'idCurrency' => (int)$id_currency,
            'typeCart' => $type_cart,
            'additionalCost' => $additional_cost
        );
    }*/

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

        $url = '';
        $base_url = '';
        $type = '';
        $envoi = '';

        if (Tools::isSubmit('orders')) {
            foreach (Tools::getValue('orders') as $ord) {
                $orders_to_get[] = (int)$ord;
            }
        } elseif (Tools::isSubmit('order')) {
            $orders_to_get[] = (int)Tools::getValue('order');
        }

        // We need order's infos for the document's url and references
        if (count($orders_to_get) > 0) {
            $references = $this->model->getReferencesToLabels($orders_to_get);

            foreach ($references as $reference) {
                $refs[] = $reference['ref_emc_eor'];
            }
        }

        // We now query the document
        if (count($refs) > 0) {
            // Get the document's base url
            $base_url = explode('?', $references[0]['link_ed']);
            $base_url = $base_url[0];

            // Set url's params
            if (Tools::getValue('sendValueRemises')) {
                $type = 'remise';
            } else {
                $type = 'bordereau';
            }
            $envoi = implode(',', $refs);

            //  Set url
            $url = $base_url . '?type=' . $type . '&envoi=' . $envoi;

            // Send the pdf request
            $cookie->error_labels = 0;
            $helper = new EnvoimoinscherHelper;
            $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
            $options = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER =>
                  array('Authorization: ' . $helper->encode($config['EMC_LOGIN'] . ':' . $config['EMC_PASS'])),
                CURLOPT_CAINFO => dirname(__FILE__) . '/ca/ca-bundle.crt',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2
            );
            $req = curl_init();
            curl_setopt_array($req, $options);
            $result = curl_exec($req);
            curl_close($req);

            // We now display the pdf
            header('Content-type: application/pdf');
            if ($type == 'remise') {
                header('Content-Disposition: attachment; filename="remises.pdf"');
            } else {
                header('Content-Disposition: attachment; filename="bordereaux.pdf"');
            }
            echo $result;
            die();
        }
        $cookie->error_labels = 1;
        $admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
        Tools::redirectAdmin($admin_link_base . '&option=history');
    }

    public function checkUpdates()
    {
        $helper = new EnvoimoinscherHelper;
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $result = array();
        $filename = $this->getEnvironment($config['EMC_ENV']) . '/api/check_updates.html?module=' . $this->ws_name .
          '&version=' . $this->local_version;
        $updates = (array)Tools::jsonDecode(Tools::file_get_contents($filename));
        foreach ($updates as $u => $update) {
            $info = (array)$update;
            $result[] = array(
                'version' => $u,
                'name' => $info['name'],
                'description' => $info['description'],
                'url' => $this->getEnvironment($config['EMC_ENV']) . $info['url']);
        }
        echo Tools::jsonEncode($result);
        die();
    }

    public function lookForCarrierUpdates()
    {
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/WebService.php');
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/Carrier.php');
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/Service.php');
        $helper = new EnvoimoinscherHelper;
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        $ser_class = new EnvService(array(
            'user' => $config['EMC_LOGIN'],
            'pass' => $config['EMC_PASS'],
            'key' => $config['EMC_KEY_' . $config['EMC_ENV']]));
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
        foreach ($offers as $offer) {
            $installed[$offer['emc_operators_code_eo'] . '_' . $offer['code_es']] = $offer;
            $checksums[$offer['emc_operators_code_eo'] . '_' . $offer['code_es']] = sha1(
                $offer['desc_es'] . $offer['desc_store_es'] . $offer['family_es']
            );
        }
        foreach ($ser_class->carriers as $c => $carrier) {
            foreach ($carrier['services'] as $service) {
                $code = $c . '_' . $service['code'];
                $exists = isset($installed[$code]);
                $service_infos = array('label_backoffice' => '', 'label_store' => '');
                if (isset($service['apiOptions']['prestashop'])) {
                    $service_infos = array(
                        'label_backoffice' =>
                          html_entity_decode($service['apiOptions']['prestashop']['label_backoffice']),
                        'label_store' => html_entity_decode($service['apiOptions']['prestashop']['label_store']),
                        'offer_family' => (int)$service['apiOptions']['prestashop']['offer_family']);
                }
                $srv_checksum = sha1(
                    $service_infos['label_backoffice'] . $service_infos['label_store'] . $service_infos['offer_family']
                );
                if (!$exists && $service['is_pluggable']) {
                    // install new service and remove from $installed array
                    $service['srvInfos'] = $service_infos;
                    if ($this->model->insertService($service, $carrier)) {
                        $carrier_lower = Tools::strtolower($carrier['code']);
                        file_put_contents(
                            _PS_MODULE_DIR_ . '/envoimoinscher/img/detail_' . $carrier_lower . '.jpg',
                            Tools::file_get_contents($carrier['logo_modules'])
                        );

                        $added++;
                        $offers_json['added'][] = $service['label'];
                    }
                } elseif ($exists && !$service['is_pluggable']) {
                    // uninstall carrier
                    if ($this->model->uninstallService($code)) {
                        $deleted++;
                        $offers_json['deleted'][] = $service['label'];
                    }
                } elseif ($exists && $checksums[$code] != $srv_checksum) {
                    // new data available, must update
                    $parts = explode('_', $code);
                    $up_data = array('desc_es' => pSQL($service_infos['label_backoffice']),
                        'desc_store_es' => pSQL($service_infos['label_store']),
                        'family_es' => (int)$service_infos['offer_family']);
                    if ($this->model->updateService($up_data, $parts[0], $parts[1])) {
                        $updated++;
                        $offers_json['updated'][] = $service['label'];
                    }
                    unset($installed[$code]);
                } elseif ($exists && isset($installed[$code])) {
                    unset($installed[$code]);
                }
            }
        }
        // clean up old services in Prestashop database
        foreach ($installed as $code => $offer) {
            if ($this->model->uninstallService($code)) {
                $deleted++;
                $offers_json['deleted'][] = $offer['label_es'];
            }
        }
        echo Tools::jsonEncode(array(
                'added' => $added,
                'updated' => $updated,
                'deleted' => $deleted,
                'addedOffers' => implode(',', $offers_json['added']),
                'updatedOffers' => implode(',', $offers_json['updated']),
                'deletedOffers' => implode(',', $offers_json['deleted'])));
        die();
    }

    public function checkLabelsAvailability()
    {
        $orders_id = explode(';', Tools::getValue('orders'));
        if (count($orders_id) > 0) {
            $documents = Db::getInstance()->ExecuteS(
                'SELECT * FROM ' . _DB_PREFIX_ . 'emc_documents
                WHERE generated_ed = 1 AND ' . _DB_PREFIX_ . 'orders_id_order in (' .
                implode(',', array_map('intval', $orders_id)) . ')'
            );

            // get all documents for each order
            $order_documents = array();
            foreach ($documents as $document) {
                $id = $document['ps_orders_id_order'];
                if (!isset($order_documents[$id])) {
                    $order_documents[$id] = array();
                }
                $order_documents[$id][] = array(
                    'type' => $document['type_ed'],
                    'name' => $this->l('download ' . $document['type_ed']),
                    'url' => $document['link_ed']
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
        if (Tools::getValue('dest_company')) {
            $company = Tools::getValue('dest_company');
        }
        $perso_alias = '_perso_' . $order_id;
        // insert new address row
        $insert_row = array(
            'id_country' => (int)$address[0]['id_country'],
            'id_state' => (int)$address[0]['id_state'],
            'id_customer' => (int)$address[0]['id_customer'],
            'id_manufacturer' => (int)$address[0]['id_manufacturer'],
            'id_supplier' => (int)$address[0]['id_supplier'],
            'alias' => pSQL(str_replace($perso_alias, '', $address[0]['alias']) . $perso_alias),
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
        Tools::redirectAdmin($admin_link_base . '&option=send&id_order=' . $order_id);
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
        if ($this->model->cleanCache()) {
            $result['error'] = 0;
        }
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
        $this->link = 'index.php?controller=' . Tools::getValue('controller') .
            '&token=' . Tools::getValue('token') .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $this->getContext()->smarty->assign('EMC_link', $this->link);

        // Get level configuration
        $emc_user = EnvoimoinscherModel::getConfig('EMC_USER');
        if ((int)$emc_user < 3) {
            // If we need to previous configuration
            if (Tools::isSubmit('previous')) {
                // if we are in the Merchant account tab, redirect to first step
                if ($emc_user == 0) {
                    EnvoimoinscherModel::updateConfig('EMC_USER', '-2');
                } else {
                    EnvoimoinscherModel::updateConfig('EMC_USER', (int)$emc_user - 1);
                }
                Tools::redirectAdmin($this->link);
                return;
            }

            // Introduction
            if (Tools::getValue('btnIntro')) {
                return $this->postProcessIntroduction();
            }
            // EMC configuration
            if (Tools::getValue('btnEmc')) {
                return $this->postProcessEmc();
            }
            // Merchant configuration
            if (Tools::getValue('btnMerchant')) {
                return $this->postProcessMerchant();
            } elseif (Tools::getValue('btnSends')) {
                return $this->postProcessSends(false);
            } elseif (Tools::getValue('btnCarriersSimple')) {
                return $this->postProcessCarriersSimple();
            }
            return;
        }

        // Save status
        if (Tools::getValue('EMC_Status') && Tools::getValue('ajax')) {
            $status = Tools::getValue('EMC_Status') === 'true' ?
              EnvoimoinscherModel::MODE_ONLINE : EnvoimoinscherModel::MODE_CONFIG;
            EnvoimoinscherModel::updateConfig('EMC_SRV_MODE', $status);

            if ($status == EnvoimoinscherModel::MODE_ONLINE) {
                $this->model->passToOnlineMode();
            }

            Tools::jsonEncode(true);
            exit;
        } elseif (Tools::getValue('EMC_Env') && Tools::getValue('ajax')) {
            EnvoimoinscherModel::updateConfig('EMC_ENV', Tools::getValue('EMC_Env'));
            Tools::jsonEncode(true);
            exit;
        } elseif (Tools::getValue('ajax')) {
            $tab = Tools::ucfirst(Tools::strtolower(Tools::getValue('EMC_tab')));
            /** Merchant Account **/
            if ($tab === 'Merchant') {
                echo $this->getContentMerchant();
            } elseif ($tab === 'Sends') {
                echo $this->getContentSends();
            } elseif ($tab === 'Settings') {
                echo $this->getContentSettings();
            } elseif ($tab === 'Simple_carriers') {
                echo $this->getContentCarriers('Simple');
            } elseif ($tab === 'Advanced_carriers') {
                echo $this->getContentCarriers('Advanced');
            } elseif ($tab === 'Help') {
                echo $this->getContentHelp();
            }
            exit;
        } elseif (Tools::getValue('btnMerchant')) {
            return $this->postProcessMerchant();
        } elseif (Tools::getValue('btnCarriersSimple')) {
            return $this->postProcessCarriersSimple();
        } elseif (Tools::getValue('btnCarriersAdvanced')) {
            return $this->postProcessCarriersAdvanced();
        } elseif (Tools::getValue('btnSends')) {
            return $this->postProcessSends();
        } elseif (Tools::getValue('btnSettings')) {
            if (Tools::getValue('EMC_track_mode') &&
                Tools::getValue('EMC_ann') &&
                Tools::getValue('EMC_envo') &&
                Tools::getValue('EMC_cmd') &&
                Tools::getValue('EMC_liv')
            ) {
                // Track mode
                EnvoimoinscherModel::updateConfig('EMC_TRACK_MODE', (int)Tools::getValue('EMC_track_mode'));
                // Status
                EnvoimoinscherModel::updateConfig('EMC_ANN', Tools::getValue('EMC_ann'));
                EnvoimoinscherModel::updateConfig('EMC_ENVO', Tools::getValue('EMC_envo'));
                EnvoimoinscherModel::updateConfig('EMC_CMD', Tools::getValue('EMC_cmd'));
                EnvoimoinscherModel::updateConfig('EMC_LIV', Tools::getValue('EMC_liv'));
                EnvoimoinscherModel::updateConfig('EMC_DISABLE_CART', Tools::getValue('EMC_disable_cart'));
                EnvoimoinscherModel::updateConfig('EMC_ENABLED_LOGS', Tools::getValue('EMC_enabled_logs'));
                EnvoimoinscherModel::updateConfig('EMC_FILTER_TYPE_ORDER', Tools::getValue('EMC_filter_type_order'));
                EnvoimoinscherModel::updateConfig(
                    'EMC_FILTER_STATUS',
                    implode(';', Tools::getValue('EMC_filter_status'))
                );
                EnvoimoinscherModel::updateConfig('EMC_FILTER_CARRIERS', Tools::getValue('EMC_filter_carriers'));
                EnvoimoinscherModel::updateConfig(
                    'EMC_FILTER_START_DATE',
                    Tools::getValue('EMC_filter_start_order_date')
                );

                require_once dirname(__FILE__) . '/Env/WebService.php';
                require_once dirname(__FILE__) . '/Env/User.php';

                $api_login = EnvoimoinscherModel::getConfig('EMC_LOGIN');
                $api_pass = EnvoimoinscherModel::getConfig('EMC_PASS');
                $api_env = EnvoimoinscherModel::getConfig('EMC_ENV');
                $api_key = EnvoimoinscherModel::getConfig('EMC_KEY_' . $api_env);

                // update e-mail configuration
                $user_class = new EnvUser(array('user' => $api_login, 'pass' => $api_pass, 'key' => $api_key));
                $user_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
                $user_class->setEnv(Tools::strtolower($api_env));

                $user_class->postEmailConfiguration(
                    array(
                        'label' => Tools::getValue('EMC_mail_label', ''),
                        'notification' => Tools::getValue('EMC_mail_notif', ''),
                        'bill' => Tools::getValue('EMC_mail_bill', '')
                    )
                );

                Tools::redirectAdmin($this->link . '&EMC_tabs=settings&conf=6');
            } else {
                return $this->displayError($this->l('Please check your form, some fields are requried'));
            }
        }
    }

    private function postProcessIntroduction()
    {
        EnvoimoinscherModel::updateConfig('EMC_USER', -1);
    }

    private function postProcessEmc()
    {
        $errors = array();
        $helper = new EnvoimoinscherHelper();

        // Validation for account creation form
        if (Tools::getValue('choice') == 'create') {
            // validate gender
            if (Tools::getValue('contact_civ')) {
                if (Tools::getValue('contact_civ') == 'M.') {
                    EnvoimoinscherModel::updateConfig('EMC_CIV', 'M');
                } else {
                    EnvoimoinscherModel::updateConfig('EMC_CIV', 'Mme');
                }
            }

            // validate surname
            if (Tools::getValue('contact_nom')) {
                EnvoimoinscherModel::updateConfig('EMC_LNAME', Tools::getValue('contact_nom'));
            } else {
                $errors[] = $this->l('Please specify your surname');
            }

            // validate first name
            if (Tools::getValue('contact_prenom')) {
                EnvoimoinscherModel::updateConfig('EMC_FNAME', Tools::getValue('contact_prenom'));
            } else {
                $errors[] = $this->l('Please specify your first name');
            }

            // validate occupation
            if (!Tools::getValue('profession')) {
                $errors[] = $this->l('Please specify your occupation');
            }

            // validate email
            if (!Tools::getValue('contact_email')) {
                $errors[] = $this->l('Please specify your email address');
            }
            if (!Tools::getValue('contact_email2')) {
                $errors[] = $this->l('Please confirm your email address');
            } elseif (Tools::getValue('contact_email') != Tools::getValue('contact_email2')) {
                $errors[] = $this->l('Please verify your email address and its confirmation');
            } elseif (!$helper->validateEmail(Tools::getValue('contact_email'))) {
                $errors[] = $this->l('Please specify a valid email address');
            } else {
                EnvoimoinscherModel::updateConfig('EMC_MAIL', Tools::getValue('contact_email'));
            }

            // validate login
            if (Tools::getValue('login')) {
                if (!$helper->validateAlpha(Tools::getValue('login'))) {
                    $errors[] = $this->l('Your ID may only contain alphanumerical characters');
                } else {
                    EnvoimoinscherModel::updateConfig('EMC_LOGIN', Tools::getValue('login'));
                }
            } else {
                $errors[] = $this->l('Please specify a login');
            }

            // validate password
            if (!Tools::getValue('password')) {
                $errors[] = $this->l('Please enter your password');
            }
            if (!Tools::getValue('confirm_password')) {
                $errors[] = $this->l('Please confirm your password');
            } elseif (Tools::getValue('password') != Tools::getValue('confirm_password')) {
                $errors[] = $this->l('Please verify your password and its confirmation');
            } elseif (Tools::strlen(Tools::getValue('password')) < 6) {
                $errors[] = $this->l('Your password must contain at least 6 characters');
            } else {
                EnvoimoinscherModel::updateConfig('EMC_PASS', Tools::getValue('password'));
            }

            // validate company
            if (Tools::getValue('contact_ste')) {
                EnvoimoinscherModel::updateConfig('EMC_COMPANY', Tools::getValue('contact_ste'));
            } else {
                $errors[] = $this->l('Please specify your company');
            }

            // validate address
            if (Tools::getValue('adresse1')) {
                // save address only if country is France
                if (Tools::getValue('pz_iso') == 'FR') {
                    $address = Tools::getValue('adresse1');
                    if (Tools::isSubmit('adresse2')) {
                        $address .= ' ' . Tools::getValue('adresse2');
                    }
                    if (Tools::isSubmit('adresse3')) {
                        $address .= ' ' . Tools::getValue('adresse3');
                    }
                    EnvoimoinscherModel::updateConfig('EMC_ADDRESS', $address);
                }
            } else {
                $errors[] = $this->l('Please specify your address');
            }

            // validate postcode
            if (Tools::getValue('contact_cp')) {
                // save postcode only if country is France
                if (Tools::getValue('pz_iso') == 'FR') {
                    EnvoimoinscherModel::updateConfig('EMC_POSTALCODE', (int)Tools::getValue('contact_cp'));
                }
            } else {
                $errors[] = $this->l('Please specify your postal code');
            }

            // validate city
            if (Tools::getValue('contact_ville')) {
                // save city only if country is France
                if (Tools::getValue('pz_iso') == 'FR') {
                    EnvoimoinscherModel::updateConfig('EMC_CITY', Tools::getValue('contact_ville'));
                }
            } else {
                $errors[] = $this->l('Please specify your city');
            }

            // validate phone
            if (Tools::getValue('contact_tel')) {
                if (!$helper->validatePhone(Tools::getValue('contact_tel'))) {
                    $errors[] = $this->l('Please specify a valid phone number');
                } else {
                    EnvoimoinscherModel::updateConfig('EMC_TEL', Tools::getValue('contact_tel'));
                }
            } else {
                $errors[] = $this->l('Please specify your telephone number');
            }

            // validate siret
            if (!Tools::getValue('contact_stesiret')) {
                if (Tools::getValue('pz_iso') == 'FR') {
                    $errors[] = $this->l('Please specify the SIRET (business identification) number');
                } else {
                    $errors[] = $this->l('Please specify the legal registration or enrolment number in the country');
                }
            } elseif (Tools::getValue('pz_iso') == 'FR' && Tools::strlen(Tools::getValue('contact_stesiret')) != 14) {
                $errors[] = $this->l(
                    'The SIRET (business identification) number is invalid. Please verify that it contains 14 figures'
                );
            }

            // validate cgv
            if (!Tools::getValue('cgv')) {
                $errors[] = $this->l('Please check the General Terms of Sale before proceeding');
            }

            if (!empty($errors)) {
                return $this->displayError(
                    $this->l('The following errors have occurred:') . '<ul><li>' .
                    implode('</li><li>', $errors) . '</li></ul>'
                );
            }
        }

        // Validation for "get key" form
        if (Tools::getValue('choice') == 'get_key') {
            // validate login
            if (Tools::getValue('login')) {
                EnvoimoinscherModel::updateConfig('EMC_LOGIN', Tools::getValue('login'));
            } else {
                $errors[] = $this->l('Please specify a login');
            }

            // validate email
            if (Tools::getValue('contact_email')) {
                if (!$helper->validateEmail(Tools::getValue('contact_email'))) {
                    $errors[] = $this->l('Please specify a valid email address');
                } else {
                    EnvoimoinscherModel::updateConfig('EMC_MAIL', Tools::getValue('contact_email'));
                }
            } else {
                $errors[] = $this->l('Please specify your email address');
            }

            // validate cgv
            if (Tools::getValue('cgv') == false) {
                $errors[] = $this->l('Please check the General Terms of Sale before proceeding');
            }

            if (!empty($errors)) {
                return $this->displayError(
                    $this->l('The following errors have occurred:') . '<ul><li>' .
                    implode('</li><li>', $errors) . '</li></ul>'
                );
            }
        }

        // Save "get key" form info if user has already got an API key and has filled in the form nonetheless
        if (Tools::getValue('choice') == 'proceed') {
            // save login
            if (Tools::getValue('login')) {
                EnvoimoinscherModel::updateConfig('EMC_LOGIN', Tools::getValue('login'));
            }

            // save email
            if (Tools::getValue('contact_email')) {
                EnvoimoinscherModel::updateConfig('EMC_MAIL', Tools::getValue('contact_email'));
            }
        }

        if (Tools::getValue('choice') == 'get_key' || Tools::getValue('choice') == 'create') {
            $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
            $options = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER =>
                  array('Authorization: ' . $helper->encode($config['EMC_LOGIN'] . ':' . $config['EMC_PASS'])),
                CURLOPT_CAINFO => dirname(__FILE__) . '/ca/ca-bundle.crt',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_POST => true
            );

            if (Tools::getValue('choice') == 'create') {
                $url = 'http://www.envoimoinscher.com/ajax/validate-inscription.txt.vtl';

                $password = $helper->encryptPassword(pSQL(Tools::getValue('password')));
                $confirm_password = $helper->encryptPassword(pSQL(Tools::getValue('confirm_password')));

                $postfields = array(
                    'module' => 'prestashop',
                    'moduleEMC' => true,
                    'facturation.contact_civ' => Tools::getValue('contact_civ'),
                    'facturation.contact_nom' => pSQL(trim(Tools::getValue('contact_nom'))),
                    'facturation.contact_prenom' => pSQL(trim(Tools::getValue('contact_prenom'))),
                    'user.profession' => Tools::getValue('profession'),
                    'user.logiciel' => Tools::getValue('logiciel'),
                    'user.partner_code' => pSQL(trim(Tools::getValue('partner_code'))),
                    'facturation.url' => pSQL(trim(Tools::getValue('url'))),
                    'facturation.contact_email' => pSQL(trim(Tools::getValue('contact_email'))),
                    'facturation.contact_email2' => pSQL(trim(Tools::getValue('contact_email2'))),
                    'user.login' => pSQL(trim(Tools::getValue('login'))),
                    'user.password' => urlencode($password),
                    'user.confirm_password' => urlencode($confirm_password),
                    'facturation.contact_ste' => pSQL(trim(Tools::getValue('contact_ste'))),
                    'facturation.adresse1' => pSQL(trim(Tools::getValue('adresse1'))),
                    'facturation.adresse2' => pSQL(trim(Tools::getValue('adresse2'))),
                    'facturation.adresse3' => pSQL(trim(Tools::getValue('adresse3'))),
                    'facturation.pays_iso' => Tools::getValue('pz_iso'),
                    'facturation.codepostal' =>
                      pSQL(trim(Tools::getValue('contact_cp'))) . ' ' . pSQL(trim(Tools::getValue('contact_ville'))),
                    'facturation.contact_tel' => pSQL(trim(Tools::getValue('contact_tel'))),
                    'facturation.contact_stesiret' => pSQL(trim(Tools::getValue('contact_stesiret'))),
                    'facturation.contact_tvaintra' => pSQL(trim(Tools::getValue('contact_tvaintra'))),
                    'facturation.contact_locale' => Tools::getValue('contact_locale'),
                    'cgv' => Tools::getValue('cgv'),
                    'newsletterEmc' => Tools::getValue('newsletterEmc'),
                    'newsletterCom' => Tools::getValue('newsletterCom'),
                );

                if (Tools::isSubmit('contact_etat')) {
                    $postfields['facturation.contact_etat'] = Tools::getValue('contact_etat');
                }
            } else {
                $url = 'http://ecommerce.envoimoinscher.com/ajax/validate-api-key.txt.vtl';
                $postfields = array(
                    'facturation.logiciel' => Tools::getValue('logiciel'),
                    'user.login' => pSQL(Tools::getValue('login')),
                    'facturation.contact_email' => pSQL(Tools::getValue('contact_email')),
                    'cgv' => Tools::getValue('cgv'),
                );
            }

            $options[CURLOPT_URL] = $url;
            $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
            $req = curl_init();
            curl_setopt_array($req, $options);
            $result = curl_exec($req);

            // Manage errors
            $curl_info = curl_getinfo($req);

            if (curl_errno($req) > 0) {
                $errors[] = curl_error($req);
            } elseif ($curl_info['http_code'] != '200' && $curl_info['http_code'] != '400'
              && $curl_info['http_code'] != '401') {
                $errors[] = $this->l('There has been an error sending the request.') . $curl_info['http_code'] . ')';
            }

            curl_close($req);
            if (!empty($errors)) {
                return $this->displayError(
                    $this->l('The following errors have occurred:') . '<ul><li>' .
                    implode('</li><li>', $errors) . '</li></ul>'
                );
            } else {
                $errors = Tools::jsonDecode($result);

                if (1 !== $errors) {
                    $errors = array_unique((array)$errors);
                    $error_string = '';
                    $i = 0;
                    foreach ($errors as $value) {
                        if ($i != 0) {
                            $error_string .= '</li><li>';
                        }
                        $i += 1;
                        $error_string .= $value;
                    }
                    return $this->displayError(
                        $this->l('The following errors have occurred:') . '<ul><li>' .
                        $error_string . '</li></ul>'
                    );
                } else {
                    EnvoimoinscherModel::updateConfig('EMC_USER', 0);
                    if (Tools::getValue('choice') == 'create') {
                        return $this->displayConfirmation(
                            $this->l(
                                'Your account has been successfully created. ' .
                                'You will receive your API key shortly, please check your mail box.'
                            )
                        );
                    } elseif (Tools::getValue('choice') == 'get_key') {
                        return $this->displayConfirmation(
                            $this->l('You will receive your API key shortly, please check your mail box.')
                        );
                    }
                }
            }
        }
        // if user has already an account and an API key
        EnvoimoinscherModel::updateConfig('EMC_USER', 0);
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
            Tools::getValue('EMC_api_test') &&
            Tools::getValue('EMC_api_prod') &&
            Tools::getValue('EMC_gender') &&
            Tools::getValue('EMC_exp_firstname') &&
            Tools::getValue('EMC_exp_lastname') &&
            Tools::getValue('EMC_exp_company') &&
            Tools::getValue('EMC_exp_address') &&
            Tools::getValue('EMC_exp_postcode') &&
            Tools::getValue('EMC_exp_town') &&
            Tools::getValue('EMC_exp_phone') &&
            Tools::getValue('EMC_exp_email')
        ) {
            // Update Value settings
            EnvoimoinscherModel::updateConfig('EMC_LOGIN', Tools::getValue('EMC_login'));
            EnvoimoinscherModel::updateConfig('EMC_PASS', Tools::getValue('EMC_pass'));
            EnvoimoinscherModel::updateConfig('EMC_KEY_TEST', Tools::getValue('EMC_api_test'));
            EnvoimoinscherModel::updateConfig('EMC_KEY_PROD', Tools::getValue('EMC_api_prod'));
            EnvoimoinscherModel::updateConfig('EMC_CIV', Tools::getValue('EMC_gender'));
            EnvoimoinscherModel::updateConfig('EMC_FNAME', Tools::getValue('EMC_exp_firstname'));
            EnvoimoinscherModel::updateConfig('EMC_LNAME', Tools::getValue('EMC_exp_lastname'));
            EnvoimoinscherModel::updateConfig('EMC_COMPANY', Tools::getValue('EMC_exp_company'));
            EnvoimoinscherModel::updateConfig('EMC_ADDRESS', Tools::getValue('EMC_exp_address'));
            EnvoimoinscherModel::updateConfig('EMC_COMPL', Tools::getValue('EMC_exp_more_infos'));
            EnvoimoinscherModel::updateConfig('EMC_POSTALCODE', Tools::getValue('EMC_exp_postcode'));
            EnvoimoinscherModel::updateConfig('EMC_CITY', Tools::getValue('EMC_exp_town'));
            EnvoimoinscherModel::updateConfig('EMC_TEL', Tools::getValue('EMC_exp_phone'));
            EnvoimoinscherModel::updateConfig('EMC_MAIL', Tools::getValue('EMC_exp_email'));

            // Remove the EMC_KEY_TEST_DONOTCHECK and EMC_KEY_PROD_DONOTCHECK flags after
            // entering the production and test API keys
            if (EnvoimoinscherModel::getConfig('EMC_KEY_TEST') != ''
              && EnvoimoinscherModel::getConfig('EMC_KEY_PROD') != '') {
                if (EnvoimoinscherModel::getConfig('EMC_KEY_TEST_DONOTCHECK') == 1) {
                    Configuration::deleteByName('EMC_KEY_TEST_DONOTCHECK');
                }
                if (EnvoimoinscherModel::getConfig('EMC_KEY_PROD_DONOTCHECK') == 1) {
                    Configuration::deleteByName('EMC_KEY_PROD_DONOTCHECK');
                }
            }

            if (Tools::isSubmit('EMC_exp_start_pickup')) {
                EnvoimoinscherModel::updateConfig('EMC_DISPO_HDE', Tools::getValue('EMC_exp_start_pickup'));
            }

            if (Tools::isSubmit('EMC_exp_end_pickup')) {
                EnvoimoinscherModel::updateConfig('EMC_DISPO_HLE', Tools::getValue('EMC_exp_end_pickup'));
            }

            // If no first time
            if ((int)EnvoimoinscherModel::getConfig('EMC_USER') >= 3) {
                Tools::redirectAdmin($this->link . '&EMC_tabs=merchant&conf=6');

            } else {
                EnvoimoinscherModel::updateConfig('EMC_USER', '1');
                return $this->displayConfirmation($this->l('Your account information is now complete.'));
            }

        } else {
            return $this->displayError($this->l('Please check your form, some fields are required'));
        }
    }

    private function postProcessCarriersParcelPoints()
    {
        $parcel_points = Tools::getValue('parcel_point');

        // fetch the 32 last char -- PS config field accept <= 32 char in PS 1.5
        if (count($parcel_points) > 0) {
            foreach ($parcel_points as $carrier => $code) {
                EnvoimoinscherModel::updateConfig('EMC_PP_' . Tools::strtoupper(Tools::substr($carrier, -25)), $code);
            }
        }
    }

    /**
     * Set Carriers configuration
     * @return string Error message
     */
    private function postProcessCarriersAdvanced()
    {
        $helper = new EnvoimoinscherHelper();
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        // do operation on offers only when "configuration" is checked
        if ($config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_CONFIG) {
            if (Tools::getValue('btnCarriersAdvanced')) {
                $this->postProcessCarriersParcelPoints();

                $from_weight = 0; // Initialize
                //update dimensions
                for ($i = 1; $i <= Tools::getValue('countDims'); $i++) {
                    $data = array(
                        'length_ed' => (int)Tools::getValue('length' . $i),
                        'width_ed' => (int)Tools::getValue('width' . $i),
                        'height_ed' => (int)Tools::getValue('height' . $i),
                        'weight_from_ed' => (float)$from_weight,
                        'weight_ed' => (float)Tools::getValue('weight' . $i)
                    );
                    $from_weight = $data['weight_ed'];
                    $this->model->updateDimensions($data, (int)Tools::getValue('id' . $i));
                }

                // handle services (insert only new services; delete only not choosen ones)
                $all_ser = (array)Tools::getValue('offers');
                EnvoimoinscherModel::updateConfig('EMC_SERVICES', implode(',', $all_ser));
                $full_list = array();
                foreach ($all_ser as $serv) {
                    $full_list[] = '\'' . pSQL($serv) . '\'';
                }
                $not_in = array();
                $srv_list = $helper->makeCodeKeys($this->model->getOffers(
                    false,
                    EnvoimoinscherModel::FAM_EXPRESSISTE,
                    ' AND CONCAT_WS("_", es.`emc_operators_code_eo` , es.`code_es` ) IN (' .
                    implode(',', $full_list) . ') '
                ));
                foreach ($srv_list as $service) {
                    $pricing = (
                        Tools::getValue(
                            $service['emc_operators_code_eo'] . '_' . $service['code_es'] . '_emc'
                        ) == 'real'
                    ) ? (EnvoimoinscherModel::REAL_PRICE) : (EnvoimoinscherModel::RATE_PRICE);

                    // EMC column
                    $data = array(
                        'id_es' => (int)$service['id_es'],
                        'pricing_es' => $pricing,
                        'name' => $service['label_es'],
                        'active' => 1,
                        'is_module' => 1,
                        'need_range' => 1,
                        'range_behavior' => 1,
                        'shipping_external' => 1,
                        'external_module_name' => $this->name
                    );

                    $carrier_id = $this->model->saveCarrier($data, $service);

                    if ($carrier_id === false) {
                        return false;
                    }
                    $not_in[] = (int)$carrier_id;

                    DB::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'emc_services
            SET id_carrier = ' . (int)$carrier_id . ', pricing_es = ' . $pricing . '
            WHERE id_es = ' . (int)$service['id_es'] . '');
                }

                // Carriers have been saved
                $not_in_carrier = '';
                if (count($not_in) > 0) {
                    $not_in_carrier = 'AND c.`id_carrier` NOT IN (' . implode(',', $not_in) . ')';
                }

                // get all EnvoiMoinsCher services (to remove images)
                $image_rmv = array();

                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` AS c
           INNER JOIN `' . _DB_PREFIX_ . 'emc_services` AS es
           ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "' . EnvoimoinscherModel::FAM_EXPRESSISTE . '"
           WHERE c.`external_module_name` = "envoimoinscher" AND c.`deleted` = 0 ' . $not_in_carrier . '';

                $services_emc = Db::getInstance()->ExecuteS($sql);
                foreach ($services_emc as $service_emc) {
                    $image_rmv[] = (int)$service_emc['id_carrier'];
                }

                $delete_sql = 'UPDATE `' . _DB_PREFIX_ . 'carrier` AS c
           INNER JOIN `' . _DB_PREFIX_ . 'emc_services` AS es
           ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "' . EnvoimoinscherModel::FAM_EXPRESSISTE . '"
           SET c.`deleted` = 1 WHERE c.`external_module_name` = "envoimoinscher" ' . $not_in_carrier . '';

                Db::getInstance()->Execute($delete_sql);
                // remove images too
                foreach ($image_rmv as $image) {
                    unlink(_PS_IMG_DIR_ . 's/' . $image . '.jpg');
                }

                Tools::redirectAdmin($this->link . '&EMC_tabs=advanced_carriers&conf=6');
            }
        } else {
            return $this->displayError($this->l('Please set the module in config mode'));
        }
    }

    private function postProcessCarriersSimple()
    {
        $helper = new EnvoimoinscherHelper();
        $config = $helper->configArray(EnvoimoinscherModel::getConfigData());
        // do operation on offers only when 'configuration' is checked
        if ($config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_CONFIG) {
            $this->postProcessCarriersParcelPoints();

            //$langs = Language::getLanguages(true);
            //$zones = Zone::getZones(true);

            // handle services (insert only new services; delete only not choosen ones)
            $all_ser = (array)Tools::getValue('offers');
            EnvoimoinscherModel::updateConfig('EMC_SERVICES', implode(',', $all_ser));
            $full_list = array();
            foreach ($all_ser as $serv) {
                $full_list[] = '\'' . pSQL($serv) . '\'';
            }
            $not_in = array();
            $srv_list = $helper->makeCodeKeys(
                $this->model->getOffers(
                    false,
                    EnvoimoinscherModel::FAM_ECONOMIQUE,
                    ' AND CONCAT_WS("_", es.`emc_operators_code_eo` , es.`code_es` ) IN (' .
                    implode(',', $full_list) . ') '
                )
            );
            foreach ($srv_list as $service) {
                $pricing = (
                    Tools::getValue(
                        $service['emc_operators_code_eo'] . '_' . $service['code_es'] . '_emc'
                    ) == 'real'
                ) ? (EnvoimoinscherModel::REAL_PRICE) : (EnvoimoinscherModel::RATE_PRICE);

                // EMC column
                $data = array(
                    'id_es' => (int)$service['id_es'],
                    'pricing_es' => $pricing,
                    'name' => $service['label_es'],
                    'active' => 1,
                    'is_module' => 1,
                    'need_range' => 1,
                    'range_behavior' => 1,
                    'shipping_external' => 1,
                    'external_module_name' => $this->name
                );

                $carrier_id = $this->model->saveCarrier($data, $service);
                if ($carrier_id === false) {
                    return false;
                }
                $not_in[] = (int)$carrier_id;

                // Remove backslash added to quotes in carrier description
                $cookie = $this->getContext()->cookie;
                DB::getInstance()->Execute(
                    'UPDATE ' . _DB_PREFIX_ . 'carrier_lang
                    SET delay = "' . stripcslashes($service['desc_store_es']) . '"
                    WHERE id_carrier = ' . (int)$carrier_id . ' AND id_lang = ' . (int)$cookie->id_lang . ''
                );
            }

            // Carriers have been saved
            $not_in_carrier = '';
            if (count($not_in) > 0) {
                $not_in_carrier = 'AND c.`id_carrier` NOT IN (' . implode(',', $not_in) . ')';
            }

            // get all EnvoiMoinsCher services (to remove images)
            $image_rmv = array();

            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'carrier` AS c
         INNER JOIN `' . _DB_PREFIX_ . 'emc_services` AS es
         ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "' . EnvoimoinscherModel::FAM_ECONOMIQUE . '"
         WHERE c.`external_module_name` = "envoimoinscher" AND c.`deleted` = 0 ' . $not_in_carrier . '';

            $services_emc = Db::getInstance()->ExecuteS($sql);
            foreach ($services_emc as $service_emc) {
                $image_rmv[] = (int)$service_emc['id_carrier'];
            }

            $delete_sql = 'UPDATE `' . _DB_PREFIX_ . 'carrier` AS c
         INNER JOIN `' . _DB_PREFIX_ . 'emc_services` AS es
         ON c.`id_carrier` = es.`id_carrier` AND es.`family_es` = "' . EnvoimoinscherModel::FAM_ECONOMIQUE . '"
         SET c.`deleted` = 1 WHERE c.`external_module_name` = "envoimoinscher" ' . $not_in_carrier . '';

            Db::getInstance()->Execute($delete_sql);

            // remove images too
            foreach ($image_rmv as $image) {
                unlink(_PS_IMG_DIR_ . 's/' . $image . '.jpg');
            }

            $step = EnvoimoinscherModel::getConfig('EMC_USER');
            EnvoimoinscherModel::updateConfig('EMC_USER', '3');

            Tools::redirectAdmin(
                $this->link . '&EMC_tabs=' . ($step == '1' ? 'merchant' : 'simple_carriers') . '&conf=6'
            );
        } else {
            return $this->displayError($this->l('Please set the module in config mode'));
        }
    }

    /**
     * Set Sends configuration
     * @param    boolean $all If is the full configuration to save
     * @return mixed             Error message
     */
    private function postProcessSends($all = true)
    {
        if (Tools::getValue('EMC_type') && Tools::getValue('EMC_nature')) {
            // Update configuration
            EnvoimoinscherModel::updateConfig('EMC_TYPE', Tools::getValue('EMC_type'));
            EnvoimoinscherModel::updateConfig('EMC_NATURE', Tools::getValue('EMC_nature'));
            EnvoimoinscherModel::updateConfig('EMC_WRAPPING', Tools::getValue('EMC_wrapping'));
            EnvoimoinscherModel::updateConfig('EMC_CONTENT_AS_DESC', (int)Tools::getValue('contentAsDesc'));
            if ($all === false) {
                EnvoimoinscherModel::updateConfig('EMC_USER', '2');
                return $this->displayConfirmation($this->l('Your shipment details are now complete.'));
            }
        } else {
            return $this->displayError($this->l('Thanks to choose type and nature of your picks.'));
        }
        if (Tools::isSubmit('pickupDay0') &&
           (Tools::getValue('pickupFrom0')
              || (Tools::isSubmit('pickupFrom0')
              && Tools::getValue('pickupFrom0') == '0'))
           && (Tools::getValue('pickupTo0')
              || (Tools::isSubmit('pickupTo0')
              && Tools::getValue('pickupTo0') == '0'))
           && Tools::isSubmit('pickupDay1')
           && (Tools::getValue('pickupTo1')
              || (Tools::isSubmit('pickupTo1')
              && Tools::getValue('pickupTo1') == '0'))
           && (Tools::getValue('pickupFrom1')
              || (Tools::isSubmit('pickupFrom1')
              && Tools::getValue('pickupFrom1') == '0'))
        ) {
            // Update CFG
            // News
            EnvoimoinscherModel::updateConfig('EMC_INDI', Tools::getValue('EMC_indiv'));
            EnvoimoinscherModel::updateConfig('EMC_MULTIPARCEL', Tools::getValue('EMC_multiparcel'));
            EnvoimoinscherModel::updateConfig('EMC_WEIGHTMIN', (int)Tools::getValue('EMC_min_weight'));
            EnvoimoinscherModel::updateConfig(
                'EMC_AVERAGE_WEIGHT',
                str_replace(',', '.', Tools::getValue('EMC_default_weight'))
            );
            EnvoimoinscherModel::updateConfig(
                'EMC_ASSU',
                Tools::isSubmit('EMC_use_axa') ? Tools::getValue('EMC_use_axa') : 0
            );
            // Old
            EnvoimoinscherModel::updateConfig('EMC_PICKUP_J1', Tools::getValue('pickupDay0'));
            EnvoimoinscherModel::updateConfig('EMC_PICKUP_F1', Tools::getValue('pickupFrom0'));
            EnvoimoinscherModel::updateConfig('EMC_PICKUP_T1', Tools::getValue('pickupTo0'));
            EnvoimoinscherModel::updateConfig('EMC_PICKUP_J2', Tools::getValue('pickupDay1'));
            EnvoimoinscherModel::updateConfig('EMC_PICKUP_F2', Tools::getValue('pickupFrom1'));
            EnvoimoinscherModel::updateConfig('EMC_PICKUP_T2', Tools::getValue('pickupTo1'));
            EnvoimoinscherModel::updateConfig('EMC_MASS', Tools::getValue('EMC_mass'));
            EnvoimoinscherModel::updateConfig('EMC_LABEL_DELIVERY_DATE', Tools::getValue('labelDeliveryDate'));

            Tools::redirectAdmin($this->link . '&EMC_tabs=sends&conf=6');
        } else {
            return $this->displayError($this->l('Please check your form, some fields are required'));
        }
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

    public static function getMapByOpe(
        $ope,
        $srv = false,
        $city = false,
        $postalcode = false,
        $address = false,
        $country = false
    ) {
        $link = (EnvoimoinscherModel::getConfig('EMC_ENV') == 'TEST') ?
          '//test.envoimoinscher.com' : '//www.envoimoinscher.com';
        $link .= '/choix-relais.html?cp=' . ($postalcode ?
            $postalcode : EnvoimoinscherModel::getConfig('EMC_POSTALCODE')) .
                '&ville=' . urlencode(($city ? $city : EnvoimoinscherModel::getConfig('EMC_CITY'))) .
                '&country=' . ($country ? $country : 'FR') . '&srv=' . $srv . '&ope=' . $ope;
        return $link;
    }

    public function handlePush()
    {
        return $this->model->handlePush();
    }
}
