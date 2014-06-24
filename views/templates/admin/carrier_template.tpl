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

  <div class="delivery_option {$offer.class|escape:'htmlall'}item emcCarrierContainer" style="{$offer.style|escape:'htmlall'}">
    <input id="id_carrier{$offer.id_carrier}{$id_address}" type="radio" name="delivery_option[{$id_address}]" class="delivery_option_radio emcCarrierRadio {if isset($offer.isParcelPoint) && $offer.isParcelPoint}hasParcelPoint{/if}"
    {$offer.checked} {if isset($offer.isParcelPoint) && $offer.isParcelPoint} onclick="handleParcelPoint('{$offer.operator.code}', '{$offer.id_carrier}', {$offer.priceTTC_db}, {$offer.priceHT_db}, {$id_address});" {else} {*if $opc*} onclick="makeOpeChecked('{$offer.id_carrier}', {$offer.priceTTC_db}, {$offer.priceHT_db});" {*onchange="updateCarrierSelectionAndGift();"*} {*/if*} {/if} 	value="{$offer.id_carrier|intval}" />
    <!-- <label for="delivery_option_{$id_address}_{$offer.id_carrier|intval}"> -->
    <!-- MOD TRE -->
    <label for="id_carrier{$offer.id_carrier|escape:'htmlall'}{$id_address|escape:'htmlall'}">
      <table class="resume">
        <tr>
          <td class="delivery_option_logo"> 
            {$offer.offerLogo|escape:'htmlall'}
            {if isset($offer.isParcelPoint) && $offer.isParcelPoint}
              {if $offer.isParcelPointError == 1}
              <div style="margin:0; padding:5px; border:1px solid red;"><ul><li style="list-style:none; padding-left:0;">Veuillez choisir votre point relais</li></ul></div> 
              {/if}
              <br /><a href="#" id="loadClick{$offer.id_carrier}{$id_address}" onclick="selectPoint('{$offer.id_carrier|intval}', this, '{$offer.operator.code}', '{$id_address}');return false;">&raquo;  S&eacute;lectionnez votre point relais</a>
              <div id="loaderPoints{$offer.id_carrier|intval}{$id_address}" style="display:none;">
                <br /><img src="{$loaderSrc|escape:'htmlall'}" alt="" style="margin-top:3px; margin-right:4px;" /> Veuillez patienter...
              </div>
              <div id="points{$offer.id_carrier|intval}{$id_address}" class="pointsUl" style="display:none;">
                <p><b>Veuillez choisir le point de retrait de votre colis. Vous pouvez visualiser les points relais sur <a href="#" id="openMap" onclick="javascript:makeMap('{$offer.operator.code}', '{$offer.id_carrier|intval}', '{$id_address}');return false;">cette carte</a></b></p>
                <ul></ul>
              </div>
              <input type="hidden" name="pointsList{$offer.id_carrier|intval}{$id_address}" id="pointsList{$offer.id_carrier|intval}{$id_address}" value="{$offer.pointsList}" />
<script type="text/javascript">
var point{$offer.id_carrier|escape:'htmlall'} = false;
opeCodes[{$offer.id_carrier|escape:'htmlall'}] = "{$offer.operator.code|escape:'htmlall'}";
</script>
            {else}
<script type="text/javascript">
var point{$offer.id_carrier|escape:'htmlall'} = true;
opeCodes[{$offer.id_carrier|escape:'htmlall'}] = "{$offer.operator.code}";
</script>
            {/if}
          </td>
          <td>
            <div class="delivery_option_title">{$offer.offerTitle|escape:'htmlall'}</div>
            <div class="delivery_option_delay">{$offer.descriptionLocal|escape:'htmlall'}{if $offer.deliveryDate != ''}<br /><br />{$offer.deliveryDate}{/if}</div>
          </td>
          <td>
            <!-- <div class="delivery_option_price">{if $offer.priceTTC_db > 0}<span class="price">{convertPrice price=$offer.priceTTC_db}  TTC </span>{else} Gratuit&nbsp;! {/if}</div> -->

            <div class="delivery_option_price">{if $offer.priceTTC_db > 0}<span class="price">{convertPrice price=$offer.priceTTC_db}  TTC </span>{else} Gratuit&nbsp;! {/if}</div> <!-- ADD TRE -->
            
            {if $offer.checked != ""}
            <script type="text/javascript">
            jQuery(document).ready(function() {
              makeOpeChecked('{$offer.id_carrier|escape:'htmlall'}', {$offer.priceTTC_db|escape:'htmlall'}, {$offer.priceHT_db|escape:'htmlall'});
            });
            </script>
            {/if}
            {if $offer.freeDelivery}
            <script type="text/javascript">
            jQuery(document).ready(function() {
              $('#total_shipping').html(0);
            });
            </script>
            {/if}
          </td>
        </tr> 
      </table>
    </label>
  </div>