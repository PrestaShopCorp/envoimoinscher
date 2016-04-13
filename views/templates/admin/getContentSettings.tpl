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
			$(document).delegate('#EMC_disable_cart','change',function(){
				$('.EMC_disable_cart_description').hide();
				$('.EMC_disable_cart_description_'+$(this).val()).show();
			});
		})
</script>
 
<form method="POST" action="{$EMC_link|escape:'htmlall':'UTF-8'}&EMC_tabs=settings">
    <fieldset>
        <legend>
			{l s='Multi-shipping' mod='envoimoinscher'}
		</legend>
		<!-- Type of send -->
		<!--label for="EMC_type" class="mt5">{l s='Type of send:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='- A fold is usually a shell whose weight does not exceed 3kg. <br />- Typically a package is a cardboard box or timber whose weight does not exceed 70 kg. But to express tenders, the package must be less than 30 kg.<br /> - The compact is therefore an object dimensions make it difficult standard packaging: furniture, objects of home or office, appliances, etc.. It requires several packages for example, but the characteristics of the command does not correspond to this type of shipment, no EMC offers will be displayed on your shop.' mod='envoimoinscher'}">
			<select name="EMC_type" id="EMC_type">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
				{if isset($shipTypes) && $shipTypes && sizeof($shipTypes)}
					{foreach from=$shipTypes key='k' item='v'}
						<option value="{$k|escape:'htmlall':'UTF-8'}"{if Tools::getValue('EMC_type', $EMC_config.EMC_TYPE) == $k}selected="selected"{/if}>&nbsp;{$v|escape:'htmlall':'UTF-8'}&nbsp;</option>
					{/foreach}
				{/if}
			</select>
		</div>
		<div class="clear both"></div-->
        <!-- Wrapping type -->
		<!-- label for="EMC_wrapping" class="mt5">{l s='Wrapping type:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='You must specify the wrapping of your parcels' mod='envoimoinscher'}">
            {if $shipWrappingAvailable}
                <select name="EMC_wrapping" id="EMC_wrapping">
                    {foreach from=$shipWrapping key=n item=wrapping}
                        <option value="{$wrapping.id|escape:'htmlall':'UTF-8'}" {if $EMC_config.EMC_WRAPPING == $wrapping.id}selected="selected"{/if}>&nbsp;{$wrapping.name|escape:'htmlall':'UTF-8'}&nbsp;</option>
                    {/foreach}
                </select>
            {else}
                <p>{l s='No wrapping available' mod='envoimoinscher'}</p>
                <input type="hidden" name="shipWrapping" id="shipWrapping" value=""></input>
            {/if}
		</div>
		<div class="clear both"></div -->
        <!-- Customer individual -->
        <input type="hidden" name="EMC_indiv" id="EMC_indiv" value="1" />
        <!--
        <label for="EMC_indiv">{l s='Customers individual:' mod='envoimoinscher'}</label>
        <div class="margin-form">
            <input type="checkbox" name="EMC_indiv" id="EMC_indiv" value="1" {if Tools::getValue('EMC_indiv', $EMC_config.EMC_INDI) == "1"} checked="checked"{/if}/>
            <p class="preference_description">
                {l s='By selecting this option, all your customers will be treated as individuals. This means that they will see offers reserved for special relay.' mod='envoimoinscher'}
            </p>
        </div>
        <div class="clear both"></div>
        -->
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
    </fieldset>
	<fieldset id="EMC_optional">
		<legend>
			{l s='Track number' mod='envoimoinscher'}
		</legend>
		<!-- Tracking -->
		<label for="EMC_track_mode">{l s='Type followed:' mod='envoimoinscher'}</label>
		<div class="margin-form">
            <div class="margin-form add-tooltip" title="{l s='Settings support message' mod='envoimoinscher'}">
                <select id="EMC_track_mode" name="EMC_track_mode">
                    {if isset($modes) && $modes && sizeof($modes)}
                        {foreach from=$modes key=m item=mode}
                            <option value="{$m|escape:'htmlall':'UTF-8'}" {if $EMC_config.EMC_TRACK_MODE == $m}selected="selected"{/if}>{$mode|escape:'htmlall':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <p class="preference_description">
                {l s='Choose what kind of tracking number must be taken and used for tracking URL that you defined on the carrier sheets.' mod='envoimoinscher'}
            </p>
        </div>
	</fieldset>
	<fieldset>
		<legend>
			{l s='Status' mod='envoimoinscher'}
		</legend>
		<!-- CMD -->
		<label for="EMC_cmd">{l s='State of the order placed:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when you trigger an order to shipment from EMC (when you placed the order for delivery). We recommend that you \'Preparation in progress\' ..' mod='envoimoinscher'}">
			<select name="EMC_cmd" id="EMC_cmd">
                {if isset($states) && $states && sizeof($states)}
                    {foreach from=$states item='state'}
                        <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {if $EMC_config.EMC_CMD == $state.id_order_state}selected="selected"{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                {/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Send -->
		<label for="EMC_envo">{l s='State of the order sent:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when the package has been removed. We recommend \'Out for Delivery\'.' mod='envoimoinscher'}">
			<select name="EMC_envo" id="EMC_envo">
                {if isset($states) && $states && sizeof($states)}
                    {foreach from=$states item='state'}
                        <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {if $EMC_config.EMC_ENVO == $state.id_order_state}selected="selected"{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                {/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Delivery -->
		<label for="EMC_liv">{l s='State of the order delivered:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when the consignment has been delivered. We recommend \'Delivered\'.' mod='envoimoinscher'}">
			<select name="EMC_liv" id="EMC_liv">
                {if isset($states) && $states && sizeof($states)}
                    {foreach from=$states item='state'}
                        <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {if $EMC_config.EMC_LIV == $state.id_order_state}selected="selected"{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                {/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Cancel -->
		<label for="EMC_ann">{l s='State of the order canceled:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when sending order was canceled. We recommend \'Cancelled\'.' mod='envoimoinscher'}">
			<select name="EMC_ann" id="EMC_ann">
                {if isset($states) && $states && sizeof($states)}
                    {foreach from=$states item='state'}
                        <option value="{$state.id_order_state|escape:'htmlall':'UTF-8'}" {if $EMC_config.EMC_ANN == $state.id_order_state}selected="selected"{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                {/if}
			</select>
		</div>
		<div class="clear both"></div>
	</fieldset>
	<fieldset>
		<legend>{l s='Add to cart shipping cost estimation' mod='envoimoinscher'}</legend>
		<p>{l s='When your client adds an item to the cart, the shipping costs displayed will be:' mod='envoimoinscher'}</p>
		<!-- Add to cart options -->
		<label for="EMC_disable_cart"></label>
		<div class="margin-form">
			<div class="margin-form add-tooltip" title="{l s='These are the shipping costs estimated before your client gets to the shipping option selection form.' mod='envoimoinscher'}">
                <select id="EMC_disable_cart" name="EMC_disable_cart">
                    <option value="2" {if Tools::getValue('EMC_disable_cart', $EMC_config.EMC_DISABLE_CART) == 2}selected{/if}>{l s='based on an estimation only' mod='envoimoinscher'}</option>
                    <option value="1" {if Tools::getValue('EMC_disable_cart', $EMC_config.EMC_DISABLE_CART) == 1}selected{/if}>{l s='quoted for authenticated users, but estimated for anonymous users' mod='envoimoinscher'}</option>
                    <option value="0" {if Tools::getValue('EMC_disable_cart', $EMC_config.EMC_DISABLE_CART) == 0}selected{/if}>{l s='quoted using client information currently available' mod='envoimoinscher'}</option>
                </select>
            </div>
			<p>
                <span class="EMC_disable_cart_description EMC_disable_cart_description_2" {if Tools::getValue('EMC_disable_cart', $EMC_config.EMC_DISABLE_CART) != 2}style="display:none"{/if}><span id="disable_cart_important">{l s='Add to cart action will be faster.' mod='envoimoinscher'}</span><br />
                {l s='Shipping costs will be estimated based on ranges defined for France in the' mod='envoimoinscher'} <a href="{$link->getAdminLink('AdminCarriers')|escape:'htmlall':'UTF-8'}">{l s='carrier edition page.' mod='envoimoinscher'}</a> {l s='Live quotations will only be used on the carrier selection page.' mod='envoimoinscher'}</span>
                <span class="EMC_disable_cart_description EMC_disable_cart_description_1" {if Tools::getValue('EMC_disable_cart', $EMC_config.EMC_DISABLE_CART) != 1}style="display:none"{/if}>{l s='Anonymous users will get a quotation based on ranges defined for France in the' mod='envoimoinscher'} <a href="{$link->getAdminLink('AdminCarriers')|escape:'htmlall':'UTF-8'}">{l s='carrier edition page.' mod='envoimoinscher'}</a><br />
				{l s='Live quotations will only be used on the carrier selection page.' mod='envoimoinscher'}</span>
				<span class="EMC_disable_cart_description EMC_disable_cart_description_0" {if Tools::getValue('EMC_disable_cart', $EMC_config.EMC_DISABLE_CART) != 0}style="display:none"{/if}>{l s='Anonymous users will get a live quotation based on a random address in France.' mod='envoimoinscher'}<br />
				{l s='This option can cause Add to cart action to be slower.' mod='envoimoinscher'}</span>
			</p>
		</div>
		<div class="clear both"></div>
	</fieldset>
	<fieldset>
		<legend>{l s='Logs' mod='envoimoinscher'}</legend>
		<!-- Logs -->
		<label for="EMC_enabled_logs">{l s='Enable Logs:' mod='envoimoinscher'}</label>

		<div class="margin-form add-tooltip" title="{l s='By selecting the log option, You will receive all errors logs if no carriers found for a specific address, errors during tracking insertion... It can help you to configure the plugin' mod='envoimoinscher'}">
			<input type="checkbox" name="EMC_enabled_logs" id="EMC_enabled_logs" value="1" {if Tools::getValue('EMC_enabled_logs', $EMC_config.EMC_ENABLED_LOGS) == "1"} checked="checked"{/if}/>
		</div>
		<div class="clear both"></div>
	</fieldset>
  <fieldset>
		<legend>{l s='Default filter settings for orders pending shipment table' mod='envoimoinscher'}</legend>
		<label for="EMC_filter_type_order">{l s='Order type:' mod='envoimoinscher'}</label>
		<div class="margin-form">
			<select id="EMC_filter_type_order" name="EMC_filter_type_order">
        <option value="all" {if !isset($EMC_config.EMC_FILTER_TYPE_ORDER) || $EMC_config.EMC_FILTER_TYPE_ORDER == "all"}selected{/if}>{l s='Show all' mod='envoimoinscher'}</option>
        <option value="0" {if isset($EMC_config.EMC_FILTER_TYPE_ORDER) && $EMC_config.EMC_FILTER_TYPE_ORDER == "0"}selected{/if}>{l s='EnvoiMoinsCher orders' mod='envoimoinscher'}</option>
        <option value="1" {if isset($EMC_config.EMC_FILTER_TYPE_ORDER) && $EMC_config.EMC_FILTER_TYPE_ORDER == "1"}selected{/if}>{l s='Non EnvoiMoinsCher orders' mod='envoimoinscher'}</option>
        <option value="2" {if isset($EMC_config.EMC_FILTER_TYPE_ORDER) && $EMC_config.EMC_FILTER_TYPE_ORDER == "2"}selected{/if}>{l s='Invalid or incomplete orders' mod='envoimoinscher'}</option>
			</select>
    </div>
		<div class="clear both"></div>
    <label for="EMC_filter_status">{l s='Status:' mod='envoimoinscher'}</label>
		<div class="margin-form">
			<select id="EMC_filter_status" name="EMC_filter_status[]" multiple size="3">
        {if isset($states) && $states && sizeof($states)}
          {if (isset($EMC_config.EMC_FILTER_STATUS))}
            {assign var=selected_statuses value=";"|explode:$EMC_config.EMC_FILTER_STATUS}
          {/if}
          {foreach from=$states key=k item=v}
          <option value="{$v['id_order_state']|escape:'htmlall':'UTF-8'}" 
          {if (isset($selected_statuses) && $v['id_order_state']|in_array:$selected_statuses)}selected
          {elseif !isset($selected_statuses)}
            selected
          {/if}
          >{$v['name']|escape:'htmlall':'UTF-8'}</option>
        {/foreach}
        {/if}    
			</select>
    </div>
		<div class="clear both"></div>
    <label for="EMC_filter_carriers">{l s='Carrier:' mod='envoimoinscher'}</label>
		<div class="margin-form">
			<select id="EMC_filter_carriers" name="EMC_filter_carriers">
        <option value="all" {if !isset($EMC_config.EMC_FILTER_CARRIERS) || $EMC_config.EMC_FILTER_CARRIERS == "all"}selected{/if}>{l s='Show all' mod='envoimoinscher'}</option>
					{foreach from=$enabledCarriers key=k item=v}
						<option value="{$v['name']|escape:'htmlall':'UTF-8'}" {if isset($EMC_config.EMC_FILTER_CARRIERS) && $EMC_config.EMC_FILTER_CARRIERS == $v['name']}selected{/if}>{$v['name']|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				<option value="del" {if isset($EMC_config.EMC_FILTER_CARRIERS) && $EMC_config.EMC_FILTER_CARRIERS == "del"}selected{/if}>{l s='Deleted carriers' mod='envoimoinscher'}</option>
			</select>
    </div>
		<div class="clear both"></div>
    <label for="EMC_filter_start_order_date">{l s='Time frame:' mod='envoimoinscher'}</label>
		<div class="margin-form">
			<select id="EMC_filter_start_order_date" name="EMC_filter_start_order_date">
        <option value="all" {if !isset($EMC_config.EMC_FILTER_START_DATE) || $EMC_config.EMC_FILTER_START_DATE == "all"}selected{/if}>{l s='Show all' mod='envoimoinscher'}</option>
        <option value="year" {if isset($EMC_config.EMC_FILTER_START_DATE) && $EMC_config.EMC_FILTER_START_DATE == "year"}selected{/if}>{l s='Last year' mod='envoimoinscher'}</option>
        <option value="month" {if isset($EMC_config.EMC_FILTER_START_DATE) && $EMC_config.EMC_FILTER_START_DATE == "month"}selected{/if}>{l s='Last month' mod='envoimoinscher'}</option>
        <option value="week" {if isset($EMC_config.EMC_FILTER_START_DATE) && $EMC_config.EMC_FILTER_START_DATE == "week"}selected{/if}>{l s='Last week' mod='envoimoinscher'}</option>
        <option value="day" {if isset($EMC_config.EMC_FILTER_START_DATE) && $EMC_config.EMC_FILTER_START_DATE == "day"}selected{/if}>{l s='Last day' mod='envoimoinscher'}</option>
			</select>
    </div>
		<div class="clear both"></div>
	</fieldset>
	<br />
	<div class="margin-form submit">
		<input type="submit" name="btnSettings" id="btnSettings" class="btn btn-default" value="{l s='Send' mod='envoimoinscher'}">
	</div>
</form>