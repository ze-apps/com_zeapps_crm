// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"view_payment",
	templateUrl:"/com_zeapps_crm/payment/view_modal",
	controller:"ZeAppsCrmModalViewPaymentCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return "Encaissement";
		}
	}
});

app.controller("ZeAppsCrmModalViewPaymentCtrl", ["$scope", "$uibModalInstance", "titre", "option", "zeHttp", function($scope, $uibModalInstance, titre, option, zeHttp) {
	$scope.titre = titre ;

	$scope.form = {};

	$scope.cancel = cancel;

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}




	// charge les infos de l'encaissement
	zeHttp.crm.payment.get(option.id).then(function (response) {
		if (response.data && response.data != "false") {
			$scope.payment = response.data.payment;
			$scope.payment_lines = response.data.payment_lines;

			$scope.payment.date_payment = $scope.payment.date_payment && $scope.payment.date_payment !== "0000-00-00" ? new Date($scope.payment.date_payment) : "";


			for (var i = 0; i < $scope.payment_lines.length; i++) {
				if ($scope.payment_lines[i].invoice_data) {
					$scope.payment_lines[i].invoice_data.date_creation = $scope.payment_lines[i].invoice_data.date_creation && $scope.payment_lines[i].invoice_data.date_creation !== "0000-00-00" ? new Date($scope.payment_lines[i].invoice_data.date_creation) : "";
				}
			}
		}
	});



}]) ;