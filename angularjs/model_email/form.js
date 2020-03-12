app.controller("ComZeappsCrmModelEmailConfigFormCtrl", ["$scope", "$route", "$routeParams", "$location", "$rootScope", "zeHttp", "menu", "Upload", '$timeout',
	function ($scope, $route, $routeParams, $location, $rootScope, zhttp, menu, Upload, $timeout) {

		menu("com_ze_apps_config", "com_zeapps_crm_config_model_email");


		$scope.form = {} ;
		$scope.form.attachments = [];

		$scope.cancel = cancel;
		$scope.success = success;


		// upload d'une pièce jointe
		$scope.uploadFiles = function(file, errFiles) {
			$scope.f = file;
			$scope.errFile = errFiles && errFiles[0];
			if (file) {
				var nomFichier = file.name ;

				file.upload = Upload.upload({
					url: '/com_zeapps_crm/model_email/upload-file',
					data: {file: file}
				});

				file.upload.then(function (response) {
					$timeout(function () {
						file.result = response.data;
						$scope.form.attachments.push({name:nomFichier, path: angular.fromJson(response.data)});
					});
				}, function (response) {
					if (response.status > 0)
						$scope.errorMsg = response.status + ': ' + response.data;
				}, function (evt) {
					file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
				});
			}
		};
		$scope.deleteFile = function (pathFile) {
			var contenuPieceJointe = [];

			angular.forEach($scope.form.attachments, function (attachment) {
				if (attachment.path != pathFile) {
					contenuPieceJointe.push(attachment);
				}
			});

			$scope.form.attachments = contenuPieceJointe ;
			console.log(pathFile);
		};





		if ($routeParams.id) {
			zhttp.crm.model_email.get($routeParams.id).then(function (response) {
				if (response.data && response.data != "false") {
					$scope.form = response.data.modelEmail;
					if (!$scope.form) {
						$scope.form = {};
					}
				}
			});
		}

		function cancel(){
			$location.url("/ng/com_zeapps_crm/model_email");
		}

		function success(){
			var data = $scope.form;
			var formatted_data = angular.toJson(data);

			zhttp.crm.model_email.save(formatted_data).then(function(response){
				if(response.data && response.data != false){
					$location.url("/ng/com_zeapps_crm/model_email");
				}
			});
		}
	}]);