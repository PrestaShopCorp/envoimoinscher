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
 * @copyright 2007-2015 PrestaShop SA / 2011-2015 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 */

 var modified = false;
$(document).ready(function() {
  $("#multiParcel").blur(function() {
		if ($("#multiParcel").val() != "" && ""+parseInt($("#multiParcel").val()) != "NaN")
		{
			$("#width").attr("disabled","disabled");
			$("#height").attr("disabled","disabled");
			$("#length").attr("disabled","disabled");
		}
		else
		{
			$("#width").removeAttr("disabled");
			$("#height").removeAttr("disabled");
			$("#length").removeAttr("disabled");
		}
    makeParcels();
  });
  $("#weight").blur(function() {
    if($("#multiParcel").val() != "") makeParcels();
  });

  $('#changeDestData').click(function() {
    $('.changeDest').removeClass("hidden"); 
    return false;
  });
  		
	$('#mandatory_form').submit(function (){
		left = 0;
		width = $(".box-left").css("width");
		if($("#nav-sidebar").length > 0)
		{
			left += $("#nav-sidebar").width() + 20;
		}
		else
		{
			left += 15;
		}
		$("#submitForm").attr("disabled","disabled");
		$("#messageSending").css("position","absolute");
		//$("#messageSending").css("left",""+left+"px");
		$("#messageSending").width(width);
		$("#messageSending").css("top",""+($("#changeDestData").offset().top + 40 ) +"px");
		$("#messageSending").removeClass("hidden");
	});
			
	$('#submitForm').click(function() {
		
	
    if($('#weight').val() != baseWeight ||
			 $('#height').val() != baseHeight || 
			 $('#width').val() != baseWidth || 
			 $('#length').val() != baseLength || 
			 modified)
    {
			$("#submitForm").attr("disabled","disabled");
      var parcel_w_lines = "";
      var parcel_l_lines = "";
      var parcel_h_lines = "";
      var parcel_d_lines = "";
      if(modified)
      {
        parcel_w_lines = $("#parcel_w_1").val();
				parcel_l_lines = $("#parcel_l_1").val();
				parcel_h_lines = $("#parcel_h_1").val();
				parcel_d_lines = $("#parcel_d_1").val();
        for(var i=2; i <= parseInt($("#multiParcel").val()); i++)
        {
          parcel_w_lines += ";"+$("#parcel_w_"+i).val();
          parcel_l_lines += ";"+$("#parcel_l_"+i).val();
          parcel_h_lines += ";"+$("#parcel_h_"+i).val();
          parcel_d_lines += ";"+$("#parcel_d_"+i).val();
        }
      }
      $('html,body').animate({scrollTop: $("#offerTable").offset().top}, 'fast');
      baseWeight = $('#weight').val();
      baseWidth = $('#width').val();
      baseLength = $('#length').val();
      baseHeight = $('#height').val();
      modified = false;
      $('#offerTable').html("Recherche des offres...");
      $.ajax({
        url: "index.php?controller=AdminEnvoiMoinsCher&id_order="+orderId+"&option=getOffersNewWeight&token="+token,
        type: 'POST', 
        data: {'multiParcel' : $('#multiParcel').val() ,'weight' : $('#weight').val(), 'parcels_d' : parcel_d_lines,'parcels_h' : parcel_h_lines,'parcels_l' : parcel_l_lines, 'parcels_w' : parcel_w_lines},
        success : function(res)
        {
          var content = $(res).find("#tableContent").html();
          if(""+content != "null" && ""+content != "undefined")
          {
            $('#offerTable').html(content);
          }
          else
          {
            $('#foundBlock').remove();
            content = $(res).find("#allOffersTable").html();
            $('#offerTable').html("");
            $('#notFoundOffer').html(content);
          } 
		  $('#submitForm').removeAttr("disabled");
        }
      });
      return false;
    }
  });
	
	$("#multiParcel").trigger("blur");
	
});

function traduireDescription(nom) 
{ 
  var url = "//translate.google.fr/?hl=fr&sl=fr&tl=en&sugg=u&hints=true&q="; 
  if (nom != "") {
    url += nom + '#';  
  }
  window.open(url, "_blank");
  return false;
}

function openPopupEmc(c)
{
  if($("#opeCode").val() == "SOGP")
  {
    window.open("//www.envoimoinscher.com/choix-relais.html?fcp="+$("#dest_code").val()+"&fadr="+$("#dest_add").val()+"&fvil="+$("#dest_city").val()+"&TypeLiv=REL&type=Exp&isPrestashop=true","emcwindow","scrollbars=1, resizable=1,width=650,height=680");
  }
  else if($("#opeCode").val() == "MONR")
  {
    if(c == "DEL")
    {
      window.open("//www.envoimoinscher.com/choix-relais.html?isPrestashop=true&isModule=true&monrCp="+$("#dest_code").val()+"&monrVille="+$("#dest_city").val()+"&monrPays="+$("#dest_country").val()+"&monrPoids="+$("#weight").val(),"emcwindow","scrollbars=1, resizable=1,width=650,height=680");
    }
    else if(c == "SHI")
    {
      window.open("//www.envoimoinscher.com/choix-relais.html?isPrestashop=true&isModule=true&monrCp="+$("#exp_cp").val()+"&monrVille="+$("#exp_city").val()+"&monrPays="+$("#exp_pays").val()+"&monrPoids="+$("#weight").val(),"emcwindow","scrollbars=1, resizable=1,width=650,height=680");
    }
  }
}

function roundFloat(num, dec) 
{
  return Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
}

function makeParcels()
{
  modified = false;
  $(".appendRowParcels").remove();
  $("#errorMultiParcel").addClass("hidden");
  if($("#multiParcel").val() == "") return false;
  var nr = parseInt($("#multiParcel").val());
  if(""+nr == "NaN" || nr < 2)
  {
    $("#errorMultiParcel").removeClass("hidden");
    return false;
  }
  $("#multiParcel").val(nr);
  var weight = parseFloat($("#weight").val());
  var weightByField = roundFloat(parseFloat(weight/nr), 2);
  for(var i=nr; i > 0; i--)
  {
    var htmlRow = '<tr class="appendRowParcels">'+
		'<th><label for="parcel_w_'+i+'">Poids #'+i+'</label></th>'+
    '<td class="paddingTableTd"><input type="text" name="parcel_w_'+i+'" id="parcel_w_'+i+'" value="'+weightByField+'" onblur="javascript: modifWeight();" /> kg</td>'+
		'</tr><tr class="appendRowParcels">'+
		'<th><label for="parcel_d_'+i+'">Largeur #'+i+'</label></th>'+
    '<td class="paddingTableTd"><input type="text" name="parcel_d_'+i+'" id="parcel_d_'+i+'" value="" onblur="javascript: modifWeight();" /> cm</td>'+
		'</tr><tr class="appendRowParcels">'+
		'<th><label for="parcel_l_'+i+'">Longueur #'+i+'</label></th>'+
    '<td class="paddingTableTd"><input type="text" name="parcel_l_'+i+'" id="parcel_l_'+i+'" value="" onblur="javascript: modifWeight();" /> cm</td>'+
		'</tr><tr class="appendRowParcels">'+
		'<th><label for="parcel_h_'+i+'">Hauteur #'+i+'</label></th>'+
    '<td class="paddingTableTd"><input type="text" name="parcel_h_'+i+'" id="parcel_h_'+i+'" value="" onblur="javascript: modifWeight();" /> cm</td>'+
    '</tr>';
    $("#multiParcelRow").after(htmlRow);
  }
  modified = true;
}

function modifWeight()
{
  var weightSum = 0;
  var nr = parseInt($("#multiParcel").val()); 
  for(var i=nr; i > 0; i--)
  {
    weightSum = weightSum + roundFloat(parseFloat($("#parcel_w_"+i).val()), 2);
  }
  $("#weight").val(roundFloat(weightSum, 2));
}

function modifInsurance(ref)
{
  if(typeof($(ref).attr("checked")) != "undefined")
  {			
		$("#htIns").removeClass("hidden");
		$("#ttcIns").removeClass("hidden");
		$("#htNoIns").addClass("hidden");
		$("#ttcNoIns").addClass("hidden");
		$(".assuTd").removeClass("hidden");
  }
	else{
		$("#htIns").addClass("hidden");
		$("#ttcIns").addClass("hidden");
		$("#htNoIns").removeClass("hidden");
		$("#ttcNoIns").removeClass("hidden");
		$(".assuTd").addClass("hidden");	
	}
}