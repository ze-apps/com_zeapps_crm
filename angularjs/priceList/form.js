app.controller("ComZeappsCrmPriceListFormCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp",
	function ($scope, $routeParams, $rootScope, zhttp) {

        $scope.listOuiNon = [{id:0, label:"Non"}, {id:1, label:"Oui"}];

		zhttp.crm.price_list.get_price_list_type().then(function(response){
			if(response.data && response.data != "false"){
				$scope.price_list_types = response.data;
			}
		});


		/*function Initform(){
			if($scope.form.id === undefined) {
                if($routeParams.id_company !== undefined && $routeParams.id_company !== 0){
                    zhttp.contact.company.get($routeParams.id_company).then(function(response){
                        if(response.data && response.data != "false"){
                            loadCompany(response.data.company);
                        }
                    });
                }
                if($routeParams.id_contact !== undefined && $routeParams.id_contact !== 0){
                    zhttp.contact.contact.get($routeParams.id_contact).then(function(response){
                        if(response.data && response.data != "false"){
                            loadContact(response.data.contact);
                        }
                    });
                }
            }
		}
        Initform();*/

	}]);