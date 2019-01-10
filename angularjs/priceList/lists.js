app.controller("ComZeappsCrmPriceListListsCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts",
    function ($scope, $location, $rootScope, zhttp, $timeout, toasts) {


        $scope.templatePriceList = '/com_zeapps_crm/price-list/form_modal';

        var src = "orders";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;


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
                    toasts('success', "Les informations ont bien été mises a jour");
                } else {
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations");
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
    }]);