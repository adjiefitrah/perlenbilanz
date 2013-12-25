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