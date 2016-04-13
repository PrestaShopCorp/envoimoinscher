{**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA / 2011-2016 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 *}
 <script>
	$(document).ready(function(){
		$(document).delegate('.fromto', 'change', function(){
			$('.fromto-'+$(this).val()).attr('selected','selected');
		});
        
        $(document).delegate('#labelDeliveryDateLangSelect', 'change', function(){
            $('.labelDeliveryDate').hide();
            var lang = $('#labelDeliveryDateLangSelect').val();
            $('input[name="labelDeliveryDate'+lang+'"]').show();
        })
	})
 </script>

<div class="support-message">
	<p>{l s='Sends support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall':'UTF-8'}&EMC_tabs=sends">
	<fieldset id="EMC_Sends">
		<legend>{l s='Your sends' mod='envoimoinscher'}</legend>
        <!-- Nature of send -->
		<label for="EMC_nature" class="mt5">{l s='Nature of send:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='You must specify the contents of your shipments. This information is transmitted to the carriers choose the most accurate among labels available language from the dropdown list. If you check the apply for the package description box, you use the language selected as the description of the contents of all your mail (this will be the information to be forwarded to (x) the carrier (s)). This data will be displayed on the shipping page on which you can trigger the order to send a parcel. It may be amended before validation of the page. Do not check the box if you want to resume direct name of products shipped (name that is saved in your product catalog)' mod='envoimoinscher'}">
			<select name="EMC_nature" id="EMC_nature">
				<option value="">-- {l s='Choose' mod='envoimoinscher'} --</option>
				{if isset($shipNature) && $shipNature && sizeof($shipNature)}
					{foreach from=$shipNature key='category_group_id' item='category_group'}
                        <optgroup label="{$category_group.name|escape:'htmlall':'UTF-8'}">
                        {foreach from=$category_group.categories key='cat_id' item='cat_name'}
                            <option value="{$cat_id|escape:'htmlall':'UTF-8'}" {if Tools::getValue('EMC_nature', $EMC_config.EMC_NATURE) == $cat_id}selected="selected"{/if}>&nbsp;{$cat_name|escape:'htmlall':'UTF-8'}&nbsp;</option>
                        {/foreach}
                        </optgroup>
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
	</fieldset>
    <fieldset>
        <legend>
            {l s='Weight management products' mod='envoimoinscher'}
        </legend>
        <!-- Default Weight -->
        <label for="EMC_default_weight" class="mt5">{l s='Default weight (%1$s):' mod='envoimoinscher' sprintf={$weightUnit|escape:'htmlall':'UTF-8'}}</label>
        <div class="margin-form">
            <input type="text" name="EMC_default_weight" id="EMC_default_weight" value="{Tools::getValue('EMC_default_weight', $EMC_config.EMC_AVERAGE_WEIGHT)|escape:'htmlall':'UTF-8'}" />
            <p class="preference_description">
                {l s='You can specify the weight that will be applied by default on your product catalog with the "Weight (package)" is not specified on the individual product sheets.' mod='envoimoinscher'}
            </p>
        </div>
        <div class="clear both"></div>
    </fieldset>
    <fieldset>
        <legend>{l s='Delivery' mod='envoimoinscher'}</legend>
        {if isset($pickupConf)}
            <label for="pickupDay1" class="mt5">{l s='Departure date:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
            <div class="margin-form">
                {l s='D +' mod='envoimoinscher'}
                <select name="pickupDay1" class="minWidthAuto">							
                    {section name=day start=0 loop=20 step=1}
                        <option value="{$smarty.section.day.index|escape:'htmlall':'UTF-8'}" {if Tools::getValue('pickupDay1', $pickupConf['j1']) == $smarty.section.day.index}selected="selected"{/if}>{$smarty.section.day.index|escape:'htmlall':'UTF-8'}</option>
                    {/section}
                </select>
                {l s='for orders submitted before' mod='envoimoinscher'} 
                <select name="pickupSplit" class="fromto minWidthAuto">							
                    {section name=hor start=0 loop=25 step=1}
                        <option value="{$smarty.section.hor.index|escape:'htmlall':'UTF-8'}" class="fromto-{$smarty.section.hor.index|escape:'htmlall':'UTF-8'}" {if Tools::getValue('pickupSplit', $pickupConf['split']) == $smarty.section.hor.index}selected="selected"{/if}>{$smarty.section.hor.index|escape:'htmlall':'UTF-8'}h00</option>
                    {/section}
                </select>
            </div>
            <div class="clear both"></div>
            <label for="pickupDay2"></label>
            <div class="margin-form">
                {l s='D +' mod='envoimoinscher'}
                 <select name="pickupDay2" class="minWidthAuto">							
                    {section name=day start=0 loop=20 step=1}
                        <option value="{$smarty.section.day.index|escape:'htmlall':'UTF-8'}" {if Tools::getValue('pickupDay2', $pickupConf['j2']) == $smarty.section.day.index}selected="selected"{/if}>{$smarty.section.day.index|escape:'htmlall':'UTF-8'}</option>
                    {/section}
                </select>
                {l s='for orders submitted after' mod='envoimoinscher'} 
                <select name="pickupSplitBis" class="fromto minWidthAuto">
                    {section name=hor start=0 loop=25 step=1}
                        <option value="{$smarty.section.hor.index|escape:'htmlall':'UTF-8'}" class="fromto-{$smarty.section.hor.index|escape:'htmlall':'UTF-8'}" {if Tools::getValue('pickupSplit', $pickupConf['split']) == $smarty.section.hor.index}selected="selected"{/if}>{$smarty.section.hor.index|escape:'htmlall':'UTF-8'}h00</option>
                    {/section}
                </select>
                <p class="preference_description">{l s='Please specify how many days it will take for the parcel to be shipped after the order is submitted (parcel drop-off date/on-site collection).' mod='envoimoinscher'}</p>
            </div>
            <div class="clear both"></div>
        {/if}
        <label for="labelDate" class="mt5">{l s='Label for delivery date:' mod='envoimoinscher'} {Tools::getValue('labelDeliveryDate')|escape:'htmlall':'UTF-8'}</label>
        <div class="margin-form">
            {foreach from=$labelDeliveryDate key='k' item='v' name=labelDeliveryDate}
                <input type="text" name="labelDeliveryDate{$k|escape:'htmlall':'UTF-8'}" value="{$v|escape:'htmlall':'UTF-8'}" class="labelDeliveryDate" {if $k != $currentLanguage} style="display:none" {/if} />
            {/foreach}
            <select id="labelDeliveryDateLangSelect" class="valignmiddle minWidthAuto">
                {foreach from=$labelDeliveryDate key='k' item='v'}
                    <option value="{$k|escape:'htmlall':'UTF-8'}" {if $k == $currentLanguage} selected=selected {/if}>{$k|truncate:2:"":true}</option>
                {/foreach}
            </select>
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
                {l s='By selecting the insurance option declared value, I have read of:' mod='envoimoinscher'}<a href="{l s='http://www.envoimoinscher.com/faq/131-tout-savoir-sur-l-assurance-ad-valorem.html' mod='envoimoinscher'}" class="action_module" target="_blank">{l s='the instructions of the insurance contract insured AXA' mod='envoimoinscher'}</a>.
                <br /><br />
                {l s='Warning, the insufficiency and inadequacy of your package are excluded from the guarantee risk AXA, be sure to overprotect your shipment.' mod='envoimoinscher'}
                <br /><br />
                {l s='Insurance is only available without option "Mode of sending mass mailings without checks."' mod='envoimoinscher'}
            </p>
        </div>
        <div class="clear both"></div>
    </fieldset>
	<br />
	<div class="margin-form submit">
		<input type="submit" name="btnSends" value="{l s='Send' mod='envoimoinscher'}" class="btn btn-default" />
	</div>
</form>