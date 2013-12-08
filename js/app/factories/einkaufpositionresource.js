angular.module('Perlenbilanz').
	factory('EinkaufPositionResource', function($resource){
		return $resource('ajax/einkaufposition/:id', {id:'@id', requesttoken:oc_requesttoken});
	});
