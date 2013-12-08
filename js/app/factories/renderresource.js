angular.module('Perlenbilanz').
	factory('RenderResource', function($resource){
		return $resource('ajax/render/:doctype', {requesttoken:oc_requesttoken},
			{renderInvoice:{method:'POST',params:{doctype:'invoice'}},
			renderReport  :{method:'GET',params:{doctype:'report'}}});
	});
