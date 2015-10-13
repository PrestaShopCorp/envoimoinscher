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

{if $multiSize > 1}
	<div class="nomargin mt15 mb15">
		<fieldset class="width400">
			<legend><img src="../img/admin/delivery.gif" alt="Point relais">{l s='Multiparcel' mod='envoimoinscher'}</legend>
			<div class="floatleft">{l s='Multiparcel' mod='envoimoinscher'} : <b>{$multiSize|escape:'htmlall':'UTF-8'} {l s='parcel' mod='envoimoinscher'}</b> (
				{foreach from=$multiParcels key=p item=parcel name=parcels}
					{$parcel.weight_eop} kg {if !$smarty.foreach.parcels.last},{/if}
				{/foreach} ).
			</div>
		</legend>
	</fieldset>
</div>
{/if}
{if isset($point)}
<div class="nomargin mt15 mb15">
	<fieldset class="width400">
		<legend><img src="../img/admin/delivery.gif" alt="Point relais">{l s='Arrival parcel point' mod='envoimoinscher'}</legend>
		<div class="floatleft width200">
			{$point.name|escape:'htmlall':'UTF-8'}<br />
			{$point.address|escape:'htmlall':'UTF-8'}<br />
			{$point.zipcode|escape:'htmlall':'UTF-8'} {$point.city|escape:'htmlall':'UTF-8'} 
		</div>
		<div class="floatleft">
			{foreach from=$schedule key=d item=day}
			{$day}<br />
			{/foreach}
		</div>
	</legend>
</fieldset>
</div>
{/if}