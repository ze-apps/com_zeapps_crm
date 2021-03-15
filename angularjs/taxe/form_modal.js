app.controller("ComZeappsCrmTaxeConfigFormModalCtrl", ["$scope", "zeHttp",
	function ($scope, zhttp) {

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_contact/accounting_numbers/form_modal';
        $scope.accountingNumberFields = [{
                label: __t("Number"),
                key: 'number'
            },
            {
                label: __t("Label"),
                key: 'label'
            },
            {
                label: __t("Type"),
                key: 'type_label'
            }
        ];

        $scope.loadAccountingNumber = loadAccountingNumber;

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.form.accounting_number = accounting_number.number;
            } else {
                $scope.form.accounting_number = "";
            }
        }
	}]);