angular.module("championSelect").controller("championSelectCtrl", ['$scope', function($scope) {
	var data = championsObj.data;
	var arr = [];
	var newObj = {data: {}};
	  
	/* Sorts the champion list */
	for (let k in data) {
		if (data.hasOwnProperty(k)) {
	  	arr.push(k);
	  }
	}

	arr.sort();
	arr.forEach(n => {
		newObj.data[n] = data[n];
	});

	/* Sets champions to sorted list of champions */
	$scope.champions = newObj.data;
}]);