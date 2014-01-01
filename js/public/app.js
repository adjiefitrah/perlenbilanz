
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

angular.module('Perlenbilanz').controller('EinkaufCtrl',
	['$scope', '$location', '$filter', '$compile', '$routeParams', 'EinkaufResource',
		'EinkaufPositionResource', 'accountNameRecommender', 'mwstCalculator',
	function ($scope, $location, $filter, $compile, $routeParams, EinkaufResource,
		EinkaufPositionResource, accountNameRecommender, mwstCalculator) {

	$scope.types = [
		{id:"Ware",text:"Ware"},
		{id:"Versand",text:"Versand"},
		{id:"Porto/Versandmaterial",text:"Porto/Versandmaterial"},
		{id:"Büromaterial",text:"Büromaterial"},
		{id:"Gebühren",text:"Gebühren"},
		{id:"Buchhaltung",text:"Buchhaltung"},
		{id:"Sonstige",text:"Sonstige"}
	];
	$scope.now = new Date();
	$scope.accountOptions = {
		options: {
			html: false,
			focusOpen: true,
			onlySelect: false,
			source: function (request, response) {
				var data = [];

				angular.forEach($scope.guessedAccounts, function(s) {
					if (typeof s === 'string') {
						data.push(s);
					}
				});
				
				if (data.length === 0) {
					angular.forEach($scope.accounts, function(s) {
						if (typeof s === 'string') {
							data.push(s);
						}
					});
				}	

				data = $scope.accountOptions.methods.filter(data, request.term);

				response(data);
			}
		},
		methods: {},
		events : {
			change: function( event, ui ) {
				$scope.guessNames();
			}	
		}
	};
	$scope.nameOptions = {
		options: {
			html: false,
			focusOpen: true,
			onlySelect: false,
			source: function (request, response) {
				var data = [];

				angular.forEach($scope.guessedNames, function(s) {
					if (typeof s === 'string') {
						data.push(s);
					}
				});
				
				if (data.length === 0) {
					angular.forEach($scope.names, function(s) {
						if (typeof s === 'string') {
							data.push(s);
						}
					});
				}	

				data = $scope.nameOptions.methods.filter(data, request.term);

				response(data);
			}
		},
		methods: {},
		events : {
			change: function( event, ui ) {
				$scope.guessAccounts();
			}	
		}
	};
	$scope.guessAccounts = function() {
		accountNameRecommender.guessAccounts($scope, $scope.einkauf, EinkaufResource);
	};
	$scope.guessNames = function() {
		accountNameRecommender.guessNames($scope, $scope.einkauf, EinkaufResource);
	};
	$scope.newEinkauf = function () {
		$scope.guessedAccounts = null;
		$scope.guessedNames = null;
		$scope.accounts = EinkaufResource.listAccounts();
		$scope.names = EinkaufResource.listNames();
		//accountNameRecommender.fetchNames($scope, EinkaufResource);
		$scope.einkauf = new EinkaufResource();
		$scope.einkauf.plattform = 'eBay';
		$scope.einkauf.zahlweise = 'PayPal';
		$scope.positionen = [];
		$scope.addPosition(0);
	};
	$scope.saveEinkauf = function () {

		$scope.einkauf.$save(function (data) {
			//update the model with the entity returned from the server
			$scope.einkauf = new EinkaufResource(data);

			//save positions with id from einkauf
			angular.forEach($scope.positionen, function(position) {
				//update id
				position.ekId = $scope.einkauf.id;

				//FIXME wie position löschen?
				// FIXME Reihenfolge der positionen hängt von eingang auf der serverseite ab
				new EinkaufPositionResource(position).$save(function (data) {
					//update the model with the entity returned from the server
					position = new EinkaufPositionResource(data);
				});
			});
			alert('Gespeichert');
			$location.path('/einkauf');
			$scope.newEinkauf();
		});
	};
	$scope.addPosition = function (index) {
		$scope.positionen.splice(index+1, 0, {
			datum: $filter('date')(new Date(),'yyyy-MM-dd'),
			typ:'Ware',
			bezeichnung:'',
			geliefert:false,
			mwstProzent:0
		});
	};
	$scope.removePosition = function (index) {
		$scope.positionen.splice(index, 1);
		//FIXME wie löschen?
	};
	$scope.$watch('positionen', function(current, previous) {
		mwstCalculator.update($scope, current);
	}, true);
	$scope.updateBrutto = function (position) {
		mwstCalculator.updateBrutto(position);
	};
	$scope.updateMwSt = function (position) {
		mwstCalculator.updateMwSt(position);
	};
	$scope.updateMwStProzent = function (position) {
		mwstCalculator.updateMwStProzent(position);
	};

	if ($routeParams.id) {
		//$scope.accounts = EinkaufResource.listAccounts();
		accountNameRecommender.fetchNames($scope, EinkaufResource);
		$scope.einkauf = EinkaufResource.get({id:$routeParams.id}, function(data){
			$scope.guessNames();
			//$scope.guessAccounts();
		});
		$scope.positionen = EinkaufPositionResource.query({ekId:$routeParams.id});
	} else {
		$scope.newEinkauf();
	}
}]);
angular.module('Perlenbilanz').controller('EinkaufPositionCtrl',
	['$scope', '$location', '$filter', '$routeParams', 'EinkaufResource', 'EinkaufPositionResource', 'mwstCalculator',
	function ($scope, $location, $filter, $routeParams, EinkaufResource, EinkaufPositionResource, mwstCalculator) {

	$scope.now = new Date();
	$scope.positionen = EinkaufPositionResource.query({geliefert:false}, function (positionen) {
		//fetch plattform
		angular.forEach(positionen, function(position) {
			EinkaufResource.get({id:position.ekId},function(data){
				position.plattform = data.plattform;
			});
		});
	});
	$scope.savePositionen = function () {
		//save positions with id from einkauf
		angular.forEach($scope.positionen, function(position) {
			position.$save(function (data) {
				//update the model with the entity returned from the server
				position = new EinkaufPositionResource(data);
			});
		});
		alert('Gespeichert');
		$scope.positionen = EinkaufPositionResource.query({geliefert:false});
	};
	$scope.updateMwSt = function (position) {
		mwstCalculator.updateMwSt(position);
	};
}]);
angular.module('Perlenbilanz').controller('MenuCtrl',
	['$scope', '$window',
	function ($scope, $window) {

	// initialize date with previous month as in main.php
	$scope.reportDate = new Date();
	$scope.reportDate.setDate(1); // set to first day of month
	$scope.reportDate.setMonth(-1); // start with previous month

	$scope.generateReport = function () {
		var reportYear = $scope.reportDate.getFullYear();
		var reportMonth = ("0" + ($scope.reportDate.getMonth() + 1)).slice(-2);
		//FIXME this is a bad hack but I could not yet find an angular way of generating urls
		$window.open('report?requesttoken='+oc_requesttoken+'&year='+reportYear+'&month='+reportMonth);
	};
	/**
	 * The ui-date does not update the model when the user changes month or year, so we do that manually.
	 * @param {number} year
	 * @param {number} month
	 */
	$scope.updateReportDate = function (year, month) {
		$scope.reportDate.setYear(year);
		$scope.reportDate.setMonth(month-1);
	};

}]);

angular.module('Perlenbilanz').controller('SucheCtrl',
	['$scope', 'VerkaufResource', 'EinkaufResource',
	function ($scope, VerkaufResource, EinkaufResource) {

	$scope.table = "einkaeufe";
	$scope.column = {};
	$scope.column.position = true;
	var dummyResults = [{
		account:'dummy acc',
		name:'dummy name',
		positionen:[
			{bezeichnung:'dummy pos1'},
			{bezeichnung:'dummy pos2'},
			{bezeichnung:'dummy pos3'},
			{bezeichnung:'dummy pos4'}
		]
	},{
		account:'dummy acc2',
		name:'dummy name2',
		positionen:[
			{bezeichnung:'dummy pos1'},
			{bezeichnung:'dummy pos2'},
			{bezeichnung:'dummy pos3'}
		]
	},{
		account:'dummy acc3',
		name:'dummy name3',
		positionen:[
			{bezeichnung:'dummy pos1'},
			{bezeichnung:'dummy pos2'},
			{bezeichnung:'dummy pos3'}
		]
	}];
	$scope.results=dummyResults;
	$scope.search = function () {
		$scope.einkaeufe = EinkaufResource.query({search:'account',query:$scope.query});
		//$scope.verkaeufe = VerkaufResource.query({search:'account',query:$scope.query});
	};
}]);

angular.module('Perlenbilanz').controller('UebersichtCtrl',
	['$scope', '$location', 'NotesResource', '$timeout', 'VerkaufResource', 'EinkaufResource',
	function ($scope, $location, NotesResource, $timeout, VerkaufResource, EinkaufResource) {

	$scope.now = new Date();
	$scope.verkaeufe = VerkaufResource.query({overview:'current'},function(response){
		$scope.vkBrutto = 0;
		$scope.vkMwSt = 0;
		$scope.vkNetto = 0;
		angular.forEach(response, function(entry) {
			if (entry.wertstellung !== null) {
				$scope.vkBrutto += entry.brutto;
				$scope.vkMwSt += entry.mwst;
				$scope.vkNetto += entry.netto;
			}
		});
	});
	$scope.overdueVerkaeufe = VerkaufResource.query({overview:'overdue'});
	$scope.einkaeufe = EinkaufResource.query({overview:'current'},function(response){
		$scope.ekBrutto = 0;
		$scope.ekMwSt = 0;
		$scope.ekNetto = 0;
		angular.forEach(response, function(entry) {
			if (entry.wertstellung !== null) {
				$scope.ekBrutto += entry.brutto;
				$scope.ekMwSt += entry.mwst;
				$scope.ekNetto += entry.netto;
			}
		});
	});
	$scope.overdueEinkaeufe = EinkaufResource.query({overview:'overdue'});
	$scope.editEinkauf = function (id) {
		$location.path('/einkauf/'+id);
	};
	$scope.editVerkauf = function (id) {
		$location.path('/verkauf/'+id);
	};
	$scope.calcBruttoSum = function (positionen) {
		var brutto = 0;
		angular.forEach(positionen, function(entry) {
			brutto += entry.brutto;
		});
		return brutto;
	};
	$scope.calcMwStSum = function (positionen) {
		var mwst = 0;
		angular.forEach(positionen, function(entry) {
			mwst += entry.mwst;
		});
		return mwst;
	};
	$scope.calcNettoSum = function (positionen) {
		var netto = 0;
		angular.forEach(positionen, function(entry) {
			netto += entry.netto;
		});
		return netto;
	};
	NotesResource.get(function(response){
		$scope.notes = response.text;
	});

	$scope.saveNotes = function () {
		if ($scope.saveTimeout) {
			$timeout.cancel($scope.saveTimeout);
		}
		$scope.saveTimeout = $timeout(function () {
			NotesResource.save({text:$scope.notes},function(response){}, function(error){
				alert('could not save notes');
			});
		}, 1000); //autosave after 2 sec
	};
}]);
angular.module('Perlenbilanz').controller('VerkaeufeCtrl',
	['$scope', '$location', '$filter', '$routeParams', 'VerkaufResource', 'VerkaufPositionResource',
	function ($scope, $location, $filter, $routeParams, VerkaufResource, VerkaufPositionResource) {

	$scope.verkaeufe = VerkaufResource.query({geliefert:false});
	$scope.editVerkauf = function (id) {
		$location.path('/verkauf/'+id);
	};
}]);
angular.module('Perlenbilanz').controller('VerkaufCtrl',
	['$scope', '$location', '$window', '$filter', '$routeParams',
		'VerkaufResource', 'VerkaufPositionResource', 'RenderResource',
		'accountNameRecommender', 'mwstCalculator',
	function ($scope, $location, $window, $filter, $routeParams,
		VerkaufResource, VerkaufPositionResource, RenderResource,
		accountNameRecommender, mwstCalculator) {

	$scope.types = [
		{id:"Ware",text:"Ware"},
		{id:"Versand",text:"Versand"},
		{id:"Aufschlag",text:"Aufschlag"},
		{id:"Rabatt",text:"Rabatt"},
		{id:"Sonstige",text:"Sonstige"}
	];
	$scope.now = new Date();
	$scope.accountOptions = {
		options: {
			html: false,
			focusOpen: true,
			onlySelect: false,
			source: function (request, response) {
				var data = [];

				angular.forEach($scope.guessedAccounts, function(s) {
					if (typeof s === 'string') {
						data.push(s);
					}
				});
				
				if (data.length === 0) {
					angular.forEach($scope.accounts, function(s) {
						if (typeof s === 'string') {
							data.push(s);
						}
					});
				}	

				data = $scope.accountOptions.methods.filter(data, request.term);

				response(data);
			}
		},
		methods: {},
		events : {
			change: function( event, ui ) {
				$scope.guessNames();
			}	
		}
	};
	$scope.nameOptions = {
		options: {
			html: false,
			focusOpen: true,
			onlySelect: false,
			source: function (request, response) {
				var data = [];

				angular.forEach($scope.guessedNames, function(s) {
					if (typeof s === 'string') {
						data.push(s);
					}
				});
				
				if (data.length === 0) {
					angular.forEach($scope.names, function(s) {
						if (typeof s === 'string') {
							data.push(s);
						}
					});
				}	

				data = $scope.nameOptions.methods.filter(data, request.term);

				response(data);
			}
		},
		methods: {},
		events : {
			change: function( event, ui ) {
				$scope.guessAccounts();
			}	
		}
	};
	$scope.invoice = null;
	$scope.guessAccounts = function() {
		accountNameRecommender.guessAccounts($scope, $scope.verkauf, VerkaufResource);
	};
	$scope.guessNames = function() {
		accountNameRecommender.guessNames($scope, $scope.verkauf, VerkaufResource);
	};
	$scope.getNextInvoiceIDs = function() {
		if ($scope.verkauf.rechnungsjahr && !$scope.verkauf.rechnungsnummer) {
			$scope.nextInvoiceIDs = VerkaufResource.getNextInvoiceIDs({rechnungsjahr:$scope.verkauf.rechnungsjahr});
		}
	};
	$scope.toggleLieferanschrift = function() {
		if (!$scope.differentaddress) {
			$scope.verkauf.lieferanschrift = null;
		}
	};
	$scope.newVerkauf = function () {
		$scope.guessedAccounts = null;
		$scope.guessedNames = null;
		accountNameRecommender.fetchAccounts($scope, VerkaufResource);
		accountNameRecommender.fetchNames($scope, VerkaufResource);
		$scope.verkauf = new VerkaufResource();
		$scope.verkauf.plattform = 'fancywork';
		$scope.verkauf.zahlweise = 'Konto';
		$scope.verkauf.lieferanschrift = null;
		$scope.verkauf.faultyreason = null;
		$scope.verkauf.rechnungsjahr = new Date().getFullYear();
		$scope.positionen = [];
		$scope.addPosition(0);
		$scope.getNextInvoiceIDs();
	};
	$scope.saveAndNew = function () {
		$scope.updateVerkauf(function () {
			alert('Gespeichert');
			$location.path('/verkauf');
			$scope.newVerkauf();
		});
	};
	$scope.updateVerkauf = function (callback) {
		$scope.verkauf.$save(function (data) {
			//update the model with the entity returned from the server
			$scope.verkauf = new VerkaufResource(data);
			$location.path('/verkauf/'+$scope.verkauf.id);
			
			//save positions with id from verkauf
			angular.forEach($scope.positionen, function(position) {
				//update id
				position.vkId = $scope.verkauf.id;
				new VerkaufPositionResource(position).$save(function (data) {
					//update the model with the entity returned from the server
					position = new VerkaufPositionResource(data);
				});
			});
			//delete positions with id from verkauf
			angular.forEach($scope.deletedPositionen, function(position) {
				if (position.id) {
					// Delete existing position
					new VerkaufPositionResource(position).$delete();
				}
			});
			$scope.deletedPositionen = [];
			if(callback){
				callback();
			}
		});
	};
	$scope.addPosition = function (index) {
		$scope.positionen.splice(index+1, 0, {
			pos:index+1,
			datum: $filter('date')(new Date(),'yyyy-MM-dd'),
			stueck:1,
			typ:'Ware',
			bezeichnung:'',
			geliefert:false,
			mwstProzent:19
		});
		$scope.renumberPositions();
	};
	$scope.renumberPositions = function () {
		var pos = 1;
		angular.forEach($scope.positionen, function(position) {
			if (position.pos !== pos) {
				position.pos = pos;
			}
			pos++;
		});
	};
	$scope.deletedPositionen = [];
	$scope.removePosition = function (index) {
		var deleted = $scope.positionen.splice(index, 1);
		$scope.deletedPositionen = $scope.deletedPositionen.concat(deleted);
		$scope.renumberPositions();
	};
	$scope.$watch('positionen', function(current, previous) {
		mwstCalculator.update($scope, current);
	}, true);
	$scope.$watch('verkauf.rechnungsjahr', function(current, previous) {
		$scope.getNextInvoiceIDs();
	}, true);
	$scope.updateBrutto = function (position) {
		mwstCalculator.updateBrutto(position);
	};
	$scope.updateMwSt = function (position) {
		mwstCalculator.updateMwSt(position);
	};
	$scope.generateInvoice = function (invoiceid) {
		$scope.verkauf.rechnungsnummer = invoiceid;
		$scope.updateVerkauf(function(){
			$scope.invoice = RenderResource.renderInvoice({vkid:$scope.verkauf.id},function()
			{
				$scope.downloadInvoice();
			});
			//$scope.invoice = $scope.verkauf.$renderInvoice();
			//FIXME danach _blank download öffnen
		});
	};
	$scope.downloadInvoice = function () {
		var number = $scope.verkauf.rechnungsnummer;
		number = (number < 100)?((!(parseInt(number/10)))? "00"+number : "0"+number) : number;
		var dir = '/Perlenbilanz/Rechnungen';
		var filename = 'Rechnung '+$scope.verkauf.rechnungsjahr+'-'+number+'.pdf';
		$window.open(OC.Router.generate('download',{file:dir+'/'+filename}));
		//$window.open(OC.linkTo('files_pdfviewer', 'viewer.php')+'?dir='+encodeURIComponent(dir).replace(/%2F/g, '/')+'&file='+encodeURIComponent(filename.replace('&', '%26')));
	};
	$scope.editInvoice = function () {
		var number = $scope.verkauf.rechnungsnummer;
		number = (number < 100)?((!(parseInt(number/10)))? "00"+number : "0"+number) : number;
		var filename = 'Rechnung '+$scope.verkauf.rechnungsjahr+'-'+number+'.html';
		startEditDoc('/Perlenbilanz/Rechnungen',filename);
	};
	$scope.markFaulty = function() {
		$scope.verkauf.faultyreason = '';
	};
	$scope.deleteInvoice = function() {
		VerkaufResource.deleteInvoice($scope.verkauf, function(data){
			$scope.verkauf = data;
			$scope.getNextInvoiceIDs();
		});
	};

	if ($routeParams.id) {
		accountNameRecommender.fetchAccounts($scope, VerkaufResource);
		accountNameRecommender.fetchNames($scope, VerkaufResource);
		$scope.verkauf = VerkaufResource.get({id:$routeParams.id}, function(data){
			$scope.guessNames();
			$scope.guessAccounts();
			$scope.getNextInvoiceIDs();
		});
		$scope.positionen = VerkaufPositionResource.query({vkId:$routeParams.id});
	} else {
		$scope.newVerkauf();
	}
}]);
angular.module('Perlenbilanz').controller('WertstellungenCtrl',
	['$scope', '$location', '$filter', '$routeParams', 'VerkaufResource', 'EinkaufResource',
	function ($scope, $location, $filter, $routeParams, VerkaufResource, EinkaufResource) {

	$scope.einkaeufe = EinkaufResource.query({wertstellung:null});
	$scope.verkaeufe = VerkaufResource.query({wertstellung:null});
	$scope.save = function () {
		angular.forEach($scope.einkaeufe, function(einkauf) {
			einkauf.$save();
		});
		angular.forEach($scope.verkaeufe, function(verkauf) {
			verkauf.$save();
		});
		alert('Gespeichert');
		$scope.einkaeufe = EinkaufResource.query({wertstellung:null});
		$scope.verkaeufe = VerkaufResource.query({wertstellung:null});
	};
}]);
angular.module('Perlenbilanz').directive('autoComplete', function($filter, $timeout) {
	return {
		restrict: 'A',
		link: function(scope, elem, attr, ctrl) {
			elem.autocomplete({
				//source: scope[iAttrs.uiItems],
				source: function(request, response) {
					response( $filter('filter')(scope[attr.uiItems], request.term) );
				},
				select: function() {
					$timeout(function() {
						elem.trigger('input');
					}, 0);
				}
			});
		}
	};
});

// matches 1,000.23
var FLOAT_REGEXP_DOT = /^[+-]?[0-9]{1,3}(?:(?:\,[0-9]{3})*(?:\.[0-9][0-9]?)?|[0-9]*(?:[\.][0-9][0-9]?)?)$/;
//matches 1.000,23
var FLOAT_REGEXP_COMMA = /^[+-]?[0-9]{1,3}(?:|(?:\.[0-9]{3})*(?:\,[0-9][0-9]?)?|[0-9]*(?:[\,][0-9][0-9]?)?)$/;

//currency input field from from http://jsfiddle.net/odiseo/dj6mX/
String.prototype.splice = function(idx, rem, s) {
	return (this.slice(0, idx) + s + this.slice(idx + Math.abs(rem)));
};

angular.module('Perlenbilanz').directive('currencyInput', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {

			//filter key events
			$(elm).keydown(function(event) {
				var keyCode = ('which' in event) ? event.which : event.keyCode;
				if (event.ctrlKey && (keyCode === 67 || keyCode === 88 || keyCode === 86)) {
					return true;
				} else if ((keyCode >= 96 && keyCode <= 105) || keyCode === 109 || keyCode === 110) { /* numpad 0..9 DOM_VK_SUBTRACT(109,-) DOM_VK_DECIMAL(110,.) */
					return true;
				} else if (keyCode >= 188 && keyCode <= 190) { /* , . DOM_VK_COMMA(188,-) DOM_VK_PERIOD(190,.) */ 
					return true;
				} else if (keyCode === 173) { /* DOM_VK_HYPHEN_MINUS(173,-) */
					return true;
				} else if (keyCode >= 58) { /* DOM_VK_COLON(58,:) */
					return false;
				}
				return true;
			});
			ctrl.$parsers.unshift(function(viewValue) {
				var clean;
				if (FLOAT_REGEXP_COMMA.test(viewValue)) {
					clean = viewValue.replace(/\./g, '').replace(/,/g, '.');
					ctrl.$setValidity('float', true);
					return parseFloat(clean);
				} else if (FLOAT_REGEXP_DOT.test(viewValue)) {
					clean = viewValue.replace(/\,/g, '');
					ctrl.$setValidity('float', true);
					return parseFloat(clean);
				} else {
					ctrl.$setValidity('float', false);
					return undefined;
				}
			});
			// model -> view
			ctrl.$render = function(){
				// render float as string
				var result = '';

				if (typeof ctrl.$viewValue === 'number') {

					var value = ctrl.$viewValue;
					value = ''+Math.round(value*100)/100;

					var decimalSplit = value.split('.');
					var intPart = decimalSplit[0];
					var decPart = decimalSplit[1];

					if (intPart.length > 3) {
						var intDiv = Math.floor(intPart.length / 3);
						while (intDiv > 0) {
							var lastPoint = intPart.indexOf(".");
							if (lastPoint < 0) {
								lastPoint = intPart.length;
							}

							if (lastPoint - 3 > 0) {
								intPart = intPart.splice(lastPoint - 3, 0, '.');
							}
							intDiv--;
						}
					}

					if (decPart === undefined) {
						decPart = '';
					} else {
						if (decPart.length === 1) {
							decPart = decPart + '0';
						}
						if (decPart.length > 2) {
							decPart = decPart.substr(0,2);
							}
						decPart = ',' + decPart;
					}
					result = intPart + decPart;
				}

				elm.val(result);
			};
		}
	};
});


angular.module('Perlenbilanz').directive('emtpyToNull', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {
			ctrl.$parsers.unshift(function(viewValue) {
				if (viewValue === '') {
					return null;
				} else {
					return viewValue;
				}
			});
		}
	};
});

// from the angularjs docs: http://docs.angularjs.org/guide/forms
var INTEGER_REGEXP = /^\-?\d*$/;
angular.module('Perlenbilanz').directive('integer', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {
			ctrl.$parsers.unshift(function(viewValue) {
				if (INTEGER_REGEXP.test(viewValue)) {
					// it is valid
					ctrl.$setValidity('integer', true);
					if (viewValue === '') {
						return null;
					}
					return parseInt(viewValue);
				} else {
					// it is invalid, return undefined (no model update)
					ctrl.$setValidity('integer', false);
					return undefined;
				}
			});
		}
	};
});

angular.module('Perlenbilanz').directive('onCursorDown', function() {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 40) {
				scope.$apply(function (){
					scope.$eval(attrs.onCursorDown);
				});
			}
		});
	};
});

angular.module('Perlenbilanz').directive('onCursorUp', function() {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 38) {
				scope.$apply(function (){
					scope.$eval(attrs.onCursorUp);
				});
			}
		});
	};
});

var FLOAT_REGEXP = /^\-?\d+((\.|\,)\d+)?$/;
angular.module('Perlenbilanz').directive('smartFloat', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {
			ctrl.$parsers.unshift(function(viewValue) {
				if (FLOAT_REGEXP.test(viewValue)) {
					ctrl.$setValidity('float', true);
					return parseFloat(viewValue.replace(',', '.'));
				} else {
					ctrl.$setValidity('float', false);
					return undefined;
				}
			});
		}
	};
});

angular.module('Perlenbilanz').
	factory('accountNameRecommender', function(){
		var accountNameRecommender = {};
		accountNameRecommender.fetchAccounts = function ($scope, Resource) {
			Resource.listAccounts(function(response){
				$scope.accounts = [];
				angular.forEach(response, function(entry) {
					$scope.accounts.push(entry);
				});
			});
		};
		accountNameRecommender.fetchNames = function ($scope, Resource) {
			Resource.listNames(function(response){
				$scope.names = [];
				angular.forEach(response, function(entry) {
					$scope.names.push(entry);
				});
			});
		};
		accountNameRecommender.guessAccounts = function ($scope, instance, Resource) {
			if (instance.name === '') {
				$scope.guessedAccounts = null;
			} else {
				Resource.guessAccount({plattform:instance.plattform, name:instance.name},function (data) {
					$scope.guessedAccounts = [];
					angular.forEach(data, function(entry) {
						$scope.guessedAccounts.push(entry);
					});
					if ($scope.guessedAccounts.length === 1 && !instance.account) {
						instance.account = $scope.guessedAccounts[0].id;
					}
				});
			}
		};
		accountNameRecommender.guessNames = function($scope, instance, Resource) {
			if (instance.account === '') {
				$scope.guessedNames = null;
			} else {
				Resource.guessName({plattform:instance.plattform, account:instance.account},function (data) {
					$scope.guessedNames = [];
					angular.forEach(data, function(entry) {
						$scope.guessedNames.push(entry);
					});
					if ($scope.guessedNames.length === 1 && !instance.name ) {
						instance.name = $scope.guessedNames[0].id;
					}
				});
			}
		};
		return accountNameRecommender;
	});

angular.module('Perlenbilanz').
	factory('EinkaufPositionResource', function($resource){
		return $resource('ajax/einkaufposition/:id', {id:'@id', requesttoken:oc_requesttoken});
	});

angular.module('Perlenbilanz').
	factory('EinkaufResource', function($resource){
		return $resource('ajax/einkauf/:id', {id:'@id', requesttoken:oc_requesttoken},
			{listAccounts:{method:'GET',params:{list:'accounts'}},
				listNames:{method:'GET',params:{list:'names'}},
				guessName:{method:'GET',params:{guess:'name'}},
			guessAccount :{method:'GET',params:{guess:'account'}}});
	});

angular.module('Perlenbilanz').
	factory('mwstCalculator', function() {
		var mwstCalculator = {};
		mwstCalculator.update = function($scope, positions) {
			var bruttoTotal = 0;
			var mwstGroups = [];
			if (positions) {
				$.each(positions, function (i, position) {
					var brutto = position.brutto;
					if (typeof brutto === 'undefined' || brutto === null) {
						brutto = 0;
					}

					if (position.typ === 'Rabatt' && brutto > 0) {
						brutto = brutto * -1;
						position.brutto = brutto;
					}

					var stueck = position.stueck;
					if (typeof stueck === 'undefined' || stueck === null) {
						stueck = 1;
					}
					brutto = brutto * stueck;
					position.bruttoSum = brutto;
					
					bruttoTotal += brutto;
					
					var mwstProzent = 0;
					if (typeof position.mwstProzent === 'number') {
						mwstProzent = position.mwstProzent;
					} else if ((typeof position.mwstProzent === 'string' && position.mwstProzent === '' ) ||
							(typeof position.mwstProzent === 'object' && position.mwstProzent === null )) {
						//calculate mwst & netto with old mwstStatus
						mwstProzent = 'various';
						position.netto = brutto-position.mwst;
					} else {
						if (typeof position.mwstStatus !== 'undefined') {
							if (position.mwstStatus === true) {
								mwstProzent = 19;
							}
						}
					}

					//add netto to existing group
					var newGroup = true;
					$.each(mwstGroups, function (i, mwstGroup) {
						if (mwstGroup.mwstProzent === mwstProzent) {
							mwstGroup.brutto += brutto;
							if (mwstProzent === 'various') {
								mwstGroup.netto += position.netto;
								mwstGroup.mwst += position.mwst;
							}
							newGroup=false;
						}
					});
					//create new group
					if (newGroup) {
						if (mwstProzent === 'various') {
							mwstGroups.push({
								mwstProzent:mwstProzent,
								brutto:brutto,
								mwst:position.mwst,
								netto:position.netto});
						} else {
							mwstGroups.push({mwstProzent:mwstProzent,brutto:brutto});
						}
					}
				});
			}

			var mwstTotal = 0;
			$.each(mwstGroups, function (i, mwstGroup) {
				if (mwstGroup.mwstProzent === 'various') {
					//already calculated, just round
					mwstGroup.mwst = Math.round(mwstGroup.mwst*100)/100;
				} else {
					mwstGroup.mwst = Math.round((mwstGroup.brutto-(mwstGroup.brutto / (1+mwstGroup.mwstProzent/100)))*100)/100;
				}
				mwstTotal += mwstGroup.mwst;
			});

			$scope.bruttoTotal = Math.round(bruttoTotal*100)/100;
			$scope.mwstGroups = mwstGroups;
			$scope.nettoTotal = Math.round((bruttoTotal-mwstTotal)*100)/100;
		};
		mwstCalculator.updateBrutto = function(position) {
			var brutto = position.brutto;
			if (typeof brutto === 'undefined' || brutto === null) {
				brutto = 0;
			}
			if (position.typ === 'Rabatt' && brutto > 0) {
				brutto = brutto * -1;
				position.brutto = brutto;
			}
			if (typeof brutto === 'number') {
				var mwstProzent = 0;
				var netto = 0;
				var stueck = position.stueck;
				if (typeof stueck === 'undefined' || stueck === null) {
					stueck = 1;
				}
				brutto = brutto * stueck;
				if (typeof position.mwstProzent === 'number') {
					mwstProzent = position.mwstProzent;
					netto = brutto / (1+mwstProzent/100);
				} else if (typeof position.mwstProzent === 'string' && position.mwstProzent === '' ) {
					//calculate mwst & netto with old mwstStatus
					netto = brutto-position.mwst;
				} else {
					if (typeof position.mwstStatus !== 'undefined') {
						if (position.mwstStatus === true) {
							mwstProzent = 19;
						}
					}
					netto = brutto / (1+mwstProzent/100);
				}
				var mwst = brutto - netto;
				position.bruttoSum = brutto;
				position.netto = netto;
				position.mwst = mwst;
			}
		};
		mwstCalculator.updateMwSt = function(position) {
			var brutto = position.brutto;
			if (typeof brutto === 'undefined' || brutto === null) {
				brutto = 0;
			}
			if (position.typ === 'Rabatt' && brutto > 0) {
				brutto = brutto * -1;
				position.brutto = brutto;
			}
			if (typeof brutto === 'number') {
				var stueck = position.stueck;
				if (typeof stueck === 'undefined' || stueck === null) {
					stueck = 1;
				}
				brutto = brutto * stueck;
				var mwstProzent = 0;
				if (typeof position.mwstStatus !== 'undefined') {
					if (position.mwstStatus === true) {
						position.mwstProzent = 19;
					} else {
						position.mwstProzent = 0;
					}
					mwstProzent = position.mwstProzent;
				}
				if (typeof position.mwstProzent === 'number') {
					mwstProzent = position.mwstProzent;
				}
				var netto = brutto / (1+mwstProzent/100);
				var mwst = brutto - netto;
				position.bruttoSum = brutto;
				position.netto = netto;
				position.mwst = mwst;
			}
		};
		mwstCalculator.updateMwStProzent = function(position) {
			var brutto = position.brutto;
			if (typeof brutto === 'undefined' || brutto === null) {
				brutto = 0;
			}
			if (position.typ === 'Rabatt' && brutto > 0) {
				brutto = brutto * -1;
				position.brutto = brutto;
			}
			if (typeof brutto === 'number') {
				var stueck = position.stueck;
				if (typeof stueck === 'undefined' || stueck === null) {
					stueck = 1;
				}
				brutto = brutto * stueck;
				var mwstProzent = position.mwstProzent;
				if (mwstProzent === null) {
					if (position.mwstStatus === true) {
						mwstProzent = 19;
					} else {
						mwstProzent = 0;
					}
				}
				var netto = brutto / (1+mwstProzent/100);
				var mwst = brutto - netto;
				position.bruttoSum = brutto;
				position.netto = netto;
				position.mwst = mwst;
			}
		};
		return mwstCalculator;
	});

angular.module('Perlenbilanz').
	factory('NotesResource', function($resource){
		return $resource('ajax/notes', {id:'@id', requesttoken:oc_requesttoken});
	});

angular.module('Perlenbilanz').
	factory('RenderResource', function($resource){
		return $resource('ajax/render/:doctype', {requesttoken:oc_requesttoken},
			{renderInvoice:{method:'POST',params:{doctype:'invoice'}},
			renderReport  :{method:'GET',params:{doctype:'report'}}});
	});

angular.module('Perlenbilanz').
	factory('VerkaufPositionResource', function($resource){
		return $resource('ajax/verkaufposition/:id', {id:'@id', requesttoken:oc_requesttoken});
	});

angular.module('Perlenbilanz').
	factory('VerkaufResource', function($resource){
		return $resource('ajax/verkauf/:id/:doctype', {id:'@id', requesttoken:oc_requesttoken},
			{listAccounts:{method:'GET',  params:{list:'accounts'}},
				listNames:{method:'GET',  params:{list:'names'}},
				guessName:{method:'GET',  params:{guess:'name'}},
			guessAccount :{method:'GET',  params:{guess:'account'}},
			deleteInvoice:{method:'POST', params:{doctype:'invoice'}},
		getNextInvoiceIDs:{method:'GET',  params:{next:'invoiceids'}}}); // TODO extra action to render as pdf?
	});

angular.module('Perlenbilanz').filter('empty', function() {
	return function(input) {
		if (typeof input === 'undefined' ||
			(typeof input === 'object' && input === null) ||
			(typeof input === 'string' && input === '')
		){
			return true;
		} else {
			return false;
		}
	};
});

angular.module('Perlenbilanz').filter('exists', function() {
	return function(input) {
		if (typeof input === 'undefined') {
			return false;
		} else {
			return true;
		}
	};
});

angular.module('Perlenbilanz').filter('notnull', function() {
	return function(input) {
		if (typeof input !== 'undefined' && input === null) {
			return false;
		} else {
			return true;
		}
	};
});

angular.module('Perlenbilanz').filter('number_de', function() {
	return function(input) {
		if (typeof input === 'undefined' || input === null) {
			return '';
		} else if (typeof input === 'number') {
			input = ''+Math.round(input*100)/100;
		}

		var decimalSplit = input.split('.');
		var intPart = decimalSplit[0];
		var decPart = decimalSplit[1];

		intPart = intPart.replace(/[^\d-+]/g, '');
		if (intPart.length > 3) {
			var intDiv = Math.floor(intPart.length / 3);
			while (intDiv > 0) {
				var lastPoint = intPart.indexOf(".");
				if (lastPoint < 0) {
					lastPoint = intPart.length;
				}

				if (lastPoint - 3 > 0) {
					intPart = intPart.splice(lastPoint - 3, 0, '.');
				}
				intDiv--;
			}
		} else if (intPart.length === 0) {
			intPart = '0';
		}

		if (decPart === undefined) {
			decPart = ',00';
		} else {
			if (decPart.length > 2) {
				decPart = decPart.substr(0,2);
			} else if (decPart.length === 1) {
				decPart = decPart + '0';
			}
			decPart = ',' + decPart;
		}
		var res = intPart + decPart;
		
		return res;
	};
});

angular.module('Perlenbilanz').filter('prependZero' , function() {
	return function(number) {
		return (number < 100)?((!(parseInt(number/10)))? "00"+number : "0"+number) : number;
	};
});

