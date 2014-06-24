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

	<link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />
	<script type="text/javascript">
	var envUrl = "{$envUrl|escape:'htmlall'}";
	var updatesUrl = "index.php?controller=AdminEnvoiMoinsCher&option=checkUpdates&token={$token|escape:'htmlall'}";
	</script>
	<script type="text/javascript" src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/js/configuration.js"></script>

	<!-- Added in 1.3 : end -->
	<div class="infoDoc">
		<!--   <br /><b>- Passez en mode "En ligne" : pour activer les offres et les mettre à disposition sur votre boutique en ligne une fois qu'elles ont été définies</b> -->
		<!--   <br /><br />NB : N'oubliez pas de sauvegarder à chaque étape. Reportez-vous à la documentation pour plus de détails.<br /><br /> -->
	</div>

	{if count($missedValues) > 0}
	<div class="alert error">Attention : certains champs 
		n'ont pas été renseignés et le module peut ne pas fonctionner correctement. Les champs manquants :
		{foreach from=$missedValues key=m item=missed}
		<br />- {$missed|escape:'htmlall'}
		{/foreach}
	</div>
	{/if}
	{if $defaultConfig.EMC_USER != "" && $defaultConfig.EMC_USER == 0}
	<div class="alert error">Vos données de connexion ne sont pas correctes. Le module ne va pas fonctionner correctement.</div>
	{/if}
	{if $multiShipping == 1}
	<div class="alert error">La version actuelle du module est incompatible avec l'option "Livraison à plusieurs adresses". Les offres du module ne seront pas proposées sur l'écran de commande.</div>
	{/if}
	{*
	{if $installNotice == 1}
	<div class="alert error">Certains fichiers n'ont pas pu être déplacés vers le répertoire /override. Probablement, une autre application a surchargé les fichiers démandés.
		<br /><br />A cause de cela, le module ne fonctionnera pas correctement. 
		<br /><br />Veuillez transférer les fichiers ci-dessus manuellement dans les répertoires correspondant dans /override : 
		<ul>
			{foreach from=$notCopied item=fil key=f}
			<li>- {$f|escape:'htmlall'} à transférer dans {$fil|escape:'htmlall'}</li>
			{/foreach}
		</ul>
	</div>
	{/if}
	*}

	{if $successForm == 1}
	<div class="conf confirm">Les données de configuration ont été mises à jour.
		{if $lastTab == '#confSrv'}<p><b>Si vous avez fini de configurer les offres et que vous souhaitez les afficher sur votre boutique, n'oubliez pas de leur appliquer le mode "En ligne"</b></p>{/if}
	</div><br />
	{/if}
	<ul id="menuTab">
		<li class="menuTabButton {if $lastTab == '#confMaj'}selected{/if}" id="tabLiConfMaj"><a href="#" rel="#confMaj">Mises à jour</a></li>
		<li class="menuTabButton {if $lastTab == '#confHelp'}selected{/if}" id="tabLiConfHelp"><a href="#" rel="#confHelp">Aide</a></li>
	</ul>

	<form action="index.php?controller={$tab|escape:'htmlall'}&configure={$configure|escape:'htmlall'}&token={$tokenForm|escape:'htmlall'}&tab_module={$tabModule|escape:'htmlall'}&module_name={$moduleName|escape:'htmlall'}&id_controller=1&section=general" method="post" class="form" id="configForm" style="width:100%; float:left;">
		<fieldset id="confMaj" class="configForm" {if $lastTab != '#confMaj'}style="display:none;"{/if}>
			<p>Cette page vous permet de gérer les mises à jour du module. Lisez tout d'abord le tableau d'information qui vous indique toutes les instructions à suivre.</p>
			<div id="updatesContainer" style="display:none; margin-bottom:20px;">
				<div class="conf">De nouvelles mises à jour ont été trouvées.</div>
				<p><b>Tableau d'information sur les nouvelles mises à jour à effectuer : description et instructions</b></p>
				<table id="updatesTable" class="table">
					<thead>
						<tr>
							<th>Version</th>
							<th>Nom</th>
							<th>Description</th>
							<th>Lien</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<p id="offerUpCont" class="hidden"><a href="index.php?controller=AdminEnvoiMoinsCher&option=lookForCarrierUpdates&token={$token|escape:'htmlall'}" class="action_module" id="ajaxCarrier" target="_blank">Vérifier les mises à jour sur les offres de transport</a> <img src="{$baseDir|escape:'htmlall'}modules/envoimoinscher/img/ajax-loader.gif" alt="" id="opesLoader" style="display:none;" /></p>
			<div id="resultCarrierUpdate" style="display:none;"><img src="{$adminImg|escape:'htmlall'}/forbbiden.gif" alt="nok" class="errorimg" /><span></span></div>
			{if count($upgrades) > 0}
			<p><b>Tableau d'installation des nouvelles mises à jour transférées sur le répertoire de votre boutique</b></p>
			<table class="table formTable">
				<thead>
					<tr>
						<th>Versions</th>
						<th>Date</th>
						<th>Changements</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$upgrades key=u item=upgrade}
					<tr {if $u%2 == 0}class="row"{/if}>
						<th>{$upgrade.from|escape:'htmlall'} - {$upgrade.to|escape:'htmlall'}</th>
						<th>{$upgrade.date|escape:'htmlall'}</th>
						<th>{$upgrade.description|escape:'htmlall'}</th>
						<th><a href="index.php?controller=AdminEnvoiMoinsCher&option=upgrade&token={$token|escape:'htmlall'}&up_id={$u|escape:'htmlall'}" class="action_module">effectuer</a></th>
					</tr>
					{/foreach}
				</tbody>
			</table>
			{else}
			<span id="updatesToInstall">Pas de mises à jour à installer.</span>
			{/if}
		</fieldset>
		<p class="center">
			<input type="hidden" id="lastTab" name="lastTab" value="{$lastTab|escape:'htmlall'}" />
			<input class="button" id="submitForm" type="submit" name="submitForm" value="Sauvegarder" />
		</p>
	</form>