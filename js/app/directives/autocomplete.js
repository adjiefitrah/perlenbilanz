angular.module('Perlenbilanz').directive('autoComplete', function($filter, $timeout) {
	return {
		restrict: 'A',
		link: function(scope, elem, attr, ctrl) {
			elem.autocomplete({
				//source: scope[iAttrs.uiItems],
				source: function(request, response) {
					response( $filter('filter')(scope[attr.uiItems], request.term) );
				},
				select: function() {
					$timeout(function() {
						elem.trigger('input');
					}, 0);
				}
			});
		}
	};
});
