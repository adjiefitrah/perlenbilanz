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
