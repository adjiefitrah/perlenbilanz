angular.module('Perlenbilanz').controller('VerkaufCtrl',
	['$scope', '$location', '$window', '$filter', '$routeParams',
		'VerkaufResource', 'VerkaufPositionResource', 'RenderResource',
		'accountNameRecommender', 'mwstCalculator',
	function ($scope, $location, $window, $filter, $routeParams,
		VerkaufResource, VerkaufPositionResource, RenderResource,
		accountNameRecommender, mwstCalculator) {

	$scope.types = [
		{id:"Ware",text:"Ware"},
		{id:"Versand",text:"Versand"},
		{id:"Aufschlag",text:"Aufschlag"},
		{id:"Rabatt",text:"Rabatt"},
		{id:"Sonstige",text:"Sonstige"}
	];
	$scope.now = new Date();
	$scope.accountOptions = {
		options: {
			html: false,
			focusOpen: true,
			onlySelect: false,
			source: function (request, response) {
				var data = [];

				angular.forEach($scope.guessedAccounts, function(s) {
					if (typeof s === 'string') {
						data.push(s);
					}
				});
				
				if (data.length === 0) {
					angular.forEach($scope.accounts, function(s) {
						if (typeof s === 'string') {
							data.push(s);
						}
					});
				}	

				data = $scope.accountOptions.methods.filter(data, request.term);

				response(data);
			}
		},
		methods: {},
		events : {
			change: function( event, ui ) {
				$scope.guessNames();
			}	
		}
	};
	$scope.nameOptions = {
		options: {
			html: false,
			focusOpen: true,
			onlySelect: false,
			source: function (request, response) {
				var data = [];

				angular.forEach($scope.guessedNames, function(s) {
					if (typeof s === 'string') {
						data.push(s);
					}
				});
				
				if (data.length === 0) {
					angular.forEach($scope.names, function(s) {
						if (typeof s === 'string') {
							data.push(s);
						}
					});
				}	

				data = $scope.nameOptions.methods.filter(data, request.term);

				response(data);
			}
		},
		methods: {},
		events : {
			change: function( event, ui ) {
				$scope.guessAccounts();
			}	
		}
	};
	$scope.invoice = null;
	$scope.guessAccounts = function() {
		accountNameRecommender.guessAccounts($scope, $scope.verkauf, VerkaufResource);
	};
	$scope.guessNames = function() {
		accountNameRecommender.guessNames($scope, $scope.verkauf, VerkaufResource);
	};
	$scope.getNextInvoiceIDs = function() {
		if ($scope.verkauf.rechnungsjahr && !$scope.verkauf.rechnungsnummer) {
			$scope.nextInvoiceIDs = VerkaufResource.getNextInvoiceIDs({rechnungsjahr:$scope.verkauf.rechnungsjahr});
		}
	};
	$scope.toggleLieferanschrift = function() {
		if (!$scope.differentaddress) {
			$scope.verkauf.lieferanschrift = null;
		}
	};
	$scope.newVerkauf = function () {
		$scope.guessedAccounts = null;
		$scope.guessedNames = null;
		accountNameRecommender.fetchAccounts($scope, VerkaufResource);
		accountNameRecommender.fetchNames($scope, VerkaufResource);
		$scope.verkauf = new VerkaufResource();
		$scope.verkauf.plattform = 'fancywork';
		$scope.verkauf.zahlweise = 'Konto';
		$scope.verkauf.lieferanschrift = null;
		$scope.verkauf.faultyreason = null;
		$scope.verkauf.rechnungsjahr = new Date().getFullYear();
		$scope.positionen = [];
		$scope.addPosition(0);
		$scope.getNextInvoiceIDs();
	};
	$scope.saveAndNew = function () {
		$scope.updateVerkauf(function () {
			alert('Gespeichert');
			$location.path('/verkauf');
			$scope.newVerkauf();
		});
	};
	$scope.updateVerkauf = function (callback) {
		$scope.verkauf.$save(function (data) {
			//update the model with the entity returned from the server
			$scope.verkauf = new VerkaufResource(data);
			$location.path('/verkauf/'+$scope.verkauf.id);
			
			//save positions with id from verkauf
			angular.forEach($scope.positionen, function(position) {
				//update id
				position.vkId = $scope.verkauf.id;
				new VerkaufPositionResource(position).$save(function (data) {
					//update the model with the entity returned from the server
					position = new VerkaufPositionResource(data);
				});
			});
			//delete positions with id from verkauf
			angular.forEach($scope.deletedPositionen, function(position) {
				if (position.id) {
					// Delete existing position
					new VerkaufPositionResource(position).$delete();
				}
			});
			$scope.deletedPositionen = [];
			if(callback){
				callback();
			}
		});
	};
	$scope.addPosition = function (index) {
		$scope.positionen.splice(index+1, 0, {
			pos:index+1,
			datum: $filter('date')(new Date(),'yyyy-MM-dd'),
			stueck:1,
			typ:'Ware',
			bezeichnung:'',
			geliefert:false,
			mwstProzent:19
		});
		$scope.renumberPositions();
	};
	$scope.renumberPositions = function () {
		var pos = 1;
		angular.forEach($scope.positionen, function(position) {
			if (position.pos !== pos) {
				position.pos = pos;
			}
			pos++;
		});
	};
	$scope.deletedPositionen = [];
	$scope.removePosition = function (index) {
		var deleted = $scope.positionen.splice(index, 1);
		$scope.deletedPositionen = $scope.deletedPositionen.concat(deleted);
		$scope.renumberPositions();
	};
	$scope.$watch('positionen', function(current, previous) {
		mwstCalculator.update($scope, current);
	}, true);
	$scope.$watch('verkauf.rechnungsjahr', function(current, previous) {
		$scope.getNextInvoiceIDs();
	}, true);
	$scope.updateBrutto = function (position) {
		mwstCalculator.updateBrutto(position);
	};
	$scope.updateMwSt = function (position) {
		mwstCalculator.updateMwSt(position);
	};
	$scope.generateInvoice = function (invoiceid) {
		$scope.verkauf.rechnungsnummer = invoiceid;
		$scope.updateVerkauf(function(){
			$scope.invoice = RenderResource.renderInvoice({vkid:$scope.verkauf.id},function()
			{
				$scope.downloadInvoice();
			});
			//$scope.invoice = $scope.verkauf.$renderInvoice();
			//FIXME danach _blank download öffnen
		});
	};
	$scope.downloadInvoice = function () {
		var number = $scope.verkauf.rechnungsnummer;
		number = (number < 100)?((!(parseInt(number/10)))? "00"+number : "0"+number) : number;
		var dir = '/Perlenbilanz/Rechnungen';
		var filename = 'Rechnung '+$scope.verkauf.rechnungsjahr+'-'+number+'.pdf';
		$window.open(OC.linkTo('files_pdfviewer', 'viewer.php')+'?dir='+encodeURIComponent(dir).replace(/%2F/g, '/')+'&file='+encodeURIComponent(filename.replace('&', '%26')));
	};
	$scope.editInvoice = function () {
		var number = $scope.verkauf.rechnungsnummer;
		number = (number < 100)?((!(parseInt(number/10)))? "00"+number : "0"+number) : number;
		var filename = 'Rechnung '+$scope.verkauf.rechnungsjahr+'-'+number+'.html';
		startEditDoc('/Perlenbilanz/Rechnungen',filename);
	};
	$scope.markFaulty = function() {
		$scope.verkauf.faultyreason = '';
	};
	$scope.deleteInvoice = function() {
		VerkaufResource.deleteInvoice($scope.verkauf, function(data){
			$scope.verkauf = data;
			$scope.getNextInvoiceIDs();
		});
	};

	if ($routeParams.id) {
		accountNameRecommender.fetchAccounts($scope, VerkaufResource);
		accountNameRecommender.fetchNames($scope, VerkaufResource);
		$scope.verkauf = VerkaufResource.get({id:$routeParams.id}, function(data){
			$scope.guessNames();
			$scope.guessAccounts();
			$scope.getNextInvoiceIDs();
		});
		$scope.positionen = VerkaufPositionResource.query({vkId:$routeParams.id});
	} else {
		$scope.newVerkauf();
	}
}]);