angular.module('Perlenbilanz').filter('prependZero' , function() {
	return function(number) {
		return (number < 100)?((!(parseInt(number/10)))? "00"+number : "0"+number) : number;
	};
});
