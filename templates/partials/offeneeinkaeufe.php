<div id="einkaeufe">

	<h1 class="heading">Offene Einkaufspositionen</h1>

	<form>

		<h2>Positionen:</h2>

		<table id="positionen">
			<tr>
				<th>Nr.</th>
				<th>Datum</th>
				<th>Plattform</th>
				<th>Positionstyp</th>
				<th>Positionsbezeichnung</th>
				<th>Geliefert</th>
				<th>Brutto</th>
				<th>MwSt?</th>
				<th>MwSt</th>
				<th>Netto</th>
				<th></th>
			</tr>
			<tr ng-repeat="position in positionen">

				<td class="nr">{{$index+1}}</td>

				<td class="datum">{{position.datum}}</td>

				<td class="plattform">{{position.plattform}}</td>

				<td class="type">{{position.typ}}</td>

				<td class="description">{{position.bezeichnung}}</td>

				<td class="delivered">
					<label><input ng-value="true" type="radio" ng-model="position.geliefert"/>ja</label><br/>
					<label><input ng-value="false" type="radio" ng-model="position.geliefert"/>nein</label>
				</td>

				<td class="brutto"><input type="text" currency-input ng-model="position.brutto" ng-change="updateMwSt(position)"/></td>

				<td class="mwst_prozent">
					<div class="mwst_status" ng-hide="position.mwstProzent">
						<label><input ng-value="true" type="radio" ng-model="position.mwstStatus" ng-change="updateMwSt(position)"/>ja</label><br/>
						<label><input ng-value="false" type="radio" ng-model="position.mwstStatus" ng-change="updateMwSt(position)"/>nein</label><br/>
						<label><input ng-value="null" type="radio" ng-model="position.mwstStatus" ng-change="updateMwSt(position)"/>offen</label>
					</div>
					<input type="text" ng-show="position.mwstProzent|notnull" currency-input ng-model="position.mwstProzent" ng-change="updateMwSt(position)"/>
				</td>

				<td class="mwst"><span ng-show="position.mwstProzent|notnull">{{position.mwst|number_de}} €</span><input type="text" ng-hide="position.mwstProzent|notnull" currency-input ng-model="position.mwst"/></td>

				<td class="netto"><span ng-show="position.mwstProzent|notnull">{{position.netto|number_de}} €</span><input type="text" ng-hide="position.mwstProzent|notnull" currency-input ng-model="position.netto"/></td>

				<td><a class="button" href="#/einkauf/{{position.ekId}}">zum Einkauf</a></td>

			</tr>

		</table>
		<button ng-click="savePositionen()">Speichern</button>
	</form>
</div>



