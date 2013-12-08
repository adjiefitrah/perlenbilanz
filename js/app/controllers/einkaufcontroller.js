angular.module('Perlenbilanz').controller('EinkaufCtrl',
	['$scope', '$location', '$filter', '$routeParams', 'EinkaufResource',
		'EinkaufPositionResource', 'accountNameRecommender', 'mwstCalculator',
	function ($scope, $location, $filter, $routeParams, EinkaufResource,
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
	$scope.accountOptions = {
		query: function (query) {
			var data = {results: []};
			var items = $scope.accounts;
			if ($scope.guessedAccounts) {
				items = $scope.guessedAccounts;
			}
			angular.forEach(items, function(item, key){
				if (query.term.toUpperCase() === item.text.substring(0, query.term.length).toUpperCase()) {
					data.results.push(item);
				}
			});
			query.callback(data);
		},
		createSearchChoice: function (term) {
			return {id:term,text:term};
		},
		initSelection : function (element, callback) {
			var data = {id: element.val(), text: element.val()};
			callback(data);
		},
		placeholder: "",
		allowClear: true
	};
	$scope.nameOptions = {
		query: function (query) {
			var data = {results: []};
			var items = $scope.names;
			if ($scope.guessedNames) {
				items = $scope.guessedNames;
			}
			angular.forEach(items, function(item, key){
				if (query.term.toUpperCase() === item.text.substring(0, query.term.length).toUpperCase()) {
					data.results.push(item);
				}
			});
			query.callback(data);
		},
		createSearchChoice: function (term) {
			return {id:term,text:term};
		},
		initSelection : function (element, callback) {
			var data = {id: element.val(), text: element.val()};
			callback(data);
		},
		placeholder: "",
		allowClear: true
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
		accountNameRecommender.fetchAccounts($scope, EinkaufResource);
		accountNameRecommender.fetchNames($scope, EinkaufResource);
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
		accountNameRecommender.fetchAccounts($scope, EinkaufResource);
		accountNameRecommender.fetchNames($scope, EinkaufResource);
		$scope.einkauf = EinkaufResource.get({id:$routeParams.id}, function(data){
			$scope.guessNames();
			$scope.guessAccounts();
		});
		$scope.positionen = EinkaufPositionResource.query({ekId:$routeParams.id});
	} else {
		$scope.newEinkauf();
	}
}]);