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

<div id="allOffersTable">
{if isset($isajax) && $isajax == "1" && !$isEMCCarrier}
  <div class="alert error" style="width:400px;">L'offre choisie n'est plus disponible. Vous pouvez en sélectionner une nouvelle (prévenez votre client du changement de l'offre) ou 
  <a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderid|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module">annuler ce changement du poids</a>.
  </div>
{elseif isset($modifPrice) && $modifPrice == "1"}
<div class="alert error" style="width:400px;">Aucune offre trouvée avec les nouvelles caractéristiques définies sur l'envoi.
</div>
{/if}
{if $offersNb > 0}
  <table class="table" cellspacing="0" style="width:870px;">
    <thead>
      <tr>
        <th>Transporteur</th>
        <th>Service</th>
        <th>Prix HT</th>
        <th>Prix TTC</th>
        <th>Prix facturé<br />au client TTC</th>
        <th>Date de collecte</th>
        <th>Date de livraison</th>
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
        <td><a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderId|escape:'htmlall'}&option=replace&code={$offer.operator.code|escape:'htmlall'}_{$offer.service.code|escape:'htmlall'}&token={$token|escape:'htmlall'}" style="font-size:10px;" class="action_module">remplacer par cette offre</a></td>
      </tr>
        {/if}
      {/foreach}
    </tbody>
  </table>
{/if}
{if isset($isajax)}
<a href="index.php?controller=AdminEnvoiMoinsCher&id_order={$orderid|escape:'htmlall'}&option=send&token={$token|escape:'htmlall'}" class="action_module">annuler le changement sur les caractéristiques de l'envoi</a>
{/if}
</div>