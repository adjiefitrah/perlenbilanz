{{ script('vendor/angular-1.2.1/angular') }}
{{ script('vendor/angular-1.2.1/angular-resource') }}
{{ script('vendor/angular-1.2.1/angular-route') }}
{{ script('public/app', 'appframework') }}
{{ script('vendor/angular-ui/angular-ui') }}
{{ script('vendor/select2/select2') }}
{{ script('public/app') }}
{{ style('style') }}
{{ style('animation') }}
{{ style('vendor/select2') }}

<div id="perlenbilanz"
	 ng-app="perlenbilanz">
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
		<div style="display:inline-block;" ui-date="{ defaultDate: '-1m', onChangeMonthYear: updateReportDate }" ></div>

	</div>
	
	<div id="view" ng-view></div>
	

</div>



