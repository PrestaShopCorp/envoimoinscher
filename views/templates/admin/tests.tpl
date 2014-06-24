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

<h2>EnvoiMoinsCher : estimation des frais de port</h2> 
<link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall'}modules/envoimoinscher/css/backend_styles.css" />
<p>La page de simulation vous permet d'effectuer un devis suivant les caractéristiques de vos colis.</p>
<p>Les prix et les offres restitués sont ceux que verra votre client.</p>
<br />
<div class="clear"></div>
<form action="index.php?controller=AdminEnvoiMoinsCher&option=tests&token={$token|escape:'htmlall'}#offers" method="post" class="form" id="configForm">
  <fieldset id="confGen" class="configForm">
    <table class="testForm">
      <tbody>
        <tr>
          <td><label for="product">Choisissez un produit : </label></td>
          <td><select name="product" id="product">
  {foreach from=$products key=p item=product}
            <option value="{$product.value}" {if isset($postData.product) && $postData.product == $product.value}selected="selected"{/if}>{$product.name}</option>
  {/foreach}
          </select></td>
        </tr>
        <tr>
          <td><label for="fromPostalCode">Code postal de départ : </label></td>
          <td><input type="text" name="fromPostalCode" id="fromPostalCode" value="{if isset($postData.fromPostalCode) && $postData.fromPostalCode != ""}{$postData.fromPostalCode}{else}{$configEmc.EMC_POSTALCODE}{/if}" /></td>
        </tr>
        <tr>
          <td><label for="fromCity">Ville de départ : </label></td>
          <td><input type="text" name="fromCity" id="fromCity" value="{if isset($postData.fromCity) && $postData.fromCity != ''}{$postData.fromCity}{else}{$configEmc.EMC_CITY}{/if}" /></td>
        </tr>
        <tr>
          <td><label for="fromAddr">Adresse de départ : </label></td>
          <td><input type="text" name="fromAddr" id="fromAddr" value="{if isset($postData.fromAddr) && $postData.fromAddr != ''}{$postData.fromAddr}{else}{$configEmc.EMC_ADDRESS}{/if}" /></td>
        </tr>
        <tr>
          <td><label for="toCountry">Pays de destination : </label></td>
          <td><select name="toCountry" id="toCountry">
  {foreach from=$countries key=c item=country}
            <option value="{$country.iso_code}" {if (isset($postData.toCountry) && $postData.toCountry == $country.iso_code) || (!isset($postData.toCountry) && $country.iso_code == 'FR')}selected="selected"{/if}>{$country.name}</option>    
  {/foreach}
          </select></td>
        </tr>
        <tr>
          <td><label for="toPostalCode">Code postal d'arrivée : </label></td>
          <td><input type="text" name="toPostalCode" id="toPostalCode" value="{if isset($postData.toPostalCode)}{$postData.toPostalCode}{/if}" /></td>
        </tr>
        <tr>
          <td><label for="toCity">Ville d'arrivée : </label></td>
          <td><input type="text" name="toCity" id="toCity" value="{if isset($postData.toCity)}{$postData.toCity}{/if}" /></td>
        </tr>
        <tr>
          <td><label for="toAddr">Adresse d'arrivée : </label></td>
          <td><input type="text" name="toAddr" id="toAddr" value="{if isset($postData.toAddr)}{$postData.toAddr}{/if}" /></td>
        </tr>
      </tbody>
    </table>
  </fieldset>
  <p class="center">
    <input class="button" id="submitForm" type="submit" name="submitForm" value="Envoyer" />
  </p>
  <div id="offers">
  {if $isError == 1}
    <div class="alert error">{$errorMsg|escape:'htmlall'}</div>
  {/if} 
  {if $isError == 0 && $offers}
    <table class="table"> 
    <tbody id="offersBody">
      <tr>
        <th style="width:100px;">Offre</th>
        <th style="width:100px;">Transporteur</th>
        <th style="width:100px;">Prix HT €</th>
        <th style="width:100px;">Prix TTC €</th>
        <th>Description</th>
      </tr> 
    {foreach from=$offers key=o item=offer}
      <tr {if $o%2 == 0}class="alt_row"{/if}>
        <td>{$offer.service.label|escape:'htmlall'}</td>
        <td>{$offer.operator.label|escape:'htmlall'}</td>
        <td>{$offer.priceHT|escape:'htmlall'} €</td>
        <td>{$offer.priceTTC_client|escape:'htmlall'} €</td>
        <td>{$offer.characteristics|escape:'htmlall'}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  {/if} 
  </div><!-- offers-->
</form>