{**
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
 * @copyright 2007-2015 PrestaShop SA / 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 *}

	{literal}
	<style type="text/css">
	.table tr td {padding: 2px; color: #000000;}
	.table tr.small th {font-size:9px;}
	.selectedRow {background: #dadada;}
	#massStats {background: url(/modules/envoimoinscher/img/ajax-loader.gif) no-repeat 220px 25px;}
	</style>
	{/literal}
	<script type="text/javascript">
	var token = "{$token|escape:'htmlall'}";
	var tries = 0;
	var withCheck = "{$withCheck|escape:'htmlall'}";
	var massOrderPassed = "{$massOrderPassed|escape:'htmlall'}";
	var orderActionUrl = "index.php?controller=AdminEnvoiMoinsCher&option=initOrder&do=1&token={$token|escape:'htmlall'}";
	var orderResultUrl = "index.php?controller=AdminEnvoiMoinsCher&option=initOrder&results=1&token={$token|escape:'htmlall'}";
	var ordersDone = 0;
	var notChecked = 0;
	var allElements = {$ordersCount|escape:'htmlall'};
	var ordersTodo = {$ordersTodo|escape:'htmlall'};
	</script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/checkboxes.js"></script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/ordersSend.js"></script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/ordersFilter.js"></script>
	<link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />

	{if $local_fancybox}
		<link href="{$emcBaseDir|unescape:'html'}/css/jquery.fancybox.css" rel="stylesheet" type="text/css" media="all" />
		<script type="text/javascript" src="{$emcBaseDir|unescape:'html'}/js/jquery.boxfancy.js"></script>
	{/if}
	
	{if $local_bootstrap}
		<link href="{$emcBaseDir|unescape:'html'}/css/back-office-15.css" rel="stylesheet" type="text/css" media="all" />
	{/if}

	<div class="bootstrap">
		{include file="$submenuTemplate" var=$actual}

		{if $ordersTodo > 0 || $massOrderPassed == 1}
		<div class="panel">
			<div id="okResult" class="conf hidden"><span></span></div>
			<div id="errorResult" class="error hidden"><span></span></div>
			{include file="$massTemplate" all=$ordersTodo done=0 token=$token}
		</div>
		{elseif $normalOrderPassed == 1}
		<div class="clearfix alert alert-success conf">
			{l s='order delivery successfully send' mod='envoimoinscher'}
		</div>
		{/if}

		<div class="panel">
			<h2>{l s='Orders pending shipment' mod='envoimoinscher'}</h2>
			<p>{l s='Here is a list of your orders pending shipment:' mod='envoimoinscher'}
				<ul>
					<li>{l s='EnvoiMoinsCher orders: you can send those right way. If you want to use multi-shipping on a command, you must send it manually clicking send (last column). Processed orders will be displayed on the Previous order(s) tab.' mod='envoimoinscher'}</li>
					<li>{l s='Other carriers: If you wish to send some with our plugin you will have to select a EnvoiMoinsCher carrier for every order before sending.' mod='envoimoinscher'}</li>
					<li>{l s='Invalid or incomplete orders: you must complete or correct them to be able to send them. You will be redirected to the information check screen upon sending. If the offer is not available yet, you will be able to select a new offer to replace the previous one (do not forget to tell your customer).' mod='envoimoinscher'}</li>
				</ul>
			</p>
			<form id="orderDo" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}">
				<div>
					{if !isset($filters) || !isset($filters.type_order) || $filters.type_order == "all"}
						{if !isset($filters)}
							{assign "filters" ""}
						{/if}
					{else}
						<div class="blockButtons {if $ordersTodo > 0}hidden{/if}">{include file="$ordersSendTop"}</div>
					{/if}
					{include file="$pagerTemplate" var=$pager}
					{include file="$ordersTableTemplate" orders=$orders tokenOrder=$tokenOrder}
					{include file="$pagerTemplate" var=$pager}
					<!--<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>-->
				</div>
			</form>
		</div>
	
	</div>