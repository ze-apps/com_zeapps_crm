app.controller("ComZeappsCrmStockViewCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "menu",
	function ($scope, $location, $rootScope, zhttp, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_stock");


        $scope.currentBranch = {};
        $scope.tree = {
            branches: []
        };


        $scope.loadList = loadList;
        $scope.update = update;

        var showSubCats = false;
        $scope.isSubCatOpen = function () {
            return showSubCats;
        };
        $scope.openSubCats = function () {
            showSubCats = true;
        };
        $scope.closeSubCats = function () {
            showSubCats = false;
        };

        $scope.export = function () {
            var id = $scope.currentBranch ? $scope.currentBranch.id : 0;
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            $rootScope.current_warehouse = $scope.filter_model.id_warehouse;

            zhttp.crm.product_stock.export(id, $scope.pageSize, offset, "", formatted_filters).then(function(response){
                if(response.data && response.data != "false"){
                    if (response.status == 200 && response.data) {
                        window.document.location.href = zhttp.crm.product_stock.get_export(response.data.link);
                    }
                }
            });
        };




        function getTree() {
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    $scope.currentBranch = $scope.tree.branches[0];
                    loadList();
                }
            });
        }
        getTree();


        function update(branch) {
            $scope.currentBranch = branch;
            loadList();
        }




		$rootScope.current_warehouse = $rootScope.current_warehouse || $rootScope.user.id_warehouse;

        $scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'ref LIKE',
                    type: 'text',
                    label: 'Référence'
                },
                {
                    format: 'input',
                    field: 'name LIKE',
                    type: 'text',
                    label: 'Libellé'
                },
                {
                    format: 'select',
                    field: 'id_warehouse',
                    type: 'text',
                    label: 'Entrepôt',
                    options: []
                },
                {
                    format: 'input',
                    field: 'date_stock',
                    type: 'date',
                    label: 'Date analyse',
                    size: 3
                }
            ]
        };

        $scope.filter_model = {
        	'id_warehouse': $rootScope.current_warehouse
		};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templateStock = '/com_zeapps_crm/stock/form_modal';







        zhttp.crm.warehouse.get_all().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.filters.main[2].options = response.data ;
            }

            loadList(true);
        });









		$scope.loadList = loadList;
		$scope.goTo = goTo;

        function loadList(context){
            context = context || "";
            var id = $scope.currentBranch ? $scope.currentBranch.id : 0;
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            $rootScope.current_warehouse = $scope.filter_model.id_warehouse;

            zhttp.crm.product_stock.get_all(id, $scope.pageSize, offset, context, formatted_filters).then(function(response){
                if(response.data && response.data != "false"){
                    $scope.product_stocks = response.data.product_stocks;
                    angular.forEach($scope.product_stocks, function(product_stock){
                        product_stock.value_ht = parseFloat(product_stock.value_ht);
                        calcTimeLeft(product_stock);
                    });

                    $scope.total = response.data.total;
                }
            });
        }

        function goTo(id){
        	$location.url("/ng/com_zeapps_crm/stock/" + id);
		}

		function calcTimeLeft(product_stock){
			if(product_stock.avg > 0) {
				var timeleft = product_stock.qty / product_stock.avg;

				if(timeleft > 0) {
					product_stock.timeleft = moment().to(moment().add(timeleft, "days"));
					product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					product_stock.classRupture = "text-success";
				}
				else{
					product_stock.timeleft = "En rupture";
					product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
					product_stock.classRupture = "text-danger";
				}

				if($scope.filter_model.id_warehouse > 0) {
					product_stock.timeResupply = moment().to(moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit));
					product_stock.dateResupply = moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit).format("DD/MM/YYYY");
					product_stock.classResupply = moment().isBefore(moment().add(timeleft, "days").subtract(product_stock.resupply_delay, product_stock.resupply_unit), "day") ? "text-success" : "text-danger";
				}
			}
			else{
				product_stock.timeleft = "-";
				product_stock.dateRupture = "";
				product_stock.timeResupply = "-";
				product_stock.dateResupply = "";
				product_stock.classRupture = "text-info";
			}
		}
	}]);