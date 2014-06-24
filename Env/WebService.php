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

class EnvWebService
{

	/** 
	 * A public variable which determines the API server host used by curl request.
	 * @access public
	 * @var string
	 */
	public $server = 'https://test.envoimoinscher.com/';

	/** 
	 * API test server host.
	 * @access public
	 * @var string
	 */
	private $server_test = 'https://test.envoimoinscher.com/';

	/** 
	 * API production server host.
	 * @access public
	 * @var string
	 */
	private $server_prod = 'https://www.envoimoinscher.com/';

	/** 
	 * A private variable which stocks options to pass into curl query.
	 * @access private
	 * @var array
	 */
	private $options = array();

	/** 
	 * A private variable with authentication credentials (login, password and api key).
	 * @access private
	 * @var array
	 */
	private $auth = array();

	/** 
	 * A public variable with _POST data sent by curl function.
	 * @access public
	 * @var array
	 */
	public $quot_post = array();

	/** 
	 * A public boolean which indicates if curl query was executed successful.
	 * @access public
	 * @var boolean
	 */
	public $curl_error = false;

	/** 
	 * A public variable with curl error text.
	 * @access public
	 * @var string
	 */
	public $curl_error_text = '';

	/** 
	 * A public variable indicates if response was executed correctly.
	 * @access public
	 * @var boolean
	 */
	public $resp_error = false;

	/** 
	 * A public variable contains error messages.
	 * @access public
	 * @var array
	 */
	public $resp_errors_list = array();

	/** 
	 * A public DOMXPath variable with parsed response.
	 * @access public
	 * @var DOMXPath
	 */
	public $xpath = null;

	/** 
	 * A public variable determines if we have check certificate in function of your request environment.
	 * @access protected
	 * @var array
	 */
	protected $ssl_check = array('peer' => true, 'host' => 2);

	/**
	 * Protected variable with GET parameters.
	 * @access protected
	 * @var string
	 */
	protected $get_params = '';

	/**
	 * Parameters array used by http_query_build.
	 * @access protected
	 * @var array
	 */
	protected $param;

	/** 
	 * Platform used
	 * @access protected
	 * @var string
	 */
	protected $platform = 'library';

	/** 
	 * Platform version
	 * @access protected
	 * @var string
	 */
	protected $platform_version = '';

	/** 
	 * Module version
	 * @access protected
	 * @var string
	 */
	protected $module_version = '1.1.3';

	/** 
	 * Class constructor.
	 * @access public
	 * @param Array $auth Array with authentication credentials.
	 * @return Void
	 */
	public function __construct($auth)
	{
		$this->auth = $auth;
	}

	/** 
	 * Function which executes api request.
	 *
	 * If an error occurs, we close curl call and put error details in $this->errorText variable.
	 * We distinguish two situations with 404 code returned in the response : <br>
	 * &nbsp;&nbsp;1) The API sets 404 code for valid request which doesn't contain any result. The type of response is application/xml.<br>
	 * &nbsp;&nbsp;2) The server sets 404 code too. It does it for resources which don't exist (like every 404 web page).
	 * &nbsp;&nbsp;In this case the responses' type is text/html.<br>
	 *
	 * If the response returns 404 server code, we cancel the operation by setting $result to false,
	 * $resp_error to true and by adding an error message to $resp_errors_list (with http_file_not_found value). 
	 *
	 * In the case of 404 API error code, we don't break the operation. We show error messages in setResponseError().
	 * @access public
	 * @return String
	 */
	public function doRequest()
	{
		$req = curl_init();
		curl_setopt_array($req, $this->options);
		$result = curl_exec($req);
		// You can uncomment this fragment to see the content returned by API
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/return.xml', $result);
		$curl_info = curl_getinfo($req);
		$content_type = explode(';', $curl_info['content_type']);
		if (curl_errno($req) > 0)
		{
			$this->curl_error = true;
			$this->curl_error_text = curl_error($req);
			curl_close($req);
			return false;
		}
		elseif (trim($content_type[0]) == 'text/html' && $curl_info['http_code'] == '404')
		{
			$result = false;
			$this->resp_error = true;
			$i = 0;
			if ($this->construct_list)
				$i = count($this->resp_errors_list);
			$this->resp_errors_list[$i] = array('code' => 'http_file_not_found',
														'url' => $curl_info['url'],
														'message' => 'Votre requête n\'a pas été correctement envoyée. Veuillez vous rassurer qu\'elle
														 questionne le bon serveur (https et non pas http). Si le problème persiste, contactez notre équipe de développement');
		}
		curl_close($req);

		return $result;
	}

	/** 
	 * Request options setter. If prod environment, sets Verisign's certificate.
	 * @access public
	 * @param Array $options The request options.
	 * @return Void
	 */
	public function setOptions($options)
	{
		$this->setSSLProtection();
		$this->options = array(
			CURLOPT_SSL_VERIFYPEER => $this->ssl_check['peer'],
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => $this->ssl_check['host'],
			CURLOPT_URL => $this->server.$options['action'].$this->get_params,
			CURLOPT_HTTPHEADER => array(
				'Authorization: '.base64_encode($this->auth['user'].':'.$this->auth['pass']).'',
				'access_key : '.$this->auth['key'].''),
			CURLOPT_CAINFO => dirname(__FILE__).'/../ca/ca-bundle.crt');
	}

	/** 
	 * It determines if CURL has to check SSL connection or not.
	 * @access private
	 * @return Void
	 */
	private function setSSLProtection()
	{
		if ($this->server != 'https://www.envoimoinscher.com/')
		{
			$this->ssl_check['peer'] = false;
			$this->ssl_check['host'] = 0;
		}
	}

	/** 
	 * Function which sets the post request. 
	 * @access public
	 * @return Void
	 */
	public function setPost()
	{
		$this->param['platform'] = $this->platform;
		$this->param['platform_version'] = $this->platform_version;
		$this->param['module_version'] = $this->module_version;
		$this->options[CURLOPT_POST] = true;
		$this->options[CURLOPT_POSTFIELDS] = http_build_query($this->param);
	}

	/** 
	 * Function sets the get params passed into the request. 
	 * @access public
	 * @return Void
	 */
	public function setGetParams()
	{
		$this->param['platform'] = $this->platform;
		$this->param['platform_version'] = $this->platform_version;
		$this->param['module_version'] = $this->module_version;
		$this->get_params = '?'.http_build_query($this->param);
	}

	/** 
	 * Function parses api server response. 
	 * 
	 * First, it checks if the parsed response doesn't contain <error /> tag. If not, it does nothing.
	 * Otherwise, it makes $resp_error parameter to true, parses the reponse and sets error messages to $resp_errors_list array.
	 * @access public
	 * @param String $document The response returned by API. For use it like a XPath object, we have to parse it with PHPs' DOMDocument class.
	 * @return Void
	 */
	public function parseResponse($document)
	{
		$dom_cl = new DOMDocument();
		$dom_cl->loadXML($document);
		$this->xpath = new DOMXPath($dom_cl);
		if ($this->hasErrors())
			$this->setResponseErrors();
	}

	/** 
	 * Function detects if xml document has error tag.
	 * @access private
	 * @return boolean true if xml document has error tag, false if it hasn't.
	 */
	private function hasErrors()
	{
		if ((int)$this->xpath->evaluate('count(/error)') > 0)
		{
			$this->resp_error = true;
			return true;
		}
		return false;
	}

	/** 
	 * Function sets error messages to $resp_errors_list. 
	 * @access private
	 * @return boolean true if xml document has error tag, false if it hasn't.
	 */
	private function setResponseErrors()
	{
		$errors = $this->xpath->evaluate('/error');
		foreach ($errors as $e => $error)
		{
			$this->resp_errors_list[$e] = array('code' => $this->xpath->evaluate('code', $error)->item(0)->nodeValue
				, 'message' => $this->xpath->evaluate('message', $error)->item(0)->nodeValue);
		}
	}

	/**
	 * Sets environment.
	 * @access public
	 * @param String $env Server's environment : test or prod .
	 * @return Void
	 */
	public function setEnv($env)
	{
		$envs = array('test', 'prod');
		if (in_array($env, $envs))
		{
			$var = 'server_'.strtolower($env);
			$this->server = $this->$var;
		}
	}

	public function setParam($param)
	{
		$this->param = $param;
	}

	public function setPlatformParams($platform, $platform_version, $module_version)
	{
		$this->platform = strtolower($platform);
		$this->platform_version = strtolower($platform_version);
		$this->module_version = strtolower($module_version);
	}
}

?>