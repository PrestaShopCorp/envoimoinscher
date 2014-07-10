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

<fieldset id="EMC_help">
	<legend>
		{l s='Help and upgrade' mod='envoimoinscher'}
	</legend>
	<div id="help-update">
		<h2 class="title-help">{l s='Upgrade' mod='envoimoinscher'}</h2>
	<li class="main-question"><h3>{l s='SQL update' mod='envoimoinscher'}</h3></li>
	<table class="table upgradeList">
		<thead>
			<tr>
				<th class="center">
					{l s='Version' mod='envoimoinscher'}
				</th>
				<th class="center">
					{l s='Date' mod='envoimoinscher'}
				</th>
				<th class="center">
					{l s='Changements' mod='envoimoinscher'}
				</th>
				<th class="center">
					{l s='Actions' mod='envoimoinscher'}
				</th>
			</tr>
		</thead>
		<tbody>
			{if isset($upgrades) && $upgrades && sizeof($upgrades)}
				{foreach from=$upgrades item='upgrade' key='u'}
					<tr>
						<td class="center">
							{$upgrade.from|escape:'htmlall'} - {$upgrade.to|escape:'htmlall'}
						</td>
						<td class="center">
							{$upgrade.date|escape:'htmlall'}
						</td>
						<td class="center">
							{$upgrade.description|unescape:'html'}
						</td>
						<td class="center">
							<a class="button" href="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall'}&option=upgrade&up_id={$u|escape:'htmlall'}" class="action_module">effectuer</a>
						</td>
					</tr>
				{/foreach}
			{else}
				<tr>
					<td colspan="4">
						<div class="warn">
							{l s='No upgrade at this time' mod='envoimoinscher'}
						</div>
					</td>
				</tr>
			{/if}
		</tbody>
	</table>
	<li class="main-question"><h3>{l s='Carriers update' mod='envoimoinscher'}</h3></li>
	<p>{l s='Update all the carriers:' mod='envoimoinscher'}<a id="loadAllCarriers" onclick="loadAllCarriers();" class="action_module" rel="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall'}&option=loadAllCarriers">{l s='Update carriers' mod='envoimoinscher'}</a></p>
	<div id="carriers_update_result"></div>
	</div>
	
	<div id="help-question">
		<h2 class="title-help">{l s='Common questions' mod='envoimoinscher'}</h2>
	
	<li class="main-question"><h3>{l s='Where can i find help with the configuration?' mod='envoimoinscher'}</h3></li>
	{l s='A documentation is available here:' mod='envoimoinscher'}<a href="http://ecommerce.envoimoinscher.com/api/download/doc_prestashop_configurer.pdf" target="_blank" class="action_module">{l s='documentation' mod='envoimoinscher'}</a><br/>	
	{l s='A documentation is also provided about sending method:' mod='envoimoinscher'}<a href="http://ecommerce.envoimoinscher.com/api/download/doc_prestashop_expedier.pdf" target="_blank" class="action_module">{l s='sending method' mod='envoimoinscher'}</a><br/>
	{l s='Contact customer service for help' mod='envoimoinscher'}
	
	<li class="main-question"><h3>{l s='Can i test my module?' mod='envoimoinscher'}</h3></li>
	{l s='A simulation page is available here:' mod='envoimoinscher'}<a href="{$link->getAdminLink('AdminEnvoiMoinsCher')}&option=tests" class="action_module" target="_blank">{l s='Help for choose offers link' mod='envoimoinscher'}</a>
	
	<li class="main-question"><h3>{l s='Why is there no offer?' mod='envoimoinscher'}</h3></li>
	{l s='If no offers, ask yourself these questions:' mod='envoimoinscher'}
	<ul class="sub-questions">
			<li><b>{l s='Is the module online?' mod='envoimoinscher'}</b> {l s='If not, set your module online' mod='envoimoinscher'}</li>
			<li><b>{l s='Are my credentials corrects?' mod='envoimoinscher'}</b> {l s='If not, there should be an error' mod='envoimoinscher'}</li>
			<li><b>{l s='Did i just changed my configuration?' mod='envoimoinscher'}</b> {l s='If not, you should clean the cache' mod='envoimoinscher'}</li>
			<li><b>{l s='Are my articles weights correct?' mod='envoimoinscher'}</b> {l s='If not, you should set your products weight' mod='envoimoinscher'}</li>
			<li><b>{l s='Is the module activated?' mod='envoimoinscher'}</b> {l s='Check if it is activated' mod='envoimoinscher'}</li>
			<li><b>{l s='Is there a valid address on my client account?' mod='envoimoinscher'}</b> {l s='Try with a simple valid address' mod='envoimoinscher'}</li>
	</ul>
	{l s='Check your logs and contact the technical service' mod='envoimoinscher'}<br/>
	{l s='Access to your logs:' mod='envoimoinscher'}<a href="{$link->getAdminLink('AdminLogs')|escape:'htmlall'}" target="_blank" class="action_module">logs</a>
	
	<li class="main-question"><h3>{l s='Why do my prices are wrong?' mod='envoimoinscher'}</h3></li>
	{l s='If your prices are wrong, ask yourself these questions.' mod='envoimoinscher'}<br/>
	{l s='If you are using the real price:' mod='envoimoinscher'}
	<ul class="sub-questions">
		<li><b>{l s='Are your article\'s weight correct?' mod='envoimoinscher'}</b> {l s='Check if your products weight are correct' mod='envoimoinscher'}</li>
	</ul>
	{l s='If you are using the package price:' mod='envoimoinscher'}
	<ul class="sub-questions">
		<li><b>{l s='Are my shippers in package mode?' mod='envoimoinscher'}</b> {l s='If not, change your shippers to package mode' mod='envoimoinscher'}</li>
		<li><b>{l s='Are my weight/price ranges correct?' mod='envoimoinscher'}</b> {l s='Check if your weight/price ranges are correct' mod='envoimoinscher'}</li>
	</ul>
	{l s='If prices are still incorrect, contact the technical service' mod='envoimoinscher'}

	<li class="main-question"><h3>{l s='Why do the parcel points aren\'t here?' mod='envoimoinscher'}</h3></li>
	{l s='If the parcel points aren\'t here, ask yourself these questions:' mod='envoimoinscher'}
	<ul class="sub-questions">
		<li><b>{l s='Am i using a custom theme?' mod='envoimoinscher'}</b> {l s='A custom theme can be a problem' mod='envoimoinscher'}</li>
		<li><b>{l s='Do i use the "free shipping" option?' mod='envoimoinscher'}</b> {l s='The free shipping option do not work' mod='envoimoinscher'}</li>
		<li><b>{l s='Is there any parcel point near this address?' mod='envoimoinscher'}</b> {l s='Maybe there is no parcel points near your address' mod='envoimoinscher'}</li>
	</ul>
	{l s='Parcel points are still not here? contact the technical service' mod='envoimoinscher'}
	
	<li class="main-question"><h3>{l s='Why is my shipment label not available?' mod='envoimoinscher'}</h3></li>
	{l s='If the shipment label can\t be loaded, ask yourself these questions:' mod='envoimoinscher'}
	<ul class="sub-questions">
		<li><b>{l s='Did i wait long enough?' mod='envoimoinscher'}</b> {l s='The shipment label can take time to generate' mod='envoimoinscher'}</li>
		<li><b>{l s='Is my module in test mode?' mod='envoimoinscher'}</b> {l s='Change to production mode and make another order on our website' mod='envoimoinscher'}<a target="_blank" href="http://www.envoimoinscher.com">www.envoimoinscher.com</a></li>
	</ul>
	{l s='If the shipment label is still not available, contact the technical service' mod='envoimoinscher'}
	
	<li class="main-question"><h3>{l s='Another question?' mod='envoimoinscher'}</h3></li>
	<p>
		{l s='For any other question, we are available:' mod='envoimoinscher'}
		<div class="borderBlock">
				<strong>{l s='Customer service:' mod='envoimoinscher'}</strong>
			<ul>
				<li>contact@envoimoinscher.com</li>
				<li>{l s='By phone:' mod='envoimoinscher'} +33 (0)1 75 77 37 97</li>
			</ul>
		</div>
		<div class="borderBlock">
				<strong>{l s='Technical service:' mod='envoimoinscher'}</strong>
			<ul>
				<li>informationAPI@envoimoinscher.com</li>
				<li>{l s='By phone:' mod='envoimoinscher'} +33 (0)1 75 77 37 96</li>
			</ul>
		</div>
	</p>
	</div>
</fieldset>