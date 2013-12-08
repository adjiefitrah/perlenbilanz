angular.module('Perlenbilanz').filter('empty', function() {
	return function(input) {
		if (typeof input === 'undefined' ||
			(typeof input === 'object' && input === null) ||
			(typeof input === 'string' && input === '')
		){
			return true;
		} else {
			return false;
		}
	};
});
