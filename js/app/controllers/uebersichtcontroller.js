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