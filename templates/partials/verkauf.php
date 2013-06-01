<div id="verkauf">

	<h1 class="heading">Verkauf erfassen</h1>

	<form>
		<h2>Rahmendaten:</h2>
		<div>
			<div style="float:left; width:460px;">
				<div style="float:right; width:320px; text-align: left; margin-top: 6px;">
					<label for="account">Account:</label>
					<input id="account" type="hidden" ui-select2="accountOptions" ng-model="verkauf.account" ng-change="guessNames()"><br/>

					<label for="name">Name:</label>
					<input id="name" type="hidden" ui-select2="nameOptions" ng-model="verkauf.name" ng-change="guessAccounts()">
				</div>
				<div style="float:left;margin-top: 5px;">
					<input type="radio" id="plattform_fancywork" value="fancywork" ng-model="verkauf.plattform">
					<label for="plattform_fancywork">fancywork</label>
					</br>
					<input type="radio" id="plattform_hobbyquelle" value="HobbyQuelle" ng-model="verkauf.plattform">
					<label for="plattform_hobbyquelle">HobbyQuelle</label>
					</br>
					<input type="radio" id="plattform_sonstige" value="Sonstige" ng-model="verkauf.plattform">
					<label for="plattform_sonstige">Sonstige</label>
				</div>
			</div>
			<div style="float:left; margin-left: 15px;">
				<div style="margin-top: 5px;">
					<input id="zahlweise_konto" type="radio" value="Konto" ng-model="verkauf.zahlweise">
					<label for="zahlweise_konto">Konto</label>
					</br>
					<input id="zahlweise_paypal" type="radio" value="PayPal" ng-model="verkauf.zahlweise">
					<label for="zahlweise_paypal">PayPal</label>
					</br>
					<input id="zahlweise_bar" type="radio" value="Bar" ng-model="verkauf.zahlweise">
					<label for="zahlweise_bar">Bar</label>
				</div>
			</div>
			<div style="float:left; margin-left: 25px; min-width:60px;">
				<div style="margin-top:6px;margin-bottom:9px;">Brutto:<br/><span style="font-size: 40px; margin-top: 10px; display: block;">[[bruttoTotal|number_de]] €</span></div>
				<div ng-repeat="mwst in mwstGroups">MwSt ([[mwst.mwstProzent]]%):<span style="float:right;">[[mwst.mwst|number_de]] €</span></div>
				<div> Netto: <span style="float:right;">[[nettoTotal|number_de]] €</span></div>
			</div>
			<div style="float:left; margin-left: 25px;">
				<div style="margin-top: 5px;">
					<fieldset>
						<legend>Verpackungsmaterial</legend>
						<input id="vpm_luftpolstertasche" type="checkbox" ng-model="verkauf.luftpolstertasche">
						<label for="vpm_luftpolstertasche">Luftpolstertasche</label>
						</br>
						<input id="vpm_briefumschlag" type="checkbox" ng-model="verkauf.briefumschlag">
						<label for="vpm_briefumschlag">Briefumschlag</label>
						</br>
						<input id="vpm_druckverschlussbeutel" type="checkbox" ng-model="verkauf.druckverschlussbeutel">
						<label for="vpm_druckverschlussbeutel">Druckverschlussbeutel</label>
						</br>
						<input id="vpm_knallfolie" type="checkbox" ng-model="verkauf.knallfolie">
						<label for="vpm_knallfolie">Knallfolie</label>
						</br>
						<input id="vpm_unverpackt" type="checkbox" ng-model="verkauf.unverpackt">
						<label for="vpm_unverpackt">Unverpackt</label>
					</fieldset>
				</div>
			</div>
			<div style="float:left; width:270px; text-align: left; margin-top: 6px; margin-left: 20px;">
				<label for="wertstellung">Wertstellung:</label><br/>
				<input type="text" id="wertstellung" ng-model="verkauf.wertstellung"
					   value="[[ verkauf.wertstellung | date:'dd.MM.yyyy' ]]"
					   ui-date="{ dateFormat: 'dd.mm.yy' }" ui-date-format="yy-mm-dd"><br/>
				<label for="rechnungsnummer">Rechnungsnummer:</label><br/>
				<input type="text" id="rechnungsnummer" ng-model="verkauf.rechnungsnummer">
			</div>
			<div style="clear: both; border-bottom: 1px solid #ddd; width: 100%; height:6px;"></div>
		</div>

		<h2>Positionen:<button ng-click="addPosition(-1)">+</button></h2>

		<table id="positionen">
			<tr>
				<th>Datum</th>
				<th>Stück</th>
				<th>Positionstyp</th>
				<th>Positionsbezeichnung</th>
				<th>Geliefert</th>
				<th>Brutto</th>
				<!--th>MwSt%</th-->
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

				<td class="count"><input type="text" integer ng-model="position.stueck" size="4"/></td>

				<td class="type">
					<select ui-select2 ng-model="position.typ" data-placeholder="-- Typ --" style="width:110px;">
						<option></option>
						<option ng-repeat="type in types" value="[[type.id]]">[[type.text]]</option>
					</select>
				</td>

				<td class="description"><textarea ng-model="position.bezeichnung"></textarea></td>

				<td class="delivered">
					<label><input ng-value="false" type="radio" ng-model="position.geliefert"/>nein</label><br/>
					<label><input ng-value="true" type="radio" ng-model="position.geliefert"/>ja</label>
				</td>

				<td class="brutto"><input type="text" currency-input ng-model="position.brutto" ng-change="updateBrutto(position)"/></td>
				<!--
				<td class="mwst_prozent"><span>[[position.mwst_prozent]] %</span></td>
				-->
				<td class="mwst"><span>[[position.mwst|number_de]] €</span></td>

				<td class="netto"><span>[[position.netto|number_de]] €</span></td>

				<td><button ng-click="addPosition($index)">+</button> <button ng-click="removePosition($index)">-</button></td>

			</tr>

		</table>

		<button ng-click="saveVerkauf(verkauf)">Speichern</button>
	</form>
</div>



