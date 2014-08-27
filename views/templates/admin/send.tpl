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

	<div class="bootstrap">

	{if $alreadyPassed}
	<div class="alert alert-danger ">{l s='shipment already send : Contact EMC for more info' mod='envoimoinscher'}
	</div>
	{else}
	{if isset($showErrorMessage) && $showErrorMessage == 1 && $errorType == "order"}
	<div class="alert alert-danger ">{l s='order shipment failed :' mod='envoimoinscher'}
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
		<div class="alert alert-danger ">
			{if !$isEMCCarrier}
				{l s='order offer not EMC carrier offer : select EMC offer' mod='envoimoinscher'}
			{else}
				{l s='order offer not avaliable : pick another one or try later' mod='envoimoinscher'}
			{/if}
		</div>
		<p><b>{l s='client selected offer :' mod='envoimoinscher'} </b> {$orderInfo.name|escape:'htmlall'}</p>
		<p class="mt20"><b>{l s='EMC offers :' mod='envoimoinscher'}</b></p>
		{if isset($showErrorMessage) && $showErrorMessage == 1 && $errorType == "quote"}
		<div class="alert alert-danger ">
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

		<p><b>{l s='Recipient Information' mod='envoimoinscher'}</b></p>
		<table class="table fullwidth" cellspacing="0">
			<thead>
				<tr>
					<th>{l s='Name' mod='envoimoinscher'}</th>
					<th>{l s='Address' mod='envoimoinscher'}</th>
					<th>{l s='City' mod='envoimoinscher'}</th>
					<th>{l s='Email' mod='envoimoinscher'}</th>
					<th>{l s='Phone number' mod='envoimoinscher'}</th>
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
		{if ($isEMCCarrier && $isFound) || (!$isEMCCarrier && !$isFound && $offersNb == 0)}<p><b>></b> <a href="#" id="changeDestData" class="action_module">{l s='Change recipient information' mod='envoimoinscher'}</a></p>{/if}
	<div id="messageSending" class="hidden relative box-left">
		<p>{l s='Your shipment is in progress, please wait.' mod='envoimoinscher'}</p>
		<p>{l s='If your page is not charging, or the order is not shipped' mod='envoimoinscher'} <u>{l s='without you receiving an error' mod='envoimoinscher'}</u>, {l s='here are the steps to follow' mod='envoimoinscher'} :</p>
		<ul class="ml10">
			<li> {l s='Connect to your account on' mod='envoimoinscher'} <a target="_blank" href="//www.envoimoinscher.com">www.envoimoinscher.com</a> {l s='and verify that the order has not been taken into account on our servers.' mod='envoimoinscher'}</li>
			<li> {l s='If your order has been taken into account, do not return your order, please contact customer service which will help you to regularize the status of your package on Prestashop.' mod='envoimoinscher'}</li>
		</ul>
	</div>
	
	</div> 
	{if $isFound || (!$isEMCCarrier && !$isFound && $offersNb == 0)}
	<div id="foundBlock" class="box-right">
		<p><b>{l s='Required informations' mod='envoimoinscher'}</b></p>
		<form method="post" action="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderId}&option={if !$isEMCCarrier && !$isFound}editAddress{else}command{/if}&token={$token}" id="mandatory_form">
			<table class="table formTable fullwidth" cellspacing="0">
				{if $isFound}
				{if $multiParcel == 1}
				<tr id="multiParcelRow">
					<th><label for="multiParcel">{l s='Multiparcel' mod='envoimoinscher'}</label></th>
					<td class="paddingTableTd">
						<input type="text" name="multiParcel" id="multiParcel" value="{$parcelsLength|escape:'htmlall'}" />
						<div id="errorMultiParcel" class="alert alert-danger hidden mt10"><img src="{$adminImg|escape:'htmlall'}/forbbiden.gif" alt="nok" />{l s='Multiparcel error explications' mod='envoimoinscher'}
						</div>
					</td>
				</tr>
				{foreach from=$parcels key=p item=parcel}
				<tr class="appendRow"><th><label for="parcel{$p|escape:'htmlall'}">{l s='Package' mod='envoimoinscher'} #{$p|escape:'htmlall'}</label></th>
					<td class="paddingTableTd"><input type="text" name="parcel[]" id="parcel{$p|escape:'htmlall'}" value="{$parcel.poids|escape:'htmlall'}" onblur="javascript: modifWeight();"  /> {l s='kg' mod='envoimoinscher'}</td>
				</tr>
				{/foreach}
				{/if}
				<tr>
					<th>
						<label for="weight">{l s='Total weight of the shipment' mod='envoimoinscher'}</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="weight" id="weight" value="{$weight|escape:'htmlall'}" class="input-text" /> {l s='kg' mod='envoimoinscher'}
					</td>
				</tr>
				<tr>
					<th>
						<label for="width">{l s='Total width of the shipment' mod='envoimoinscher'}</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="width" id="width" value="{$dimensions.width_ed|intval}" class="input-text" /> {l s='cm' mod='envoimoinscher'}
					</td>
				</tr>
				<tr>
					<th>
						<label for="length">{l s='Total length of the shipment' mod='envoimoinscher'}</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="length" id="length" value="{$dimensions.length_ed|intval}" class="input-text" /> {l s='cm' mod='envoimoinscher'}
					</td>
				</tr>
				<tr>
					<th>
						<label for="height">{l s='Total height of the shipment' mod='envoimoinscher'}</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="height" id="height" value="{$dimensions.height_ed|intval}" class="input-text" /> {l s='cm' mod='envoimoinscher'}
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
					<label for="collecte">{l s='Pickup date' mod='envoimoinscher'}</label></th>
					<td class="paddingTableTd"><input type="text" name="collecte" id="collecte" value="{$offer.collection.date|escape:'htmlall'}" class="input-text" />
						<p class="note">{l s='format : AAAA-MM-JJ, e.g. 2000-12-26 for 2000 december 26th' mod='envoimoinscher'}</p>
					</td></tr>
					{/if}
					<tr class="changeDest {if (!isset($deliveryInfo.phoneAlert) || !$deliveryInfo.phoneAlert) && !$showDstBlock}hidden{/if}"><th>
						<label for="dest_tel">{l s='Recipient phone number' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_tel" id="dest_tel" value="{$deliveryInfo.tel|escape:'htmlall'}" class="input-text" />
							{if isset($deliveryInfo.phoneAlert)}{$deliveryInfo.phoneAlert}<p class="note">{l s='Your recipient has not informed his phone number. Your phone number has been taken.' mod='envoimoinscher'}</p>{/if}
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_fname">{l s='Recipient first name' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_fname" id="dest_fname" value="{$deliveryInfo.prenom|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_lname">{l s='Recipient last name' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_lname" id="dest_lname" value="{$deliveryInfo.nom|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_add">{l s='Recipient address' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_add" id="dest_add" value="{$deliveryInfo.adresse|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_code">{l s='Recipient zip code' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_code" id="dest_code" value="{$deliveryInfo.code_postal|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_city">{l s='Recipient city' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_city" id="dest_city" value="{$deliveryInfo.ville|escape:'htmlall'}" class="input-text" />
						</td>
					</tr>
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_email">{l s='Recipient email' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_email" id="dest_email" value="{$deliveryInfo.email|escape:'htmlall'}" class="input-text" />
						</td>
					</tr> 
					<tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
						<label for="dest_company">{l s='Company name' mod='envoimoinscher'}</label></th>
						<td class="paddingTableTd"><input type="text" name="dest_company" id="dest_company" value="{$deliveryInfo.societe|escape:'htmlall'}" class="input-text" />
						</td>
					</tr> 
					{if isset($proforma) && $proforma}
					<tr><th colspan="2"><br /><b>{l s='Information for the proforma invoice generated by EnvoiMoinsCher.com' mod='envoimoinscher'}</b></th></tr>
					{foreach from=$proformaData key=p item=proforma}  
					<tr><th><label for="desc_fr_{$p|escape:'htmlall'}">{l s='Item description' mod='envoimoinscher'} #{$p|escape:'htmlall'}</label></th>
						<td class="paddingTableTd">
							<p><input type="text" name="desc_fr_{$p|escape:'htmlall'}" id="desc_fr_{$p|escape:'htmlall'}" value="{$proforma.description_fr|escape:'htmlall'}" class="input-text" /> <small>{l s='French' mod='envoimoinscher'}</small></p>
							<p><input type="text" name="desc_en_{$p|escape:'htmlall'}" id="desc_en_{$p|escape:'htmlall'}" value="{$proforma.description_en|escape:'htmlall'}" class="input-text" /> <small>{l s='English' mod='envoimoinscher'}</small>
								<a href="#" onclick="traduireDescription('{$proforma.description_fr|escape:'htmlall'}');return false;">{l s='Translate' mod='envoimoinscher'}</a></p> 
							</td>
						</tr>
						{/foreach}
						<input type="hidden" name="proformaSend" id="proformaSend" value="1" />
						{/if}
						{if isset($offer.insurance)}
						<tr><th colspan="2"><br /><b>{l s='AXA insurance information' mod='envoimoinscher'}</b></th></tr>
						<tr class="appendRow"><th><label for="insurance">{l s='Insure this shipment' mod='envoimoinscher'}</label></th>
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
						<tr class="assuTd {if !$offer.insurance}hidden{/if}"><th>{$mandatory.label}
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
					<td class="text_align_center" colspan="2">
						<input type="hidden" name="opeCode" id="opeCode" value="{if isset($offer.operator.code)}{$offer.operator.code}{/if}" />
						<input type="hidden" name="dest_country" id="dest_country" value="{$deliveryInfo.pays|escape:'htmlall'}" />
						<input type="hidden" name="exp_pays" id="exp_pays" value="{$shipperInfo.country|escape:'htmlall'}" />
						<input type="hidden" name="exp_cp" id="exp_cp" value="{$shipperInfo.postalcode|escape:'htmlall'}" />
						<input type="hidden" name="exp_city" id="exp_city" value="{$shipperInfo.city|escape:'htmlall'}" />
						<input type="submit" name="send" id="submitForm" value="{l s='Send' mod='envoimoinscher'}" class="button" />
					</td>
				</tr>
			</table>
		</form>
	</div>  
	{/if}
	{/if}
	</div>
	<div class="clear"></div>