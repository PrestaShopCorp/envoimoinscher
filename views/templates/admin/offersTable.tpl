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

<div id="tableContent" class="bootstrap">
    {if isset($modifPrice) && $modifPrice == "1"}
    <div class="alert alert-success alert-size conf">
    {l s='Following your modifications, the price has been updated. To trigger the dispatch with the new price, press a second time on the Send button.' mod='envoimoinscher'}
    </div>
    {/if}
    <table class="table fullwidth" cellspacing="0">
      <thead>
        <tr>
		<th>{l s='Carrier' mod='envoimoinscher'}</th>
        <th>{l s='Service' mod='envoimoinscher'}</th>
        <th>{l s='Tax-free price' mod='envoimoinscher'}</th>
        <th>{l s='Tax-included price' mod='envoimoinscher'}</th>
        <th>{l s='Pickup date' mod='envoimoinscher'}</th>
        <th>{l s='Delivery date' mod='envoimoinscher'}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>{$offer.operator.label|escape:'htmlall':'UTF-8'}</td>
          <td>{$offer.service.label|escape:'htmlall':'UTF-8'}</td>
          <td><span id="htIns" {if !$offer.insurance}class="hidden"{/if}>{$offer.priceHT}&nbsp;€</span> <span id="htNoIns"  {if $offer.insurance}class="hidden"{/if}>{$offer.priceHTNoIns}&nbsp;€</span></td>
          <td><span id="ttcIns" {if !$offer.insurance}class="hidden"{/if}>{$offer.priceTTC}&nbsp;€</span> <span id="ttcNoIns"  {if $offer.insurance}class="hidden"{/if}>{$offer.priceTTCNoIns}&nbsp;€</span></td>
          <td>{$offer.collect|escape:'htmlall':'UTF-8'}</td>
          <td>{$offer.delivery|escape:'htmlall':'UTF-8'}</td>
        </tr>
      </tbody>
    </table>
</div>