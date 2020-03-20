app.controller("ComZeappsCrmQuoteConfigCtrl", ["$scope", "zeHttp", "menu",
    function ($scope, zhttp, menu) {

        menu("com_ze_apps_config", "com_ze_apps_quotes");

        $scope.format = "";
        $scope.textBefore = "";
        $scope.textAfter = "";

        $scope.success = success;
        $scope.test = test;

        zhttp.config.quote.get.format().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.format = response.data.value;
            }
        });

        zhttp.config.quote.get.numerotation().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.numerotation = parseInt(angular.fromJson(response.data));
            }
        });

        zhttp.config.get("crm_quote_text_before_lines").then(function (response) {
            if (response.data && response.data != "false") {
                if (response.data && response.data.value) {
                    $scope.textBefore = response.data.value;
                }
            }
        });

        zhttp.config.get("crm_quote_text_after_lines").then(function (response) {
            if (response.data && response.data != "false") {
                if (response.data && response.data.value) {
                    $scope.textAfter = response.data.value;
                }
            }
        });

        function success() {

            var data = {};

            data[0] = {
                id: "crm_quote_format",
                value: $scope.format
            };
            data[1] = {
                id: "crm_quote_numerotation",
                value: $scope.numerotation
            };
            data[2] = {
                id: "crm_quote_text_before_lines",
                value: $scope.textBefore
            };
            data[3] = {
                id: "crm_quote_text_after_lines",
                value: $scope.textAfter
            };

            var formatted_data = angular.toJson(data);
            zhttp.config.save(formatted_data);
        }

        function test() {
            var data = {};

            data["format"] = $scope.format;
            data["numerotation"] = $scope.numerotation;

            var formatted_data = angular.toJson(data);
            zhttp.crm.quote.test(formatted_data).then(function (response) {
                if (response.data && response.data != false) {
                    $scope.result = angular.fromJson(response.data);
                }
            });
        }

    }]);