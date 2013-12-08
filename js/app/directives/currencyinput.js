/*
see http://stackoverflow.com/a/1296071/828717
^[+-]?[0-9]{1,3}
(?:
    (?:\,[0-9]{3})*
    (?:.[0-9]{2})?
|
    (?:\.[0-9]{3})*
    (?:\,[0-9]{2})?
|
    [0-9]*
    (?:[\.\,][0-9]{2})?
)$
 */
// matches 1,000.23
var FLOAT_REGEXP_DOT = /^[+-]?[0-9]{1,3}(?:(?:\,[0-9]{3})*(?:\.[0-9][0-9]?)?|[0-9]*(?:[\.][0-9][0-9]?)?)$/;
//matches 1.000,23
var FLOAT_REGEXP_COMMA = /^[+-]?[0-9]{1,3}(?:|(?:\.[0-9]{3})*(?:\,[0-9][0-9]?)?|[0-9]*(?:[\,][0-9][0-9]?)?)$/;

//currency input field from from http://jsfiddle.net/odiseo/dj6mX/
String.prototype.splice = function(idx, rem, s) {
	return (this.slice(0, idx) + s + this.slice(idx + Math.abs(rem)));
};

angular.module('Perlenbilanz').directive('currencyInput', function() {
	return {
		require: 'ngModel',
		link: function(scope, elm, attrs, ctrl) {

			//filter key events
			$(elm).keydown(function(event) {
				var keyCode = ('which' in event) ? event.which : event.keyCode;
				if (event.ctrlKey && (keyCode === 67 || keyCode === 88 || keyCode === 86)) {
					return true;
				} else if ((keyCode >= 96 && keyCode <= 105) || keyCode === 109 || keyCode === 110) { /* numpad 0..9 DOM_VK_SUBTRACT(109,-) DOM_VK_DECIMAL(110,.) */
					return true;
				} else if (keyCode >= 188 && keyCode <= 190) { /* , . DOM_VK_COMMA(188,-) DOM_VK_PERIOD(190,.) */ 
					return true;
				} else if (keyCode === 173) { /* DOM_VK_HYPHEN_MINUS(173,-) */
					return true;
				} else if (keyCode >= 58) { /* DOM_VK_COLON(58,:) */
					return false;
				}
				return true;
			});
			ctrl.$parsers.unshift(function(viewValue) {
				var clean;
				if (FLOAT_REGEXP_COMMA.test(viewValue)) {
					clean = viewValue.replace(/\./g, '').replace(/,/g, '.');
					ctrl.$setValidity('float', true);
					return parseFloat(clean);
				} else if (FLOAT_REGEXP_DOT.test(viewValue)) {
					clean = viewValue.replace(/\,/g, '');
					ctrl.$setValidity('float', true);
					return parseFloat(clean);
				} else {
					ctrl.$setValidity('float', false);
					return undefined;
				}
			});
			// model -> view
			ctrl.$render = function(){
				// render float as string
				var result = '';

				if (typeof ctrl.$viewValue === 'number') {

					var value = ctrl.$viewValue;
					value = ''+Math.round(value*100)/100;

					var decimalSplit = value.split('.');
					var intPart = decimalSplit[0];
					var decPart = decimalSplit[1];

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
					}

					if (decPart === undefined) {
						decPart = '';
					} else {
						if (decPart.length === 1) {
							decPart = decPart + '0';
						}
						if (decPart.length > 2) {
							decPart = decPart.substr(0,2);
							}
						decPart = ',' + decPart;
					}
					result = intPart + decPart;
				}

				elm.val(result);
			};
		}
	};
});

