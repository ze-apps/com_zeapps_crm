app.controller("ComZeappsCrmStockViewCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "menu", "toasts",
	function ($scope, $location, $rootScope, zhttp, menu, toasts) {

        menu("com_ze_apps_sales", "com_zeapps_crm_stock");


        $scope.currentBranch = {};
        $scope.tree = {
            branches: []
        };

        $scope.modeInventaire = false ;
        $scope.showSaveInventaire = false ;


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

        $scope.activerInventaire = function () {
            if ($scope.filter_model.id_warehouse && $scope.filter_model.date_stock) {
                $scope.modeInventaire = true ;;
            } else {
                toasts("danger", __t("To activate inventory mode, you must define a warehouse and a date in the filters"));
            }
        };

        $scope.desactiverInventaire = function () {
            $scope.modeInventaire = false ;
        };

        $scope.enregistrer_inventaire = function () {
           angular.forEach($scope.product_stocks, function (product_stock) {
                if (convertFloat(product_stock.qty_inventaire) != convertFloat(product_stock.qty)) {
                    var y = $scope.filter_model.date_stock.getFullYear();
                    var M = $scope.filter_model.date_stock.getMonth() + 1;
                    var d = $scope.filter_model.date_stock.getDate();

                    var qtyUpdate = convertFloat(product_stock.qty_inventaire) - convertFloat(product_stock.qty) ;


                    if (qtyUpdate != 0) {
                        var stock_mvt = {};
                        stock_mvt.label = __t("Inventory update: Theoretical quantity") + " (" + convertFloat(product_stock.qty) + "), " + __t("Qty read") + " (" + convertFloat(product_stock.qty_inventaire) + ")";
                        stock_mvt.qty = qtyUpdate;
                        stock_mvt.id_warehouse = $scope.filter_model.id_warehouse;
                        stock_mvt.id_product = product_stock.id;
                        stock_mvt.date_mvt_field = y + "-" + M + "-" + d;

                        var formatted_data = angular.toJson(stock_mvt);

                        zhttp.crm.product_stock.add_mvt(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {

                            }
                        });
                    }

                    product_stock.qty = product_stock.qty_inventaire ;
                }
            });

            $scope.showSaveInventaire = false ;
        };

        $scope.keyEventInventaire = function () {
            var afficheSaveInventaire = false ;
            angular.forEach($scope.product_stocks, function (product_stock) {
                if (convertFloat(product_stock.qty_inventaire) != convertFloat(product_stock.qty)) {
                    afficheSaveInventaire = true ;
                }
            });

            if (afficheSaveInventaire) {
                $scope.showSaveInventaire = true ;
            } else {
                $scope.showSaveInventaire = false ;
            }
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


        if (!$rootScope.current_warehouse && $rootScope.user && $rootScope.user.id_warehouse) {
            $rootScope.current_warehouse = $rootScope.user.id_warehouse;
        } else if (!$rootScope.current_warehouse) {
            $rootScope.current_warehouse = 1;
        }

        $scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'ref LIKE',
                    type: 'text',
                    label: __t("Reference")
                },
                {
                    format: 'input',
                    field: 'name LIKE',
                    type: 'text',
                    label: __t("Label")
                },
                {
                    format: 'select',
                    field: 'id_warehouse',
                    type: 'text',
                    label: __t("Warehouse"),
                    options: []
                },
                {
                    format: 'input',
                    field: 'date_stock',
                    type: 'date',
                    label: __t("Analysis date"),
                    size: 3
                }
            ]
        };

        $scope.filter_model = {
        	'id_warehouse': $rootScope.current_warehouse+""
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
            if ($scope.filter_model) {
                $scope.showSaveInventaire = false ;

                context = context || "";
                let id = $scope.currentBranch ? $scope.currentBranch.id : 0;
                let offset = ($scope.page - 1) * $scope.pageSize;
                let formatted_filters = angular.toJson($scope.filter_model);

                // insert la date au bon format
                if ($scope.filter_model && $scope.filter_model.date_stock) {
                    let anneeFiltre = $scope.filter_model.date_stock.getFullYear();
                    let moisFiltre = $scope.filter_model.date_stock.getMonth() + 1;
                    let joursFiltre = $scope.filter_model.date_stock.getDay();

                    formatted_filters = JSON.parse(formatted_filters);
                    formatted_filters.date_stock = anneeFiltre + "-" + moisFiltre + "-" + joursFiltre;
                    formatted_filters = JSON.stringify(formatted_filters);
                }


                // désactive le mode inventaire si l'entrepot et la date ne sont pas définis
                if (!$scope.filter_model.id_warehouse || !$scope.filter_model.date_stock) {
                    $scope.modeInventaire = false ;
                }

                $rootScope.current_warehouse = $scope.filter_model.id_warehouse;


                zhttp.crm.product_stock.get_all(id, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                    if (response.data && response.data != "false") {
                        $scope.product_stocks = response.data.product_stocks;
                        angular.forEach($scope.product_stocks, function (product_stock) {
                            product_stock.qty_inventaire = product_stock.qty ;
                            product_stock.value_ht = parseFloat(product_stock.value_ht);
                            calcTimeLeft(product_stock);
                        });

                        $scope.total = response.data.total;
                    }
                });
            }
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

        function convertFloat(value) {
            if (value && typeof value == 'string') {
                if (!value.endsWith(',') && !value.endsWith('.')) {
                    value = value.replace(",", ".");
                    value = value * 1;
                }
            }

            return value;
        }
	}]);