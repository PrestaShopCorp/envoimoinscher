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

require_once(realpath(dirname(__FILE__).'/../../config/config.inc.php'));
require_once(realpath(dirname(__FILE__).'/../../init.php'));

require_once(realpath(dirname(__FILE__).'/Env/WebService.php'));
require_once(realpath(dirname(__FILE__).'/Env/ParcelPoint.php'));
require_once(realpath(dirname(__FILE__).'/envoimoinscher.php'));
require_once(realpath(dirname(__FILE__).'/EnvoimoinscherHelper.php'));

$helper = new EnvoimoinscherHelper;
$carrier = (int)$_POST['carrier'];
$address_id = (int)$_POST['addressId'];
$env_cl = new Envoimoinscher;
$config = $helper->configArray(Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'configuration
	 WHERE name LIKE "EMC_%"'));
$poi_cl = new EnvParcelPoint(array(
	'user' => $config['EMC_LOGIN'],
	'pass' => $config['EMC_PASS'],
	'key' => $config['EMC_KEY']));
$poi_cl->setPlatformParams($env_cl->ws_name, _PS_VERSION_, $env_cl->version);
$poi_cl->setEnv(strtolower($config['EMC_ENV']));

$poi_cl->construct_list = true;
foreach (explode(',', $_POST['points']) as $point)
	if (ctype_alnum($_POST['country']))
		$poi_cl->getParcelPoint('dropoff_point', $point, $_POST['country']);

$i = 0;
$addresses = array();
$infos = array();
$names = array();
$ids = array();
foreach ($poi_cl->points['dropoff_point'] as $p => $point)
{
	if ($point['name'] != '')
	{
		$is_checked = false;
		if ($point['code'] == $_POST['pointValue']) $is_checked = true;

		$select_pr_js_method = 'selectPr(\''.$point['code'].'\', \''.(int)$_POST['carrier'].'\', \''.$address_id.'\');';
		echo '<li style="list-style:none;">';
		echo '<input type="radio" id="point'.$carrier.$point['code'].$address_id.'" name="point'.
					$carrier.$_POST['ope'].$address_id.'" value="'.$point['code'].'" '.($is_checked?'checked="checked"':'').
					' onclick="'.$select_pr_js_method.'" class="point'.$carrier.$address_id.'" />';
		echo '<b>'.$point['name'].'</b><br /><small>('.$point['address'].', '.$point['zipcode'].' '.$point['city'].')</small></li>';

		if ($is_checked)
		{
?>
		<script type="text/javascript">
		$(document).ready(function ()
		{
		<?php echo $select_pr_js_method;?>
		});
		</script>
<?php 
		}
		$addresses[] = $point['address'].', '.$point['zipcode'].' '.$point['city'];
		$day = $helper->setSchedule($point['schedule']);
		$infos[] = implode('<br />', $day);
		$names[] = $point['name'];
		$ids[] = $point['code'];
	}
	$i++;
}
if ($i == 0)
	die('noPoint');

echo '<input type="hidden" name="parcelPoints'.$carrier.$_POST['ope'].$address_id.
		'" id="parcelPoints'.$carrier.$_POST['ope'].$address_id.'" value="'.implode('|', $addresses).
		'" /><input type="hidden" name="parcelInfos'.$carrier.$_POST['ope'].$address_id.
		'" id="parcelInfos'.$carrier.$_POST['ope'].$address_id.'" value="'.implode('|', $infos).
		'" /><input type="hidden" name="parcelNames'.$carrier.$_POST['ope'].$address_id.
		'" id="parcelNames'.$carrier.$_POST['ope'].$address_id.'" value="'.implode('|', $names).
		'" /><input type="hidden" name="parcelIds'.$carrier.$_POST['ope'].$address_id.
		'" id="parcelIds'.$carrier.$_POST['ope'].$address_id.'" value="'.implode('|', $ids).
		'" /><input type="hidden" name="counter'.$carrier.$_POST['ope'].$address_id.'" id="counter'.$carrier.$_POST['ope'].$address_id.'" value="0" />';
?>


<!--[if IE]>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var marker = new google.maps.Marker(); 
var infowindow = new google.maps.InfoWindow();
</script>
<![endif]-->

<?php
die();
?>