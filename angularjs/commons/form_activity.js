app.controller("ZeAppsCrmModalFormActivityCtrl", ["$scope", "zeHttp", "$rootScope", function($scope, zhttp, $rootScope) {
    $scope.activity_types = [];

    $scope.updateType = updateType;
    $scope.loadAccountManager = loadAccountManager;


    $scope.accountManagerHttp = zhttp.app.user;
    $scope.accountManagerFields = [
        {label:'Prénom',key:'firstname'},
        {label:'Nom',key:'lastname'}
    ];

    zhttp.crm.activity_types.all().then(function(response){
        if(response.data && response.data != "false"){
            $scope.activity_types = response.data.activity_types;
        }
    });

    if($scope.form.id){
        $scope.form.deadline = new Date($scope.form.deadline);
    } else {
        $scope.form.status = "A faire";
        $scope.form.deadline = new Date();


        $scope.form.id_user = $rootScope.user.id;
        $scope.form.name_user = $rootScope.user.firstname + " " + $rootScope.user.lastname;
    }

    function updateType(){
        angular.forEach($scope.activity_types, function(type){
            if(type.id === $scope.form.id_type){
                $scope.form.label_type = type.label;
            }
        });
    }


    function loadAccountManager(user) {
        if (user) {
            $scope.form.id_user = user.id;
            $scope.form.name_user = user.firstname + " " + user.lastname;
        } else {
            $scope.form.id_user = 0;
            $scope.form.name_user = "";
        }
    }
}]) ;