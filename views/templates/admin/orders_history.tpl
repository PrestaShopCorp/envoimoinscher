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

	{if $local_fancybox}
		<link href="{$emcBaseDir|unescape:'html'}/css/jquery.fancybox.css" rel="stylesheet" type="text/css" media="all" />
		<script type="text/javascript" src="{$emcBaseDir|unescape:'html'}/js/jquery.boxfancy.js"></script>
	{/if}
	{if $local_bootstrap}
		<link href="{$emcBaseDir|unescape:'html'}/css/back-office-15.css" rel="stylesheet" type="text/css" media="all" />
	{/if}
	{literal}
	<style type="text/css">
	.table tr td {padding: 2px; color: #000000;}
	.table tr.small th {font-size:9px;}
	.selectedRow {background: #dadada;}
	</style>
	{/literal}
	<script type="text/javascript">
	var token = "{$token|escape:'htmlall'}";
	var labelsToDo = 0;
	var tries = 0;
	var noLabels = new Array(); 
	var reqs = new Array();
	var ordersIds = new Array();
	var cartIds = new Array();
	{foreach from=$orders key=o item=order}
	{if $order.id_order|in_array:$noAjaxOrderIds}{else} noLabels.push("{$order.ref_emc_eor}"); ordersIds.push("{$order.idOrder}"); cartIds.push("{$order.id_cart}"); labelsToDo++; {/if}
	{/foreach}
	var notChecked = new Array();
	var allElements = new Array();
	notChecked[1] = 0;
	allElements[1] = {$allOrders|escape:'htmlall'}
	</script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/checkboxes.js"></script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/orders.js"></script>
  <script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/ordersHistoryFilter.js"></script>
  <link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />

	<div class="bootstrap">
		{include file="$submenuTemplate" var=$actual}

		<div class="clearfix"></div>

		{if $errorLabels == 1}
		<div class="alert alert-warning warn">{l s='please select slips to download' mod='envoimoinscher'}</div>
		{/if}


		<div class="panel">
			<p>{l s='list shipments, to download slips : check then clic "download"' mod='envoimoinscher'}</p>
			<form method="post" target="_blank" action="index.php?controller=AdminEnvoiMoinsCher&option=downloadLabels&token={$token|escape:'htmlall'}">
				<p class="text_align_right">
					<input type="submit" class="btn btn-default" id="send1" name="sendValueRemises" value="{l s='Download delivery slips' mod='envoimoinscher'}" />
					<input type="submit" class="btn btn-default" id="send1" name="sendValue" value="{l s='Download shipment notes' mod='envoimoinscher'}" />
				</p>
				{include file="$pagerTemplate" var=$pager}
				{include file="$ordersTableTemplate" id="1" orders=$orders tokenOrder=$tokenOrder ordersTodo=$orders type="history"}
				{include file="$pagerTemplate" var=$pager}
			</form>
		</div>
	</div>