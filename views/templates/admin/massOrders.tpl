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

<div id="massStats">
<p class="first">{l s='order currently shiping' mod='envoimoinscher'}</p>
<p>{l s='pending :' mod='envoimoinscher'} <span id="todo">{$all|escape:'htmlall':'UTF-8'}</span></p>
<p>{l s='done :' mod='envoimoinscher'} <span id="done">{$done|escape:'htmlall':'UTF-8'}</span></p>
<p><a href="index.php?controller=AdminEnvoiMoinsCher&option=cancelOrder&token={$token|escape:'htmlall':'UTF-8'}" class="action_module btn btn-default">{l s='cancel pending shipment' mod='envoimoinscher'}</a></p>
</div>