app.controller("ComZeappsCrmOrderListsPartialCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts",
	function ($scope, $location, $rootScope, zhttp, $timeout, toasts) {

		if(!$rootScope.orders)
			$rootScope.orders = {};
		$scope.id_company = 0;
		$scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'numerotation LIKE',
                    type: 'text',
                    label: __t("Number")
                },
                {
                    format: 'input',
                    field: 'libelle LIKE',
                    type: 'text',
                    label: __t("Label")
                },
                {
                    format: 'input',
                    field: 'name_company LIKE',
                    type: 'text',
                    label: __t("Compagny")
                },
                {
                    format: 'input',
                    field: 'name_contact LIKE',
                    type: 'text',
                    label: __t("Contact")
                },
                {
                    format: 'select',
                    field: 'finalized',
                    type: 'text',
                    label: __t("Status"),
                    options: [{id:0,label:__t("Opened")}, {id:1,label:__t("Closed")}]
                }



            ],
            secondaries: [
                {
                    format: 'input',
                    field: 'date_creation >=',
                    type: 'date',
                    label: __t("Creation date: Start"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_creation <=',
                    type: 'date',
                    label: __t("End"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_limit >=',
                    type: 'date',
                    label: __t("Deadline: Start"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_limit <=',
                    type: 'date',
                    label: __t("End"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ht >',
                    type: 'text',
                    label: __t("Total HT: Greater than"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ht <',
                    type: 'text',
                    label: __t("Less than"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ttc >',
                    type: 'text',
                    label: __t("Total including tax: Greater than"),
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ttc <',
                    type: 'text',
                    label: __t("Less than"),
                    size: 3
                },
                {
                    format: 'select',
                    field: 'id_account_family',
                    type: 'text',
                    label: __t("Type of account"),
                    options: [],
                    size: 3
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templateOrder = '/com_zeapps_crm/orders/form_modal';

        // charge les familles de compte pour le filtre
        zhttp.contact.company.context().then(function (response) {
            if (response.status == 200) {
                $scope.filters.secondaries[8].options = response.data.account_families ;
            }
        });

        var src = "orders";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.goTo = goTo;
		$scope.add = add;
		$scope.edit = edit;
		$scope.delete = del;

		$scope.$on("comZeappsContact_dataEntrepriseHook", function(event, data) {
			if ($scope.id_company !== data.id_company){
				$scope.id_company = data.id_company;
				src = "company";
                src_id = data.id_company;

                loadList(true) ;
			}
		});
		$scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

		$scope.$on("comZeappsContact_dataContactHook", function(event, data) {
			if ($scope.id_contact !== data.id_contact){
				$scope.id_contact = data.id_contact;
				$scope.id_company = data.id_company;
                src = "contact";
                src_id = data.id_contact;

                loadList(true) ;
			}
		});
		$scope.$emit("comZeappsContact_triggerContactHook", {});

        $timeout(function(){ // Making sure the default call is only triggered after the potential broadcast from a hook
        	if(src_id === 0) {
                loadList(true);
            }
        }, 0);







        function loadList(context) {
            context = context || "";
            var offset = ($scope.page - 1) * $scope.pageSize;



            var filtre = {} ;
            angular.forEach($scope.filter_model, function (value, key) {
                filtre[key] = value ;
            });



            // convet date JS to YYYY-MM-DD
            var arrayFieldDate = ["date_creation >=", "date_creation <=", "date_limit >=", "date_limit <="] ;
            for (var i_arrayFieldDate = 0 ; i_arrayFieldDate < arrayFieldDate.length ; i_arrayFieldDate++) {
                if (filtre[arrayFieldDate[i_arrayFieldDate]] != undefined) {
                    filtre[arrayFieldDate[i_arrayFieldDate]] = filtre[arrayFieldDate[i_arrayFieldDate]].getFullYear() + "-" + (filtre[arrayFieldDate[i_arrayFieldDate]].getMonth() + 1) + "-" + filtre[arrayFieldDate[i_arrayFieldDate]].getDate();
                }
            }


            // convert , to . for numeric
            var arrayFieldNumeric = ["total_ht >", "total_ht <", "total_ttc >", "total_ttc <"] ;
            for (var i_arrayFieldNumeric = 0 ; i_arrayFieldNumeric < arrayFieldNumeric.length ; i_arrayFieldNumeric++) {
                if (filtre[arrayFieldNumeric[i_arrayFieldNumeric]] != undefined) {
                    filtre[arrayFieldNumeric[i_arrayFieldNumeric]] = filtre[arrayFieldNumeric[i_arrayFieldNumeric]].replace(",", ".").replace(" ", "") ;
                }
            }

            var formatted_filters = angular.toJson(filtre);
            zhttp.crm.order.get_all(src_id, src, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.orders = response.data.orders;

                    for (var i = 0; i < $scope.orders.length; i++) {
                        $scope.orders[i].date_creation = $scope.orders[i].date_creation !== "0000-00-00 00:00:00" ? new Date($scope.orders[i].date_creation) : 0;
                        $scope.orders[i].date_limit = $scope.orders[i].date_limit !== "0000-00-00 00:00:00" ? new Date($scope.orders[i].date_limit) : 0;
                        $scope.orders[i].global_discount = parseFloat($scope.orders[i].global_discount);
                    }

                    $scope.total = response.data.total;

                    $rootScope.orders.ids = response.data.ids;
                    $rootScope.orders.src_id = src_id;
                    $rootScope.orders.src = src;
                }
            });
        }

        function goTo(id){
            $location.url('/ng/com_zeapps_crm/order/'+id);
        }

        function add(order) {
            var data = order;

            if(data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_creation = 0;
            }

            if(data.date_limit) {
                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_limit = 0;
            }

            var formatted_data = angular.toJson(data);
            zhttp.crm.order.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    $rootScope.orders.ids.unshift(response.data);
                    $location.url("/ng/com_zeapps_crm/order/" + response.data);
                }
            });
        }

        function edit(order){
            var data = order;

            if(data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_creation = 0;
            }

            if(data.date_limit) {
                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_limit = 0;
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.order.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    toasts('success', __t("The order information has been updated"));
                }
                else{
                    toasts('danger', __t("There was an error updating the order information"));
                }
            });
        }

		function del(order){
			zhttp.crm.order.del(order.id).then(function(response){
				if(response.data && response.data != "false"){
                    $scope.orders.splice($scope.orders.indexOf(order), 1);
                    $rootScope.orders.ids.splice($rootScope.orders.ids.indexOf(order.id), 1);
				}
			});
		}


	}]);