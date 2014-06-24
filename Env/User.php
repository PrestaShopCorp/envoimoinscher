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

class EnvUser extends EnvWebService
{

	/**
	 * Array with user configuration informations. Actually we put only email informations.
	 * @access public
	 * @var array
	 */
	public $user_configuration = array('emails' => array());

	/**
	 * Gets information about e-mail configuration for logged user.
	 * @access public
	 * @return Void
	 */
	public function getEmailConfiguration()
	{
		$this->setOptions(array('action' => '/api/v1/emails_configuration'));
		$this->setEmailConfiguration();
	}

	/**
	 * Posts new informations about e-mail configuration for logged user.
	 * Accepted keys are : label, notification, bill. If you want to remove the e-mail sending
	 * for one of these keys, you must put into it an empty string like "".
	 * @access public
	 * @param Array $params Params with new e-mail configuration
	 * @return Void
	 */
	public function postEmailConfiguration($params)
	{
		$this->setOptions(array('action' => '/api/v1/emails_configuration'));
		$this->param = $params;
		$this->setPost();
		$this->setEmailConfiguration();
	}

	/**
	 * Parses API response and puts the values into e-mail configuration array.
	 * @access private
	 * @return Void
	 */
	private function setEmailConfiguration()
	{
		$node_name = 'nodeName';
		$node_value = 'nodeValue';
		$source = parent::doRequest();
		if ($source !== false)
		{
			parent::parseResponse($source);
			foreach ($this->xpath->evaluate('/user/mails')->item(0)->childNodes as $config_line)
			{
				if (!($config_line instanceof DOMText))
					$this->user_configuration['emails'][$config_line->$node_name] = $config_line->$node_value;
			}
		}
	}
}

?>