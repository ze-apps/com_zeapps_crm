app.controller("ComZeappsCrmModelEmailConfigCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "menu", "$http",
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, menu, $http) {

		menu("com_ze_apps_config", "com_zeapps_crm_config_model_email");

		$scope.model_emails = [] ;

		$scope.edit = edit;
		$scope.delete = del;

		var loadList = function () {
			zhttp.crm.model_email.get_all().then(function(response){
				if(response.data && response.data != "false"){
					$scope.model_emails = response.data;
				}
			});
		}
		loadList();

		function edit(model_email) {
			$location.url("/ng/com_zeapps_crm/model_email/edit/" + model_email.id);
		}

		function del(model_email) {
			zhttp.crm.model_email.del(model_email.id).then(function (response) {
				if (response.data && response.data != "false") {
					$scope.model_emails.splice($scope.model_emails.indexOf(model_email), 1);
				}
			});
		}


		$scope.dupliquer = function (id) {
			$http.post('/com_zeapps_crm/model_email/duplicate/' + id).then(function (response) {
			    loadList();
			});
		};
		
	}]);