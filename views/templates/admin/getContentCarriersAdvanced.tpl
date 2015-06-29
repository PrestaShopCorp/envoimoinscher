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
	<p>{l s='Carrier advanced support message' mod='envoimoinscher'}</p>
</div>
<form method="POST" action="{$EMC_link|escape:'htmlall':'UTF-8'}&EMC_tabs=advanced_carriers">
	<ul id="menu_tabs">
		<li class="selected" data-tab="0">{l s='Choice carriers' mod='envoimoinscher'}</li>
		<li data-tab="1"><u>{l s='Weight settings' mod='envoimoinscher'}</u></li>
	</ul>
	<div id="tabs">
		<div class="tab">
			{if sizeof($advancedExpressCarriers)}
				<fieldset id="EMC_carriers" class="baseForm">
					<legend>{l s='Express offers' mod='envoimoinscher'}</legend>
					{include file="$familTableTpl" offers=$advancedExpressCarriers disableServices=$disableServices}
				</fieldset>
			{/if}
		</div>
		<div class="tab">
			<p>
				{l s='On this page, you can customize the maximum size of your shipments by weight ranges. We recommend that you perform this customization work because the dimensions are one of the key criteria for the award of the tender of delivery. Indicate and the most common sizes for the most realistic prices.' mod='envoimoinscher'}
				<br /><br />
				{l s='Without customization, it is the default dimensions, developed by Envoimoinscher, will be displayed.' mod='envoimoinscher'}
			</p>
			<table class="table offersList">
				<thead>
					<tr>
						<th class="center">
							 #
						</th>
						<th class="center">
							{l s='Weight to' mod='envoimoinscher'}
						</th>
						<th class="center">
							{l s='Length max' mod='envoimoinscher'}
						</th>
						<th class="center">
							{l s='Width max' mod='envoimoinscher'}
						</th>
						<th class="center">
							{l s='Height max' mod='envoimoinscher'}
						</th>
					</tr>
				</thead>
				<tbody>
					{if isset($dims) && $dims && sizeof($dims)}
						{foreach from=$dims key=d item=dim}
							<tr>
								<td class="center">
									<span class="big">{$d+1|intval}</span>
								</td>
								<td class="center">
									<input type="text" name="weight{$d+1|intval}" id="weight{$d+1|intval}" value="{$dim.weight_ed|intval}" class="smallInput" /> <span>kg</span>
								</td>
								<td class="center">
									<input type="text" name="length{$d+1|intval}" id="length{$d+1|intval}" value="{$dim.length_ed|intval}" class="smallInput" /> <span>cm</span>
								</td>
								<td class="center">
									<input type="text" name="width{$d+1|intval}" id="width{$d+1|intval}" value="{$dim.width_ed|intval}" class="smallInput" /> <span>cm</span>
								</td>
								<td class="center">
									<input type="text" name="height{$d+1|intval}" id="height{$d+1|intval}" value="{$dim.height_ed|intval}" class="smallInput" /> <span>cm</span>
									<input type="hidden" name="id{$d+1|intval}" id="id{$d+1|intval}" value="{$dim.id_ed|intval}" />
								</td>
							</tr>
						{/foreach}
					{/if}
					<input type="hidden" name="countDims" id="countDims" value="{sizeof($dims)|intval}" />
				</tbody>
			</table>
		</div>
	</div>
	<br />
	<div class="margin-form">
		<input type="submit" name="btnCarriersAdvanced" value="{l s='Send' mod='envoimoinscher'}" class="btn btn-default" />
	</div>
</form>
<script type="text/javascript">
	$(function() {
		$("#menu_tabs li").click(function(){
			$("#menu_tabs li").removeClass('selected');
			$("#tabs .tab").hide();
			$("#tabs .tab:eq("+$(this).data('tab')+")").show();
			$(this).addClass('selected');
		});
	});
</script>