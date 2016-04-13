<?php
/**
* 2011-2016 Boxtale
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
* @copyright 2011-2016 Boxtale
* @license   http://www.gnu.org/licenses/
*/

class EnvParcelPoint extends EnvWebService
{

    /**
     * Protected array which indicates the possibles root elements in the server reply document.
     * @access protected
     * @var array
     */
    protected $types = array('pickup_point', 'dropoff_point');

    /**
     * Public array with parcel points. It takes pickup_point or dropoff_point as the keys.
     *
     * <samp>
     * Structure :<br>
     * $points['pickup_point'|'dropoff_point'][x] => array(<br>
     * &nbsp;&nbsp;['code']                => data<br>
     * &nbsp;&nbsp;['name']                => data<br>
     * &nbsp;&nbsp;['address']        => data<br>
     * &nbsp;&nbsp;['city']                => data<br>
     * &nbsp;&nbsp;['zipcode']        => data<br>
     * &nbsp;&nbsp;['country']        => data<br>
     * &nbsp;&nbsp;['description'] => data<br>
     * &nbsp;&nbsp;['schedule'][x]    => array(<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['weekday']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['open_am']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['close_am']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['open_pm']        => data<br>
     * &nbsp;&nbsp;&nbsp;&nbsp;['close_pm']        => data<br>
     * &nbsp;&nbsp;)<br>
     * )
     * @access public
     * @var array
     */
    public $points = array();


    /**
     * Public boolean variable which specifies if the public $points variable will contain one
     * or more parcel points.
     * @access public
     * @var array
     */
    public $construct_list = false;

    /**
     * Function load one parcel point.
     * @access public
     * @param $type : Parcel point type to load.
     * @param $code : Parcel point code composed by operator code and point id
     * @param $country : Parcel point country.
     * @return Void
     */
    public function getParcelPoint($type = '', $code = '', $country = 'FR')
    {
        if (in_array($type, $this->types)) {
            $this->setOptions(array('action' => 'api/v1/' . $type . '/' . $code . '/' . $country . '/informations'));
            $this->doSimpleRequest($type);
        } else {
            $this->resp_error = true;
            $this->resp_errors_list[0] = array('code' => 'type_not_correct', 'url' => '');
        }
    }

    /**
     * Function executes parcel point request and prepares the $points array.
     * @access private
     * @return Void
     */
    private function doSimpleRequest($type)
    {
        $source = parent::doRequest();

        /* We make sure there is an XML answer and try to parse it */
        if ($source !== false) {
            parent::parseResponse($source);

            $point = $this->xpath->query('/' . $type)->item(0);
            $point_detail = array(
                'code' => $this->xpath->query('./code', $point)->item(0)->nodeValue,
                'name' => $this->xpath->query('./name', $point)->item(0)->nodeValue,
                'address' => $this->xpath->query('./address', $point)->item(0)->nodeValue,
                'city' => $this->xpath->query('./city', $point)->item(0)->nodeValue,
                'zipcode' => $this->xpath->query('./zipcode', $point)->item(0)->nodeValue,
                'country' => $this->xpath->query('./country', $point)->item(0)->nodeValue,
                'phone' => $this->xpath->query('./phone', $point)->item(0)->nodeValue,
                'description' => $this->xpath->query('./description', $point)->item(0)->nodeValue);

            /* We get open and close informations  */
            $schedule = array();
            foreach ($this->xpath->query('./schedule/day', $point) as $d => $day_node) {
                $childs = $this->xpath->query('*', $day_node);
                foreach ($childs as $child_node) {
                    if ($child_node->nodeName != '#text') {
                        $schedule[$d][$child_node->nodeName] = $child_node->nodeValue;
                    }
                }
            }
            $point_detail['schedule'] = $schedule;

            /* We store the data in the right array (defined by $type) */
            if ($this->construct_list) {
                if (!isset($this->points[$type])) {
                    $this->points[$type] = array();
                }
                $this->points[$type][count($this->points[$type])] = $point_detail;
            } else {
                $this->points[$type] = $point_detail;
            }
        }
    }
}
