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

<div id="allOffersTable">
{if isset($isajax) && $isajax == "1" && !$isEMCCarrier}
  <div class="alert alert-danger error_size error">{l s='Carrier not available anymore. You can select a new one (inform your customer about the change) or ' mod='envoimoinscher'}
  <a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderid|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module">{l s='cancel this weight change' mod='envoimoinscher'}</a>.
  </div>
{elseif isset($modifPrice) && $modifPrice == "1"}
<div class="alert alert-danger error_size error">{l s='No carrier found for new caracteristics of the dispatch.' mod='envoimoinscher'}
</div>
{/if}
{if $offersNb > 0}
  <table class="table" cellspacing="0">
    <thead>
      <tr>
        <th>{l s='Carrier' mod='envoimoinscher'}</th>
        <th>{l s='Service' mod='envoimoinscher'}</th>
        <th>{l s='Tax-free price' mod='envoimoinscher'}</th>
        <th>{l s='Tax-included price' mod='envoimoinscher'}</th>
        <th>{l s='Customer price' mod='envoimoinscher'}</th>
        <th>{l s='Pickup date' mod='envoimoinscher'}</th>
        <th>{l s='Delivery date' mod='envoimoinscher'}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      {foreach from=$offers key=o item=offer}
        {if in_array($offer.code, $installedServices)}
      <tr>
        <td>{$offer.operator.label|escape:'htmlall'}</td>
        <td>{$offer.service.label|escape:'htmlall'}</td>
        <td>{$offer['price']['tax-exclusive']|escape:'htmlall'}&nbsp;€</td>
        <td>{$offer['price']['tax-inclusive']|escape:'htmlall'}&nbsp;€</td>
        <td>{$orderInfo.total_shipping|escape:'htmlall'}&nbsp;€ </td>
        <td>{date('d-m-Y', strtotime($offer.collection.date))|escape:'htmlall'}</td>
        <td>{date('d-m-Y', strtotime($offer.delivery.date))|escape:'htmlall'}</td>
        <td><a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderId|escape:'htmlall'}&option=replace&code={$offer.operator.code|escape:'htmlall'}_{$offer.service.code|escape:'htmlall'}&token={$token|escape:'htmlall'}" class="action_module font-size10">{l s='Replace with this offer' mod='envoimoinscher'}</a></td>
      </tr>
        {/if}
      {/foreach}
    </tbody>
  </table>
{/if}
{if isset($isajax)}
<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderid|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module">{l s='Cancel change of shipment characteristics' mod='envoimoinscher'}</a>
{/if}
</div>