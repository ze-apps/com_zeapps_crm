app.controller("ComZeappsCrmQuoteSendEmailCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp", "$location",
    function ($scope, $routeParams, $rootScope, zhttp, $location) {

        $scope.attachments = [];

        if ($routeParams.id !== undefined && $routeParams.id !== 0) {
            // demande le chemin vers le PDF du document

            zhttp.crm.quote.get($routeParams.id).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.quote = response.data.quote;

                    $scope.attachments = [];
                    zhttp.crm.quote.pdf.make($scope.quote.id).then(function (response) {
                        if (response.data && response.data != "false") {
                            var url_file = angular.fromJson(response.data);
                            $scope.attachments.push({file: url_file, url: "/" + url_file, name: "quote.pdf"});

                        }
                    });
                }
            });


        }

        $scope.cancel = function () {
            $location.path("/ng/com_zeapps_crm/quote/" + $routeParams.id);
        };

        $scope.send = function () {
            var data = {};

            data.id = $scope.quote.id;
            data.subject = $scope.form.subject;
            data.content = $scope.form.content;
            data.to = $scope.form.to ;
            data.attachments = $scope.attachments ;

            var formatted_data = angular.toJson(data);

            zhttp.crm.quote.send_email(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    $location.path("/ng/com_zeapps_crm/quote/" + $routeParams.id);
                }
            });
        };


    }]);