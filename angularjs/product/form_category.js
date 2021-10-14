app.controller("ComZeappsCrmProductFormCategoryCtrl", ["$scope", "$routeParams", "$location", "zeHttp", "menu",
	function ($scope, $routeParams, $location, zhttp, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.currentBranch = {};
		$scope.tree = {
			branches: []
		};
        $scope.tree_select = [] ;
		$scope.form = [];
		$scope.error = "";

		$scope.update = update;
		$scope.success = success;
		$scope.cancel = cancel;


		if ($routeParams.id && $routeParams.id > 0) {
			loadCtxtEdit();
		}

		if($routeParams.id_parent && $routeParams.id_parent >= 0){
			loadCtxtNew();
		}

		function update(branch){
			$scope.currentBranch = branch;
            $scope.form.id_parent = branch.id;
		}

        function strRepeat(car, nbRepeat) {
            var strReturn = "" ;
            for(var i = 1 ; i <= nbRepeat ; i++) {
                strReturn += car ;
            }
            return strReturn ;
        }

        function updateTreeSelect(niveau, branchesContent) {
            if (niveau == 0) {
                $scope.tree_select = [] ;
            }

            var id_categorieEdit = -9999 ;

            if ($routeParams.id != 0) {
                id_categorieEdit = $routeParams.id ;
            }

            var tree = [] ;


            angular.forEach(branchesContent, function(branche){
            	if (branche.id != id_categorieEdit) {
                    tree.push({id: branche.id, name: strRepeat("&nbsp;", 5 * niveau) + branche.name});

                    if (branche.branches) {
                        var sousBranche = updateTreeSelect(niveau + 1, branche.branches);
                        tree = tree.concat(sousBranche);
                    }
                }
            });


            if (niveau == 0) {
                $scope.tree_select = tree ;
            }

            return tree ;

        }

		function loadCtxtNew(){
			zhttp.crm.category.tree().then(function (response) {
				if (response.status == 200) {
					$scope.tree.branches = response.data;
                    updateTreeSelect(0, $scope.tree.branches);

					zhttp.crm.category.openTree($scope.tree, $routeParams.id_parent);
					zhttp.crm.category.get($routeParams.id_parent).then(function (response) {
						if (response.status == 200) {
                            $scope.currentBranch = response.data;
						}
					});
				}
			});
		}


		function loadCtxtEdit(){
			zhttp.crm.category.tree().then(function (response) {
				if (response.status == 200) {
					$scope.tree.branches = response.data;
                    updateTreeSelect(0, $scope.tree.branches);

					zhttp.crm.category.get($routeParams.id).then(function (response) {
						if (response.status == 200) {
							$scope.form = response.data;

							zhttp.crm.category.get($scope.form.id_parent).then(function (response) {
								if (response.status == 200) {
                                    $scope.currentBranch = response.data;
                                    if ($scope.currentBranch) {
										zhttp.crm.category.openTree($scope.tree, $scope.currentBranch.data.id);
									} else {
										zhttp.crm.category.openTree($scope.tree, 0);
									}
								}
							});
						}
					});
				}
			});
		}

		function success() {
			var data = {};

			if ($routeParams.id != 0) {
				data.id = $routeParams.id;
			}

			data.id_parent = $scope.form.id_parent;
			data.name = $scope.form.name;

			var formatted_data = angular.toJson(data);

			zhttp.crm.category.save(formatted_data).then(function (response) {
				if(typeof(response.data.error) === "undefined") {
					// pour que la page puisse être redirigé
					if ($routeParams.url_retour) {
						$location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
					} else {
						$location.path("/ng/com_zeapps_crm/product/category/" + response.data);
					}
				}
				else{
					$scope.error = response.data.error;
				}
			});
		}

		function cancel() {
			if ($routeParams.url_retour) {
				$location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp,"/"));
			} else {
				$location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_parent);
			}
		}

	}]);