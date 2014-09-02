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

<h2>{l s='EnvoiMoinsCher : Update' mod='envoimoinscher'}</h2> 
  {if $error}
    <div class="bootstrap"><div class="alert alert-danger error">{l s='The update was not properly executed.' mod='envoimoinscher'}</div></div>
  {else}
    <div class="bootstrap"><div class="conf alert alert-success">{l s='The update has been properly executed.' mod='envoimoinscher'}</div></div>
  {/if}
  <p>&larr; <a href="index.php?controller=AdminModules&configure=envoimoinscher&token={$token|escape:'htmlall'}&tab_module=shipping_logistics&module_name=envoimoinscher" class="action_module">{l s='Back to configuration page' mod='envoimoinscher'}</a></p>
</form>