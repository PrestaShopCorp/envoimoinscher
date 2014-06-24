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

{if count($rows) > 0 || $showPoint == 1}
  {if !$isAdmin}
<script type="text/javascript">
  {literal}$(document).ready(function() {{/literal}
    {if count($rows) > 0}
    $('#LIST_TRACKING').appendTo('#block-order-detail');
    {/if}
    {if $showPoint == 1}
    $('#LIST_POINT').appendTo('#block-order-detail');
    {/if}
  {literal}});{/literal}
</script>
  {/if}
  {if count($rows) > 0}
<div id="LIST_TRACKING" class="table_block">
  <table class="detail_step_by_step std">
    <thead>
      <tr><th>Suivi transporteur</th></tr>
    </thead>
    <tbody>
    {if $isAdmin}
     <tr><td><b>Numéro de la commande :</b> {$order.id_order|escape:'htmlall'}</td></tr>
     <tr><td><b>Destinataire :</b> {$order.firstname|escape:'htmlall'} {$order.lastname|escape:'htmlall'}</td></tr>
    {/if}
    {foreach from=$rows key=r item=row}
  <tr class="{if $r%2 == 0}item{else}alternate_item{/if}" ><td><b>{$row.date}</b>, {$row.localisation_et}<br />
  {$row.text_et|escape:'htmlall'}</td>
  </tr>
    {/foreach}
    </tbody>
  </table>
</div>
  {/if}
{else}
  {if $isAdmin}
    <p><b>Suivi transporteur</b></p>
    <p><b>Numéro de la commande :</b> {$order.id_order|escape:'htmlall'}</p>
    <p><b>Destinataire :</b> {$order.firstname|escape:'htmlall'} {$order.lastname|escape:'htmlall'}</p>
    <p>Il n'y a pas d'informations de suivi pour cette commande. Veuillez réessayer plus tard ou contacter le service client d'EnvoiMoinsCher</p>
  {/if}
{/if}

{if $showPoint == 1}
<div id="LIST_POINT" class="table_block">
  <table class="detail_step_by_step std">
    <thead>
      <tr><th colspan="2">Point relais sélectionné</th></tr>
    </thead>
    <tbody>
     <tr class="item"><td>{$point.name|escape:'htmlall'}<br />
{$point.address|escape:'htmlall'}<br />
{$point.zipcode|escape:'htmlall'} {$point.city|escape:'htmlall'}</td>
<td>
{foreach from=$schedule key=d item=day}
  {$day|escape:'htmlall'}<br />
{/foreach}</td></tr>
 </tbody>
 </table>
</div>
{/if}