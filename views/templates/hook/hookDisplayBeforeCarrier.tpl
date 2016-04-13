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

{if isset($removeCarriers)}
    <script type="text/javascript">
        var removeCarriers = new Array();
        {foreach from=$removeCarriers key=o item=carrier}
            removeCarriers.push("{$carrier|escape:'htmlall':'UTF-8'}");
        {/foreach}
        {literal}
            $(document).ready(function(){
                $('.delivery_option').each(function(){
                    var key = $(this).find('input.delivery_option_radio').attr("value");
                    if (removeCarriers.indexOf(key) != -1) {
                        $(this).addClass('emc_hidden');
                    }
                });
                $('.emc_hidden').find('input.delivery_option_radio').prop('disabled', true);
                $('.emc_hidden').each(function(index, element){
                    if($(element).find('.emc_warning').length == 0) {
                        $(element).prepend("<p class='emc_warning'>{/literal}{l s='This carrier is not available for this order' mod='envoimoinscher'}{literal}</p>");
                    }
                });
                $('.emc_hidden').appendTo('.delivery_options');
                var row = 0; 
                $('.delivery_option').each(function(){
                    if (row % 2 == 0) {
                        $(this).removeClass('alternate_item').addClass('item');
                    } else {
                        $(this).removeClass('item').addClass('alternate_item');
                    }
                    if (row == 0) {
                        $(this).css('border-top', 'none');
                    }
                    row += 1;
                });
            });
        {/literal}
    </script>
{/if}