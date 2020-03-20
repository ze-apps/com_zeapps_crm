app.controller("ComZeappsCrmOrderConfigCtrl", ["$scope", "zeHttp", "menu",
	function ($scope, zhttp, menu) {

        menu("com_ze_apps_config", "com_ze_apps_orders");

		$scope.format = "";
		$scope.numerotation = 1;
		$scope.textBefore = "";
		$scope.textAfter = "";

		$scope.success = success;
		$scope.test = test;

		zhttp.config.order.get.format().then(function(response){
			if(response.data && response.data != "false"){
				$scope.format = response.data.value;
			}
		});

		zhttp.config.order.get.numerotation().then(function(response){
			if(response.data && response.data != "false"){
                $scope.numerotation = parseInt(angular.fromJson(response.data));
			}
		});

		zhttp.config.get("crm_order_text_before_lines").then(function (response) {
			if (response.data && response.data != "false") {
				if (response.data && response.data.value) {
					$scope.textBefore = response.data.value;
				}
			}
		});

		zhttp.config.get("crm_order_text_after_lines").then(function (response) {
			if (response.data && response.data != "false") {
				if (response.data && response.data.value) {
					$scope.textAfter = response.data.value;
				}
			}
		});

		function success(){

			var data = {};

			data[0] = {
				id: "crm_order_format",
				value: $scope.format
			};
			data[1] = {
				id: "crm_order_numerotation",
				value: $scope.numerotation
			};
			data[2] = {
				id: "crm_order_text_before_lines",
				value: $scope.textBefore
			};
			data[3] = {
				id: "crm_order_text_after_lines",
				value: $scope.textAfter
			};

			var formatted_data = angular.toJson(data);
			zhttp.config.save(formatted_data);

		}

		function test(){
			var data = {};

			data["format"] = $scope.format;
			data["numerotation"] = $scope.numerotation;

			var formatted_data = angular.toJson(data);
			zhttp.crm.order.test(formatted_data).then(function(response){
				if(response.data && response.data != false){
					$scope.result = angular.fromJson(response.data);
				}
			});
		}

	}]);