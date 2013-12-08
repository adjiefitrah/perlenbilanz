angular.module('Perlenbilanz').
	factory('EinkaufResource', function($resource){
		return $resource('ajax/einkauf/:id', {id:'@id', requesttoken:oc_requesttoken},
			{listAccounts:{method:'GET',params:{list:'accounts'}},
				listNames:{method:'GET',params:{list:'names'}},
				guessName:{method:'GET',params:{guess:'name'}},
			guessAccount :{method:'GET',params:{guess:'account'}}});
	});
