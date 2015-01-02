{**
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
 *}

{foreach from=$points item=point}
	<li class="no-list-style">
		<input type="radio" id="{$point.id|escape:'htmlall'}" name="{$point.input_name|escape:'htmlall'}" value="{$point.code|escape:'htmlall'}"
		 {if $point.checked}checked="checked" {/if}class="{$point.class|escape:'htmlall'}" onclick="{$point.js|unescape:'html'}" />
		<b>{$point.name}</b><br /><small>({$point.address|escape:'htmlall'}, {$point.zipcode|escape:'htmlall'} {$point.city|escape:'htmlall'})</small>
	</li>
	{if $point.checked}
		<script type="text/javascript">
		$(document).ready(function ()
		{
		{$point.js|unescape:'html'}
		});
		</script>
	{/if}
{/foreach}

{foreach from=$inputs item=input}
	<input type="hidden" name="{$input.name|escape:'htmlall'}" id="{$input.id|escape:'htmlall'}" value="{$input.value|unescape:'html'}"/>
{/foreach}

<!--[if IE]>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
var marker = new google.maps.Marker(); 
var infowindow = new google.maps.InfoWindow();
</script>
<![endif]-->