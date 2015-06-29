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

 // checkboxes handling
function selectDeselectAll(container, ref)
{
  var idClass = $(ref).val();
  $(container).each(function() {
    if($(ref).hasClass('selectAll')) 
    {
      $(this).attr('checked', true); 
    }
    else 
    {
      $(this).attr('checked', false);
    }
  });
  if($(ref).hasClass('selectAll')) 
  {
    $(ref).removeClass('selectAll').addClass('deselectAll');
    notChecked[idClass] = 0;
  }
  else
  {
    $(ref).removeClass('deselectAll').addClass('selectAll');
    notChecked[idClass] = -allElements[idClass];
  }
}

function checkboxClick(ref, allContainer, idClass)
{
  if($(ref).attr("checked") == true)
  {
    notChecked[idClass]--;
  }
  else
  {
    notChecked[idClass]++;
  }
  var result = "deselect";
  $(allContainer).removeClass('deselectAll').addClass('selectAll').attr('checked', false);
  if(notChecked[idClass] == 0) 
  {
    result = "select";
    $(allContainer).addClass('deselectAll').removeClass('selectAll').attr('checked', true);
  }
  return result;
}