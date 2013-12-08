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
