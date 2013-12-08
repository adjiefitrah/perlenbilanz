angular.module('Perlenbilanz').
	factory('NotesResource', function($resource){
		return $resource('ajax/notes', {id:'@id', requesttoken:oc_requesttoken});
	});
