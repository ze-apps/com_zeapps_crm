app.controller("ZeAppsCrmModalFormDocumentCtrl", ["$scope", function($scope) {
    $scope.upload = upload;

    function upload(files) {
        $scope.form.files = files;

        if(!$scope.form.name && $scope.form.files[0]){
            $scope.form.name = $scope.form.files[0].name;
        }
    }
}]) ;