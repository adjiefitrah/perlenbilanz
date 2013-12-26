angular.module('Perlenbilanz').
	factory('mwstCalculator', function() {
		var mwstCalculator = {};
		mwstCalculator.update = function($scope, positions) {
			var bruttoTotal = 0;
			var mwstGroups = [];
			if (positions) {
				$.each(positions, function (i, position) {
					var brutto = position.brutto;
					if (typeof brutto === 'undefined' || brutto === null) {
						brutto = 0;
					}

					if (position.typ === 'Rabatt' && brutto > 0) {
						brutto = brutto * -1;
						position.brutto = brutto;
					}

					var stueck = position.stueck;
					if (typeof stueck === 'undefined' || stueck === null) {
						stueck = 1;
					}
					brutto = brutto * stueck;
					position.bruttoSum = brutto;
					
					bruttoTotal += brutto;
					
					var mwstProzent = 0;
					if (typeof position.mwstProzent === 'number') {
						mwstProzent = position.mwstProzent;
					} else if ((typeof position.mwstProzent === 'string' && position.mwstProzent === '' ) ||
							(typeof position.mwstProzent === 'object' && position.mwstProzent === null )) {
						//calculate mwst & netto with old mwstStatus
						mwstProzent = 'various';
						position.netto = brutto-position.mwst;
					} else {
						if (typeof position.mwstStatus !== 'undefined') {
							if (position.mwstStatus === true) {
								mwstProzent = 19;
							}
						}
					}

					//add netto to existing group
					var newGroup = true;
					$.each(mwstGroups, function (i, mwstGroup) {
						if (mwstGroup.mwstProzent === mwstProzent) {
							mwstGroup.brutto += brutto;
							if (mwstProzent === 'various') {
								mwstGroup.netto += position.netto;
								mwstGroup.mwst += position.mwst;
							}
							newGroup=false;
						}
					});
					//create new group
					if (newGroup) {
						if (mwstProzent === 'various') {
							mwstGroups.push({
								mwstProzent:mwstProzent,
								brutto:brutto,
								mwst:position.mwst,
								netto:position.netto});
						} else {
							mwstGroups.push({mwstProzent:mwstProzent,brutto:brutto});
						}
					}
				});
			}

			var mwstTotal = 0;
			$.each(mwstGroups, function (i, mwstGroup) {
				if (mwstGroup.mwstProzent === 'various') {
					//already calculated, just round
					mwstGroup.mwst = Math.round(mwstGroup.mwst*100)/100;
				} else {
					mwstGroup.mwst = Math.round((mwstGroup.brutto-(mwstGroup.brutto / (1+mwstGroup.mwstProzent/100)))*100)/100;
				}
				mwstTotal += mwstGroup.mwst;
			});

			$scope.bruttoTotal = Math.round(bruttoTotal*100)/100;
			$scope.mwstGroups = mwstGroups;
			$scope.nettoTotal = Math.round((bruttoTotal-mwstTotal)*100)/100;
		};
		mwstCalculator.updateBrutto = function(position) {
			var brutto = position.brutto;
			if (typeof brutto === 'undefined' || brutto === null) {
				brutto = 0;
			}
			if (typeof brutto === 'number') {
				var mwstProzent = 0;
				var netto = 0;
				var stueck = position.stueck;
				if (typeof stueck === 'undefined' || stueck === null) {
					stueck = 1;
				}
				brutto = brutto * stueck;
				if (typeof position.mwstProzent === 'number') {
					mwstProzent = position.mwstProzent;
					netto = brutto / (1+mwstProzent/100);
				} else if (typeof position.mwstProzent === 'string' && position.mwstProzent === '' ) {
					//calculate mwst & netto with old mwstStatus
					netto = brutto-position.mwst;
				} else {
					if (typeof position.mwstStatus !== 'undefined') {
						if (position.mwstStatus === true) {
							mwstProzent = 19;
						}
					}
					netto = brutto / (1+mwstProzent/100);
				}
				var mwst = brutto - netto;
				position.bruttoSum = brutto;
				position.netto = netto;
				position.mwst = mwst;
			}
		};
		mwstCalculator.updateMwSt = function(position) {
			var brutto = position.brutto;
			if (typeof brutto === 'undefined' || brutto === null) {
				brutto = 0;
			}
			if (typeof brutto === 'number') {
				var stueck = position.stueck;
				if (typeof stueck === 'undefined' || stueck === null) {
					stueck = 1;
				}
				brutto = brutto * stueck;
				var mwstProzent = 0;
				if (typeof position.mwstStatus !== 'undefined') {
					if (position.mwstStatus === true) {
						position.mwstProzent = 19;
					} else {
						position.mwstProzent = 0;
					}
					mwstProzent = position.mwstProzent;
				}
				if (typeof position.mwstProzent === 'number') {
					mwstProzent = position.mwstProzent;
				}
				var netto = brutto / (1+mwstProzent/100);
				var mwst = brutto - netto;
				position.bruttoSum = brutto;
				position.netto = netto;
				position.mwst = mwst;
			}
		};
		mwstCalculator.updateMwStProzent = function(position) {
			var brutto = position.brutto;
			if (typeof brutto === 'undefined' || brutto === null) {
				brutto = 0;
			}
			if (typeof brutto === 'number') {
				var stueck = position.stueck;
				if (typeof stueck === 'undefined' || stueck === null) {
					stueck = 1;
				}
				brutto = brutto * stueck;
				var mwstProzent = position.mwstProzent;
				if (mwstProzent === null) {
					if (position.mwstStatus === true) {
						mwstProzent = 19;
					} else {
						mwstProzent = 0;
					}
				}
				var netto = brutto / (1+mwstProzent/100);
				var mwst = brutto - netto;
				position.bruttoSum = brutto;
				position.netto = netto;
				position.mwst = mwst;
			}
		};
		return mwstCalculator;
	});
