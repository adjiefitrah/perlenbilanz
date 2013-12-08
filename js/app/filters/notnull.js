angular.module('Perlenbilanz').filter('notnull', function() {
	return function(input) {
		if (typeof input !== 'undefined' && input === null) {
			return false;
		} else {
			return true;
		}
	};
});
