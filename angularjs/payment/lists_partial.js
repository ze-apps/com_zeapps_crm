app.controller("ComZeappsCrmPaymentListsPartialCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts", "zeapps_modal",
    function ($scope, $location, $rootScope, zhttp, $timeout, toasts, zeapps_modal) {

        if (!$rootScope.quotes) {
            $rootScope.quotes = {};
        }
        $scope.id_company = 0;
        $scope.filters = {};
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templatePayment = '/com_zeapps_crm/payment/form_modal';

        var src = "quotes";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.goTo = goTo;
        $scope.add = add;
        $scope.delete = del;

        $scope.$on("comZeappsContact_dataEntrepriseHook", function (event, data) {
            if ($scope.id_company !== data.id_company) {
                $scope.id_company = data.id_company;
                src = "company";
                src_id = data.id_company;

                loadList(true);
            }
        });
        $scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

        $scope.$on("comZeappsContact_dataContactHook", function (event, data) {
            if ($scope.id_contact !== data.id_contact) {
                $scope.id_contact = data.id_contact;
                $scope.id_company = data.id_company;
                src = "contact";
                src_id = data.id_contact;

                loadList(true);
            }
        });
        $scope.$emit("comZeappsContact_triggerContactHook", {});









        $timeout(function () { // Making sure the default call is only triggered after the potential broadcast from a hook
            if (src_id === 0) {
                loadList(true);
            }
        }, 0);

        function loadList(context) {
            context = context || "";
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            zhttp.crm.payment.get_all(src_id, src, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.payments = response.data.payments;

                    for (var i = 0; i < $scope.payments.length; i++) {
                        $scope.payments[i].date_payment = $scope.payments[i].date_payment && $scope.payments[i].date_payment !== "0000-00-00" ? new Date($scope.payments[i].date_payment) : "";
                    }

                    $scope.total = response.data.total;
                }
            });
        }

        function goTo(id) {
            var options = {} ;
            options.id = id ;
            zeapps_modal.loadModule("com_zeapps_crm", "view_payment", options, function (objReturn) {
                if (objReturn) {

                }
            });
        }

        function add(payment) {
            console.log(payment);

            var data = payment;

            var formatted_data = angular.toJson(data);
            zhttp.crm.payment.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    loadList(true);
                }
            });
        }



        function del(payment) {
            zhttp.crm.payment.del(payment.id).then(function (response) {
                if (response.data && response.data != "false") {
                    loadList(true);
                }
            });
        }
    }]);