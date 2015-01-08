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
<form method="POST" action="{$EMC_link|escape:'htmlall'}&EMC_tab=merchant">
	<fieldset id="introduction">
		<div id="logo_intro"><a href="http://www.envoimoinscher.com"></a></div>
		<p class="important">{l s='Envoimoinscher lets you plug 15 carriers on your site and save up to 75% on your shipping costs, without requirement of volume or contract!' mod='envoimoinscher'}</p>
		<p><b>{l s='FedEx, UPS, DHL, TNT, Chronopost, Coliposte, GLS, Mondial Relay, Relais Colis, Colis Privé' mod='envoimoinscher'}</b>{l s='carriers ... the best carriers available via 1 single module!' mod='envoimoinscher'}</p>
		<p class="important">{l s='Manage your shipments easily from your PrestaShop back office, through the delivery module Envoimoinscher' mod='envoimoinscher'}</p>
		<ul>
			<li>{l s='Connect on your website ' mod='envoimoinscher'}<b>{l s='the best carriers in 1 click!' mod='envoimoinscher'}</b></li>
			<li>{l s='The Envoimoinscher module gives you access to ' mod='envoimoinscher'}<b>{l s='15 carriers' mod='envoimoinscher'}</b>{l s=' accessible without contract' mod='envoimoinscher'}</li>
			<li>{l s='Enjoy' mod='envoimoinscher'}<b>{l s='negotiated exclusive rates up to 75%' mod='envoimoinscher'}</b>{l s='reduction, irrespective of volume' mod='envoimoinscher'}</li>
			<li>{l s='Give your customers choice:' mod='envoimoinscher'}<b>{l s='express' mod='envoimoinscher'}</b>{l s=', delivery' mod='envoimoinscher'}<b>{l s='home' mod='envoimoinscher'}</b>{l s=', delivery' mod='envoimoinscher'}<b>{l s='parcel point' mod='envoimoinscher'}</b></li>
			<li><b>{l s='Generate your slips in 1 click' mod='envoimoinscher'}</b>{l s='from your back office, even for the Post' mod='envoimoinscher'}</li>
			<li>{l s='Reassemble information' mod='envoimoinscher'}<b>{l s='shipment tracking' mod='envoimoinscher'}</b>{l s='in your backoffice' mod='envoimoinscher'}</li>
			<li><b>{l s='One dedicated customer service' mod='envoimoinscher'}</b>{l s='for your customer service' mod='envoimoinscher'}<b>{l s='1 single billing' mod='envoimoinscher'}</b>{l s='regardless of the selected carriers' mod='envoimoinscher'}</li>
		</ul>
		<p class="important text_align_center mt10"><a target="_blank" href="http://ecommerce.envoimoinscher.com/landing/prestashop/?utm_source=prestashop&utm_medium=partner&utm_campaign=201409_landing">{l s='Create your free account and get your API key' mod='envoimoinscher'}</a></p>
		<input type="submit" class="hidden" name="btnIntro" value="Suivant">
	</fieldset>
</form>