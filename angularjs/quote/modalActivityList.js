// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"quote_activities_list",
	templateUrl:"/com_zeapps_crm/quotes/modal_activities_list",
	controller:"ComZeappsCrmModalActivitiesListCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return __t("Activity monitoring");
		}
	}
});


app.controller("ComZeappsCrmModalActivitiesListCtrl", ["$scope", "$uibModalInstance", "zeHttp", "titre", "option", function($scope, $uibModalInstance, zhttp, titre, option) {
	$scope.titre = titre ;

	$scope.cancel = cancel;
    $scope.loadList = loadList;

    $scope.addActivity = addActivity;
    $scope.editActivity = editActivity;
    $scope.deleteActivity = deleteActivity;

    $scope.quoteActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";

    $scope.quote = option ;

    function loadList() {
        $scope.activities = [];
        for (var i = 0; i < option.activities.length; i++) {
            option.activities[i].created_at_info = option.activities[i].created_at !== "0000-00-00 00:00:00" ? new Date(option.activities[i].created_at) : 0;
            option.activities[i].deadline = option.activities[i].deadline !== "0000-00-00 00:00:00" ? new Date(option.activities[i].deadline) : 0;

            $scope.activities.push(option.activities[i]);
        }

		$scope.activities = option.activities;
    }
    loadList();

	function cancel() {
		$uibModalInstance.dismiss("Cancel");
	}



    function addActivity(activity) {
        var y = activity.deadline.getFullYear();
        var M = activity.deadline.getMonth();
        var d = activity.deadline.getDate();

        activity.deadline = new Date(Date.UTC(y, M, d));
        activity.id_quote = $scope.quote.id;
        var formatted_data = angular.toJson(activity);

        zhttp.crm.quote.activity.save(formatted_data).then(function (response) {
            if (response.data && response.data != "false") {
                response.data.created_at_info = new Date(response.data.created_at);
                response.data.deadline = new Date(response.data.deadline);
                $scope.quote.activities.push(response.data);
                $scope.activities = $scope.quote.activities ;

                option.callback($scope.quote);
            }
        });
    }

    function editActivity(activity) {
        var y = activity.deadline.getFullYear();
        var M = activity.deadline.getMonth();
        var d = activity.deadline.getDate();

        activity.deadline = new Date(Date.UTC(y, M, d));
        var formatted_data = angular.toJson(activity);
        zhttp.crm.quote.activity.save(formatted_data).then(function (response) {
            var activities = [];
            for (var i = 0 ; i < $scope.quote.activities.length ; i++) {
                var tmpActivity = $scope.quote.activities[i] ;
                tmpActivity = tmpActivity.id == activity.id ? activity : tmpActivity;
                activities.push(tmpActivity);
            }
            $scope.activities = activities ;
            $scope.quote.activities = activities ;
    
            option.callback($scope.quote);
        });        
    }

    function deleteActivity(activity) {
        zhttp.crm.quote.activity.del(activity.id).then(function (response) {
            if (response.status == 200) {
                $scope.activities.splice($scope.activities.indexOf(activity), 1);
                $scope.quote.activities = $scope.activities ;

                option.callback($scope.quote);
            }
        });
    }

}]) ;