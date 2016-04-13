/**
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
 */
 
$(document).ready(function() {
  allElements[1] = $('#ORDERSTABLE1 tbody input[type="checkbox"]').length;
  $('.openTrackPopup').click(function() {
    window.open($(this).attr('href'),"emcwindow","scrollbars=1, resizable=1,width=950,height=680");
    return false;
  });
  $('.selectRow').click(function() {
    $($(this).attr('rel')).addClass('selectedRow');
    $(this).addClass('deselectRow').removeClass('selectRow');
    return false;
  });
  $('.deselectRow').click(function() {
    $($(this).attr('rel')).removeClass('selectedRow');
    $(this).removeClass('deselectRow').addClass('selectRow');
    return false;
  });
  doLabelRequest();
  $('#selectOrDeselectAll1').click(function() {
    selectDeselectAll('#ORDERSTABLE1 tbody input[type="checkbox"]', this);
  });
  $('#ORDERSTABLE1 tbody input[type="checkbox"]').click(function() {
    checkboxClick(this, "#selectOrDeselectAll1", 1);
  });
  /*$('input[type="submit"][name="sendValue"]').click(function(e) {
    e.preventDefault();
    orders = [];
    $('#ORDERSTABLE1 tbody input[type="checkbox"]:checked').each(function(){
      orders.push($(this).val());  
    });
    if(orders != []){
      $.ajax({
        type: 'POST',
        url: "index.php?controller=AdminEnvoiMoinsCher&option=downloadLabels&token="+token,
        success: function(result){
          console.log(result);
        },
        data: orders,
        async: false,
      });
    }
  });*/
  
  
});

function delayDoLabelRequest()
{
	setTimeout("doLabelRequest()",10000);
}

function doLabelRequest()
{
  // Get all order's to check
	orders_to_call = "";
	$(".label-not-generated").each(function(){
		if (orders_to_call != "")
		{
			orders_to_call += ";";
		}
		orders_to_call += $(this).attr("order-id");
	});
	
	if (orders_to_call != "")
	{
		// Call server for documents
		$.ajax({
			url: "index.php?controller=AdminEnvoiMoinsCher&orders="+orders_to_call+"&option=checkLabelsAvailability&token="+token,
			type: "GET",
			dataType: "json",
			success: function(res)
			{
				// update orders
				for(order_id in res)
				{
					// add documents
					labels = res[order_id];
					content = "";
					for(label_id in labels)
					{
						content += "<a href=\""+labels[label_id].url+"\" class=\"doc-"+labels[label_id].type+" action_module btn btn-default\" target=\"_blank\">"+labels[label_id].name+"</a><br/>";
					}
					$("#label"+order_id+" .documents").html(content);		
					
					// no need to check again
					$("#label"+order_id+" .documents").removeClass("label-not-generated");
					$("#label"+order_id+" .openTrackPopup").removeClass("hidden");
					$("#checkbox-"+order_id).removeClass("hidden");
					$("#order-"+order_id).attr("checked",false);
				}
				delayDoLabelRequest();
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				delayDoLabelRequest();
			}	
		});
	}
}