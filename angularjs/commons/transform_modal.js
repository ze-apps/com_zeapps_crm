// declare the modal to the app service
listModuleModalFunction.push({
	module_name:"com_zeapps_crm",
	function_name:"transform_document",
	templateUrl:"/com_zeapps_crm/crm_commons/transform_modal",
	controller:"ZeAppsCrmModalDocumentTransformCtrl",
	size:"lg",
	resolve:{
		titre: function () {
			return __t("Duplicate document");
		}
	}
});

app.controller("ZeAppsCrmModalDocumentTransformCtrl", ["$scope", "zeHttp", "$uibModalInstance", "titre", "option", function($scope, zeHttp, $uibModalInstance, titre, option) {
	$scope.titre = titre ;
	$scope.msgError = '' ;
	$scope.showError = false ;

	$scope.form = {};
	$scope.invoices = [];

	$scope.cancel = cancel;
	$scope.transform = transform;

	$scope.form.type_deposit = "-";

	$scope.types_deposit = [];
	$scope.types_deposit.push({
		value: "-",
		label: "Veuillez choisir"
	});
	$scope.types_deposit.push({
		value: "percent",
		label: "en %"
	});
	$scope.types_deposit.push({
		value: "deposit_ht",
		label: "par montant HT"
	});
	$scope.types_deposit.push({
		value: "deposit_ttc",
		label: "par montant TTC"
	});

	$scope.form.invoicesSelected = [];
	$scope.checkInvoice = function (id) {
		const index = $scope.form.invoicesSelected.indexOf(id);
		if (index > -1) {
			$scope.form.invoicesSelected.splice(index, 1);
		} else {
			$scope.form.invoicesSelected.push(id);
		}
	};

	$scope.loadInvoiceRelated = function () {
		if ($scope.form.invoice_with_down_payment_deduction) {
			$scope.invoices = [];
			$scope.form.invoicesSelected = [];

			let formatted_data = angular.toJson(option);
			zeHttp.crm.commons.getInvoicesRelated(formatted_data).then(function(response){
				if (response.data && response.data != "false") {
					$scope.invoices = response.data;
				}
			});
		}
	};

	function cancel() {
		$uibModalInstance.dismiss("cancel");
	}

	function transform() {
		let error = false;
		let msgError = [] ;

		// controle de la saisie pour les factures d'acompte
		if ($scope.form.deposit_invoices) {
			if ($scope.form.type_deposit == "-") {
				error = true;
				msgError.push('Veuillez choisir le type d\'acompte') ;
			} else if (isNaN($scope.form.deposit_invoices_value)) {
				error = true;
				msgError.push('Veuillez indiquer une valeur pour l\'acompte') ;
			}
		}

		// controle de la saisie pour les factures avec déduction d'acompte
		if ($scope.form.invoice_with_down_payment_deduction && $scope.form.invoicesSelected.length == 0) {
			error = true;
			msgError.push('Veuillez sélection au moins une facture à déduire') ;
		}

		

		if (error == false) {
			$uibModalInstance.close($scope.form);
		} else {
			$scope.msgError = msgError ;
			$scope.showError = true ;
		}
	}
}]) ;