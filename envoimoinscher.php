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

if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_.'/envoimoinscher/EnvoimoinscherModel.php');
require_once(_PS_MODULE_DIR_.'/envoimoinscher/EnvoimoinscherHelper.php');
require_once(_PS_MODULE_DIR_.'/envoimoinscher/EnvoimoinscherOrder.php');

class Envoimoinscher extends CarrierModule
{

	/**
	 * Protected array to imit SQL transactions.
	 * @var array
	 * @access protected
	 */
	protected $tables = array('emc_categories', 'emc_dimensions', 'emc_documents', 'emc_operators',
		'emc_orders', 'emc_orders_tmp', 'emc_points', 'emc_services', 'emc_tracking', 'emc_api_pricing');

	/**
	 * Array with environments of envoimoinscher web service.
	 * @var array
	 * @access protected
	 */
	protected $environments =
		array(
			'TEST' => array(
				'link' => 'https://test.envoimoinscher.com',
				'alias' => 'de test'
			),
			'PROD' => array(
				'link' => 'https://www.envoimoinscher.com',
				'alias' => 'de production'
			)
		);

	/**
	 * Shipping types.
	 * @var array
	 * @access protected
	 */
	protected $ship_types = array('colis', 'encombrant', 'palette', 'pli');

	/**
	 * List with civilities used on quotation and shippment order.
	 * @var array
	 * @access protected
	 */
	protected $civilities = array('M' => 'M', 'Mme' => 'Mme', 'Mlle' => 'Mlle');

	/**
	 * List with order modes (automatic or manually)
	 * @var array
	 * @access protected
	 */
	protected $modes = array('automatique', 'manuel');

	/**
	 * Pricing types.
	 * @var array
	 * @access protected
	 */
	protected $pricing = array('real' => 'le prix réel', 'scale' => 'le forfait');

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

	private $shipping_cost_cache = array();
	private $api_params_cache = array();

	public function __construct()
	{
		$this->name = 'envoimoinscher';
		$this->tab = 'shipping_logistics'; // tab_module
		$this->version = '3.0.4';
		$this->author = 'EnvoiMoinsCher';
		$this->local_version = '3.0.4';
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		$this->displayName = 'EnvoiMoinsCher';
		$this->ws_name = 'Prestashop';
		$this->description = 'Module de livraison : 21 transporteurs à tarifs négociés';
		if (!defined('__DIR__'))
			define('__DIR__', _PS_MODULE_DIR_.'/envoimoinscher');
		// commente require(dirname(__FILE__).'/backward_compatibility/backward.php');
		$this->model = new EnvoimoinscherModel(Db::getInstance(), $this->name);
		$this->link = new Link();
	}

	/**
	 * Install function.
	 * @access public
	 * @return boolean True if correct installation, false if not
	 */
	public function install()
	{
		if (!extension_loaded('curl'))
		{
			$error  = '[ENVOIMOINSCHER]['.time().'] installation :  Impossible d\'installer le module car';
			$error .= 'l\'extension cURL n\'est pas activée, voir avec votre webmaster pour l\'activer.';
			Logger::addLog($error);
			return false;
		}
		$query = array();
		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_categories`';
		$query[] = '
				 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_categories` (
				 `id_eca` int(11) NOT NULL,
				 `emc_categories_id_eca` int(11) NOT NULL,
				 `name_eca` varchar(100) NOT NULL,
				 PRIMARY KEY (`id_eca`)
				 ) DEFAULT CHARSET=utf8 ';

		$query[] = '
			 INSERT INTO `'._DB_PREFIX_.'emc_categories` (`id_eca`, `emc_categories_id_eca`, `name_eca`) VALUES
			 (10000, 0, "Livres et documents"),
			 (10100, 10000, "Documents sans valeur commerciale"),
			 (10120, 10000, "Journaux"),
			 (10130, 10000, "Magazines, revues"),
			 (10140, 10000, "Manuels techniques"),
			 (10150, 10000, "Livres"),
			 (10160, 10000, "Passeports"),
			 (10170, 10000, "Billets d\'avion"),
			 (10180, 10000, "Radiographies"),
			 (10190, 10000, "Photographies"),
			 (10200, 10000, "Courrier interne d\'entreprise"),
			 (10210, 10000, "Propositions commerciales"),
			 (10220, 10000, "Documents publicitaires"),
			 (10230, 10000, "Catalogues, rapports annuels"),
			 (10240, 10000, "Listings informatiques"),
			 (10250, 10000, "Plans, dessins"),
			 (10260, 10000, "Documents d\'impression"),
			 (10280, 10000, "Patrons"),
			 (10290, 10000, "Etiquettes, autocollants"),
			 (10300, 10000, "Documents d\'appels d\'offres"),
			 (20000, 0, "Alimentation et matières périssables"),
			 (20100, 20000, "Denrées alimentaires non périssables"),
			 (20102, 20000, "Produits frais et périssables"),
			 (20103, 20000, "Produits réfrigérés"),
			 (20105, 20000, "Produits surgelés"),
			 (20110, 20000, "Boissons non alcoolisées"),
			 (20120, 20000, "Boissons alcoolisées"),
			 (20130, 20000, "Plantes, fleurs, semences"),
			 (30000, 0, "Produits"),
			 (30100, 30000, "Cosmétiques, bien-être"),
			 (30200, 30000, "Pharmacie, médicaments"),
			 (30300, 30000, "Chimie, droguerie, produits d\'entretien"),
			 (50190, 30000, "Tabac"),
			 (50200, 30000, "Parfums"),
			 (40000, 0, "Habillement et accessoires"),
			 (40100, 40000, "Chaussures"),
			 (40110, 40000, "Tissus, vêtements neufs"),
			 (40120, 40000, "Vêtements usagés"),
			 (40125, 40000, "Accessoires vestimentaires, de mode"),
			 (40130, 40000, "Cuirs, peaux, maroquinerie"),
			 (40150, 40000, "Bijoux fantaisie"),
			 (50160, 40000, "Bijoux, objets précieux"),
			 (50000, 0, "Appareils et matériels"),
			 (50100, 50000, "Matériel médical"),
			 (50110, 50000, "Informatique, High tech, téléphonie fixe"),
			 (50113, 50000, "Téléphonie mobile et accessoires"),
			 (50114, 50000, "Téléviseurs, écrans d\'ordinateur"),
			 (50120, 50000, "Autres appareils et matériels"),
			 (50130, 50000, "Supports numériques, CD, DVD"),
			 (50140, 50000, "Pièces de rechange et accessoires (auto)"),
			 (50150, 50000, "Pièces de rechange et accessoires (autres)"),
			 (50170, 50000, "Montres, horlogerie (hors bijoux)"),
			 (50330, 50000, "Articles de camping, de pêche"),
			 (50350, 50000, "Articles de sport (hors vêtement)"),
			 (50360, 50000, "Instruments de musique et accessoires"),
			 (50380, 50000, "Matériel de chauffage, chaudronnerie"),
			 (50390, 50000, "Matériel de labo, optique, de mesure"),
			 (50395, 50000, "Matériel électrique, transfo., câbles"),
			 (50400, 50000, "Fournitures de bureau, papeterie, recharges"),
			 (50420, 50000, "Moteurs"),
			 (50430, 50000, "Motos, scooters"),
			 (50440, 50000, "Vélos, cycles sans moteur"),
			 (50450, 50000, "Outillage, outils, bricolage"),
			 (50490, 50000, "Plomberie, tubes plastiques"),
			 (50500, 50000, "Quincaillerie, robinetterie, serrurerie"),
			 (60000, 0, "Mobilier et décoration"),
			 (60100, 60000, "Mobilier d\'habitation"),
			 (60102, 60000, "Mobilier de bureau"),
			 (60105, 60000, "Mobilier démonté sous emballage"),
			 (60108, 60000, "Mobilier ancien (antiquité)"),
			 (60110, 60000, "Electroménager "),
			 (60112, 60000, "Petit électroménager, petits appareils ménagers"),
			 (60120, 60000, "Objets ou tableaux cotés, de collection, miroirs, vitres"),
			 (60122, 60000, "Objets et tableaux courants "),
			 (60124, 60000, "Lampes, luminaire"),
			 (60126, 60000, "Tapis"),
			 (60128, 60000, "Toiles, rideaux, draps"),
			 (60129, 60000, "Sanitaires, verres, cristallerie, bibelots"),
			 (60130, 60000, "Autres objets fragiles et sculptures"),
			 (70000, 0, "Effets personnels, cadeaux"),
			 (50180, 70000, "Cadeaux, cadeaux entreprise"),
			 (70100, 70000, "Bagages, valises, malles"),
			 (70200, 70000, "Petit déménagement, cartons, effets personnels") ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_dimensions`';
		$query[] = '
				 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_dimensions` (
				 `id_ed` int(3) NOT NULL AUTO_INCREMENT,
				 `length_ed` int(3) NOT NULL,
				 `width_ed` int(3) NOT NULL,
				 `height_ed` int(3) NOT NULL,
				 `weight_from_ed` float NOT NULL,
				 `weight_ed` float NOT NULL,
				 PRIMARY KEY (`id_ed`)
			   )  DEFAULT CHARSET=utf8 ';

		$query[] = '
			 INSERT INTO '._DB_PREFIX_.'emc_dimensions (`id_ed`, `length_ed`, `width_ed`, `height_ed`, `weight_from_ed`, `weight_ed`) VALUES
			 (1, 18, 18, 18, 0, 1),
			 (2, 22, 22, 22, 1, 2),
			 (3, 26, 26, 26, 2, 3),
			 (4, 28, 28, 28, 3, 4),
			 (5, 31, 31, 31, 4, 5),
			 (6, 33, 33, 33, 5, 6),
			 (7, 34, 34, 34, 6, 7),
			 (8, 36, 36, 36, 7, 8),
			 (9, 37, 37, 37, 8, 9),
			 (10, 39, 39, 39, 9, 10),
			 (11, 44, 44, 44, 10, 15),
			 (12, 56, 56, 56, 15, 20),
			 (13, 57, 57, 57, 20, 50) ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_documents`';
		$query[] = '
				 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_documents` (
				 `id_ed` int(11) NOT NULL AUTO_INCREMENT,
				 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
				 `'._DB_PREFIX_.'cart_id_cart` int(10) unsigned NOT NULL,
				 `link_ed` varchar(255) NOT NULL,
				 `generated_ed` int(1) NOT NULL DEFAULT 0,
				 `type_ed` enum("label","proforma") NOT NULL,
				 PRIMARY KEY (`id_ed`),
				 KEY `'._DB_PREFIX_.'orders_id_order` (`'._DB_PREFIX_.'orders_id_order`)
			   ) DEFAULT CHARSET=utf8 ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_operators`';
		$query[] = '
				 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_operators` (
				 `id_eo` int(2) NOT NULL AUTO_INCREMENT,
				 `name_eo` varchar(100) NOT NULL,
				 `code_eo` char(4) NOT NULL,
				 PRIMARY KEY (`id_eo`)
				 )  DEFAULT CHARSET=utf8 ';

		$query[] = '
			 INSERT INTO `'._DB_PREFIX_.'emc_operators` (`id_eo`, `name_eo`, `code_eo`) VALUES
			 (1, "DHL Freight", "DHLF"),
			 (2, "Distribike", "DTBK"),
			 (3, "FedEx", "FEDX"),
			 (4, "Guisnel", "GUIN"),
			 (5, "Premier Air Courier", "PACO"),
			 (6, "Sernam", "SERN"),
			 (7, "Sodexi", "SODX"),
			 (8, "Relais Colis", "SOGP"),
			 (9, "TNT", "TNTE"),
			 (10, "UPS", "UPSE"),
			 (11, "Chronopost", "CHRP"),
			 (12, "Aramex", "ARAM"),
			 (13, "Saga Express", "SAGA"),
			 (14, "Colis Privé", "COPR"),
			 (15, "Evertrans", "EVER"),
			 (16, "Kordex", "KODX"),
			 (17, "Top Chrono", "TOPC"),
			 (18, "SLS-GCI", "SLSP"),
			 (19, "Agediss", "AGED"),
			 (20, "Mondial Relay", "MONR"),
			 (21, "DHL Express", "DHLE"),
			 (22, "Low Cost Express", "LOCO"),
			 (23, "Colis Privé", "COPR"),
			 (24, "La Poste", "POFR");';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_orders`';
		$query[] = '
		  CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_orders` (
		  `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
		  `emc_operators_code_eo` char(4) NOT NULL,
		  `price_ht_eor` float NOT NULL,
		  `price_ttc_eor` float NOT NULL,
		  `ref_emc_eor` char(20) NOT NULL,
		  `service_eor` varchar(20) NOT NULL,
		  `date_order_eor` datetime NOT NULL,
		  `ref_ope_eor` varchar(20) NOT NULL,
		  `info_eor` varchar(20) NOT NULL,
		  `date_collect_eor` datetime NOT NULL,
		  `date_del_eor` datetime NOT NULL,
		  `date_del_real_eor` datetime NOT NULL,
		  `tracking_eor` CHAR(255) NOT NULL,
		  `parcels_eor` INT(4) NOT NULL,
		  `base_url_eor` VARCHAR(255) NOT NULL,
		  PRIMARY KEY (`'._DB_PREFIX_.'orders_id_order`),
		  KEY `emc_operators_code_eo`(`emc_operators_code_eo`)
		  ) DEFAULT CHARSET=utf8 ';

		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_orders_errors` (
			 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
			 `errors_eoe` TEXT NOT NULL,
			 PRIMARY KEY (`'._DB_PREFIX_.'orders_id_order`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_orders_parcels` (
			 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
			 `number_eop` INT(10) unsigned NOT NULL,
			 `weight_eop` DECIMAL(5,2) NOT NULL,
			 `length_eop` INT(3) NOT NULL,
			 `width_eop` INT(3) NOT NULL,
			 `height_eop` INT(3) NOT NULL,
			 PRIMARY KEY (`'._DB_PREFIX_.'orders_id_order`, `number_eop`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_orders_plannings` (
			 `id_eopl` INT (10) unsigned NOT NULL AUTO_INCREMENT,
			 `orders_eopl` TEXT NOT NULL,
			 `stats_eopl` VARCHAR(500) NOT NULL,
			 `errors_eopl` TEXT NOT NULL,
			 `date_eopl` DATETIME NOT NULL,
			 `type_eopl` INT(1) NOT NULL,
			 PRIMARY KEY (`id_eopl`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_orders_post` (
			 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
			 `data_eopo` TEXT NOT NULL,
			 `date_eopo` DATETIME NOT NULL,
			 PRIMARY KEY (`'._DB_PREFIX_.'orders_id_order`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_orders_tmp`';
		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_orders_tmp` (
			 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
			 `data_eot` text NOT NULL,
			 `date_eot` datetime NOT NULL,
			 `errors_eot` text NOT NULL,
			 PRIMARY KEY (`'._DB_PREFIX_.'orders_id_order`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_points`';
		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_points` (
			 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
			 `point_ep` varchar(10) NOT NULL,
			 `emc_operators_code_eo` char(4) NOT NULL,
			 PRIMARY KEY (`'._DB_PREFIX_.'orders_id_order`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_services`';
		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_services` (
			 `id_es` int(3) NOT NULL AUTO_INCREMENT,
			 `id_carrier` int(11) NOT NULL DEFAULT 0,
			 `code_es` varchar(40) NOT NULL,
			 `emc_operators_code_eo` char(4) NOT NULL,
			 `label_es` varchar(100) NOT NULL,
			 `desc_es` varchar(150) NOT NULL,
			 `desc_store_es` varchar(150) NOT NULL,
			 `label_store_es` varchar(100) NOT NULL,
			 `price_type_es` int(1) NOT NULL,
			 `is_parcel_point_es` int(1) NOT NULL,
			 `is_parcel_dropoff_point_es` int(1) NOT NULL,
			 `family_es` int(1) NOT NULL,
			 `type_es` int(1) NOT NULL,
			 PRIMARY KEY (`id_es`),
			 KEY (`id_carrier`),
			 KEY `emc_operators_code_eo` (`emc_operators_code_eo`),
			 KEY `code_es` (`code_es`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = '
			 INSERT INTO `'._DB_PREFIX_.'emc_services` (`id_es`, `code_es`, `emc_operators_code_eo`, `label_es`, `desc_es`, `desc_store_es`,
			  `label_store_es`, `price_type_es`, `is_parcel_point_es`, `is_parcel_dropoff_point_es`, `family_es`, `type_es`) VALUES
				(1, "RelaisColis", "SOGP", "Relais Colis eco", "Dépôt en Relais Colis - Livraison en Relais Colis en 10 jours, en France",
			   "Livraison en Relais Colis en 10 jours", "Relais Colis®", 0, 1, 1, 1, 1),
				(2, "Standard", "UPSE", "UPS Standard", "Livraison à domicile en 24h à 72h (avant 19h), en France et dans les pays européens",
			   "Livraison à domicile en 24h à 72h (avant 19h)", "UPS Standard", 0, 0, 0, 2, 2),
				(3, "ExpressSaver", "UPSE", "UPS Express Saver", "Livraison à domicile en 72h  (avant 19h), à l\'international  (hors délai de douanes)",
			   "Livraison à domicile en 72h (avant 19h, hors délai de douanes)", "UPS Express Saver", 0, 0, 0, 2, 2),
				(4, "InternationalEconomy", "FEDX", "FedEx International Economy",
				 "Livraison à domicile en 5 jours à l\'international (hors délai de douanes)", "Livraison à domicile en 5 jours (hors délai de douanes)",
				 "FedEx International Economy", 0, 0, 0, 2, 2),
				(5, "InternationalPriority", "FEDX", "FedEx International Priority", "Livraison express à domicile, en 24h à 48h (hors délai de douanes)",
				 "Livraison express à domicile en 24h à 48h (hors délai de douanes)", "FedEx International Priority", 0, 0, 0, 2, 2),
				(6, "ExpressNational", "TNTE", "13:00 Express", "Livraison express à domicile le lendemain (avant 13h), en France",
				 "Livraison express à domicile le lendemain (avant 13h)", "13:00 Express", 1, 0, 0, 2, 2),
				(7, "Chrono13", "CHRP", "Chrono13",
		 "Dépôt en bureau de poste - Livraison express à domicile, le lendemain (avant 13h), en France.Dépôt en bureau de poste si la livraison rate.",
				 "Livraison express à domicile, le lendemain (avant 13h). Si la livraison rate, dépôt en bureau de poste", "Chrono13", 0, 0, 0, 1, 1),
				(8, "ChronoInternationalClassic", "CHRP", "Chrono Classic",
				 "Dépôt en bureau de poste - Livraison à domicile en 2 à 4 jours, à l\'international (hors délai de douanes)",
				 "Livraison à domicile en 2 à 4 jours (hors délai de douanes)", "Chrono Classic", 0, 0, 0, 1, 1),
				(9, "ExpressStandard", "SODX", "Express Standard", "Livraison à domicile en 2 à 3 jours, en France", "Livraison à domicile en 2 à 3 jours",
				 "Express Standard", 0, 0, 0, 2, 2),
				(10, "ExpressStandardInterColisMarch", "SODX", "Inter Express Standard",
				 "Livraison à domicile en 7 à 10 jours, à l\'international (hors délai de douanes)",
				 "Livraison à domicile en 7 à 10 jours (hors délai de douanes)", "Inter Express Standard", 0, 0, 0, 2, 2),
				(11, "ExpressStandardInterPlisDSVC", "SODX", "Inter Express Standard doc",
				 "Livraison à domicile en 7 à 10 jours, à l\'international (hors délai de douanes)",
				 "Livraison à domicile en 7 à 10 jours (hors délai de douanes)", "Inter Express Standard doc", 0, 0, 0, 2, 2),
				(12, "CpourToi", "MONR", "C.pourToi®", "Dépôt en point relais - Livraison en point relais en 3 à 5 jours, en France",
				 "Livraison en point relais en 3 à 5 jours", "C.pourToi®", 0, 1, 1, 1, 1),
				(13, "CpourToiEurope", "MONR", "C.pourToi® - Europe",
				 "Dépôt en point relais - Livraison en point relais en 4 à 6 jours, dans certains pays d\'Europe",
				 "Livraison en point relais en 4 à 6 jours", "C.pourToi®", 0, 1, 1, 1, 1),
				(14, "ExpressWorldwide", "DHLE", "DHL Express Worldwide",
				 "Livraison express à domicile en 24h à 72h, à l\'international (hors délai de douanes)",
				 "Livraison express à domicile en 24h à 72h (hors délai de douanes)", "DHL Express Worldwide", 0, 0, 0, 2, 2),
				(15, "EconomyExpressInternational", "TNTE", "Economy Express",
				 "Livraison à domicile en 2 à 5 jours, à l\'international (hors délai de douanes)",
				 "Livraison à domicile en 2 à 5 jours (hors délai de douanes)", "Economy Express", 0, 0, 0, 2, 2),
				(16, "DepotexpressEurope", "LOCO", "Dépôt Express Europe",
				 "Dépôt en bureau de poste - Livraison à domicile en 2 à 4 jours, en Europe (hors délai de douanes)",
				 "Livraison à domicile en 2 à 4 jours (hors délai de douanes)", "Dépôt Express Europe", 0, 0, 0, 2, 2),
				(17, "Depotexpress", "LOCO", "Dépôt Express",
	 "Dépôt en bureau de poste - Livraison express à domicile, le lendemain (avant 13h), en France. Dépôt en bureau de poste si la livraison rate.",
				 "Livraison express à domicile, le lendemain (avant 13h). Si la livraison rate, dépôt en bureau de poste", "Dépôt Express", 0, 0, 0, 2, 2),
				(18, "ChronoRelais", "CHRP", "Chrono Relais", "Livraison en points relais Chronopost", "Livraison en points relais Chronopost",
				 "Chrono Relais", 0, 1, 0, 1, 1),
				(19, "ExpressInternationalColis", "TNTE", "Express International",
				 "Livraison à domicile en 1 à 7 jours, à l\'international (hors délai de douanes)",
				 "Livraison à domicile en 1 à 7 jours (hors délai de douanes)", "Express International", 0, 0, 0, 2, 2),
				(20, "EASY", "COPR", "Colis Privé EASY",
				 "Livraison à domicile en 2 à 3 jours. En cas d\'absence, 2nde présentation ou dépôt en relais. <b>Offre sous conditions de volume.</b>",
				 "Livraison à domicile en 2 à 3 jours. En cas d\'absence, 2nde présentation ou dépôt en relais Kiala", "Colis Privé EASY", 0, 0, 0, 1, 1),
				(21, "Chrono18", "CHRP", "Chrono18",
	 "Dépôt en bureau de poste - Livraison express à domicile, le lendemain (avant 18h), en France. Dépôt en bureau de poste si la livraison rate.",
				 "Livraison express à domicile, le lendemain (avant 18h). Si la livraison rate, dépôt en bureau de poste", "Chrono18", 0, 0, 0, 1, 1),
				(22, "ColissimoAccess", "POFR", "La Poste Colissimo Access France",
				 "Délai indicatif de 48h en jours ouvrables pour les envois en France métropolitaine. Remise sans signature.",
				 "Livraison à domicile en 48h", "La Poste Colissimo Access France. Remise sans signature.", 0, 0, 0, 1, 1),
				(23, "ColissimoExpert", "POFR", "La Poste Colissimo Expert France",
				 "Délai indicatif de 48h en jours ouvrables pour les envois en France métropolitaine. Remise contre signature.",
				 "Livraison à domicile en 48h", "La Poste Colissimo Expert France. Remise contre signature.", 0, 0, 0, 1, 1);';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_operators_categories`';
		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_operators_categories` (
			 `id_eoca` int(11) NOT NULL AUTO_INCREMENT,
			 `id_eo` int(11) NOT NULL,
			 `id_eca` int(11) NOT NULL,
			 PRIMARY KEY (`id_eoca`),
			 UNIQUE KEY `id_eo` (`id_eo`, `id_eca`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = 'INSERT INTO `'._DB_PREFIX_.'emc_operators_categories` (id_eo, id_eca) VALUES
		 (11, 10300), (11, 20102), (11, 20103), (11, 20105), (11, 20130), (11, 30200), (11, 30300),
		 (11, 50114), (11, 50160), (11, 50190), (11, 50200), (11, 50420), (11, 60100), (11, 60102),
		 (11, 60108), (11, 60110), (11, 60120), (11, 70100), (8, 10160), (8, 10170), (8, 10300),
		 (8, 20100), (8, 20102), (8, 20103), (8, 20105), (8, 20110), (8, 20120), (8, 20130), (8, 30200),
		 (8, 30300), (8, 50114), (8, 50160), (8, 50190), (8, 50200), (8, 50430), (8, 60100), (8, 60102),
		 (8, 60108), (8, 60110), (8, 60120), (8, 60129), (8, 70100), (20, 10300), (20, 20102), (20, 20103),
		 (20, 20105), (20, 20130), (20, 30200), (20, 30300), (20, 50114), (20, 50160), (20, 50190), (20, 50200),
		 (20, 60100), (20, 60102), (20, 60108), (20, 60110), (20, 60120), (20, 70100), (20, 70200), (23, 10160),
		 (23, 10170), (23, 10300), (23, 20102), (23, 20103), (23, 20105), (23, 20130), (23, 30200), (23, 30300),
		 (23, 50114), (23, 50160), (23, 50190), (23, 50200), (23, 50430), (23, 60100), (23, 60102), (23, 60108),
		 (23, 60110), (23, 60120), (23, 70100), (21, 10300), (21, 20100), (21, 20102), (21, 20103), (21, 20105),
		 (21, 20110), (21, 20120), (21, 20130), (21, 30200), (21, 30300), (21, 50114), (21, 50160), (21, 50190),
		 (21, 50200), (21, 50420), (21, 50430), (21, 60100), (21, 60102), (21, 60108), (21, 60110), (21, 60120),
		 (21, 60124), (21, 60129), (21, 60130), (21, 70100), (21, 70200), (3, 10300), (3, 20102), (3, 20103),
		 (3, 20105), (3, 20130), (3, 30200), (3, 30300), (3, 50114), (3, 50160), (3, 50190), (3, 50200),
		 (3, 60100), (3, 60102), (3, 60108), (3, 60110), (3, 60120), (3, 70100), (3, 70200), (22, 10300),
		 (22, 20102), (22, 20103), (22, 20105), (22, 20130), (22, 30200), (22, 30300), (22, 50114),
		 (22, 50160), (22, 50190), (22, 50200), (22, 50420), (22, 60100), (22, 60102), (22, 60108),
		 (22, 60110), (22, 60120), (22, 70100), (7, 10160), (7, 10170), (7, 10300), (7, 20102), (7, 20103),
		 (7, 20105), (7, 20120), (7, 20130), (7, 30200), (7, 30300), (7, 40120), (7, 50114), (7, 50160),
		 (7, 50190), (7, 50200), (7, 50430), (7, 60100), (7, 60102), (7, 60108), (7, 60110), (7, 60120),
		 (7, 60129), (7, 60130), (9, 10160), (9, 20102), (9, 20103), (9, 20105), (9, 20120), (9, 20130),
		 (9, 30200), (9, 30300), (9, 50114), (9, 50160), (9, 50190), (9, 50200), (9, 50430), (9, 60100),
		 (9, 60102), (9, 60108), (9, 60110),  (9, 60120), (9, 70100), (10, 10160), (10, 20102), (10, 20103),
		 (10, 20105), (10, 20110), (10, 20120), (10, 20130), (10, 30200), (10, 30300), (10, 50100), (10, 50110),
		 (10, 50113), (10, 50114), (10, 50120), (10, 50160), (10, 50190), (10, 50200), (10, 50360), (10, 50390),
		 (10, 50420), (10, 50430), (10, 50450), (10, 60100), (10, 60102), (10, 60108), (10, 60110),  (10, 60120),
		 (10, 60124), (10, 60129), (10, 60130), (10, 70100), (10, 70200) ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_tracking`';
		$query[] = '
			 CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_tracking` (
			 `id_et` int(11) NOT NULL AUTO_INCREMENT,
			 `'._DB_PREFIX_.'orders_id_order` int(10) unsigned NOT NULL,
			 `state_et` char(4) NOT NULL,
			 `date_et` datetime NOT NULL,
			 `text_et` text NOT NULL,
			 `localisation_et` varchar(50) NOT NULL,
			 PRIMARY KEY (`id_et`),
			 KEY `'._DB_PREFIX_.'orders_id_order` (`'._DB_PREFIX_.'orders_id_order`)
			 ) DEFAULT CHARSET=utf8 ';

		$query[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_api_pricing`';
		$query[] = '
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'emc_api_pricing` (
				`id_ap` VARCHAR(255) NOT NULL,
				`'._DB_PREFIX_.'cart_id_cart` int(10) unsigned NOT NULL,
				`prices_eap` text NOT NULL,
				`price_eap` float NOT NULL DEFAULT 0,
				`date_eap` DATETIME NOT NULL,
				`carriers_eap` TEXT NOT NULL,
				`treated_eap` TEXT,
				`free_shipping_eap` INT(1) NOT NULL COMMENT "0 - no, 1 - yes",
				`point_eap` VARCHAR( 40 ),
				`order_done_eap` INT(1) NOT NULL COMMENT "0 - not done, 1 - done" DEFAULT 0,
				`points_eap` TEXT NOT NULL,
				`date_delivery` TEXT NOT NULL,
				PRIMARY KEY (`id_ap`)
		) DEFAULT CHARSET=utf8 ';

		// do relations only when InnoDB engine (prevent errors on Prestashop 1.3)
		if (strtolower(_MYSQL_ENGINE_) == 'innodb')
		{
			$query[] = '
				 ALTER TABLE `'._DB_PREFIX_.'emc_documents`
				 ADD CONSTRAINT `emc_documents_ibfk_1` FOREIGN KEY (`'._DB_PREFIX_.'orders_id_order`)
				 REFERENCES `'._DB_PREFIX_.'orders` (`id_order`) ON DELETE CASCADE ';

			$query[] = '
				 ALTER TABLE `'._DB_PREFIX_.'emc_orders`
				 ADD CONSTRAINT `emc_orders_ibfk_3` FOREIGN KEY (`'._DB_PREFIX_.'orders_id_order`)
				 REFERENCES `'._DB_PREFIX_.'orders` (`id_order`) ON DELETE CASCADE;';

			$query[] = '
				 ALTER TABLE `'._DB_PREFIX_.'emc_orders_tmp`
				 ADD CONSTRAINT `emc_orders_tmp_ibfk_1` FOREIGN KEY (`'._DB_PREFIX_.'orders_id_order`)
				 REFERENCES `'._DB_PREFIX_.'orders` (`id_order`) ON DELETE CASCADE;';

			$query[] = '
				 ALTER TABLE `'._DB_PREFIX_.'emc_points`
				 ADD FOREIGN KEY (`'._DB_PREFIX_.'orders_id_order`)
				 REFERENCES `'._DB_PREFIX_.'orders` (`id_order`) ON DELETE CASCADE;';

			$query[] = '
				 ALTER TABLE `'._DB_PREFIX_.'emc_tracking`
				 ADD FOREIGN KEY (`'._DB_PREFIX_.'orders_id_order`)
				 REFERENCES `'._DB_PREFIX_.'orders` (`id_order`) ON DELETE CASCADE ;';

			$query[] = '
				 ALTER TABLE `'._DB_PREFIX_.'emc_api_pricing`
				 ADD FOREIGN KEY (`'._DB_PREFIX_.'cart_id_cart`)
				 REFERENCES `'._DB_PREFIX_.'cart` (`id_cart`) ON DELETE CASCADE ;';
		}

		$query[] = '
			ALTER TABLE `'._DB_PREFIX_.'carrier` ADD `emc_services_id_es` INT(3) NOT NULL AFTER `id_carrier`, ADD INDEX (`emc_services_id_es`)';

		// emc_type : 0 => shipping rate, 1 => API price
		$query[] = '
			ALTER TABLE `'._DB_PREFIX_.'carrier` ADD `emc_type` INT(1) NOT NULL';

		// Delete configuration
		Configuration::deleteByName('EMC_ENVO');
		Configuration::deleteByName('EMC_CMD');
		Configuration::deleteByName('EMC_ANN');
		Configuration::deleteByName('EMC_LIV');

		// Set default configuration
		Configuration::updateValue('EMC_USER', 0);
		Configuration::updateValue('EMC_MSG', 'La plate-forme d\'expéditions est actuellement indisponible');
		Configuration::updateValue('EMC_SRV_MODE', 'config');
		Configuration::updateValue('EMC_MASS', EnvoimoinscherModel::WITH_CHECK);
		Configuration::updateValue('EMC_TRACK_MODE', false);
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
		Configuration::updateValue('EMC_LABEL_DELIVERY_DATE', 'Livraison prévue : {DATE}');
		Configuration::updateValue('EMC_TRACK_MODE', '2');
		Configuration::updateValue('EMC_LAST_CARRIER_UPDATE', '');

		// We avoid the auto update if we install the module
		Configuration::updateValue('EMC_AUTO_UPDATE_202', 'done');

		// Set default configuration state
		$states = OrderState::getOrderStates((int)Context::getContext()->language->id);

		foreach ($states as $state)
		{
			if ($state['template'] === 'preparation')
				$emc_cmd = (int)$state['id_order_state'];
			else if ($state['template'] === 'shipped')
				$emc_envo = (int)$state['id_order_state'];
			else if ($state['template'] === 'order_canceled')
				$emc_ann = (int)$state['id_order_state'];
			else if ($state['template'] == '')
				$emc_liv = (int)$state['id_order_state'];
		}

		Configuration::updateValue('EMC_CMD', (int)$emc_cmd);
		Configuration::updateValue('EMC_ENVO', (int)$emc_envo);
		Configuration::updateValue('EMC_ANN', (int)$emc_ann);
		Configuration::updateValue('EMC_LIV', (int)$emc_liv);

		// Execute queries
		foreach ($query as $q)
		{
			if (Db::getInstance()->execute($q) === false)
			{
				Logger::addLog('[ENVOIMOINSCHER]['.time().'] installation :  Une erreur d\'installation s\'est produite sur la requête : '.$q);
				// do rollback
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
		// not more used by Prestashop 1.5 : $this->registerHook('extraCarrier');
		$this->registerHook('displayCarrierList');
		$this->registerHook('updateCarrier');
		$this->registerHook('header');
		// $this->registerHook('shoppingCart');
		$this->registerHook('adminOrder');
		$this->registerHook('footer');
		$this->registerHook('DisplayBackOfficeHeader');

		$tab = new Tab();
		$tab->class_name = 'AdminEnvoiMoinsCher';
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');
		if ($tab->id_parent == 0)
			$tab->id_parent = 3;
		$tab->module = 'envoimoinscher';
		$tab->name[(int)Configuration::get('PS_LANG_DEFAULT')] = 'EnvoiMoinsCher';
		if ($tab->add() === false)
		{
			Logger::addLog('[ENVOIMOINSCHER]['.time().'] installation :  Impossible de rajouter le Tab dans le menu');
			$this->tablesRollback();
			return false;
		}
		return true;
	}

	/**
	 * Update the module
	 * necessary as the sql update sometime isn't enough (especially for hook updates)
	 * @access public
	 * @return void
	 */
	public function autoUpdate()
	{
		// hook update
		$this->registerHook('DisplayBackOfficeHeader');
		// database update
		Db::getInstance()->Execute('ALTER TABLE  '._DB_PREFIX_.'emc_services ADD  type_es INT(1) NOT NULL');
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services SET type_es = family_es WHERE 1');
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services SET type_es = 1 WHERE id_es = 7');
		Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'emc_operators_categories
						 (id_eoca int(11) NOT NULL AUTO_INCREMENT,  id_eo int(11) NOT NULL,  id_eca int(11) NOT NULL,
						 PRIMARY KEY (id_eoca),  UNIQUE KEY id_eo (id_eo,id_eca) )');
		Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'emc_operators_categories (id_eo, id_eca) VALUES
		(11, 10300), (11, 20102), (11, 20103), (11, 20105), (11, 20130), (11, 30200), (11, 30300), (11, 50114), (11, 50160),
		(11, 50190), (11, 50200), (11, 50420), (11, 60100), (11, 60102), (11, 60108), (11, 60110), (11, 60120), (11, 70100),
		(8, 10160), (8, 10170), (8, 10300), (8, 20100), (8, 20102), (8, 20103), (8, 20105), (8, 20110), (8, 20120), (8, 20130),
		(8, 30200), (8, 30300), (8, 50114), (8, 50160), (8, 50190), (8, 50200), (8, 50430), (8, 60100), (8, 60102), (8, 60108),
		(8, 60110), (8, 60120), (8, 60129), (8, 70100), (20, 10300), (20, 20102), (20, 20103), (20, 20105), (20, 20130), (20, 30200),
		(20, 30300), (20, 50114), (20, 50160), (20, 50190), (20, 50200), (20, 60100), (20, 60102), (20, 60108), (20, 60110), (20, 60120),
		(20, 70100), (20, 70200), (23, 10160), (23, 10170), (23, 10300), (23, 20102), (23, 20103), (23, 20105), (23, 20130), (23, 30200),
		(23, 30300), (23, 50114), (23, 50160), (23, 50190), (23, 50200), (23, 50430), (23, 60100), (23, 60102), (23, 60108), (23, 60110),
		(23, 60120), (23, 70100), (21, 10300), (21, 20100), (21, 20102), (21, 20103), (21, 20105), (21, 20110), (21, 20120), (21, 20130),
		(21, 30200), (21, 30300), (21, 50114), (21, 50160), (21, 50190), (21, 50200), (21, 50420), (21, 50430), (21, 60100), (21, 60102),
		(21, 60108), (21, 60110), (21, 60120), (21, 60124), (21, 60129), (21, 60130), (21, 70100), (21, 70200), (3, 10300), (3, 20102),
		(3, 20103), (3, 20105), (3, 20130), (3, 30200), (3, 30300), (3, 50114), (3, 50160), (3, 50190), (3, 50200), (3, 60100), (3, 60102),
		(3, 60108), (3, 60110), (3, 60120), (3, 70100), (3, 70200), (22, 10300), (22, 20102), (22, 20103), (22, 20105), (22, 20130),
		(22, 30200), (22, 30300), (22, 50114), (22, 50160), (22, 50190), (22, 50200), (22, 50420), (22, 60100), (22, 60102), (22, 60108),
		(22, 60110), (22, 60120), (22, 70100), (7, 10160), (7, 10170), (7, 10300), (7, 20102), (7, 20103), (7, 20105), (7, 20120), (7, 20130),
		(7, 30200), (7, 30300), (7, 40120), (7, 50114), (7, 50160), (7, 50190), (7, 50200), (7, 50430), (7, 60100), (7, 60102), (7, 60108),
		(7, 60110), (7, 60120), (7, 60129), (7, 60130), (9, 10160), (9, 20102), (9, 20103), (9, 20105), (9, 20120), (9, 20130), (9, 30200),
		(9, 30300), (9, 50114), (9, 50160), (9, 50190), (9, 50200), (9, 50430), (9, 60100), (9, 60102), (9, 60108), (9, 60110),  (9, 60120),
		(9, 70100), (10, 10160), (10, 20102), (10, 20103), (10, 20105), (10, 20110), (10, 20120), (10, 20130), (10, 30200), (10, 30300),
		(10, 50100), (10, 50110), (10, 50113), (10, 50114), (10, 50120), (10, 50160), (10, 50190), (10, 50200), (10, 50360), (10, 50390),
		(10, 50420), (10, 50430), (10, 50450), (10, 60100), (10, 60102), (10, 60108), (10, 60110),  (10, 60120), (10, 60124), (10, 60129),
		(10, 60130), (10, 70100), (10, 70200)');
		Db::getInstance()->Execute('ALTER TABLE '._DB_PREFIX_.'emc_operators ADD  map TEXT NOT NULL');
		Db::getInstance()->Execute('ALTER TABLE '._DB_PREFIX_.'emc_services ADD  is_parcel_dropoff_point_es INT(1) NOT NULL');
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services SET is_parcel_dropoff_point_es = 0');
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services SET is_parcel_dropoff_point_es = 1 WHERE is_parcel_point_es = 1');
		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'emc_services SET is_parcel_dropoff_point_es = 0 WHERE code_es = \'ChronoRelais\'');
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
	}

	/**
	 * Install updates in the database.
	 * @access public
	 * @return boolean True if correct, false otherwise.
	 */
	public function installUpdate()
	{
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
		$columns = DB::getInstance()->executeS('DESCRIBE `'._DB_PREFIX_.'carrier`');
		$column_to_remove = array('emc_services_id_es', 'emc_type'); // Column to remove
		$alter_table_carrier = 'ALTER TABLE `'._DB_PREFIX_.'carrier` ';
		$find = false;
		if ($columns && count($columns) > 0)
		{
			foreach ($columns as $column)
			{
				if (in_array($column['Field'], $column_to_remove))
				{
					if ($find === true)
						$alter_table_carrier .= ', ';
					$alter_table_carrier .= ' DROP COLUMN `'.$column['Field'].'` ';
					$find = true;
				}
			}
		}

		$query = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'emc_operators_categories`';
		DB::getInstance()->execute($query);

		// Get configuration keys
		$helper = new EnvoimoinscherHelper;
		$values = array();
		foreach ($helper->getConfigKeys() as $value)
			$values[] = '"'.$value.'"';

		// If execution doesn't work
		if ($this->tablesRollback() === false ||
				parent::uninstall() === false ||
				$tab->delete() === false ||
				Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'configuration WHERE name IN('.implode(',', $values).') ') === false ||
				$this->tablesRollback() === false ||
				DB::getInstance()->execute($alter_table_carrier) === false)
			return false;

		return true;
	}

	/**
	 * Rollback SQL queries.
	 * @access private
	 * @return boolean
	 */
	private function tablesRollback()
	{
		$query = 'DROP TABLE IF EXISTS ';
		$find = false;
		foreach ($this->tables as $table)
		{
			if ($find === true)
				$query .= ', ';

			$query .= ' `'._DB_PREFIX_.''.$table.'` ';
			$find = true;
		}

		return DB::getInstance()->execute($query);
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

	/**
	 * Return an array with the options necessary for the configuration
	 * @access public
	 * @return array
	 */
	public function getApiParams()
	{
		require_once(__DIR__.'/Env/WebService.php');
		require_once(__DIR__.'/Env/Quotation.php');

		$login = Configuration::get('EMC_LOGIN');
		$pass = Configuration::get('EMC_PASS');
		$key = Configuration::get('EMC_KEY');
		$env = Configuration::get('EMC_ENV');

		$cache_code = $login.$pass.$key.$env;
		if (isset($this->api_params_cache[$cache_code]))
			return $this->api_params_cache[$cache_code];

		$params = array();
		$params['error_code'] = array();

		if ($login == '' && $pass == '' && $key == '')
			return $params;

		// get a quotation for the params
		$from = array('pays' => 'FR','code_postal' => '75002','ville' => 'Paris','type' => 'entreprise','adresse' => '');
		$to = array('pays' => 'FR','code_postal' => '75002','ville' => 'Paris','type' => 'particulier','adresse' => '');
		$date = new DateTime();
		$quot_info = array('collecte' => $date->format('Y-m-d'),'delai' => 'aucun','valeur' => '10','code_contenu' => 10120,'operateur' => 'POFR');
		$cot_cl = new EnvQuotation(array('user' => $login, 'pass' => $pass, 'key' => $key));
		$cot_cl->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$cot_cl->setPerson('expediteur', $from);
		$cot_cl->setPerson('destinataire', $to);
		$cot_cl->setEnv(strtolower($env));
		$cot_cl->setType('colis', array(1 => array('poids' => 1,'longueur' => 20,'largeur' => 20,'hauteur' => 20)));
		$cot_cl->getQuotation($quot_info);
		$cot_cl->getOffers(false);

		foreach ($cot_cl->offers as $offer)
			if ($offer['operator']['code'] == 'POFR' && !isset($params['type_emballage.emballage']))
				$params['type_emballage.emballage'] = $offer['mandatory']['type_emballage.emballage'];

		$params['error_code'] = $cot_cl->resp_errors_list;

		// Liste des patterns d'erreurs attendu, pas ce qu'il y a de plus optimisé mais en attendant d'avoir mieux ça fera l'affaire
		foreach ($params['error_code'] as $i => $error)
			$params['error_code'][$i]['id'] = $this->getApiErrorCode($error['message']);

		$this->api_params_cache[$cache_code] = $params;
		return $params;
	}

	public function getApiErrorCode($message)
	{
		$error_list = array(
			'access_denied - invalid API key' 								=> 'API error : Invalid API key',
			'access_denied - invalid user password' 					=> 'API error : Invalid password',
			'access_denied - wrong credentials' 							=> 'API error : Wrong credentials',
			'access_denied - Invalid account payment method' 	=> 'API error : Invalid account payment method',
		);

		foreach ($error_list as $err => $id)
			if (strpos($message, $err) !== false)
				return $id;

		return false;
	}

	/**
	 * Configuration method.
	 * @access public
	 * @return void
	 */
	public function getContent()
	{
		global $smarty;

		$content = $this->postProcess();

		// on effectue automatiquement la première mise à jour des offres, si la connexion le permet
		if (Configuration::get('EMC_LAST_CARRIER_UPDATE') == '')
			$this->loadAllCarriers(false);

		// on applique la mise à jour du module si on vient d'une version < 3.0.0
		if (Configuration::get('EMC_AUTO_UPDATE_202') != 'done')
		{
			$this->autoUpdate();
			Configuration::updateValue('EMC_AUTO_UPDATE_202', 'done');
			header('Location: '.$_SERVER['REQUEST_URI']);
		}

		require_once(__DIR__.'/Env/WebService.php');
		require_once(__DIR__.'/Env/User.php');

		// get default contact data
		$address_if_filled = array(
			'EMC_COMPANY'    => false,
			'EMC_ADDRESS'    => false,
			'EMC_POSTALCODE' => false,
			'EMC_CITY'       => false,
			'EMC_TEL'        => false,
			'EMC_MAIL'       => false
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
		$helper = new EnvoimoinscherHelper();
		$config = $helper->configArray($this->model->getConfigData());
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

		$emc_user = (int)$config['EMC_USER'];

		$datas = array(
			'missedValues' => ($emc_user > 3 ? $this->makeMissedList($obligatory, $config) : array()),
			'EMC_config'   => $config,
			'link'         => new Link(),
			'envUrl'       => (!empty($config['EMC_ENV']) ? $this->environments[$config['EMC_ENV']]['link'] : null)
		);

		if ($emc_user <= 2)
		{
			if ($emc_user == 0 || $emc_user == '' || empty($emc_user))
			{
				$datas['content'] = $content.$this->getContentMerchant();
				$content = '';
			}
			else if ($emc_user == 1)
			{
				$datas['content'] = $content.$this->getContentSends(false);
				$content = '';
			}
			else if ($emc_user == 2)
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

		$smarty->assign($datas);

		$content .= $this->getContentBody();

		return $content;
	}

	private function getContentBody()
	{
		$api_params = $this->getApiParams();

		$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;

		$helper = new EnvoimoinscherHelper();

		$ver = explode('.', _PS_VERSION_);

		// on verifie si les offres ont ete mises a jour recement
		$last_update = Configuration::get('EMC_LAST_CARRIER_UPDATE');
		$send_offers_update_warning = true;
		if ($last_update != '')
		{
			$date_limit = new DateTime();
			$date_limit = date_sub($date_limit, date_interval_create_from_date_string('1 month'));
			//$date_limit->sub(new DateInterval('P1M'));
			$date_update = new DateTime($last_update);
			$send_offers_update_warning = $date_update < $date_limit;
		}

		$datas = array(
			'need_update'		=> $send_offers_update_warning,
			'PS_ver'				=> $ver[0],
			'PS_ver'				=> $ver[0],
			'PS_subver'			=> $ver[1],
			'API_errors'		=> $api_params['error_code'],
			'EMC_config'    => $helper->configArray($this->model->getConfigData()),
			'multiShipping' => Configuration::get('PS_ALLOW_MULTISHIPPING'),
			'successForm'   => (int)$cookie->success_form,
			'upgrades'      => $this->parseUpgradeXml(__DIR__.'/upgrades/upgrades.xml'),
			'modulePath'    => $this->_path
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentBody.tpl');
	}

	public function getContentHelp()
	{
		$smarty = $this->context->smarty;

		//$helper = new EnvoimoinscherHelper();
		$datas = array(
			'link'     => new Link(),
			'upgrades' => $this->parseUpgradeXml(__DIR__.'/upgrades/upgrades.xml')
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentHelp.tpl');
	}

	/**
	 * Get ajax settings content
	 * @return template Smarty Template
	 */
	private function getContentMerchant()
	{
		$smarty = $this->context->smarty;
		//$id_lang = (int)$this->context->language->id;

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
		$smarty = $this->context->smarty;

		$helper = new EnvoimoinscherHelper();

		$config = $helper->configArray($this->model->getConfigData());

		$datas = array(
			// Configuration
			'EMC_config'      => $config,
			'srvModes'        => array(
									'config' => EnvoimoinscherModel::MODE_CONFIG,
									'online' => EnvoimoinscherModel::MODE_ONLINE
								),
			'families'        => $this->model->getOffersFamilies(),
			'familTableTpl'   => $this->getTemplatePath('views/templates/admin/familyTpl.tpl'),
			'disableServices' => isset($config['EMC_SRV_MODE']) && $config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_ONLINE,
			'pricing'         => $this->pricing,
			'operators'       => EnvoimoinscherModel::getOperatorsForType($config['EMC_NATURE']),
			'nameCategory'    => EnvoimoinscherModel::getNameCategory($config['EMC_NATURE'])
		);

		$smarty->assign($datas);

		if ($type === 'Simple')
			return $this->getContentCarriersSimple($smarty);
		else if ($type === 'Advanced')
			return $this->getContentCarriersAdvanced($smarty);
	}

	/**
	 * Simple Carrier
	 * @param  Smarty $smarty Smarty
	 * @return string         Template parsed
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
	 * @param  Smarty $smarty Smarty
	 * @return string         Template parsed
	 */
	private function getContentCarriersAdvanced(Smarty $smarty)
	{
		$rows = $this->model->getDimensions();

		$datas = array(
			'dims'										 => $rows,
			'advancedExpressCarriers'  => $this->model->getOffersByFamily(EnvoimoinscherModel::FAM_EXPRESSISTE)
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
		$smarty = $this->context->smarty;
		//$id_lang = (int)$this->context->language->id;

		$api_params = $this->getApiParams();

		// we build the array $wrapping_types (wrapping type for POFR)
		$wrapping_types = array();
		if (isset($api_params['type_emballage.emballage']))
			foreach ($api_params['type_emballage.emballage']['array'] as $type)
				$wrapping_types[count($wrapping_types)] = array(
					'id' => $type,
					'name' => substr($type, strpos($type, '-') + 1)
				);

		$helper = new EnvoimoinscherHelper();

		$config = $helper->configArray($this->model->getConfigData()); // Get configs
		$config['wsName'] = $this->ws_name; // add wsName to config
		$config['localVersion'] = $this->local_version; // Add localVersionto config
		// Get pickup conf
		if (!isset($config['EMC_PICKUP_J1']))
			$pick_up_conf = array(
				array(
					'j'    => 0,
					'from' => 0,
					'to'   => 0
				),
				array(
					'j'    => 0,
					'from' => 0,
					'to'   => 0)
			);
		else
			$pick_up_conf = array(
				array(
					'j'    => $config['EMC_PICKUP_J1'],
					'from' => $config['EMC_PICKUP_F1'],
					'to'   => $config['EMC_PICKUP_T1']
					),
				array(
					'j'    => $config['EMC_PICKUP_J2'],
					'from' => $config['EMC_PICKUP_F2'],
					'to'   => $config['EMC_PICKUP_T2']
					)
			);

		$datas = array(
			// Configuration
			'EMC_config'           	=> $config,
			'shipTypes'            	=> $this->ship_types,
			'shipNature'           	=> $this->model->getCategoriesTree($config),
			'shipWrappingAvailable'	=> count($wrapping_types) > 0,
			'shipWrapping' 				 	=> $wrapping_types,
			'pickupConf'           	=> $pick_up_conf,
			'withoutMass'          	=> EnvoimoinscherModel::WITHOUT_CHECK,
			'withMass'             	=> EnvoimoinscherModel::WITH_CHECK,
			'link'                 	=> new Link(),
			'disableServices'      	=> isset($config['EMC_SRV_MODE']) && $config['EMC_SRV_MODE'] == EnvoimoinscherModel::MODE_ONLINE,
			'families'             	=> $this->model->getOffersFamilies(),
			'familTableTpl'        	=> $this->getTemplatePath('views/templates/admin/familyTpl.tpl'),
			'all'                  	=> $all
		);

		$smarty->assign($datas);

		return $this->display(__FILE__, '/views/templates/admin/getContentSends.tpl');
	}

	private function getContentSettings()
	{
		$smarty = $this->context->smarty;
		$id_lang = (int)$this->context->language->id;
		$helper = new EnvoimoinscherHelper();

		$config = $helper->configArray($this->model->getConfigData()); // Get configs

		require_once dirname(__FILE__).'/Env/WebService.php';
		require_once dirname(__FILE__).'/Env/User.php';

		$user_class = new EnvUser(array('user' => $config['EMC_LOGIN'], 'pass' => $config['EMC_PASS'], 'key' => $config['EMC_KEY']));
		$user_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$user_class->setEnv(strtolower($config['EMC_ENV']));
		$user_class->getEmailConfiguration();

		$datas = array(
			'EMC_config' => $config,
			'states'     => OrderState::getOrderStates($id_lang),
			'modes' => $this->model->getTrackingModes(),
			'mailConfig' => $user_class->user_configuration['emails']
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
			$this->context->controller->addCSS($this->_path.'/css/back-office.css', 'all');
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
			 and p.id_product = '.$id;
		if (count($attributes) > 0)
		{
			$sql .= ' and (';
			$count = count($attributes);
			for ($i = 0; $i < $count; $i++)
			{
				if ($i > 0)
					$sql .= ' or ';
				$sql .= 'pac.id_attribute = '.$attributes[$i];
			}
			$sql .= ')';
		}
		$sql .= ' UNION SELECT DISTINCT p.weight, p.price, 0, 0, 0
			 FROM '._DB_PREFIX_.'product p
			 WHERE p.id_product = '.$id;

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
		global $smarty, $cookie;
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
							'value' =>  ($tmp_product_name['value'].'_'.$attribute['id_attribute'])
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
			require_once(__DIR__.'/Env/WebService.php');
			require_once(__DIR__.'/Env/Quotation.php');
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
				 WHERE iso_code = "'.$country_iso.'"');

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
				'dbCart' => array('delivery_option' => serialize(array()))
			));
			if (isset($offers['isError']) && $offers['isError'] == 1)
			{
				$error = 1;
				$error_msg = $offers['message'];
			}
			elseif (count($offers) == 0)
			{
				$error = 1;
				$error_msg = 'Pas d\'offres correspondant à votre recherche';
			}
			else
			{
				$error = 0;
				$out_offers = array();
				foreach ($offers as $offer)
				{
					$offer['characteristics'] = '<b>-</b>'.implode('<br /><b>-</b>  ', $offer['characteristics']);
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
		global $cookie, $smarty;
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());

		// all orders to send
		$orders = $this->model->getEligibleOrders(array('lang' => $cookie->id_lang));
		$planning = $this->model->getLastPlanning();
		$orders_to_send = unserialize($planning['orders_eopl']);
		$smarty->assign('tokenOrder', Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$cookie->id_employee));
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('ordersEmc', $orders['emc']);
		$smarty->assign('ordersEmcCount', count($orders['emc']));
		$smarty->assign('ordersNoEmcCount', count($orders['others']));
		$smarty->assign('ordersErrorsCount', count($orders['errors']));
		$smarty->assign('ordersOthers', $orders['others']);
		$smarty->assign('ordersErrors', $orders['errors']);
		$smarty->assign('ordersTodo', count($orders_to_send['todo']));
		$smarty->assign('withCheck', $config['EMC_MASS'] == EnvoimoinscherModel::WITH_CHECK);
		$smarty->assign('showEmcTable', count($orders['emc']) > 0);
		$smarty->assign('showOthersTable', count($orders['others']) > 0);
		$smarty->assign('showErrorsTable', count($orders['errors']) > 0);
		$smarty->assign('successSend', (int)$cookie->success_send);
		$smarty->assign('errorLabels', (int)$cookie->error_labels);
		$smarty->assign('pagerTemplate', __DIR__.'/views/templates/admin/pager_template.tpl');
		$smarty->assign('submenuTemplate', __DIR__.'/views/templates/admin/order_submenu_template.tpl');
		$smarty->assign('ordersTableTemplate', __DIR__.'/views/templates/admin/orders_table_template.tpl');
		$smarty->assign('massTemplate', __DIR__.'/views/templates/admin/massOrders.tpl');
		$smarty->assign('ordersSendTop', __DIR__.'/views/templates/admin/table_send.tpl');
		$smarty->assign('ordersSendBottom', __DIR__.'/views/templates/admin/table_send.tpl');
		$smarty->assign('actual', '');
		$smarty->assign('actual', '');
		$smarty->assign('baseDir', __PS_BASE_URI__);
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
		require_once(__DIR__.'/Env/WebService.php');
		require_once(__DIR__.'/Env/Quotation.php');
		$admin_link_base = $this->link->getAdminLink('AdminEnvoiMoinsCher');
		$helper = new EnvoimoinscherHelper();
		$config = $helper->configArray($this->model->getConfigData());
		$emc_order = new EnvoimoinscherOrder($this->model);
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
				'result'   => 1,
				'doOthers' => 1
			);
			// do order actions
			$emc_order->setOrderId(0);
			$id_order = (int)$emc_order->getOrderId();
			if ($id_order > 0)
			{
				$data = $this->model->prepareOrderInfo($emc_order->getOrderId(), $config);
				$emc_order->setOrderData($data);
				$emc_order->setPrestashopConfig($this->getModuleConfig());
				$emc_order->setOfferData($this->getOfferToSendPage($data, $helper));
				$result['result'] = (int)$emc_order->doOrder();
				if (!$emc_order->doOtherOrders())
					$result['doOthers'] = 0;
			}
			else
				$result = array(
					'result'   => 0,
					'doOthers' => 0
				);
		}
		elseif (Tools::getValue('mode') == 'skip')
		{
			$emc_order->skipOrder((int)$_GET['previous']);
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
		echo json_encode($result);
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
		global $cookie, $smarty;
		$smarty->assign('tokenOrder', Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$cookie->id_employee));
		$count_query = Db::getInstance()->ExecuteS('SELECT COUNT(eo.'._DB_PREFIX_.'orders_id_order) AS allCmd FROM '._DB_PREFIX_.'emc_orders eo
			 JOIN '._DB_PREFIX_.'orders o ON eo.'._DB_PREFIX_.'orders_id_order = o.id_order
			 JOIN '._DB_PREFIX_.'carrier c ON c.id_carrier = o.id_carrier
			 JOIN '._DB_PREFIX_.'emc_services es ON es.id_es = c.emc_services_id_es
			 JOIN '._DB_PREFIX_.'emc_operators eop ON eop.code_eo = es.emc_operators_code_eo
			 WHERE eo.ref_emc_eor != ""');
		// set pager
		$page = 1;
		$per_page = 20;
		$all_pages = $count_query[0]['allCmd'];
		if (isset($_GET['p']))
			$page = (int)$_GET['p'];
		require_once(__DIR__.'/lib/Pager.php');
		$pager = new Pager(array(
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
		$orders = $this->model->getDoneOrders(array('lang' => $cookie->id_lang, 'start' => $start, 'limit' => $per_page));
		$smarty->assign('token', Tools::getValue('token'));
		$smarty->assign('orders', $orders);
		$smarty->assign('allOrders', count($orders));
		$smarty->assign('successSend', (int)$cookie->success_send);
		$smarty->assign('errorLabels', (int)$cookie->error_labels);
		$smarty->assign('pagerTemplate', __DIR__.'/views/templates/admin/pager_template.tpl');
		$smarty->assign('ordersTableTemplate', __DIR__.'/views/templates/admin/orders_table_template.tpl');
		$smarty->assign('submenuTemplate', __DIR__.'/views/templates/admin/order_submenu_template.tpl');
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
		global $cookie, $smarty;
		$order_id = (int)Tools::getValue('id_order');
		$post_data = $this->model->getPostData($order_id);
		$emc_order = new EnvoimoinscherOrder($this->model);
		$order_stats = $emc_order->getStats();
		$helper = new EnvoimoinscherHelper;
		$data = $this->model->prepareOrderInfo($order_id, $helper->configArray($this->model->getConfigData()));
		if ($data['is_pp'] == 1)
			$helper->setFields('depot.pointrelais',
				array('helper' => '<p class="note"><a data-fancybox-type="iframe" href="'.Envoimoinscher::getMapByOpe($data['code_eo']).
				'" style="width:1000px;height:1000px;" class="getParcelPoint action_module fancybox">'.$this->l('Get parcel point').'</a></p>'));
		else if ($data['is_pp'] == 2)
			$helper->setFields('depot.pointrelais',
				array(
					'type'   => 'input',
					'helper' => '',
					'hidden' => true
				)
			);
		$helper->setFields('retrait.pointrelais',
			array('helper' => '<p class="note"><a data-fancybox-type="iframe" href="'.Envoimoinscher::getMapByOpe($data['code_eo'],
			urlencode($data['delivery']['ville']),
			$data['delivery']['code_postal'],
			urlencode($data['delivery']['adresse'])).'" class="getParcelPoint fancybox action_module">'.$this->l('Get parcel point').'</a></p>'));

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
		$smarty->assign('moduleBaseDir', _MODULE_DIR_.'envoimoinscher/');
		$smarty->assign('showDstBlock', count($delivery_info) > 1 || (!$emc_carrier && !$offer_data['isFound']));
		$smarty->assign('weight', $weight);
		$smarty->assign('tableTemplate', __DIR__.'/views/templates/admin/offersTable.tpl');
		$smarty->assign('notFoundTemplate', __DIR__.'/views/templates/admin/offersNotFound.tpl');
		$smarty->assign('ordersAll', $order_stats['total']);
		$smarty->assign('ordersDone', $order_stats['skipped'] + $order_stats['ok'] + $order_stats['errors']);
		$smarty->assign('orderTodo', $order_stats['total'] - ($order_stats['skipped'] + $order_stats['ok'] + $order_stats['errors']));
		$smarty->assign('nextOrderId', $emc_order->getNextOrderId());
		$smarty->assign('massTemplate', __DIR__.'/views/templates/admin/massOrders.tpl');
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
										width: "1000px",
										height: "800px",
										autoDimensions: false
									});
								});
							</script>';
		if ((float)$weight == 0)
			$html .= parent::adminDisplayWarning('Your order weight are empty, please check products or enable min weight in the module settings.');

		if (Configuration::get('PS_FORCE_SMARTY_2') != 1)
			return $html.$this->display(__FILE__, '/views/templates/admin/send.tpl');
		else
			return $html.$this->display(__FILE__, '/views/templates/admin/send_13.tpl');
	}

	/**
	 * Gets offer for changed weight.
	 * @access public
	 * @return Displayed template.
	 */
	public function getOffersNewWeight()
	{
		global $smarty;
		$order_id = (int)$_GET['id_order'];
		$helper = new EnvoimoinscherHelper;
		$data = $this->model->prepareOrderInfo(
			$order_id,
			$helper->configArray($this->model->getConfigData()),
			(float)str_replace(',', '.', $_POST['weight']));
		$data['productWeight'] = (float)str_replace(',', '.', $_POST['weight']);
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
		global $cookie;
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
		global $cookie;
		require_once(__DIR__.'/Env/WebService.php');
		require_once(__DIR__.'/Env/Quotation.php');
		$order_id = (int)$_GET['id_order'];
		$code = explode('_', $_GET['code']);
		if (ctype_alnum($code[0]) && ctype_alnum($code[1]))
		{
			$rows = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'emc_services es
				 JOIN '._DB_PREFIX_.'emc_operators eo ON eo.code_eo = es.emc_operators_code_eo
				 LEFT JOIN '._DB_PREFIX_.'carrier c ON c.emc_services_id_es = es.id_es
				 WHERE es.code_es = "'.$code[1].'" AND es.emc_operators_code_eo = "'.$code[0].'"');
			if (count($rows) == 0 || (int)$rows[0]['id_carrier'] == 0)
			{
				// carrier was not found, insert a new carrier (which is deleted)
				$data = array(
					'emc_services_id_es'   => $rows[0]['id_es'],
					'name'                 => $rows[0]['label_es'].' ('.$rows[0]['name_eo'].')',
					'active'               => 0,
					'is_module'            => 1,
					'need_range'           => 1,
					'deleted'              => 1,
					'range_behavior'       => 1,
					'shipping_external'    => 1,
					'external_module_name' => $this->name
				);
				$lang_data = array(
					'id_lang' => $cookie->id_lang,
					'delay'   => $rows[0]['desc_store_es']
				);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier', $data, 'INSERT');
				$lang_data['id_carrier'] = (int)Db::getInstance()->Insert_ID();
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_lang', $lang_data, 'INSERT');
				$carrier = array('id_carrier' => $lang_data['id_carrier'], 'id_group' => '');
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', $carrier, 'INSERT');
				$rows[0]['id_carrier'] = $lang_data['id_carrier'];
			}
			// update carrier for this order
			Db::getInstance()->autoExecute(_DB_PREFIX_.'orders', array('id_carrier' => $rows[0]['id_carrier']), 'UPDATE', 'id_order = '.$order_id);
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
		global $smarty;
		$order_id = (int)$_GET['id_order'];
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
		global $cookie;
		if (isset($session_data['quote']) && count($session_data['quote']) > 0)
			$quote_data = $session_data['quote'];
		if (isset($session_data['parcels']) && count($session_data['parcels']) > 0)
			$data['parcels'] = $session_data['parcels'];
		require_once(__DIR__.'/Env/WebService.php');
		require_once(__DIR__.'/Env/Quotation.php');
		$offers_orders = $this->model->getOffersOrder();
		// EnvoiMoinsCher library
		$cot_cl = new EnvQuotation(
			array(
				'user' => $data['config']['EMC_LOGIN'],
				'pass' => $data['config']['EMC_PASS'],
				'key'  => $data['config']['EMC_KEY']
			)
		);
		$cot_cl->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$quot_info = array(
			'collecte'     => $this->setCollectDate(
				array(
					array(
						'j'    => $data['config']['EMC_PICKUP_J1'],
						'from' => $data['config']['EMC_PICKUP_F1'],
						'to'   => $data['config']['EMC_PICKUP_T1']
					),
					array(
						'j'    => $data['config']['EMC_PICKUP_J2'],
						'from' => $data['config']['EMC_PICKUP_F2'],
						'to'   => $data['config']['EMC_PICKUP_T2']
					)
				)
			),
			'type_emballage.emballage' 	=> Configuration::get('EMC_WRAPPING'),
			'delai'        							=> $offers_orders[0]['emcValue'],
			'code_contenu' 							=> $data['config']['EMC_NATURE'],
			'valeur'       							=> (float)$data['order'][0]['total_products'],
			'module'       							=> $this->ws_name,
			'version' 									=> $this->local_version
		);

		$cot_cl->setEnv(strtolower($data['config']['EMC_ENV']));
		$cot_cl->setPerson(
			'expediteur',
			array(
				'pays'        => 'FR',
				'code_postal' => $data['config']['EMC_POSTALCODE'],
				'ville'       => $data['config']['EMC_CITY'],
				'type'        => 'entreprise',
				'adresse'     => $data['config']['EMC_ADDRESS']
			)
		);
		$cot_cl->setPerson(
			'destinataire',
			array(
				'pays'        => $data['delivery']['pays'],
				'code_postal' => $data['delivery']['code_postal'],
				'ville'       => $data['delivery']['ville'],
				'type'        => $data['delivery']['type'],
				'adresse'     => $data['delivery']['adresse']
			)
		);
		$cot_cl->setType($data['config']['EMC_TYPE'], $data['parcels']);
		$cot_cl->getQuotation($quot_info); // Init params for Quotation
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
				$offer['priceHTNoIns']  = $offer['price']['tax-exclusive'];
				$offer['priceTTCNoIns'] = $offer['price']['tax-inclusive'];
				$offer['priceHT']       = $offer['price']['tax-exclusive'] + (float)isset($offer['insurance']) ? $offer['insurance']['tax-exclusive'] : 0;
				$offer['priceTTC']      = $offer['price']['tax-inclusive'] + (float)isset($offer['insurance']) ? $offer['insurance']['tax-inclusive'] : 0;
				$offer['insurance']     = $data['default']['assurance.selection'];

				$offer['collect']       = date('d-m-Y', strtotime($offer['collection']['date']));
				$offer['delivery']      = date('d-m-Y', strtotime($offer['delivery']['date']));
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
						if (preg_match('/POST/i', $mandatory['array'][0]))
							$field_type = 'hidden';
					}

					$offer['output'][] = $helper->prepareMandatory($mandatory, $default_info, $field_type, true);
				}
				if (isset($offer['mandatory']['proforma.description_en']) && count($offer['mandatory']['proforma.description_en']) > 0)
				{
					$is_proforma = true;
					$proforma_data = $data['proforma'];
					$session_proforma = unserialize($cookie->emc_order_proforma);
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

		//$slice = array();

		//$find = false;

		foreach ($delays as $delay)
		{
			if ((int)$delay['to'] != '24')
				$time_to = strtotime('+'.(int)$delay['to'].' hours', $today);
			else
				$time_to = strtotime('Tomorrow');

			if ($time >= strtotime($today.' '.(int)$delay['from'].':00') && $time < $time_to)
			{
				//$find = true;
				$days_delay = $delay['j'];
				break;
			}
		}

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
			'EMC_KEY' => 'la clé API',
			'EMC_LOGIN' => 'le login EnvoiMoinsCher',
			'EMC_PASS' => 'le mot de passe EnvoiMoinsCher',
			'EMC_FNAME' => 'le prénom',
			'EMC_LNAME' => 'le nom',
			'EMC_COMPANY' => 'la société',
			'EMC_ADDRESS' => 'l\'adresse',
			'EMC_POSTALCODE' => 'le code postal',
			'EMC_CITY' => 'la ville',
			'EMC_TEL' => 'le numéro dé téléphone',
			'EMC_MAIL' => 'l\'adresse e-mail',
			'EMC_PICKUP' => 'le jour d\'enlèvement');
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
		global $cookie;
		// global $cookie;
		$cart_context = Context::getContext()->cart;

		$cache_code = $this->id_carrier.spl_object_hash($ref).$shipping_cost;

		// cache of shipping cost
		if (isset($this->shipping_cost_cache[$cache_code]))
			return $this->shipping_cost_cache[$cache_code];

		if (Configuration::get('EMC_SRV_MODE') == EnvoimoinscherModel::MODE_CONFIG)
			return false;

		$pricing_code = EnvoimoinscherHelper::getPricingCode($cart_context);
		// ajax page : get carrier pricing from database
		$price_row = Db::getInstance()->getRow('SELECT *
			 FROM `'._DB_PREFIX_.'emc_api_pricing`
			 WHERE `id_ap` = "'.$pricing_code.'"
			 AND DATE(`date_eap`) = CURDATE() ');
		$update = false;
		if ($price_row !== 'false')
		{
			$date_eap = date_create($price_row['date_eap']);
			$date_eap_timestamp = date_timestamp_get($date_eap);
			$query = 'SELECT `date_upd` FROM `'._DB_PREFIX_.'configuration` WHERE `name` = "PS_SHIPPING_HANDLING" ';

			$date_upd_cfg = date_create(DB::getInstance()->getValue($query));
			$date_upd_cfg_timestamp = date_timestamp_get($date_upd_cfg);

			$update = $date_eap_timestamp < $date_upd_cfg_timestamp || $date_upd_cfg_timestamp === false;
		}

		if ($price_row === false || $update === true)
		{
			$addresses_array = array();
			$addresses_array[] = (int)$ref->id_address_delivery;
			require_once(__DIR__.'/Env/WebService.php');
			require_once(__DIR__.'/Env/Quotation.php');
			// load only services which have shipping_external = 1
			//$helper = new EnvoimoinscherHelper;
			//$config = $helper->configArray($this->model->getConfigData());
			// get informations about the current order
			$cart_data = $this->prepareQuotationCartData($ref->id, $ref->id_address_delivery);
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
					'address'         => $cart_data['address'],
					'weight'          => $cart_data['weight'],
					'dimensions'      => $dimensions[0],
					'cart'            => (int)$ref->id,
					'cartValue'       => $ref->getOrderTotal(true, $cart_data['typeCart']),
					'currency'        => $cart_data['idCurrency'],
					'id_lang'         => isset($cookie->id_lang) ? (int)$cookie->id_lang : '',
					'cartObject'      => $ref,
					'additionalCost'  => $cart_data['additionalCost'],
					'dbCart'          => $db_cart,
					'pricingCode'     => $pricing_code,
				));
				self::$api_results = $api_results;
			}

			$offers = self::$api_results;

			if (isset($offers[$this->id_carrier]))
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_api_pricing', $offers[$this->id_carrier]['pricingData'], 'REPLACE');
				$return = $offers[$this->id_carrier]['priceHT_db'];
			}
			else if (count($offers) === 0)
			{
				Logger::addLog('[ENVOIMOINSCHER]['.time().'] Aucune offre trouvée pour l\'adresse de destination '.
					$cookie->customer_firstname.' '.$cookie->customer_lastname.', '.
					$cart_data['address']['street'].' '.$cart_data['address']['postcode'].' '.
					$cart_data['address']['country'].', le poids du panier est de '.$cart_data['weight'].' kg', 4);
				$return = false;
			}
			else
				$return = false;
		}
		else
		{
			$prices = unserialize($price_row['carriers_eap']);
			if (!isset($prices[$ref->id_address_delivery]) || !isset($prices[$ref->id_address_delivery][$this->id_carrier]))
				$return = false;
			else
				$return = $prices[$ref->id_address_delivery][$this->id_carrier]['price_ht'];
		}

		$this->shipping_cost_cache[$cache_code] = $return;
		return $return;
	}

	public function getPackageShippingCost($r, $s, $products = null)
	{
		// prestashop standards ...
		if (isset($s) || isset($products)) $this->getOrderShippingCost($r, 322);
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
				echo 'Votre module doit être en mode de configuration';
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
		require_once(__DIR__.'/Env/WebService.php');
		require_once(__DIR__.'/Env/CarriersList.php');
		$login = Configuration::get('EMC_LOGIN');
		$pass = Configuration::get('EMC_PASS');
		$key = Configuration::get('EMC_KEY');
		$env = Configuration::get('EMC_ENV');
		$lib = new EnvCarriersList(array('user' => $login, 'pass' => $pass, 'key' => $key));
		$lib->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$lib->setEnv(strtolower($env));
		$lib->loadCarriersList($this->ws_name, $this->version);

		if ($lib->curl_error)
		{
			if ($ajax)
			{
				ob_end_clean();
				echo 'Erreur lors de l\'envoi de la requête : ';
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
				echo 'La requête n\'est pas valide : ';
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
							$service['is_parcel_point_es'] != $carrier['parcel_pickup_point'] ||
							$service['is_parcel_dropoff_point_es'] != $carrier['parcel_dropoff_point'] ||
							$service['family_es'] != $carrier['family'] ||
							$service['type_es'] != $carrier['zone'])
						$srv_to_update[count($srv_to_update)] = $carrier;
					else
						$srv_no_change[count($srv_no_change)] = $carrier;
					break;
				}
			}
			if ($srv_found == -1)
				$srv_to_insert[count($srv_to_insert)] = $carrier;
			else
				unset($services[$srv_found]);
		}

		$srv_to_delete = $services;
		$ope_to_delete = $operators;

		// On met à jour la base
		// Requête insert services
		$query = array();
		$query[0] = '';
		$first_line = true;
		if (count($srv_to_insert) > 0)
		{
			$query[0] .= 'INSERT INTO '._DB_PREFIX_.'emc_services VALUES';
			foreach ($srv_to_insert as $service)
			{
				if (!$first_line)
					$query[0] .= ',';
				$first_line = false;
				$query[0] .= '(null,0,"'.$service['srv_code'].
															'","'.$service['ope_code'].
															'","'.$service['srv_name'].
															'","'.$service['label_store'].
															'","'.$service['description'].
															'","'.$service['description_store'].
															'",0,'.$service['parcel_pickup_point'].
															','.$service['parcel_dropoff_point'].
															','.$service['family'].
															','.$service['zone'].
															')';
			}
			$query[0] .= ';';
		}
		// Requête insert opeateurs
		if (count($ope_to_insert) > 0)
		{
			$query[0] .= 'INSERT INTO '._DB_PREFIX_.'emc_operators VALUES';
			$first_line = true;
			foreach ($ope_to_insert as $operator)
			{
				if (!$first_line)
					$query[0] .= ',';
				$first_line = false;
				$query[0] .= '(null,"'.$operator['ope_name'].'","'.$operator['ope_code'].'")';
			}
			$query[0] .= ';';
		}

		// Requête update services
		$query[1] = '';
		foreach ($srv_to_update as $service)
		{
			$query[1] .= 'UPDATE '._DB_PREFIX_.'emc_services SET
										 label_es = "'.$service['srv_name'].'"
										 ,desc_es = "'.$service['label_store'].'"
										 ,desc_store_es = "'.$service['description'].'"
										 ,label_store_es = "'.$service['description_store'].'"
										 ,price_type_es = 0
										 ,is_parcel_point_es = '.$service['parcel_pickup_point'].'
										 ,is_parcel_dropoff_point_es = '.$service['parcel_dropoff_point'].'
										 ,family_es = '.$service['family'].'
										 ,type_es = '.$service['zone'].'
										 WHERE code_es = "'.$service['srv_code'].'"
										 AND emc_operators_code_eo = "'.$service['ope_code'].'";';
		}
		// Requête update operateurs
		foreach ($ope_to_update as $operator)
		{
			$query[1] .= 'UPDATE '._DB_PREFIX_.'emc_operators SET
				 name_eo = "'.$operator['ope_name'].'" WHERE code_eo = "'.$operator['ope_code'].'";';
		}

		// Requête delete services
		$query[2] = '';
		$query[3] = '';
		if (count($srv_to_delete) > 0)
		{
			$query[2] .= 'DELETE FROM '._DB_PREFIX_.'carrier WHERE ';
			$first_line = true;
			foreach ($srv_to_delete as $service)
			{
				if (!$first_line)
					$query[2] .= ' OR ';
				$first_line = false;
				$query[2] .= 'emc_services_id_es = '.$service['id_es'];
			}
			$query[2] .= ';';
			$query[3] .= 'DELETE FROM '._DB_PREFIX_.'emc_services WHERE ';
			$first_line = true;
			foreach ($srv_to_delete as $service)
			{
				if (!$first_line)
					$query[3] .= ' OR ';
				$first_line = false;
				$query[3] .= 'id_es = '.$service['id_es'];
			}
			$query[3] .= ';';
		}
		// Requête delete operateurs
		$first_line = true;
		if (count($ope_to_delete) > 0)
		{
			$query[2] .= 'DELETE FROM '._DB_PREFIX_.'emc_operators WHERE ';
			foreach ($ope_to_delete as $operator)
			{
				if (!$first_line)
					$query[2] .= ' OR ';
				$first_line = false;
				$query[2] .= 'id_eo = '.$operator['id_eo'];
			}
			$query[2] .= ';';
		}

		Db::getInstance()->execute('START TRANSACTION;');
		foreach ($query as $q)
		{
			if ($q != '' && Db::getInstance()->execute($q) === false)
			{
				Logger::addLog('[ENVOIMOINSCHER]['.time().'] Mise à jour : Erreur lors de la mise à jour des offres disponible : '.$q);
				if ($ajax)
				{
					Db::getInstance()->execute('ROLLBACK;');
					ob_end_clean();
					echo 'Erreur lors de la mise à jour des offres disponible : '.$q;
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
			echo json_encode($result);
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
		global $smarty;
		require_once(realpath(dirname(__FILE__).'/EnvoimoinscherHelper.php'));
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
		return $this->display(__FILE__, '/views/templates/admin/hookAdminOrder.tpl');
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
		global $cookie;
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
				if (isset($ope[0]) && $ope[0]['is_parcel_point_es'] == 1 && !$correct_code && $_POST['ajax'] != 'true' && $_GET['ajax'] != 'true')
				{
					$error_occured = true;
					Logger::addLog('[ENVOIMOINSCHER]['.time().'] Le point relais obligé, n\'a pas été choisi pour le panier '.$cart_id, 4);
					$variable = 'choosePoint'.$ope[0]['emc_operators_code_eo'].$o;
					$cookie->$variable = 1;
				}
				$prices = unserialize($ope[0]['prices_eap']);
				$price = $price + $prices[$o][$ope[0]['id_carrier']];
			}
		}
		if ($error_occured)
		{
			Tools::redirect('order.php?step=2');
			return false;
		}
		Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_api_pricing', array('price_eap' => $price), 'UPDATE', _DB_PREFIX_.'cart_id_cart = '.$cart_id.' ');
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
		global $cookie;
		$cookie->emc_carrier = '';
		// Get cart carrier (if EnvoiMoinsCher, make some supplementary operations)
		$row = $this->model->getCarrierByCartPricing($params['cart']->id);
		if ($row[0]['point_eap'] != '')
		{
			// Insert parcel point informations
			$point = explode('-', $row[0]['point_eap']);
			if (ctype_alnum(trim($point[0])) && ctype_alnum(trim($point[1])) && strpos(trim($point[0]), $row[0]['emc_operators_code_eo']) !== false)
			{
				$data = array(
					_DB_PREFIX_.'orders_id_order' => $params['order']->id,
					'point_ep' => trim($point[1]),
					'emc_operators_code_eo' => trim($point[0])
				);
				Db::getInstance()->autoExecute(_DB_PREFIX_.'emc_points', $data, 'INSERT');
			}
		}
		return true;
	}

	/**
	 * Handles tracking informations.
	 * @param array $params List of params.
	 * @return void
	 */
	public function hookOrderDetail($params)
	{
		global $cookie, $smarty;
		require_once(realpath(dirname(__FILE__).'/EnvoimoinscherHelper.php'));
		$helper = new EnvoimoinscherHelper;
		// get tracking informations
		$rows = $this->model->getTrackingByOrderAndCustomer($_GET['id_order'], $cookie->id_customer);
		$smarty->assign('rows', $rows);
		$smarty->assign('isAdmin', false);
		$point = $this->model->getPointInfos($_GET['id_order']);
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
		$rows = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'carrier c
			 JOIN '._DB_PREFIX_.'emc_services es ON c.emc_services_id_es = es.id_es
			 WHERE c.id_carrier = '.$params['id_carrier'].'');
		$data = array('emc_type' => $rows[0]['emc_type'], 'emc_services_id_es' => $rows[0]['id_es']);
		Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier', $data, 'UPDATE', 'id_carrier = '.(int)$params['carrier']->id.'');
	}

	/**
	 * Header's hook. It displays included JavaScript for GoogleMaps API.
	 * @access public
	 * @return Displayed Smarty template.
	 */
	public function hookHeader()
	{
		global $smarty;
		$smarty->assign('emcBaseDir', _MODULE_DIR_.'/envoimoinscher/');
		return $this->display(__FILE__, '/views/templates/admin/header_hook.tpl');
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
		global $smarty;
		$points = array();
		$point = '';
		$pricing = $this->model->getLastPrices(EnvoimoinscherHelper::getPricingCode($params['cart']));
		if (isset($pricing['points_eap']))
		{
			$points = unserialize($pricing['points_eap']);
			$point = $pricing['point_eap'];
		}
		$delivery = ($pricing['date_delivery'] == '')?array():unserialize($pricing['date_delivery']);
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
		return $this->display(__FILE__, '/views/templates/admin/envoimoinscher_carrier.tpl');
	}

	public function hookFooter($data)
	{
		if ($data) return $this->display(__FILE__, '/views/templates/admin/footer.tpl');
		return $this->display(__FILE__, '/views/templates/admin/footer.tpl');
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
	 * Sets parcel point in the database.
	 * @access public
	 * @param string $post_point Id of choosen parcel point.
	 * @return void
	 */
	public function setPoint($post_point)
	{
		global $cookie;
		$point = explode('-', $post_point);
		if (ctype_alnum(trim($point[0])) && ctype_alnum(trim($point[1])))
		{
			$data = array(
				'point_eap' => trim($post_point)
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
		global $cookie;

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
		$options = (array)unserialize($params['dbCart']['delivery_option']);
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
				new Currency((int)$params['cartObject']->id_currency)
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
		$cot_cl = new EnvQuotation(
			array(
				'user' => $config['EMC_LOGIN'],
				'pass' => $config['EMC_PASS'],
				'key'  => $config['EMC_KEY']
			)
		);
		$cot_cl->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$quot_info = array(
			'collecte'     => $this->setCollectDate(
				array(
					array(
						'j'    => $config['EMC_PICKUP_J1'],
						'from' => $config['EMC_PICKUP_F1'],
						'to'   => $config['EMC_PICKUP_T1']
					),
					array(
						'j'    => $config['EMC_PICKUP_J2'],
						'from' => $config['EMC_PICKUP_F2'],
						'to'   => $config['EMC_PICKUP_T2']
					)
				)
			),
			'delai'        => $offers_orders[0]['emcValue'],
			'code_contenu' => $config['EMC_NATURE'],
			'valeur'       => $params['cartValue'],
			'module'       => $this->ws_name,
			'version'      => $this->local_version
		);

		$cot_cl->setEnv(strtolower($config['EMC_ENV']));

		if (!$params['testPage'])
			$cot_cl->setPerson(
				'expediteur',
				array(
					'pays'        => 'FR',
					'code_postal' => $config['EMC_POSTALCODE'],
					'ville'       => $config['EMC_CITY'],
					'type'        => 'entreprise',
					'adresse'     => $config['EMC_ADDRESS']
				)
			);
		else
			$cot_cl->setPerson('expediteur', $params['addressShipper']);

		$cot_cl->setPerson(
			'destinataire',
			array(
				'pays'        => $params['address']['country'],
				'code_postal' => $params['address']['postcode'],
				'ville'       => $params['address']['city'],
				'type'        => $dest_type,
				'adresse'     => $params['address']['street']
			)
		);

		$cot_cl->setType(
			$config['EMC_TYPE'],
			array(
				1 => array(
					'poids'    => $params['weight'],
					'longueur' => (float)$params['dimensions']['length_ed'],
					'largeur'  => (float)$params['dimensions']['width_ed'],
					'hauteur'  => (float)$params['dimensions']['height_ed']
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

		// if there is less than 10 operators activated, we get them one per one (faster way)
		$offers_quotation = array();
		if (count($offers_db) >= 10)
		{
			$cot_cl->getQuotation($quot_info);
			$cot_cl->getOffers(false);
			$offers_quotation = $cot_cl->offers;
		}
		else
			foreach ($offers_db as $offer_db)
			{
				$quot_info['operateur'] = $offer_db['emc_operators_code_eo'];
				$quot_info['service'] = $offer_db['code_es'];
				$cot_cl->getQuotation($quot_info);
				if ($cot_cl->curl_error)
					continue;
				$cot_cl->getOffers(false);
				if (isset($cot_cl->offers[0]))
					foreach ($cot_cl->offers as $quot_offer)
						$offers_quotation[count($offers_quotation)] = $quot_offer;
				$cot_cl->offers = array();
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
			Logger::addLog('[ENVOIMOINSCHER]['.time().'] Une erreur pendant la récupération des offres EnvoiMoinsCher :'.$error_msg, 4);
		}
		else
		{
			if (count($offers_quotation) == 0)
			{
				//$error = 1;
				$error_msg = 'Pas d\'offres correspondant à votre recherche';
				$discount_shipping = false;
			}
			else
			{
				//$error = 0;
				$presented = array();
				// look for discount type : free shipping
				$discount_shipping = false;
				if (!$params['testPage'])
					foreach (Context::getContext()->cart->getCartRules() as $rule)
						if ((int)$rule['free_shipping'] == 1)
							$discount_shipping = true;
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
						if (($free_delivery && !in_array($offer_db['id_es'], $config['EMC_NO_FREESHIP'])) || $discount_shipping)
						{
							$offer['priceHT_db'] = 0;
							$offer['priceHT'] = 0;
							$offer['priceTTC_db'] = 0;
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
						if (!$free_delivery && !$discount_shipping)
						{
							$carrier_tax = 0;
							$carrier = new Carrier($offer_db['id_carrier']);
							$carrier_tax = Tax::getCarrierTaxRate((int)$offer_db['id_carrier'], (int)$params['cartObject']->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
							if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
								$is_weight_method = true;
							else
								$is_weight_method = false;
							// For rating price
							// Use Prestashop methods to generate the prices
							// if ($offer_db['emc_type'] == 0)
							if ($offer_db['emc_type'] == EnvoimoinscherModel::RATE_PRICE)
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
							new Currency((int)$params['cartObject']->id_currency));
						$offer['priceHT'] = Tools::convertPrice(
							(float)$offer['priceHT'],
							new Currency((int)$params['cartObject']->id_currency));
						$offer['priceHT_db'] = Tools::convertPrice(
							(float)$offer['priceHT_db'],
							new Currency((int)$params['cartObject']->id_currency));
						$offer['priceTTC_client'] = Tools::convertPrice(
							(float)$offer['priceTTC_client'],
							new Currency((int)$params['cartObject']->id_currency));
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
								'price_ht'  => $offer['priceHT_db'],
								'price_ttc' => $offer['priceTTC_db'],
								'tax'       => $carrier_tax
							);
						}
					}
				}
			}
		}
		$prices_eap = (isset($params['dbCart']['current_address']) ? serialize(array($params['dbCart']['current_address'] => $prices_array)) : null);
		$carriers_eap = (isset($params['dbCart']['current_address']) ? serialize(array($params['dbCart']['current_address'] => $carriers_info)) : null);
		$pricing_data = array(
			_DB_PREFIX_.'cart_id_cart' => $params['cart'],
			'date_eap'                 => date('Y-m-d H:i:s'),
			'date_delivery' 					 => serialize($all_delivery_dates),
			'free_shipping_eap'        => ($free_delivery || (isset($discount_shipping) && $discount_shipping)) ? 1 : 0,
			'points_eap'               => serialize($all_points_list),
			'id_ap'                    => (isset($params['pricingCode']) ? $params['pricingCode'] : false),
			'prices_eap'               => $prices_eap,
			'carriers_eap'             => $carriers_eap
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
	 * Executes upgrade queries.
	 * @access public
	 * @return Displayed template
	 */
	public function makeUpgrade()
	{
		global $cookie, $smarty;
		$error = false;
		$list = $this->parseUpgradeXml(__DIR__.'/upgrades/upgrades.xml');
		$id = (int)$_GET['up_id'];
		$queries = explode('-- REQUEST --', file_get_contents(__DIR__.'/upgrades/sql/'.$list[$id]['file']));
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
			file_put_contents(__DIR__.'/upgrades/sql/'.$list[$id]['file'], implode('-- REQUEST --', $queries));
		else
		{
			$error = $this->removeUpgradeItem($id, __DIR__.'/upgrades/upgrades.xml');
			unlink(__DIR__.'/upgrades/sql/'.$list[$id]['file']);
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
		$result = array();
		$dom_cl = new DOMDocument();
		$dom_cl->load($file);
		$xpath = new DOMXPath($dom_cl);
		$upgrades = $xpath->evaluate('/upgrades/upgrade');
		foreach ($upgrades as $upgrade)
		{
			$date = strtotime($upgrade->getElementsByTagName('date')->item(0)->nodeValue);
			$result[$upgrade->getElementsByTagName('id')->item(0)->nodeValue] = array('from' => $upgrade->getElementsByTagName('from')->item(0)->nodeValue,
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
		$main = $dom_cl->document_element;
		$xpath = new DOMXPath($dom_cl);
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

			// delivery address
			$street = $current['address1'];

			if ($current['address2'] != '')
				$street .= $current['address2'];

			$type = 'particulier';

			if ($current['company'] != '')
				$type = 'entreprise';
			$address = array(
				'id_zone'  => $current['id_zone'],
				'type'     => $type,
				'country'  => $current['iso_code'],
				'city'     => $current['city'],
				'postcode' => $current['postcode'],
				'street'   => $street);
			$id_currency = $current['id_currency'];
		}
		else
		{
			$address = array();
			$id_currency = false;
		}

		// option < 100g
		if ($weight < 0.1 && $weight >= 0 && (int)Configuration::get('EMC_WEIGHTMIN') == 1)
			$weight = 0.1;

		// cart's type (to calculate order total amount)
		$type_cart = Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING;
		return array(
			'weight'         => (float)$weight,
			'address'        => $address,
			'idCurrency'     => (int)$id_currency,
			'typeCart'       => $type_cart,
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
		global $cookie;
		$orders_to_get = array();
		if (isset($_POST['orders']))
			foreach ($_POST['orders'] as $ord)
				$orders_to_get[] = (int)$ord;
		elseif (isset($_GET['order']))
			$orders_to_get[] = (int)$_GET['order'];
		$references = $this->model->getReferencesToLabels($orders_to_get);
		$refs = array();
		foreach ($references as $reference)
			$refs[] = $reference['ref_emc_eor'];
		if (count($refs) > 0)
		{
			$cookie->error_labels = 0;
			$helper = new EnvoimoinscherHelper;
			$config = $helper->configArray($this->model->getConfigData());
			// prepare cURL elements
			$options = array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $this->environments[$config['EMC_ENV']]['link'].'/documents?type=bordereau&envoi='.implode(',', $refs),
				CURLOPT_HTTPHEADER => array('Authorization: '.base64_encode($config['EMC_LOGIN'].':'.$config['EMC_PASS'])),
				CURLOPT_CAINFO => dirname(__FILE__).'/ca/ca-bundle.crt',
				CURLOPT_SSL_VERIFYPEER => true,
				CURLOPT_SSL_VERIFYHOST => 2
			);
			$req = curl_init();
			curl_setopt_array($req, $options);
			$result = curl_exec($req);
			//$curl_info = curl_getinfo($req);
			curl_close($req);
			header('Content-type: application/pdf');
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
		$updates = (array)json_decode(file_get_contents($filename));
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
		echo json_encode($result);
		die();
	}

	public function lookForCarrierUpdates()
	{
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/WebService.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/Carrier.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/Service.php');
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		$ser_class = new EnvService(array(
			'user' => $config['EMC_LOGIN'],
			'pass' =>	$config['EMC_PASS'],
			'key' => $config['EMC_KEY']));
		$ser_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$ser_class->setEnv(strtolower($config['EMC_ENV']));
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
						$carrier_lower = strtolower($carrier['code']);
						file_put_contents(_PS_MODULE_DIR_.'/envoimoinscher/img/detail_'.$carrier_lower.'.jpg', file_get_contents($carrier['logo_modules']));
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
		echo json_encode(array(
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
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/WebService.php');
		require_once(_PS_MODULE_DIR_.'/envoimoinscher/Env/OrderStatus.php');
		$helper = new EnvoimoinscherHelper;
		$config = $helper->configArray($this->model->getConfigData());
		$ors_class = new EnvOrderStatus(array(
			'user' => $config['EMC_LOGIN'],
			'pass' =>	$config['EMC_PASS'],
			'key' => $config['EMC_KEY']));
		$ors_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
		$ors_class->setEnv(strtolower($config['EMC_ENV']));
		$ors_class->setGetParams(array('module' => $this->ws_name, 'version' => $this->local_version));
		$ors_class->getOrderInformations($_GET['ref']);
		ob_end_clean();
		if (!$ors_class->curl_error)
		{
			$label = Db::getInstance()->ExecuteS('SELECT *
				 FROM '._DB_PREFIX_.'emc_documents
				 WHERE '._DB_PREFIX_.'orders_id_order = '.(int)$_GET['order']);
			if ((bool)$ors_class->order_info['labelAvailable'])
			{
				Db::getInstance()->autoExecute(
					_DB_PREFIX_.'emc_documents',
					array('generated_ed' => 1),
					'UPDATE',
					''._DB_PREFIX_.'orders_id_order = '.(int)$_GET['order']
				);
			}
			Db::getInstance()->autoExecute(
				_DB_PREFIX_.'emc_orders',
				array('ref_ope_eor' => $ors_class->order_info['opeRef']),
				'UPDATE',
				''._DB_PREFIX_.'orders_id_order = '.(int)$_GET['order']
			);
			if (EnvoimoinscherModel::TRACK_OPE_TYPE == $config['EMC_TRACK_MODE'])
			{
				Db::getInstance()->autoExecute(
					_DB_PREFIX_.'orders',
					array('shipping_number' => $ors_class->order_info['opeRef']),
					'UPDATE',
					'id_order = '.(int)$_GET['order']
				);
			}
			$ors_class->order_info['error'] = 0;
			$ors_class->order_info['labelUrl'] = $label[0]['link_ed'];
			$ors_class->order_info['labelAvailable'] = (int)$ors_class->order_info['labelAvailable'];
			echo json_encode($ors_class->order_info);
			die();
		}
		echo json_encode(array('error' => 1));
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
		global $cookie;
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
		$order_id = (int)$_GET['id_order'];
		// get old address data
		$address = $this->model->getOrderData($order_id);
		$company = $address[0]['company'];
		if (Tools::getValue('dest_company'))
			$company = Tools::getValue('dest_company');
		$perso_alias = '_perso_'.$order_id;
		// insert new address row
		$insert_row = array(
			'id_country' => $address[0]['id_country'],
			'id_state' => $address[0]['id_state'],
			'id_customer' => $address[0]['id_customer'],
			'id_manufacturer' => $address[0]['id_manufacturer'],
			'id_supplier' => $address[0]['id_supplier'],
			'alias' => str_replace($perso_alias, '', $address[0]['alias']).$perso_alias,
			'company' => $company,
			'lastname' => Tools::getValue('dest_lname'),
			'firstname' => Tools::getValue('dest_fname'),
			'address1' => Tools::getValue('dest_add'),
			'address2' => $address[0]['address2'],
			'postcode' => Tools::getValue('dest_code'),
			'city' => Tools::getValue('dest_city'),
			'other' => $address[0]['other'],
			'phone' => Tools::getValue('dest_tel'),
			'phone_mobile' => $address[0]['phone_mobile'],
			'vat_number' => $address[0]['vat_number'],
			'dni' => $address[0]['dni'],
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
		echo json_encode($result);
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
		$this->context->smarty->assign('EMC_link', $this->link);

		// Get level configuration
		$config_status = (int)Configuration::get('EMC_USER');

		if ($config_status < 3)
		{
			// If we need to previous configuration
			if (Tools::getValue('previous'))
			{
				Configuration::updateValue('EMC_USER', Tools::getValue('previous') - 1);
				Tools::redirectAdmin($this->link);
				return;
			}

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

			json_encode(true);
			exit;
		}
		// Save environment
		else if (Tools::getValue('EMC_Env') && Tools::getValue('ajax'))
		{
			Configuration::updateValue('EMC_ENV', Tools::getValue('EMC_Env'));
			json_encode(true);
			exit;
		}
		// Load tabs
		else if (Tools::getValue('ajax'))
		{
			$tab = ucfirst(strtolower(Tools::getValue('EMC_tab')));
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

				require_once dirname(__FILE__).'/Env/WebService.php';
				require_once dirname(__FILE__).'/Env/User.php';

				$api_login = Configuration::get('EMC_LOGIN');
				$api_pass = Configuration::get('EMC_PASS');
				$api_key = Configuration::get('EMC_KEY');
				$api_env = Configuration::get('EMC_ENV');

				// update e-mail configuration
				$user_class = new EnvUser(array('user' => $api_login, 'pass' => $api_pass, 'key' => $api_key));
				$user_class->setPlatformParams($this->ws_name, _PS_VERSION_, $this->version);
				$user_class->setEnv(strtolower($api_env));

				$user_class->postEmailConfiguration(
					array(
						'label'        => Tools::getValue('EMC_mail_label', ''),
						'notification' => Tools::getValue('EMC_mail_notif', ''),
						'bill'         => Tools::getValue('EMC_mail_bill', '')
					)
				);

				Tools::redirectAdmin($this->link.'&EMC_tabs=settings&conf=6');
			}
			else
				return $this->displayError($this->l('Please check your form, some fields are requried'));
		}
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
			/*
			TODO : A déplacer
			Configuration::updateValue('EMC_RELAIS_SOGP', Tools::getValue('EMC_exp_relais_colis'));
			Configuration::updateValue('EMC_RELAIS_MONR', Tools::getValue('EMC_exp_mondial_relay'));
			*/

			/*$datas = array(
				'apiLogin' => Tools::getValue('EMC_login'),
				'apiEnv'   => Configuration::get('EMC_ENV'),
				'apiKey'   => Tools::getValue('EMC_api'),

			);*/
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

		if (count($parcel_points) > 0)
			foreach ($parcel_points as $carrier => $code)
				Configuration::updateValue('EMC_PP_'.strtoupper($carrier), $code);
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
			if (Tools::getValue('btnCarriersAdvanced'))
			{
				$this->postProcessCarriersParcelPoints();

				$from_weight = 0; // Initialize
				//update dimensions
				for ($i = 1; $i <= Tools::getValue('countDims'); $i++)
				{
					$data = array(
						'length_ed'      => Tools::getValue('length'.$i),
						'width_ed'       => Tools::getValue('width'.$i),
						'height_ed'      => Tools::getValue('height'.$i),
						'weight_from_ed' => (float)$from_weight,
						'weight_ed'      => (float)Tools::getValue('weight'.$i)
					);
					$from_weight = $data['weight_ed'];
					$this->model->updateDimensions($data, (int)Tools::getValue('id'.$i));
				}

				// handle services (insert only new services; delete only not choosen ones)
				$all_ser = (array)Tools::getValue('offers');
				Configuration::updateValue('EMC_SERVICES', implode(',', $all_ser));
				$full_list = array();
				foreach ($all_ser as $serv)
					$full_list[] = '\''.$serv.'\'';
				$not_in = array();
				$srv_list = $helper->makeCodeKeys($this->model->getOffers(
					false,
					EnvoimoinscherModel::FAM_EXPRESSISTE,
					' AND CONCAT_WS("_", es.`emc_operators_code_eo` , es.`code_es` ) IN ('.implode(',', $full_list).') ')
				);
				foreach ($srv_list as $service)
				{
					// EMC column
					$emc_type = EnvoimoinscherModel::RATE_PRICE; // price rate
					if (Tools::getValue($service['emc_operators_code_eo'].'_'.$service['code_es'].'_emc') == 'real')
						$emc_type = EnvoimoinscherModel::REAL_PRICE; // API price
					$data = array(
						'emc_services_id_es'   => (int)$service['id_es'],
						'name'                 => $service['label_es'].' ('.$service['name_eo'].')',
						'active'               => 1,
						'is_module'            => 1,
						'need_range'           => 1,
						'range_behavior'       => 1,
						'shipping_external'    => 1,
						'emc_type'             => $emc_type,
						'external_module_name' => $this->name
					);
					/*$lang_data = array(
						// 'id_lang' => 2,
						'delay' => addslashes($service['desc_store_es'])
					);*/

					$carrier_id = $this->model->saveCarrier($data, $service);
					if ($carrier_id === false)
						return false;
					$not_in[]   = (int)$carrier_id;
				}

				// Carriers have been saved
				$not_in_carrier = '';
				if (count($not_in) > 0)
					$not_in_carrier = 'AND c.`id_carrier` NOT IN ('.implode(',', $not_in).')';

				// get all EnvoiMoinsCher services (to remove images)
				$image_rmv = array();

				$sql = 'SELECT * FROM `'._DB_PREFIX_.'carrier` AS c
					 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
					 ON c.`emc_services_id_es` = es.`id_es` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_EXPRESSISTE.'"
					 WHERE c.`external_module_name` = "envoimoinscher" AND c.`deleted` = 0 '.$not_in_carrier.'';

				$services_emc = Db::getInstance()->ExecuteS($sql);
				foreach ($services_emc as $service_emc)
					$image_rmv[] = (int)$service_emc['id_carrier'];

				$delete_sql = 'UPDATE `'._DB_PREFIX_.'carrier` AS c
					 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
					 ON c.`emc_services_id_es` = es.`id_es` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_EXPRESSISTE.'"
					 SET c.`deleted` = 1 WHERE c.`external_module_name` = "envoimoinscher" '.$not_in_carrier.'';

				Db::getInstance()->Execute($delete_sql);
				// remove images too
				foreach ($image_rmv as $image)
					unlink(_PS_IMG_DIR_.'s/'.$image.'.jpg');

				Tools::redirectAdmin($this->link.'&EMC_tabs=advanced_carriers&conf=6');
			}
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
				$full_list[] = '\''.$serv.'\'';
			$not_in = array();
			$srv_list = $helper->makeCodeKeys($this->model->getOffers(
				false,
				EnvoimoinscherModel::FAM_ECONOMIQUE,
				' AND CONCAT_WS("_", es.`emc_operators_code_eo` , es.`code_es` ) IN ('.implode(',', $full_list).') ')
			);

			foreach ($srv_list as $service)
			{
				// EMC column
				$emc_type = EnvoimoinscherModel::RATE_PRICE; // price rate
				if (Tools::getValue($service['emc_operators_code_eo'].'_'.$service['code_es'].'_emc') == 'real')
					$emc_type = EnvoimoinscherModel::REAL_PRICE; // API price
				$data = array(
					'emc_services_id_es'   => (int)$service['id_es'],
					'name'                 => $service['label_es'].' ('.$service['name_eo'].')',
					'active'               => 1,
					'is_module'            => 1,
					'need_range'           => 1,
					'range_behavior'       => 1,
					'shipping_external'    => 1,
					'emc_type'             => $emc_type,
					'external_module_name' => $this->name
				);
				/*$lang_data = array(
					// 'id_lang' => 2,
					'delay' => addslashes($service['desc_store_es'])
				);*/

				$carrier_id = $this->model->saveCarrier($data, $service);
				if ($carrier_id === false)
					return false;
				$not_in[]   = (int)$carrier_id;
			}

			// Carriers have been saved
			$not_in_carrier = '';
			if (count($not_in) > 0)
				$not_in_carrier = 'AND c.`id_carrier` NOT IN ('.implode(',', $not_in).')';

			// get all EnvoiMoinsCher services (to remove images)
			$image_rmv = array();

			$sql = 'SELECT * FROM `'._DB_PREFIX_.'carrier` AS c
				 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
				 ON c.`emc_services_id_es` = es.`id_es` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_ECONOMIQUE.'"
				 WHERE c.`external_module_name` = "envoimoinscher" AND c.`deleted` = 0 '.$not_in_carrier.'';

			$services_emc = Db::getInstance()->ExecuteS($sql);
			foreach ($services_emc as $service_emc)
				$image_rmv[] = (int)$service_emc['id_carrier'];

			$delete_sql = 'UPDATE `'._DB_PREFIX_.'carrier` AS c
				 INNER JOIN `'._DB_PREFIX_.'emc_services` AS es
				 ON c.`emc_services_id_es` = es.`id_es` AND es.`family_es` = "'.EnvoimoinscherModel::FAM_ECONOMIQUE.'"
				 SET c.`deleted` = 1 WHERE c.`external_module_name` = "envoimoinscher" '.$not_in_carrier.'';

			Db::getInstance()->Execute($delete_sql);

			// remove images too
			foreach ($image_rmv as $image)
				unlink(_PS_IMG_DIR_.'s/'.$image.'.jpg');

			$step = Configuration::get('EMC_USER');
			Configuration::updateValue('EMC_USER', 3);

			Tools::redirectAdmin($this->link.'&EMC_tabs='.($step == '2' ? 'merchant' : 'simple_carriers').'&conf=6');
		}
		else
			return $this->displayError($this->l('Please set the module in config mode'));
	}

	/**
	 * Set Sends configuration
	 * @param  boolean $all If is the full configuration to save
	 * @return mixed       Error message
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

		if (Tools::getValue('pickupDay0') &&
			(Tools::getValue('pickupFrom0') || (isset($_POST['pickupFrom0']) && $_POST['pickupFrom0'] == '0')) &&
			(Tools::getValue('pickupTo0') || (isset($_POST['pickupTo0']) && $_POST['pickupTo0'] == '0')) &&
			Tools::getValue('pickupDay1') &&
			(Tools::getValue('pickupTo1') || (isset($_POST['pickupTo1']) && $_POST['pickupTo1'] == '0')) &&
			(Tools::getValue('pickupFrom1') || (isset($_POST['pickupFrom1']) && $_POST['pickupFrom1'] == '0')))
		{
			// Update CFG
			// News
			Configuration::updateValue('EMC_INDI', Tools::getValue('EMC_indiv'));
			Configuration::updateValue('EMC_MULTIPARCEL', Tools::getValue('EMC_multiparcel'));
			Configuration::updateValue('EMC_WEIGHTMIN', (int)Tools::getValue('EMC_min_weight'));
			Configuration::updateValue('EMC_AVERAGE_WEIGHT', str_replace(',', '.', Tools::getValue('EMC_default_weight')));
			Configuration::updateValue('EMC_ASSU', Tools::getValue('EMC_use_axa'));
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
			return $this->displayError($this->l('Please check your form, some fields are requried'));
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

	public static function getMapByOpe($ope, $city = false, $postalcode = false, $address = false)
	{
		switch ($ope)
		{
			case 'MONR':
				$link = '//www.envoimoinscher.com/choix-relais.html?monrCp='.
								($postalcode ? $postalcode : Configuration::get('EMC_POSTALCODE')).
								'&monrVille='.urlencode(($city ? $city : Configuration::get('EMC_CITY'))).
								'&monrPays=FR&poids=3.0&ope='.$ope.'&noSelect=false&mapDiv=loadMapexpe&list=listexpe&map=expe&isPrestashop=true';
				break;
			case 'SOGP':
				$link = '//www.envoimoinscher.com/choix-relais.html?fcp='.
								($postalcode ? $postalcode : Configuration::get('EMC_POSTALCODE')).
								'&fadr='.urlencode(($address ? $address : Configuration::get('EMC_ADDRESS	'))).
								'&fvil='.urlencode(($city ? $city : Configuration::get('EMC_CITY'))).'&TypeLiv=REL&type=Exp&isPrestashop=true';
				break;
			case 'CHRP':
			default:
				$link = '#';
				break;
		}
		return $link;
	}
}