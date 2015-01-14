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

class EnvoimoinscherOrder
{
	private $order_data, $orders, $stats, $offer_data, $prestashop_config = array();
	private $order_id, $planning_id, $type = 0;
	protected $model  = null;

	/**
	 * Mapping between POST values (. is replaced by _) and the values sent to API.
	 * @access protected
	 * @var array
	 */
	protected $mapping = array('disponibilite_HDE' => 'disponibilite.HDE',
	'disponibilite_HLE' => 'disponibilite.HLE', 'collecte' => 'collecte', 'depot_pointrelais' => 'depot.pointrelais',
	'retrait_pointrelais' => 'retrait.pointrelais', 'envoi_raison' => 'raison',
	'colis_valeur' => 'colis.valeur', 'palette_valeur' => 'palette.valeur', 'encombrant_valeur' => 'encombrant.valeur',
	'pli_valeur' => 'pli.valeur', 'colis_description' => 'colis.description', 'palette_description' => 'palette.description',
	'encombrant_description' => 'encombrant.description', 'pli_description' => 'pli.description',
	'assurance_emballage' => 'assurance.emballage', 'assurance_materiau' => 'assurance.materiau',
	'assurance_protection' => 'assurance.protection', 'assurance_fermeture' => 'assurance.fermeture'
	);

	/**
	 * Mapping between POST values for delivery address.
	 * @access protected
	 * @var array
	 */
	protected $post_dest_fields = array('dest_tel' => 'tel', 'dest_fname' => 'prenom', 'dest_lname' => 'nom', 'dest_add' => 'adresse',
	'dest_code' => 'code_postal', 'dest_city' => 'ville', 'dest_email' => 'email', 'dest_company' => 'societe');

	/**
	 * Public constructor.
	 * @access public
	 * @param MySQL $model Database instance
	 * @return void
	 */
	public function __construct($model)
	{
		$this->model = $model;
		$planning = $this->model->getLastPlanning();
		if (isset($planning['id_eopl']))
		{
			$this->planning_id = $planning['id_eopl'];
			$this->orders = Tools::jsonDecode($planning['orders_eopl'], true);
			$this->stats = Tools::jsonDecode($planning['stats_eopl'], true);
			$this->errors = Tools::jsonDecode($planning['errors_eopl'], true);
			$this->type = $planning['type_eopl'];
		}
	}

	/**
	 * Initialize the API Object for a make order
	 * @access private
	 * @return mixed : array with the quotation object and param used
	 */
	private function getOrderObject()
	{
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/WebService.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/Quotation.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/includes/EnvoimoinscherHelper.php');

		$order_object = array();

		$helper = new EnvoimoinscherHelper();

		////////////////////////////////////////////////
		////// generation of the quotation object //////
		////////////////////////////////////////////////
		$cot_cl = new Env_Quotation(
			array(
				'user' => $this->order_data['config']['EMC_LOGIN'],
				'pass' => $this->order_data['config']['EMC_PASS'],
				'key'  => $this->order_data['config']['EMC_KEY']
			)
		);

		$offers_orders = $this->model->getOffersOrder();
		$emc = Module::getInstanceByName('envoimoinscher');
		$cot_cl->setPlatformParams($emc->ws_name, _PS_VERSION_, $emc->version);
		$quot_info = array(
			'delai'        => $offers_orders[$this->order_data['config']['EMC_ORDER']]['emcValue'],
			'code_contenu' => $this->order_data['config']['EMC_NATURE'],
			'module'       => $this->prestashop_config['wsName'],
			'version'      => $this->prestashop_config['version'],
			'type_emballage.emballage' => Configuration::get('EMC_WRAPPING'),
			'partnership'  => $this->model->getPartnership()
		);
		$cot_cl->setEnv(Tools::strtolower($this->order_data['config']['EMC_ENV']));

		//////////////////////////////////////////////////
		////// add the delivery and shipper address //////
		//////////////////////////////////////////////////
		$cot_cl->setPerson(
			'expediteur',
			array(
				'pays'        => 'FR',
				'code_postal' => $this->order_data['config']['EMC_POSTALCODE'],
				'ville'       => $this->order_data['config']['EMC_CITY'],
				'type'        => 'entreprise',
				'societe'     => $this->order_data['config']['EMC_COMPANY'],
				'adresse'     => $this->order_data['config']['EMC_ADDRESS'],
				'civilite'    => $this->order_data['config']['EMC_CIV'],
				'prenom'      => $this->order_data['config']['EMC_FNAME'],
				'nom'         => $this->order_data['config']['EMC_LNAME'],
				'email'       => $this->order_data['config']['EMC_MAIL'],
				'tel'         => $this->order_data['config']['EMC_TEL'],
				'infos'       => $this->order_data['config']['EMC_COMPL']
			)
		);
		$dest_type = $this->order_data['delivery']['type'];
		if ((int)Configuration::get('EMC_INDI') == 1) $dest_type = 'particulier';

		$dest_array = array(
			'pays'        => $this->order_data['delivery']['pays'],
			'code_postal' => $this->order_data['delivery']['code_postal'],
			'ville'       => $this->order_data['delivery']['ville'],
			'type'        => $dest_type,
			'adresse'     => $this->order_data['delivery']['adresse'],
			'civilite' 	  => $this->order_data['delivery']['civilite'] == 'M.' ? 'M.' : 'Mme',
			'prenom'      => $this->order_data['delivery']['prenom'],
			'nom'         => $this->order_data['delivery']['nom'],
			'email'       => $this->order_data['delivery']['email'],
			'societe'     => $this->order_data['delivery']['societe'],
			'tel'         => $helper->normalizeTelephone($this->order_data['delivery']['tel']),
			'infos'       => $this->order_data['delivery']['other']
		);
		foreach ($this->post_dest_fields as $field => $value)
		{
			if (Tools::isSubmit($field))
			{
				$dest_array[$value] = Tools::getValue($field);
				unset($_POST[$field]);
			}
		}

		$cot_cl->setPerson('destinataire', $dest_array);
		$order_object['tmp_del'] = $dest_array;

		/////////////////////////////////////////////////
		////// generate the quotation informations //////
		/////////////////////////////////////////////////
		$quot_info = array_merge($quot_info, $this->order_data['default']);
		$quot_info['service'] = $this->offer_data['offer']['service']['code'];
		$quot_info['operateur'] = $this->order_data['order'][0]['emc_operators_code_eo'];
		$quot_info['collecte'] = $this->offer_data['offer']['collection']['date'];
		if ($_POST)
		{
			// add some complementary informations from _POST values
			foreach ($_POST as $p => $post)
				if (isset($this->mapping[$p]))
					$quot_info[$this->mapping[$p]] = $post;
			$quot_info['operateur'] = $this->order_data['order'][0]['emc_operators_code_eo'];
			$quot_info['description'] = Tools::getValue($this->order_data['config']['EMC_TYPE'].'_description');
			$quot_info['valeur_declaree.valeur'] = Tools::getValue($this->order_data['config']['EMC_TYPE'].'_valeur');
			$quot_info['valeur'] = Tools::getValue($this->order_data['config']['EMC_TYPE'].'_valeur');
			//$parcel_weight = (float)$_POST['weight'];
		}
		$quot_info['depot.pointrelais'] = $this->order_data['order'][0]['emc_operators_code_eo'].'-'.$quot_info['depot.pointrelais'];
		$quot_info['retrait.pointrelais'] = $this->order_data['order'][0]['emc_operators_code_eo'].'-'.$quot_info['retrait.pointrelais'];

		// set tracking key
		$tracking_key = sha1($this->order_id.$helper->getValueToToken($quot_info).Tools::getRemoteAddr().time());
		$url_params = '?key='.$tracking_key.'&order='.$this->order_id;
		//$shop_domain = Tools::getShopDomain();
		$url = Tools::getHttpHost(true, true).'/modules/envoimoinscher/push/push.php';
		$quot_info['url_push'] = $url.$url_params;

		$order_object['tmp_quote'] = $quot_info;
		$order_object['tracking_key'] = $tracking_key;

		////////////////////////////////////////////////////
		////// generate the proforma if international //////
		////////////////////////////////////////////////////
		$order_object['tmp_proforma'] = array();
		if ((Tools::getValue('proformaSend') == 1) || (!$_POST && $dest_array['pays'] != 'FR'))
		{
			$proforma_post = array();
			$proforma_weight = 0;
			$i = 1;
			foreach ($this->order_data['proforma'] as $item)
			{
				$item['poids'] = (float)$item['poids'] - 0.01;
				if (Tools::isSubmit('desc_en_'.$i)) $item['description_en'] = Tools::getValue('desc_en_'.$i);
				if (Tools::isSubmit('desc_fr_'.$i)) $item['description_fr'] = Tools::getValue('desc_fr_'.$i);
				$proforma_post[$i] = $item;
				if ($proforma_post[$i]['poids'] <= 0)
					$proforma_post[$i]['poids'] = 0.001;
				$proforma_post[$i]['poids'] = EnvoimoinscherHelper::normalizeToKg(Configuration::get('PS_WEIGHT_UNIT'), $proforma_post[$i]['poids']);
			// $proforma_weight = already in kg (proformaPost is converted every time)
				$proforma_weight = $proforma_weight + $proforma_post[$i]['poids'];
				$i++;
			}
			if ($proforma_weight > $this->order_data['productWeight'])
			{
				$diff = $proforma_weight - $this->order_data['productWeight'];
				$proforma_post[$i - 1]['poids'] = $proforma_post[$i - 1]['poids'] - $diff;
			}
			elseif ($proforma_weight == $this->order_data['productWeight'])
				$proforma_post[$i - 1]['poids'] = $proforma_post[$i - 1]['poids'] - 0.01;
			$order_object['tmp_proforma'] = $proforma_post;
			//$tmp_proforma = $proforma_post;
			$cot_cl->setProforma($proforma_post);
		}

		////////////////////////////////////////////////////
		////// add the parcel data (size, weight etc) //////
		////////////////////////////////////////////////////
		$cot_cl->setType($this->order_data['config']['EMC_TYPE'], $this->order_data['parcels']);
		$order_object['tmp_parcels'] = $this->order_data['parcels'];

		$order_object['object'] = $cot_cl;

		return $order_object;
	}

	/**
	 * Send order request to EnvoiMoinsCher's API and insert the data into Prestashop database.
	 * @access public
	 * @param boolean $do_skip If true, makes modifies array with orders to send.
	 * @return boolean True for success, false otherwise.
	 */
	public function doOrder($do_skip = true)
	{
		$emc = Module::getInstanceByName('envoimoinscher');
		$cookie = $emc->getContext()->cookie;

		$order_object = $this->getOrderObject();
		$order_passed = $order_object['object']->makeOrder($order_object['tmp_quote'], true);

		$mass_order = false;
		// if 'todo' exists, we send more than one order
		if (isset($this->orders['todo']) && $do_skip)
		{
			$mass_order = true;
			$this->skipOrder($this->order_id);
		}

		if (!$order_object['object']->curl_error && !$order_object['object']->resp_error && $order_passed)
		{// order was passed
			$result = true;

			$this->order_data['tracking_key'] = $order_object['tracking_key'];
			$this->order_data['new_order_state'] = $this->order_data['config']['EMC_CMD'];
			$this->order_data['employee'] = (int)$cookie->id_employee;
			$this->model->insertOrder($this->order_id, $this->order_data, $order_object['object']->order, $_POST);
			// increment 'ok'
			if (!isset($this->stats['ok']))
				$this->stats['ok'] = 0;
			$this->stats['ok']++;
		}
		else
		{// order did not passed
			$error_list = array();
			if ($order_object['object']->curl_error_text != '')
				$error_list[] = $order_object['object']->curl_error_text;
			foreach ($order_object['object']->resp_errors_list as $error)
				$error_list[] = $error['message'];
			$this->model->insertOrderError($this->order_id, implode('', $error_list));
			$this->errors[] = array('id' => $this->order_id, 'message' => implode('<br /> -', $error_list));
			unset($cookie->emc_error_txt);
			unset($cookie->emc_error_send);
			if (!$mass_order)
			{
				$cookie->emc_error_txt = implode('', $error_list);
				$cookie->emc_error_send = 1;
			}
			Logger::addLog('[ENVOIMOINSCHER] Une erreur pendant l\'envoi de la commande '.$this->order_id.' '.$cookie->emc_error_txt, 4);
			// increment errors
			if (!isset($this->stats['errors']))
				$this->stats['errors'] = 0;
			$this->stats['errors']++;

			$this->model->addPostData($this->order_id, array(
				'delivery' => $order_object['tmp_del'],
				'quote' => $order_object['tmp_quote'],
				'parcels' => $order_object['tmp_parcels'],
				'proforma' => $order_object['tmp_proforma'],
				'emcErrorTxt' => $cookie->emc_error_txt,
				'emcErrorSend' => $cookie->emc_error_send
				));
		}
		if ($do_skip)
			$this->updateOrdersList();
		return $result;
	}

	/**
	 * Getters
	 */
	/**
	 * Get final result after sending checked orders.
	 * @access public
	 * @param string $format Returned format (array or json)
	 * @result mixed Array or JSON format returned to user
	 */
	public function getFinalResult($format = 'array')
	{
		$result = array('stats' => $this->stats, 'errors' => $this->errors);
		if ($format == 'json')
			return Tools::jsonEncode($result);
		return $result;
	}

	/**
	 * Returns actual id of treated order.
	 * @access public
	 * @return int Order id.  If 0, no order is treated.
	 */
	public function getOrderId()
	{
		return $this->order_id;
	}

	/**
	 * Gets next order to treat.
	 * @access public
	 * @return int Order id. If 0, they are no more orders to treat.
	 */
	public function getNextOrderId()
	{
		if (isset($this->orders['todo'][1])) return $this->orders['todo'][1];
		return 0;
	}

	/**
	 * Gets orders.
	 * @access public
	 * @return array Array with orders
	 */
	public function getOrders()
	{
		return $this->orders;
	}

	/**
	 * Gets stats.
	 * @access public
	 * @return array Stats array
	 */
	public function getStats()
	{
		return $this->stats;
	}

	/**
	 * Setters
	 */
	/**
	 * Sets informations about treated order.
	 * @access public
	 * @param array $order_data Order informations.
	 * @return void
	 */
	public function setOrderData($order_data)
	{
		$this->order_data = $order_data;
	}

	/**
	 * Sets offer informations. Offer data is used to get some mandatory informations as collection date
	 * which can be removed by the user.
	 * @access public
	 * @param array $offer_data Offer informations.
	 * @return void
	 */
	public function setOfferData($offer_data)
	{
		$this->offer_data = $offer_data;
	}

	/**
	 * Sets id of actual order.
	 * @access public
	 * @param int $order_id Id of order to treat. If 0, get the order id automatically.
	 * @return void
	 */
	public function setOrderId($order_id = 0)
	{
		if ($order_id > 0)
			$this->order_id = $order_id;
		elseif ($order_id == 0 && count($this->orders['todo']) > 0)
			$this->order_id = $this->orders['todo'][0];
	}

	/**
	 * Sets plugin informations (version, if Prestashop 1.3 installed etc.)
	 * @access public
	 * @param array $config Configuration data
	 * @return void
	 */
	public function setPrestashopConfig($config)
	{
		$this->prestashop_config = $config;
	}

	/**
	 * Skipping methods
	 */
	/**
	 * Skips order by $order_id. It's a public method because it can be called from outside.
	 * @access public
	 * @param int $order_id Order id to skip (was treated or will not be treated)
	 * @return void
	 */
	public function skipOrder($order_id)
	{
		// order treated
		$key = array_keys($this->orders['todo'], $order_id);
		// remove from todo
		unset($this->orders['todo'][$key[0]]);
		// add to done
		$this->orders['done'][] = $order_id;
		$s = 0;
		$orders_todo = array();
		$todo_clone = $this->orders['todo'];

		$keys = array_keys($this->orders['todo']);
		foreach ($keys as $t)
		{
			$orders_todo[$s] = $todo_clone[$t];
			unset($this->orders['todo'][$t]);
			$s++;
		}
		$this->orders['todo'] = $orders_todo;
	}

	/**
	 * Increments skipped orders.
	 * @access public
	 * @return void
	 */
	public function incrementSkipped()
	{
		$this->stats['skipped']++;
	}

	/**
	 * Init methods
	 */
	/**
	 * Construct orders list.
	 * @access public
	 * @param array $orders Orders array
	 * @param int $type Type of sending
	 * @return void
	 */
	public function constructOrdersLists($orders, $type)
	{
		$this->model->makeNewPlanning($orders, $type);
	}

	/**
	 * Updates orders list.
	 * @access public
	 * @return void
	 */
	public function updateOrdersList()
	{
		$this->model->updateOrdersList(array('orders' => $this->orders, 'stats' => $this->stats, 'errors' =>  $this->errors), $this->planning_id);
	}

	/**
	 * Checkers
	 */
	/**
	 * Checks if we can do some more 'mass orders'.
	 * @access public
	 * @return bool True if we can send more, false otherwise.
	 */
	public function doOtherOrders()
	{
		return isset($this->orders['todo']) && count($this->orders['todo']) > 0;
	}

	/**
	 * Checks if 'mass orders' type is an 'error type' (orders from the last table).
	 * @access public
	 * @return bool True if is error type, false otherwise
	 */
	public function isErrorType()
	{
		return $this->type == 3;
	}

	/**
	 * Removes all planning data.
	 * @access public
	 * @return void
	 */
	public function cleanOrders()
	{
		$this->model->removePlanning();
	}

}

?>