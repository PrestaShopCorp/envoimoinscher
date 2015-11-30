<?php
/**
* 2011-2015 Boxtale
*
* NOTICE OF LICENSE
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* @author    Boxtale EnvoiMoinsCher <informationapi@boxtale.com>
* @copyright 2011-2015 Boxtale
* @license   http://www.gnu.org/licenses/
*/

class EnvQuotation extends EnvWebService
{

    /**
     * Public variable represents offers array.
     * <samp>
     * Structure :<br>
     * $offers[x]                    => array(<br>
     * &nbsp;&nbsp;['mode']                        => data<br>
     * &nbsp;&nbsp;['url']                        => data<br>
     * &nbsp;&nbsp;['operator']                => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                    => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['logo']                        => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['service']                => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                    => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['price']                    => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['currency']                => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['tax-exclusive']    => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['tax-inclusive']        => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['collection']            => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['type']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['date']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                        => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['delivery']                => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['type']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['date']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                        => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['characteristics'] => data<br>
     * &nbsp;&nbsp;['alert']                    => data<br>
     * &nbsp;&nbsp;['mandatory']            => array([...])<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $offers = array();

    /**
     * Public array containing order informations like order number, order date...
     * <samp>
     * Structure :<br>
     * &nbsp;&nbsp;$orders[x]                        => array(<br>
     * &nbsp;&nbsp;['ref']                            => data<br>
     * &nbsp;&nbsp;['date']                            => data<br>
     * &nbsp;&nbsp;['url']                            => data<br>
     * &nbsp;&nbsp;['mode']                            => data<br>
     * &nbsp;&nbsp;['offer']['operator']    => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['logo']                            => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['service']                    => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                        => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['price']                        => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['currency']                    => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['tax-exclusive']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['tax-inclusive']            => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['collection']                => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['type_label']                => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['date']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['time']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                            => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['delivery']                    => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['code']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['type_label']                => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['date']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['time']                            => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['label']                            => data<br>
     * &nbsp;&nbsp;)<br>
     * &nbsp;&nbsp;['proforma']                        => data<br>
     * &nbsp;&nbsp;['alert'][x]                        => data<br>
     * &nbsp;&nbsp;['chars'][x]                        => data<br>
     * &nbsp;&nbsp;['labels'][x]                        => data<br>
     * )
     * </samp>
     * @access public
     * @var array
     */
    public $order = array();

    /**
     * Protected variable with pallet dimensions accepted by EnvoiMoinsCher.com. The dimensions are given
     * in format 'length cm x width cm'. They are sorted from the longest to the shortest.
     * To pass a correct pallet values, use the $palletDimss' key in your 'pallet' parameter.
     * <samp>
     * Example : <br>
     * $quot_info = array(<br>
     * &nbsp;&nbsp;'collecte_date' => '2015-04-29', <br>
     * &nbsp;&nbsp;'delay' => 'aucun',  <br>
     * &nbsp;&nbsp;'content_code' => 10120,<br>
     * &nbsp;&nbsp;<b>'pallet' => 130110</b><br>
     * );<br>
     * $this->makeOrder($quot_info, true);
     * </samp>
     * @access protected
     * @var array
     */
    protected $pallet_dims = array(
        130110 => '130x110',
        122102 => '122x102',
        120120 => '120x120',
        120100 => '120x100',
        12080 => '120x80',
        114114 => '114x114',
        11476 => '114x76',
        110110 => '110x110',
        107107 => '107x107',
        8060 => '80x60');

    /**
     * Protected variable with shipment reasons. It is used to generate proforma invoice.
     * <samp>
     * Example :
     * $quot_info = array(<br>
     * &nbsp;&nbsp;'collecte_date' => '2015-04-29', <br>
     * &nbsp;&nbsp;'delay' => 'aucun', <br>
     * &nbsp;&nbsp;'content_code' => 10120,<br>
     * &nbsp;&nbsp;'operator' => 'UPSE', <br>
     * &nbsp;&nbsp;<b>'reason' => 'repair'</b><br>
     * );<br>
     * $this->makeOrder($quot_info, true);
     * </samp>
     * @access protected
     * @var array
     */
    protected $ship_reasons = array(
        'sale' => 'sale',
        'repair' => 'repr',
        'return' => 'rtrn',
        'gift' => 'gift',
        'sample' => 'smpl',
        'personnal' => 'prsu',
        'document' => 'icdt',
        'other' => 'othr');

    /**
     * Public setter used to pass proforma parameters into the api request.
     * You must pass a multidimentional array, even for one line.
     * The array keys must start with 1, not with 0.
     * Exemple :
     * $this->setProforma(array(1 => array('description_en' => 'english description for this item',
     * 'description_fr' => 'la description française pour ce produit', 'origine' => 'FR',
     * 'number' => 2, 'value' => 500)));
     * The sense of keys in the proforma array :
     *  - description_en => description of your item in English
     *  - description_fr => description of your item in French
     *  - origine => origin of your item (you can put EEE four every product which comes
     *    from EEA (European Economic Area))
     *  - number => quantity of items which you send
     *  - value => unitary value of one item
     * @access public
     * @param Array $data Array with proforma informations.
     * @return Void
     */
    public function setProforma($data)
    {
        foreach ($data as $key => $value) {
            // we ignore proforma with an incorrect quantity value
            if (((!isset($value['number']) || $value['number'] <= 0)
                    && (!isset($value['nombre']) || $value['nombre'] <= 0))
                || isset($value['number']) && isset($value['nombre'])
            ) {
                continue;
            }
            foreach ($value as $line_key => $line_value) {
                $this->param['proforma_' . $key . '.' . $line_key] = $line_value;
            }
        }
    }

    /**
     * Function which sets informations about package.
     * Please note that if you send the pallet cotation, you can't indicate the dimensions like for
     * other objects. In this case, you must pass the key from $pallet_dims protected variable. If the key
     * is not passed, the request will return an empty result.
     * @access public
     * @param String $type Type : pli, colis, encombrant, palette.
     * @param Array $data Array with package informations : weight, length, width and height.
     * @return Void
     */
    public function setType($type, $dimensions)
    {
        foreach ($dimensions as $d => $data) {
            $this->param[$type . '_' . $d . '.poids'] = $data['poids'];
            if ($type == 'palette') {
                $pallet_dim = explode('x', $this->pallet_dims[$data['palletDims']]);
                $data[$type . '_' . $d . '.longueur'] = (int)$pallet_dim[0];
                $data[$type . '_' . $d . '.largeur'] = (int)$pallet_dim[1];
            }
            $this->param[$type . '_' . $d . '.longueur'] = $data['longueur'];
            $this->param[$type . '_' . $d . '.largeur'] = $data['largeur'];
            if ($type != 'pli') {
                $this->param[$type . '_' . $d . '.hauteur'] = $data['hauteur'];
            }
        }
    }

    /**
     * Public function which sets shipper and recipient objects.
     * @access public
     * @param String $type Person type (shipper or recipient).
     * @param Array $data Array with person informations.
     * @return Void
     */
    public function setPerson($type, $data)
    {
        foreach ($data as $key => $value) {
            $this->param[$type . '.' . $key] = $value;
        }
    }

    /**
     * Public function which receives the quotation.
     * @access public
     * @param Array $data Array with quotation demand informations (date, type, delay and insurance value).
     * @return true if request was executed correctly, false if not
     */
    public function getQuotation($quot_info)
    {
        $this->param = array_merge($this->param, $quot_info);
        $this->setGetParams(array());
        $this->setOptions(array('action' => 'api/v1/cotation'));

        return $this->doSimpleRequest();
    }

    /**
     * Public function setting array of params for curl multi request before getting quotation
     * @access public
     * @return void
     */
    public function setParamMulti($quot_info_multi)
    {
        $this->param_multi[] = array_merge($this->param, $quot_info_multi);
    }

    /**
     * Public function which receives the quotation for curl  multi request.
     * @access public
     * @return true if request was executed correctly, false if not
     */
    public function getQuotationMulti()
    {
        $this->setGetParamsMulti(array());
        $this->setOptionsMulti(array('action' => 'api/v1/cotation'));
        return $this->doSimpleRequestMulti();
    }

    /**
     * Function which gets quotation details.
     * @access private
     * @return false if server response isn't correct; true if it is
     */
    private function doSimpleRequest()
    {
        $source = parent::doRequest();

        /* We make sure there is an XML answer and try to parse it */
        if ($source !== false) {
            parent::parseResponse($source);
            return (count($this->resp_errors_list) == 0);
        }
        return false;
    }

    /**
     * Function which gets quotation details for curlmulti request.
     * @access private
     * @return false if server response isn't correct; true if it is
     */
    private function doSimpleRequestMulti()
    {
        $source = parent::doRequestMulti();

        /* We make sure there is an XML answer and try to parse it */
        if ($source !== false) {
            parent::parseResponseMulti($source);
            return (count($this->resp_errors_list) == 0);
        }
        return false;
    }


    /**
     * Function load all offers for curl multi request calling getOffers() function.
     * @access public
     * @return Void
     */
    public function getOffersMulti()
    {
        $i = 0;
        foreach ($this->xpath as $xpath) {
            $this->getOffers(false, $xpath, $i);
            $i++;
        }
    }

    /**
     * Function load all offers
     * @access public
     * @param bool $only_com If true, we have to get only offers in the 'order' mode.
     * @return Void
     */
    public function getOffers($only_com = false, $multi = false, $i = 0)
    {
        if ($multi) {
            $xpath = $multi;
        } else {
            $xpath = $this->xpath;
        }

        $offers = $xpath->query('/cotation/shipment/offer');

        foreach ($offers as $o => $offer) {
            $offer_mode = $xpath->query('./mode', $offer)->item(0)->nodeValue;
            if (!$only_com || ($only_com && $offer_mode == 'COM')) {
                // Mandatory informations - you must fill it up when you want to order this offer
                $informations = $xpath->query('./mandatory_informations/parameter', $offer);
                $mand_infos = array();
                foreach ($informations as $mandatory) {
                    $arr_key = $xpath->query('./code', $mandatory)->item(0)->nodeValue;
                    $mand_infos[$arr_key] = array();
                    $mandatory_childs = $xpath->query('*', $mandatory);
                    foreach ($mandatory_childs as $mandatory_child) {
                        $mand_infos[$arr_key][$mandatory_child->nodeName] = trim($mandatory_child->nodeValue);
                        if ($mandatory_child->nodeName == 'type') {
                            $nodes = $xpath->query('*', $mandatory_child);
                            foreach ($nodes as $node) {
                                if ($node->nodeName == 'enum') {
                                    $mand_infos[$arr_key][$mandatory_child->nodeName] = 'enum';
                                    $mand_infos[$arr_key]['array'] = array();
                                    $childs = $xpath->query('*', $node);
                                    foreach ($childs as $child) {
                                        if (trim($child->nodeValue) != '') {
                                            $mand_infos[$arr_key]['array'][] = $child->nodeValue;
                                        }
                                    }
                                } else {
                                    $mand_infos[$arr_key][$mandatory_child->nodeName] = $node->nodeName;
                                }
                            }
                        }
                    }
                    unset($mand_infos[$arr_key]['#text']);
                }
                // options
                $options_xpath = $xpath->query('./options/option', $offer);
                $options = array();
                foreach ($options_xpath as $option) {
                    $code_option = $xpath->query('./code', $option)->item(0)->nodeValue;
                    //$s = $o_key + 1;
                    $options[$code_option] = array(
                        'name' => $xpath->query('./name', $option)->item(0)->nodeValue,
                        'parameters' => array());
                    $parameters = $xpath->query('./parameter', $option);
                    foreach ($parameters as $parameter) {
                        $param_code = $xpath->query('./code', $parameter)->item(0);
                        $param_label = $xpath->query('./label', $parameter)->item(0);
                        $param_type = $xpath->query('./type', $parameter)->item(0);
                        $options[$code_option]['parameters'][$param_code->nodeValue] = array(
                            'code' => $param_code->nodeValue,
                            'label' => $param_label->nodeValue,
                            'values' => array());
                        if (trim($param_type->nodeValue) != '') {
                            $values = array();
                            foreach ($param_type->getElementsByTagName('enum')->item(0)->childNodes as $param_option) {
                                if (trim($param_option->nodeValue) != '') {
                                    $values[$param_option->nodeValue] = $param_option->nodeValue;
                                }
                            }
                            $options[$code_option]['parameters'][$param_code->nodeValue]['values'] = $values;
                        }
                    }
                }

                // characteristics generation
                $charact_detail = $xpath->evaluate('./characteristics', $offer)->item(0)->childNodes;
                $charact_array = array();
                foreach ($charact_detail as $c => $char) {
                    if (trim($char->nodeValue) != '') {
                        $charact_array[$c] = $char->nodeValue;
                    }
                }

                $alert = '';
                $alert_node = $xpath->query('./alert', $offer)->item(0);
                if (!empty($alert_node)) {
                    $alert = $alert_node->nodeValue;
                } else {
                    $alert = '';
                }

                $this->offers[$o + $i] = array(
                    'mode' => $offer_mode,
                    'url' => $xpath->query('./url', $offer)->item(0)->nodeValue,
                    'operator' => array(
                        'code' => $xpath->query('./operator/code', $offer)->item(0)->nodeValue,
                        'label' => $xpath->query('./operator/label', $offer)->item(0)->nodeValue,
                        'logo' => $xpath->query('./operator/logo', $offer)->item(0)->nodeValue),
                    'service' => array(
                        'code' => $xpath->query('./service/code', $offer)->item(0)->nodeValue,
                        'label' => $xpath->query('./service/label', $offer)->item(0)->nodeValue),
                    'price' => array(
                        'currency' => $xpath->query('./price/currency', $offer)->item(0)->nodeValue,
                        'tax-exclusive' => $xpath->query('./price/tax-exclusive', $offer)->item(0)->nodeValue,
                        'tax-inclusive' => $xpath->query('./price/tax-inclusive', $offer)->item(0)->nodeValue),
                    'collection' => array(
                        'type' => $xpath->query('./collection/type/code', $offer)->item(0)->nodeValue,
                        'date' => $xpath->query('./collection/date', $offer)->item(0)->nodeValue,
                        'label' => $xpath->query('./collection/type/label', $offer)->item(0)->nodeValue),
                    'delivery' => array(
                        'type' => $xpath->query('./delivery/type/code', $offer)->item(0)->nodeValue,
                        'date' => $xpath->query('./delivery/date', $offer)->item(0)->nodeValue,
                        'label' => $xpath->query('./delivery/type/label', $offer)->item(0)->nodeValue),
                    'characteristics' => $charact_array,
                    'alert' => $alert,
                    'mandatory' => $mand_infos,
                    'options' => $options
                );
                // Ajout de l'insurance si elle est retournée
                if ($xpath->evaluate('boolean(./insurance)', $offer)) {
                    $this->offers[$o + $i]['insurance'] = array(
                        'currency' => $xpath->query('./insurance/currency', $offer)->item(0)->nodeValue,
                        'tax-exclusive' => $xpath->query('./insurance/tax-exclusive', $offer)->item(0)->nodeValue,
                        'tax-inclusive' => $xpath->query('./insurance/tax-inclusive', $offer)->item(0)->nodeValue);
                    $this->offers[$o + $i]['hasInsurance'] = true;
                } else {
                    $this->offers[$o + $i]['hasInsurance'] = false;
                }
            }
        }
    }

    /**
     * Get order informations about collection, delivery, offer, price, service, operator, alerts
     * and characteristics.
     * @access private
     * @return Void
     */
    private function getOrderInfos()
    {
        $shipment = $this->xpath->query('/order/shipment')->item(0);
        $offer = $this->xpath->query('./offer', $shipment)->item(0);
        $this->order['url'] = $this->xpath->query('./url', $offer)->item(0)->nodeValue;
        $this->order['mode'] = $this->xpath->query('./mode', $offer)->item(0)->nodeValue;
        $this->order['offer']['operator']['code'] = $this->xpath->query('./operator/code', $offer)->item(0)->nodeValue;
        $this->order['offer']['operator']['label'] =
          $this->xpath->query('./operator/label', $offer)->item(0)->nodeValue;
        $this->order['offer']['operator']['logo'] = $this->xpath->query('./operator/logo', $offer)->item(0)->nodeValue;
        $this->order['service']['code'] = $this->xpath->query('./service/code', $offer)->item(0)->nodeValue;
        $this->order['service']['label'] = $this->xpath->query('./service/label', $offer)->item(0)->nodeValue;
        $this->order['price']['currency'] = $this->xpath->query('./service/code', $offer)->item(0)->nodeValue;
        $this->order['price']['tax-exclusive'] =
          $this->xpath->query('./price/tax-exclusive', $offer)->item(0)->nodeValue;
        $this->order['price']['tax-inclusive'] =
          $this->xpath->query('./price/tax-inclusive', $offer)->item(0)->nodeValue;
        $this->order['collection']['code'] = $this->xpath->query('./collection/type/code', $offer)->item(0)->nodeValue;
        $this->order['collection']['type_label'] =
          $this->xpath->query('./collection/type/label', $offer)->item(0)->nodeValue;
        $this->order['collection']['date'] = $this->xpath->query('./collection/date', $offer)->item(0)->nodeValue;
        $time = $this->xpath->query('./collection/time', $offer)->item(0);
        if ($time) {
            $this->order['collection']['time'] = $time->nodeValue;
        } else {
            $this->order['collection']['time'] = '';
        }
        $this->order['collection']['label'] = $this->xpath->query('./collection/label', $offer)->item(0)->nodeValue;
        $this->order['delivery']['code'] = $this->xpath->query('./delivery/type/code', $offer)->item(0)->nodeValue;
        $this->order['delivery']['type_label'] =
          $this->xpath->query('./delivery/type/label', $offer)->item(0)->nodeValue;
        $this->order['delivery']['date'] = $this->xpath->query('./delivery/date', $offer)->item(0)->nodeValue;
        $time = $this->xpath->query('./delivery/time', $offer)->item(0);
        if ($time) {
            $this->order['delivery']['time'] = $time->nodeValue;
        } else {
            $this->order['delivery']['time'] = '';
        }
        $this->order['delivery']['label'] = $this->xpath->query('./delivery/label', $offer)->item(0)->nodeValue;
        $proforma = $this->xpath->query('./proforma', $shipment)->item(0);
        if ($proforma) {
            $this->order['proforma'] = $proforma->nodeValue;
        } else {
            $this->order['proforma'] = '';
        }
        $this->order['alerts'] = array();
        $alerts_nodes = $this->xpath->query('./alert', $offer);
        foreach ($alerts_nodes as $a => $alert) {
            $this->order['alerts'][$a] = $alert->nodeValue;
        }
        $this->order['chars'] = array();
        $char_nodes = $this->xpath->query('./characteristics/label', $offer);
        foreach ($char_nodes as $c => $char) {
            $this->order['chars'][$c] = $char->nodeValue;
        }
        $this->order['labels'] = array();
        $label_nodes = $this->xpath->query('./labels/label', $shipment);
        foreach ($label_nodes as $l => $label) {
            $this->order['labels'][$l] = trim($label->nodeValue);
        }
    }

    /**
     * Public function which sends order request.
     * If you don't want to pass insurance parameter, you have to make insurance to false
     * in your parameters array ($quot_info). It checks also if you pass insurance parameter
     * which is obligatory to order a transport service.
     *
     * The response should contains a order number composed by 10 numbers, 4 letters, 4
     * number and 2 letters. We use this rule to check if the order was correctly executed
     * by API server.
     * @param $data : Array with order informations (date, type, delay).
     * @param $get_info : Precise if we want to get more informations about order.
     * @return boolean : True if order was passed successfully; false if an error occured.
     * @access public
     */
    public function makeOrder($quot_info, $get_info = false)
    {
        $this->quot_info = $quot_info;
        $this->get_info = $get_info;
        if (isset($quot_info['reason']) && $quot_info['reason']) {
            $quot_info['envoi.raison'] = $this->ship_reasons[$quot_info['reason']];
            unset($quot_info['reason']);
        }
        if (!isset($quot_info['assurance.selected']) || $quot_info['assurance.selected'] == '') {
            $quot_info['assurance.selected'] = false;
        }
        $this->param = array_merge($this->param, $quot_info);
        $this->setOptions(array('action' => 'api/v1/order'));
        $this->setPost();

        if ($this->doSimpleRequest() && !$this->resp_error) {
            // The request is ok, we check the order reference
            $nodes = $this->xpath->query('/order/shipment');
            $reference = $nodes->item(0)->getElementsByTagName('reference')->item(0)->nodeValue;
            if (preg_match('/^[0-9a-zA-Z]{20}$/', $reference)) {
                $this->order['ref'] = $reference;
                $this->order['date'] = date('Y-m-d H:i:s');
                if ($get_info) {
                    $this->getOrderInfos();
                }
                return true;
            }
            return false;
        } else {
            return false;
        }
    }


    /**
     * Public getter of shippment reasons
     * @access public
     * @param Array $translations Array with reasons' translations. You must translate by $this->ship_reasons values,
     * not the keys.
     * @return Array Array with shippment reasons, may by used to pro forma generation.
     */
    public function getReasons($translations)
    {
        $reasons = array();
        if (count($translations) == 0) {
            $translations = $this->ship_reasons;
        }
        foreach ($this->ship_reasons as $r => $reason) {
            $reasons[$reason] = $translations[$r];
        }
        return $reasons;
    }


    /**
     * Method which allowes you to make double order (the same order in two directions : from shipper
     * to recipient and from recipient to shipper). It can be used by some stores for send a test product
     * to customer and receive it back if the customer isn't satisfied.
     * @return boolean True if second order was passed successfully; false if an error occured.
     */
    public function makeDoubleOrder($quot_info = array(), $get_info = false)
    {
        if (count($quot_info) == 0) {
            $quot_info = $this->quot_info;
        } else {
            $quot_info = $this->setNewQuotInfo($quot_info);
        }
        $this->switchPeople();
        $this->makeOrder($quot_info, $get_info);
    }

    /**
     * Person switcher; it switchs shipper to recipient and recipient to shipper.
     * @return Void
     */
    private function switchPeople()
    {
        $local_params = $this->param;
        $old = array('expediteur', 'destinataire', 'tmp_exp', 'tmp_dest');
        $new = array('tmp_exp', 'tmp_dest', 'destinataire', 'expediteur');
        foreach ($local_params as $key => $value) {
            $this->param[str_replace($old, $new, $key)] = $value;
        }
    }

    /**
     * Setter for new request parameters. If a new parameter is defined, it overriddes the old one
     * (for exemple new service, new hour disponibility).
     * @return Array Array containing new quotation informations.
     */
    private function setNewQuotInfo($quot_info)
    {
        $keys = array_keys((array)$this->quot_info);
        foreach ($keys as $q) {
            if (array_key_exists($q, $quot_info)) {
                $this->quot_info[$q] = $quot_info[$q];
            }
        }
        $keys = array_keys($quot_info);
        foreach ($keys as $q) {
            if (!array_key_exists($q, (array)$this->quot_info)) {
                $this->quot_info[$q] = $quot_info[$q];
            }
        }
        return $this->quot_info;
    }

    /**
     * Method which removes old quotation parameters.
     * @return Void
     */
    public function unsetParams($quot_info)
    {
        foreach ($quot_info as $info) {
            unset($this->quot_info[$info]);
            unset($this->param[$info]);
        }
    }
}
