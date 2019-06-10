app.controller("ComZeappsCrmInvoiceFormCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp",
	function ($scope, $routeParams, $rootScope, zhttp) {
        if ($scope.form.finalized == 1) {
            $scope.form.zeapps_modal_hide_save_btn = true ;
        }

        $scope.showCheckArea = false;
        updateModality();


        $scope.accountManagerHttp = zhttp.app.user;
        $scope.accountManagerFields = [
            {label:'Prénom',key:'firstname'},
            {label:'Nom',key:'lastname'}
        ];

        $scope.companyHttp = zhttp.contact.company;
        $scope.companyTplNew = '/com_zeapps_contact/companies/form_modal/';
        $scope.companyFields = [
            {label:'Nom',key:'company_name'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'billing_city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

        $scope.contactHttp = zhttp.contact.contact;
        $scope.contactTplNew = '/com_zeapps_contact/contacts/form_modal/';
        $scope.contactFields = [
            {label:'Nom',key:'last_name'},
            {label:'Prénom',key:'first_name'},
            {label:'Entreprise',key:'name_company'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_contact/accounting_numbers/form_modal';
        $scope.accountingNumberFields = [
            {label:'Numero',key:'number'},
            {label:'Libelle',key:'label'},
            {label:'Type',key:'type_label'}
        ];

        /******* gestion de la tabs *********/
        $scope.navigationState = "body";
        $scope.setTab = setTab;

        $scope.compagny_addresses = [{id:0, company_name: "--"}];
        $scope.contact_addresses = [{id:0, company_name: "--"}];

        $scope.compagny_loaded = null;
        $scope.contact_loaded = null;


		$scope.updateDateLimit = updateDateLimit;
		$scope.loadAccountManager = loadAccountManager;
		$scope.loadCompany = loadCompany;
		$scope.loadContact = loadContact;
        $scope.updateAdresse = updateAdresse;
        $scope.loadAccountingNumber = loadAccountingNumber;
        $scope.updateModality = updateModality;

		Initform();

		zhttp.crm.warehouse.get_all().then(function(response){
			if(response.data && response.data != "false"){
				$scope.warehouses = response.data;
			}
		});

        zhttp.crm.crm_origin.get_all().then(function(response){
            if(response.data && response.data != "false"){
                $scope.crm_origins = response.data;

                if ($scope.form.id === undefined) {
                    angular.forEach($scope.crm_origins, function (crm_origin) {
                        if (crm_origin.default_value == 1) {
                            $scope.form.id_origin = crm_origin.id ;
                        }
                    });
                }
            }
        });

        $scope.status = [];
        zhttp.crm.statuts.getAll().then(function(response){
            if(response.data && response.data != "false"){
                $scope.status = response.data;
            }
        });


        // charge la liste des grilles de prix
        $scope.price_lists = false;
        zhttp.crm.price_list.get_all().then(function (response) {
            if (response.status == 200) {
                $scope.price_lists = response.data;
            }
        });

		function Initform(){
			if($scope.form.id === undefined) {
			    $scope.form.id_user_account_manager = $rootScope.user.id;
                $scope.form.name_user_account_manager = $rootScope.user.firstname + " " + $rootScope.user.lastname;
                $scope.form.id_warehouse = $rootScope.user.id_warehouse;
                $scope.form.date_creation = new Date();
                $scope.form.date_limit = new Date();
                $scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);

                if($routeParams.id_company !== undefined && $routeParams.id_company !== 0){
                    zhttp.contact.company.get($routeParams.id_company).then(function(response){
                        if(response.data && response.data != "false"){
                            loadCompany(response.data.company);
                        }
                    });
                }
                if($routeParams.id_contact !== undefined && $routeParams.id_contact !== 0){
                    zhttp.contact.contact.get($routeParams.id_contact).then(function(response){
                        if(response.data && response.data != "false"){
                            loadContact(response.data.contact);
                        }
                    });
                }
            } else {
                if ($scope.form.id_contact != 0) {
                    zhttp.contact.contact.get($scope.form.id_contact).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadContact(response.data.contact, function () {
                                if ($scope.form.id_company != 0) {
                                    zhttp.contact.company.get($scope.form.id_company).then(function (response) {
                                        if (response.data && response.data != "false") {
                                            loadCompany(response.data.company);
                                        }
                                    });
                                }
                            });
                        }
                    });
                } else if ($scope.form.id_company != 0) {
                    zhttp.contact.company.get($scope.form.id_company).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompany(response.data.company);
                        }
                    });
                }
            }
		}

        function setTab(tab) {
            $scope.navigationState = tab;
        }

		function updateDateLimit(){
			$scope.form.date_limit = new Date($scope.form.date_creation);
			$scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);
		}

        function loadAccountManager(user) {
            if (user) {
                $scope.form.id_user_account_manager = user.id;
                $scope.form.name_user_account_manager = user.firstname + " " + user.lastname;
            } else {
                $scope.form.id_user_account_manager = "0";
                $scope.form.name_user_account_manager = "";
            }
        }

        function loadCompany(company) {
            $scope.compagny_loaded = company ;
            $scope.compagny_addresses = [{id:0, company_name: "--"}] ;

            if (company) {
                if (company.sub_adresses) {
                    $scope.compagny_addresses = $scope.compagny_addresses.concat(company.sub_adresses);
                }

                $scope.form.check_issuer = company.company_name;

                $scope.form.id_company = company.id;
                $scope.form.name_company = company.company_name;
                $scope.form.accounting_number = company.accounting_number || $scope.form.accounting_number;
                $scope.form.global_discount = parseFloat(company.discount) || $scope.form.global_discount;
                $scope.form.id_modality = company.id_modality || $scope.form.id_modality;
                $scope.form.label_modality = company.label_modality || $scope.form.label_modality;

                // applique la grille de prix
                if (company.id_price_list) {
                    $scope.form.id_price_list = company.id_price_list;
                }
            } else {
                $scope.form.id_company = "0";
                $scope.form.name_company = "";
                $scope.form.id_company_address_billing = 0;
            }

            updateAdresse();
        }

        function loadContact(contact) {
            $scope.contact_loaded = contact ;
            $scope.contact_addresses = [{id:0, company_name: "--"}];

            if (contact) {
                if (contact.sub_adresses) {
                    $scope.contact_addresses = $scope.contact_addresses.concat(contact.sub_adresses);
                }

                $scope.form.id_contact = contact.id;
                $scope.form.name_contact = contact.last_name + " " + contact.first_name;
                $scope.form.accounting_number = $scope.form.accounting_number || contact.accounting_number;
                $scope.form.global_discount = $scope.form.global_discount || parseFloat(contact.discount);
                $scope.form.id_modality = $scope.form.id_modality || contact.id_modality;
                $scope.form.label_modality = $scope.form.label_modality || contact.label_modality;

                if(contact.id_company !== "0" && contact.id_company !== 0 && ($scope.form.id_company === undefined || $scope.form.id_company === 0)){
					zhttp.contact.company.get(contact.id_company).then(function(response){
						if(response.data && response.data != "false"){
                            loadCompany(response.data.company);
						}
					})
				} else {
                    $scope.form.check_issuer = contact.last_name + " " + contact.first_name;

                    // applique la grille de prix
                    if (($scope.form.id_company === undefined || $scope.form.id_company === 0) && contact.id_price_list) {
                        $scope.form.id_price_list = contact.id_price_list;
                    }
                }
            } else {
                $scope.form.id_contact = "0";
                $scope.form.name_contact = "";
                $scope.form.id_contact_address_billing = 0;
            }

            updateAdresse();
        }

        function updateAdresse() {
            $scope.form.name_company = "";
            $scope.form.name_contact = "";
            $scope.form.delivery_name_company = "";
            $scope.form.delivery_name_contact = "";

            $scope.form.billing_address_1 = "";
            $scope.form.billing_address_2 = "";
            $scope.form.billing_address_3 = "";
            $scope.form.billing_city = "";
            $scope.form.billing_zipcode = "";
            $scope.form.billing_state_id = "";
            $scope.form.billing_state = "";
            $scope.form.billing_country_id = "";
            $scope.form.billing_country_name = "";

            $scope.form.delivery_address_1 = "";
            $scope.form.delivery_address_2 = "";
            $scope.form.delivery_address_3 = "";
            $scope.form.delivery_city = "";
            $scope.form.delivery_zipcode = "";
            $scope.form.delivery_state_id = "";
            $scope.form.delivery_state = "";
            $scope.form.delivery_country_id = "";
            $scope.form.delivery_country_name = "";


            if ($scope.form.id_company != 0) {

                if ($scope.form.id_company_address_billing) {
                    angular.forEach($scope.compagny_addresses, function (compagny_address) {
                        if (compagny_address.id == $scope.form.id_company_address_billing) {
                            $scope.form.name_company = compagny_address.company_name;
                            if (compagny_address.first_name != "" || compagny_address.last_name != "") {
                                $scope.form.name_contact = compagny_address.first_name + " " + compagny_address.last_name;
                            }
                            $scope.form.billing_address_1 = compagny_address.address_1 || "";
                            $scope.form.billing_address_2 = compagny_address.address_2 || "";
                            $scope.form.billing_address_3 = compagny_address.address_3 || "";
                            $scope.form.billing_city = compagny_address.city || "";
                            $scope.form.billing_zipcode = compagny_address.zipcode || "";
                            $scope.form.billing_state_id = compagny_address.state_id || 0;
                            $scope.form.billing_state = compagny_address.state || "";
                            $scope.form.billing_country_id = compagny_address.country_id || "";
                            $scope.form.billing_country_name = compagny_address.country_name || "";
                        }
                    });
                } else if ($scope.compagny_loaded && $scope.compagny_loaded.billing_address_1) {
                    $scope.form.name_company = $scope.compagny_loaded.company_name;
                    $scope.form.name_contact = "";
                    $scope.form.billing_address_1 = $scope.compagny_loaded.billing_address_1 || "";
                    $scope.form.billing_address_2 = $scope.compagny_loaded.billing_address_2 || "";
                    $scope.form.billing_address_3 = $scope.compagny_loaded.billing_address_3 || "";
                    $scope.form.billing_city = $scope.compagny_loaded.billing_city || "";
                    $scope.form.billing_zipcode = $scope.compagny_loaded.billing_zipcode || "";
                    $scope.form.billing_state_id = $scope.compagny_loaded.billing_state_id || 0;
                    $scope.form.billing_state = $scope.compagny_loaded.billing_state || "";
                    $scope.form.billing_country_id = $scope.compagny_loaded.billing_country_id || "";
                    $scope.form.billing_country_name = $scope.compagny_loaded.billing_country_name || "";
                }



                if ($scope.form.id_company_address_delivery) {
                    angular.forEach($scope.compagny_addresses, function (compagny_address) {
                        if (compagny_address.id == $scope.form.id_company_address_delivery) {
                            $scope.form.delivery_name_company = compagny_address.company_name;
                            if (compagny_address.first_name != "" || compagny_address.last_name != "") {
                                $scope.form.delivery_name_contact = compagny_address.first_name + " " + compagny_address.last_name;
                            }
                            $scope.form.delivery_address_1 = compagny_address.address_1 || "";
                            $scope.form.delivery_address_2 = compagny_address.address_2 || "";
                            $scope.form.delivery_address_3 = compagny_address.address_3 || "";
                            $scope.form.delivery_city = compagny_address.city || "";
                            $scope.form.delivery_zipcode = compagny_address.zipcode || "";
                            $scope.form.delivery_state_id = compagny_address.state_id || 0;
                            $scope.form.delivery_state = compagny_address.state || "";
                            $scope.form.delivery_country_id = compagny_address.country_id || "";
                            $scope.form.delivery_country_name = compagny_address.country_name || "";
                        }
                    });
                } else {
                    if ($scope.compagny_loaded && $scope.compagny_loaded.delivery_address_1) {
                        $scope.form.delivery_address_1 = $scope.compagny_loaded.delivery_address_1 || "";
                        $scope.form.delivery_address_2 = $scope.compagny_loaded.delivery_address_2 || "";
                        $scope.form.delivery_address_3 = $scope.compagny_loaded.delivery_address_3 || "";
                        $scope.form.delivery_city = $scope.compagny_loaded.delivery_city || "";
                        $scope.form.delivery_zipcode = $scope.compagny_loaded.delivery_zipcode || "";
                        $scope.form.delivery_state_id = $scope.compagny_loaded.delivery_state_id || "";
                        $scope.form.delivery_state = $scope.compagny_loaded.delivery_state || "";
                        $scope.form.delivery_country_id = $scope.compagny_loaded.delivery_country_id || "";
                        $scope.form.delivery_country_name = $scope.compagny_loaded.delivery_country_name || "";
                    } else if ($scope.compagny_loaded) {
                        $scope.form.delivery_address_1 = $scope.compagny_loaded.billing_address_1 || "";
                        $scope.form.delivery_address_2 = $scope.compagny_loaded.billing_address_2 || "";
                        $scope.form.delivery_address_3 = $scope.compagny_loaded.billing_address_3 || "";
                        $scope.form.delivery_city = $scope.compagny_loaded.billing_city || "";
                        $scope.form.delivery_zipcode = $scope.compagny_loaded.billing_zipcode || "";
                        $scope.form.delivery_state_id = $scope.compagny_loaded.billing_state_id || "";
                        $scope.form.delivery_state = $scope.compagny_loaded.billing_state || "";
                        $scope.form.delivery_country_id = $scope.compagny_loaded.billing_country_id || "";
                        $scope.form.delivery_country_name = $scope.compagny_loaded.billing_country_name || "";
                    }
                }




            } else if ($scope.form.id_contact != 0) {
                if ($scope.form.id_contact_address_billing != 0) {
                    angular.forEach($scope.contact_addresses, function (contact_address) {
                        if (contact_address.id == $scope.form.id_contact_address_billing) {
                            $scope.form.name_company = contact_address.company_name;
                            if (contact_address.first_name != "" || contact_address.last_name != "") {
                                $scope.form.name_contact = contact_address.first_name + " " + contact_address.last_name;
                            }
                            $scope.form.billing_address_1 = contact_address.address_1 || "";
                            $scope.form.billing_address_2 = contact_address.address_2 || "";
                            $scope.form.billing_address_3 = contact_address.address_3 || "";
                            $scope.form.billing_city = contact_address.city || "";
                            $scope.form.billing_zipcode = contact_address.zipcode || "";
                            $scope.form.billing_state_id = contact_address.state_id || 0;
                            $scope.form.billing_state = contact_address.state || "";
                            $scope.form.billing_country_id = contact_address.country_id || "";
                            $scope.form.billing_country_name = contact_address.country_name || "";
                        }
                    });
                } else if ($scope.contact_loaded) {
                    $scope.form.name_contact = $scope.contact_loaded.first_name + " " + $scope.contact_loaded.last_name;
                    $scope.form.billing_address_1 = $scope.contact_loaded.address_1 || "";
                    $scope.form.billing_address_2 = $scope.contact_loaded.address_2 || "";
                    $scope.form.billing_address_3 = $scope.contact_loaded.address_3 || "";
                    $scope.form.billing_city = $scope.contact_loaded.city || "";
                    $scope.form.billing_zipcode = $scope.contact_loaded.zipcode || "";
                    $scope.form.billing_state_id = $scope.contact_loaded.state || 0;
                    $scope.form.billing_state = $scope.contact_loaded.state || "";
                    $scope.form.billing_country_id = $scope.contact_loaded.country_id || 0;
                    $scope.form.billing_country_name = $scope.contact_loaded.country_name || "";
                }

                if ($scope.form.id_contact_address_delivery != 0) {
                    angular.forEach($scope.contact_addresses, function (contact_address) {
                        if (contact_address.id == $scope.form.id_contact_address_delivery) {
                            $scope.form.delivery_name_company = contact_address.company_name;
                            if (contact_address.first_name != "" || contact_address.last_name != "") {
                                $scope.form.delivery_name_contact = contact_address.first_name + " " + contact_address.last_name;
                            }
                            $scope.form.delivery_address_1 = contact_address.address_1 || "";
                            $scope.form.delivery_address_2 = contact_address.address_2 || "";
                            $scope.form.delivery_address_3 = contact_address.address_3 || "";
                            $scope.form.delivery_city = contact_address.city || "";
                            $scope.form.delivery_zipcode = contact_address.zipcode || "";
                            $scope.form.delivery_state_id = contact_address.state_id || 0;
                            $scope.form.delivery_state = contact_address.state || "";
                            $scope.form.delivery_country_id = contact_address.country_id || "";
                            $scope.form.delivery_country_name = contact_address.country_name || "";
                        }
                    });
                } else if ($scope.contact_loaded) {
                    $scope.form.delivery_name_contact = $scope.contact_loaded.first_name + " " + $scope.contact_loaded.last_name;
                    $scope.form.delivery_address_1 = $scope.contact_loaded.address_1 || "";
                    $scope.form.delivery_address_2 = $scope.contact_loaded.address_2 || "";
                    $scope.form.delivery_address_3 = $scope.contact_loaded.address_3 || "";
                    $scope.form.delivery_city = $scope.contact_loaded.city || "";
                    $scope.form.delivery_zipcode = $scope.contact_loaded.zipcode || "";
                    $scope.form.delivery_state_id = $scope.contact_loaded.state || 0;
                    $scope.form.delivery_state = $scope.contact_loaded.state || "";
                    $scope.form.delivery_country_id = $scope.contact_loaded.country_id || 0;
                    $scope.form.delivery_country_name = $scope.contact_loaded.country_name || "";
                }
            }
        }

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.$parent.form.accounting_number = accounting_number.number;
            } else {
                $scope.$parent.form.accounting_number = "";
            }
        }




        function updateModality() {
            $scope.showCheckArea = false;
            angular.forEach($scope.modalities, function (modality) {
                if (modality.id === $scope.form.id_modality) {
                    $scope.form.label_modality = modality.label;

                    if (modality.situation >= 1 && modality.type_modality == 1) {
                        $scope.showCheckArea = true;
                    }
                }
            });
        }
	}]);