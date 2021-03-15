app.controller("ComZeappsCrmPriceListListsCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts", "menu",
    function ($scope, $location, $rootScope, zhttp, $timeout, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product_price_list");


        $scope.templatePriceList = '/com_zeapps_crm/price-list/form_modal';

        var src = "orders";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;
        $scope.taux = taux;


        function loadList() {
            zhttp.crm.price_list.get_all_admin().then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.priceLists = response.data;
                }
            });
        }

        loadList();


        function add(priceList) {
            var data = priceList;

            var formatted_data = angular.toJson(data);
            zhttp.crm.price_list.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', "Les informations ont bien été enregistré");
                    loadList();
                }
            });
        }

        function edit(priceList) {
            var data = priceList;

            var formatted_data = angular.toJson(data);

            zhttp.crm.price_list.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', __t("The information has been updated"));
                } else {
                    toasts('danger', __t("There was an error updating the information"));
                }
                loadList();
            });
        }

        function del(priceList) {
            zhttp.crm.price_list.del(priceList.id).then(function (response) {
                if (response.data && response.data != "false") {
                    loadList();
                }
            });
        }

        function taux(idPriceList) {
            $location.url('/ng/com_zeapps_crm/product_price_list_taux/' + idPriceList);
        }
    }]);