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
 * @copyright 2007-2015 PrestaShop SA / 2011-2014 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 *}

	<div class="filters clearfix">
		<fieldset class="filters-box filters">
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='ID' mod='envoimoinscher'}</label>
				<input type="text" name="filter_id_order" class="widthauto-important" size="4" {if isset($filters.filter_id_order)}value={$filters.filter_id_order}{/if}>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Keyword' mod='envoimoinscher'}</label>
				<input type="text" name="recipient" class="widthauto-important" placeholder="{l s='Recipient name or email' mod='envoimoinscher'}" size="30" {if isset($filters.recipient)}value="{' '|implode:$filters.recipient}"{/if}>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Order type' mod='envoimoinscher'}</label>
				<select name="type_order" class="widthauto-important">
					<option value="all" {if !isset($filters.type_order) || $filters.type_order == "all"}selected{/if}>{l s='Show all' mod='envoimoinscher'}</option>
					<option value="0" {if isset($filters.type_order) && $filters.type_order == "0"}selected{/if}>{l s='EnvoiMoinsCher orders' mod='envoimoinscher'}</option>
					<option value="1" {if isset($filters.type_order) && $filters.type_order == "1"}selected{/if}>{l s='Non EnvoiMoinsCher orders' mod='envoimoinscher'}</option>
					<option value="2" {if isset($filters.type_order) && $filters.type_order == "2"}selected{/if}>{l s='Invalid or incomplete orders' mod='envoimoinscher'}</option>
				</select>
				<div class="widthauto-important font-size10 pl2">{l s='Mass order will only be available if' mod='envoimoinscher'}<br />{l s='you select a type.' mod='envoimoinscher'}</div>
			</div>
			<div class="inline-block valigntop mr5">
				<label class="widthauto-important float-none-important">{l s='Status' mod='envoimoinscher'}</label>
				<select name="status[]" class="widthauto-important" multiple size=3>
					{foreach from=$states key=k item=v}
						<option value="{$v['id_order_state']|escape:'htmlall'}" 
						{if (isset($filters.status) && $v['id_order_state']|in_array:$filters.status)}selected
						{elseif !isset($filters.status)}
							{if $v['id_order_state'] == $defaultStatus}selected
							{/if}
						{/if}
						>{$v['name']|escape:'htmlall'}</option>
					{/foreach}
				</select>
				<div class="widthauto-important font-size10 pl2">{l s='Use ctrl clic to select multiple values' mod='envoimoinscher'}</div>
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
				<label class="widthauto-important float-none-important">{l s='Date' mod='envoimoinscher'}</label>
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
					{l s='Reset to default filter' mod='envoimoinscher'}
				</button>
        <button class="btn btn-default filter no-filter">
					{l s='All orders' mod='envoimoinscher'}
				</button>
        <div class="font-size10 pl2">{l s="You can change default filter settings on the" mod='envoimoinscher'}<a href="{$configPage|escape:'htmlall'}">{l s="EnvoiMoinsCher module configuration page" mod='envoimoinscher'}</a>.</div>
			</div>
		</fieldset>
	</div>
	<div class="table-responsive clearfix">
		{if $showTable}
			<table class="table order" 
					{if !isset($filters) || !isset($filters.type_order) || $filters.type_order == "all"}
						id="ORDERSTABLE"
					{else}
							{if $filters.type_order == 1}
									id="ORDERSTABLE2"
								{else if $filters.type_order == 2}
									id="ORDERSTABLE3"
								{else}
									id="ORDERSTABLE1"
							{/if} 
					{/if}
					cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						{if !isset($filters) || !isset($filters.type_order) || $filters.type_order == "all"}
							<th class="fixed-width-xs"></th>
						{else}
								{if $filters.type_order == 1}
									<th class="fixed-width-xs"><span class="title_box text-center"><input id="selectOrDeselectAll2" type="checkbox" checked="checked" /></span>
									<input type="hidden" name="type" value="withoutEmc" />
									<input type="hidden" name="typeDb" value="2" />
								{else if $filters.type_order == 2}
									<th class="fixed-width-xs"><span class="title_box text-center"><input id="selectOrDeselectAll3" type="checkbox" checked="checked" /></span>
									<input type="hidden" name="type" value="errors" />
									<input type="hidden" name="typeDb" value="3" />
								{else}
									<th class="fixed-width-xs"><span class="title_box text-center"><input id="selectOrDeselectAll1" type="checkbox" checked="checked" /></span>
									<input type="hidden" name="type" value="withEmc" />
									<input type="hidden" name="typeDb" value="1" />
								{/if} 
							</th>
						{/if}
						<th class="fixed-width-xs text-center"><span class="title_box active text-center">{l s='ID' mod='envoimoinscher'}</span></th>	
						<th><span class="title_box text-center">{l s='First name' mod='envoimoinscher'}</span></th>
						<th><span class="title_box text-center">{l s='Last name' mod='envoimoinscher'}</span></th> 
						<th><span class="title_box text-center">{l s='Email' mod='envoimoinscher'}</span></th>
						<th><span class="title_box text-center">{l s='Order Type' mod='envoimoinscher'}</span></th>
						{$test_error = false}
						{foreach from=$orders key=o item=order}
							{if $order.errors_eoe != ""}
								{$test_error = true}
							{/if}
						{/foreach}
						{if $test_error == true}
						<th><span class="title_box text-center">{l s='Error' mod='envoimoinscher'}</span></th>
						{/if}
						<th><span class="title_box text-center">{l s='Status' mod='envoimoinscher'}</span></th>
						<th class="text-right"><span class="title_box text-right">{l s='Customer price (ATI)' mod='envoimoinscher'}</span></th>
						<th class="text-right"><span class="title_box text-right">{l s='Total (ATI)' mod='envoimoinscher'}</span></th>
						<th><span class="title_box">{l s='Carrier (offer)' mod='envoimoinscher'}</span></th>
						<th><span class="title_box text-center">{l s='Date' mod='envoimoinscher'}</span></th>
						<th class="text-center"><span class="title_box text-center">{l s='Order card' mod='envoimoinscher'}</span></th>
						<th class="text-center"><span class="title_box text-center">{l s='Action' mod='envoimoinscher'}</span></th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$orders key=o item=order}
					<tr id="row-{$order.idOrder|escape:'htmlall'}">
						<td class="text-center">
							<span id="checkbox-{$order.idOrder}" class="{if $order.generated_ed == "0"}hidden{/if}"><input type="checkbox" 
								{if !isset($filters) || !isset($filters.type_order) || $filters.type_order == "all"}
								{else}checked="checked" 
								{/if}
							name="orders[]" id="order-{$order.idOrder|escape:'htmlall'}" value="{$order.idOrder|escape:'htmlall'}" /></span>
						</td>
						<td class="text-center">{$order.idOrder|escape:'htmlall'}</td>
						<td class="text-center">{$order.toFirstname|escape:'htmlall'}</td>
						<td class="text-center">{$order.toLastname|escape:'htmlall'}</td>
						<td class="text-center">{$order.email|escape:'htmlall'}</td>
						<td class="text-center">
							{if $order.external_module_name != "envoimoinscher"}
								<span class="">{l s='Other' mod='envoimoinscher'}</span>
							{else}
								<span class="">{l s='EnvoiMoinsCher' mod='envoimoinscher'}</span>
							{/if} 
						</td>
						{if $test_error == true}
							<td>
								{if $order.errors_eoe != ""}
									<span class="red_color">{l s='Errors' mod='envoimoinscher'} : {$order.errors_eoe|escape:'htmlall'}</span>
								{/if}
							</td>
						{/if}
						<td>{$order.name|escape:'htmlall'}</td>
						<td class="text-right">{$order.total_shipping|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
						<td class="text-right">{$order.total_paid|escape:'htmlall'}&nbsp;{$order.sign|escape:'htmlall'}</td>
						<td>{if isset($order.carrierName)}{$order.carrierName}{/if}</td>
						<td>{if isset($order.order_date_add)}{$order.order_date_add}{/if}</td>
						<td class="text-center"><a target="_blank" href="index.php?controller=AdminOrders&id_order={$order.idOrder|escape:'htmlall'}&vieworder&token={$tokenOrder|escape:'htmlall'}" class="btn btn-default action_module"><i class="icon-file-text"></i> {l s='Display' mod='envoimoinscher'}</a></td>
						<td class="text-center">
							<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$order.idOrder|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module btn btn-default">
								<i class="icon-truck"></i> {l s='Send' mod='envoimoinscher'}
							</a>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		{else}
			<p>{l s='No order found' mod='envoimoinscher'}</p>
		{/if}
	</div>