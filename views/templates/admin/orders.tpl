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


	{include file="$submenuTemplate" var=$actual}

	{if $ordersTodo > 0 || $massOrderPassed == 1}
	<fieldset style="margin:20px 0;">
		<div id="okResult" class="conf" style="display:none;"><span></span></div>
		<div id="errorResult" class="error" style="display:none;"><span></span></div>
		{include file="$massTemplate" all=$ordersTodo done=0 token=$token}
	</fieldset>
	{elseif $normalOrderPassed == 1}
	<div class="conf">La commande de livraison a été correctement passée.</div>
	{/if}

	{if $showEmcTable}
	<h2>Commandes avec transporteur EnvoiMoinsCher sélectionné</h2>
	<p>Voici les commandes pour lesquelles votre acheteur a choisi un transporteur EMC à l'étape "frais de port" sur votre boutique en ligne. Le transporteur EMC est déjà défini : vous pouvez expédiez ces commandes dès maintenant sans avoir à les compléter. Mais si vous souhaitez utiliser le multi-colis sur une commande, vous devez l'envoyer manuellement en cliquant sur "Expédier" en dernière colonne. Toute commande traitée sera classée dans l'historique, parfois après rafraîchissement de la page.</p>
	
	<form id="orderDo1" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}"><div>
		<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendTop"}</div>
		{include file="$ordersTableTemplate" id="1" orders=$ordersEmc tokenOrder=$tokenOrder type="with"}
		<input type="hidden" name="type" value="withEmc" />
		<input type="hidden" name="typeDb" value="1" />
		<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>
	</div></form>
	<br /><br />
	{else}
	<p>Pas de commandes EnvoiMoinsCher à expédier.</p> 
	{/if}

	{if $showOthersTable}
	<h2>Commandes sans transporteur EnvoiMoinsCher sélectionné</h2>
	<p>Voici les commandes pour lesquelles votre acheteur a désigné un transporteur autre que ceux proposés par le module EMC ou aucun transporteur. Si vous souhaitez en expédier avec notre module, vous devrez pour chacune d'entre elles sélectionner le transporteur EMC avant déclenchement de l'envoi</p>
	<form id="orderDo2" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}"><div>
		<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendTop"}</div>
		{include file="$ordersTableTemplate" id="2" orders=$ordersOthers tokenOrder=$tokenOrder type="without"}
		<input type="hidden" name="type" value="withoutEmc" />
		<input type="hidden" name="typeDb" value="2" />
		<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>
	</div></form>
	<br /><br />
	{/if}

	{if $showErrorsTable}
	<br /><br />
	<h2 id="errorsTable">Commandes à compléter ou invalides</h2>
	<p>Voici les commandes pour lesquelles il existe :
		<br />- Des informations manquantes : l'offre de transport choisie par votre acheteur n'est plus disponible par exemple
		<br />- Des erreurs de validation : numéro de téléphone du destinataire incorrect, etc.
		<br /><br />Pour pouvoir expédier les commandes, vous devrez les compléter ou les corriger. Pour cela, vous
		passerez automatiquement par l'écran de vérification des informations de chaque envoi. Dans le cas
		où l'offre n'est plus disponible, vous pourrez sélectionner une nouvelle offre de transport qui
		remplacera l'ancienne (n'oubliez pas d'en informer votre client), etc.
	</p>
	<form id="orderDo3" method="post" action="index.php?controller=AdminEnvoiMoinsCher&option=initOrder&token={$token|escape:'htmlall'}"><div>
		<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendTop"}</div>
		{include file="$ordersTableTemplate" id="3" orders=$ordersErrors tokenOrder=$tokenOrder type="error"}
		<input type="hidden" name="type" value="errors" />
		<input type="hidden" name="typeDb" value="3" />
		<div class="blockButtons" style="{if $ordersTodo > 0}display:none;{/if}">{include file="$ordersSendBottom"}</div>
	</div></form>
	{/if}