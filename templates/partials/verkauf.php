<div id="verkauf">

	<h1 class="heading">Verkauf erfassen</h1>

	<form>
		<div>
			<div style="float:left;">
				<fieldset>
					<legend>Kanal:</legend>
					<input type="radio" id="plattform_fancywork" value="fancywork" ng-model="verkauf.plattform">
					<label for="plattform_fancywork">fancywork</label>
					</br>
					<input type="radio" id="plattform_hobbyquelle" value="HobbyQuelle" ng-model="verkauf.plattform">
					<label for="plattform_hobbyquelle">HobbyQuelle</label>
					</br>
					<input type="radio" id="plattform_sonstige" value="Sonstige" ng-model="verkauf.plattform">
					<label for="plattform_sonstige">Sonstige</label>
				</fieldset>
			</div>
			<div style="float:left; margin-left: 15px; border-left: 1px solid lightgrey; padding-left: 15px; height: 135px;" class="col2">
				<fieldset>
					<legend>Käufer:</legend>
					<div style="margin-left: 5px;">
						<label for="account">Bestnr:</label>
						<input type="text" class="plattform_bestellnummer"
							   ng-model="verkauf.bestellnummer">
					</div>
					<div style="margin-left: 5px;">
						<label style="vertical-align: bottom; margin-bottom: 7px;" for="account">Account:</label>
						<input id="account" type="hidden" ui-select2="accountOptions"
							   ng-model="verkauf.account" ng-change="guessNames()">
					</div>
					<div style="margin-left: 5px;">
						<label style="vertical-align: bottom; margin-bottom: 7px;" for="name">Name:</label>
						<input id="name" type="hidden" ui-select2="nameOptions"
							   ng-model="verkauf.name" ng-change="guessAccounts()">
					</div>
				</fieldset>
			</div>
			<div style="float:left; margin-left: 15px;">
				<label for="rechnungsanschrift" style="font-variant: small-caps; text-decoration: underline;">Rechnungsanschrift:</label><br/>
				<textarea id="rechnungsanschrift" ng-model="verkauf.rechnungsanschrift" style="width:170px; height: 67px;"/><br/>

				<input id="differentaddress" type="checkbox" ng-model="differentaddress"
					   ng-checked="verkauf.lieferanschrift|notnull"
					   ng-change="toggleLieferanschrift()" style="margin-left: 5px;"/>
				<label for="differentaddress">Abweichende Lieferanschrift</label>
			</div>
			<div ng-show="differentaddress||verkauf.lieferanschrift|notnull" style="float:left; margin-left: 15px;">
				<label for="lieferanschrift" style="font-variant: small-caps; text-decoration: underline;">Lieferanschrift:</label><br/>
				<textarea id="lieferanschrift" ng-model="verkauf.lieferanschrift" style="width:170px; height: 67px;"/><br/>
			</div>
			<div style="float:left; margin-left: 15px; border-left: 1px solid lightgrey; padding-left: 15px; height: 135px;">
				<fieldset>
					<legend>Verpackungsmaterial:</legend>
					<input id="vpm_luftpolstertasche" type="checkbox" ng-model="verkauf.luftpolstertasche" style="margin-left: 5px;">
					<label for="vpm_luftpolstertasche">Luftpolstertasche</label>
					</br>
					<input id="vpm_briefumschlag" type="checkbox" ng-model="verkauf.briefumschlag" style="margin-left: 5px;">
					<label for="vpm_briefumschlag">Briefumschlag</label>
					</br>
					<input id="vpm_druckverschlussbeutel" type="checkbox" ng-model="verkauf.druckverschlussbeutel" style="margin-left: 5px;">
					<label for="vpm_druckverschlussbeutel">Druckverschlussbeutel</label>
					</br>
					<input id="vpm_knallfolie" type="checkbox" ng-model="verkauf.knallfolie" style="margin-left: 5px;">
					<label for="vpm_knallfolie">Knallfolie</label>
					</br>
					<input id="vpm_unverpackt" type="checkbox" ng-model="verkauf.unverpackt" style="margin-left: 5px;">
					<label for="vpm_unverpackt">Unverpackt</label>
				</fieldset>
			</div>
			<div style="float:left; margin-left: 15px; border-left: 1px solid lightgrey; padding-left: 15px; height: 135px;">
				<fieldset>
					<legend>Zahlweise:</legend>
					<input id="zahlweise_konto" type="radio" value="Konto" ng-model="verkauf.zahlweise">
					<label for="zahlweise_konto">Konto</label>
					</br>
					<input id="zahlweise_paypal" type="radio" value="PayPal" ng-model="verkauf.zahlweise">
					<label for="zahlweise_paypal">PayPal</label>
					</br>
					<input id="zahlweise_bar" type="radio" value="Bar" ng-model="verkauf.zahlweise">
					<label for="zahlweise_bar">Bar</label>
				</fieldset>
			</div>
			<div style="float:left; margin-left: 15px;">
				<label for="wertstellung" style="font-variant: small-caps; text-decoration: underline;">Wertstellung:</label><br/>
				<input type="text" id="wertstellung" ng-model="verkauf.wertstellung"
					   value="[[ verkauf.wertstellung | date:'dd.MM.yyyy' ]]"
					   ui-date="{ dateFormat: 'dd.mm.yy' }" ui-date-format="yy-mm-dd"
					   style="margin-bottom: 23px;"><br/>
				<label for="rechnungsnummer" style="font-variant: small-caps; text-decoration: underline;">Rechnungsnummer:</label><br/>
				<!-- input type="text" id="rechnungsnummer" ng-model="verkauf.rechnungsnummer" -->
				<!--
	workflow optimalfall
		angezeigt wierden die freien rechnungsnummern (document_stroke?)
		1. Rechnung generieren					-> id generieren, html generieren, pdf generieren
		2. Rechnung generieren & herunterladen	-> id generieren, html generieren, pdf generieren, download
	
	workflow rechnung bereits generiert
		angezeigt wird document_fill
		1. anklicken		-> herunterladen
		2. bearbeiten icon	-> editor -> speichern & neu generieren
		3. fehlerhaft icon	-> störer eingeben, nummer nicht freigeben aber fehlerhaft icon anzeigen
		4. löschen icon		-> nummer freigeben (und Rechnung löschen? -> in Papierkorb gelöscht verchieben)
	
	workflow rechnung als fehlerhaft markiert
		angezeigt wird document_denied
		1. anklicken		-> herunterladen
		2. bearbeiten icon	-> editor -> speichern & neu generieren
	
	
	fehlerhaft markierte rechnungen müssen überall noch auftauchen (weil die rechnungsnummer vergeben wurde)
	aber in den summen darf nicht mehr berücksichtig werden ... hm das ist doch quatsch?
	sie sind durch den störer zu erkennen?
				-->
				<div>
					<span ng-hide="verkauf.rechnungsnummer">
						<input type="text" id="rechnungsjahr" integer ng-model="verkauf.rechnungsjahr"
							   on-cursor-up="verkauf.rechnungsjahr=verkauf.rechnungsjahr+1"
							   on-cursor-down="verkauf.rechnungsjahr=verkauf.rechnungsjahr-1">
						frei: <button ng-repeat="nextInvoiceID in nextInvoiceIDs" ng-click="generateInvoice(nextInvoiceID)">[[nextInvoiceID]]</button>
					</span>
					<span ng-show="verkauf.rechnungsnummer" style="margin-left: 5px;">
						<!-- button für jede freie id zeigen
							- download per xhr geht nicht
							- also bei klick auf id generieren
								- document_stoke anzeigen (= rechnungsnummer gespeichert )
								- bei klick: neu generieren (xhr)
							- checken ob datei existiert
								- wenn ok document_fill anzeigen
								- bei klick: download? (target = blank?)
						-->
						<!--
						<a class="icon generate" ng-click="generateInvoice(verkauf)" title="generieren"></a>
						-->
						[[verkauf.rechnungsjahr]]-[[verkauf.rechnungsnummer|prependZero]]:
						<a class="icon download"   ng-hide="verkauf.faultyreason|notnull" ng-click="downloadInvoice()" title="herunterladen"></a>
						<a class="icon faulty"     ng-show="verkauf.faultyreason|notnull" ng-click="downloadInvoice()" title="herunterladen"></a>
						<!--<a class="icon preview"    ng-click="previewInvoice()"  title="vorschau"></a> same as edit? -->
						<a class="icon edit"       ng-click="editInvoice()"     title="bearbeiten"></a>
						<a class="icon markfaulty" ng-hide="verkauf.faultyreason|notnull" ng-click="markFaulty()"      title="fehlerhaft"></a>
						<a class="icon delete"    ng-click="deleteInvoice()"  title="löschen"></a>
						<!--
						<a class="icon archive"    ng-click="archiveInvoice()"  title="archivieren"></a>
						-->
						<!-- kopiert die rechnung als Rechnung_XXX_<timestamp>.html/pdf mit manuell angegbenem störer -->
					</span>
				</div>
				<div ng-show="verkauf.faultyreason|notnull">
					<label for="faultyreason">Störer:</label><br/>
					<input type="text" id="faultyreason" emtpy-to-null ng-model="verkauf.faultyreason">
				</div>
			</div>
			<div style="float: left; margin-left: 15px; min-width: 120px;">
				<div style="margin-bottom: 9px; font-variant: small-caps; text-decoration: underline;">Brutto:</div>
				<div style="margin-left: 5px; font-size: 40px; height:30px;">[[bruttoTotal|number_de]] €</div>
				
				<div style="margin-left: 5px;" ng-repeat="mwst in mwstGroups">MwSt ([[mwst.mwstProzent]]%):<span style="float:right;">[[mwst.mwst|number_de]] €</span></div>
				<div style="margin-left: 5px;">Netto: <span style="float:right;">[[nettoTotal|number_de]] €</span></div>
			</div>
			<div style="clear: both; border-bottom: 1px solid #ddd; width: 100%; height:6px;"></div>
		</div>

		<br/>
		
		<table id="positionen">
			<tr>
				<th>Datum</th>
				<th>Stück</th>
				<th>Positionstyp</th>
				<th>Positionsbezeichnung</th>
				<th>Geliefert</th>
				<th>Brutto</th>
				<th>Brutto ∑</th>
				<!--th>MwSt%</th-->
				<th>MwSt</th>
				<th>Netto</th>
				<th><!-- action --></th>
			</tr>
			<tr ng-repeat="position in positionen" ng-hide="position.deleted">

				<td class="datum">
					<input type="text" ng-model="position.datum" required
						 value="[[ position.datum | date:'dd.MM.yyyy' ]]"
						 ui-date="{ dateFormat: 'dd.mm.yy' }"
						 ui-date-format="yy-mm-dd">
				</td>

				<td class="count"><input type="text" integer ng-model="position.stueck" size="4"
										 on-cursor-up="position.stueck=position.stueck+1"
										 on-cursor-down="position.stueck=position.stueck-1"
										 ng-change="updateBrutto(position)"/></td>

				<td class="type">
					<select ui-select2 ng-model="position.typ" data-placeholder="-- Typ --" style="width:110px;">
						<option></option>
						<option ng-repeat="type in types" value="[[type.id]]">[[type.text]]</option>
					</select>
				</td>

				<td class="description"><textarea ng-model="position.bezeichnung"></textarea></td>

				<td class="delivered">
					<input type="checkbox" ng-model="position.geliefert" />
				</td>

				<td class="brutto"><input type="text" currency-input ng-model="position.brutto" ng-change="updateBrutto(position)"/></td>
				<!--
				<td class="mwst_prozent"><span>[[position.mwst_prozent]] %</span></td>
				-->
				<td class="bruttoSum"><span>[[position.bruttoSum|number_de]] €</span></td>
				
				<td class="mwst"><span>[[position.mwst|number_de]] €</span></td>

				<td class="netto"><span>[[position.netto|number_de]] €</span></td>

				<td><button ng-click="addPosition($index)">+</button> <button ng-click="removePosition($index)">-</button></td>

			</tr>

		</table>

		<button style="margin-top: 18px;" ng-click="saveAndNew()">Speichern</button>
	</form>
</div>



