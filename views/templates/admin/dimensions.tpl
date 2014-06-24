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

<h2>EnvoiMoinsCher : dimensions</h2>
<link type="text/css" rel="stylesheet" href="{$baseDir|escape:'htmlall'}/modules/envoimoinscher/css/backend_styles.css" /> 

{if $successForm == 1}
<div class="conf confirm">Les données de configuration ont été mises à jour.</div>
{/if}

<div class="clear"></div>
<p>Sur cette page, vous pouvez personnaliser les dimensions maximum de vos envois par tranches de poids. Nous vous recommandons d'effectuer ce travail de personnalisation car les dimensions constituent un des critères déterminants du prix des offres de livraison. Indiquez ainsi les dimensions les plus courantes pour obtenir les tarifs les plus proches de la réalité
<br /><br />
Sans personnalisation, ce sont les dimensions par défaut, élaborées par EnvoiMoinsCher, qui seront affichées.</p>
<form action="index.php?controller=AdminEnvoiMoinsCher&option=dimensions&token={$token|escape:'htmlall'}" method="post" class="form" id="configForm">
  <fieldset id="confGen" class="configForm dimensions">
    <table>
      <tbody>
  {foreach from=$dims key=d item=dim}
        <tr>
          <td class="floatAll">
            <span class="big">{$d+1|intval}</span> <label for="weight{$d+1|intval}">Poids jusqu'à</label> 
            <input type="text" name="weight{$d+1|intval}" id="weight{$d+1|intval}" value="{$dim.weight_ed|intval}" class="smallInput" /> <span>kg</span>
            <label for="length{$d+1|intval}">Longueur max</label> 
            <input type="text" name="length{$d+1|intval}" id="length{$d+1|intval}" value="{$dim.length_ed|intval}" class="smallInput" /> <span>cm</span>
            <label for="width{$d+1|intval}">Largeur max</label> 
            <input type="text" name="width{$d+1|intval}" id="width{$d+1|intval}" value="{$dim.width_ed|intval}" class="smallInput" /> <span>cm</span>
            <label for="height{$d+1|intval}">Hauteur max</label> 
            <input type="text" name="height{$d+1|intval}" id="height{$d+1|intval}" value="{$dim.height_ed|intval}" class="smallInput" /> <span>cm</span>
            <input type="hidden" name="id{$d+1|intval}" id="id{$d+1|intval}" value="{$dim.id_ed|intval}" />
          </td>
        </tr>
  {/foreach}
      </tbody>
    </table>
  </fieldset>
  <p class="center">
    <input type="hidden" name="countDims" id="countDims" value="{$countDims|intval}" />
    <input class="button" id="submitForm" type="submit" name="submitForm" value="Sauvegarder" />
  </p>
</form>