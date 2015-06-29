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

{if count($rows) > 0}
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
      <tr><th>{l s='Carrier tracking' mod='envoimoinscher'}</th></tr>
    </thead>
    <tbody>
    {if $isAdmin}
     <tr><td><b>{l s='Order number' mod='envoimoinscher'} :</b> {$order.id_order|escape:'htmlall':'UTF-8'}</td></tr>
     <tr><td><b>{l s='Recipient' mod='envoimoinscher'} :</b> {$order.firstname|escape:'htmlall':'UTF-8'} {$order.lastname|escape:'htmlall':'UTF-8'}</td></tr>
    {/if}
    {foreach from=$rows key=r item=row}
  <tr class="{if $r%2 == 0}item{else}alternate_item{/if}" ><td><b>{$row.date}</b>, {$row.localisation_et}<br />
  {$row.text_et|escape:'htmlall':'UTF-8'}</td>
  </tr>
    {/foreach}
    </tbody>
  </table>
</div>
  {/if}
{else}
  {if $isAdmin}
    <p><b>{l s='Carrier tracking' mod='envoimoinscher'}</b></p>
    <p><b>{l s='Order number' mod='envoimoinscher'} :</b> {$order.id_order|escape:'htmlall':'UTF-8'}</p>
    <p><b>{l s='Recipient' mod='envoimoinscher'} :</b> {$order.firstname|escape:'htmlall':'UTF-8'} {$order.lastname|escape:'htmlall':'UTF-8'}</p>
    <p>{l s='There is no tracking information for this order. Please try again later or contact Envoimoinscher customer service.' mod='envoimoinscher'}</p>
  {/if}
{/if}