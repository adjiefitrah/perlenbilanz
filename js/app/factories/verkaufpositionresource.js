angular.module('Perlenbilanz').
	factory('VerkaufPositionResource', function($resource){
		return $resource('ajax/verkaufposition/:id', {id:'@id', requesttoken:oc_requesttoken});
	});
