{**
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
 *}

	<form id="introduction" method="POST" action="{$EMC_link|escape:'htmlall'}&EMC_tab=merchant">
		<div id="MainBox">
			<div id="content-wrapper">
				<div class="box" id="box1">
				<div class="title">{l s='Access to 15 carriers with negotiated prices' mod='envoimoinscher'}<div id="logo_intro"><a href="http://www.envoimoinscher.com"></a></div></div>
					<div class="content">
						<ul class="iconList s17">
							<li><span class="orange bold">{l s='Up to 75%' mod='envoimoinscher'}</span>{l s=' immediate discount on your shipments' mod='envoimoinscher'}</li>
							<li>{l s='Without requirement of volume or contract' mod='envoimoinscher'}</li>
							<li>{l s='Easy and transparent package monitoring' mod='envoimoinscher'}</li>
							<li><span class="blue bold">{l s='1 dedicated customer service' mod='envoimoinscher'}</span>{l s=' for your SAV and ' mod='envoimoinscher'}<span class="blue bold">{l s='one single billing' mod='envoimoinscher'}</span></br>{l s=' whatever the carriers : ' mod='envoimoinscher'}<span class="orange bold">{l s='Envoimoinscher handles everything!' mod='envoimoinscher'}</span></li>
						</ul>
						<hr>
						<div id="img-operators">
							<table>
								<tr>
									<td class="laposte"></td>
									<td class="mondialrelay"></td>
									<td class="relaiscolis"></td>
									<td class="chronopost"></td>
									<td class="tnt"></td>
								</tr>
								<tr>
									<td class="ups"></td>
									<td class="dhl"></td>
									<td class="fedex"></td>
									<td class="sodexi"></td>
									<td class="colisprive"></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		<div class="text_align_center mt20"><a href="#" class="btnValid create button-orange text_align_center">{l s='Create your free account' mod='envoimoinscher'}</a><a href="#" class="btnValid button-orange text_align_center ml10p">{l s='I already have an account' mod='envoimoinscher'}</a></div>
		<input type="hidden" name="choice" value="">
		<input type="submit" class="hidden" name="btnIntro" value="Suivant">
			<script type="text/javascript">
				{literal}		
					$(".btnValid").click(function() {
						if($(this).hasClass("create")) $('#introduction').find('input[name=choice]').val("create");
						else $('#introduction').find('input[name=choice]').val("api");
						$('#introduction').find('input[type=submit]').click();
					});	
				{/literal}
			</script>		 
		</div>		
	</form>
