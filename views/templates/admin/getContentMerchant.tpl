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
	<p>{l s='Merchant support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall'}&EMC_tab=merchant">
	<fieldset>
		<legend>{l s='API account' mod='envoimoinscher'}</legend>
		<label for="EMC_login">
			{l s='Login:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Login of your account EnvoiMoinsCher.com' mod='envoimoinscher'}">
			<input type="text" id="EMC_login" name="EMC_login" value="{Tools::getValue('EMC_login', $EMC_config.EMC_LOGIN)|escape:'htmlall'}"/>
		</div>
		<div class="clear both"></div>
		<label for="EMC_pass">
			{l s='Password:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Password to your account EnvoiMoinsCher.com' mod='envoimoinscher'}">
			<input type="password" id="EMC_pass" name="EMC_pass" value="{Tools::getValue('EMC_pass', $EMC_config.EMC_PASS)|escape:'htmlall'}"/>
		</div>
		<div class="clear both"></div>
		<label for="EMC_api">
			{l s='API key:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Key you received by email from Envoimoinscher. Com. Just copy it into the provided field. Warning, this key must match the work environment chosen: testing, or production' mod='envoimoinscher'}">
			<input type="text" id="EMC_api" name="EMC_api" value="{Tools::getValue('EMC_api', $EMC_config.EMC_KEY)|escape:'htmlall'}"/>
		</div>
		<div class="clear both"></div>
		<div class="margin-form">
			<p class="preference_description">
				<a href="http://ecommerce.envoimoinscher.com/" class="action_module" target="_blank">{l s='Click here to get API key' mod='envoimoinscher'}</a>
			</p> 
		</div>
		<div class="clear both"></div>
	</fieldset>
	<fieldset>
		<legend>{l s='Pickup address' mod='envoimoinscher'}</legend>
		<!-- Gender -->
		<label for="EMC_login">
			{l s='Gender:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Title of the person sending the commands: choose M, Mrs. or Miss' mod='envoimoinscher'}">
			{if isset($genders) && sizeof($genders)}
				{foreach from=$genders item='gender'}
					<input type="radio" name="EMC_gender"{if Tools::getValue('EMC_gender', $EMC_config.EMC_CIV) == $gender} checked="checked"{/if} value="{$gender}" id="Gender_{$gender}" />
					<label for="Gender_{$gender|escape:'htmlall'}">{l s=$gender mod='envoimoinscher'}</label>
				{/foreach}
			{/if}
		</div>
		<div class="clear both"></div>
		<!-- First name -->
		<label for="EMC_exp_firstname">
			{l s='First name of the sender:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Firstname of the person sending' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_firstname" name="EMC_exp_firstname" value="{Tools::getValue('EMC_exp_firstname', $EMC_config.EMC_FNAME)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- Last name -->
		<label for="EMC_exp_lastname">
			{l s='Last name of the sender:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Name of the person sending' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_lastname" name="EMC_exp_lastname" value="{Tools::getValue('EMC_exp_lastname', $EMC_config.EMC_LNAME)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- Company -->
		<label for="EMC_exp_company">
			{l s='Company:' mod='envoimoinscher'}
		</label>
		<div class="margin-form add-tooltip" title="{l s='Name of the company from which the package.' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_company" name="EMC_exp_company" value="{Tools::getValue('EMC_exp_company', $EMC_config.EMC_COMPANY)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- Address -->
		<label for="EMC_exp_address">
			{l s='Address:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='The address from which depart packages' mod='envoimoinscher'}">
			<textarea id="EMC_exp_address" name="EMC_exp_address">{$EMC_config.EMC_ADDRESS|escape:'htmlall'}</textarea>
		</div>
		<div class="clear both"></div>
		<!-- Post code -->
		<label for="EMC_exp_postcode">
			{l s='Post code:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Postal code of the address where the package depart' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_postcode" name="EMC_exp_postcode" value="{Tools::getValue('EMC_exp_postcode', $EMC_config.EMC_POSTALCODE)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- Town -->
		<label for="EMC_exp_town">
			{l s='Town:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='City of the address where the package depart.' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_town" name="EMC_exp_town" value="{Tools::getValue('EMC_exp_town', $EMC_config.EMC_CITY)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- Phone -->
		<label for="EMC_exp_phone">
			{l s='Phone:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Phone number of the sender (it is possible that the driver will contact you for your pickups).' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_phone" name="EMC_exp_phone" value="{Tools::getValue('EMC_exp_phone', $EMC_config.EMC_TEL)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- Email -->
		<label for="EMC_exp_email">
			{l s='Email:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Email address of the sender (Where carriers can contact you any time to the pickups)' mod='envoimoinscher'}">
			<input type="text" id="EMC_exp_email" name="EMC_exp_email" value="{Tools::getValue('EMC_exp_email', $EMC_config.EMC_MAIL)|escape:'htmlall'}" />
		</div>
		<div class="clear both"></div>
		<!-- More informations -->
		<label for="EMC_exp_more_infos">
			{l s='More information about the address:' mod='envoimoinscher'}
		</label>
		<div class="margin-form">
			<textarea id="EMC_exp_more_infos" name="EMC_exp_more_infos">{Tools::getValue('EMC_exp_more_infos', $EMC_config.EMC_COMPL)|escape:'htmlall'}</textarea>
			<p class="preference_description">
				{l s='Floors, code, ...' mod='envoimoinscher'}
			</p>
		</div>
		<div class="clear both"></div>
		<div class="margin-form"></div>
		<div class="clear both"></div>
		<!-- Start time to pick up availability -->
		<label for="EMC_exp_start_pickup">
			{l s='Start time to pick up availability:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Time from which you are available for pick packages. Choose a time from the drop-down list (pickups by carriers generally begin at 12:00).' mod='envoimoinscher'}">
			<select name="EMC_exp_start_pickup" id="EMC_exp_start_pickup">
				{foreach from=$dispoStart key=d item=dispo}
					<option value="{$dispo}" {if Tools::getValue('EMC_exp_start_pickup', $EMC_config.EMC_DISPO_HDE) == $dispo}selected="selected"{/if}>{$dispo}</option>
				{/foreach}
			</select>
		</div>
		<div class="clear both"></div>
		<!-- End time to pick up availability -->
		<label for="EMC_exp_end_pickup">
			{l s='End time to pick up availability:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
		</label>
		<div class="margin-form add-tooltip" title="{l s='Time from which you are not available for pick packages. Choose a time from the drop-down list (pickups by carriers generally begin at 17:00).' mod='envoimoinscher'}">
			<select name="EMC_exp_end_pickup" id="EMC_exp_end_pickup">
				{foreach from=$dispoEnd key=d item=dispo}
				<option value="{$dispo}" {if Tools::getValue('EMC_exp_end_pickup', $EMC_config.EMC_DISPO_HLE) == $dispo}selected="selected"{/if}>{$dispo}</option>
				{/foreach}
			</select>
		</div>
		<div class="clear both"></div>
		{*
		 TODO : A déplacer
		<!-- Relais colis -->
		<label for="EMC_exp_relais_colis">
			{l s='Relay point depot to Relais Colis®:' mod='envoimoinscher'}
		</label>
		<div class="margin-form">
			<input type="text" id="EMC_exp_relais_colis" name="EMC_exp_relais_colis" value="{Tools::getValue('EMC_exp_relais_colis', $EMC_config.EMC_RELAIS_SOGP)|escape:'htmlall'}" />
			<p class="preference_description">
				{l s='To choose Relais Colis ®, copy and paste the code point near your choice (for example: K1168, K1161, K1170).' mod='envoimoinscher'}<br />
				<a href="#" class="action_module" onclick="javascript: openPopupEmc('SOGP'); return false;">{l s='Get the code' mod='envoimoinscher'}</a>
			</p>
		</div>
		<div class="clear both"></div>
		<!-- Mondial relay -->
		<label for="EMC_exp_mondial_relay">
			{l s='Relay point depot to Mondial Relay:' mod='envoimoinscher'}
		</label>
		<div class="margin-form add-tooltip">
			<input type="text" id="EMC_exp_mondial_relay" name="EMC_exp_mondial_relay" value="{Tools::getValue('EMC_exp_mondial_relay', $EMC_config.EMC_RELAIS_MONR)|escape:'htmlall'}" />
			<p class="preference_description">
				{l s='To select your pick-up point, copy and paste the code point near your choice (for example: 010081, 042358, 072559, 067199).' mod='envoimoinscher'}<br />
				<a href="#" class="action_module" onclick="javascript: openPopupEmc('MONR'); return false;">{l s='Get the code' mod='envoimoinscher'}</a>
			</p>
		</div>
		<div class="clear both"></div>
		*}
	</fieldset>
	<br />
	<div class="margin-form">
		<input type="submit" name="btnMerchant" value="{l s='Send' mod='envoimoinscher'}" class="btn btn-default" />
	</div>
</form>