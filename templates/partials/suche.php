<div id="suche">
	<h1>Suche</h1>

	<form ng-submit="search()">

	<div>
		<div style="clear: both;">

			<div style="float:left; text-align: left;" >
				<input type="text" ng-model="query" placeholder="Suchtext" ng-change="search()"/>
				<button type="submit">Suchen</button>
				<fieldset>
					<legend>Suchen in:</legend>
					<input id="table_ek" type="radio" value="einkauf" ng-model="table">
					<label for="table_ek">Einkauf</label>
					<input id="table_vk" type="radio" value="verkauf" ng-model="table">
					<label for="table_vk">Verkauf</label>
				</fieldset>
			</div>
			<div style="float:left; margin-left: 15px; border-left: 1px solid lightgrey; padding-left: 15px; height: 80px; text-align: left;">
				<fieldset>
					<legend>Spalte:</legend>
					<input id="column_position" type="checkbox" ng-model="column.position">
					<label for="column_position">Position</label>
					</br>
					<input id="column_account" type="checkbox" ng-model="column.account">
					<label for="column_account">Account</label>
					</br>
					<input id="column_name" type="checkbox" ng-model="column.name">
					<label for="column_name">Name</label>
				</fieldset>
			</div>
		</div>
	</div>

	</form>

	<div class="centered" style="clear:both;">

		<hr/>
		<div class="results">
			<div class="result">
				<table style="width:80%;">
					<tr>
						<th style="background-color: white; width:15px;"><!-- hide / show --></th>
						<th>Id</th>
						<th>Plattform</th><!-- Kanal für EK -->
						<th>Account</th>
						<th>Name</th>
						<th>Brutto</th>
						<th>MwSt</th>
						<th>Netto</th>
						<th>Zahlweise</th>
					</tr>
					<tr ng-repeat-start="result in results" ng-click="openResult(result)">

						<td style="background-color: white; width:15px;">
							<input type="checkbox" ng-model="result.show"><!-- hide / show --></td>
						<td class="id">{{result.id}}</td>
						<td class="plattform">{{result.plattform}}</td>
						<td class="account">{{result.account}}</td>
						<td class="name">{{result.name}}</td>
						<td class="brutto">{{result.brutto|number_de}} €</td>
						<td class="mwst">{{result.mwst|number_de}} €</td>
						<td class="netto">{{result.netto|number_de}} €</td>
						<td class="zahlweise">{{result.zahlweise}}</td>

					</tr>
					<tr ng-repeat-end ng-show="result.show || ( result.positionen | filter:query ).length"
						style="background-color:white;">
						<td style="background-color: white; width:15px;"></td>
						<td colspan="8" style="padding:0;">
							<table class="positionen" style="width:100%; margin-top: 1px; margin-bottom: 1px;">
								<tr>
									<th style="background-color: white; width:15px;"><!-- hide / show --></th>
									<th>Datum</th>
									<th>Stück</th>
									<th>Positionstyp</th>
									<th>Positionsbezeichnung</th>
									<th>Geliefert</th>
									<th>Brutto</th>
									<th>Brutto ∑</th>
									<th>MwSt</th>
									<th>Netto</th>
								</tr>
								<tr ng-repeat="position in result.positionen"
									ng-show="result.show || ( [position] | filter:query ).length">
									<td style="background-color: white; width:15px;"><!-- hide / show --></td>
									<td class="datum">{{ position.datum | date:'dd.MM.yyyy' }}</td>
									<td class="count">{{position.stueck}}</td>
									<td class="type">{{position.typ}}</td>
									<td class="description">{{position.bezeichnung}}</td>
									<td class="delivered">
										<input type="checkbox" ng-model="position.geliefert" disabled="disabled"/>
									</td>
									<td class="brutto">{{position.brutto|number_de}}</td>
									<td class="bruttoSum"><span>{{position.bruttoSum|number_de}} €</span></td>
									<td class="mwst"><span>{{position.mwst|number_de}} €</span></td>
									<td class="netto"><span>{{position.netto|number_de}} €</span></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>

	</div>
</div>



