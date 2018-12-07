app.controller("ComZeappsCrmProductFormCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "menu", "toasts",
	function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, menu, toasts) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.currentBranch = {};
        $scope.tree = {
            branches: []
        };

        $scope.tree_select = [] ;




        // définit les types de produits
        $scope.type_products = [] ;
        $scope.type_products.push({id:"product", label:"Produit"}) ;
        $scope.type_products.push({id:"service", label:"Service"}) ;
        $scope.type_products.push({id:"pack", label:"Pack"}) ;


		$scope.form = [];
		$scope.form.extra = {};
		$scope.error = "";
		$scope.max_length = {
			desc_short: 140,
			desc_long: 1000
		};

        $scope.form.sublines = [] ;

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_contact/accounting_numbers/form_modal';
        $scope.accountingNumberFields = [
            {label:'Numero',key:'number'},
            {label:'Libelle',key:'label'},
            {label:'Type',key:'type_label'}
        ];

		$scope.update = update;
		$scope.loadProductStock = loadProductStock;
		$scope.removeProductStock = removeProductStock;
        $scope.loadAccountingNumber = loadAccountingNumber;
		$scope.updateTaxe = updateTaxe;
		$scope.updatePrice = updatePrice;
		$scope.descState = descState;
		$scope.success = success;
		$scope.cancel = cancel;

		zhttp.config.product.get.attr().then(function(response){
			if(response.data && response.data != "false"){
				$scope.attributes = angular.fromJson(response.data.value);
			}
		});

		if ($routeParams.id && $routeParams.id > 0) {
			loadCtxtEdit();
		}

		if ($routeParams.category) {
			loadCtxtNew();
		}

		function update(branch){
            $scope.currentBranch = branch;
            $scope.form.id_cat = branch.id;
		}


		function updateTreeSelect(niveau, branchesContent) {
			if (niveau == 0) {
                $scope.tree_select = [] ;
            }

            var tree = [] ;


            angular.forEach(branchesContent, function(branche){
                tree.push({id:branche.id, name: strRepeat("&nbsp;", 5 * niveau) + branche.name});

                if (branche.branches) {
                    var sousBranche = updateTreeSelect(niveau+1, branche.branches);
                    tree = tree.concat(sousBranche);
				}
            });


            if (niveau == 0) {
                $scope.tree_select = tree ;
            }

            return tree ;

		}

		function strRepeat(car, nbRepeat) {
			var strReturn = "" ;
			for(var i = 1 ; i <= nbRepeat ; i++) {
                strReturn += car ;
			}
			return strReturn ;
		}


		function loadCtxtNew(){
			zhttp.crm.category.tree().then(function (response) {
				if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    updateTreeSelect(0, $scope.tree.branches);

					zhttp.crm.category.openTree($scope.tree, $routeParams.category);
					zhttp.crm.category.get($routeParams.category).then(function (response) {
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

					zhttp.crm.product.get($routeParams.id).then(function (response) {
						if (response.status == 200) {
							$scope.form = response.data;

                            $scope.form.sublines = [] ;


							$scope.form.price_ht = parseFloat($scope.form.price_ht);
							$scope.form.value_taxe = parseFloat($scope.form.value_taxe);
                            updatePrice('ttc');
							if($scope.form.extra) {
                                $scope.form.extra = angular.fromJson($scope.form.extra);
                            }
                            else{
								$scope.form.extra = {};
							}
							zhttp.crm.category.openTree($scope.tree, $scope.form.id_cat);
							zhttp.crm.category.get($scope.form.id_cat).then(function (response) {
								if (response.status == 200) {
                                    $scope.currentBranch = response.data;
								}
							});
							zhttp.crm.product_stock.get($scope.form.id_stock).then(function (response) {
								if (response.status == 200) {
									var stock = response.data.product_stock;
									$scope.form.name_stock = stock.ref ? stock.ref + " - " + stock.label : stock.label;
								}
							});
						}
					});
				}
			});
		}

		function loadProductStock() {
			zeapps_modal.loadModule("com_zeapps_crm", "search_product_stock", {}, function(objReturn) {
				if (objReturn) {
					$scope.form.id_stock = objReturn.id_stock;
					$scope.form.name_stock = objReturn.ref ? objReturn.ref + " - " + objReturn.label : objReturn.label;
				} else {
					$scope.form.id_stock = 0;
					$scope.form.name_stock = "";
				}
			});
		}

		function removeProductStock() {
			$scope.form.id_stock = 0;
			$scope.form.name_stock = "";
		}

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.form.accounting_number = accounting_number.number;
            } else {
                $scope.form.accounting_number = "";
            }
        }

		function updateTaxe(){
			angular.forEach($rootScope.taxes, function(taxe){
				if(taxe.id == $scope.form.id_taxe){
					$scope.form.value_taxe = taxe.value;
				}
			});
		}

		function updatePrice(price) {
            $scope.form.price_ht = parseFloat($scope.form.price_ht) ;
            $scope.form.price_ttc = parseFloat($scope.form.price_ttc) ;
			if ($scope.form.type_product == "pack") {
                var montantHT = 0 ;
                var montantTTC = 0 ;
                for (var i = 0; i < $scope.form.sublines.length; i++) {
                    var montantLigneHT = $scope.form.sublines[i].price_unit * $scope.form.sublines[i].qty ;

                    montantHT += montantLigneHT ;
                    montantTTC += montantLigneHT ;

                    var tauxTva = 0 ;
                    angular.forEach($rootScope.taxes, function(taxe){
                        if(taxe.id == $scope.form.sublines[i].id_taxe){
                            tauxTva = taxe.value;

                            montantTTC += montantLigneHT * tauxTva / 100 ;
                        }
                    });
                }


                if ($scope.form.update_price_from_subline) {
                    $scope.form.price_ht = round2(parseFloat(montantHT));
                    $scope.form.price_ttc = round2(parseFloat(montantTTC));
                } else {
                    if (price === "ht") {
                        console.log(montantTTC);
                        console.log($scope.form.price_ttc);
                        var coef = parseFloat($scope.form.price_ttc) / montantTTC ;

                        $scope.form.price_ht = round2(montantHT * coef);
                    } else if (price === "ttc") {
                        var coef = parseFloat($scope.form.price_ht) / montantHT ;
                        $scope.form.price_ttc = round2(montantTTC * coef);
                    }
				}
            } else {
                var value_taxe = $scope.form.value_taxe;
                if (!value_taxe) {
                    value_taxe = 0;
                } else {
                    value_taxe = parseFloat(value_taxe);
                }

                if (price === "ht") {
                    $scope.form.price_ht = round2(parseFloat($scope.form.price_ttc) / (1 + value_taxe / 100));
                } else if (price === "ttc") {
                    $scope.form.price_ttc = round2(parseFloat($scope.form.price_ht) * (1 + value_taxe / 100));
                }

			}
		}

		function descState(current, max){
			if(current > max)
				return "text-danger";
			else if(current > Math.ceil(max*0.9) && current < max)
				return "text-warning";
			else
				return "text-success";

		}

		function success() {
			var data = {};

			if ($routeParams.id != 0) {
				data.id = $routeParams.id;
			}

            if ($routeParams.category) {
                data.id_cat = $routeParams.category;
            } else {
                data.id_cat = 0;
			}

			data.ref = $scope.form.ref;
			data.name = $scope.form.name;
			data.id_cat = $scope.form.id_cat;
			data.id_stock = $scope.form.id_stock;
			data.description = $scope.form.description;
			data.price_ht = $scope.form.price_ht;
			data.price_ttc = $scope.form.price_ttc;
			data.id_taxe = $scope.form.id_taxe;
			data.value_taxe = $scope.form.value_taxe;
			data.accounting_number = $scope.form.accounting_number;
			data.extra = angular.toJson($scope.form.extra);

			var formatted_data = angular.toJson(data);

			zhttp.crm.product.save(formatted_data).then(function (response) {
				if(typeof(response.data.error) === "undefined") {
					// pour que la page puisse être redirigé
					if ($routeParams.url_retour) {
						$location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
					} else {
						$location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
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
				$location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
			}
		}



        $scope.addLine = function () {
            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function (objReturn) {
                if (objReturn) {
                    if (objReturn.type_product != "pack") {
                        var line = {
                            type: "product",
                            id_product: objReturn.id,
                            ref: objReturn.ref,
                            designation_title: objReturn.name,
                            designation_desc: objReturn.description,
                            qty: 1,
                            discount: 0.00,
                            price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
                            id_taxe: parseFloat(objReturn.id_taxe),
                            value_taxe: parseFloat(objReturn.value_taxe),
                            accounting_number: parseFloat(objReturn.accounting_number),
                            sort: $scope.form.sublines.length + 1,
                        };
                        $scope.form.sublines.push(line);

                        updatePrice();
                    } else {
                        toasts("danger", "Les packs ne peuvent pas être ajoutés");
                    }
                }
            });
        };

        $scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;

        function keyEventaddFromCode($event) {
            if ($event.which === 13) {
                addFromCode();
                setFocus($event.currentTarget);
            } else if ($event.which === 9) {
                addFromCode();
                setFocus($event.currentTarget);
            }
        }

        function setFocus(element) {
            setTimeout(function () {
                jQuery(element).focus();
            }, 500);
        }

        function addFromCode() {
            if ($scope.codeProduct !== "") {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        if (response.data.type_product != "pack") {
                            var line = {
                                type: "product",
                                id_product: response.data.id,
                                ref: response.data.ref,
                                designation_title: response.data.name,
                                designation_desc: response.data.description,
                                qty: 1,
                                discount: 0.00,
                                price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                                id_taxe: parseFloat(response.data.id_taxe),
                                value_taxe: parseFloat(response.data.value_taxe),
                                accounting_number: parseFloat(response.data.accounting_number),
                                sort: $scope.form.sublines.length + 1
                            };
                            $scope.form.sublines.push(line);

                            updatePrice();

                            $scope.codeProduct = "";

                        } else {
                            toasts("danger", "Les packs ne peuvent pas être ajoutés");
                        }
                    } else {
                        toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de données.");
					}
                });
            }
        }

        $scope.deleteLine = function (line) {
            if ($scope.form.sublines.indexOf(line) > -1) {
                for (var i = 0; i < $scope.form.sublines.length; i++) {
                    if ($scope.form.sublines[i].sort > line.sort) {
                        $scope.form.sublines[i].sort--;
                    }
                }

                $scope.form.sublines.splice($scope.form.sublines.indexOf(line), 1);

                updatePrice();
            }
        };

        $scope.updatePriceSubLine = function () {
            updatePrice();
        };


        function round2(num) {
            return +(Math.round(num + "e+2")  + "e-2");
        }
	}]);