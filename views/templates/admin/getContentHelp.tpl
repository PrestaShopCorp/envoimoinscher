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

<fieldset id="EMC_help">
	<legend>
		{l s='Help and upgrade' mod='envoimoinscher'}
	</legend>
	<div id="help-update">
		<h2 class="title-help">{l s='Upgrade' mod='envoimoinscher'}</h2>
	<li class="main-question">
	{if isset($upgrades) && $upgrades && sizeof($upgrades)}
		<h2>{l s='SQL update' mod='envoimoinscher'}</h2></li>
		<table class="table upgradeList">
			<thead>
				<tr>
					<th class="center">
						{l s='Version' mod='envoimoinscher'}
					</th>
					<th class="center">
						{l s='Date' mod='envoimoinscher'}
					</th>
					<th class="center">
						{l s='Changements' mod='envoimoinscher'}
					</th>
					<th class="center">
						{l s='Actions' mod='envoimoinscher'}
					</th>
				</tr>
			</thead>
			<tbody>
				{if isset($upgrades) && $upgrades && sizeof($upgrades)}
					{foreach from=$upgrades item='upgrade' key='u'}
						<tr>
							<td class="center">
								{$upgrade.from|escape:'htmlall':'UTF-8'} - {$upgrade.to|escape:'htmlall':'UTF-8'}
							</td>
							<td class="center">
								{$upgrade.date|escape:'htmlall':'UTF-8'}
							</td>
							<td class="center">
								{$upgrade.description}
							</td>
							<td class="center">
								<a class="button" href="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall':'UTF-8'}&option=upgrade&up_id={$u|escape:'htmlall':'UTF-8'}" class="action_module">effectuer</a>
							</td>
						</tr>
					{/foreach}
				{else}
					<tr>
						<td colspan="4">
							<div class="warn">
								{l s='No upgrade at this time' mod='envoimoinscher'}
							</div>
						</td>
					</tr>
				{/if}
			</tbody>
		</table>
	{/if}
	<li class="main-question"><h2>{l s='Carriers update' mod='envoimoinscher'}</h2></li>
	<p class="ml30-important">{l s='Update all the carriers:' mod='envoimoinscher'}<a id="loadAllCarriers" onclick="loadAllCarriers();" class="action_module" rel="{$link->getAdminLink('AdminEnvoiMoinsCher')|escape:'htmlall':'UTF-8'}&option=loadAllCarriers">{l s='Update carriers' mod='envoimoinscher'}</a></p>
	<div id="carriers_update_result"></div>
	</div>
	
	<div id="help-question">
		<h2 class="title-help">{l s='Common questions' mod='envoimoinscher'}</h2>
		<div >
			<div>
				<div class="openable-title closed">
					<div class="title">
						<p>
							<img src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/img/arrow_right.png" class="imgSwitchFaq" class="imgSwitchFaq" alt="clic to switch">
							{l s='install, activation and test of module' mod='envoimoinscher'}
						</p>
					</div><!-- eod  -->
				</div><!-- eod  -->
				<div class="bloc-reponses">
					<ul class="list_question">
						<li class="noliststyle">
							<div class="subtitle closed">
								<p>
									{l s='Can i test my module?' mod='envoimoinscher'}
								</p>
							</div>
							<div class="response">
								<div>
									<p>
										{l s='you can test your settings, enable test mode' mod='envoimoinscher' js=1}<br />
										{l s='try the simulation page, you can see your shipment cost like in front' mod='envoimoinscher' js=1}
									</p>
								</div><!-- eod  -->
							</div>
						</li>
						<li class="noliststyle">
							<div class="subtitle closed">
								<p>
									{l s='how to enable production mode' mod='envoimoinscher'}
								</p>
							</div>
							<div class="response">
								<div>
									<p>
									 	{l s='enable production env check, on the top of emc module' mod='envoimoinscher' js=1}
									</p>
								</div><!-- eod  -->
							</div>
						</li>
						<li class="noliststyle">
							<div class="subtitle closed">
								<p>
									{l s='i enabled production mode and i got "invalid account payment method"' mod='envoimoinscher'}
								</p>
							</div>
							<div class="response">
								<div>
									<p>
										{l s='emc module require deferred paiement, error code displayed only in production mode' mod='envoimoinscher' js=1}
										{l s='to switch to defered paiement, please go on emc website or contact us' mod='envoimoinscher' js=1}
										<a href="mailto:{l s='compta@envoimoinscher.com' mod='envoimoinscher'}">{l s='compta@envoimoinscher.com' mod='envoimoinscher'}</a><br />
										{l s='you must give use login, iban and sepa contract' mod='envoimoinscher' js=1}
									</p>
								</div><!-- eod  -->
							</div>
						</li>
						<li class="noliststyle">
							<div class="subtitle closed">
								<p>
									{l s='folowing error displayed : contact our billing service, what should i do' mod='envoimoinscher'}
								</p>
							</div>
							<div class="response">
								<div>
									<p>
									 	{l s='thats a paiement issue, some exemples, please contact accountancy' mod='envoimoinscher' js=1}<a href="mailto:{l s='compta@envoimoinscher.com' mod='envoimoinscher'}">{l s='compta@envoimoinscher.com' mod='envoimoinscher'}</a>
									</p>
								</div><!-- eod  -->
							</div>
						</li>
					</ul>
				</div>
				<div class="openable-title closed">
					<div class="title">
						<p>
							<img src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/img/arrow_right.png" class="imgSwitchFaq" alt="clic to switch">
							{l s='general module configuration' mod='envoimoinscher'}
						</p>
					</div><!-- eod  -->
				</div>
				<div class="bloc-reponses">
					<div>
						<ul class="list_question">
							<li class="noliststyle">
								<div class="response">
									<p>
										{l s='A documentation is available here:' mod='envoimoinscher'}<a href="http://ecommerce.envoimoinscher.com/api/download/doc_prestashop_configurer.pdf" target="_blank" class="action_module">{l s='documentation' mod='envoimoinscher'}</a><br/>	
										{l s='A documentation is also provided about sending method:' mod='envoimoinscher'}<a href="http://ecommerce.envoimoinscher.com/api/download/doc_prestashop_expedier.pdf" target="_blank" class="action_module">{l s='sending method' mod='envoimoinscher'}</a><br/>
										{l s='Contact customer service for help' mod='envoimoinscher'} 
									</p>
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to configure merchant account settings' mod='envoimoinscher'} 
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											 {l s='login and password are the emc login' mod='envoimoinscher'} <a href="{l s='http://www.envoimoinscher.com/' mod='envoimoinscher'}" target="_blank">{l s='www.envoimoinscher.com' mod='envoimoinscher'}</a>. {l s='it allow you to login on emc website, the api key provided by emc' mod='envoimoinscher'}
										</p>
										<ul>
											<li>
												{l s='set the test api key to set up the module then test' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='once you are ready, you can set the prod key' mod='envoimoinscher' js=1} {l s='one api key can be used by mutliple website, if your accountancy is different, create another account' mod='envoimoinscher' js=1} 
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to configure shipment settings' mod='envoimoinscher'}
									</p>
								</div>
								<div class="response">
									<div>
										<ul>
											<li>
												{l s='choose between envelop, parcel, palett' mod='envoimoinscher' js=1}<br/>
												{l s='carefull, choose parcel even if you send buble envelope, pickup points dont allow envelope' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='choose shipping type among the list, choose the most important one' mod='envoimoinscher' js=1}<br/>
												{l s='carefull, some shipment type are borbidden with somes carriers, contact customers service for more informations' mod='envoimoinscher' js=1}
												{l s='packaging is needed if colissimo avaliable, if you use mailing tubes, -price-' mod='envoimoinscher' js=1}
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to configure carriers settings' mod='envoimoinscher'}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='choose among carriers list, if you enable the carrier, you make it visible by your client, select real price or contract' mod='envoimoinscher' js=1}
										</p>
										<ul>
											<li>
												{l s='real price: the front office price is the emc price, the shipment price, you do not have nothing to configure' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='contract: displayed shipping cost are those you set' mod='envoimoinscher' js=1}
											</li>
										</ul>
                                        <br/>
										<p>
											{l s='to set you contract, go in shipping configuration and define shipping grid' mod='envoimoinscher'}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='what the difernce between simple and advanced carriers, how to configure advanced carrier' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='simple carrier calculate shipping cost based on weight' mod='envoimoinscher' js=1}<br/>
											{l s='advanced carriers calculate shipping cost based on weight and size' mod='envoimoinscher' js=1}<br/>
											{l s='parcel bulk could not be calculated inreal time, you have to set a grid size and weight' mod='envoimoinscher' js=1}<br/>
											{l s='you have to set your size and weight if you dont want the default ones' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to set free shippment with emc module' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='carefull, do not enable free shipping in prestashop, you can make shipping free with range configuration' mod='envoimoinscher' js=1}
										</p>
										<p>
											{l s='choose contract prices with one carrier, go in carriers configuration, define range in the proper section' mod='envoimoinscher' js=1}
										</p>
										<p>
											{l s='for exemple, if you set free shipment after 50$, define a range 0 - 50 and antoher 50 - 100 000' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='can i ship parcel in just-in-time distribution' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='just-in-time distribution is no an issue, just clic on -ship- when parcel is ready' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="openable-title closed">
					<div class="title">
						<p>
							<img src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/img/arrow_right.png" class="imgSwitchFaq" alt="clic to switch">
							{l s='most frequent issues with settings' mod='envoimoinscher' js=1}
						</p>
					</div><!-- eod  -->
				</div>
				<div class="bloc-reponses">
					<div>
						<ul class="list_question">
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='offers does not appear' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='If no offers, ask yourself these questions:' mod='envoimoinscher'}
										</p>
										<ul>
											<li><b>{l s='Is the module online?' mod='envoimoinscher'}</b> {l s='If not, set your module online' mod='envoimoinscher'}</li>
											<li><b>{l s='Are my credentials corrects?' mod='envoimoinscher'}</b> {l s='If not, there should be an error' mod='envoimoinscher'}</li>
											<li><b>{l s='Did i just changed my configuration?' mod='envoimoinscher'}</b> {l s='If not, you should clean the cache' mod='envoimoinscher'}</li>
											<li><b>{l s='Are my articles weights correct?' mod='envoimoinscher'}</b> {l s='If not, you should set your products weight' mod='envoimoinscher'}</li>
											<li><b>{l s='Is the module activated?' mod='envoimoinscher'}</b> {l s='Check if it is activated' mod='envoimoinscher'}</li>
											<li><b>{l s='Is there a valid address on my client account?' mod='envoimoinscher'}</b> {l s='Try with a simple valid address' mod='envoimoinscher'}</li>
										</ul>
										<p>
											{l s='Check your logs and contact the technical service' mod='envoimoinscher'}<br/>
											{l s='Access to your logs:' mod='envoimoinscher'}<a href="{$link->getAdminLink('AdminLogs')|escape:'htmlall':'UTF-8'}" target="_blank" class="action_module">logs</a>
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='customers prices dont match with thoses i set' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='If your prices are wrong, ask yourself these questions.' mod='envoimoinscher'}
										</p>
										<p class="bold">
											{l s='If you are using the real price:' mod='envoimoinscher'}
										</p>
										<ul>
											<li>
												<b>{l s='Are your article\'s weight correct?' mod='envoimoinscher'}</b> {l s='Check if your products weight are correct' mod='envoimoinscher'}
											</li>
										</ul>
										<p class="bold">
											{l s='If you are using the package price:' mod='envoimoinscher'}
										</p>
										<ul>
											<li>
												<b>{l s='Are my shippers in package mode?' mod='envoimoinscher'}</b> {l s='If not, change your shippers to package mode' mod='envoimoinscher'}
											</li>
											<li>
												<b>{l s='Are my weight/price ranges correct?' mod='envoimoinscher'}</b> {l s='Check if your weight/price ranges are correct' mod='envoimoinscher'}
											</li>
										</ul>
										<p>
											{l s='If prices are still incorrect, contact the technical service' mod='envoimoinscher'}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='Why do the parcel points aren\'t here?' mod='envoimoinscher'}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='If the parcel points aren\'t here, ask yourself these questions:' mod='envoimoinscher'}
										</p>
										<ul>
											<li>
												<b>{l s='Am i using a custom theme?' mod='envoimoinscher'}</b> {l s='A custom theme can be a problem' mod='envoimoinscher'}
											</li>
											<li>
												<b>{l s='Do i use the "free shipping" option?' mod='envoimoinscher'}</b> {l s='The free shipping option do not work' mod='envoimoinscher'}
											</li>
											<li>
												<b>{l s='Is there any parcel point near this address?' mod='envoimoinscher'}</b> {l s='Maybe there is no parcel points near your address' mod='envoimoinscher'}
											</li>
										</ul>
										<p>
											{l s='Parcel points are still not here? contact the technical service' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='why prices are different between website and bacloffice' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='the module give pre taxes prices, you have to set the tva, go carrires setup set the tva' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="openable-title closed">
					<div class="title">
						<p>
							<img src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/img/arrow_right.png" class="imgSwitchFaq" alt="clic to switch">
							{l s='shipment, tracking and insurance' mod='envoimoinscher' js=1}
						</p>
					</div><!-- eod  -->
				</div>
				<div class="bloc-reponses">
					<div>
						<p class="bold pl50">
							{l s='here is some documentation about shipment:' mod='envoimoinscher' js=1} <a href="{l s='link doc shipment pdf' mod='envoimoinscher' js=1}" target="_blank">{l s='here' mod='envoimoinscher' js=1}</a> 
						</p>
						<ul class="list_question">
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='where do i ship my orders ?' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='go in orders sections and clic on emc' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to read orders list' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='tree list appears:' mod='envoimoinscher' js=1}
										</p>
										<ul>
											<li>
												{l s='emc orders: your client chose an emc carrier' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='not emc orders: your client chose another carrier, you can replace it by an emc carrier and ship it manualy' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='orders with error: lake of informations or validating errors' mod='envoimoinscher' js=1}
												
												{l s='most of the time, they are order from first list, you can try to ship them again, you may select another offer if the current is not avaliable' mod='envoimoinscher' js=1}
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to ship one by one emc orders' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<ul>
											<li>
												<b>{l s='clic on ship button on the right of the line' mod='envoimoinscher' js=1}</b><br/>
												{l s='a confirmation appear, you can modify data like weight, date ...' mod='envoimoinscher' js=1}
											</li>
											<li>
												<b>{l s='end by ship button' mod='envoimoinscher'}</b><br/>
												{l s='in case of dropoff, dropoff demand is sent to the carrier' mod='envoimoinscher' js=1}
											</li>
											<li>
												<b>{l s='if all shipments went fine, an history list is updated' mod='envoimoinscher' js=1}</b><br/>
												{l s='in case of error, errors list and history list is updated' mod='envoimoinscher' js=1}
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to mass shiping with emc carriers' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<ul>
											<li class="bold">
												{l s='select orders to ship' mod='envoimoinscher' js=1}
											</li>
											<li>
												<b>{l s='clic on ship with or without verfication' mod='envoimoinscher'}</b><br/>
												{l s='theres a bloc which show you the status, you can stop it anytime' mod='envoimoinscher' js=1}
											</li>
											<li>
												<b>{l s='end by ship button' mod='envoimoinscher'}</b><br/>
												{l s='in case of dropoff, dropoff demand is sent to the carrier' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='if all shipments went fine, an history list is updated' mod='envoimoinscher' js=1}<br/>
												{l s='in case of error, errors list and history list is updated' mod='envoimoinscher' js=1}
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to ship whith a non emc carrier' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='you can select emc carrier to replace non emc carrier' mod='envoimoinscher' js=1}
										</p>
										<ul class="bold">
											<li>
												{l s='in emc orders, non emc list' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='select the order you want to ship' mod='envoimoinscher'}
											</li>
											<li>
												{l s='clic on ship in non emc carriers' mod='envoimoinscher'}
											</li>
											<li>
												{l s='choose the emc carrier and replace by this offer' mod='envoimoinscher'}
											</li>
											<li>
												{l s='verify the order and ship' mod='envoimoinscher' js=1}
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to use multi parcel' mod='envoimoinscher'}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='you can use multi parcel, 2steps:' mod='envoimoinscher' js=1}
										</p>
										<ol class="bold">
											<li>
												{l s='enable module multi parcel' mod='envoimoinscher'}
											</li>
											<li>
												{l s='check each order for multi parcel' mod='envoimoinscher'}
											</li>
										</ol>
										<p class="bold italic">
											{l s='configure module step1' mod='envoimoinscher'}
										</p>
										<p>
											{l s='multi parcel will be avaliable if you check in config, in shipment details' mod='envoimoinscher' js=1}
										</p>
										<p class="bold italic">
											{l s='multi parcel each order step2' mod='envoimoinscher'}
										</p>
										<p>
											<b>{l s='Multi-parcel is available every time you go through the shipment verification screen.' mod='envoimoinscher' js=1}</b>
											{l s='Multi-parcel has to be indicated in the "Obligatory informations" block.' mod='envoimoinscher'}
										</p>
										<p>
											{l s='to use multi parcel, set number of parcel superior or equal 2' mod='envoimoinscher' js=1}<br/>
											{l s='total weight will be devised by the parcel number' mod='envoimoinscher' js=1}
										</p>
										<p>
											{l s='you could modify the parcel weight, price will be updated' mod='envoimoinscher' js=1}<br/>
											{l s='after validation, slip will be generated' mod='envoimoinscher' js=1}
										</p>
										<p>
											<b>{l s='Attention:' mod='envoimoinscher' js=1} {l s='it may have a price difference' mod='envoimoinscher' js=1}</b> 
											{l s='the customer pay one parcel, you pay several parcel at once, price can be more expensive or cheaper' mod='envoimoinscher' js=1}
										</p>
										<p>
											{l s='a slip is charged, used or not' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='what specifications for foreign coutries' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='Attenion:' mod='envoimoinscher' js=1} {l s='for foreign coutries shipment, you have to declare in french and english' mod='envoimoinscher' js=1}
										</p> 
										<p>
											{l s='french description is made from product name of your articles' mod='envoimoinscher' js=1}<br/>
											<b>{l s='for the english description, you can use the link translate' mod='envoimoinscher'}</b>
										</p>
										<p class="italic">
											{l s='notices' mod='envoimoinscher' js=1}
										</p>
										<ul>
											<li>
												{l s='for foreign countries shippments, the module does not block if description is not in english, be carefull' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='you have to join to the parcel 5 invoice copies' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='any specific documentation or particularity is responsability' mod='envoimoinscher' js=1}
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='how to download slips' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='3 possibility to fetch slips, from ps, by mail, by the website' mod='envoimoinscher' js=1}
										</p>
										<ul>
											<li>
												<em>{l s='from prestashop italic' mod='envoimoinscher'}</em><br/>
												{l s='go to orders then emc, history. clic on download slips or check and download severals slips' mod='envoimoinscher' js=1}
											</li>
											<li>
												<em>{l s='by email italic' mod='envoimoinscher'}</em><br/>
												{l s='you can recieve by mail, check in configuration the correct settings' mod='envoimoinscher' js=1}
											</li>
											<li>
												<em>{l s='from emc website, account, preference' mod='envoimoinscher'}</em>
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='i want to ship my order but i get an error and the order disappear' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='orders which can not be shipped are in the errors orders list' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='My slip does not generate itself after shipment.' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='After sending a Envoimoinscher order, a link to the slip must appear in the list of sent parcels.' mod='envoimoinscher'} {l s='If ever this link is still being generated, here are the questions you have to ask yourself:' mod='envoimoinscher'}
										</p>
										<ul>
											<li>
												<em>{l s='Did I wait long enough?' mod='envoimoinscher' js=1}</em> {l s='Depending on the carrier, generation can take up to 5 minutes, so you have to wait a bit.' mod='envoimoinscher' js=1}
											</li>
											<li>
												<em>{l s='Am I in a test environment?' mod='envoimoinscher' js=1}</em> {l s='If this is the case, the order was not accepted and the slips will not generate themselves.' mod='envoimoinscher' js=1} {l s='If your order was real, you must first enter the production environment (top left of this page: "Environment") and re-place your order on our website, do not forget to log into your account for the order to be added to your bill.' mod='envoimoinscher' js=1} 
												<a href="{l s='http://www.envoimoinscher.com' mod='envoimoinscher'}" target="_blank">{l s='Click here to access Envoimoinscher website.' mod='envoimoinscher'}</a>
											</li>
										</ul>
										<p>
											{l s='Contact technical service (<a href="mailto:informationAPI@envoimoinscher.com"> informationAPI@envoimoinscher.com </a>) stating your problem and an access to your back office (url, login, password) to accelerate the resolution of your problem.' mod='envoimoinscher' js=1} {l s='A response will be made ​​as soon as possible.' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='How to track my shipment?' mod='envoimoinscher' js=1} {l s='How to set up shipment tracking for my customers?' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='The configuration of shipment tracking takes place in 2 stages:' mod='envoimoinscher' js=1}
										</p>
										<ol class="bold">
											<li>
												{l s='Configure your Envoimoinscher module for choosing the tracking number' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='Fill in the URL tracking numbers for every carrier' mod='envoimoinscher' js=1}
											</li>
										</ol>
										<p class="bold italic">
											{l s='Configure your module (step 1)' mod='envoimoinscher' js=1}
										</p>
										<p>
											{l s='It is important to configure the module to choose the type of tracking number desired.' mod='envoimoinscher' js=1}<br/>
											{l s='Go to "Settings".' mod='envoimoinscher'} {l s='Two choices are available: display the "Envoimoinscher" tracking number or the "carrier" tracking number.' mod='envoimoinscher' js=1}
										</p>
										<p>
											<b>{l s='Attention:' mod='envoimoinscher'}</b> {l s='We advise you to choose as a type of tracking number carrier so that your customer can track the parcel online through the carrier s website.' mod='envoimoinscher'}
										</p>
										<p>
											{l s='The information will be more apparent to your client.' mod='envoimoinscher' js=1}
										</p>
										<p class="bold italic">
											{l s='Configure the carrier tracking URLs (step 2)' mod='envoimoinscher'}
										</p>
										<p>
											{l s='Secondly you will have to configure the carrier tracking URLs.' mod='envoimoinscher'} {l s='Go to the "Delivery" tab then the "Carrier" subtab.' mod='envoimoinscher' js=1} {l s='Fill the "tracking URL" box with the corresponding URL.' mod='envoimoinscher' js=1}
										</p>
										<p>
											<em>{l s='Carrier tracking URLs with Envoimoinscher module (for information)' mod='envoimoinscher'}</em><br/>
											{l s='Note: These URLs are provided for information only and can be changed without notice.' mod='envoimoinscher' js=1}
										</p>
										<ul>
											<li>
												<a href="http://www.chronopost.fr/expedier/inputLTNumbersNoJahia.do?lang=fr_FR&listeNumeros=@" target="_blank">Chronopost</a>
											</li>
											<li>
												<a href="http://www.mondialrelay.fr/ww2/public/mr_suivi.aspx?cab=@" target="_blank">Mondial relay</a>
											</li>
                                            <li>
												<a href="http://relaiscolis.envoimoinscher.com/suivi-colis.html?reference=@" target="_blank">Relais Colis</a>
											</li>
											<li>
												<a href="https://wwwapps.ups.com/WebTracking/track?HTMLVersion=5.0&loc=fr_FR&Requester=UPSHome&WBPM_lid=homepage%252Fct1.html_pnl_trk&track.x=Suivi&trackNums=@" target="_blank">UPS</a>
											</li>
											<li>
												<a href="http://www.dhl.fr/content/fr/fr/dhl_express/suivi_expedition.shtml?brand=DHL&AWB=@" target="_blank">DHL Express</a>
											</li>
											<li>
												<a href="https://www.fedex.com/fedextrack/?tracknumbers=@" target="_blank">Fedex</a>
											</li>
											<li>
												<a href="http://www.colissimo.fr/portail_colissimo/suivreResultat.do?parcelnumber=@" target="_blank">La Poste</a>
											</li>
											<li>
												<a href="http://www.tnt.fr/public/suivi_colis/recherche/visubontransport.do?radiochoixrecherche=BT&bonTransport=@" target="_blank">TNT</a>
											</li>
										</ul>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='Where can I find the tracking number for my shipments?' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											<em>{l s='For the buyer:' mod='envoimoinscher' js=1}</em> {l s='the buyer will have access to the link in the customer area on your website (to configure in Prestashop).' mod='envoimoinscher' js=1}
										</p>
										<p>
											<em>{l s='For the seller:' mod='envoimoinscher'}</em> {l s='in the module shipping history, click on "track the shipment" in the last column of the table.' mod='envoimoinscher' js=1} {l s='You can also track the shipment from your account on EnvoiMoinsCher.com.' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='How to ensure my shipments Ad Valorem?' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p class="bold">
											{l s='The configuration for the shipments insurance takes place in 2 steps:' mod='envoimoinscher' js=1}
										</p>
										<ol class="bold">
											<li>
												{l s='Configure your Envoimoinscher module to enable the ability to ensure its shipments' mod='envoimoinscher' js=1}
											</li>
											<li>
												{l s='Check insurance on every order you want to ensure' mod='envoimoinscher' js=1}
											</li>
										</ol>
										<p>
											{l s='Yet, your orders will still have the contractual insurance carrier.' mod='envoimoinscher' js=1} {l s='For the conditions of compensation, see the carriers Terms & Conditions on the partner carriers page.' mod='envoimoinscher' js=1}
										</p>
										<p class="bold italic">
											{l s='Configure your module (step 1)' mod='envoimoinscher'}
										</p>
										<p>
											{l s='Insurance will only be available if you check the option in the configuration of your Envoimoinscher module in the "description of the items" tab.' mod='envoimoinscher' js=1}
										</p>
										<p class="bold italic">
											{l s='Check the box when sending the command (step 2)' mod='envoimoinscher' js=1}
										</p>
										<p>
											{l s='Make sure when you send the command that "ensure the shipment" is checked if you want to insure your shipment.' mod='envoimoinscher' js=1} {l s='The charged price is recalculated automatically at the top of the page.' mod='envoimoinscher' js=1}
										</p>
										<p>
											<b>{l s='Attention:' mod='envoimoinscher'}</b> {l s='Insurance costs do not affect the customer.' mod='envoimoinscher' js=1} {l s='If you want the customer to pay the insurance costs, you will have to adjust your rates (price package with insurance costs) or set a fixed cost in the PrestaShop handling fee.' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="openable-title closed">
					<div class="title">
						<p>
							<img src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/img/arrow_right.png" class="imgSwitchFaq" alt="clic to switch">
							{l s='Updates' mod='envoimoinscher' js=1}
						</p>
					</div><!-- eod  -->
				</div>
				<div class="bloc-reponses">
					<div>
						<ul class="list_question">
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='Will the update affect my preferences?' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='The update does not affect your preferences: parameters, carriers ...' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
							<li class="noliststyle">
								<div class="subtitle closed">
									<p>
										{l s='Can I benefit from new transport offerings available through updates?' mod='envoimoinscher' js=1}
									</p>
								</div>
								<div class="response">
									<div>
										<p>
											{l s='Yes.' mod='envoimoinscher' js=1}
										</p>
									</div><!-- eod  -->
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="openable-title closed">
					<div class="title">
						<p>
							<img src="{$emcBaseDir|escape:'htmlall':'UTF-8'}/views/img/arrow_right.png" class="imgSwitchFaq" alt="clic to switch">
							{l s='Another question?' mod='envoimoinscher'}
						</p>
					</div><!-- eod  -->
				</div>
				<div class="bloc-reponses">
					<div>
						<ul class="list_question">
							<li class="noliststyle">
								<div class="subtitle closed">
									<div>
										{l s='If you do not find an answer to your questions in this FAQ, please contact our technical support by explaining your problem to the following address informationapi@envoimoinscher.com.' mod='envoimoinscher' js=1}<br/>
										{l s='Please provide us a superadmin access (url, login, password) so we can take a look at your back office.' mod='envoimoinscher'} {l s='To do so, go to the Prestashop Administration tab, Employees and click on "Add an employee."' mod='envoimoinscher' js=1}
									</div><!-- eod  -->
								</div>
							</li>
						</ul>
					</div>
				</div>

			</div><!-- eod  -->
		</div>
	<script>
		window.emcBaseDir = "{$emcBaseDir|escape:'htmlall':'UTF-8'}";
	</script>
	<script src="{$emcBaseDir|escape:'htmlall':'UTF-8'}views/js/getcontenthelp.js" type="text/javascript"></script>
	</div>

</fieldset>