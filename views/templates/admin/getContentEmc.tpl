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

{if isset($choice) && $choice == "create"}

	<script type="text/javascript" src="{$baseDir|escape:'htmlall':'UTF-8'}modules/envoimoinscher/views/js/getContentEmc.js"></script>
	<script>
		{if Tools::getValue('contact_etat')}
			var contact_etat = "{Tools::getValue('contact_etat')|escape:'htmlall':'UTF-8'}";
		{/if}
		var siret_label_fr = "{l s='SIRET:' mod='envoimoinscher'}";
		var siret_label_world = "{l s='Registration No. in the country:' mod='envoimoinscher'}";
		token = "{$token|escape:'htmlall':'UTF-8'}";
	</script>
	
	<div class="support-message mb10">
		<input type="checkbox" checked="checked" disabled="true" />
		<span>{l s='I would like to install the EnvoiMoinsCher module directly on my E-Commerce site. I will receive my API key upon validation of this form.' mod='envoimoinscher'}</span>
	</div>
	<form method="POST" id="account_emc" action="{$EMC_link|escape:'htmlall':'UTF-8'}&EMC_tab=merchant">
		<fieldset>
			<legend>{l s='Person to contact' mod='envoimoinscher'}</legend>
			
			<label></label>
			<div class="margin-form">
				<input type="radio" name="contact_civ" {if !Tools::isSubmit('contact_civ') || Tools::getValue('contact_civ') == 'M.'} checked="checked" {/if} value="M." />
				<label for="contact_civ">{l s='Mr' mod='envoimoinscher'}</label>
				<input type="radio" name="contact_civ" {if Tools::getValue('contact_civ') == 'Mme'} checked="checked" {/if} value="Mme" />
				<label for="contact_civ">{l s='Mrs' mod='envoimoinscher'}</label>
			</div>
			<div class="clear both"></div>
			
			<!-- Last name -->
			<label for="contact_nom">
				{l s='Surname:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_nom" value="{Tools::getValue('contact_nom')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- First name -->
			<label for="contact_prenom">
				{l s='First name:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_prenom" value="{Tools::getValue('contact_prenom')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Profession -->
			<label for="profession">
				{l s='Occupation:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">					
				<select name="profession">
					<option value="">{l s='-- Please choose one --' mod='envoimoinscher'}</option>
					<option value="gerant" {if Tools::getValue('profession') == 'gerant'}selected="selected"{/if}>{l s='E-commerce site manager' mod='envoimoinscher'}</option>
					<option value="developpeur" {if Tools::getValue('profession') == 'developpeur'}selected="selected"{/if}>{l s='Developer' mod='envoimoinscher'}</option>
					<option value="agence" {if Tools::getValue('profession') == 'agence'}selected="selected"{/if}>{l s='Agency' mod='envoimoinscher'}</option>
					<option value="free-lance" {if Tools::getValue('profession') == 'free-lance'}selected="selected"{/if}>{l s='Freelance' mod='envoimoinscher'}</option>
					<option value="autre" {if Tools::getValue('profession') == 'autre'}selected="selected"{/if}>{l s='Other' mod='envoimoinscher'}</option>
				</select>				
			</div>
			<div class="clear both"></div>
			
			<!-- Software -->
			<label for="logiciel">
				{l s='Prestashop version you are using:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">					
				<select name="logiciel">
					<option value="prestashop-1.4" {if Tools::getValue('logiciel') == 'prestashop-1.4'}selected="selected"{/if}>{l s='Prestashop 1.4' mod='envoimoinscher'}</option>
					<option value="prestashop-1.5" {if Tools::getValue('logiciel') == 'prestashop-1.5'}selected="selected"{/if}>{l s='Prestashop 1.5' mod='envoimoinscher'}</option>
					<option value="prestashop-1.6" {if !Tools::isSubmit('logiciel') || Tools::getValue('logiciel') == 'prestashop-1.6'}selected="selected"{/if}>{l s='Prestashop 1.6' mod='envoimoinscher'}</option>
					<option value="je-ne-sais-pas" {if Tools::getValue('logiciel') == 'je-ne-sais-pas'}selected="selected"{/if}>{l s='I dont know' mod='envoimoinscher'}</option>
				</select>				
			</div>
			<div class="clear both"></div>
			
			<!-- Partner code -->
			<label for="partner_code">
				{l s='Partner code, if you have one:' mod='envoimoinscher'}
			</label>
			<div class="margin-form">
				<input type="text" name="partner_code" value="{Tools::getValue('partner_code')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Site URL -->
			<label for="url">
				{l s='Your site URL:' mod='envoimoinscher'}
			</label>
			<div class="margin-form">
				<input type="text" name="url" value="{Tools::getValue('url')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Email -->
			<label for="contact_email">
				{l s='Your email address:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_email" value="{Tools::getValue('contact_email')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			<label for="contact_email2">
				{l s='Confirm your email address:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_email2" value="{Tools::getValue('contact_email2')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<fieldset>
			<legend>{l s='Your login' mod='envoimoinscher'}</legend>
			<div class="mb10">{l s='These login details will allow you to log into your EnvoiMoinsCher account' mod='envoimoinscher'}</div>
			<div class="clear both"></div>
			
			<!-- Login -->
			<label for="login">
				{l s='Login:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form add-tooltip" title="{l s='Alphanumeric characters only (letter and/or numbers without accents)' mod='envoimoinscher'}">
				<input type="text" name="login" value="{Tools::getValue('login')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Password -->
			<label for="password">
				{l s='Password:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form add-tooltip" title="{l s='Minimum of 6 characters' mod='envoimoinscher'}">
				<input type="password" name="password" value="{Tools::getValue('password')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			<label for="confirm_password">
				{l s='Confirm the password:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="password" name="confirm_password" value="{Tools::getValue('confirm_password')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<fieldset>
			<legend>{l s='Your invoice address' mod='envoimoinscher'}</legend>
			
			<!-- Company -->
			<label for="contact_ste">
				{l s='Company/Organisation:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_ste" value="{Tools::getValue('contact_ste')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Address -->
			<label for="adresse1">
				{l s='Your address:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="adresse1" value="{Tools::getValue('adresse1')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			<label for="adresse2">
				{l s='Address 1:' mod='envoimoinscher'}
			</label>
			<div class="margin-form">
				<input type="text" name="adresse2" value="{Tools::getValue('adresse2')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			<label for="adresse3">
				{l s='Address 2:' mod='envoimoinscher'}
			</label>
			<div class="margin-form">
				<input type="text" name="adresse3" value="{Tools::getValue('adresse3')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Country -->
			<label for="pz_iso">
				{l s='Country:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<select name="pz_iso" id="pz_iso">
					{foreach from=$countries item='country'}
						<option in-ue={$country.id_zone|escape:'htmlall':'UTF-8'} value="{$country.iso_code|escape:'htmlall':'UTF-8'}" {if !Tools::getValue('pz_iso') && $country.iso_code == "FR"} selected="selected" {elseif $country.iso_code == Tools::getValue('pz_iso')} selected="selected" {/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
			<div class="clear both"></div>
			
			<!-- State -->
			<label for="contact_etat" style="display:none">
				{l s='State:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form" style="display:none">
				<select id="contact_etat" name="contact_etat"></select>
			</div>
			<div class="clear both"></div>
			
			<!-- Postcode -->
			<label for="contact_cp">
				{l s='Postcode:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_cp" value="{Tools::getValue('contact_cp')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- City -->
			<label for="contact_ville">
				{l s='City:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_ville" value="{Tools::getValue('contact_ville')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Phone -->
			<label for="contact_tel">
				{l s='Telephone:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_tel" value="{Tools::getValue('contact_tel')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Language -->
			<label for="contact_locale">
				{l s='Preferred language of correspondence:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<select name="contact_locale">
					<option value="fr_FR" {if !Tools::getValue('contact_locale') && $lang == 'fr'} selected="selected" {elseif Tools::getValue('contact_locale') == 'fr_FR'} selected="selected" {/if}>{l s='French' mod='envoimoinscher'}</option>
					<option value="en_US" {if !Tools::getValue('contact_locale') && $lang == 'en'} selected="selected" {elseif Tools::getValue('contact_locale') == 'en_US'} selected="selected" {/if}>{l s='English' mod='envoimoinscher'}</option>
					<option value="es_ES" {if !Tools::getValue('contact_locale') && $lang == 'es'} selected="selected" {elseif Tools::getValue('contact_locale') == 'es_ES'} selected="selected" {/if}>{l s='Spanish' mod='envoimoinscher'}</option>
				</select>
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<fieldset>
			<legend>{l s='Professional account' mod='envoimoinscher'}</legend>
			
			<!-- SIRET -->
			<label for="contact_stesiret">
				{l s='SIRET:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="text" name="contact_stesiret" value="{Tools::getValue('contact_stesiret')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- TVA -->
			<div id="tva">
				<label for="contact_tvaintra">
					{l s='Intra-community VAT No.:' mod='envoimoinscher'}
				</label>
				<div class="margin-form">
					<input type="text" name="contact_tvaintra" value="{Tools::getValue('contact_tvaintra')|escape:'htmlall':'UTF-8'}" />
				</div>
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<fieldset>
			<legend>{l s='Legal notes' mod='envoimoinscher'}</legend>
			
			<div class="margin-form">
				<input type="checkbox" name="cgv" {if Tools::getValue('cgv')}checked="checked"{/if} />
				<label for="cgv">{l s='I acknowledge having read the General Terms and Conditions of Sale and of Use of the website EnvoiMoinsCher.com and the General Terms and Conditions of Use of the EnvoiMoinsCher.com API in full and agree to the terms thereof.' mod='envoimoinscher'} <a target="_blank" href="{l s='http://ecommerce.envoimoinscher.com/cgvu/' mod='envoimoinscher'}">{l s='General Terms and Conditions of Sale and of Use' mod='envoimoinscher'}</a> {l s='and ' mod='envoimoinscher'} <a target="_blank" href="http://ecommerce.envoimoinscher.com/api/download/cgu_api_envoimoinscher_fr.pdf">{l s='Conditions of Use of the EnvoiMoinsCher.com API ' mod='envoimoinscher'}</a> {l s='in full and agree to the terms thereof.' mod='envoimoinscher'}</label>
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form">
				<input type="checkbox" name="newsletterEmc" {if Tools::getValue('newsletterEmc')}checked="checked"{/if} />
				<label for="newsletterEmc">{l s='I wish to receive information regarding EnvoiMoinsCher.com news.' mod='envoimoinscher'}</label>
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form">
				<input type="checkbox" name="newsletterCom" {if Tools::getValue('newsletterCom')}checked="checked"{/if} />
				<label for="newsletterCom">{l s='I would like to receive promotional information from EnvoiMoinsCher.com partners.' mod='envoimoinscher'}</label>
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<br />
		<div class="text_align_center"><a href="#" class="btnValid selected button-orange text_align_center">{l s='Create an account' mod='envoimoinscher'}</a></span>
		<input type="hidden" name="choice" value="create">
		<input type="submit" class="hidden" name="btnEmc" value="Suivant">
		<script type="text/javascript">
			{literal}		
				$(".btnValid").click(function() {
					$('#account_emc').find('input[type=submit]').click();
				});	
			{/literal}
		</script>
	</form>
	
{else}
	<div class="support-message mb10">
		<span>{l s='Please fill in the following form to receive your API key.' mod='envoimoinscher'}</span>
	</div>
	
	<form method="POST" id="account_emc" action="{$EMC_link|escape:'htmlall':'UTF-8'}&EMC_tab=merchant">
		<fieldset>
			<legend>{l s='Prestashop version' mod='envoimoinscher'}</legend>
			
			<!-- Software -->
			<label for="logiciel">
				{l s='Prestashop version you are using:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">					
				<select name="logiciel">
					<option value="prestashop-1.4" {if Tools::getValue('logiciel') == 'prestashop-1.4'}selected="selected"{/if}>{l s='Prestashop 1.4' mod='envoimoinscher'}</option>
					<option value="prestashop-1.5" {if Tools::getValue('logiciel') == 'prestashop-1.5'}selected="selected"{/if}>{l s='Prestashop 1.5' mod='envoimoinscher'}</option>
					<option value="prestashop-1.6" {if !Tools::isSubmit('logiciel') || Tools::getValue('logiciel') == 'prestashop-1.6'}selected="selected"{/if}>{l s='Prestashop 1.6' mod='envoimoinscher'}</option>
					<option value="je-ne-sais-pas" {if Tools::getValue('logiciel') == 'je-ne-sais-pas'}selected="selected"{/if}>{l s='I dont know' mod='envoimoinscher'}</option>
				</select>				
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<fieldset>
			<legend>{l s='Your login' mod='envoimoinscher'}</legend>
			
			<!-- Login -->
			<label for="login">
				{l s='Login:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form add-tooltip" title="{l s='Alphanumeric characters only (letter and/or numbers without accents)' mod='envoimoinscher'}">
				<input type="text" name="login" value="{Tools::getValue('login')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<!-- Email -->
			<label for="password">
				{l s='Password:' mod='envoimoinscher'} <sup class="emc-required">*</sup>
			</label>
			<div class="margin-form">
				<input type="password" name="password" value="{Tools::getValue('password')|escape:'htmlall':'UTF-8'}" />
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<fieldset>
			<legend>{l s='Legal notes' mod='envoimoinscher'}</legend>
			
			<div class="margin-form">
				<input type="checkbox" name="cgv" {if Tools::getValue('cgv')}checked="checked"{/if} />
				<label for="cgv">{l s='I acknowledge having read the General Terms and Conditions of Sale and of Use of the website EnvoiMoinsCher.com and the General Terms and Conditions of Use of the EnvoiMoinsCher.com API in full and agree to the terms thereof.' mod='envoimoinscher'} <a target="_blank" href="{l s='http://ecommerce.envoimoinscher.com/cgvu/' mod='envoimoinscher'}">{l s='General Terms and Conditions of Sale and of Use' mod='envoimoinscher'}</a> {l s='and ' mod='envoimoinscher'} <a target="_blank" href="http://ecommerce.envoimoinscher.com/api/download/cgu_api_envoimoinscher_fr.pdf">{l s='Conditions of Use of the EnvoiMoinsCher.com API ' mod='envoimoinscher'}</a> {l s='in full and agree to the terms thereof.' mod='envoimoinscher'}</label>
			</div>
			<div class="clear both"></div>
			
			<div class="margin-form"></div>
			<div class="clear both"></div>
			
		</fieldset>
		<br />
		<div class="text_align_center"><a href="#" class="btnValid get_key button-orange text_align_center">{l s='Get an API key' mod='envoimoinscher'}</a><a href="#" class="btnValid ml10p button-orange text_align_center">{l s='I already have an API key' mod='envoimoinscher'}</a></span>
		<input type="hidden" name="choice" value="">
		<input type="submit" class="hidden" name="btnEmc" value="Suivant">
		<script type="text/javascript">
			{literal}		
				$(".btnValid").click(function() {
					if($(this).hasClass("get_key")) $('#account_emc').find('input[name=choice]').val("get_key");
					else $('#account_emc').find('input[name=choice]').val("proceed")
					$('#account_emc').find('input[type=submit]').click();
				});	
			{/literal}
		</script>		 
	</form>

{/if}