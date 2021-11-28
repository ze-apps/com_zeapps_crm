app.controller("ComZeappsCrmActivitiesListsCtrl", ["$scope", "$rootScope", "menu", "zeHttp", "$timeout", 
	function ($scope, $rootScope, menu, zhttp, $timeout) {

        menu("com_ze_apps_sales", "com_zeapps_crm_activities");


		setTimeout(() => {
			$scope.id_company = 0;
			$scope.filters = {
				main: [],
				secondaries: [
				]
			};

			var rights = JSON.parse($rootScope.user.rights);
			if (rights.com_zeapps_crm_activities_view_all) {
				$scope.filters.main.push({
							format: 'select',
							field: 'id_user',
							type: 'text',
							label: __t("Person responsible"),
							options: []
						});
			}

			$scope.filters.main.push({
						format: 'select',
						field: 'status',
						type: 'text',
						label: __t("Status"),
						options: [{
							id: 'A faire',
							label: __t("To do")
						}, {
							id: 'Terminé',
							label: __t("Finished")
						}]
					});

			$scope.filters.main.push({
						format: 'input',
						field: 'deadline <=',
						type: 'date',
						label: __t("Deadline: before"),
						size: 3
					});

			$scope.filters.main.push({
						format: 'input',
						field: 'deadline >=',
						type: 'date',
						label: __t("Deadline: after"),
						size: 3
					});


			$scope.filter_model = {};
			$scope.page = 1;
			$scope.pageSize = 15;
			$scope.total = 0;
			$scope.templateQuote = '/com_zeapps_crm/quotes/form_modal';

			// valeur par défaut du filtre de recherche
			$scope.filter_model.id_user = $rootScope.filter_model_id_user ? $rootScope.filter_model_id_user : $rootScope.user.id+"" ;
			$scope.filter_model.status = $rootScope.filter_model_activity_status ? $rootScope.filter_model_activity_status : "A faire";


			// remonte la liste des managers
			if (rights.com_zeapps_crm_activities_view_all) {
				$scope.filters.main[0].options = [];
				zhttp.app.user.all().then(function (response) {
					if (response.data && response.data != "false") {
						angular.forEach(response.data, function (user) {
							$scope.filters.main[0].options.push({ id: user.id, label: user.firstname + " " + user.lastname });
						});
					}
				});
			}

			$timeout(function () { // Making sure the default call is only triggered after the potential broadcast from a hook
				loadList(true);
			}, 0);
		}, 800);

        



      
        $scope.loadList = loadList;

        function loadList() {
            var offset = ($scope.page - 1) * $scope.pageSize;

			$rootScope.filter_model_id_user = $scope.filter_model.id_user ;
			$rootScope.filter_model_activity_status = $scope.filter_model.status ;



            var filtre = {} ;
            angular.forEach($scope.filter_model, function (value, key) {
                filtre[key] = value ;
            });



            // convet date JS to YYYY-MM-DD
            var arrayFieldDate = ["deadline <=", "deadline >="] ;
            for (var i_arrayFieldDate = 0 ; i_arrayFieldDate < arrayFieldDate.length ; i_arrayFieldDate++) {
                if (filtre[arrayFieldDate[i_arrayFieldDate]] != undefined) {
                    filtre[arrayFieldDate[i_arrayFieldDate]] = filtre[arrayFieldDate[i_arrayFieldDate]].getFullYear() + "-" + (filtre[arrayFieldDate[i_arrayFieldDate]].getMonth() + 1) + "-" + filtre[arrayFieldDate[i_arrayFieldDate]].getDate();
                }
            }



            let formatted_filters = angular.toJson(filtre);
            zhttp.crm.activities.get_all($scope.pageSize, offset, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.activities = response.data.activities;

                    for (let i = 0; i < $scope.activities.length; i++) {
                        $scope.activities[i].date = $scope.activities[i].date !== "0000-00-00 00:00:00" ? new Date($scope.activities[i].date) : 0;
                        $scope.activities[i].deadline = $scope.activities[i].deadline !== "0000-00-00 00:00:00" ? new Date($scope.activities[i].deadline) : 0;
                        $scope.activities[i].reminder = $scope.activities[i].reminder !== "0000-00-00 00:00:00" ? new Date($scope.activities[i].reminder) : 0;
                        $scope.activities[i].validation = $scope.activities[i].validation !== "0000-00-00 00:00:00" ? new Date($scope.activities[i].validation) : 0;
                    }

                    $scope.total = response.data.total;
                }
            });
        }
	}]);