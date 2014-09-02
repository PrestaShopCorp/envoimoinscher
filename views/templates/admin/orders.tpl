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
	var notChecked = new Array();
	var allElements = new Array();
	notChecked[1] = 0;
	allElements[1] = {$ordersEmcCount|escape:'htmlall'};
	notChecked[2] = 0;
	allElements[2] = {$ordersNoEmcCount|escape:'htmlall'};
	notChecked[3] = 0;
	allElements[3] = {$ordersErrorsCount|escape:'htmlall'};
	var ordersTodo = {$ordersTodo|escape:'htmlall'};
	</script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/checkboxes.js"></script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/ordersSend.js"></script>
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
		{if $showEmcTable}
			<h2>{l s='EMC carrier order selected' mod='envoimoinscher'}</h2>
			<p>{l s='list EMC carrier orders pending help' mod='envoimoinscher'}</p>
			<form id="orderDo1" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}">
				<div>
					<div class="blockButtons {if $ordersTodo > 0}hidden{/if}">{include file="$ordersSendTop"}</div>
					{include file="$ordersTableTemplate" id="1" orders=$ordersEmc tokenOrder=$tokenOrder type="with"}
					<input type="hidden" name="type" value="withEmc" />
					<input type="hidden" name="typeDb" value="1" />
					<!--<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>-->
				</div>
			</form>
			<br />
			{include file="$pagerTemplate" pager=$pager_emc}
			<br />
		{else}
			<h2>{l s='EMC carrier order selected' mod='envoimoinscher'}</h2>
			<p>{l s='no EMC order to ship' mod='envoimoinscher'}</p> 
		{/if}
		</div>

		{if $showOthersTable}
		<div class="panel">
			<h2>{l s='order without EMC carrier' mod='envoimoinscher'}</h2>
			<p>{l s='list no EMC carrier orders pending help' mod='envoimoinscher'}</p>
			<form id="orderDo2" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}"><div>
				<div class="blockButtons {if $ordersTodo > 0}hidden{/if}">{include file="$ordersSendTop"}</div>
				{include file="$ordersTableTemplate" id="2" orders=$ordersOthers tokenOrder=$tokenOrder type="without"}
				<input type="hidden" name="type" value="withoutEmc" />
				<input type="hidden" name="typeDb" value="2" />
				<!--<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>-->
			</div></form>
			<br />
			{include file="$pagerTemplate" pager=$pager_others}
			<br />
		</div>
		{/if}

		{if $showErrorsTable}
		<div class="panel">
			<h2 id="errorsTable">{l s='invalid or uncomplete order' mod='envoimoinscher'}</h2>
			<p>{l s='list orders where :' mod='envoimoinscher'}
				<br />{l s='list order list 1' mod='envoimoinscher'}
				<br />{l s='list order list 2' mod='envoimoinscher'}
				<br /><br />{l s='list order help' mod='envoimoinscher'}
			</p>
			<form id="orderDo3" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}"><div>
				<div class="blockButtons {if $ordersTodo > 0}hidden{/if}">{include file="$ordersSendTop"}</div>
				{include file="$ordersTableTemplate" id="3" orders=$ordersErrors tokenOrder=$tokenOrder type="error"}
				<input type="hidden" name="type" value="errors" />
				<input type="hidden" name="typeDb" value="3" />
				<!--<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>-->
			</div></form>
			<br />
			{include file="$pagerTemplate" pager=$pager_error}
			<br />
		</div>
		{/if}
	
	</div>