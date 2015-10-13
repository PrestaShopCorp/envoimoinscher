{**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA / 2011-2015 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 *}

<div class="support-message">
	<p>{l s='Carrier simple support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall':'UTF-8'}&EMC_tabs=simple_carriers">
	
	{if sizeof($simpleEconomicCarriers)}
		<fieldset id="EMC_carriers" class="baseForm">
			<legend>{l s='Economic offers' mod='envoimoinscher'}</legend>
			{include file="$familTableTpl" offers=$simpleEconomicCarriers disableServices=$disableServices}
		</fieldset>
	{/if}
	
	<br />
	<div class="margin-form submit">
		<input type="submit" name="btnCarriersSimple" value="{l s='Send' mod='envoimoinscher'}" class="btn btn-default" />
	</div>
</form>