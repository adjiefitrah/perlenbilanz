angular.module('Perlenbilanz').
	factory('VerkaufResource', function($resource){
		return $resource('ajax/verkauf/:id/:doctype', {id:'@id', requesttoken:oc_requesttoken},
			{listAccounts:{method:'GET',  params:{list:'accounts'}},
				listNames:{method:'GET',  params:{list:'names'}},
				guessName:{method:'GET',  params:{guess:'name'}},
			guessAccount :{method:'GET',  params:{guess:'account'}},
			deleteInvoice:{method:'POST', params:{doctype:'invoice'}},
		getNextInvoiceIDs:{method:'GET',  params:{next:'invoiceids'}}}); // TODO extra action to render as pdf?
	});
