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
<script type="text/javascript">
var baseWeight = "{$weight|escape:'htmlall'}";
var orderId = "{$orderId|escape:'htmlall'}";
var token = "{$token|escape:'htmlall'}";
var emcBaseDir = "{$moduleBaseDir|escape:'htmlall'}";
var envUrl = "{$envUrl|escape:'htmlall'}";

$(document).ready(function() { // TODO placer le texte au dessus du bouton
	$("#submitForm").click(function(){
		$("#submitForm").attr("disabled","disabled");
		$("#messageSending").css("display","block");
		var top = $("#submitForm").offset().top + $("#submitForm").height() - 
							$("#messageSending").offset().top - $("#messageSending").height();
		$("#messageSending").css("top",top + "px");
	});
});
</script>
<script type="text/javascript" src="{$baseDirCss|escape:'htmlall'}modules/envoimoinscher/js/send.js"></script>
<link type="text/css" rel="stylesheet" href="{$baseDirCss|escape:'htmlall'}modules/envoimoinscher/css/backend_styles_13.css" />
<link type="text/css" rel="stylesheet" href="{$baseDirCss|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />
{literal}
<style type="text/css">
.box-left {width:450px; margin-right:35px; float:left;}
.box-right {width:430px; float:left; display:inline;}
.formTable tr td {padding:10px 0;}
.formTable a {color: #268CCD;}
.table tr td {color: #000000;}
{/literal}
{if !$isFound && !$isEMCCarrier}
  {literal}
.box-left {float:none;}
  {/literal}
{/if}
</style>
{if $alreadyPassed}
<div class="alert error"><img src="{$adminImg|escape:'htmlall'}/forbbiden.gif" alt="nok" />
L'envoi a déjà été passé pour cette commande. Si vous avez des questions, contactez le service après vente d'EnvoiMoinsCher au numéro
01 75 77 37 97.
</div>
{else}
  {if $errorSend == 1}
  <div class="alert error"><img src="{$adminImg|escape:'htmlall'}/forbbiden.gif" alt="nok" /> La commande n'a pas été expédiée. Une erreur s'est produite :
    {$errorCotation|escape:'htmlall'}
  </div>
  {/if}
  <div class="box-left">
  {if $isFound}
  <p><b>Informations sur l'offre</b></p>
  <div id="offerTable">
    {include file="$tableTemplate" var=$offer}
  </div><!-- offerTable -->
  <div id="notFoundOffer"></div><!-- notFoundOffer-->
  <br />
  {elseif !$isFound}
  <div class="alert error"><img src="{$adminImg|escape:'htmlall'}/forbbiden.gif" alt="nok" />
    {if !$isEMCCarrier}
    L'offre choisie par votre client sur cette commande n'est pas une offre du module EnvoiMoinsCher. Cette page vous permet de la remplacer par une offre EnvoiMoinsCher : sélectionnez-en une ci-dessous parmi celles que les transporteurs EnvoiMoinsCher proposent (prévenez votre client du changement de l'offre) afin de l'appliquer pour l'expédition de cette commande.
    {else}
    L'offre choisie par votre client n'est plus disponible. Vous pouvez en sélectionner une nouvelle (prévenez votre client du changement de l'offre) ou réessayer plus tard.
    {/if}
  </div>
  <p><b>L'offre sélectionnée par le client : </b> {$orderInfo.label_es|escape:'htmlall'} ({$orderInfo.name_eo|escape:'htmlall'}) </p>
  <p style="margin-top:20px;"><b>Les offres EnvoiMoinsCher</b></p>
    {include file="$notFoundTemplate" var=$isEMCCarrier var=$offers var=$installedServices}
  <br />
  {/if}
  <p><b>Informations sur le destinataire</b></p>
  <table class="table" cellspacing="0" style="width:470px;">
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
  {if !$showDstBlock}<p><b>></b> <a href="#" id="changeDestData" class="action_module">modifier les informations sur le destinataire</a></p>{/if}
<div style="margin-top:15px;display:none;position:relative;" id="messageSending">
		<p>Votre expédition est en cours, veuillez patienter.</p>
		<p>Si jamais votre page ne se recharge pas, ou que la commande ne s'envoie pas <u>sans que vous ne receviez une erreur</u>, voici la marche à suivre :</p>
		<ul style="margin-left:10px;">
			<li> - Connectez vous à votre compte sur <a target="_blank" href=\"www.envoimoinscher.com\">www.envoimoinscher.com</a> et vérifiez que la commande n'a pas été prise en compte sur nos serveurs.</li>
			<li> - Si votre commande a été prise en compte, surtout ne renvoyez pas votre colis, contactez le service client au 01 75 77 37 97 qui vous aidera à régulariser la situation de votre colis sur Prestashop.</li>
		</ul>
	</div>
  </div>  
  {if $isFound}
  <div id="foundBlock" class="box-right">
    <p><b>Informations obligatoires</b></p>
    <form method="post" action="index.php?tab=AdminEnvoiMoinsCher&id_order={$orderId|escape:'htmlall'}&option=command&token={$token|escape:'htmlall'}" id="mandatory_form">
      <table class="table formTable" cellspacing="0">
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
						<input type="text" name="width" id="width" value="{$dimensions.width_ed|escape:'htmlall'}" class="input-text" /> cm
					</td>
				</tr>
				<tr>
					<th>
						<label for="weight">Longueur totale de l'envoi</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="length" id="length" value="{$dimensions.length_ed|escape:'htmlall'}" class="input-text" /> cm
					</td>
				</tr>
				<tr>
					<th>
						<label for="weight">Hauteur totale de l'envoi</label>
					</th>
					<td class="paddingTableTd">
						<input type="text" name="height" id="height" value="{$dimensions.height_ed|escape:'htmlall'}" class="input-text" /> cm
					</td>
				</tr>
    {foreach from=$offer.output key=m item=mandatory}
      {if count($mandatory) > 0 && ($offer.operator.code != 'CHRP' || $offer.operator.code == 'CHRP' && $mandatory.code != 'depot.pointrelais')}
        <tr><th>{$mandatory.label|unescape:'html'}
        </th><td style="padding-left:10px;">{$mandatory.field|unescape:'html'}
        {$mandatory.helper|unescape:'html'}
        </td></tr>
      {else} {* Only hidden fields, these values shouldn't be modified *}
        {$mandatory.field'|unescape:'html'} 
      {/if}
    {/foreach}
        <tr><th>
          <label for="date">Date d'enlèvement</label></th>
          <td style="padding-left:10px;"><input type="text" name="collecte" id="collecte" value="{$offer.collection.date|escape:'htmlall'}" class="input-text" />
          <p class="note">format : AAAA-MM-JJ, par exemple 2000-12-26 pour le 26 décembre 2000</p>
        </td></tr>
        <tr class="changeDest {if !$deliveryInfo.phoneAlert && !$showDstBlock}hidden{/if}"><th>
          <label for="date">Le numéro de téléphone du destinataire</label></th>
          <td style="padding-left:10px;"><input type="text" name="dest_tel" id="dest_tel" value="{$deliveryInfo.tel|escape:'htmlall'}" class="input-text" />
          {if $deliveryInfo.phoneAlert}<p class="note">! Votre destinataire n'a pas renseigné son numéro de téléphone. Votre numéro de téléphone a été repris.</p>{/if}
        </td></tr>
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="date">Le prénom du destinataire</label></th>
          <td class="paddingTableTd"><input type="text" name="dest_fname" id="dest_fname" value="{$deliveryInfo.prenom|escape:'htmlall'}" class="input-text" />
        </td></tr>
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="date">Le nom du destinataire</label></th>
          <td class="paddingTableTd"><input type="text" name="dest_lname" id="dest_lname" value="{$deliveryInfo.nom|escape:'htmlall'}" class="input-text" />
        </td></tr>
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="date">L'adresse du destinataire</label></th>
          <td style="padding-left:10px;"><input type="text" name="dest_add" id="dest_add" value="{$deliveryInfo.adresse|escape:'htmlall'}" class="input-text" />
        </td></tr>
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="date">Le code postal du destinataire</label></th>
          <td class="paddingTableTd"><input type="text" name="dest_code" id="dest_code" value="{$deliveryInfo.code_postal|escape:'htmlall'}" class="input-text" />
        </td></tr>
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="date">La ville du destinataire</label></th>
          <td class="paddingTableTd"><input type="text" name="dest_city" id="dest_city" value="{$deliveryInfo.ville|escape:'htmlall'}" class="input-text" />
        </td></tr>
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="date">L'e-mail du destinataire</label></th>
          <td class="paddingTableTd"><input type="text" name="dest_email" id="dest_email" value="{$deliveryInfo.email|escape:'htmlall'}" class="input-text" />
        </td></tr>
       {if $deliveryInfo.company != ""}
        <tr class="changeDest {if !$showDstBlock}hidden{/if}"><th>
          <label for="comp">Le nom de la société</label></th>
          <td class="paddingTableTd"><input type="text" name="dest_company" id="dest_company" value="{$deliveryInfo.societe|escape:'htmlall'}" class="input-text" />
        </td></tr>
    {/if}
    {if $proforma}
        <tr><th colspan="2"><br /><b>Informations pour la facture proforma, générée par EnvoiMoinsCher.com</b></th></tr>
      {foreach from=$proformaData key=p item=proforma}  
        <tr><th><label for="desc_fr_{$p|escape:'htmlall'}">Description d'objet #{$p|escape:'htmlall'}</label></th>
          <td style="padding-left:10px;">
            <p><input type="text" name="desc_fr_{$p|escape:'htmlall'}" id="desc_fr_{$p|escape:'htmlall'}" value="{$proforma.description_fr|escape:'htmlall'}" class="input-text" /> <small>(en français)</small></p>
            <p><input type="text" name="desc_en_{$p|escape:'htmlall'}" id="desc_en_{$p|escape:'htmlall'}" value="{$proforma.description_en|escape:'htmlall'}" class="input-text" /> <small>(en anglais)</small>
            <a href="#" onclick="traduireDescription('{$proforma.description_fr|escape:'htmlall'}');return false;">traduire</a></p> 
          </td>
        </tr>
      {/foreach}
    <input type="hidden" name="proformaSend" id="proformaSend" value="1" />
    {/if}
{if $offer.insurance}
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
        </th><td class="paddingTableTd">{$mandatory.field|escape:'htmlall'}
          {$mandatory.helper|escape:'htmlall'}
        </td></tr>
        {else} {* Only hidden fields, these values shouldn't be modified *}
          {$mandatory.field|escape:'htmlall'}
        {/if}
      {/foreach}
    {/if}
        <tr><td style="text-align:center;" colspan="2"><input type="hidden" name="opeCode" id="opeCode" value="{$offer.operator.code|escape:'htmlall'}" />
        <input type="hidden" name="dest_country" id="dest_country" value="{$deliveryInfo.iso_code|escape:'htmlall'}" />
        <input type="hidden" name="exp_pays" id="exp_pays" value="{$shipperInfo.country|escape:'htmlall'}" />
        <input type="hidden" name="exp_cp" id="exp_cp" value="{$shipperInfo.postalcode|escape:'htmlall'}" />
        <input type="hidden" name="exp_city" id="exp_city" value="{$shipperInfo.city|escape:'htmlall'}" />
		<input type="submit" name="send" id="submitForm" value="Expédier" class="button" /></td></tr>
      </table>
    </form>
  </div>  
  {/if}
{/if}
<div class="clear"></div>