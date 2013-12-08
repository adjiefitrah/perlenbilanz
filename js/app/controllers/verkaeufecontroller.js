angular.module('Perlenbilanz').controller('VerkaeufeCtrl',
	['$scope', '$location', '$filter', '$routeParams', 'VerkaufResource', 'VerkaufPositionResource',
	function ($scope, $location, $filter, $routeParams, VerkaufResource, VerkaufPositionResource) {

	$scope.verkaeufe = VerkaufResource.query({geliefert:false});
	$scope.editVerkauf = function (id) {
		$location.path('/verkauf/'+id);
	};
}]);