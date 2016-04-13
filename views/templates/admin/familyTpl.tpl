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

<table class="offersList table"> 
	<thead> 
		<tr>
      <th class="carrier">{l s='Carrier' mod='envoimoinscher'}</th>
			<th class="offer">{l s='Offer' mod='envoimoinscher'}</th>
      <th class="area">{l s='Coverage' mod='envoimoinscher'}</th>
			<th class="departure">{l s='Departure' mod='envoimoinscher'}</th>
      <th class="destination">{l s='Destination' mod='envoimoinscher'}</th>
      <th class="delivery_due_time">{l s='Delay' mod='envoimoinscher'}</th>
			<th class="status">{l s='State' mod='envoimoinscher'}</th>
      <th class="price">{l s='Price' mod='envoimoinscher'}
        <div class="margin-form add-tooltip" title="
             {l s='Real price: the price automatically calculated on boxtale.com.' mod='envoimoinscher'}<br />
             {l s='Fixed price: the price fixed by yourself on the Carriers edition page.' mod='envoimoinscher'}
             ">
        </div>
      </th>
      <th class="edit">{l s='Edit' mod='envoimoinscher'}</th>
		</tr> 
	</thead> 
	<tbody>
		{if isset($carriersByType) && $carriersByType && sizeof($carriersByType)}
            {assign var="index" value=0}
            {foreach from=$carriersByType key=type item=list}
                {foreach from=$list key=o item=offer}
                    {assign var="defaultPP" value="{EnvoimoinscherModel::getConfig(strtoupper('EMC_PP_'|cat:substr($offer.offerCode,-25)))|escape:'htmlall':'UTF-8'}"}
                    {if $offer.delivery_type_es == 1 && $index == $titleCount[$type]}
                        <tr class="delivery_type labelType marker" ><td colspan = 9>{l s='Pickup point' mod='envoimoinscher'}</td></tr>
                    {elseif $offer.delivery_type_es == 2 && $index == $titleCount[$type]}
                        <tr class="delivery_type labelType home" ><td colspan = 9>{l s='On-site standard' mod='envoimoinscher'}</td></tr>
                    {elseif $offer.delivery_type_es == 3 && $index == $titleCount[$type]}        
                        <tr class="delivery_type labelType express" ><td colspan = 9>{l s='Express' mod='envoimoinscher'}</td></tr>
                    {/if}
                    {assign var='index' value=$index+1}
                    <tr class="{if in_array($offer.id_eo, $operators)} disabled{/if}">
                        <td class="operator">{$offer.srv_name_fo_es|escape:'htmlall':'UTF-8'}</td>
                        <td class="offer">
                            <label for="offer{$offer.id_es|escape:'htmlall':'UTF-8'}">
                                {$offer.label_store_es|escape:'htmlall':'UTF-8'}
                            </label>
                            <div class="margin-form add-tooltip" title="
                              {for $i=0 to {$offer.details_es|@count -1}}
                                <span>{$offer.details_es[$i]|escape:'htmlall':'UTF-8'}</span>{if $i < {$offer.details_es|@count -1}}<br />{/if}
                              {/for}">
                            </div>
                            {if $offer.is_parcel_dropoff_point_es == 1 && in_array($offer.id_eo, $operators) == false}
                                <div class="parcelPoint" {if $offer.id_carrier == ''} style="display:none;"{/if}>
                                    <button data-fancybox-type="iframe"  href="{$urlChoixRelais|escape:'htmlall':'UTF-8'}&ope={$offer.code_eo|escape:'htmlall':'UTF-8'}&srv={$offer.code_es|escape:'htmlall':'UTF-8'}&inputCallBack=parcel_point[{$offer.offerCode|escape:'htmlall':'UTF-8'}]"  class="btn btn-default iframe fancybox" {if $disableServices} disabled="disabled"{/if} style="color:#000;">{l s='Get parcel point' mod='envoimoinscher'}</button>
                                    <input type="text" name="parcel_point[{$offer.offerCode|escape:'htmlall':'UTF-8'}]" value="{$defaultPP|escape:'htmlall':'UTF-8'}" placeholder="{l s='Dropoff point' mod='envoimoinscher'}" {if $disableServices} disabled="disabled"{/if}/>
                                </div>
                            {else}
                                <input type="hidden" name="parcel_point[{$offer.offerCode|escape:'htmlall':'UTF-8'}]" id="pp_{$offer.id_es|escape:'htmlall':'UTF-8'}" value="POST" {if $disableServices} disabled="disabled"{/if}/>
                            {/if}
                        </td>
                        <td class="area">
                            {if $offer.zone_fr_es == 1}<span class="zone_fr">{l s='FR' mod='envoimoinscher'}</span>{/if}
                            {if $offer.zone_eu_es == 1}<span class="zone_eu">{l s='EU' mod='envoimoinscher'}</span>{/if}
                            {if $offer.zone_int_es == 1}<span class="zone_int">{l s='INTER' mod='envoimoinscher'}</span>{/if}
                            {if $offer.zone_restriction_es != ''}<div class="margin-form add-tooltip" title="{$offer.zone_restriction_es|escape:'htmlall':'UTF-8'}"></div>{/if}
                        </td>
                        <td class="departure">
                            {if $offer.is_parcel_dropoff_point_es != 0}
                                <span class="ico-marker"></span>
                            {else}
                                <span class="ico-home"></span>
                            {/if}
                            <span class="{if $offer.is_parcel_dropoff_point_es != 0}point{else}home{/if}">{$offer.dropoff_place_es|escape:'htmlall':'UTF-8'}</span>
                        </td>
                        <td class="destination">
                            {if $offer.is_parcel_pickup_point_es == 1}
                                <span class="ico-marker"></span>
                            {else}
                                <span class="ico-home"></span>
                            {/if}
                            <span class="{if $offer.is_parcel_pickup_point_es == 1}point{else}home{/if}">{$offer.pickup_place_es|escape:'htmlall':'UTF-8'}</span>
                        </td>
                        <td class="delivery_due_time"><span>{$offer.delivery_due_time_es|escape:'htmlall':'UTF-8'}</span></td>
                        <td class="status">
                            <div class="hide">
                                <input type="checkbox" name="offers[]" value="{$offer.offerCode|escape:'htmlall':'UTF-8'}" id="offer{$offer.id_es|escape:'htmlall':'UTF-8'}" {if $offer.id_carrier > 0 && in_array($offer.id_eo, $operators) == false} checked="checked"{/if}{if $disableServices} disabled="disabled"{/if} />
                            </div>
                            <img src="../img/admin/{if $offer.id_carrier > 0 && in_array($offer.id_eo, $operators) == false}enabled{else}disabled{/if}.gif" alt="{if $offer.id_carrier > 0 && in_array($offer.id_eo, $operators) == false}true{else}done{/if}" class="toggleCarrier" onClick="EMCtoggleCarrier($(this))" />
                        </td>
                        <td class="price">
                          {if !in_array($offer.id_eo, $operators)}
                            <div id="price_switch_{$offer.id_es|escape:'htmlall':'UTF-8'}" class="EMC_switch switch prestashop-switch clear"{if $offer.id_carrier == '' || in_array($offer.id_eo, $operators)} style="display:none;"{/if}>
                              {foreach from=$pricing key=p item=price}
                                <input type="radio" name="{$offer.offerCode|escape:'htmlall':'UTF-8'}_emc" id="off_{$p|escape:'htmlall':'UTF-8'}_{$offer.id_es|escape:'htmlall':'UTF-8'}" value="{$price|escape:'htmlall':'UTF-8'}" {if (($offer.pricing_es == '' || $offer.pricing_es == 1) && $price == 'real') || ($offer.pricing_es != '' && $offer.pricing_es == 0 && $price == 'scale')}checked="checked"{/if} {if $disableServices}disabled="disabled"{/if} />
                                <label  class="radioCheck {if $price == 'scale'}scale{/if}{if $price == 'real'}real{/if}" for="off_{$p|escape:'htmlall':'UTF-8'}_{$offer.id_es|escape:'htmlall':'UTF-8'}">
                                  {if $price == "scale"}{l s='Rate' mod='envoimoinscher'}{/if}
                                  {if $price == "real"}{l s='Real' mod='envoimoinscher'}{/if}
                                </label>
                              {/foreach}
                              <a class="slide-button btn"></a>
                              <span class="cover"></span>
                            </div>
                            <script>
                              $("#price_switch_{$offer.id_es|escape:'htmlall':'UTF-8'}").on('click', function(event){
                                if (event.currentTarget !== this) {
                                  return;
                                }
                                EMCChangePrice(this);
                              });
                            </script>
                          {else}
                            <div id="disabled-carrier">
                              <p>{$nameCategory|escape:'htmlall':'UTF-8'}</p>
                            </div>
                          {/if}
                        </td>
                        <td class="edit">
                            <div class="btn-group-action">
                                <div class="btn-group">
                                    <a href="{$carrierControllerUrl|escape:'htmlall':'UTF-8'}&id_carrier={$offer.id_carrier|escape:'htmlall':'UTF-8'}" title="{l s='Edit' mod='envoimoinscher'}" class="edit btn btn-default" target="_blank" {if $offer.id_carrier == ""} disabled {/if} >
                                        <i class="icon-edit"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="disable-edit">-</div>
                        </td>
                    </tr>
                {/foreach}
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
  
<style type="text/css">
    /* fixed size fancybox */
    .fancybox-skin, .fancybox-outer, .fancybox-inner, .fancybox-frame{
        width:1150px !important;
        height:605px !important;
    }
</style>
<script type="text/javascript">
  $(document).ready(function() {
      var activeCarrier = $("img.toggleCarrier");
      activeCarrier.each(function() {
        if($(this).attr('alt') === "true") {
          $(this).parent().parent().find(".edit .disable-edit").addClass("hide");
        } else {
          $(this).parent().parent().find(".edit .btn-group-action").addClass("hide");
        }
      });
    });
    function EMCChangePrice(price) {
      var price_scale = $(price.querySelector("input")).attr("checked") != undefined;
      if(price_scale) {
        $(price.querySelector("input")).removeAttr("checked");
        $(price.querySelectorAll("input")[1]).attr("checked", "checked");
      }
      else {
        $(price.querySelectorAll("input")[1]).removeAttr("checked");
        $(price.querySelector("input")).attr("checked", "checked");
      }
    }
</script>
