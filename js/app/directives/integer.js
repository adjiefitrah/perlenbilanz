// from the angularjs docs: http://docs.angularjs.org/guide/forms
var INTEGER_REGEXP = /^\-?\d*$/;
angular.module('Perlenbilanz').directive('integer', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {
			ctrl.$parsers.unshift(function(viewValue) {
				if (INTEGER_REGEXP.test(viewValue)) {
					// it is valid
					ctrl.$setValidity('integer', true);
					if (viewValue === '') {
						return null;
					}
					return parseInt(viewValue);
				} else {
					// it is invalid, return undefined (no model update)
					ctrl.$setValidity('integer', false);
					return undefined;
				}
			});
		}
	};
});
