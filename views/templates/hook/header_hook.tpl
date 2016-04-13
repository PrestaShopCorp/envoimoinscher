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

<script type="text/javascript" src="{$emcBaseDir|escape:'htmlall':'UTF-8'}views/js/carrier.js"></script>
<link type="text/css" rel="stylesheet" href="{$emcBaseDir|escape:'htmlall':'UTF-8'}views/css/carrier.css" />

<script type="text/javascript">
    var wasCall = false;
    var parcelPointId = "";
    var pointsLoadingWasDone = "";
    var geocoder;
    var map;
    var points;
    var infos;
    var parcelNames;
    var parcelIds;
    var infoParcel = new Array();
    var loaderSrc = "{$loaderSrc|escape:'htmlall':'UTF-8'}";
    var host = "{$host|escape:'htmlall':'UTF-8'}";
    var closeMapTranslation = "{$closeMapTranslation|escape:'htmlall':'UTF-8'}";
    
    /* This document ready is loaded only once */
    $(document).ready(function() {
        $('body').append('<div id="mapContainer"><p><a href="#" onclick="hideMap(); return false;">'+closeMapTranslation+'</a></p><div id="map_canvas"></div></div>');
       
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
        
        $(document).delegate("#HOOK_PAYMENT a, [name='processCarrier']", "click", function(e) {
            if(validateNextScreen(true)) {
                return;
            }
            e.preventDefault();
        });
        
        /* fix for paypal */
        $(document).delegate('#paypal_payment_form', "submit", function() {
            if(validateNextScreen(false)) {
                return true;
            } else {
                return false;
            }
        });

    });
</script>

