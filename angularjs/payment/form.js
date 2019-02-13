app.controller("ComZeappsCrmPaymentFormCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp",
    function ($scope, $routeParams, $rootScope, zhttp) {
        $scope.companyHttp = zhttp.contact.company;
        $scope.companyTplNew = '/com_zeapps_contact/companies/form_modal/';
        $scope.companyFields = [
            {label: 'Nom', key: 'company_name'},
            {label: 'Téléphone', key: 'phone'},
            {label: 'Ville', key: 'billing_city'},
            {label: 'Gestionnaire du compte', key: 'name_user_account_manager'}
        ];

        $scope.contactHttp = zhttp.contact.contact;
        $scope.contactTplNew = '/com_zeapps_contact/contacts/form_modal/';
        $scope.contactFields = [
            {label: 'Nom', key: 'last_name'},
            {label: 'Prénom', key: 'first_name'},
            {label: 'Entreprise', key: 'name_company'},
            {label: 'Téléphone', key: 'phone'},
            {label: 'Ville', key: 'city'},
            {label: 'Gestionnaire du compte', key: 'name_user_account_manager'}
        ];

        $scope.loadCompany = loadCompany;
        $scope.loadContact = loadContact;
        $scope.updateModality = updateModality;
        $scope.updateTotal = updateTotal;
        $scope.updateEcart = updateEcart;



        function checkForm() {
            $scope.form.zeapps_modal_form_custom_isvalid = true ;

            // il ne doit pas y avoir d'écart
            if ($scope.ecart != 0) {
                $scope.form.zeapps_modal_form_custom_isvalid = false ;
            }

            var aucun_montant = true ;
            for (var i = 0; i < $scope.form.invoices.length; i++) {
                var montant_ligne = 0 ;
                if ($scope.form.invoices[i].amount_payment) {
                    montant_ligne = parseFloat($scope.form.invoices[i].amount_payment.replace(",", "."));
                }

                if (montant_ligne != 0) {
                    aucun_montant = false ;
                }
            }
            if (aucun_montant) {
                $scope.form.zeapps_modal_form_custom_isvalid = false ;
            }
        }


        Initform();

        function Initform() {
            if ($scope.form.id === undefined) {
                if ($routeParams.id_company !== undefined && $routeParams.id_company !== 0) {
                    zhttp.contact.company.get($routeParams.id_company).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompany(response.data.company);
                        }
                    });
                }
                if ($routeParams.id_contact !== undefined && $routeParams.id_contact !== 0) {
                    zhttp.contact.contact.get($routeParams.id_contact).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadContact(response.data.contact);
                        }
                    });
                }
            }
        }

        function loadCompany(company) {
            if (company) {
                $scope.form.id_company = company.id;
                $scope.form.name_company = company.company_name;

                $scope.form.check_issuer = $scope.form.name_company ;

                loadInvoice("company", company.id);
            } else {
                $scope.form.id_company = 0;
                $scope.form.name_company = "";
            }
        }

        function loadContact(contact) {
            if (contact) {
                $scope.form.id_contact = contact.id;
                $scope.form.name_contact = contact.last_name + " " + contact.first_name;

                if (contact.id_company !== "0" && ($scope.form.id_company === undefined || $scope.form.id_company === 0)) {
                    zhttp.contact.company.get(contact.id_company).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompany(response.data.company);
                        }
                    })
                } else {
                    $scope.form.check_issuer = $scope.form.name_contact ;
                    loadInvoice("contact", contact.id);
                }
            } else {
                $scope.form.id_contact = 0;
                $scope.form.name_contact = "";
            }
        }

        $scope.form.invoices = [];
        $scope.total_invoice_due = 0;

        function loadInvoice(type_contact, id_client) {
            $scope.form.invoices = [];
            $scope.total_invoice_due = 0;

            zhttp.crm.invoice.due(type_contact, id_client).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.form.invoices = response.data;

                    for (var i = 0; i < $scope.form.invoices.length; i++) {
                        $scope.total_invoice_due += $scope.form.invoices[i].due * 1 ;
                        $scope.form.invoices[i].date_creation = $scope.form.invoices[i].date_creation && $scope.form.invoices[i].date_creation !== "0000-00-00" ? new Date($scope.form.invoices[i].date_creation) : "";
                    }

                    updateEcart();
                }
            });
        }

        $scope.type_payment_is_check = false ;
        function updateModality() {
            $scope.form.type_payment_label = "" ;

            angular.forEach($scope.modalities, function (modality) {
                if ((modality.id * 1) == ($scope.form.type_payment * 1)) {
                    if (modality.type_modality == 1) {
                        $scope.type_payment_is_check = true;
                    } else {
                        $scope.type_payment_is_check = false;
                    }
                    $scope.form.type_payment_label = modality.label;
                }
            });
        }

        function updateTotal() {
            var montant_total = 0;
            if ($scope.form.total) {
                montant_total = parseFloat($scope.form.total.replace(",", ".")) ;
            }

            var montant_a_deduire = montant_total ;

            for (var i = 0; i < $scope.form.invoices.length; i++) {

                if (montant_a_deduire >= $scope.form.invoices[i].due) {
                    $scope.form.invoices[i].amount_payment = ($scope.form.invoices[i].due*1).toFixed(2) ;
                    montant_a_deduire -= $scope.form.invoices[i].due ;

                } else if (montant_a_deduire != 0) {
                    $scope.form.invoices[i].amount_payment = montant_a_deduire.toFixed(2) ;
                    montant_a_deduire = 0 ;

                } else {
                    $scope.form.invoices[i].amount_payment = (0).toFixed(2) ;
                }
            }

            updateEcart() ;
        }

        $scope.ecart = 0 ;
        function updateEcart() {
            var montant_total = 0;
            if ($scope.form.total) {
                montant_total = parseFloat($scope.form.total.replace(",", ".")) ;
            }
            var cumul = 0 ;

            for (var i = 0; i < $scope.form.invoices.length; i++) {
                var montant_ligne = 0 ;
                if ($scope.form.invoices[i].amount_payment) {
                    montant_ligne = parseFloat($scope.form.invoices[i].amount_payment.replace(",", "."))
                }
                cumul += montant_ligne ;
            }
            $scope.ecart = montant_total-cumul ;

            checkForm();
        }
    }]);