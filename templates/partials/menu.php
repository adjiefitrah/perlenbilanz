<div id="menu">

	<form>
	<div class="centered">
	<h1 class="heading">Perlenbilanz</h1>

	<a class="button" href="#/einkauf">Einkauf erfassen</a></br></br>
	<a class="button" href="#/verkauf">Verkauf erfassen</a></br>

	<hr></br>

	<a class="button" href="#/einkaeufe">EK Positionen schließen</a></br></br>
	<a class="button" href="#/verkaeufe">Verkäufe schließen</a></br></br>
	<a class="button" href="#/wertstellungen">Wertstellungen pflegen</a></br>

	<hr></br>

	<a class="button" ng-click="generateReport()">Buchhaltungsbericht</a></br></br>

		<div style="display:inline-block;" ui-date="{ defaultDate: '-1m', onChangeMonthYear: updateReportDate }" ></div></br>
		<!-- monat und jahr -> kriterium für wertstellungsdatum dateFormat: 'mm.yy', ui-date-format="mm.yy"
		excel datei mit zwei listen
		 einkauf
		 	wertstellung, positionstyp (wenn alle positionen gleich, sonst '#gemischt' in rot), brutto, mwst, netto, name, zahlweise (paypal, bar, konto)
		 verkauf
		 	wertstellung, positionstyp (wenn alle positionen gleich, sonst '#gemischt' in rot), brutto, mwst, netto, name, zahlweise (paypal, bar, konto)
		-->

	<hr></br>

	<a class="button" href="">DB öffnen</a></br>

	<hr></br>

	<textarea class="notes" ng-model="notes" ng-change="saveNotes()"></textarea>

	</div>
	</form>
</div>



