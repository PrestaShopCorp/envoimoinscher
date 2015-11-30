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
<script type="text/javascript">
/*
* We need text translation for external js
* the script next to this need some information and smarty cant give those translations
* (*.js => not parsed by smarty)
*/
var carrier_translation = {
	no_pickup_point_found_try_other_addr : "{l s='no pickup point found : try modify shipmnt address' mod='envoimoinscher'}",
	select_pickup_point1 				 : "{l s='select pickup point 1' mod='envoimoinscher'}",
	select_pickup_point2 				 : "{l s='select pickup point 2' mod='envoimoinscher'}",
	select_this_pickup_point 			 : "{l s='select this pickup point' mod='envoimoinscher'}",
	before_continue_select_pickup_point  : "{l s='before continue : select pickup point' mod='envoimoinscher'}",
	close_map : "{l s='close X' mod='envoimoinscher'}"
};
</script>

<script type="text/javascript" src="{$baseDir|escape:'htmlall':'UTF-8'}modules/envoimoinscher/views/js/carrier.js"></script>
<link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall':'UTF-8'}modules/envoimoinscher/views/css/carrier.css" />
<script type="text/javascript">	
	
	/* function to block submit payment/next step form if relay point unchecked */
	function validateNextScreen(nextStepButton, type){
		/* check if operator checked need relay point */
		var opeChecked = $('input[name="delivery_option['+idAddressParams+']"]:checked').val().replace(",", "");
		var pointChecked = $('input.point'+opeChecked+idAddress+':checked').val();
		if(carrierWithPoints.indexOf(";"+opeChecked+";") !== -1 && (""+pointChecked == "undefined" || pointChecked == ""))
		{
			/* send an alert message if relay unchecked */
			if(lookForPoints($('input[name="delivery_option['+idAddressParams+']"]:checked')) == 'shown')
			{
				alert(carrier_translation.before_continue_select_pickup_point);
				return false;
			}
		}else{
			/* happen old events attached to payment/next step buttons */
			if(typeof $(nextStepButton).attr("onclick") != "undefined" && $(nextStepButton).attr("onclick") != ""){
				eval($(nextStepButton).attr("onclick"));
			}else if($(nextStepButton).attr("type") == "submit"){
				$(nextStepButton).unbind("click" );
				$(nextStepButton).trigger( "click" );
			}else{
				window.location = $(nextStepButton).attr("href");
			}
			return true;
		}
	}

	/* fix 1.5 */
  if(typeof idAddressParams == "undefined") {
		var idAddressParams = "{$id_address_params|escape:'htmlall':'UTF-8'}";
  } else {
		idAddressParams = "{$id_address_params|escape:'htmlall':'UTF-8'}";
  }
  if(typeof idAddress == "undefined") {
		var idAddress = "{$id_address|escape:'htmlall':'UTF-8'}";
  } else {
		idAddress = "{$id_address|escape:'htmlall':'UTF-8'}";
  }

  if(typeof carrierWithPoints == "undefined") {
		var parcelPointValue = "{$point|escape:'htmlall':'UTF-8'}";
		var parcelPointId = "";
		var carrierWithPoints = "";
		var pointsLoadingWasDone = "";
		var geocoder;
		var map;
		var marker = new google.maps.Marker();
		var infowindow = new google.maps.InfoWindow();
		var points;
		var infos;
		var parcelNames;
		var parcelIds;
		var infoParcel = new Array();
		var loaderSrc = "{$loaderSrc|escape:'htmlall':'UTF-8'}";

		{foreach from=$points key=id_carrier item=point}
			{if $point != ""}
				carrierWithPoints = carrierWithPoints + ";{$id_carrier|escape:'htmlall':'UTF-8'};";
			{/if}
		{/foreach}

		jQuery(document).ready(function() {
			$('body').append('<div id="allMap"><div id="mapContainer"><p><a href="#" onclick="hideMap(); return false;">'+carrier_translation.close_map+'</a></p><div id="map_canvas"></div></div></div>');
			// init google maps
			var contentMap = $('#allMap').html();
			$('#allMap').remove();
			$('body').append(contentMap);
			var myOptions = {
				zoom: 11,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
			geocoder = new google.maps.Geocoder();
			geocoder.geocode({ 'address': '41, rue Saint Augustin, 75002 Paris' }, function(results, status) { });

			// hack to avoid to hide extra carrier when prestashop natif carriers don't have offers
			$('#noCarrierWarning').hide();
			$('#carrierTable').show();
		});
  }
  else if(orderProcess == "order-opc") {
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
		lookForPoints($('input[name="delivery_option['+idAddress+']"]:checked'));
	}
	else
	{
		$(document).ready(function() {
			// prevent of displaying JavaScript's alert twice on standard order mode
			if(orderProcess != "order-opc")
			{
				$(document).delegate("change", "input[name='delivery_option['+idAddressParams+']']", function() {
					lookForPoints($(this));
				});
			}
			lookForPoints($('input[name="delivery_option['+idAddressParams+']"]:checked'));
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
				descr.html(descr.html() + "<span class='carrier_delivery_message'><br/>"+ deliveryMessage.replace(dateReplace,'</span><span class="carrier_delivery_date">{$del|escape:'htmlall':'UTF-8'}'.replace("-","/")) +"</span>");
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