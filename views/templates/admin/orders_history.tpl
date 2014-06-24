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
	</style>
	{/literal}
	<script type="text/javascript">
	var token = "{$token|escape:'htmlall'}";
	var labelsToDo = 0;
	var tries = 0;
	var noLabels = new Array(); 
	var reqs = new Array();
	var ordersIds = new Array();
	{foreach from=$orders key=o item=order}
	{if $order.generated_ed == "0" && $order.ref_emc_eor != ""} noLabels.push("{$order.ref_emc_eor}"); ordersIds.push("{$order.idOrder}"); labelsToDo++; {/if}
	{/foreach}
	var notChecked = new Array();
	var allElements = new Array();
	notChecked[1] = 0;
	allElements[1] = {$allOrders|escape:'htmlall'}
	</script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/checkboxes.js"></script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/orders.js"></script>
	<link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />

	{include file="$submenuTemplate" var=$actual}


	{if $errorLabels == 1}
	<div class="conf">Veuillez sélectionner le(s) bordereau(x) à télécharger.</div>
	{/if}
	<p>Les commandes ci-dessous sont celles dont vous avez déjà déclenché l'ordre d'envoi. Pour télécharger les bordereaux de plusieurs de vos envois, cochez les cases situées en-dessous des numéros de commande puis cliquez sur "Télécharger les bordereaux</p>


	{* include pager template *}
	{include file="$pagerTemplate" var=$pager}
	{* include pager template *}

	<form method="post" target="_blank" action="index.php?controller=AdminEnvoiMoinsCher&option=download&token={$token|escape:'htmlall'}">
		<p style="text-align:right"><input type="submit" class="button" id="send1" name="sendValue" value="Télécharger les bordereaux" /></a></b></p><br />
		{include file="$ordersTableTemplate" id="1" orders=$orders tokenOrder=$tokenOrder ordersTodo=$orders type="history"}
		<br />
		<p style="text-align:right"><input type="submit" class="button" id="send2" name="sendValue" value="Télécharger les bordereaux" /></a></b></p>
	</form>
	{* include pager template *}
	{include file="$pagerTemplate" var=$pager}
	{* include pager template *}