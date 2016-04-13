{**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA / 2011-2016 EnvoiMoinsCher
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registred Trademark & Property of PrestaShop SA
 *}

<div class="bootstrap">	
	{if $local_bootstrap}
		<link href="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/css/back-office-15.css" rel="stylesheet" type="text/css" media="all" />
	{/if}
	
	{if isset($missedValues) && count($missedValues)}
		<div class="bootstrap">
			<div class="alert alert-danger error">
			{l s='missing fields info list' mod='envoimoinscher'}
			{foreach from=$missedValues key=m item=missed}
				<br />- {$missed|escape:'htmlall':'UTF-8'}
			{/foreach}
		</div>
		</div>
	{/if}
	{if $EMC_config.EMC_USER != "" && $EMC_config.EMC_USER == 1 && ($EMC_config.EMC_KEY_TEST == '' || $EMC_config.EMC_KEY_PROD == '' || $EMC_config.EMC_LOGIN == '' || $EMC_config.EMC_PASS == '')}
		{if $EMC_config.EMC_KEY_TEST == '' && $EMC_config.EMC_ENV == 'PROD' && isset($EMC_config.EMC_KEY_PROD_DONOTCHECK) && $EMC_config.EMC_KEY_PROD_DONOTCHECK == 1 }
			<div class="bootstrap">
				<div class="alert alert-warning warn warning">{l s='Following the module update, please enter your API key test account.' mod='envoimoinscher'}</div>
			</div>
		{elseif $EMC_config.EMC_KEY_PROD == '' && $EMC_config.EMC_ENV == 'TEST' && isset($EMC_config.EMC_KEY_TEST_DONOTCHECK) && $EMC_config.EMC_KEY_TEST_DONOTCHECK == 1 }
			<div class="bootstrap">
				<div class="alert alert-warning warn warning">{l s='Following the module update, please enter your API key production account.' mod='envoimoinscher'}</div>
			</div>		
		{else}
			<div class="bootstrap">
				<div class="alert alert-danger error">{l s='You are using the wrong credentials. The module will not work.' mod='envoimoinscher'}</div>
			</div>		
		{/if}
	{/if}
	{if $multiShipping == 1}
		<div class="bootstrap">
			<div class="alert alert-danger error">{l s='The module is not compatible with the "multishipping" option, it will not work.' mod='envoimoinscher'}</div>
		</div>
	{/if}

	{if $successForm == 1}
		<div class="conf confirm">{l s='Configuration saved' mod='envoimoinscher'}
			{if $lastTab == '#confSrv'}
				<p>
					<b>{l s='Do not forget to set your module "Online" if you want to display your offers' mod='envoimoinscher'}</b>
				</p>
			{/if}
		</div>
	{/if}
    
    {if $EMC_config.EMC_USER == 1}
        {foreach from=$API_errors item=error}
            {if $error.id === false}
            <div class="bootstrap">
                <div class="alert alert-danger error">{l s='API error : unknow error' mod='envoimoinscher'}{$error.message|escape:'htmlall':'UTF-8'}</div>
            </div>
            {elseif $error.id === "API error : Invalid account payment method"}
            <div class="bootstrap">
                <div class="alert alert-danger error">{l s='the module in production mode requires the activation of deferred payment. to do so, go to preference tab into your' mod='envoimoinscher'} <a href="{$website_url|escape:'htmlall':'UTF-8'}" target="_blank" alt="Website">{l s='account' mod='envoimoinscher'}</a> {l s='and activate the deferred payment.' mod='envoimoinscher'}</div>
            </div>
            {else}
            <div class="bootstrap">
                <div class="alert alert-danger error">{l s=$error.id mod='envoimoinscher'}</div>
            </div>
            {/if}
        {/foreach}
    {else}
        <div class="bootstrap">
            <div class="alert alert-danger error">{l s='You need to fill in correct account information before you can use the module.' mod='envoimoinscher'}</div>
        </div>
    {/if}
    
    {if $EMC_config.EMC_USER == 1}
        <div class="alert alert-warning warn warning {if $EMC_config.EMC_ENV != 'TEST'}hidden{/if}">
            {l s='Boxtale plugin can be activated only if the deferred payment by direct debit was chosen at' mod='envoimoinscher'}
            <a href="{$website_url|escape:'htmlall':'UTF-8'}" target="_blank" alt="Website"> {l s='boxtale.com' mod='envoimoinscher'} </a>
            {l s='=> sign in => Profile / Preferences.' mod='envoimoinscher'}
        </div>
    {/if}
    
	{if $need_update}
		<div class="bootstrap">
			<div class="alert alert-warning warn">{l s='Your last offers update is one month old, think about updating them in the "Help" section' mod='envoimoinscher'}</div>
		</div>
	{/if}
    
    <link href="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/css/back-office.css" rel="stylesheet" type="text/css" media="all" />
    {if $foreign}
        <link href="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/css/back-office-foreign.css" rel="stylesheet" type="text/css" media="all" />
	{/if}
    
    {if $local_fancybox}
		<link href="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/css/jquery.fancybox.css" rel="stylesheet" type="text/css" media="all" />
		<script type="text/javascript" src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/js/jquery.boxfancy.js"></script>
	{/if}
    
    {if $EMC_config.EMC_USER == 1}
        <div id="warn-online-message" class="alert alert-warning warn warning {if $EMC_config.EMC_SRV_MODE === 'online'}hidden{/if}" >
            {l s='Once the configuration done, set your module "Online" in order to display your offers' mod='envoimoinscher'}
        </div>
	{/if}

	<div class="EMC_box">
		
		<div id="EMC_Infos">
			<div id="emc-infos">
                <div class="logoemc"></div><div class="version"><div class="pt53">{l s='EMC module version' mod='envoimoinscher'} {$module_version|escape:'htmlall':'UTF-8'}</div></div>
                <div><a href="http://ecommerce.envoimoinscher.com/tarifs/?utm_source=prestashop&utm_medium=Referral&utm_campaign=20160412_TarifsPrestaShop" class="button-orange text_align_center mb10" target="_blank">{l s='See rates' mod='envoimoinscher'}</a></div>
                <div class="mb5"><a href="//ecommerce.envoimoinscher.com/api/download/doc_prestashop_configurer.pdf" target="_blank" class="action_module">{l s='documentation' mod='envoimoinscher'}</a></div>	
                <div class="mb5"><a href="//ecommerce.envoimoinscher.com/api/download/doc_prestashop_expedier.pdf" target="_blank" class="action_module">{l s='sending method' mod='envoimoinscher'}</a></div>
			</div>
			{include file="$tpl_news" tab_news=$tab_news}
		</div>
											
		<div id="EMC_top">
			<div id="EMC_Globals">
				<div class="over-config" id="EMC_state">
					<label>{l s='State of module:' mod='envoimoinscher'}</label>
					<div class="margin-form">
						<!--  title="{l s='Enable your module on your website' mod='envoimoinscher'}" -->
						<div id="online_switch" class="EMC_switch switch prestashop-switch fixed-width-lg">
							<input type="radio" name="module_online" id="module_online" value="1" {if $EMC_config.EMC_SRV_MODE === 'online'}checked{/if}>
							<label class="radioCheck module_online" for="module_online">{l s='Online' mod='envoimoinscher'}</label>
							<input type="radio" name="module_offline" id="module_offline" value="0" {if $EMC_config.EMC_SRV_MODE !== 'online'}checked{/if}>
							<label class="radioCheck module_offline" for="module_offline">{l s='Offline' mod='envoimoinscher'}</label>
							<a class="slide-button btn"></a>
							<span class="cover"></span>
						</div>
						<script>
							$("#online_switch").on('click', function(event){
								if (event.currentTarget !== this) {
									return;
                                }
								{if $EMC_config.EMC_USER == 1}
                                    EMCChangeState(this);
                                {/if}
							});
						</script>
					</div>
				</div>
				<div class="over-config">
					<label>{l s='Work environment:' mod='envoimoinscher'}</label>
					<div class="margin-form">
						<!-- title="{l s='Enable your module on your website' mod='envoimoinscher'}" -->
						<div id="env_switch" class="EMC_switch switch prestashop-switch fixed-width-lg">
							<input type="radio" name="api_prod" id="api_prod" value="PROD" {if $EMC_config.EMC_ENV == 'PROD'} checked{/if}/>
							<label class="radioCheck api_prod" for="api_prod">{l s='Prod.' mod='envoimoinscher'}</label>
							<input type="radio" name="api_test" id="api_test" value="TEST" {if $EMC_config.EMC_ENV == 'TEST'} checked{/if}/>
							<label class="radioCheck api_test" for="api_test">{l s='Test' mod='envoimoinscher'}</label>
							<a class="slide-button btn"></a>
							<span class="cover"></span>
						</div>
						<script>
							$("#env_switch").on('click', function(event){
								if (event.currentTarget !== this) {
									return;
                                }
                                {if $EMC_config.EMC_USER == 1}
                                    EMCChangeEnv(this);
                                {/if}
							});
						</script>
					</div>
				</div>
				<div class="over-config">
					<label>{l s='Cache managment:' mod='envoimoinscher'}</label>
					<div class="margin-form">
						<a id="cleanCache" class="btn btn-default" href="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall':'UTF-8'}&option=cleanCache">
							{l s='Clear the cache:' mod='envoimoinscher'}
						</a>
					</div>
					<!--<div class="margin-form add-tooltip" title="{l s='Clear the offers cache' mod='envoimoinscher'}">
					</div>-->
				</div>
			</div>
			<div id="EMC_Menu">
				<ul>
					<li class="merchant{if $default_tab === 'merchant'} active{/if}">
						<a href="#EMC_tab" data-tab="merchant">
						</a>
						<div>
							{l s='Merchant account' mod='envoimoinscher'}
						</div>
					</li>
					<li class="sends{if $default_tab === 'sends'} active{/if}">
						<a href="#EMC_tab" data-tab="sends">
						</a>
						<div>
							{l s='Sends description' mod='envoimoinscher'}
						</div>
					</li>
					<li class="simple_carriers{if $default_tab === 'simple_carriers'} active{/if}">
						<a href="#EMC_tab" data-tab="simple_carriers">
						</a>
						<div>
							{l s='Weight-based carriers' mod='envoimoinscher'}
						</div>
					</li>
					<li class="advanced_carriers{if $default_tab === 'advanced_carriers'} active{/if}">
						<a href="#EMC_tab" data-tab="advanced_carriers">
						</a>
						<div>
							{l s='Size and weight based carriers' mod='envoimoinscher'}
						</div>
					</li>
					<li class="simulator">
						<a id="simulator-link" href="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall':'UTF-8'}&option=tests" target="_blank" data-tab="simulator">
						</a>
						<div>
							{l s='Rates simulator' mod='envoimoinscher'}
						</div>
					</li>
                    <li class="settings{if $default_tab === 'settings'} active{/if}">
						<a href="#EMC_tab" data-tab="settings">
						</a>
						<div>
							{l s='Advanced settings' mod='envoimoinscher'}
						</div>
					</li>
					<li class="help{if $default_tab === 'help'} active{/if}">
						<a href="#EMC_tab" data-tab="help">
						</a>
						<div>
							{l s='Help' mod='envoimoinscher'}
						</div>
					</li>
				</ul>
			</div>
		</div>

	</div>

	<script src="{$modulePath|escape:'htmlall':'UTF-8'}views/js/jquery.tooltipster.min.js"></script>
	<script type="text/javascript">
		{literal}
		var EMC_modify = false;
    
		$(document).ready(function() {     
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
							alert("{/literal}{l s='an error occured : cache clear' mod='envoimoinscher'}{literal}");
						}
						else
						{
							alert("{/literal}{l s='cache succefuly cleared' mod='envoimoinscher'}{literal}");
						}
					}
				});
				return false;
			});
        }); 
		
		function loadAllCarriers(){
			$('#carriers_update_result').html("");
			$.ajax({
				url: $("#loadAllCarriers").attr("rel"),
				type: "GET", 
				dataType: "json", 
				success : function(ret)
				{
					var message = "";
					if (ret.offers_added.length == 0 && ret.offers_updated.length == 0 && ret.offers_deleted.length == 0)
					{
						message = "<p>{/literal}{l s='no offer update avaliable' mod='envoimoinscher'}{literal}</p>";
					}
					else
					{
						message = "<p class='green_color'>{/literal}{l s='offer update succeed' mod='envoimoinscher'}{literal}</p><br/>";
						if (ret.offers_added.length > 0)
						{
							message += "<b>" + ret.offers_added.length + " {/literal}{l s='new offers : list' mod='envoimoinscher'}{literal}</b>"
							message += "<ul class='list-style-square ml20'>";
							for (i = 0 ; i < ret.offers_added.length ; i++)
							{
								message += "<li>"+ret.offers_added[i]+"</li>";
							}
							message += "</ul><br/>";
						}
						if (ret.offers_updated.length > 0)
						{
							message += "<b>" + ret.offers_updated.length + " {/literal}{l s='new updated offers : list' mod='envoimoinscher'}{literal}</b>"
							//message += "<br/><b style='color:orange;'>Attention : si un transporteur que vous utilisez est mis à jour, vous devez le supprimer et le recréer via le module pour appliquer les changements de <u>description</u></b>"
							message += "<ul class='list-style-square ml20'>";
							for (i = 0 ; i < ret.offers_updated.length ; i++)
							{
								message += "<li>"+ret.offers_updated[i]+"</li>";
							}
							message += "</ul><br/>";
						}
						if (ret.offers_deleted.length > 0)
						{
							message += "<b>" + ret.offers_deleted.length + " {/literal}{l s='x offers deleted : list' mod='envoimoinscher'}{literal}</b>"
							message += "<ul class='list-style-square ml20'>";
							for (i = 0 ; i < ret.offers_deleted.length ; i++)
							{
								message += "<li>"+ret.offers_deleted[i]+"</li>";
							}
							message += "</ul><br/>";
						}
					}
					$('#carriers_update_result').html(message);
				},
				error : function(ret)
				{
					$('#carriers_update_result').html("<p class='red_color'>{/literal}{l s='Error : cant get carriers list' mod='envoimoinscher'}{literal} <br/><div class='red_color'>"+ret.responseText+"</div></p>");
				}
			});
			return false;
		}
		
		function EMCTooltipHelp() {
			var divClass = '<div class="tooltip"></div>';
			$(".add-tooltip").each(function() {
				var parent = $(this);
				parent.after(divClass);
				parent.next('.tooltip').attr('title', parent.attr('title'));
				parent.removeAttr('title');
				parent.removeClass('add-tooltip');
			});
			// Initialize tooltip
			$('.tooltip').tooltipster({
				position : 'right',
				maxWidth : 350
			});

		}

		function EMCtoggleCarrier (carrier) {
			if ($("#module_online").attr("checked")  == "checked") {
				alert("{/literal}{l s='Please set the module state to off' mod='envoimoinscher'}{literal}");
				return;
			}
			EMC_modify = true;
			var value = carrier.attr('alt');
			if (carrier.parents('tr').hasClass('disabled') === false) {
				var prices = carrier.parents('tr').find('.price').children('div');
				var checkbox = carrier.parent('td').find('input');
        var edit = carrier.parent().parent().find("td.edit .btn-group-action");
        var disableEdit = carrier.parent().parent().find("td.edit .disable-edit");
				if (value === 'true') {
					carrier.parents('tr').find('.parcelPoint').fadeOut();
					prices.fadeOut();
					carrier.attr('alt', 'false');
					checkbox.attr('checked', false);
					carrier.attr('src', '../img/admin/disabled.gif');
          edit.addClass("hide");
          disableEdit.hasClass("hide") ? disableEdit.removeClass("hide") : '';
				} else {
					carrier.parents('tr').find('.parcelPoint').fadeIn();
					prices.fadeIn();
					carrier.attr('alt', 'true');
					checkbox.attr('checked', true);
					carrier.attr('src', '../img/admin/enabled.gif');
          edit.removeClass('hide');
          disableEdit.addClass('hide');
				}
			}
		}
		{/literal}
	</script>
    <fieldset id="EMC_Content" class="panel">
        <img src="../img/loader.gif" alt="" />
    </fieldset>
    <script type="text/javascript">
        {literal}

            var EMC_load = false;
            var currentValueInput = null;
            var envUrl = "{/literal}{$envUrl|escape:'htmlall':'UTF-8'}{literal}";
            var changingEnv = false;
            $(function(){
                EMCGetContentAjax("{/literal}{$default_tab|escape:'htmlall':'UTF-8'}{literal}");
                $("#EMC_Menu > ul > li").click(function(){
                    // cas special de simulation
                    if ($(this).hasClass('simulator')){
                        window.open($("#simulator-link").attr("href"));
                        return false;
                    }
                    
                    if ($("#EMC_Menu > ul > li.active").hasClass('simple_carriers') || $(this).hasClass('simple_carriers') || $(this).hasClass('advanced_carriers') || $("#EMC_Menu > ul > li.active").hasClass('advanced_carriers')) {
                        if (EMC_modify === true) {
                            var message = '{/literal}{html_entity_decode(addslashes({l s='Did you save your settings before exiting?' mod='envoimoinscher'}))}{literal}';
                            $(window).bind('beforeunload', function(){
                                return message;
                            });

                            if ($("#EMC_Menu > ul > li.active").hasClass('simple_carriers') || $("#EMC_Menu > ul > li.active").hasClass('advanced_carriers'))
                                if (!confirm(message))
                                    return false;

                            if (!$(this).hasClass('simple_carriers') && !$(this).hasClass('advanced_carriers')) {
                                $(window).unbind('beforeunload', '');
                            }
                        }
                    }

                    $("#EMC_Menu > ul > li").removeClass("active");
                    $(this).addClass('active');
                    EMCGetContentAjax($(this).children('a').data('tab'));
                    return false;
                });
            });

            function EMCGetContentAjax(tab) {
                if(EMC_load === true)
                    return;
                EMC_load = true;
                $("#EMC_Content").html('<div class="center"><img src="../img/loader.gif" alt="" /></div>');
                $.ajax({
                    type : "POST",
                    url : "",
                    data : "ajax=1&EMC_tab=" + tab,
                    success : function (msg) {
                        currentValueInput = null
                        EMC_modify = false;
                        $("#EMC_Content").html(msg);
                        $(".row input[type=radio],.row input[type=checkbox]").change(function(){
                            EMC_modify = true;
                        });
                        $(".row input[type=text]").focusin(function(){
                            currentValueInput = $(this).val();
                        });
                        $(".row input[type=text]").focusout(function(){
                            if (currentValueInput != $(this).val())
                                EMC_modify = true;
                        });

                        $("input[name=btnCarriersSimple], input[name=btnCarriersAdvanced]").click(function(){
                                $(window).unbind('beforeunload', '');
                        });

                        $(".fancybox").fancybox({
                            'width'			: 1000,
                            'height'		: 760,
                            'autoDimensions': false,
                            'autoScale'		: false
                        });
                        EMC_load = false;
                        EMCTooltipHelp();
                    },
                    error : function(msg){
                        console.log("Error : " + msg);
                    }
                });
            }

            function EMCChangeState(state) {
                
                var module_on = $(state.querySelector("input")).attr("checked") != undefined;
                // Go off
                if(module_on) {
                    if (!confirm("{/literal}{l s='Are you sure to offline the module? (Carriers Envoimoinscher will not be available to discount module)' mod='envoimoinscher'}{literal}")) {
                        return false;
                    }
                    
                    // delete checked
                    $(state.querySelector("input")).removeAttr("checked");
                    // set new attr 
                    $(state.querySelectorAll("input")[1]).attr("checked", "checked");

                    $("#warn-online-message").fadeIn();
                }
                // Go on
                else {
                    
                    // delete checked
                    $(state.querySelectorAll("input")[1]).removeAttr("checked");
                    // set new attr 
                    $(state.querySelector("input")).attr("checked", "checked");

                    $("#warn-online-message").fadeOut();
                }

                var newStatus = !module_on;
                $.ajax({
                    type : "POST",
                    url : "",
                    data : "ajax=1&EMC_Status=" + newStatus,
                    success : function (msg) {
                        if(!newStatus) {
                            $(".offersList input").attr("disabled", false);
                            $(".offersList button.fancybox").attr("disabled", false);
                        }
                        else {
                            $(".offersList input").attr("disabled", true);
                            $(".offersList button.fancybox").attr("disabled", true);
                        }
                    }
                });
            }

            function EMCChangeEnv(env) {
                if (changingEnv){
                    return false;
                }
                changingEnv = true;
                var module_test = $(env.querySelector("input")).attr("checked") != undefined;
                if(module_test) {
                    $(env.querySelector("input")).removeAttr("checked");
                    $(env.querySelectorAll("input")[1]).attr("checked", "checked");
                }
                else {
                    $(env.querySelectorAll("input")[1]).removeAttr("checked");
                    $(env.querySelector("input")).attr("checked", "checked");
                }

                
                var newEnv = $('#env_switch input[checked="checked"]').attr('value');
                
                $.ajax({
                    type : "POST",
                    url : "",
                    data : "ajax=1&EMC_Env=" + newEnv,
                    success : function (msg) {
                        window.location.reload();
                    }
                });
            }

            function openPopupEmc(ope) {
                var post_code = $("#EMC_exp_postcode").val();
                var address = $("#EMC_exp_address").val();
                var city = $("#EMC_exp_town").val();

                var popin_name = 'emcwindow';
                var popin_width = 800;
                var popin_height = 680;
                var popin_scrollbars = 1;
                var popin_resizable = 1;

                if(ope == "SOGP") {
                    window.open(
                        envUrl + "/magento_rc.html?fcp="+ post_code +"&fadr=" + address + "&fvil=" + city + "&TypeLiv=REL&type=Exp&isPrestashop=true",
                        popin_name,
                        "scrollbars=" + popin_scrollbars + ", resizable=" + popin_resizable + ",width=" + popin_width + ",height=" + popin_width + ""
                    );
                } else if(ope == "MONR") {
                    window.open(
                        envUrl + "/modules_monr.html?isPrestashop=true&isModule=true&monrCp=" + post_code + "&monrVille=" + city + "&monrPays=FR&monrPoids=1",
                        popin_name,
                        "scrollbars=" + popin_scrollbars + ", resizable=" + popin_resizable + ",width=" + popin_width + ",height=" + popin_width + ""
                    );
                }
            }
        {/literal}
    </script>
	{if $EMC_config.EMC_USER == 0}
		<div id="popup_onboarding" class="hidden">
            <div class="close_onboarding pointer"></div>
			<ul class="EMC_steps">
				<li>
					<a class="{if $step == "intro"}selected{else}selected old btnPrev pointer-important{/if}">
						<label for="" class="stepNumber">1</label>
						<span class="stepDesc">{l s='Introduction' mod='envoimoinscher'}</span>.
                        {if $step == "emc"}
                            <form method="POST" class="hidden" id="btnPrev">
                                <input type="hidden" name="previous" value="1" />
                                <input type="submit">
                            </form>
                        {/if}
					</a>
				</li>
				<li>
					<a{if $step == "emc"} class="selected"{/if}>
						<label for="" class="stepNumber">2</label>
						<span class="stepDesc">{l s='EnvoiMoinsCher account' mod='envoimoinscher'}</span>
					</a>
				</li>
			</ul>
			<div>
				<fieldset>
					{$popup}
				</fieldset>
			</div>
		</div>
		<script type="text/javascript">
			{literal}
			$(function(){
				var content = $("#popup_onboarding").html();
				var toAppend = '<div id="EMC_cfg_bg"></div>';
				{/literal}
				{if $PS_ver == "1"}
					{if $PS_subver == "5"}
						toAppend = '<div id="EMC_cfg_bg" class="presta_1_5"></div>';
					{else}
						toAppend = '<div id="EMC_cfg_bg" class="presta_1_6"></div>';
					{/if}
				{/if}
				{literal}
				
				$("#content").append(toAppend);
				$("#popup_onboarding:eq(0)").remove();
				var element = $("#EMC_cfg_bg");
				element.html('<div id="popup_onboarding">' + content + '</div>');
                if ($('.module_error').length > 0) {
                    $('.EMC_steps').after('<div class="bootstrap">'+$('.module_error').closest('.bootstrap').html()+'</div>');
                }
                {/literal}
                {if $open_popup}
                    element.show();
                {/if}
                {literal}
				$("#content > .warn").appendTo("#EMC_cfg_bg #popup_onboarding fieldset");

				$(".btnValid").click(function() {
					if ($(".btnValid").hasClass('selected'))
					{
						$(this).parent().prev('div').find('fieldset').find('input[type=submit]').click();
					}
				});

				$(".btnClose").click(function() {
					if ($(".btnClose").hasClass('selected'))
					{
						$(this).parent().prev('div').find('fieldset').find('input[type=submit]').click();
					}
				});

				$(".btnPrev").click(function() {
					if ($(".btnPrev").hasClass('selected'))
					{
						$("#btnPrev").submit();
					}
				});

				// Initialize tooltip
				$('.tooltip').tooltipster({
					position : 'right',
					maxWidth : 350
				});

				EMCTooltipHelp();
			});
            
            $(document).ready(function() {     
                $('.close_onboarding').click(function() {
                    $('#EMC_cfg_bg').hide();
                });
            }); 
			{/literal}
		</script>
	{/if}
</div>