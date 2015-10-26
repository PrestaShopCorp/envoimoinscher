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

class EnvoimoinscherHelper
{

    /**
     * Fields used to generate mandatory form
     * @access protected
     * @var array
     */
    protected $fields = array(
        'colis.description' => array(
            'type' => 'input',
            'maxlength' => '255',
            'helper' => ''
        ),
        'colis.valeur' => array(
            'type' => 'input',
            'maxlength' => '10',
            'helper' => ''
        ),

        'civilite' => array(
            'helper' => '',
            'type' => array(
                'select',
                'content' => array(
                    array(
                        'nom' => 'civility',
                        'source' => 'this/getCivilities'
                    )
                )
            )
        ),

        'nom' => array(
            'type' => 'input',
            'maxlength' => '10',
            'helper' => ''
        ),

        'prenom' => array(
            'type' => 'input',
            'maxlength' => '10',
            'helper' => ''
        ),

        'email' => array(
            'type' => 'input',
            'maxlength' => '10',
            'helper' => ''
        ),
        'tel' => array(
            'type' => 'input',
            'maxlength' => '10',
            'helper' => ''
        ),
        'disponibilite.HDE' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getDispo',
                    'params' => 'START'
                )
            )
        ),

        'disponibilite.HLE' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getDispo',
                    'params' => 'END'
                )
            )
        ),
        'retrait.pointrelais' => array(
            'type' => 'input',
            'maxlength' => '15',
            'helper' =>
              '<p class="note">Sélectionnez <a href="#" class="action_module">le code du point de proximité</a></p>'
        ),
        'depot.pointrelais' => array(
            'type' => 'input',
            'maxlength' => '15',
            'helper' =>
              '<p class="note">Sélectionnez <a href="#" class="action_module">le code du point de proximité</a></p>'
        ),
        'envoi.raison' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getReasons'
                )
            )
        ),
        'assurance.emballage' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getInsuranceChoices',
                    'params' => 'assurance.emballage'
                )
            )
        ),
        'assurance.materiau' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getInsuranceChoices',
                    'params' => 'assurance.materiau'
                )
            )
        ),

        'assurance.protection' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getInsuranceChoices',
                    'params' => 'assurance.protection'
                )
            )
        ),
        'assurance.fermeture' => array(
            'type' => 'select',
            'content' => array(
                array(
                    'source' => 'this/getInsuranceChoices',
                    'params' => 'assurance.fermeture'
                )
            )
        )
    );

    /**
     * Keys of options stocked in configuration table.
     * @access proteced
     * @var array
     */
    protected $config_keys = array('EMC_LOGIN', 'EMC_PASS', 'EMC_KEY_TEST', 'EMC_KEY_PROD', 'EMC_ENV',
        'EMC_TYPE', 'EMC_NATURE', 'EMC_CIV', 'EMC_FNAME', 'EMC_LNAME', 'EMC_COMPANY',
        'EMC_ADDRESS', 'EMC_COMPL', 'EMC_POSTALCODE', 'EMC_CITY', 'EMC_TEL', 'EMC_MAIL',
        'EMC_PICKUP', 'EMC_MODE', 'EMC_ORDER', 'EMC_RELAIS_SOGP', 'EMC_RELAIS_MONR',
        'EMC_DISPO_HDE', 'EMC_DISPO_HLE', 'EMC_ANN', 'EMC_ENVO', 'EMC_CMD',
        'EMC_LIV', 'EMC_USER', 'EMC_WEIGHTMIN', 'EMC_DELIVERY_LABEL', 'EMC_AVERAGE_WEIGHT',
        'EMC_CONTENT_AS_DESC', 'EMC_SERVICES', 'EMC_NO_FREESHIP', 'EMC_MULTIPARCEL',
        'EMC_TRACK_MODE', 'EMC_ASSU', 'EMC_INDI', 'EMC_LABEL_DELIVERY_DATE', 'EMC_LAST_CARRIER_UPDATE',
        'EMC_MASS', 'EMC_PICKUP_F1', 'EMC_PICKUP_F2', 'EMC_PICKUP_J1', 'EMC_PICKUP_J2',
        'EMC_PICKUP_T1', 'EMC_PICKUP_T2', 'EMC_PP_CHRP_CHRONORELAIS', 'EMC_PP_MONR_CPOURTOI',
        'EMC_PP_MONR_CPOURTOIEUROPE', 'EMC_PP_SOGP_RELAISCOLIS', 'EMC_SRV_MODE', 'EMC_WRAPPING',
        'EMC_PARTNERSHIP');


    /**
     * SQL tables names of the module
     * @var array
     * @access protected
     */
    protected $tables_names = array(
        'emc_categories',
        'emc_dimensions',
        'emc_documents',
        'emc_operators',
        'emc_operators_categories',
        'emc_orders',
        'emc_orders_errors',
        'emc_orders_parcels',
        'emc_orders_plannings',
        'emc_orders_post',
        'emc_orders_tmp',
        'emc_points',
        'emc_services',
        'emc_tracking',
        'emc_cache',
        'emc_cart_tmp'
    );

    /**
     * Days labels.
     * @var array
     * @access protected
     */
    protected $days = array(1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi',
        6 => 'samedi', 7 => 'dimanche');

    /**
     * Proformas' labels translations.
     * @access protected
     * @var array
     */
    protected $proforma = array('sale' => 'vente', 'repair' => 'réparation', 'return' => 'retour',
        'gift' => 'cadeau, don', 'sample' => 'echantillon, maquette', 'personnal' => 'usage personnel',
        'document' => 'documents inter-entreprises', 'other' => 'autre'
    );

    /**
     * Disponibilites for pickup.
     * @var array
     * @access protected
     */
    protected $pick_dispo = array(
        'START' => array(
            '12:00' => '12:00', '12:15' => '12:15', '12:30' => '12:30', '12:45' => '12:45',
            '13:00' => '13:00', '13:15' => '13:15', '13:30' => '13:30', '13:45' => '13:45',
            '14:00' => '14:00', '14:15' => '14:15', '14:30' => '14:30', '14:45' => '14:45',
            '15:00' => '15:00', '15:15' => '15:15', '15:30' => '15:30', '15:45' => '15:45',
            '16:00' => '16:00', '16:15' => '16:15', '16:30' => '16:30', '16:45' => '16:45',
            '17:00' => '17:00'),
        'END' => array(
            '17:00' => '17:00', '17:15' => '17:15', '17:30' => '17:30', '17:45' => '17:45',
            '18:00' => '18:00', '18:15' => '18:15', '18:30' => '18:30', '18:45' => '18:45',
            '19:00' => '19:00', '19:15' => '19:15', '19:30' => '19:30', '19:45' => '19:45',
            '20:00' => '20:00', '20:15' => '20:15', '20:30' => '20:30', '20:45' => '20:45',
            '21:00' => '21:00')
    );
    /**
     * Array with choices for insurance AXA.
     * @access protected
     * @var array
     */
    protected $insurance_choices = array();

    /**
     * String for module authentification.
     * @access protected
     * @var string
     */
    protected $pass_phrase = 'T+sGKCHeRddqiGb+tot/q2hzGRh5oP3GlB1NEMHEGTw=';

    /**
     * Gets random value to generate the tracking uniq key.
     * @param array $data List of parameters taken to generate the ticket.
     * @return string String of value used in the token.
     */
    public function getValueToToken($data)
    {
        // all 'flat' entries
        $all = count($data);
        $random = rand(0, ($all - 1));
        $key = 0;
        foreach ($data as $item) {
            if ($key == $random) {
                $default = $item;
                if (is_array($default)) {
                    $default = $this->getValueToToken($default);
                }
                break;
            }
            $key++;
        }
        return $default;
    }

    /**
     * Prepares mandatory fields.
     * @access public
     * @param array $field Array with field informations returned by API.
     * @param string $type Type of input field (text or hidden)
     * @param boolean $order_page Determines if we add some supplementary constraints in field
     *                generation (actually used only for <select /> generation).
     * @return array List with HTML code (field key), form label (label key) and helper text (helper key).
     */
    public function prepareMandatory($field, $default, $type = 'text', $order_page = false)
    {
        if (array_key_exists($field['code'], $this->fields)) {
            $spec = $this->fields[$field['code']];

            $uniq_id = rand(0, 3333) . time();
            if ($spec['type'] == 'input' && $type == 'text') {
                $field_form = '<input type="text" name="' . $field['code'] . '" value="' . Tools::safeOutput($default) .
                    '" maxlength="' . $spec['maxlength'] . '" class="input-text" />';
            } elseif ($spec['type'] == 'input' && $type == 'hidden') {
                $field_form =
                  '<input type="hidden" name="' . $field['code'] . '" value="' . Tools::safeOutput($default) . '" />';
            } elseif ($spec['type'] == 'select') {
                $field_form = $this->prepareSelect($field['code'], $spec['content'], $default, $order_page);
            }

            if ($type !== 'hidden' && isset($spec['hidden']) && $spec['hidden'] === true) {
                $field_form = '<input type="hidden" name="' . $field['code'] . '" value="' .
                  Tools::safeOutput($default) . '" maxlength="' . $spec['maxlength'] . '" class="input-text" />';
            }

            $field_html = '<label for="field_' . $uniq_id . '">' . Tools::ucfirst($field['label']) . '</label>';
            return array(
                'label' => $field_html,
                'field' => $field_form,
                'helper' => (isset($spec['helper']) ? $spec['helper'] : ''),
                'code' => $field['code'],
                'type' => $type);
        }
    }

    /**
     * Creates configuration array
     * @param array Array with values to parse.
     * @return array List with new values.
     */
    public function configArray($array)
    {
        $config = array();
        foreach ($this->config_keys as $v) {
            $config[$v] = '';
        }

        foreach ($array as $value) {
            $config[$value['name']] = $value['value'];
        }

        if (!isset($config['EMC_NO_FREESHIP']) || trim($config['EMC_NO_FREESHIP']) == '') {
            $config['EMC_NO_FREESHIP'] = array();
        } else {
            $config['EMC_NO_FREESHIP'] = Tools::jsonDecode($config['EMC_NO_FREESHIP'], true);
        }
        return $config;
    }

    /**
     * Makes a services array which service code as an array key.
     * @param array $services List of services.
     * @return array New list of services.
     */
    public function makeCodeKeys($services)
    {
        $services_list = array();
        foreach ($services as $service) {
            $services_list[$service['emc_operators_code_eo'] . '_' . $service['code_es']] = $service;
        }
        return $services_list;
    }

    /**
     * Public method used to generate schedule of one parcel point.
     * @param array $schedule List of informations about the parcel point.
     * @return array Returns day and hours or an empty array if no informations was given.
     */
    public function setDay($schedule)
    {
        $dispo = array();
        if ($schedule['open_am'] != '' && $schedule['close_am'] != '') {
            $dispo[] = array('from' => $schedule['open_am'], 'to' => $schedule['close_am']);
        }
        if ($schedule['open_pm'] != '' && $schedule['close_pm'] != '') {
            $dispo[] = array('from' => $schedule['open_pm'], 'to' => $schedule['close_pm']);
        }
        if (count($dispo) > 0) {
            return array('day' => $this->days[$schedule['weekday']], 'hours' => $dispo);
        }
        return array('day' => array(), 'hours' => array());
    }

    public function setSchedule($schedule)
    {
        $day = array();
        foreach ($schedule as $sched) {
            $day_arr = $this->setDay($sched);
            if (count($day_arr['hours']) > 0) {
                $from_to = array();
                foreach ((array)$day_arr['hours'] as $hour) {
                    $from_to[] = Tools::safeOutput('de ' . $hour['from'] . ' à ' . $hour['to'] . '');
                }
                $day[] = '<b>' . Tools::safeOutput($day_arr['day']) . '</b> <br />' . implode('<br />', $from_to);
            }
        }
        return $day;
    }

    /**
     * Utilitary method to make html selects.
     * @access public
     * @param string $code Code used to field name.
     * @param array $infos HTML informations to select.
     * @param boolean $order_page Marks if we have to add supplementary constraints.
     * @return string String with HTML.
     */
    public function prepareSelect($code, $infos, $default, $order_page = false)
    {
        $html = null;
        foreach ($infos as $info) {
            $html .= '<select name="' . $code . '' . (isset($info['nom']) ? $info['nom'] : '') . '">';
            // get informations to options
            $source = explode('/', $info['source']);
            if ($source[0] == 'this') {
                $params = isset($info['params']) ? (array)$info['params'] : array();
                $options = $this->$source[1]($params, Tools::safeOutput($order_page));
                foreach ($options as $o => $option) {
                    $selected = '';
                    if ($o == Tools::safeOutput($default)) {
                        $selected = 'selected="selected"';
                    }
                    $html .= '<option value="' . $o . '" ' . $selected . '>&nbsp;' . $option . '&nbsp;</option>';
                }
            }
            $html .= '</select> ' . (isset($info['after']) ? $info['after'] : '') . ' ';
        }
        return $html;
    }

    /**
     * Get reasons for international shippment.
     * @access public
     * @return array Array with reasons.
     */
    public function getReasons()
    {
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/WebService.php');
        require_once(_PS_MODULE_DIR_ . '/envoimoinscher/Env/Quotation.php');
        $cot_cl = new EnvQuotation(array());
        return $cot_cl->getReasons($this->proforma);
    }

    public function getConfigKeys()
    {
        return $this->config_keys;
    }

    public function getTablesNames()
    {
        return $this->tables_names;
    }

    /**
     * Utilitary method to get disponibilites for collection.
     * @access private
     * @param array $param Array with type (start or end).
     * @param boolean $order_page If list is used to sending page.
     * @return array Array with disponibilites.
     */
    public function getDispo($param, $order_page = false)
    {
        if ($order_page) {
            $dispo_list = $this->pick_dispo[$param[0]];
            // avoid situation when pickup time is lower than actual time
            foreach ($dispo_list as $key => $dispo) {
                if (time() > strtotime(date('Y-m-d ' . $dispo . ':00'))) {
                    unset($dispo_list[$key]);
                } else {
                    break;
                }
            }
            return $dispo_list;
        }
        return $this->pick_dispo[$param[0]];
    }

    /**
     * Normalize telephone number (removes all non numerical characters).
     * @access public
     * @param string $tel Number to normalize
     * @return string Telephone number normalized
     */
    public static function normalizeTelephone($tel)
    {
        $tel = Tools::strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $tel));
        $tel = preg_replace('/[^0-9]/', '', $tel);
        return $tel;
    }

    /**
     * Puts new insurance choice.
     * @access public
     * @param string $key Choice's key.
     * @param array $values Choice's values.
     * @return void
     */
    public function putNewInsuranceChoice($key, $values)
    {
        $this->insurance_choices[$key] = $values;
    }

    /**
     * Returns insurance choices
     * @access public
     * @param array $key Choice's key array.
     * @param boolean $order_page If list is used to sending page.
     * @return array List with possible choices
     */
    public function getInsuranceChoices($key, $order_page = false)
    {
        if (isset($this->insurance_choices[$key[0]])) {
            return $this->insurance_choices[$key[0]];
        } elseif ($order_page) {
            return array();
        }
        return array();
    }

    /**
     * Converts g to kg if necessary.
     * @access public
     * @param string $unit Kg or g...
     * @param float $weight Weight to convert.
     * @return float Weight in kg.
     */
    public static function normalizeToKg($unit, $weight)
    {
        switch(Tools::strtolower($unit)) {
            case 'g':
                $weight = $weight / 1000;
                break;

            case 'lb':
                $weight = $weight * 0.45359237;
                break;

            case 'oz':
                $weight = $weight * 0.0283495231;
                break;

            default:
                break;
        }
        return round($weight, 2);
    }

    /**
     * Constructs primary key for emc_cache table.
     * @access public
     * @param $from    : departure address
     * @param $to      : delivery address
     * @param $parcels : parcel configuration
     * @param $params  : additional parameters
     * @param $curlMulti : true : the function will do a multirequest on all activated services,
     * else it will do a simple request
     * @return string Primary key.
     */
    public static function getPricingCode($from, $to, $parcels, $params, $curlMulti)
    {

        $code = $from['societe'];
        $code .= $from['code_postal'];
        $code .= $from['ville'];
        $code .= $from['pays'];
        if (isset($to['societe'])) {
            $code .= $to['societe'];
        }
        $code .= $to['code_postal'];
        $code .= $to['ville'];
        $code .= $to['pays'];
        $code .= serialize($parcels);
        $code .= $params['collecte'];
        $code .= $params['delai'];
        $code .= $params['code_contenu'];
        $code .= $params['valeur'];
        $code .= $curlMulti ? '1' : '0';
        $code .= date('Y-m');

        return "quote_".sha1($code);
    }

    /**
     * Constructs primary key for emc_cache table.
     * @access public
     * @param $cartId : the cart id
     * @return string Primary key.
     */
    public static function getPointCode($cartId)
    {
        return "points_".$cartId;
    }

    /**
     * Constructs primary key for emc_cache table.
     * @access public
     * @param $cartId : the cart id
     * @return string Primary key.
     */
    public static function getDeliveryDateCode($cartId)
    {
        return "delivery_dates_".$cartId;
    }

    /**
     * Constructs primary key for emc_cache table.
     * @access public
     * @param $offers : array of offers
     * @param $cartId : cart id
     * @param $cart_rules_in_cart : cart rules
     * @return string Primary key.
     */
    public static function getOfferProcessedCode($offers, $cartId, $cart_rules_in_cart)
    {
        $code = serialize($offers);
        $code .= $cartId;
        $code .= serialize($cart_rules_in_cart);

        return "offer_processed_".sha1($code);
    }

    /**
     * Get cart rules (only those which use a code).
     * @access public
     * @param $cartId : cart id
     * @param $cart_rules_in_cart : cart rules
     * @return array $cart_rules_in_cart composed of cart rules id.
     */
    public function getCartRules($cartId)
    {
        $result = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart='.$cartId);
        $cart_rules_in_cart = array();
        foreach ($result as $row) {
            $cart_rules_in_cart[] = $row['id_cart_rule'];
        }

        return $cart_rules_in_cart;
    }

    public function setFields($key, $value)
    {
        $old = $this->fields[$key];
        $this->fields[$key] = $value;

        if (is_array($old) && is_array($value)) {
            foreach ($old as $k => $v) {
                if (!isset($this->fields[$key][$k])) {
                    $this->fields[$key][$k] = $v;
                }
            }
        }
    }

    /**
     * Function do an encode 64 bits on a string
     *
     * @access public
     * @param String $string The string to encode
     * @return String : encoded string
     */
    public function encode($string)
    {
        $bytes_encoding = array(
            '000000' => 'A', '000001' => 'B', '000010' => 'C', '000011' => 'D', '000100' => 'E', '000101' => 'F',
            '000110' => 'G', '000111' => 'H', '001000' => 'I', '001001' => 'J', '001010' => 'K', '001011' => 'L',
            '001100' => 'M', '001101' => 'N', '001110' => 'O', '001111' => 'P', '010000' => 'Q', '010001' => 'R',
            '010010' => 'S', '010011' => 'T', '010100' => 'U', '010101' => 'V', '010110' => 'W', '010111' => 'X',
            '011000' => 'Y', '011001' => 'Z', '011010' => 'a', '011011' => 'b', '011100' => 'c', '011101' => 'd',
            '011110' => 'e', '011111' => 'f', '100000' => 'g', '100001' => 'h', '100010' => 'i', '100011' => 'j',
            '100100' => 'k', '100101' => 'l', '100110' => 'm', '100111' => 'n', '101000' => 'o', '101001' => 'p',
            '101010' => 'q', '101011' => 'r', '101100' => 's', '101101' => 't', '101110' => 'u', '101111' => 'v',
            '110000' => 'w', '110001' => 'x', '110010' => 'y', '110011' => 'z', '110100' => '0', '110101' => '1',
            '110110' => '2', '110111' => '3', '111000' => '4', '111001' => '5', '111010' => '6', '111011' => '7',
            '111100' => '8', '111101' => '9', '111110' => '+', '111111' => '/'
        );
        $string_array = str_split($string);
        $byte_array = array();
        $result = '';
        $buff = '';
        $count = 0;
        // string(8) to bytes
        foreach ($string_array as $s) {
            for ($i = 7; $i >= 0; $i--) {
                $byte_array[] = (ord($s) & (1 << $i)) >> $i;
            }
        }
        // bytes to string(6)
        foreach ($byte_array as $b) {
            $buff .= $b;
            $count++;
            if ($count == 6) {
                $result .= $bytes_encoding[$buff];
                $buff = '';
                $count = 0;
            }
        }
        if ($count == 4) {
            $result .= $bytes_encoding[$buff . '00'] . '=';
        } elseif ($count == 2) {
            $result .= $bytes_encoding[$buff . '0000'] . '==';
        }
        return $result;
    }

    /**
     * Function to validate email string
     *
     * @access public
     * @param String $string The email
     * @return boolean
     */
    public function validateEmail($string)
    {
        if (filter_var($string, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return true;
    }

    /**
     * Function to validate alphanumeric string
     *
     * @access public
     * @param String $string The string
     * @return boolean
     */
    public function validateAlpha($string)
    {
        if (ctype_alnum($string) === false) {
            return false;
        }

        return true;
    }

    /**
     * Function to validate phone number
     *
     * @access public
     * @param String $string The number
     * @return boolean
     */
    public function validatePhone($string)
    {
        if (preg_match('/^([+\- \(\)]*[\d])+$/', $string) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Function to encrypt password
     *
     * @access public
     * @param String $string The password
     * @return String
     */
    public function encryptPassword($string)
    {
        $salt = Tools::substr($this->pass_phrase, 0, 16);
        $iv = Tools::substr($this->pass_phrase, 16, 16);

        $key = $this->pbkdf2('sha1', $this->pass_phrase, $salt, 100, 32, true);
        return base64_encode(openssl_encrypt($string, 'aes-128-cbc', $key, true, $iv));
    }

    public function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
        $algorithm = Tools::strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true)) {
            throw new Exception('PBKDF2 ERROR: Invalid hash algorithm.');
        }

        if ($count <= 0 || $key_length <= 0) {
            throw new Exception('PBKDF2 ERROR: Invalid parameters.');
        }

        $hash_length = Tools::strlen(hash($algorithm, '', true));
        $block_count = ceil($key_length / $hash_length);
        for ($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack('N', $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output = '';
            $output .= $xorsum;
            if ($raw_output) {
                return Tools::substr($output, 0, $key_length);
            } else {
                return bin2hex(Tools::substr($output, 0, $key_length));
            }
        }
    }

     /**
     * Simplifies product carriers array to get product carrier IDs
     *
     * @access public
     * @param array $productCarriers
     * @return array $productCarrierIds product carrier ids
     */
    public function getProductCarrierIds($productCarriers)
    {
        if (empty($productCarriers) || !is_array($productCarriers)) {
            return false;
        } else {
            $productCarrierIds = array();
            foreach ($productCarriers as $value) {
                if (isset($value['id_carrier'])) {
                    array_push($productCarrierIds, $value['id_carrier']);
                }
            }
            return $productCarrierIds;
        }
    }
}
