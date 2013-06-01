<div id="wertstellungen">

	<!--
	zwei Listen (ver & einkäufe)
	 für alle ek/vk mit leerem wertstellungsdatum
	 Nr, Plattform, account, name, bruttoTotal, wertstellung (editierbar), button zum ek / vk
	 unter beiden liste: speichern für alle, click=alert+reload
	-->
	<h1 class="heading">Einkäufe ohne Wertstellungsdatum</h1>

	<form>
		<table id="einkaeufe">
			<tr>
				<th>Nr.</th>
				<th>Plattform</th>
				<th>Account</th>
				<th>Name</th>
				<th>Brutto</th>
				<th>Zahlweise</th>
				<th>Wertstellung</th>
				<th></th>
			</tr>
			<tr ng-repeat="einkauf in einkaeufe">

				<td class="nr">[[$index+1]]</td>

				<td class="plattform">[[einkauf.plattform]]</td>

				<td class="account">[[einkauf.account]]</td>

				<td class="name"><input type="text" ng-model="einkauf.name" /></td>

				<td class="brutto">[[einkauf.bruttoTotal|number_de]] €</td>

				<td class="zahlweise">[[einkauf.zahlweise]]</td>

				<td class="wertstellung">
					<input type="text" ng-model="einkauf.wertstellung"
						   value="[[ verkauf.wertstellung | date:'dd.MM.yyyy' ]]"
						   ui-date="{ dateFormat: 'dd.mm.yy' }" ui-date-format="yy-mm-dd" /><br/>
				</td>

				<td><a class="button" href="#/einkauf/[[einkauf.id]]">zum Einkauf</a></td>

			</tr>

		</table>
	</form>

	<h1 class="heading">Verkäufe ohne Wertstellungsdatum</h1>

	<form>
		<table id="verkaeufe">
			<tr>
				<th>Nr.</th>
				<th>Plattform</th>
				<th>Account</th>
				<th>Name</th>
				<th>Brutto</th>
				<th>Zahlweise</th>
				<th>Wertstellung</th>
				<th></th>
			</tr>
			<tr ng-repeat="verkauf in verkaeufe">

				<td class="nr">[[$index+1]]</td>

				<td class="plattform">[[verkauf.plattform]]</td>

				<td class="account">[[verkauf.account]]</td>

				<td class="name"><input type="text" ng-model="verkauf.name" /></td>

				<td class="brutto">[[verkauf.bruttoTotal|number_de]] €</td>

				<td class="zahlweise">[[verkauf.zahlweise]]</td>
				
				<td class="wertstellung">
					<input type="text" ng-model="verkauf.wertstellung"
						   value="[[ verkauf.wertstellung | date:'dd.MM.yyyy' ]]"
						   ui-date="{ dateFormat: 'dd.mm.yy' }" ui-date-format="yy-mm-dd" /><br/>
				</td>

				<td><a class="button" href="#/verkauf/[[verkauf.id]]">zum Verkauf</a></td>

			</tr>

		</table>
	</form>

	<button ng-click="save()">Speichern</button>
</div>



