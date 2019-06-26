app.controller("ComZeappsCrmProductFormCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "menu", "toasts",
    function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, menu, toasts) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.currentBranch = {};
        $scope.tree = {
            branches: []
        };

        $scope.tree_select = [];


        $scope.navigationState = "body";

        $scope.setTab = function (tab) {
            $scope.navigationState = tab;
        };


        // charge la liste des grilles de prix
        $scope.price_lists = false;
        zhttp.crm.price_list.get_all().then(function (response) {
            if (response.status == 200) {
                $scope.price_lists = response.data;
            }
        });


        // définit les types de produits
        $scope.type_products = [];
        $scope.type_products.push({id: "product", label: "Produit"});
        $scope.type_products.push({id: "service", label: "Service"});
        $scope.type_products.push({id: "pack", label: "Pack"});
        $scope.type_products.push({id: "virtual", label: "Virtuel"});


        $scope.form = {};
        //$scope.form.extra = {};
        $scope.error = "";
        $scope.max_length = {
            desc_short: 140,
            desc_long: 1000
        };

        $scope.form.sublines = [];

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_contact/accounting_numbers/form_modal';
        $scope.accountingNumberFields = [
            {label: 'Numero', key: 'number'},
            {label: 'Libelle', key: 'label'},
            {label: 'Type', key: 'type_label'}
        ];

        $scope.update = update;
        $scope.loadProductStock = loadProductStock;
        $scope.removeProductStock = removeProductStock;
        $scope.openModalAccountingNumber = openModalAccountingNumber;
        $scope.loadAccountingNumber = loadAccountingNumber;
        $scope.updateTaxe = updateTaxe;
        $scope.updatePrice = updatePrice;
        $scope.descState = descState;
        $scope.success = success;
        $scope.cancel = cancel;
        $scope.addLine = addLine;
        $scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;
        $scope.deleteLine = deleteLine;
        $scope.updatePriceSubLine = updatePriceSubLine;







        $scope.supplierPurchases = [] ;

        $scope.supplierPurchases.push({
            date_purchase: '25/09/2018',
            supplier: 'Offset 5',
            quantity: 4000,
            price_ht: 12000,
            value_taxe: 20,
            price_ttc: 14400,
        }) ;

        $scope.supplierPurchases.push({
            date_purchase: '01/02/2016',
            supplier: 'Ulzama',
            quantity: 10000,
            price_ht: 25000,
            value_taxe: 20,
            price_ttc: 30000,
        }) ;








        zhttp.config.product.get.attr().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.attributes = angular.fromJson(response.data.value);
            }
        });

        if ($routeParams.id && $routeParams.id > 0) {
            loadCtxtEdit();
        }

        if ($routeParams.category) {
            loadCtxtNew();
        }

        function update(branch) {
            $scope.currentBranch = branch;
            $scope.form.id_cat = branch.id;
        }


        function updateTreeSelect(niveau, branchesContent) {
            if (niveau == 0) {
                $scope.tree_select = [];
            }

            var tree = [];


            angular.forEach(branchesContent, function (branche) {
                tree.push({id: branche.id, name: strRepeat("&nbsp;", 5 * niveau) + branche.name});

                if (branche.branches) {
                    var sousBranche = updateTreeSelect(niveau + 1, branche.branches);
                    tree = tree.concat(sousBranche);
                }
            });


            if (niveau == 0) {
                $scope.tree_select = tree;
            }

            return tree;

        }

        function strRepeat(car, nbRepeat) {
            var strReturn = "";
            for (var i = 1; i <= nbRepeat; i++) {
                strReturn += car;
            }
            return strReturn;
        }


        function loadCtxtNew() {
            $scope.form.type_product = 'product';
            $scope.form.active = 1;

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

        function loadCtxtEdit() {
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    updateTreeSelect(0, $scope.tree.branches);

                    zhttp.crm.product.get($routeParams.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.form = response.data;

                            $scope.form.price_ht = parseFloat($scope.form.price_ht);
                            $scope.form.value_taxe = parseFloat($scope.form.value_taxe);
                            updatePrice();

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
            zeapps_modal.loadModule("com_zeapps_crm", "search_product_stock", {}, function (objReturn) {
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


        var accountingNumberToUpdate = -1;

        function openModalAccountingNumber(data) {
            accountingNumberToUpdate = data;
        };

        function loadAccountingNumber(accounting_number) {
            if (accountingNumberToUpdate == -1) {
                if (accounting_number) {
                    $scope.form.accounting_number = accounting_number.number;
                } else {
                    $scope.form.accounting_number = "";
                }
            } else {
                if (!$scope.form.priceList[accountingNumberToUpdate]) {
                    $scope.form.priceList[accountingNumberToUpdate] = {};
                }
                if (accounting_number) {
                    $scope.form.priceList[accountingNumberToUpdate].accounting_number = accounting_number.number;
                } else {
                    $scope.form.priceList[accountingNumberToUpdate].accounting_number = "";
                }
            }
        }

        function updateTaxe(indexPriceList) {
            angular.forEach($rootScope.taxes, function (taxe) {
                var idTaxeTest = 0;

                if (!indexPriceList || indexPriceList == -1) {
                    idTaxeTest = $scope.form.id_taxe;
                } else {
                    idTaxeTest = $scope.form.priceList[indexPriceList].id_taxe;
                }


                if (taxe.id == idTaxeTest) {
                    if (!indexPriceList || indexPriceList == -1) {
                        $scope.form.value_taxe = taxe.value;
                    } else {
                        var priceListByDefault = false ;
                        angular.forEach($scope.price_lists, function (price_list) {
                            if (price_list.id == indexPriceList && price_list.default) {
                                priceListByDefault = true ;
                            }
                        });

                        if (!$scope.form.priceList[indexPriceList]) {
                            $scope.form.priceList[indexPriceList] = {};
                        }
                        $scope.form.priceList[indexPriceList].value_taxe = taxe.value;

                        // pour mettre à jour le taux de tva par defaut
                        if (priceListByDefault) {
                            $scope.form.id_taxe = $scope.form.priceList[indexPriceList].id_taxe;
                            $scope.form.value_taxe = taxe.value;
                        }
                    }
                }
            });
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



        String.prototype.endsWith = function(suffix) {
            return this.indexOf(suffix, this.length - suffix.length) !== -1;
        };

        function updatePrice(indexPriceList, price) {

            if (indexPriceList == undefined) {
                angular.forEach($scope.price_lists, function (price_list) {
                    updatePrice(price_list.id) ;
                });
            }

            if (!indexPriceList || indexPriceList == -1) {
                $scope.form.price_ht = convertFloat($scope.form.price_ht);
                $scope.form.price_ttc = convertFloat($scope.form.price_ttc);

            } else {
                if (!$scope.form.priceList[indexPriceList]) {
                    $scope.form.priceList[indexPriceList] = {};
                }

                if (!$scope.form.priceList[indexPriceList].price_ht) {
                    $scope.form.priceList[indexPriceList].price_ht = 0;
                }

                if (!$scope.form.priceList[indexPriceList].price_ttc) {
                    $scope.form.priceList[indexPriceList].price_ttc = 0;
                }

                $scope.form.priceList[indexPriceList].price_ht = convertFloat($scope.form.priceList[indexPriceList].price_ht);
                $scope.form.priceList[indexPriceList].price_ttc = convertFloat($scope.form.priceList[indexPriceList].price_ttc);
            }


            if ($scope.form.type_product == "pack") {
                var montantHT = 0;
                var montantTTC = 0;

                if (!indexPriceList || indexPriceList == -1) {
                    for (var i = 0; i < $scope.form.sublines.length; i++) {
                        var montantLigneHT = $scope.form.sublines[i].price_ht * $scope.form.sublines[i].quantite;

                        montantHT += montantLigneHT;
                        montantTTC += montantLigneHT;

                        var tauxTva = 0;
                        angular.forEach($rootScope.taxes, function (taxe) {
                            if (taxe.id == $scope.form.sublines[i].id_taxe) {
                                tauxTva = taxe.value;

                                montantTTC += montantLigneHT * tauxTva / 100;
                            }
                        });
                    }


                    if ($scope.form.update_price_from_subline) {
                        $scope.form.price_ht = round2(convertFloat(montantHT));
                        $scope.form.price_ttc = round2(convertFloat(montantTTC));
                    } else {
                        if (price === "ht") {
                            var coef = convertFloat($scope.form.price_ttc) / montantTTC;
                            $scope.form.price_ht = round2(montantHT * coef);

                        } else if (price === "ttc") {
                            var coef = convertFloat($scope.form.price_ht) / montantHT;
                            $scope.form.price_ttc = round2(montantTTC * coef);
                        }
                    }
                } else {
                    for (var i = 0; i < $scope.form.sublines.length; i++) {
                        if (!$scope.form.sublines[i].priceList[indexPriceList]) {
                            $scope.form.sublines[i].priceList[indexPriceList] = {};
                        }

                        if (!$scope.form.sublines[i].priceList[indexPriceList].price_ht) {
                            $scope.form.sublines[i].priceList[indexPriceList].price_ht = 0;
                        }

                        if (!$scope.form.sublines[i].priceList[indexPriceList].price_ttc) {
                            $scope.form.sublines[i].priceList[indexPriceList].price_ttc = 0;
                        }

                        if (!$scope.form.sublines[i].priceList[indexPriceList].id_taxe) {
                            $scope.form.sublines[i].priceList[indexPriceList].id_taxe = 0;
                        }


                        var montantLigneHT = $scope.form.sublines[i].priceList[indexPriceList].price_ht * $scope.form.sublines[i].quantite;

                        montantHT += montantLigneHT;
                        montantTTC += montantLigneHT;

                        var tauxTva = 0;
                        angular.forEach($rootScope.taxes, function (taxe) {
                            if (taxe.id == $scope.form.sublines[i].priceList[indexPriceList].id_taxe) {
                                tauxTva = taxe.value;

                                montantTTC += montantLigneHT * tauxTva / 100;
                            }
                        });
                    }


                    if ($scope.form.update_price_from_subline) {
                        $scope.form.priceList[indexPriceList].price_ht = round2(convertFloat(montantHT));
                        $scope.form.priceList[indexPriceList].price_ttc = round2(convertFloat(montantTTC));
                    } else {
                        if (price === "ht") {
                            var coef = convertFloat($scope.form.priceList[indexPriceList].price_ttc) / montantTTC;
                            $scope.form.priceList[indexPriceList].price_ht = round2(montantHT * coef);

                        } else if (price === "ttc") {
                            var coef = convertFloat($scope.form.priceList[indexPriceList].price_ht) / montantHT;
                            $scope.form.priceList[indexPriceList].price_ttc = round2(montantTTC * coef);
                        }
                    }
                }
            } else {
                var value_taxe = 0;
                if (!indexPriceList || indexPriceList == -1) {
                    value_taxe = $scope.form.value_taxe;
                } else {
                    value_taxe = $scope.form.priceList[indexPriceList].value_taxe;
                }

                if (!value_taxe) {
                    value_taxe = 0;
                } else {
                    value_taxe = convertFloat(value_taxe);
                }

                if (price === "ht") {
                    if (!indexPriceList || indexPriceList == -1) {
                        $scope.form.price_ht = round2(convertFloat($scope.form.price_ttc) / (1 + value_taxe / 100));
                    } else {
                        $scope.form.priceList[indexPriceList].price_ht = round2(convertFloat($scope.form.priceList[indexPriceList].price_ttc) / (1 + value_taxe / 100));
                    }
                } else if (price === "ttc") {
                    if (!indexPriceList || indexPriceList == -1) {
                        $scope.form.price_ttc = round2(convertFloat($scope.form.price_ht) * (1 + value_taxe / 100));
                    } else {
                        $scope.form.priceList[indexPriceList].price_ttc = round2(convertFloat($scope.form.priceList[indexPriceList].price_ht) * (1 + value_taxe / 100));
                    }
                }
            }


            // pour mettre à jour le tarif par defaut du produit
            if (indexPriceList >= 1) {
                var priceListByDefault = false;
                angular.forEach($scope.price_lists, function (price_list) {
                    if (price_list.id == indexPriceList && price_list.default) {
                        priceListByDefault = true;
                    }
                });
                if (priceListByDefault) {
                    $scope.form.price_ht = $scope.form.priceList[indexPriceList].price_ht ;
                    $scope.form.price_ttc = $scope.form.priceList[indexPriceList].price_ttc ;
                }
            }
        }

        function descState(current, max) {
            if (current > max)
                return "text-danger";
            else if (current > Math.ceil(max * 0.9) && current < max)
                return "text-warning";
            else
                return "text-success";

        }

        function success() {
            var data = $scope.form;

            if ($routeParams.id != 0) {
                data.id = $routeParams.id;
            }

            if (!data.id_cat && $routeParams.category) {
                data.id_cat = $routeParams.category;
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.product.save(formatted_data).then(function (response) {
                if (typeof (response.data.error) === "undefined") {
                    // pour que la page puisse être redirigé
                    if ($routeParams.url_retour) {
                        $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
                    } else {
                        $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
                    }
                } else {
                    $scope.error = response.data.error;
                }
            });
        }

        function cancel() {
            if ($routeParams.url_retour) {
                $location.path($routeParams.url_retour.replace(charSepUrlSlashRegExp, "/"));
            } else {
                $location.path("/ng/com_zeapps_crm/product/category/" + $scope.form.id_cat);
            }
        }


        function addLine() {
            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function (objReturn) {
                if (objReturn) {
                    if (objReturn.type_product != "pack") {
                        var id_product = objReturn.id ;
                        var line = objReturn;
                        line.id = 0;
                        line.id_product = id_product;
                        line.quantite = 1;
                        line.sort = $scope.form.sublines.length + 1;
                        $scope.form.sublines.push(line);

                        updatePrice();
                    } else {
                        toasts("danger", "Les packs ne peuvent pas être ajoutés");
                    }
                }
            });
        };


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
                            var id_product = response.data.id ;
                            var line = response.data;
                            line.id = 0;
                            line.id_product = id_product;
                            line.quantite = 1;
                            line.sort = $scope.form.sublines.length + 1;
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

        function deleteLine(line) {
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

        function updatePriceSubLine() {
            updatePrice();
        };


        function round2(num) {
            return +(Math.round(num + "e+2") + "e-2");
        }
    }]);