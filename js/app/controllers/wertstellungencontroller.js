angular.module('Perlenbilanz').controller('WertstellungenCtrl',
	['$scope', '$location', '$filter', '$routeParams', 'VerkaufResource', 'EinkaufResource',
	function ($scope, $location, $filter, $routeParams, VerkaufResource, EinkaufResource) {

	$scope.einkaeufe = EinkaufResource.query({wertstellung:'null'});
	$scope.verkaeufe = VerkaufResource.query({wertstellung:'null'});
	$scope.save = function () {
		angular.forEach($scope.einkaeufe, function(einkauf) {
			einkauf.$save();
		});
		angular.forEach($scope.verkaeufe, function(verkauf) {
			verkauf.$save();
		});
		alert('Gespeichert');
		$scope.einkaeufe = EinkaufResource.query({wertstellung:'null'});
		$scope.verkaeufe = VerkaufResource.query({wertstellung:'null'});
	};
}]);