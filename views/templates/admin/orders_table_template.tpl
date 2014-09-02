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

	<div class="table-responsive clearfix">
		<table class="table order" id="ORDERSTABLE{$id|escape:'htmlall'}" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="fixed-width-xs"></th>
					<th class="fixed-width-xs text-center"><span class="title_box active text-center">{l s='ID' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='recipient' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Pickup' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Delivery' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Command' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='status' mod='envoimoinscher'}</span></th>
					<th class="text-right"><span class="title_box text-right">{l s='Real price (TTC)' mod='envoimoinscher'}</span></th>
					<th class="text-right"><span class="title_box text-right">{l s='Client price (TTC)' mod='envoimoinscher'}</span></th>
					<th class="text-right"><span class="title_box text-right">{l s='Total(TTC)' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='EMC reference' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='carrier (offer)' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box text-center">{l s='Record' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box text-center">{l s='Action' mod='envoimoinscher'}</span></th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$orders key=o item=order}
				<tr id="row-{$order.idOrder|escape:'htmlall'}">
					<td class="text-center">
						<span id="checkbox-{$order.idOrder}" class="{if $order.generated_ed == "0"}hidden{/if}"><input type="checkbox" checked="checked" name="orders[]" id="order-{$order.idOrder}" value="{$order.idOrder}" /></span>
					</td>
					<td class="text-center">{$order.idOrder|escape:'htmlall'}</td>
					<td>{$order.firstNameShort|escape:'htmlall'}. {$order.lastname|escape:'htmlall'}</td>
					<td>{$order.dateCol|escape:'htmlall'}</td>
					<td>{$order.dateDel|escape:'htmlall'}</td>
					<td>{$order.dateCom|escape:'htmlall'}</td>
					<td>
						{if $type == "error"}
						<p>{l s='Errors' mod='envoimoinscher'}  : {$order.errors_eoe|escape:'htmlall'}</p>
						{else}
						{$order.name|escape:'htmlall'} 
						{/if} 
					</td> 
					<td class="text-right">{$order.priceRound|escape:'htmlall'}</td>
					<td class="text-right">{$order.total_shipping|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
					<td class="text-right">{$order.total_paid|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
					<td>{$order.ref_emc_eor|escape:'htmlall'}</td>
					<td>{if isset($order.carrierName)}{$order.carrierName}{/if}</td>
					<td class="text-center"><a target="_blank" href="index.php?controller=AdminOrders&id_order={$order.idOrder|escape:'htmlall'}&vieworder&token={$tokenOrder|escape:'htmlall'}" class="btn btn-default action_module"><i class="icon-file-text"></i> {l s='Display' mod='envoimoinscher'}</a></td>
					<td class="text-center">
						{if $order.date_order_eor != ''} 
						{if $order.generated_ed == "0"}
						<span id="labelgen{$order.idOrder|escape:'html'}">
						{if $order.parcels_eor > 1}
							{l s='slips currenttly generating' mod='envoimoinscher'}
						{else}
							{l s='slip currenttly generating' mod='envoimoinscher'}
						{/if}
						</span>
						{/if}  
						<span id="label{$order.idOrder}" class="{if $order.generated_ed == "0"}hidden{/if}">
							<a href="{if $order.parcels_eor > 1}index.php?controller=AdminEnvoiMoinsCher&option=download&token={$token}&order={$order.idOrder}{else}{$order.link_ed}{/if}" class="action_module btn btn-default" target="_blank">
							{if $order.parcels_eor > 1}
								{l s='download slips' mod='envoimoinscher'}
							{else}
								{l s='download slip' mod='envoimoinscher'}
							{/if}
							</a> 
							<br /><br />
							<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=tracking&token={$token|escape:'htmlall'}" class="action_module openTrackPopup btn btn-default" target="_blank">
								{l s='track shipment' mod='envoimoinscher'}
							</a> 
						</span>
						{else}
						<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module btn btn-default">
							<i class="icon-truck"></i> {l s='ship' mod='envoimoinscher'}
						</a>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>