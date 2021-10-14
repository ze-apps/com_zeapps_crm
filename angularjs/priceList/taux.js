app.controller("ComZeappsCrmPriceListTauxCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts", "menu", "$routeParams",
    function ($scope, $location, $rootScope, zhttp, $timeout, toasts, menu, $routeParams) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product_price_list");


        $scope.loadList = loadList;
        $scope.grille_tarif = {} ;
        $scope.categories = [] ;
        $scope.pricelist_rate = [] ;

        function loadList() {
            zhttp.crm.price_list.get($routeParams.id).then(function (response) {
                if (response.status == 200) {
                    $scope.grille_tarif = response.data ;

                    zhttp.crm.category.tree().then(function (response) {
                        if (response.status == 200) {
                            $scope.categories = generateCategories(response.data, 0) ;

                            zhttp.crm.price_list.rate($routeParams.id).then(function (response) {
                                if (response.status == 200) {
                                    $scope.pricelist_rate = response.data ;

                                    angular.forEach($scope.categories, function (category) {
                                        var rate = getRate(category) ;
                                        if (rate) {
                                            category.taux_remise = rate.percentage ;
                                            category.compte_compta = rate.accounting_number ;
                                            category.id_taxe = rate.id_taxe ;
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        }
        loadList();


        var generateCategories = function (arrCategorie, niveau) {
            var arrCat = [];
            for (var i_cat = 0; i_cat < arrCategorie.length; i_cat++) {
                arrCategorie[i_cat].niveau = niveau ;
                arrCat.push(arrCategorie[i_cat]);
                if (arrCategorie[i_cat].branches) {
                    var arrReturn = generateCategories(arrCategorie[i_cat].branches, niveau + 1);
                    for (var i_sous_cat = 0; i_sous_cat < arrReturn.length; i_sous_cat++) {
                        arrCat.push(arrReturn[i_sous_cat]);
                    }
                }
            }

            return arrCat;
        };


        $scope.espace = function (niveau) {
            var espaceReturn = "" ;

            for (var i_niveau = 1 ; i_niveau <= niveau ; i_niveau++) {
                espaceReturn += "-----" ;
            }

            return espaceReturn ;
        };






        var getRate = function (category) {
            var objRate = null ;

            $.each($scope.pricelist_rate, function (index, value) {
                if (value.id_category == category.id) {
                    objRate = value ;
                }
            }) ;


            if (objRate == null && category.id_parent) {
                angular.forEach($scope.categories, function (subCategory) {
                    if (subCategory.id == category.id_parent) {
                        objRate = getRate(subCategory);
                    }
                });
            }

            return objRate ;
        };



        $scope.save = function (categorie) {

            var data = {};
            data.id_pricelist = $routeParams.id ;
            data.id_category = categorie.id ;

            if (categorie.taux_remise) {
                data.percentage = categorie.taux_remise;
            }

            if (categorie.compte_compta) {
                data.accounting_number = categorie.compte_compta;
            }

            if (categorie.id_taxe) {
                data.id_taxe = categorie.id_taxe;
                data.value_taxe = 0;

                angular.forEach($rootScope.taxes, function (taxe) {
                    if (taxe.id == categorie.id_taxe) {
                        data.value_taxe = taxe.value;
                    }
                });
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.price_list.save_rate(formatted_data).then(function (response) {
                loadList();
            });
        };

    }]);