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

<table class="offersList table"> 
	<thead> 
		<tr> 
			<th class="offer">{l s='Offers' mod='envoimoinscher'}</th>
			<th class="price">{l s='Restitution prices' mod='envoimoinscher'}</th>
			<th class="desc">{l s='Description' mod='envoimoinscher'}</th>
			<th class="status">{l s='State' mod='envoimoinscher'}</th>
		</tr> 
	</thead> 
	<tbody>
		{if isset($offers) && $offers && sizeof($offers)}
			{foreach from=$offers key=o item=offer}
				{assign var="defaultPP" value="{Configuration::get(strtoupper('EMC_PP_'|cat:$offer.offerCode))}"}
				<tr class="{if in_array($offer.id_eo, $operators)} disabled{/if}">
					<td class="offer">
						<label for="offer{$offer.id_es|escape:'htmlall'}">
							{$offer.name_eo|escape:'htmlall'} (<b>{$offer.label_es|escape:'htmlall'}</b>)
						</label>
						{if $offer.is_parcel_dropoff_point_es == 1 && in_array($offer.id_eo, $operators) == false}
							<div class="parcelPoint {if $offer.id_carrier == ''} hidden{/if}">
								<label for="pp_{$offer.id_es|escape:'htmlall'}">{l s='Deposit parcel point:' mod='envoimoinscher'}</label>
								<input type="text" name="parcel_point[{$offer.offerCode}]" id="pp_{$offer.id_es}" value="{$defaultPP}" placeholder="{l s='Parcel point code' mod='envoimoinscher'}" {if $disableServices} disabled="disabled"{/if}/><br />
								<a data-fancybox-type="iframe" href="{Envoimoinscher::getMapByOpe($offer.code_eo)|escape:'htmlall'}" class="getParcelPoint fancybox">{l s='Get parcel point' mod='envoimoinscher'}</a>
							</div>
						{else}
							<input type="hidden" name="parcel_point[{$offer.offerCode}]" id="pp_{$offer.id_es}" value="POST" {if $disableServices} disabled="disabled"{/if}/><br />
						{/if}
					</td>
					<td class="price">
						{if in_array($offer.id_eo, $operators)}
							<div class="center EMC_error">
								{l s='Disabled for %s' mod='envoimoinscher' sprintf={$nameCategory|unescape:'html'}}
							</div>
						{/if}
						<div id="field2-offer{$offer.id_es}" {if $offer.id_carrier == '' || in_array($offer.id_eo, $operators)} class="hidden"{/if}>
							{foreach from=$pricing key=p item=price}
								<div class="clear">
									<label for="off_{$p|escape:'htmlall'}_{$offer.id_es|escape:'htmlall'}">
										{if $price == "scale"}{l s='Rate' mod='envoimoinscher'}{/if}
										{if $price == "real"}{l s='Real price' mod='envoimoinscher'}{/if}
									</label>
									<input type="radio" name="{$offer.offerCode}_emc" id="off_{$p}_{$offer.id_es}" value="{$price}" {if (($offer.pricing_es == '' || $offer.pricing_es == 1) && $price == 'real') || ($offer.pricing_es != '' && $offer.pricing_es == 0 && $price == 'scale')}checked="checked"{/if} {if $disableServices}disabled="disabled"{/if}/>
								</div>
							{/foreach}
						</div>
					</td>
					<td class="desc">{$offer.desc_es|escape:'htmlall'}</td>
					<td class="status">
						<div class="hide">
							<input type="checkbox" name="offers[]" value="{$offer.offerCode}" id="offer{$offer.id_es}" {if $offer.id_carrier > 0 && in_array($offer.id_eo, $operators) == false} checked="checked"{/if}{if $disableServices} disabled="disabled"{/if} />
						</div>
						<img src="../img/admin/{if $offer.id_carrier > 0 && in_array($offer.id_eo, $operators) == false}enabled{else}disabled{/if}.gif" alt="{if $offer.id_carrier > 0 && in_array($offer.id_eo, $operators) == false}true{else}done{/if}" class="toggleCarrier" onClick="EMCtoggleCarrier($(this))" />
					</td>
				</tr>
			{/foreach}
		{else}
			<tr>
				<td colspan="4">
					<div class="warn">
						{l s='No offers at this time' mod='envoimoinscher'}
					</div>
				</td>
			</tr>
		{/if}
	</tbody> 
</table>
