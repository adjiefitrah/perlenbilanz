angular.module('Perlenbilanz').filter('exists', function() {
	return function(input) {
		if (typeof input === 'undefined') {
			return false;
		} else {
			return true;
		}
	};
});
