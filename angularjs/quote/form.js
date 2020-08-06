app.controller("ComZeappsCrmQuoteFormCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp",
	function ($scope, $routeParams, $rootScope, zhttp) {
        $scope.showCheckArea = false;
        updateModality();

        $scope.accountManagerHttp = zhttp.app.user;
        $scope.accountManagerFields = [
            {label:'Prénom',key:'firstname'},
            {label:'Nom',key:'lastname'}
        ];

        $scope.companyHttp = zhttp.contact.company;
        $scope.companyTplNew = '/com_zeapps_contact/companies/form_modal';
        $scope.companyFields = [
            {label:'Nom',key:'company_name'},
            {label:'Téléphone',key:'phone'},
            {label:'Ville',key:'billing_city'},
            {label:'Gestionnaire du compte',key:'name_user_account_manager'}
        ];

        $scope.contactHttp = zhttp.contact.contact;
        $scope.contactTplNew = '/com_zeapps_contact/contacts/form_modal';
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

        $scope.compagny_delivery_loaded = null;
        $scope.contact_delivery_loaded = null;


		$scope.updateDateLimit = updateDateLimit;
		$scope.loadAccountManager = loadAccountManager;

		$scope.loadCompany = loadCompany;
		$scope.loadContact = loadContact;

        $scope.loadCompanyDelivery = loadCompanyDelivery;
        $scope.loadContactDelivery = loadContactDelivery;

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
                $scope.form.status = 0;
                $scope.form.date_creation = new Date();
                $scope.form.date_limit = new Date();
                $scope.form.date_limit.setDate($scope.form.date_limit.getDate() + 30);

                if($routeParams.id_company !== undefined && $routeParams.id_company !== 0){
                    zhttp.contact.company.get($routeParams.id_company).then(function(response){
                        if(response.data && response.data != "false"){
                            loadCompany(response.data.company, true);
                        }
                    });
                }
                if($routeParams.id_contact !== undefined && $routeParams.id_contact !== 0){
                    zhttp.contact.contact.get($routeParams.id_contact).then(function(response){
                        if(response.data && response.data != "false"){
                            loadContact(response.data.contact, true);
                        }
                    });
                }
            } else {
                if ($scope.form.id_contact != 0) {
                    zhttp.contact.contact.get($scope.form.id_contact).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadContact(response.data.contact, false, function () {
                                if ($scope.form.id_company != 0) {
                                    zhttp.contact.company.get($scope.form.id_company).then(function (response) {
                                        if (response.data && response.data != "false") {
                                            loadCompany(response.data.company, false);
                                        }
                                    });
                                }
                            });
                        }
                    });
                } else if ($scope.form.id_company != 0) {
                    zhttp.contact.company.get($scope.form.id_company).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompany(response.data.company, false);
                        }
                    });
                }

                if ($scope.form.id_contact_delivery != 0) {
                    zhttp.contact.contact.get($scope.form.id_contact_delivery).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadContactDelivery(response.data.contact, false, function () {
                                if ($scope.form.id_company_delivery != 0) {
                                    zhttp.contact.company.get($scope.form.id_company_delivery).then(function (response) {
                                        if (response.data && response.data != "false") {
                                            loadCompanyDelivery(response.data.company, false);
                                        }
                                    });
                                }
                            });
                        }
                    });
                } else if ($scope.form.id_company_delivery != 0) {
                    zhttp.contact.company.get($scope.form.id_company_delivery).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompanyDelivery(response.data.company, false);
                        }
                    });
                }
            }

            setTimeout(function () {
                updateAdresse()
            }, 1500);
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
                $scope.form.id_user_account_manager = 0;
                $scope.form.name_user_account_manager = "";
            }
        }

        function loadCompany(company, resetAddress) {
            if (resetAddress == undefined) {
                resetAddress = true ;
            }
            console.log("Company : " + resetAddress);

            if (resetAddress) {
                $scope.form.id_company_address_billing = 0;
                $scope.form.id_contact_address_billing  = 0;
            }

            $scope.compagny_loaded = company ;
            $scope.compagny_addresses = [{id:0, company_name: "--"}] ;

            if (company) {
                if (company.sub_adresses) {
                    $scope.compagny_addresses = $scope.compagny_addresses.concat(company.sub_adresses);
                }

                $scope.form.check_issuer = company.company_name;

                $scope.form.id_company = company.id;
                $scope.form.name_company = company.company_name;
                $scope.form.accounting_number = company.accounting_number || $scope.form.accounting_number;

                if (resetAddress) {
                    $scope.form.global_discount = parseFloat(company.discount);
                    $scope.form.id_modality = company.id_modality;
                    $scope.form.label_modality = company.label_modality;

                    // applique la grille de prix
                    if (company.id_price_list) {
                        $scope.form.id_price_list = company.id_price_list;
                    }
                }
            } else {
                $scope.form.id_company = 0;
                $scope.form.name_company = "";
                $scope.form.id_company_address_billing = 0;
            }

            if (resetAddress) {
                updateAdresse();
            }
        }

        function loadContact(contact, resetAddress) {
            if (resetAddress == undefined) {
                resetAddress = true ;
            }
            console.log(resetAddress);

            if (resetAddress && $scope.form.id_company == 0) {
                $scope.form.id_company_address_billing = 0;
                $scope.form.id_contact_address_billing  = 0;
            }

            $scope.contact_loaded = contact ;
            $scope.contact_addresses = [{id:0, company_name: "--"}];

            if (contact) {
                if (contact.sub_adresses) {
                    $scope.contact_addresses = $scope.contact_addresses.concat(contact.sub_adresses);
                }

                $scope.form.id_contact = contact.id;
                $scope.form.name_contact = contact.last_name + " " + contact.first_name;
                $scope.form.accounting_number = $scope.form.accounting_number || contact.accounting_number;

                if (resetAddress) {
                    $scope.form.global_discount = parseFloat(contact.discount);
                    $scope.form.id_modality = contact.id_modality;
                    $scope.form.label_modality = contact.label_modality;
                }

                if (contact.id_company !== "0" && contact.id_company !== 0 && ($scope.form.id_company === undefined || $scope.form.id_company === 0)) {
                    zhttp.contact.company.get(contact.id_company).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompany(response.data.company, resetAddress);
                        }
                    })
                } else {
                    // applique la grille de prix
                    if (resetAddress) {
                        if (($scope.form.id_company === undefined || $scope.form.id_company === 0) && contact.id_price_list) {
                            $scope.form.id_price_list = contact.id_price_list;
                        }
                    }

                    $scope.form.check_issuer = contact.last_name + " " + contact.first_name;
                }
            } else {
                $scope.form.id_contact = 0;
                $scope.form.name_contact = "";
                $scope.form.id_contact_address_billing = 0;
            }

            if (resetAddress) {
                updateAdresse();
            }
        }

        function loadCompanyDelivery(company, resetAddress) {
            if (resetAddress == undefined) {
                resetAddress = true ;
            }

            if (resetAddress) {
                $scope.form.id_company_address_delivery = 0;
                $scope.form.id_contact_address_delivery = 0;
            }

            $scope.compagny_delivery_loaded = company ;
            $scope.compagny_delivery_addresses = [{id:0, company_name: "--"}] ;

            if (company) {
                if (company.sub_adresses) {
                    $scope.compagny_delivery_addresses = $scope.compagny_delivery_addresses.concat(company.sub_adresses);
                }

                $scope.form.id_company_delivery = company.id;
                $scope.form.name_company_delivery = company.company_name;
            } else {
                $scope.form.id_company_delivery = 0;
                $scope.form.name_company_delivery = "";
            }

            if (resetAddress) {
                updateAdresse();
            }
        }

        function loadContactDelivery(contact, resetAddress, next) {
            if (resetAddress == undefined) {
                resetAddress = true ;
            }

            if (resetAddress && $scope.form.id_company_delivery == 0) {
                $scope.form.id_company_address_delivery = 0;
                $scope.form.id_contact_address_delivery = 0;
            }

            $scope.contact_delivery_loaded = contact ;
            $scope.contact_delivery_addresses = [{id:0, company_name: "--"}];

            if (contact) {
                if (contact.sub_adresses) {
                    $scope.contact_delivery_addresses = $scope.contact_delivery_addresses.concat(contact.sub_adresses);
                }

                $scope.form.id_contact_delivery = contact.id;
                $scope.form.name_contact_delivery = contact.last_name + " " + contact.first_name;

                if (contact.id_company_delivery !== "0" && contact.id_company_delivery !== 0 && ($scope.form.id_company_delivery === undefined || $scope.form.id_company_delivery === 0)) {
                    zhttp.contact.company.get(contact.id_company_delivery).then(function (response) {
                        if (response.data && response.data != "false") {
                            loadCompanyDelivery(response.data.company);
                        }
                    })
                }
            } else {
                $scope.form.id_contact_delivery = 0;
                $scope.form.name_contact_delivery = "";
                $scope.form.id_contact_address_delivery = 0;
            }

            if (resetAddress) {
                updateAdresse();
            }

            if(next) {
                next();
            }
        }

        function updateAdresse() {
            $scope.form.billing_address_1 = "";
            $scope.form.billing_address_2 = "";
            $scope.form.billing_address_3 = "";
            $scope.form.billing_city = "";
            $scope.form.billing_zipcode = "";
            $scope.form.billing_state_id = "";
            $scope.form.billing_state = "";
            $scope.form.billing_country_id = "";
            $scope.form.billing_country_name = "";
            $scope.form.billing_address_full_text = "";

            $scope.form.delivery_address_1 = "";
            $scope.form.delivery_address_2 = "";
            $scope.form.delivery_address_3 = "";
            $scope.form.delivery_city = "";
            $scope.form.delivery_zipcode = "";
            $scope.form.delivery_state_id = "";
            $scope.form.delivery_state = "";
            $scope.form.delivery_country_id = "";
            $scope.form.delivery_country_name = "";
            $scope.form.delivery_address_full_text = "";

            zhttp.contact.address.get($scope.form.id_company, $scope.form.id_company_address_billing, $scope.form.id_contact, $scope.form.id_contact_address_billing, "billing").then(function (response) {
                if (response.status == 200) {
                    $scope.form.billing_address_full_text = response.data.full_text;

                    $scope.form.billing_address_1 = response.data.address_1;
                    $scope.form.billing_address_2 = response.data.address_2;
                    $scope.form.billing_address_3 = response.data.address_3;
                    $scope.form.billing_city = response.data.city;
                    $scope.form.billing_zipcode = response.data.zipcode;
                    $scope.form.billing_state_id = response.data.state_id;
                    $scope.form.billing_state = response.data.state;
                    $scope.form.billing_country_id = response.data.country_id;
                    $scope.form.billing_country_name = response.data.country_name;
                }
            });



            var id_company_delivery = $scope.form.id_company_delivery ;
            var id_company_address_delivery = $scope.form.id_company_address_delivery ;
            var id_contact_delivery = $scope.form.id_contact_delivery ;
            var id_contact_address_delivery = $scope.form.id_contact_address_delivery ;

            if (id_company_delivery == 0 && id_contact_delivery == 0) {
                id_company_delivery = $scope.form.id_company ;
                id_contact_delivery = $scope.form.id_contact ;
            }

            zhttp.contact.address.get(id_company_delivery, id_company_address_delivery, id_contact_delivery, id_contact_address_delivery, "delivery").then(function (response) {
                if (response.status == 200) {
                    $scope.form.delivery_address_full_text = response.data.full_text;

                    //$scope.form.delivery_name_company = response.data.company;
                    $scope.form.delivery_address_1 = response.data.address_1;
                    $scope.form.delivery_address_2 = response.data.address_2;
                    $scope.form.delivery_address_3 = response.data.address_3;
                    $scope.form.delivery_city = response.data.city;
                    $scope.form.delivery_zipcode = response.data.zipcode;
                    $scope.form.delivery_state_id = response.data.state_id;
                    $scope.form.delivery_state = response.data.state;
                    $scope.form.delivery_country_id = response.data.country_id;
                    $scope.form.delivery_country_name = response.data.country_name;
                }
            });
        }

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.$parent.form.accounting_number = accounting_number.number;
            } else {
                $scope.$parent.form.accounting_number = "";
            }
        }

        function updateModality(){
            $scope.showCheckArea = false;
            angular.forEach($scope.modalities, function(modality){
                if((modality.id * 1) == ($scope.form.id_modality * 1)){
                    $scope.form.label_modality = modality.label;

                    if (modality.situation >= 1 && modality.type_modality == 1) {
                        $scope.showCheckArea = true;
                    }
                }
            });
        }
	}]);