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

  <div class="filters clearfix">
		<fieldset class="filters filters-box">
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='ID' mod='envoimoinscher'}</label>
				<input type="text" name="filter_id_order" class="widthauto-important" size="4" {if isset($filters.filter_id_order)}value={$filters.filter_id_order}{/if}>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Keyword' mod='envoimoinscher'}</label>
				<input type="text" name="recipient" class="widthauto-important" placeholder="{l s='Recipient name or email' mod='envoimoinscher'}" size="30" {if isset($filters.recipient)}value="{' '|implode:$filters.recipient}"{/if}>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Carrier' mod='envoimoinscher'}</label>
				<select name="carriers" class="widthauto-important">
					<option value="all" {if !isset($filters.carriers) || $filters.carriers == "all"}selected{/if}>{l s='Show all' mod='envoimoinscher'}</option>
					{foreach from=$enabledCarriers key=k item=v}
						<option value="{$v['name']}" {if isset($filters.carriers) && $filters.carriers == $v['name']}selected{/if}>{$v['name']}</option>
					{/foreach}
					<option value="del" {if isset($filters.carriers) && $filters.carriers == "del"}selected{/if}>{l s='Deleted carriers' mod='envoimoinscher'}</option>
				</select>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Creation date' mod='envoimoinscher'}</label>
				<div class="input-group fixed-width-md">
					<input type="text" class="filter date-input form-control datepicker start_creation_date" name="start_creation_date" placeholder="{l s='From' mod='envoimoinscher'}" {if isset($filters.start_creation_date)}value={$filters.start_creation_date|date_format:"%Y-%m-%d"}{/if}>
					<span class="input-group-addon">
						<i class="icon-calendar"></i>
					</span>
				</div>
				<div class="input-group pt4 fixed-width-md">
					<input type="text" class="filter date-input form-control datepicker end_creation_date" name="end_creation_date" placeholder="{l s='To' mod='envoimoinscher'}" {if isset($filters.end_creation_date)}value={$filters.end_creation_date|date_format:"%Y-%m-%d"}{/if}>
					<span class="input-group-addon">
						<i class="icon-calendar"></i>
					</span>
				</div>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Order date' mod='envoimoinscher'}</label>
				<div class="input-group fixed-width-md">
					<input type="text" class="filter date-input form-control datepicker start_order_date" name="start_order_date" placeholder="{l s='From' mod='envoimoinscher'}" {if isset($filters.start_order_date)}value={$filters.start_order_date|date_format:"%Y-%m-%d"}{/if}>
					<span class="input-group-addon">
						<i class="icon-calendar"></i>
					</span>
				</div>
				<div class="input-group pt4 fixed-width-md">
					<input type="text" class="filter date-input form-control datepicker end_order_date" name="end_order_date" placeholder="{l s='To' mod='envoimoinscher'}" {if isset($filters.end_order_date)}value={$filters.end_order_date|date_format:"%Y-%m-%d"}{/if}>
					<span class="input-group-addon">
						<i class="icon-calendar"></i>
					</span>
				</div>
			</div>
			<div class="inline-block mt20">
				<button class="btn btn-default filter get-filter">
					<i class="icon-search"></i>
					{l s='Filter' mod='envoimoinscher'}
				</button>
				<button class="btn btn-default filter reset-filter">
					{l s='Reset filter' mod='envoimoinscher'}
				</button>
			</div>
		</fieldset>
	</div>
	<div class="table-responsive clearfix">
		<table class="table order" id="ORDERSTABLE{$id|escape:'htmlall'}" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="fixed-width-xs"><span class="title_box text-center"><input id="selectOrDeselectAll{$id|escape:'htmlall'}" type="checkbox" checked="checked" /></span></th>
					<th class="fixed-width-xs text-center"><span class="title_box active text-center">{l s='ID' mod='envoimoinscher'}</span></th>
          <th><span class="title_box text-center">{l s='First name' mod='envoimoinscher'}</span></th>
					<th><span class="title_box text-center">{l s='Last name' mod='envoimoinscher'}</span></th> 
					<th><span class="title_box text-center">{l s='Email' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Creation' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Command' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Pickup' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box">{l s='Delivery' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='Status' mod='envoimoinscher'}</span></th>
					<th class="text-right"><span class="title_box text-right">{l s='Real price (ATI)' mod='envoimoinscher'}</span></th>
					<th class="text-right"><span class="title_box text-right">{l s='Customer price (ATI)' mod='envoimoinscher'}</span></th>
					<th class="text-right"><span class="title_box text-right">{l s='Total (ATI)' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='EMC reference' mod='envoimoinscher'}</span></th>
					<th><span class="title_box">{l s='Carrier (offer)' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box text-center">{l s='Order card' mod='envoimoinscher'}</span></th>
					<th class="text-center"><span class="title_box text-center">{l s='Action' mod='envoimoinscher'}</span></th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$orders key=o item=order}
				<tr id="row-{$order.idOrder|escape:'htmlall'}">
					<td class="text-center">
						<span id="checkbox-{$order.idOrder}" class="{if !array_key_exists($order.id_order, $orderDocuments)}hidden{/if}"><input type="checkbox" checked="checked" name="orders[]" id="order-{$order.idOrder}" value="{$order.idOrder}" /></span>
					</td>
					<td class="text-center">{$order.idOrder|escape:'htmlall'}</td>
					<td class="text-center">{$order.firstname|escape:'htmlall'}</td>
					<td class="text-center">{$order.lastname|escape:'htmlall'}</td>
          <td class="text-center">{$order.email|escape:'htmlall'}</td>
					<td class="text-center">{$order.dateAdd|escape:'htmlall'}</td>
					<td class="text-center">{$order.dateCom|escape:'htmlall'}</td>
          <td class="text-center">{$order.dateCol|escape:'htmlall'}</td>
					<td class="text-center">{$order.dateDel|escape:'htmlall'}</td>
					<td class="breakable-cell">
						{if $type == "error"}
						<p>{l s='Errors' mod='envoimoinscher'}  : {$order.errors_eoe|escape:'htmlall'}</p>
						{else}
						{$order.name|escape:'htmlall'} 
						{/if} 
					</td> 
					<td class="text-right">{$order.priceRound|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
					<td class="text-right">{$order.total_shipping|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
					<td class="text-right">{$order.total_paid|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
					<td>{$order.ref_emc_eor|escape:'htmlall'}</td>
					<td>{if isset($order.carrierName)}{$order.carrierName}{/if}</td>
					<td class="text-center"><a target="_blank" href="index.php?controller=AdminOrders&id_order={$order.idOrder|escape:'htmlall'}&vieworder&token={$tokenOrder|escape:'htmlall'}" class="btn btn-default action_module"><i class="icon-file-text"></i> {l s='Display' mod='envoimoinscher'}</a></td>
					<td class="text-center">
						{if $order.date_order_eor != ''} 
							<span id="label{$order.idOrder|escape:'htmlall'}">
								{if !array_key_exists($order.id_order, $orderDocuments)}
									<div class="documents label-not-generated" order-id="{$order.idOrder|escape:'htmlall'}">
										{if $order.parcels_eor > 1}
											{l s='Slips currently generating' mod='envoimoinscher'}
										{else}
											{l s='Slip currently generating' mod='envoimoinscher'}
										{/if}
									</div>
									<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=tracking&token={$token|escape:'htmlall'}" class="action_module table-action hidden openTrackPopup btn btn-default" target="_blank">
										{l s='Track shipment' mod='envoimoinscher'}
									</a> 
								{else}  
									<div class="documents" order-id="{$order.idOrder|escape:'htmlall'}">
										{foreach from=$orderDocuments[$order.idOrder] key=name item=url}
											<a href="{$url|escape:'htmlall'}" class="doc-{$name|escape:'htmlall'} table-action action_module btn btn-default" target="_blank">{l s="download $name" mod='envoimoinscher'}</a><br/>
										{/foreach}
									</div>
									<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=tracking&token={$token|escape:'htmlall'}" class="action_module table-action openTrackPopup btn btn-default" target="_blank">
										{l s='Track shipment' mod='envoimoinscher'}
									</a> 
								{/if}
							</span>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>