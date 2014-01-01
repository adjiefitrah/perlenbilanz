<?php
\OCP\Util::addScript('perlenbilanz', 'vendor/angular-1.2.1/angular');
\OCP\Util::addScript('perlenbilanz', 'vendor/angular-1.2.1/angular-resource');
\OCP\Util::addScript('perlenbilanz', 'vendor/angular-1.2.1/angular-route');
\OCP\Util::addScript('perlenbilanz', 'vendor/ui-date-0.0.3/src/date');
\OCP\Util::addScript('perlenbilanz', 'vendor/ui-select2-0.0.4/src/select2');
\OCP\Util::addScript('perlenbilanz', 'vendor/select2-3.4.5/select2');
\OCP\Util::addScript('perlenbilanz', 'vendor/ui-autocomplete/autocomplete');
\OCP\Util::addScript('perlenbilanz', 'public/app');

\OCP\Util::addStyle('perlenbilanz', 'style');
\OCP\Util::addStyle('perlenbilanz', 'animation');
\OCP\Util::addStyle('perlenbilanz', 'vendor/select2');
?>
<div id="perlenbilanz" ng-app="Perlenbilanz">
	<div id="controls" ng-controller="MenuCtrl">
		<a class="button" href="#/">Übersicht</a>

		&nbsp;&nbsp; | &nbsp;&nbsp;

		<a class="button" href="#/einkauf">neuer EK</a>
		<a class="button" href="#/verkauf">neuer VK</a>
		
		&nbsp;&nbsp; | &nbsp;&nbsp;

		<a class="button" href="#/einkaeufe">EK schließen</a>
		<a class="button" href="#/verkaeufe">VK schließen</a>
		<a class="button" href="#/wertstellungen">Wertstellungen</a>
		
		&nbsp;&nbsp; | &nbsp;&nbsp;

		<a class="button" href="#/suche">Suche</a>
		
		&nbsp;&nbsp; | &nbsp;&nbsp;

		<a class="button" ng-click="generateReport()">Bericht</a>
		<!-- intialize date with previous month as in menucontroller.js -->
		<div style="display:inline-block;"
			 ui-date="{ defaultDate: '-1m', dateFormat: 'dd.mm.yy', onChangeMonthYear: updateReportDate }" ></div>

	</div>
	
	<div id="view" ng-view></div>
	

</div>



