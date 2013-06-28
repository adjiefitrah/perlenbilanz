<div id="uebersicht">

	<form>
	<div class="centered">
		<div>
			
			<div style="float:left; width:100%;">
				<div style="margin-right: 280px;">
			<textarea class="notes" ng-model="notes" ng-change="saveNotes()"></textarea>
				</div>
			</div>
			
			<table style="float: left; width: 250px; margin-left: -250px;" class="overview">
				<tr>
					<th></th>
					<th>Brutto</th>
					<th>MwSt.</th>
					<th>Netto</th>
				</tr>
				<tr>
					<td>Verkäufe</td>
					<td style="text-align: right;">[[vkBrutto|number_de]] €</td>
					<td style="text-align: right;">[[vkMwSt|number_de]] €</td>
					<td style="text-align: right;">[[vkNetto|number_de]] €</td>
				</tr>
				<tr>
					<td>Einkäufe</td>
					<td style="text-align: right;">[[ekBrutto|number_de]] €</td>
					<td style="text-align: right;">[[ekMwSt|number_de]] €</td>
					<td style="text-align: right;">[[ekNetto|number_de]] €</td>
				</tr>
				<tr class="sum">
					<td>+/-</td>
					<td style="text-align: right;">[[vkBrutto-ekBrutto|number_de]] €</td>
					<td style="text-align: right;">[[vkMwSt-ekMwSt|number_de]] €</td>
					<td style="text-align: right;">[[vkNetto-ekNetto|number_de]] €</td>
				</tr>
			</table>
		</div>

		<div class="uebersicht-vk">
			<h2>Übersicht VK</h2>
			
			<table id="einkaeufe">
				<tr>
					<th>Id</th>
					<th>Plattform</th>
					<th>Account</th>
					<th>Name</th>
					<th>Brutto</th>
					<th>MwSt</th>
					<th>Netto</th>
					<th>Zahlweise</th>
					<th>Wertstellung</th>
				</tr>
				<tr ng-repeat="verkauf in verkaeufe" ng-click="editVerkauf(verkauf.id)">

					<td class="id">[[verkauf.id]]</td>

					<td class="plattform">[[verkauf.plattform]]</td>

					<td class="account">[[verkauf.account]]</td>

					<td class="name">[[verkauf.name]]</td>

					<td class="brutto">[[verkauf.brutto|number_de]] €</td>
					
					<td class="mwst">[[verkauf.mwst|number_de]] €</td>
					
					<td class="netto">[[verkauf.netto|number_de]] €</td>

					<td class="zahlweise">[[verkauf.zahlweise]]</td>

					<td class="wertstellung">[[verkauf.wertstellung | date:'dd.MM.yyyy']]</td>

				</tr>

				
				<tr class="sum" ng-show="verkaeufe.length > 0">

					<td class="id"></td>

					<td class="plattform"></td>

					<td class="account"></td>

					<td class="name" style="text-align: right;">Summe:</td>

					<td class="brutto">[[calcBruttoSum(verkaeufe)|number_de]] €</td>
					
					<td class="brutto">[[calcMwStSum(verkaeufe)|number_de]] €</td>
					
					<td class="brutto">[[calcNettoSum(verkaeufe)|number_de]] €</td>

					<td class="zahlweise"></td>

					<td class="wertstellung"></td>

				</tr>
				
			</table>
		</div>
		
		<br/>
		
		<div class="uebersicht-ek">
			<h2>Übersicht EK</h2>
			
			<table id="einkaeufe">
				<tr>
					<th>Id</th>
					<th>Plattform</th>
					<th>Account</th>
					<th>Name</th>
					<th>Brutto</th>
					<th>MwSt</th>
					<th>Netto</th>
					<th>Zahlweise</th>
					<th>Wertstellung</th>
				</tr>
				<tr ng-repeat="einkauf in einkaeufe" ng-click="editEinkauf(einkauf.id)">

					<td class="id">[[einkauf.id]]</td>

					<td class="plattform">[[einkauf.plattform]]</td>

					<td class="account">[[einkauf.account]]</td>

					<td class="name">[[einkauf.name]]</td>

					<td class="brutto">[[einkauf.brutto|number_de]] €</td>

					<td class="mwst">[[einkauf.mwst|number_de]] €</td>

					<td class="netto">[[einkauf.netto|number_de]] €</td>

					<td class="zahlweise">[[einkauf.zahlweise]]</td>

					<td class="wertstellung">[[einkauf.wertstellung | date:'dd.MM.yyyy']]</td>

				</tr>

				<tr class="sum" ng-show="einkaeufe.length > 0">

					<td class="id"></td>

					<td class="plattform"></td>

					<td class="account"></td>

					<td class="name" style="text-align: right;">Summe:</td>

					<td class="brutto">[[calcBruttoSum(einkaeufe)|number_de]] €</td>

					<td class="mwst">[[calcMwStSum(einkaeufe)|number_de]] €</td>

					<td class="netto">[[calcNettoSum(einkaeufe)|number_de]] €</td>

					<td class="zahlweise"></td>

					<td class="wertstellung"></td>

				</tr>
				
			</table>
		</div>
		
	</div>
	</form>
</div>



