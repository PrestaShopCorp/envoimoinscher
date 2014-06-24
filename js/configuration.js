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

$(document).ready(function() {

	$('#defineOtherAddress').click(function() {
		$("#addressTable .hiddenRow").show();
		return false;
	});

	checkUpdates();

	$('#menuTab li a').click(function() {
		$('#menuTab li.selected').removeClass('selected');
		$('#configForm .configForm').hide();
		$($(this).attr('rel')).show();
		$(this).parent().addClass('selected');
		$('#lastTab').val($(this).attr('rel'));
		return false;
	});

	$('#cleanCache').click(function() {
		$('#cacheCleaning').show();
		$.ajax({
			url: $(this).attr("href"),
			type: "GET", 
			dataType: "json", 
			success : function(ret)
			{
				$('#cacheCleaning').hide();
				if(ret.error != 0)
				{
					alert("Une erreur s'est produite pendant la suppression du cache");
				}
				else
				{
					alert("Cache a été correctement supprimé");
				}
			}
		});
		return false;
	});

	$('#loadAllCarriers').click(function() {
		$('#cacheCleaning').show();
		$.ajax({
			url: $(this).attr("href"),
			type: "GET", 
			dataType: "json", 
			success : function(ret)
			{
				$('#cacheCleaning').hide();
				if(ret.error != 0)
				{
					alert("Une erreur s'est produite pendant la suppression du cache");
				}
				else
				{
					alert("Cache a été correctement supprimé");
				}
			}
		});
		return false;
	});

	$('#ajaxCarrier').click(function() {
		$('#opesLoader').show();
		$('#resultCarrierUpdate').hide();
		$.ajax({
			url: $(this).attr("href"),
			type: "GET", 
			dataType: "json", 
			success : function(res)
			{
				$('#opesLoader').hide();
				var textPlus = "";
				if(res.added > 0 || res.updated > 0 || res.deleted > 0)
				{
					textPlus = " <b>Les mises à jour décrites ci-dessus ont bien été appliquées. Pour les afficher, veuillez cliquer sur le bouton \"Sauvegarder\" en bas de page puis rafraîchir la page.</b>";
				}
				var sentAdded = "("+res.addedOffers+")";
				if(res.addedOffers == "") sentAdded = "";
				var sentUpdated = "("+res.updatedOffers+")";
				if(res.updatedOffers == "") sentUpdated = "";
				var sentDeleted = "("+res.deletedOffers+")";
				if(res.deletedOffers == "") sentDeleted = "";
				$('#resultCarrierUpdate img.errorimg').hide();
				$('#resultCarrierUpdate img.okimg').show();
				$('#resultCarrierUpdate span').html(" Offres ajoutées : <b>[" + res.added + "]</b> "+sentAdded+" <br /> Offres supprimées  : <b>[" + res.deleted + "]</b> "+sentDeleted+" <br />  Offres actualisées : <b>[" + res.updated + "]</b> "+sentUpdated+" <br />" + textPlus);
				$('#resultCarrierUpdate').addClass("conf confirm").removeClass("alert error").show();
			},
			error : function(jqXHR, textStatus, errorThrown) 
			{
				$('#opesLoader').hide();
				$('#resultCarrierUpdate img.errorimg').show();
				$('#resultCarrierUpdate img.okimg').hide();
				$('#resultCarrierUpdate span').html("Une erreur s'est produite pendant la récupération des offres de transport. Veuillez réessayer.");
				$('#resultCarrierUpdate').removeClass("conf confirm").addClass("alert error").show();        
			}
		});
return false;
});

$('.offersList td input[type="checkbox"]').click(function() {
	var display = 'none';
	if($(this).attr('checked') == true || $(this).attr('checked') == "checked")
	{
		display = 'block';
	}
	$('#field-'+$(this).attr('id')+', #field2-'+$(this).attr('id')).css('display', display);
});

});

function openPopupEmc(ope)
{
	if(ope == "SOGP")
	{
		window.open("//www.envoimoinscher.com/choix-relais.html?fcp="+$("#expZipcode").val()+"&fadr="+$("#expAddress").val()+"&fvil="+$("#expCity").val()+"&TypeLiv=REL&type=Exp&isPrestashop=true","emcwindow","scrollbars=1, resizable=1,width=650,height=680");
	}
	else if(ope == "MONR")
	{
		window.open("//www.envoimoinscher.com/choix-relais.html?isPrestashop=true&isModule=true&monrCp="+$("#expZipcode").val()+"&monrVille="+$("#expCity").val()+"&monrPays=FR&monrPoids=1","emcwindow","scrollbars=1, resizable=1,width=800,height=680");
	}
}

function checkUpdates()
{
	$.ajax({
		url: updatesUrl,
		type: "GET", 
		dataType: "json", 
		success : function(res)
		{
			if(res.length > 0)
			{
				$('#offerUpCont').removeClass('hidden');
				$('#menuTab li.selected').removeClass('selected');
				$('#configForm .configForm').hide();
				$("#confMaj").show();
				$("#tabLiConfMaj").addClass('selected');
				$('#lastTab').val("#confMaj");
				$("#updatesContainer").show();
				$("#updatesToInstall").hide();
				$('html,body').animate({scrollTop: $("#updatesContainer").offset().top}, 'fast');
			}
			for(var i=0; i < res.length; i++)
			{
				$("#updatesTable tbody").append("<tr><td>"+res[i].version+"</td><td>"+res[i].name+"</td><td>"+
					res[i].description+'</td><td><a href="'+res[i].url+'" target="_blank" class="action_module">'+res[i].url+"</a></td></tr>");
			}
		},
		error : function(jqXHR, textStatus, errorThrown) 
		{

		}
	});  
}

function handleServices(type)
{
	var isDisabled = true;
	var newClass = "gris";
	if(type == "config")
	{
		isDisabled = false;
		newClass = "";
	}
	$("#confSrv").removeClass("gris").addClass(newClass);
	$("#confBase").removeClass("gris").addClass(newClass);
	$("#confSrv table input").attr("disabled", isDisabled);
	$("#confBase table input").attr("disabled", isDisabled);
}