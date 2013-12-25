/**
 * ownCloud - Perlenbilanz app
 *
 * @author Jörn Friedrich Dreyer
 * @copyright 2013 Jörn Friedrich Dreyer <jfd@butonic.de>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// fix SVGs in IE8
if(!SVGSupport()) {
	var replaceSVGs = function() {
		replaceSVG();
		// call them periodically to keep track of possible changes in the artist view
		setTimeout(replaceSVG, 10000);
	};
	replaceSVG();
	setTimeout(replaceSVG, 1000);
	setTimeout(replaceSVGs, 5000);
}

angular.module('Perlenbilanz', [
		'ngRoute',
		'ngResource',
		'ui.date',
		'ui.select2',
		'ui.autocomplete'
	]).
	config(['$interpolateProvider', '$routeProvider',
		function ($interpolateProvider, $routeProvider) {
			/*
		$interpolateProvider.startSymbol('[[');
		$interpolateProvider.endSymbol(']]');
*/

		$routeProvider.
			when('/', {templateUrl: 'templates/partials/uebersicht.php', controller: 'UebersichtCtrl'}).
			when('/einkauf', {templateUrl: 'templates/partials/einkauf.php', controller: 'EinkaufCtrl'}).
			when('/einkauf/:id', {templateUrl: 'templates/partials/einkauf.php', controller: 'EinkaufCtrl'}).
			when('/verkauf', {templateUrl: 'templates/partials/verkauf.php', controller: 'VerkaufCtrl'}).
			when('/verkauf/:id', {templateUrl: 'templates/partials/verkauf.php', controller: 'VerkaufCtrl'}).
			when('/einkaeufe', {templateUrl: 'templates/partials/offeneeinkaeufe.php', controller: 'EinkaufPositionCtrl'}).
			when('/verkaeufe', {templateUrl: 'templates/partials/offeneverkaeufe.php', controller: 'VerkaeufeCtrl'}).
			when('/wertstellungen', {templateUrl: 'templates/partials/wertstellungen.php', controller: 'WertstellungenCtrl'}).
			when('/suche', {templateUrl: 'templates/partials/suche.php', controller: 'SucheCtrl'}).
			otherwise({redirectTo: '/'});
	}]);
