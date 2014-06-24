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

	<table id="ORDERSTABLE{$id|escape:'htmlall'}" class="table" cellspacing="0" cellpadding="0" style="width:100%;">
		<thead>
			<tr class="small">
				<th><input type="checkbox" name="selectAll{$id|escape:'htmlall'}" id="selectOrDeselectAll{$id|escape:'htmlall'}" class="deselectAll" checked="checked" value="{$id|escape:'htmlall'}" /></th>
				<th>Destinataire</th>
				<th><p style="width:160px;">Dates prévues</p></th> 
				<th><p style="width:100px;">Statut</p></th>
				<th><p style="width:70px;">Coût transport <br />réel (TTC, €)</p></th>
				<th><p style="width:70px;">Coût transport <br />client (TTC)</p></th>
				<th>Montant total</th>
				<th>Référence EnvoiMoinsCher</th>
				<th>Transporteur <br />(Offre)</th>
				<th>Fiche commande</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$orders key=o item=order}
			<tr id="row-{$order.idOrder}" {if $o%2 != 0}class="row"{/if}>
				<td style="padding: 4px 6px">{$order.idOrder|escape:'htmlall'}
					<span id="checkbox-{$order.idOrder}" {if $order.generated_ed == "0"}style="display:none;"{/if}><input type="checkbox" checked="checked" name="orders[]" id="order-{$order.idOrder}" value="{$order.idOrder}" /></span>
				</td>
				<td>{$order.firstNameShort|escape:'htmlall'}. {$order.lastname|escape:'htmlall'}</td>
				<td>
					<br />Collecte : {$order.dateCol|escape:'htmlall'}
					<br />Livraison : {$order.dateDel|escape:'htmlall'}
					<br />Commande EnvoiMoinsCher : {$order.dateCom|escape:'htmlall'}
				</td>
				<td style="width:100px;">
					{if $type == "error"}
					<p>Erreurs : {$order.errors_eoe|escape:'htmlall'}</p>
					{else}
					{$order.name|escape:'htmlall'} 
					{/if} 
				</td> 
				<td>{$order.priceRound|escape:'htmlall'}</td>
				<td>{$order.total_shipping|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
				<td>{$order.total_paid|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
				<td>{$order.ref_emc_eor|escape:'htmlall'}</td>
				<td>{if isset($order.carrierName)}{$order.carrierName}{/if}</td>
				<td><a href="index.php?controller=AdminOrders&id_order={$order.idOrder|escape:'htmlall'}&vieworder&token={$tokenOrder|escape:'htmlall'}" class="action_module">Voir</a></td>
				<td>
					{if $order.date_order_eor != ''} 
					{if $order.generated_ed == "0"}
					<span id="labelgen{$order.idOrder}">bordereau{if $order.parcels_eor > 1}x{/if} en cours de génération</span>
					{/if}  
					<span id="label{$order.idOrder}" {if $order.generated_ed == "0"}style="display:none;"{/if}>
						<a href="{if $order.parcels_eor > 1}index.php?controller=AdminEnvoiMoinsCher&option=download&token={$token}&order={$order.idOrder}{else}{$order.link_ed}{/if}" class="action_module" target="_blank">télécharger bordereau{if $order.parcels_eor > 1}x{/if}</a> 
						<br /><br /><a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=tracking&token={$token|escape:'htmlall'}" class="action_module openTrackPopup" target="_blank">suivre l'envoi</a> 
					</span>
					{else}
					<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module">expédier</a>
					{/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>