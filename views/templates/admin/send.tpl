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

	<script type="text/javascript">
	{literal}
	var baseWeight = "{/literal}{$weight}{literal}";
	var baseHeight = "{/literal}{$dimensions.height_ed}{literal}";
	var baseLength = "{/literal}{$dimensions.length_ed}{literal}";
	var baseWidth = "{/literal}{$dimensions.width_ed}{literal}";
	var orderId = "{/literal}{$orderId}{literal}";
	var token = "{/literal}{Tools::getValue('token')}{literal}";
	var emcBaseDir = "{/literal}{$moduleBaseDir}{literal}";
	var envUrl = "{/literal}{$envUrl}{literal}";
	{/literal}
	</script>
	<script type="text/javascript" src="{$baseDirCss|escape:'htmlall'}modules/envoimoinscher/js/send.js"></script>
	<link type="text/css" rel="stylesheet" href="{$baseDirCss|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />
	<link type="text/css" rel="stylesheet" href="{$baseDirCss|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />
	{if $local_fancybox}
		<link href="{$emcBaseDir|unescape:'html'}/css/jquery.fancybox.css" rel="stylesheet" type="text/css" media="all" />
		<script type="text/javascript" src="{$emcBaseDir|unescape:'html'}/js/jquery.boxfancy.js"></script>
	{/if}
	{if $alreadyPassed}
	<div class="alert error" style="width:400px;">{l s='shipment already send : Contact EMC for more info' mod='envoimoinscher'}
	</div>
	{else}
	{if isset($showErrorMessage) && $showErrorMessage == 1 && $errorType == "order"}
	<div class="alert error" style="width:400px;">{l s='order shipment failed :' mod='envoimoinscher'}
		{$errorMessage|escape:'htmlall'}
	</div>
	{/if}

	{if $ordersAll > 0}
	<div class="path_bar">{include file="$massTemplate" all=$ordersAll done=$ordersDone token=$token}
		{if $orderTodo > 1}<p><a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$nextOrderId}&option=initOrder&token={$token}&mode=skip&previous={$orderId}" class="action_module">{l s='next order' mod='envoimoinscher'}</a></p>{/if}
	</div>
	{/if}
	<h2>{l s='order number :' mod='envoimoinscher'} {$orderId|escape:'htmlall'}</h2>
	<div class="box-left">
		{if $isFound}
		<p><b>{l s='offer information' mod='envoimoinscher'}</b></p>
		<div id="offerTable">
			{include file="$tableTemplate" var=$offer}
		</div><!-- offerTable -->
		<div id="notFoundOffer"></div><!-- notFoundOffer-->
		<br />
		{elseif !$isFound}
		<div class="alert error" style="width:400px;">
			{if !$isEMCCarrier}
				{l s='order offer not EMC carrier offer : select EMC offer' mod='envoimoinscher'}
			{else}
				{l s='order offer not avaliable : pick another one or try later' mod='envoimoinscher'}
			{/if}
		</div>
		<p><b>{l s='client selected offer :' mod='envoimoinscher'} </b> {$orderInfo.name|escape:'htmlall'}</p>
		<p style="margin-top:20px;"><b>{l s='EMC offers :' mod='envoimoinscher'}</b></p>
		{if isset($showErrorMessage) && $showErrorMessage == 1 && $errorType == "quote"}
		<div class="alert error" style="width:400px;">
			{l s='no EMC offer found : error :' mod='envoimoinscher'}
			{$errorMessage|escape:'htmlall'}
			{if $orderTodo <= 1}
			<p><a href="index.php?controller=AdminEnvoiMoinsCher&option=cancelOrder&token={$token|escape:'htmlall'}" class="action_module">Annuler l'envoi</a></p>
			{/if}
		</div>
		{/if}
		{include file="$notFoundTemplate" var=$isEMCCarrier var=$offersNb var=$offers var=$installedServices}
		<br />
		{/if}

		<p><b>Informations sur le destinataire</b></p>
		<table class="table" cellspacing="0" style="width: 100%;">
			<thead>
				<tr>
					<th>Nom Prénom</th>
					<th>Adresse</th>
					<th>Ville</th>
					<th>E-mail</th>
					<th>Numéro de téléphone</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$deliveryInfo.prenom|escape:'htmlall'} {$deliveryInfo.nom|escape:'htmlall'}</td>
					<td>{$deliveryInfo.adresse|escape:'htmlall'}</td>
					<td>{$deliveryInfo.code_postal|escape:'htmlall'} {$deliveryInfo.ville|escape:'htmlall'}</td>
					<td>{$deliveryInfo.email|escape:'htmlall'}</td>
					<td>{$deliveryInfo.tel|escape:'htmlall'}</td>
				</tr>
			</tbody>
		</table>
		{if ($isEMCCarrier && $isFound) || (!$isEMCCarrier && !$isFound && $offersNb == 0)}<p><b>></b> <a href="#" id="changeDestData" class="action_module">modifier les informations sur le destinataire</a></p>{/if}
	<div style="display:none;position:relative;" id="messageSending" class="box-left">
		<p>Votre expédition est en cours, veuillez patienter.</p>
		<p>Si jamais votre page ne se recharge pas, ou que la commande ne s'envoie pas <u>sans que vous ne receviez une erreur</u>, voici la marche à suivre :</p>
		<ul style="margin-left:10px;">
			<li> - Connectez vous à votre compte sur <a target="_blank" href="//www.envoimoinscher.com">www.envoimoinscher.com</a> et vérifiez que la commande n'a pas été prise en compte sur nos serveurs.</li>
			<li> - Si votre commande a été prise en compte, surtout ne renvoyez pas votre colis, contactez le service client au 01 75 77 37 97 qui vous aidera à régulariser la situation de votre colis sur Prestashop.</li>
		</ul>
	</div>
	
	</div> 
	{if $isFound || (!$isEMCCarrier && !$isFound && $offersNb == 0)}
	<div id="foundBlock" class="box-right">
		<p><b>Informations obligatoires</b></p>
		<form method="post" action="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderId}&option={if !$isEMCCarrier && !$isFound}editAddress{else}command{/if}&token={$token}" id="mandatory_form">
			<table class="table formTable" cellspacing="0" style="width: 100%;">
				{if $isFound}
				{if $multiParcel == 1}
				<tr id="multiParcelRow">
					<th><label for="multiParcel">Multi-colis</label></th>
					<td class="paddingTableTd">
						<input type="text" name="multiParcel" id="multiParcel" value="{$parcelsLength|escape:'htmlall'}" />
						<div id="errorMultiParcel" class="alert error" style="width:160px; margin-top:10px; display:none;"><img src="{$adminImg|escape:'htmlall'}/forbbiden.gif" alt="nok" />Le multi-colis permet d'expédier la commande
							en plusieurs colis au lieu d'un seul : veuillez indiquer le nombre de colis à expédier pour cette
							commande en saisissant un chiffre supérieur ou égal à 2. Si la commande doit être envoyée
							en 1 seul colis, laissez la case vide
						</div>
					</td>
				</tr>
				{foreach from=$parcels key=p item=parcel}
				<tr class="appendRow"><th><label for="parcel{$p|escape:'htmlall'}">Colis #{$p|escape:'htmlall'}</label></th>
					<td class="paddingTableTd"><input type="text" name="parcel[]" id="parcel{$p|escape:'htmlall'}" value="{$parcel.poids|escape:'htmlall'}" onblur="javascript: modifWeight();"  /> kg</td>
				</tr>
				{/foreach}
				{/if}
				<tr>
					<th>
						<label for="weight">Poids total de l'envoi</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="weight" id="weight" value="{$weight|escape:'htmlall'}" class="input-text" /> kg
					</td>
				</tr>
				<tr>
					<th>
						<label for="weight">Largeur totale de l'envoi</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="width" id="width" value="{$dimensions.width_ed|intval}" class="input-text" /> cm
					</td>
				</tr>
				<tr>
					<th>
						<label for="weight">Longueur totale de l'envoi</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="length" id="length" value="{$dimensions.length_ed|intval}" class="input-text" /> cm
					</td>
				</tr>
				<tr>
					<th>
						<label for="weight">Hauteur totale de l'envoi</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="height" id="height" value="{$dimensions.height_ed|intval}" class="input-text" /> cm
					</td>
				</tr>
					{foreach from=$offer.output key=m item=mandatory}
						{if count($mandatory) > 0 && $mandatory.type != "hidden"}
						<tr>
							<th>{$mandatory.label|unescape:'html'}</th>
							<td class="paddingTableTd">{$mandatory.field|unescape:'html'} {$mandatory.helper|unescape:'html'}</td>
						</tr>
						{else} {* Only hidden fields, these values shouldn't be modified *}
							{$mandatory.field|unescape:'html'}
						{/if}
					{/foreach}
				<tr><th>
					<label for="date">Date d'enlèvement</label></th>
					<td class="paddingTableTd"><input type="text" name="collecte" id="collecte" value="{$offer.collection.date|escape:'htmlall'}" class="input-text" />
						<p class="note">format : AAAA-MM-JJ, par exemple 2000-12-26 pour le 26 décembre 2000</p>
					</td></tr>
					{/if}
					<tr class="changeDest {if (!isset($deliveryInfo.phoneAlert) || !$deliveryInfo.phoneAlert) && !$showDstBlock}hidden{/if}"><th>
						<label for="date">Le numéro de téléphone du destinataire</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_tel" id="dest_tel" value="{$deliveryInfo.tel|escape:'htmlall'}" class="input-text" />
							{if isset($deliveryInfo.phoneAlert)}{$deliveryInfo.phoneAlert}<p class="note">! Votre destinataire n'a pas renseigné son numéro de téléphone. Votre numéro de téléphone a été repris.</p>{/if}
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="date">Le prénom du destinataire</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_fname" id="dest_fname" value="{$deliveryInfo.prenom|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="date">Le nom du destinataire</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_lname" id="dest_lname" value="{$deliveryInfo.nom|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="date">L'adresse du destinataire</label></th>
						<td style="padding-left:10px;"><input type="text" name="dest_add" id="dest_add" value="{$deliveryInfo.adresse|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="date">Le code postal du destinataire</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_code" id="dest_code" value="{$deliveryInfo.code_postal|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="date">La ville du destinataire</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_city" id="dest_city" value="{$deliveryInfo.ville|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="date">L'e-mail du destinataire</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_email" id="dest_email" value="{$deliveryInfo.email|escape:'htmlall'}" class="input-text" />
						</td>
					</tr> 
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="comp">Le nom de la société</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_company" id="dest_company" value="{$deliveryInfo.societe|escape:'htmlall'}" class="input-text" />
						</td>
					</tr> 
					{if isset($proforma) && $proforma}
					<tr><th colspan="2"><br /><b>Informations pour la facture proforma, générée par EnvoiMoinsCher.com</b></th></tr>
					{foreach from=$proformaData key=p item=proforma}  
					<tr><th><label for="desc_fr_{$p|escape:'htmlall'}">Description d'objet #{$p|escape:'htmlall'}</label></th>
						<td class="paddingTableTd">
							<p><input type="text" name="desc_fr_{$p|escape:'htmlall'}" id="desc_fr_{$p|escape:'htmlall'}" value="{$proforma.description_fr|escape:'htmlall'}" class="input-text" /> <small>(en français)</small></p>
							<p><input type="text" name="desc_en_{$p|escape:'htmlall'}" id="desc_en_{$p|escape:'htmlall'}" value="{$proforma.description_en|escape:'htmlall'}" class="input-text" /> <small>(en anglais)</small>
								<a href="#" onclick="traduireDescription('{$proforma.description_fr|escape:'htmlall'}');return false;">traduire</a></p> 
							</td>
						</tr>
						{/foreach}
						<input type="hidden" name="proformaSend" id="proformaSend" value="1" />
						{/if}
						{if isset($offer.insurance)}
						<tr><th colspan="2"><br /><b>Informations pour l'assurance AXA</b></th></tr>
						<tr class="appendRow"><th><label for="insurance">Assurer cet envoi</label></th>
							<td class="paddingTableTd"><input type="checkbox" name="insurance" id="insurance" value="1" {if $offer.insurance || $checkAssu}checked="checked"{/if} onclick="javascript: modifInsurance(this);" />
								{if $offer.insurance || $checkAssu}
								<script type="text/javascript">
								$(document).ready(function() {
									modifInsurance($("#insurance"));
								});
								</script>
								{/if}
							</td>
						</tr>
						{foreach from=$offer.insuranceHtml key=m item=mandatory}
						{if count($mandatory) > 0 && $mandatory.type != "hidden"}
						<tr class="assuTd" {if !$offer.insurance}style="display:none;"{/if}><th>{$mandatory.label}
						</th><td class="paddingTableTd">{$mandatory.field|unescape:'html'}
						{$mandatory.helper|unescape:'html'}
					</td>
				</tr>
				{else} {* Only hidden fields, these values shouldn't be modified *}
				{$mandatory.field|escape:'htmlall'}
				{/if}
				{/foreach}
				{/if}
				<tr>
					<td style="text-align:center;" colspan="2">
						<input type="hidden" name="opeCode" id="opeCode" value="{if isset($offer.operator.code)}{$offer.operator.code}{/if}" />
						<input type="hidden" name="dest_country" id="dest_country" value="{$deliveryInfo.pays|escape:'htmlall'}" />
						<input type="hidden" name="exp_pays" id="exp_pays" value="{$shipperInfo.country|escape:'htmlall'}" />
						<input type="hidden" name="exp_cp" id="exp_cp" value="{$shipperInfo.postalcode|escape:'htmlall'}" />
						<input type="hidden" name="exp_city" id="exp_city" value="{$shipperInfo.city|escape:'htmlall'}" />
						<input type="submit" name="send" id="submitForm" value="Expédier" class="button" />
					</td>
				</tr>
			</table>
		</form>
	</div>  
	{/if}
	{/if}
	<div class="clear"></div>