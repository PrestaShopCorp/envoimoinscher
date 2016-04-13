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

<script type="text/javascript">
   /*
    * We need text translation for external js
    * the script next to this need some information and smarty cant give those translations
    * (*.js or header_hook => not parsed by smarty)
    */
    var carrier_translation = {
        no_pickup_point_found_try_other_addr : "{l s='no pickup point found : try modify shipmnt address' mod='envoimoinscher'}",
        select_pickup_point1 				 : "{l s='select pickup point 1' mod='envoimoinscher'}",
        select_pickup_point2 				 : "{l s='select pickup point 2' mod='envoimoinscher'}",
        select_this_pickup_point 			 : "{l s='select this pickup point' mod='envoimoinscher'}",
        before_continue_select_pickup_point  : "{l s='before continue : select pickup point' mod='envoimoinscher'}",
        close_map : "{l s='close X' mod='envoimoinscher'}"
    };
    var carrierWithPoints;

    if(typeof idAddress == "undefined") {
        var idAddress = "{$id_address|escape:'htmlall':'UTF-8'}";
    } else {
        idAddress = "{$id_address|escape:'htmlall':'UTF-8'}";
    }

    if(typeof carrierWithPoints == "undefined") {
        var parcelPointValue = "{$point|escape:'htmlall':'UTF-8'}";

        {foreach from=$points key=id_carrier item=point}
            {if $point != ""}
                carrierWithPoints = carrierWithPoints + ";{$id_carrier|escape:'htmlall':'UTF-8'};";
            {/if}
        {/foreach}
    } else if(orderProcess == "order-opc") {
        parcelPointValue = "{$point|escape:'htmlall':'UTF-8'}";

        // make carrierWithPoints and
        pointsLoadingWasDone = "";
        carrierWithPoints = "";
        {foreach from=$points key=id_carrier item=point}
            {if $point != ""}
                carrierWithPoints = carrierWithPoints + ";{$id_carrier|escape:'htmlall':'UTF-8'};";
            {/if}
        {/foreach}
    }

	/**
	 * This code is used to handle Express Guest Checkout with One Page Checkout mode.
	 * If this mode is actived, we render the lookForPoints() method available immediately. When
	 * we don't do that, the user can't load the parcel points list when clicking on carrier radio
	 * input.
	 * Otherwise, we wait the end of document loading.
	 */
    if(typeof isGuest == "undefined") var isGuest = 0;
	if(typeof guestCheckoutEnabled == "undefined") var guestCheckoutEnabled = 0;
	if(typeof isLogged == "undefined") var isLogged = 0;
	var isGuestCheckString = ""+isGuest+""+guestCheckoutEnabled+""+isLogged;
	if((isGuestCheckString == "010" || isGuestCheckString == "111" || isGuestCheckString == "000")  && typeof(orderProcess) != "undefined" && orderProcess == "order-opc")
	{
        $(document).ready(function() {
            lookForPoints($('input[name="delivery_option['+idAddress+']"]:checked'));
        });
	}
	else
	{
        /* Careful this document ready is called everytime carriers are reloaded */
		$(document).ready(function() {
            if(typeof carrierWithPoints != "undefined") {
                lookForPoints($('input[name="delivery_option['+idAddress+']"]:checked'));
            }
		});
	}
    
	var deliveryMessage = "{$deliveryLabel|escape:'htmlall':'UTF-8'}";
	var dateReplace = "{ldelim}DATE{rdelim}";
	if (deliveryMessage != "")
	{
	{foreach from=$delivery key=id_carrier item=del}
		descr = $('div input[value="{$id_carrier|escape:'htmlall':'UTF-8'},"]').parent().find(".delivery_option_delay");
		if (descr.length == 0)
		{
			descr = $('div input[value="{$id_carrier|escape:'htmlall':'UTF-8'},"]').parent().parent().parent().find(".delivery_option_radio").next().next();
		}
		if (descr.find(".carrier_delivery_message").length == 0)
		{
				descr.html(descr.html() + "<div><span class='carrier_delivery_message'>"+ deliveryMessage.replace(dateReplace,'</span><span class="carrier_delivery_date">{$del|escape:'htmlall':'UTF-8'}'.replace("-","/")) +"</span></div>");
		}
	{/foreach}
	}
	// disable "best_grade" carrier display
	{foreach from=$points key=id_carrier item=point}
        $('div input[value="{$id_carrier|escape:'htmlall':'UTF-8'},"]').parent().parent().addClass('emcCarrier');
	{/foreach}
</script>

{foreach from=$points key=id_carrier item=onePoint}
      <input type="hidden" name="pointsList{$id_carrier|intval}" id="pointsList{$id_carrier|intval}{$id_address|intval}" value="{$onePoint|escape:'htmlall':'UTF-8'}" />
{/foreach}
<input type="hidden" name="destCountry" id="destCountry" value="{$destCountry|escape:'htmlall':'UTF-8'}" /><input type="hidden" name="voucherOk" id="voucherOk" value="0" />