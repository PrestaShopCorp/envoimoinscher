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
	<p>{l s='Settings support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall'}&EMC_tabs=settings">
	<fieldset id="EMC_optional">
		<legend>
			{l s='Track number' mod='envoimoinscher'}
		</legend>
		<!-- Follow type -->
		<label for="EMC_track_mode">{l s='Type followed:' mod='envoimoinscher'}</label>
		<div class="margin-form">
			<select id="EMC_track_mode" name="EMC_track_mode">
				{if isset($modes) && $modes && sizeof($modes)}
					{foreach from=$modes key=m item=mode}
						<option value="{$m}" {if $EMC_config.EMC_TRACK_MODE == $m}selected="selected"{/if}>{$mode}</option>
					{/foreach}
				{/if}
			</select>
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
		<label for="EMC_cmd">{l s='State of the order placed:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when you trigger an order to shipment from EMC (when you placed the order for delivery). We recommend that you \'Preparation in progress\' ..' mod='envoimoinscher'}">
			<select name="EMC_cmd" id="EMC_cmd">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
					{if isset($states) && $states && sizeof($states)}
						{foreach from=$states item='state'}
							<option value="{$state.id_order_state}" {if $EMC_config.EMC_CMD == $state.id_order_state}selected="selected"{/if}>{$state.name}</option>
						{/foreach}
					{/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Send -->
		<label for="EMC_envo">{l s='State of the order sent:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when the package has been removed. We recommend \'Out for Delivery\'.' mod='envoimoinscher'}">
			<select name="EMC_envo" id="EMC_envo">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
					{if isset($states) && $states && sizeof($states)}
						{foreach from=$states item='state'}
							<option value="{$state.id_order_state}" {if $EMC_config.EMC_ENVO == $state.id_order_state}selected="selected"{/if}>{$state.name}</option>
						{/foreach}
					{/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Delivery -->
		<label for="EMC_liv">{l s='State of the order delivered:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when the consignment has been delivered. We recommend \'Delivered\'.' mod='envoimoinscher'}">
			<select name="EMC_liv" id="EMC_liv">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
					{if isset($states) && $states && sizeof($states)}
						{foreach from=$states item='state'}
							<option value="{$state.id_order_state}" {if $EMC_config.EMC_LIV == $state.id_order_state}selected="selected"{/if}>{$state.name}</option>
						{/foreach}
					{/if}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- Cancel -->
		<label for="EMC_ann">{l s='State of the order canceled:' mod='envoimoinscher'} <sup class="emc-required">*</sup></label>
		<div class="margin-form add-tooltip" title="{l s='Choose the status that the command should display (both on your back office and your e-commerce site) when sending order was canceled. We recommend \'Cancelled\'.' mod='envoimoinscher'}">
			<select name="EMC_ann" id="EMC_ann">
				<option value="">-- {l s='Please choose' mod='envoimoinscher'} --</option>
					{if isset($states) && $states && sizeof($states)}
						{foreach from=$states item='state'}
							<option value="{$state.id_order_state}" {if $EMC_config.EMC_ANN == $state.id_order_state}selected="selected"{/if}>{$state.name}</option>
						{/foreach}
					{/if}
			</select>
		</div>
		<div class="clear both"></div>
	</fieldset>
	<fieldset>
		<legend>{l s='Messages you want to send or receive' mod='envoimoinscher'}</legend>
		<!-- Mail label -->
		<label for="EMC_mail_label">{l s='Mail the slip:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='sent to the sender (ie you), this email contains the instructions and shipping or packing slips' mod='envoimoinscher'}">
			<input type="checkbox" name="EMC_mail_label" id="EMC_mail_label" class="checkbox" value="1" {if isset($mailConfig.label) && $mailConfig.label == "true"}checked="checked"{/if}/>
		</div>
		<div class="clear both"></div>
		<!-- Mail label -->
		<label for="EMC_mail_notif">{l s='Mail notification to the receiver:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='sent to the recipient (your buyer), this mail informs the recipient that he will soon be sending delivered. Warning, this notification is sent and signed by Envoimoinscher and not by the carrier' mod='envoimoinscher'}">
			<input type="checkbox" name="EMC_mail_notif" id="EMC_mail_notif" class="checkbox" value="1" {if isset($mailConfig.notification) && $mailConfig.notification == "true"}checked="checked"{/if} />
		</div>
		<div class="clear both"></div>
		<!-- Mail label -->
		<label for="EMC_mail_bill">{l s='Mail with invoice:' mod='envoimoinscher'}</label>
		<div class="margin-form add-tooltip" title="{l s='sent to the email billing selected in your profile Envoimoinscher address, email can provide the invoice for items that you have made.' mod='envoimoinscher'}">
			<input type="checkbox" name="EMC_mail_bill" id="EMC_mail_bill" class="checkbox" value="1" {if isset($mailConfig.bill) && $mailConfig.bill == "true"}checked="checked"{/if} />
		</div>
		<div class="clear both"></div>
	</fieldset>
	<br />
	<div class="margin-form">
		<input type="submit" name="btnSettings" id="btnSettings" class="btn btn-default" value="{l s='Send' mod='envoimoinscher'}">
	</div>
</form>