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
					<td>[[vkBrutto|number_de]] €</td>
					<td>[[vkMwSt|number_de]] €</td>
					<td>[[vkNetto|number_de]] €</td>
				</tr>
				<tr>
					<td>Einkäufe</td>
					<td>[[ekBrutto|number_de]] €</td>
					<td>[[ekMwSt|number_de]] €</td>
					<td>[[ekNetto|number_de]] €</td>
				</tr>
				<tr class="sum">
					<td>Summe</td>
					<td>[[vkBrutto-ekBrutto|number_de]] €</td>
					<td>[[vkMwSt-ekMwSt|number_de]] €</td>
					<td>[[vkNetto-ekNetto|number_de]] €</td>
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
					<th>Zahlweise</th>
					<th>Wertstellung</th>
				</tr>
				<tr ng-repeat="verkauf in verkaeufe" ng-click="editVerkauf(verkauf.id)">

					<td class="id">[[verkauf.id]]</td>

					<td class="plattform">[[verkauf.plattform]]</td>

					<td class="account">[[verkauf.account]]</td>

					<td class="name">[[verkauf.name]]</td>

					<td class="brutto">[[verkauf.brutto|number_de]] €</td>

					<td class="zahlweise">[[verkauf.zahlweise]]</td>

					<td class="wertstellung">[[verkauf.wertstellung | date:'dd.MM.yyyy']]</td>

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
					<th>Zahlweise</th>
					<th>Wertstellung</th>
				</tr>
				<tr ng-repeat="einkauf in einkaeufe" ng-click="editEinkauf(einkauf.id)">

					<td class="id">[[einkauf.id]]</td>

					<td class="plattform">[[einkauf.plattform]]</td>

					<td class="account">[[einkauf.account]]</td>

					<td class="name">[[einkauf.name]]</td>

					<td class="brutto">[[einkauf.brutto|number_de]] €</td>

					<td class="zahlweise">[[einkauf.zahlweise]]</td>

					<td class="wertstellung">[[einkauf.wertstellung | date:'dd.MM.yyyy']]</td>

				</tr>

			</table>
		</div>
		
	</div>
	</form>
</div>



