app.run(["zeHttp", "$rootScope", function(zhttp, $rootScope){
    /*$rootScope.modalities =[];

    $modalities = zhttp.contact.modality.get_all() ;
    $modalities.success(function (response) {
        angular.forEach(response, function(modality){
            $rootScope.modalities.push(modality);
        });
    });*/


    zhttp.crm.taxe.get_all().then(function(response){
    	console.log(response.data);
        if(response.data && response.data != "false"){
            $rootScope.taxes = response.data;
            angular.forEach($rootScope.taxes, function(taxe){
                taxe.value = parseFloat(taxe.value);
            });
        }
    });
}]);