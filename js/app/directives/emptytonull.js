angular.module('Perlenbilanz').directive('emtpyToNull', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {
			ctrl.$parsers.unshift(function(viewValue) {
				if (viewValue === '') {
					return null;
				} else {
					return viewValue;
				}
			});
		}
	};
});
