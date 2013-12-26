angular.module('Perlenbilanz').filter('number_de', function() {
	return function(input) {
		if (typeof input === 'undefined' || input === null) {
			return '';
		} else if (typeof input === 'number') {
			input = ''+Math.round(input*100)/100;
		}

		var decimalSplit = input.split('.');
		var intPart = decimalSplit[0];
		var decPart = decimalSplit[1];

		intPart = intPart.replace(/[^\d-+]/g, '');
		if (intPart.length > 3) {
			var intDiv = Math.floor(intPart.length / 3);
			while (intDiv > 0) {
				var lastPoint = intPart.indexOf(".");
				if (lastPoint < 0) {
					lastPoint = intPart.length;
				}

				if (lastPoint - 3 > 0) {
					intPart = intPart.splice(lastPoint - 3, 0, '.');
				}
				intDiv--;
			}
		} else if (intPart.length === 0) {
			intPart = '0';
		}

		if (decPart === undefined) {
			decPart = ',00';
		} else {
			if (decPart.length > 2) {
				decPart = decPart.substr(0,2);
			} else if (decPart.length === 1) {
				decPart = decPart + '0';
			}
			decPart = ',' + decPart;
		}
		var res = intPart + decPart;
		
		return res;
	};
});
