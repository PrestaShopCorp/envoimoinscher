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
  if(labelsToDo > 0 ) doLabelRequest(0);
  $('#selectOrDeselectAll1').click(function() {
    selectDeselectAll('#ORDERSTABLE1 tbody input[type="checkbox"]', this);
  });
  $('#ORDERSTABLE1 tbody input[type="checkbox"]').click(function() {
    checkboxClick(this, "#selectOrDeselectAll1", 1);
  });  
});

function doLabelRequest(index)
{
  if(typeof noLabels[index] == "undefined" && noLabels.length > 0) 
  {
    setTimeout("doLabelRequest(0)", 10000);
    return;
  } //alert("checking " + index + " << >> " + labelsToDo);
  reqs[index] = $.ajax({
    url: "index.php?tab=AdminEnvoiMoinsCher&ref="+noLabels[index]+"&order="+ordersIds[index]+"&option=checkLabelsAvailability&token="+token,
    type: "GET",
    dataType: "json",
    success: function(res)
    {
      tries = 0;
      if(res.error == 0)
      { //alert(res.labelAvailable);
        if(""+res.labelAvailable == "1")
        {
          allElements[1]++;
          notChecked[1]++;
          $('#selectOrDeselectAll1').removeClass('deselectAll').addClass('selectAll')
          // put label's url into orders table
          $("#labelgen"+ordersIds[index]).remove(); 
          $("#label"+ordersIds[index]).show(); 
          $("#checkbox-"+ordersIds[index]).show(); 
          // $("#label"+ordersIds[index] + " a").attr("href", res.labelUrl); 
          ordersIds.splice(index, 1);
          noLabels.splice(index, 1);
          labelsToDo--;
          if(labelsToDo == 0) return;
        }
        var o = index + 1;
        reqs[index].abort();
        doLabelRequest(o);
      }
      else if(res.error == 1)
      {
        reqs[index].abort();
        doLabelRequest(index);
      }
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      if(tries < 3)
      {
        tries++;
        doLabelRequest(index);
      }
      else
      {
        alert("Une erreur s'est produite pendant la récupération des bordereaux de livraison : "+errorThrown)
      }
    }	
  });
}