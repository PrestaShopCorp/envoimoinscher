/**
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
 */
 
$(document).ready(function(){
	//adds datepicker to date fields
	$(".datepicker").datepicker({
		prevText: '',
		nextText: '',
		dateFormat: 'yy-mm-dd'
	});
	
	$(".filters").keydown(function(e){
		if(e.keyCode == 13){
			e.preventDefault();
			applyFilter(token);
		}
	});
	
	$(".btn.get-filter").click(function(event){
		event.preventDefault();
		applyFilter(token);
	});
	
	$(".btn.reset-filter").click(function(event){
		event.preventDefault();
		resetFilter(token);
	});
  
  $(".btn.no-filter").click(function(event){
		event.preventDefault();
		noFilter(token);
	});
});
	
//create filter url for order table
function applyFilter(token)
{
	var data = {};
	data['controller'] = 'AdminEnvoiMoinsCher';
	data['token'] = token;
	
	//send order type filter content
	data['type_order'] = $("select[name='type_order']").val();
	
	//send order id filter content
	var filter_id_order = $.trim($("input[name='filter_id_order']").val());
	if(filter_id_order!="")
	{
		if(Math.floor(filter_id_order) == filter_id_order && $.isNumeric(filter_id_order))
		{
			data['filter_id_order'] = filter_id_order; 
		}
	}
	
	//send recipient filter content
	var recipient = $("input[name='recipient']").val();
	if(recipient!="")
	{
		data['recipient'] = recipient;
	}
	
	//send carriers filter content
	data['carriers'] = $("select[name='carriers']").val();
	
	//send date filter content
	var start_order_date = $("input[name='start_order_date']").val();
	var end_order_date = $("input[name='end_order_date']").val();

	if(start_order_date == ""){
		data['start_order_date'] = "all";	
	}
	else{
		data['start_order_date'] = start_order_date;
	}
	if(end_order_date == ""){
		data['end_order_date'] = "all";
	}
	else{
		data['end_order_date'] = end_order_date;
	}

	params = $.param(data);
	
	//send status filter content
	if($("select[name='status[]'] option:selected")){
		var appendUrl = '';
		$("select[name='status[]'] option:selected").each(function(index, elem){
			appendUrl += '&status[]='+elem.value;
		});
		params += appendUrl;
	}
	
	window.location.assign(location.href.substring(0, location.href.indexOf("?")+1) + params);
}

//create filter url for order table
function resetFilter(token)
{
	var data = {};
	data['controller'] = 'AdminEnvoiMoinsCher';
	data['token'] = token;

	params = $.param(data);
	window.location.assign(location.href.substring(0, location.href.indexOf("?")+1) + params);
}

//create filter url for order table
function noFilter(token)
{
	var data = {};
	data['controller'] = 'AdminEnvoiMoinsCher';
	data['token'] = token;
	data['type_order'] = "all";
	data['carriers'] = "all";
	data['start_order_date'] = "all";
	data['end_order_date'] = "all";
	
	params = $.param(data);
	
  var appendUrl = '';
  $("select[name='status[]'] option").each(function(index, elem){
    appendUrl += '&status[]='+elem.value;
  });
  params += appendUrl;
	
	window.location.assign(location.href.substring(0, location.href.indexOf("?")+1) + params);
}