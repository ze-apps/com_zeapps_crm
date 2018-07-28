app.run(["zeHttp", "$rootScope", function (zhttp, $rootScope) {
    zhttp.crm.taxe.get_all().then(function (response) {
        if (response.data && response.data != "false") {
            $rootScope.taxes = response.data;
            angular.forEach($rootScope.taxes, function (taxe) {
                taxe.value = parseFloat(taxe.value);
            });
        }
    });
}]);