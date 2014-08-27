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

 <script>
	$(document).ready(function(){
		$(document).delegate('.fromto','change',function(){
			$('.fromto-'+$(this).val()).attr('selected','selected');
		});
	})
 </script>
 
<div class="support-message">
	<p>{l s='Sends support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall'}&EMC_tabs=sends">
	<fieldset id="EMC_Sends">
		<legend>{l s='Your sends' mod='envoimoinscher'}</legend>
		<!-- Type of send -->
		<label for="EMC_type">{l s='Type of send:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='- A fold is usually a shell whose weight does not exceed 3kg. <br />- Typically a package is a cardboard box or timber whose weight does not exceed 70 kg. But to express tenders, the package must be less than 30 kg.<br /> - The compact is therefore an object dimensions make it difficult standard packaging: furniture, objects of home or office, appliances, etc.. It requires several packages for example, but the characteristics of the command does not correspond to this type of shipment, no EMC offers will be displayed on your shop.' mod='envoimoinscher'}">
			<select name="EMC_type" id="EMC_type">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
				{if isset($shipTypes) && $shipTypes && sizeof($shipTypes)}
					{foreach from=$shipTypes item='v'}
						<option value="{$v}"{if Tools::getValue('EMC_type', $EMC_config.EMC_TYPE) == $v}selected="selected"{/if}>&nbsp;{$v}&nbsp;</option>
					{/foreach}
				{/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Nature of send -->
		<label for="EMC_nature">{l s='Nature of send:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='You must specify the contents of your shipments. This information is transmitted to the carriers choose the most accurate among labels available language from the dropdown list. If you check the apply for the package description box, you use the language selected as the description of the contents of all your mail (this will be the information to be forwarded to (x) the carrier (s)). This data will be displayed on the shipping page on which you can trigger the order to send a parcel. It may be amended before validation of the page. Do not check the box if you want to resume direct name of products shipped (name that is saved in your product catalog)' mod='envoimoinscher'}">
			<select name="EMC_nature" id="EMC_nature">
				<option value="">-- {l s='Choose' mod='envoimoinscher'} --</option>
				{if isset($shipNature) && $shipNature && sizeof($shipNature)}
					{foreach from=$shipNature item='nature'}
						<option value="{$nature.id}" {if Tools::getValue('EMC_nature', $EMC_config.EMC_NATURE) == $nature.id}selected="selected"{/if}>&nbsp;{$nature.name}&nbsp;</option>
					{/foreach}
				{/if}
			</select>
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
		<label for="EMC_wrapping">{l s='Wrapping type:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
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
				<input type="text" name="EMC_default_weight" id="EMC_default_weight" value="{Tools::getValue('EMC_default_weight', $EMC_config.EMC_AVERAGE_WEIGHT)|escape:'htmlall'}" />
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
					<label for="pickupDay{$smarty.section.conf.index|escape:'htmlall'}">{l s='Pickup date : D +' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
					<div class="margin-form">
						<input type="text" name="pickupDay{$smarty.section.conf.index|escape:'htmlall'}" id="pickupDay{$smarty.section.conf.index|escape:'htmlall'}" value="{$pickupConf[conf].j|escape:'htmlall'}" class="pickupDaylist" /> {l s='for orders past between' mod='envoimoinscher'} 
						<select name="pickupFrom{$smarty.section.conf.index|intval}" class="fromto pickupFrom_date">
							{if $smarty.section.conf.index == 0}
								<option value="0" selected="selected">0:00</option>
							{else}
								{section name=hor start=0 loop=25 step=1}
									<option value="{$smarty.section.hor.index|escape:'htmlall'}" class="fromto-{$smarty.section.hor.index}" {if Tools::getValue('pickupFrom'|cat:$smarty.section.conf.index, $pickupConf[conf].from) == $smarty.section.hor.index}selected="selected"{/if}>{$smarty.section.hor.index|escape:'htmlall'}h00</option>
								{/section}
							{/if}
						</select> {l s='and' mod='envoimoinscher'}
						<select name="pickupTo{$smarty.section.conf.index|escape:'htmlall'}" class="fromto pickupFrom_date">							
							{if $smarty.section.conf.index == 1}
								<option value="24" selected="selected">24:00</option>
							{else}
								{section name=hor start=0 loop=25 step=1}
									<option value="{$smarty.section.hor.index}" class="from fromto-{$smarty.section.hor.index}" {if Tools::getValue('pickupTo'|cat:$smarty.section.conf.index, $pickupConf[conf].to) == $smarty.section.hor.index}selected="selected"{/if}>{$smarty.section.hor.index}h00</option>
								{/section}
							{/if}
						</select>
						{if $smarty.section.conf.index == 1}
							<p class="preference_description">{l s='Specify how many days after the order is taken by the buyer, the pickup be programmed.' mod='envoimoinscher'}</p>
						{/if}
					</div>
					<div class="clear both"></div>
				{/section}
			{/if}
			<label for="labelDate">{l s='Label for delivery date :' mod='envoimoinscher'}</label>
			<div class="margin-form">
				<input type="text" name="labelDeliveryDate" id="labelDeliveryDate" value="{$EMC_config.EMC_LABEL_DELIVERY_DATE|escape:'htmlall'}" class="labelDeliveryDate" />
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
		<input type="submit" name="btnSends" value="{l s='Send' mod='envoimoinscher'}" class="btn btn-default" />
	</div>
</form>