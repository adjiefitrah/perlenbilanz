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