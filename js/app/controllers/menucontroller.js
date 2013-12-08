angular.module('Perlenbilanz').controller('MenuCtrl',
	['$scope', '$window',
	function ($scope, $window) {

	$scope.reportDate = new Date();
	$scope.reportDate.setDate(1);
	$scope.reportDate.setMonth($scope.reportDate.getMonth()-1);

	$scope.reportYear = ''+$scope.reportDate.getFullYear();
	if ($scope.reportDate.getMonth() < 10) {
		$scope.reportMonth = '0'+$scope.reportDate.getMonth();
	} else {

		$scope.reportMonth = ''+$scope.reportDate.getMonth();
	}

	$scope.generateReport = function () {
		//FIXME this is a bad hack but I could not yet find an angular way of generating urls
		$window.open('report?requesttoken='+oc_requesttoken+'&year='+$scope.reportYear+'&month='+$scope.reportMonth);
	};
	$scope.updateReportDate = function (year, month) {
		$scope.reportYear = ''+year;
		if (month < 10) {
			$scope.reportMonth = '0'+month;
		} else {

			$scope.reportMonth = ''+month;
		}
	};

}]);
