angular.module('Perlenbilanz').directive('onCursorUp', function() {
	return function (scope, element, attrs) {
		element.bind("keydown keypress", function (event) {
			if(event.which === 38) {
				scope.$apply(function (){
					scope.$eval(attrs.onCursorUp);
				});
			}
		});
	};
});
