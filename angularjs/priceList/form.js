app.controller("ComZeappsCrmPriceListFormCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp",
	function ($scope, $routeParams, $rootScope, zhttp) {

        $scope.listOuiNon = [{id:0, label:__t("No")}, {id:1, label:__t("Yes")}];

		zhttp.crm.price_list.get_price_list_type().then(function(response){
			if(response.data && response.data != "false"){
				$scope.price_list_types = response.data;
			}
		});


		$scope.changeParDefaut = function () {
			if ($scope.form.default == 1) {
				$scope.form.type_pricelist = 0 ;
			}
		};

	}]);