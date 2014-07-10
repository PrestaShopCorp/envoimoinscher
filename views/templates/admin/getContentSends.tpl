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

<div class="support-message">
	<p>{l s='Sends support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall'}&EMC_tabs=sends">
	<fieldset id="EMC_Sends">
		<legend>{l s='Your sends' mod='envoimoinscher'}</legend>
		<!-- Type of send -->
		<label for="EMC_type">{l s='Type of send:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='- A fold is usually a shell whose weight does not exceed 3kg. <br />- Typically a package is a cardboard box or timber whose weight does not exceed 70 kg. But to express tenders, the package must be less than 30 kg.<br /> - The compact is therefore an object dimensions make it difficult standard packaging: furniture, objects of home or office, appliances, etc.. It requires several packages for example, but the characteristics of the command does not correspond to this type of shipment, no EMC offers will be displayed on your shop.' mod='envoimoinscher'}">
			<select name="EMC_type" id="EMC_type">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
				{if isset($shipTypes) && $shipTypes && sizeof($shipTypes)}
					{foreach from=$shipTypes item='v'}
						<option value="{$v}"{if Tools::getValue('EMC_type', $EMC_config.EMC_TYPE) == $v}selected="selected"{/if}>&nbsp;{$v}&nbsp;</option>
					{/foreach}
				{/if}
			</select> <sup>*</sup>
		</div>
		<div class="clear both"></div>
		<!-- Nature of send -->
		<label for="EMC_nature">{l s='Nature of send:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='You must specify the contents of your shipments. This information is transmitted to the carriers choose the most accurate among labels available language from the dropdown list. If you check the apply for the package description box, you use the language selected as the description of the contents of all your mail (this will be the information to be forwarded to (x) the carrier (s)). This data will be displayed on the shipping page on which you can trigger the order to send a parcel. It may be amended before validation of the page. Do not check the box if you want to resume direct name of products shipped (name that is saved in your product catalog)' mod='envoimoinscher'}">
			<select name="EMC_nature" id="EMC_nature">
				<option value="">-- {l s='Choose' mod='envoimoinscher'} --</option>
				{if isset($shipNature) && $shipNature && sizeof($shipNature)}
					{foreach from=$shipNature item='nature'}
						<option value="{$nature.id}" {if Tools::getValue('EMC_nature', $EMC_config.EMC_NATURE) == $nature.id}selected="selected"{/if}>&nbsp;{$nature.name}&nbsp;</option>
					{/foreach}
				{/if}
			</select> <sup>*</sup>
		</div>
		<div class="clear both"></div>
		<div class="margin-form">
			<p class="preference_description">
				<input type="checkbox" name="contentAsDesc" id="contentAsDesc" value="1" class="checkbox" {if Tools::getValue('contentAsDesc', $EMC_config.EMC_CONTENT_AS_DESC) == "1"}checked="checked"{/if} />
				{l s='Apply for a description of the package.' mod='envoimoinscher'}<br /><br />
				{l s='Information transmitted to carrier(s), select the language as accurate as possible.' mod='envoimoinscher'} <br />
				{l s='If you check, you apply the language chosen as a description of the contents of your mail (instead of the name of products shipped).' mod='envoimoinscher'}
			</p>
		</div>
		<div class="clear both"></div>
		<!-- Wrapping type -->
		<label for="EMC_wrapping">{l s='Wrapping type:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='You must specify the wrapping of your parcels' mod='envoimoinscher'}">
{if $shipWrappingAvailable}
                <select name="EMC_wrapping" id="EMC_wrapping">
{foreach from=$shipWrapping key=n item=wrapping}
                   <option value="{$wrapping.id}" {if $EMC_config.EMC_WRAPPING == $wrapping.id}selected="selected"{/if}>&nbsp;{$wrapping.name}&nbsp;</option>
{/foreach}
                </select>
{else}
								<p>{l s='No wrapping available' mod='envoimoinscher'}</p>
								<input type="hidden" name="shipWrapping" id="shipWrapping" value=""></input>
{/if}
			<sup>*</sup>
		</div>
		<div class="clear both"></div>
		{if isset($all) && $all === true}
			<!-- Customer individual -->
			<label for="EMC_indiv">{l s='Customers individual:' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="checkbox" name="EMC_indiv" id="EMC_indiv" value="1" {if Tools::getValue('EMC_indiv', $EMC_config.EMC_INDI) == "1"} checked="checked"{/if}/>
				<p class="preference_description">
					{l s='By selecting this option, all your customers will be treated as individuals. This means that they will see offers reserved for special relay.' mod='envoimoinscher'}
				</p>
			</div>
			<div class="clear both"></div>

			<!-- Multi Package -->
			<label for="EMC_multiparcel">{l s='Use multi-package' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="checkbox" name="EMC_multiparcel" id="EMC_multiparcel" {if Tools::getValue('EMC_multiparcel', $EMC_config.EMC_MULTIPARCEL) == "on"} checked="checked"{/if}>
				<p class="preference_description">
					{l s='You can choose to send some commands in several parcels instead of one.' mod='envoimoinscher'} <br />
					{l s='Caution! There may be a difference in price compared to the price paid by your buyer.' mod='envoimoinscher'} <br />
					{l s='All vouchers will be charged.' mod='envoimoinscher'}
				</p>
			</div>
		{/if}
	</fieldset>
	{if isset($all) && $all === true}
		<fieldset>
			<legend>
				{l s='Weight management products' mod='envoimoinscher'}
			</legend>
			<!-- Default Weight -->
			<label for="EMC_default_weight">{l s='Default weight:' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="text" name="EMC_default_weight" id="EMC_default_weight" value="{Tools::getValue('EMC_default_weight', $EMC_config.EMC_AVERAGE_WEIGHT)|escape:'htmlall'}" /> {l s='kg' mod='envoimoinscher'}
				<p class="preference_description">
					{l s='You can specify the weight that will be applied by default on your product catalog with the "Weight (package)" is not specified on the individual product sheets.' mod='envoimoinscher'}
				</p>
			</div>
			<div class="clear both"></div>
			<!-- Default Weight -->
			<label for="EMC_min_weight">{l s='Min weight:' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="checkbox" name="EMC_min_weight" id="EMC_min_weight" value="1" {if Tools::getValue('EMC_min_weight', $EMC_config.EMC_WEIGHTMIN) == "1"} checked="checked"{/if}/>
				<p class="preference_description">
					{l s='Applying weight of 100g to less than 100g items.' mod='envoimoinscher'}
				</p>
			</div>
			<div class="clear both"></div>
		</fieldset>
		<fieldset>
			<legend>{l s='Pickups' mod='envoimoinscher'}</legend>
			{if isset($pickupConf)}
				{section name=conf loop=$pickupConf}
					<label for="pickupDay{$smarty.section.conf.index|escape:'htmlall'}">{l s='Pickup date : D +' mod='envoimoinscher'}</label>
					<div class="margin-form">
						<input type="text" name="pickupDay{$smarty.section.conf.index|escape:'htmlall'}" id="pickupDay{$smarty.section.conf.index|escape:'htmlall'}" value="{$pickupConf[conf].j|escape:'htmlall'}" style="width: 30px;" /> <sup>*</sup> {l s='for orders past between' mod='envoimoinscher'} 
						<select name="pickupFrom{$smarty.section.conf.index|intval}" style="width:70px;">
							{section name=hor start=0 loop=25 step=1}
								<option value="{$smarty.section.hor.index|escape:'htmlall'}" {if Tools::getValue('pickupFrom'|cat:$smarty.section.conf.index, $pickupConf[conf].from) == $smarty.section.hor.index}selected="selected"{/if}>{$smarty.section.hor.index|escape:'htmlall'}h00</option>
							{/section}
						</select> <sup>*</sup> {l s='and' mod='envoimoinscher'} 
						<select name="pickupTo{$smarty.section.conf.index|escape:'htmlall'}" style="width:70px;">
							{section name=hor start=0 loop=25 step=1}
								<option value="{$smarty.section.hor.index}" {if Tools::getValue('pickupTo'|cat:$smarty.section.conf.index, $pickupConf[conf].to) == $smarty.section.hor.index}selected="selected"{/if}>{$smarty.section.hor.index}h00</option>
							{/section}
						</select> <sup>*</sup>
						{if $smarty.section.conf.index == 1}
							<p class="preference_description">{l s='Specify how many days after the order is taken by the buyer, the pickup be programmed.' mod='envoimoinscher'}</p>
						{/if}
					</div>
					<div class="clear both"></div>
				{/section}
			{/if}
			<label for="labelDate">{l s='Label for delivery date :' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="text" name="labelDeliveryDate" id="labelDeliveryDate" value="{$EMC_config.EMC_LABEL_DELIVERY_DATE|escape:'htmlall'}" style="width:250px;" />
				<p class="preference_description">{l s='Specify the delivery date label seen by the client. Example : "Delivery date : {DATE}". Empty label to disable.' mod='envoimoinscher'}</p>
			</div>
			<div class="clear both"></div>
		</fieldset>
		<fieldset>
			<legend>{l s='Insurance' mod='envoimoinscher'}</legend>
			<!-- Use AXA -->
			<label for="EMC_use_axa">{l s='Use AXA insurance:' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="checkbox" name="EMC_use_axa" id="EMC_use_axa" value="1" {if Tools::getValue('EMC_use_axa', $EMC_config.EMC_ASSU) == "1"} checked="checked"{/if}/>
				<p class="preference_description">
					{l s='By selecting the insurance option declared value, I have read of:' mod='envoimoinscher'}<a href="http://www.envoimoinscher.com/notice-axa.html" class="action_module" target="_blank">{l s='the instructions of the insurance contract insured AXA' mod='envoimoinscher'}</a>.
					<br /><br />
					{l s='Warning, the insufficiency and inadequacy of your package are excluded from the guarantee risk AXA, be sure to overprotect your shipment.' mod='envoimoinscher'}
					<br /><br />
					{l s='Insurance is only available without option "Mode of sending mass mailings without checks."' mod='envoimoinscher'}
				</p>
			</div>
			<div class="clear both"></div>
		</fieldset>
		
	{/if}
	<br />
	<div class="margin-form">
		<input type="submit" name="btnSends" value="{l s='Send' mod='envoimoinscher'}" class="button" />
	</div>
	{*
	TODO : A déplacer
	<fieldset>
		<!-- Mode of sending mass -->
		<label for="EMC_mass">{l s='Mode of sending mass:' mod='envoimoinscher'}</label>
		<div class="margin-form">
			<select name="EMC_mass" id="EMC_mass">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
				<option value="1" {if $withMass == $EMC_config.EMC_MASS}selected="selected"{/if}>{l s='Check with each shipment' mod='envoimoinscher'}</option>
				<option value="0" {if $withoutMass == $EMC_config.EMC_MASS}selected="selected"{/if}>{l s='Without verification of shipments' mod='envoimoinscher'}</option>
			</select> <sup>*</sup>
			<p class="preference_description">
				{l s='When you send multiple commands at once, you can choose to check and confirm the information of each item before triggers.' mod='envoimoinscher'}
			</p>
		</div>
		<div class="clear both"></div>
		<!-- Submit -->
		<div class="margin-form">
			<input type="submit" name="btnSends" id="btnSends" class="button" value="{l s='Send' mod='envoimoinscher'}" />
		</div>
	</fieldset>
	<fieldset id="EMC_advanced">
		<legend>{l s='Advanced parameters' mod='envoimoinscher'}</legend>
		<p>
			{l s='Select shipping offers you want to offer your rates and the type of store (see instructions in the third configuration step above).' mod='envoimoinscher'}<br /><br />
			<a href="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall'}&option=tests" class="action_module" target="_blank">{l s='Help choosing offers' mod='envoimoinscher'}</a><br /><br />
			<a href="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall'}&option=cleanCache" class="action_module" target="_blank">{l s='Purge the cache' mod='envoimoinscher'}</a><br /><br />
		</p>
		<fieldset>
			<legend>{l s='Advanced carriers' mod='envoimoinscher'}</legend>
			<p>
				{l s='The carriers listed below reflect the specific weight and dimensions of your package to calculate the cost of transport.' mod='envoimoinscher'}<br /> 
				{l s='This is why we advise you to customize the dimensions of your package.' mod='envoimoinscher'}
				<br /><br />
				<strong>{l s='Then you get a more accurate billing and no surprises.' mod='envoimoinscher'}</strong>
				<br /><br />
			</p>
			<ul>
				<li>{l s='Real Price: Price returned by Envoimoinscher, you have nothing more to do.' mod='envoimoinscher'}</li>
				<li>{l s='Package: Shipping you set yourself before setting your weight ranges or prices in the Transport tab).' mod='envoimoinscher'}</li>
				<li>{l s='Remember that you can offer shipping from the amount of your choice in the Transport tab> Transport Prestashop part handling.' mod='envoimoinscher'}</li>
			</ul>
			<p>{l s='For more details, view the documentation' mod='envoimoinscher'}</p>
			{if isset($families) && sizeof(families)}
				{foreach from=$families key=f item=family}
					{if $f == 3}
						<fieldset>
							<legend>{$family|escape:'htmlall'}</legend>
							{include file="$familTableTpl" offers=$offersExpress disableServices=$disableServices}
						</fieldset>
					{/if}
				{/foreach}
			{/if}
		</fieldset>
		<fieldset>
			<legend>{l s='Customizing dimencions to send' mod='envoimoinscher'}</legend>
			<p>
				{l s='On this page, you can customize the maximum size of your shipments by weight ranges. We recommend that you perform this customization work because the dimensions are one of the key criteria for the award of the tender of delivery. Indicate and the most common sizes for the most realistic prices.' mod='envoimoinscher'}
				<br /><br />
				{l s='Without customization, it is the default dimensions, developed by Envoimoinscher, will be displayed.' mod='envoimoinscher'}
			</p>
			<table class="table offersList">
				<thead>
					<tr>
						<th class="center">
							 #
						</th>
						<th class="center">
							{l s='Weight to' mod='envoimoinscher'}
						</th>
						<th class="center">
							{l s='Length max' mod='envoimoinscher'}
						</th>
						<th class="center">
							{l s='Width max' mod='envoimoinscher'}
						</th>
						<th class="center">
							{l s='Height max' mod='envoimoinscher'}
						</th>
					</tr>
				</thead>
				<tbody>
					{if isset($dims) && $dims && sizeof($dims)}
						{foreach from=$dims key=d item=dim}
							<tr>
								<td class="center">
									<span class="big">{$d+1|intval}</span>
								</td>
								<td class="center">
									<input type="text" name="weight{$d+1|intval}" id="weight{$d+1|intval}" value="{$dim.weight_ed|intval}" class="smallInput" /> <span>kg</span>
								</td>
								<td class="center">
									<input type="text" name="length{$d+1|intval}" id="length{$d+1|intval}" value="{$dim.length_ed|intval}" class="smallInput" /> <span>cm</span>
								</td>
								<td class="center">
									<input type="text" name="width{$d+1|intval}" id="width{$d+1|intval}" value="{$dim.width_ed|intval}" class="smallInput" /> <span>cm</span>
								</td>
								<td class="center">
									<input type="text" name="height{$d+1|intval}" id="height{$d+1|intval}" value="{$dim.height_ed|intval}" class="smallInput" /> <span>cm</span>
									<input type="hidden" name="id{$d+1|intval}" id="id{$d+1|intval}" value="{$dim.id_ed|intval}" />
								</td>
							</tr>
						{/foreach}
					{/if}
					<input type="hidden" name="countDims" id="countDims" value="{sizeof($dims)|intval}" />
				</tbody>
			</table>
		</fieldset>
		<div class="margin-form">
			<input type="submit" name="btnAdvanced" id="btnAdvanced" class="button" value="{l s='Send' mod='envoimoinscher'}">
		</div>
		<div class="clear both"></div>
	</fieldset>
	*}
</form>