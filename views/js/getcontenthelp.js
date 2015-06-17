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

 $(function(){
	// click on Title
	$(".openable-title").click(function() 
	{
		if ($(this).hasClass("closed")) {
			//HIDE OTHERS
			$others = $(".openable-title.opened");
			$others.next().toggle("fast");
			$others.removeClass("opened");
			$others.addClass("closed");
			$others.children(":first-child").children(":first-child").children(":first-child").attr("src",
					window.emcBaseDir+"img/arrow_right.png");
			//SHOW
			$(this).next().toggle("fast");
			$(this).addClass("opened");
			$(this).removeClass("closed");
			$(this).children(":first-child").children(":first-child").children(":first-child").attr("src",
					window.emcBaseDir+"img/arrow_down.png");
		} else {
			$(this).next().toggle("fast");
			$(this).addClass("closed");
			$(this).removeClass("opened");
			$(this).children(":first-child").children(":first-child").children(":first-child").attr("src",
					window.emcBaseDir+"img/arrow_right.png");
		}
	});

	// open the first title
	$(".openable-title:first-child").next().toggle("fast");
	$(".openable-title:first-child").addClass("opened");
	$(".openable-title:first-child").removeClass("closed");
	$(".openable-title:first-child").children(":first-child").children(":first-child").children(":first-child").attr("src",
			window.emcBaseDir+"img/arrow_down.png");


	// clic on subtitle 
	$(".subtitle").click(function() 
	{
		if ($(this).hasClass("closed")) {
			//HIDE OTHERS
			$others = $(".subtitle.opened");
			$others.next().toggle("fast");
			$others.removeClass("opened");
			$others.addClass("closed");

			//SHOW
			$(this).next().toggle("fast");
			$(this).addClass("opened");
			$(this).removeClass("closed");

		} else {
			$(this).next().toggle("fast");
			$(this).addClass("closed");
			$(this).removeClass("opened");

		}
	});
});