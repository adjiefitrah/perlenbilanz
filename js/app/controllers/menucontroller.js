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
