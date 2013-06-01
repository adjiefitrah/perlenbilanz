<div id="einkauf">

	<h1 class="heading">Einkauf erfassen</h1>

	<form>
		<h2>Rahmendaten:</h2>
		<div>
			<div style="float:left; width:440px;">
				<div style="float:right; width:320px; text-align: left; margin-top: 6px;">
					<label for="account">Account:</label>
					<input id="account" type="hidden" ui-select2="accountOptions" ng-model="einkauf.account" ng-change="guessNames()"><br/>

					<label for="name">Name:</label>
					<input id="name" type="hidden" ui-select2="nameOptions" ng-model="einkauf.name" ng-change="guessAccounts()">
				</div>
				<div style="float:left;margin-top: 5px;">
					<input id="plattform_ebay" type="radio" value="eBay" ng-model="einkauf.plattform">
					<label for="plattform_ebay">eBay</label>
					</br>
					<input id="plattform_dawanda" type="radio" value="DaWanda" ng-model="einkauf.plattform">
					<label for="plattform_dawanda">DaWanda</label>
					</br>
					<input id="plattform_amazon" type="radio" value="Amazon" ng-model="einkauf.plattform">
					<label for="plattform_amazon">Amazon</label>
					</br>
					<input id="plattform_sonstige" type="radio" value="Sonstige" ng-model="einkauf.plattform">
					<label for="plattform_sonstige">Sonstige</label>
				</div>
			</div>
			<div style="float:left; margin-left: 15px;">
				<div style="margin-top: 5px;">
					<input id="zahlweise_paypal" type="radio" value="PayPal" ng-model="einkauf.zahlweise">
					<label for="zahlweise_paypal">PayPal</label>
					</br>
					<input id="zahlweise_konto" type="radio" value="Konto" ng-model="einkauf.zahlweise">
					<label for="zahlweise_konto">Konto</label>
					</br>
					<input id="zahlweise_bar" type="radio" value="Bar" ng-model="einkauf.zahlweise">
					<label for="zahlweise_bar">Bar</label>
				</div>
			</div>
			<div style="float:left; margin-left: 25px; min-width:60px;">
				<div style="margin-top:6px;margin-bottom:9px;">Brutto:<br/><span style="font-size: 40px; margin-top: 10px; display: block;">[[bruttoTotal|number_de]] €</span></div>
				<div ng-repeat="mwst in mwstGroups">MwSt ([[mwst.mwstProzent]]%):<span style="float:right;">[[mwst.mwst|number_de]] €</span></div>
				<div> Netto: <span style="float:right;">[[nettoTotal|number_de]] €</span></div>
			</div>
			<div style="float:left; width:180px; text-align: left; margin-top: 6px; margin-left: 20px;">
				<label for="wertstellung">Wertstellung:</label><br/>
				<input id="wertstellung" ng-model="einkauf.wertstellung"
					   type="text" value="[[ einkauf.wertstellung | date:'dd.MM.yyyy' ]]"
					   ui-date="{ dateFormat: 'dd.mm.yy' }" ui-date-format="yy-mm-dd">
			</div>
			<div style="clear: both; border-bottom: 1px solid #ddd; width: 100%; height:6px;"></div>
		</div>

		<h2>Positionen:<button ng-click="addPosition(-1)">+</button></h2>

		<table id="positionen">
			<tr>
				<th>Einkaufsdatum</th>
				<th>Positionstyp</th>
				<th>Positionsbezeichnung</th>
				<th>Geliefert</th>
				<th>Brutto</th>
				<th>MwSt %</th>
				<th>MwSt</th>
				<th>Netto</th>
				<th><!-- action --></th>
			</tr>
			<tr ng-repeat="position in positionen">

				<td class="datum">
					<input type="text" ng-model="position.datum" required
						 value="[[ position.datum | date:'dd.MM.yyyy' ]]"
						 ui-date="{ dateFormat: 'dd.mm.yy' }"
						   ui-date-format="yy-mm-dd">
				</td>

				<td class="type">
					<select ui-select2 ng-model="position.typ" data-placeholder="-- Typ --" style="width:170px;">
						<option></option>
						<option ng-repeat="type in types" value="[[type.id]]">[[type.text]]</option>
					</select>
				</td>

				<td class="description"><textarea ng-model="position.bezeichnung"></textarea></td>

				<td class="delivered">
					<label><input ng-value="true" type="radio" ng-model="position.geliefert"/>ja</label><br/>
					<label><input ng-value="false" type="radio" ng-model="position.geliefert"/>nein</label>
				</td>

				<td class="brutto"><input type="text" currency-input ng-model="position.brutto" ng-change="updateBrutto(position)"/></td>

				<td class="mwst_prozent">
					<div class="mwst_status">
						<label><input ng-value="true" type="radio" ng-model="position.mwstStatus" ng-change="updateMwSt(position)"/>ja</label><br/>
						<label><input ng-value="false" type="radio" ng-model="position.mwstStatus" ng-change="updateMwSt(position)"/>nein</label><br/>
						<label><input ng-value="null" type="radio" ng-model="position.mwstStatus" ng-change="updateMwSt(position)"/>offen</label>
					</div>
					<input type="text" ng-show="position.mwstStatus" integer ng-model="position.mwstProzent" ng-change="updateMwStProzent(position)"/>
				</td>
				
				<td class="mwst"><span ng-hide="position.mwstProzent|empty">[[position.mwst|number_de]] €</span><input type="text" ng-show="position.mwstProzent|empty" currency-input ng-model="position.mwst"/></td>

				<td class="netto"><span>[[position.netto|number_de]] €</span></td>

				<td><button ng-click="addPosition($index)">+</button> <button ng-click="removePosition($index)">-</button></td>

			</tr>

		</table>

		<!-- FIXME: save mit popup bestätigen -->
		<button ng-click="saveEinkauf(einkauf)">Speichern</button>
	</form>
</div>



