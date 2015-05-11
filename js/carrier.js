/**
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
 */

 if(typeof carrierWithPoints == "undefined") {
	 
/**
 * Calls page to get the list of availables parcel points.
 */
function selectPoint(ref, link, operator, address)
{
  $('#id_carrier'+ref+address).attr('checked', 'checked');
  // make offset
  var arr = $(link).offset();
  $('#mapContainer').css('top', (arr.top-50)+'px');
  $('#loaderPoints'+ref+address).show(); 
  $.ajax({
    url: 'index.php?fc=module&module=envoimoinscher&controller=ajax&option=get_point',
    type: 'POST', 
    data: { 'points' : $('#pointsList'+ref+address).val(), carrier : ref, ope: operator, addressId : address, country : $('#destCountry').val(), pointValue : parcelPointValue },
    success : function(res)
    {	
      if(res.indexOf("noPoint") != -1){
    	alert(carrier_translation.no_pickup_point_found_try_other_addr);
      }
      else{
				res = '<p><b>'+carrier_translation.select_pickup_point1+'<a href="#" id="openMap" class="click-here" onclick="javascript:makeMap(\'MONR\', \''+ref+'\', \''+address+'\');return false;">'+carrier_translation.select_pickup_point2+'</a></b></p>'+
				'<ul>'+res +'</ul>';
	      $('#points'+ref+address).show();
	      $('#points'+ref+address).html(res);
	      $('#loaderPoints'+ref+address).remove();
	      if(typeof $(parcelPointId) != "undefined" && typeof $(parcelPointId) != "")
	      {
	        $(parcelPointId).attr("checked", true);
	        $(parcelPointId).attr("checked", "checked");
	      }
      }
    }
  });
}

/**
 * Handles map making. If the map has already been opened, show only the <div />. Otherwise
 * generate the Google Maps.
 */
function makeMap(ope, carrierdId, address)
{
  initialize(ope, carrierdId, address);
}

/**
 * Constructs Google Map.
*/
function initialize(ope, carrierdId, addressId) 
{
  $('#counter'+carrierdId+ope).val(0);
  document.getElementById('mapContainer').style.display = 'block';
  geocoder = new google.maps.Geocoder();
  var myOptions = {
    zoom: 11, 
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  var addressId = addressId;
  var carrierdId = carrierdId;
  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  points = $('#parcelPoints'+carrierdId+ope+addressId).val().split('|');
  infos = $('#parcelInfos'+carrierdId+ope+addressId).val().split('|');
  parcelNames = $('#parcelNames'+carrierdId+ope+addressId).val().split('|');
  parcelIds = $('#parcelIds'+carrierdId+ope+addressId).val().split('|');
  for(var i = 0; i <  points.length ; i++)
  {
    (function(i) {
      var address = points[i];
      infoParcel[i] = '<b>'+parcelNames[i]+'</b>' + '<br /><a href="#" onclick="javascript: selectPr(\''+parcelIds[i]+'\', \''+carrierdId+'\', \''+addressId+'\'); return false;">'+carrier_translation.select_this_pickup_point+'</a> <br />'  + address + '<br />' + infos[i]; 
      if(geocoder)
      {
        geocoder.geocode({ 'address': address }, function(results, status) {
          if(status == google.maps.GeocoderStatus.OK)   
          {
            makeMarker(i, results[0].geometry.location);
          }
        });
      }
    })(i);
  }
  document.getElementById('mapContainer').style.display = 'block'; 
}

/**
 * Makes Google Maps markers. 
 */
function makeMarker(s, location)
{
  map.setCenter(location);
  var marker = new google.maps.Marker({
    map: map, 
    position: location,
    title : ""+parcelNames[s]
  });
  marker.set("bulkContent", infoParcel[s]);
  google.maps.event.addListener(marker,"click",function() { 
    infowindow.setContent(this.get("bulkContent"));
    infowindow.open(map,marker);
  });
}

/**
 * Shows Google Map.
 */
function showMap()
{ 
  document.getElementById('mapContainer').style.display = 'block'; 
}

/**
 * Hides Google Map.
 */
function hideMap()
{ 
  document.getElementById('mapContainer').style.display = 'none'; 
}

/**
 * Sets a choosen parcel point when clicked on link "select this point". 
*/
function selectPr(pr, carrierId, addressId)
{
  makeOpeChecked(carrierId, 0, 0, addressId);
  $.ajax({
    url: 'index.php?fc=module&module=envoimoinscher&controller=ajax&option=set_point',
    type: 'POST', 
    data: { 'point' : pr }
  });
  $('#point'+carrierId+pr+addressId).attr('checked', 'checked');
  $('#point'+carrierId+pr+addressId).attr('checked', true);
  parcelPointId = '#point'+carrierId+pr+addressId;
  getCarrierInput($('#point'+carrierId+pr+addressId)).attr('checked', 'checked');
  getCarrierInput($('#point'+carrierId+pr+addressId)).attr('checked', true);
  hideMap();
}

/**
 * Gets carrier radio input
 */
function getCarrierInput(ref)
{
  return ref.parent().parent().parent().parent().find('input[name="delivery_option['+idAddress+']"]');
}

/**
 * Handle parcel point. If it's not choosen, shows alert message.
 */
function handleParcelPoint(ref, carrier, price, priceHT, address)
{
  makeOpeChecked(carrier, price, priceHT, address);
  if(''+$('input[name="point'+carrier+ref+address+'"]:checked').val() == 'undefined')
  {
    alert(carrier_translation.before_continue_select_pickup_point);
    return false;
  }
} 

/**
 * Method called after every select of the new carrier. It checks if user muste choose 
 * a parcel point for the currenct carrier. If it's the case, this method loads parcel points
 * list at the bottom of carrier box. Additionally we show JavaScript's alert with message which
 * reminds user about necessity of parcel point choosing.
 */
function lookForPoints(ref)
{
	// Check if the param contain any value
	if (ref == null || 'string' != typeof(ref.val())){
		return;
	}
  var value = ref.val().replace(",", "");
  if(carrierWithPoints.indexOf(";"+value+";") !== -1 && pointsLoadingWasDone.indexOf(";"+value+";") === -1)
  { 
    pointsLoadingWasDone = pointsLoadingWasDone + ";"+value+";"; 
    var itemParent = $(ref).parents(".delivery_option_radio");
		if (itemParent.length == 0)
		{
			itemParent = ref.parent();
		}
    itemParent.append(
      '<div id="loaderPoints'+value+idAddress+'" class="padding5">'+
      '<br /><img src="'+loaderSrc+'" alt="" class="wating_loader_point" /> Veuillez patienter...'+
      '</div><div id="points'+value+idAddress+'" class="pointsUl padding5 list_points_loaded">'+
      '</div>');
    selectPoint(value,  ref , 'MONR', idAddress);
		//alert(carrier_translation.before_continue_select_pickup_point);
  }
	else{
		return 'shown';
	}
}

/**
 * Selects an operator.
 */
function makeOpeChecked(opeName, address)
{
  $("#delivery_option_"+opeName+"_"+address).attr("checked", true);
  $("#delivery_option_"+opeName+"_"+address).attr("checked", "checked");
}
 
}