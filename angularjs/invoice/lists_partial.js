app.controller("ComZeappsCrmInvoiceListsPartialCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts",
	function ($scope, $location, $rootScope, zhttp, $timeout, toasts) {

		if(!$rootScope.invoices) {
            $rootScope.invoices = {};
        }
		$scope.id_company = 0;
		$scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'numerotation LIKE',
                    type: 'text',
                    label: 'Numéro'
                },
                {
                    format: 'input',
                    field: 'libelle LIKE',
                    type: 'text',
                    label: 'Libellé'
                },
                {
                    format: 'input',
                    field: 'name_company LIKE',
                    type: 'text',
                    label: 'Entreprise'
                },
                {
                    format: 'input',
                    field: 'name_contact LIKE',
                    type: 'text',
                    label: 'Contact'
                },
                {
                    format: 'checkbox',
                    field: 'unpaid',
                    type: 'text',
                    label: 'Impayé'
                },
                {
                    format: 'select',
                    field: 'finalized',
                    type: 'text',
                    label: 'Statut',
                    options: [{id:0,label:"Ouverte"}, {id:1,label:"Clôturée"}]
                }
            ],
            secondaries: [
                {
                    format: 'input',
                    field: 'date_creation >=',
                    type: 'date',
                    label: 'Date de création : Début',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_creation <=',
                    type: 'date',
                    label: 'Fin',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_limit >=',
                    type: 'date',
                    label: 'Date limite : Début',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_limit <=',
                    type: 'date',
                    label: 'Fin',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ht >',
                    type: 'text',
                    label: 'Total HT : Supérieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ht <',
                    type: 'text',
                    label: 'Inférieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ttc >',
                    type: 'text',
                    label: 'Total TTC : Supérieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'total_ttc <',
                    type: 'text',
                    label: 'Inférieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'due >',
                    type: 'text',
                    label: 'Solde : Supérieur à',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'due <',
                    type: 'text',
                    label: 'Inférieur à',
                    size: 3
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templateInvoice = '/com_zeapps_crm/invoices/form_modal';

        var src = "invoices";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.goTo = goTo;
		$scope.add = add;
		$scope.edit = edit;
		$scope.delete = del;

		$scope.$on("comZeappsContact_dataEntrepriseHook", function(event, data) {
			if ($scope.id_company !== data.id_company){
				$scope.id_company = data.id_company;
				src = "company";
                src_id = data.id_company;

                loadList(true) ;
			}
		});
		$scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

		$scope.$on("comZeappsContact_dataContactHook", function(event, data) {
			if ($scope.id_contact !== data.id_contact){
				$scope.id_contact = data.id_contact;
				$scope.id_company = data.id_company;
                src = "contact";
                src_id = data.id_contact;

                loadList(true) ;
			}
		});
		$scope.$emit("comZeappsContact_triggerContactHook", {});

        $timeout(function(){ // Making sure the default call is only triggered after the potential broadcast from a hook
        	if(src_id === 0) {
                loadList(true);
            }
        }, 0);

        function loadList(context) {
            context = context || "";
            var offset = ($scope.page - 1) * $scope.pageSize;



            var filtre = {} ;
            angular.forEach($scope.filter_model, function (value, key) {
                filtre[key] = value ;
            });



            // convet date JS to YYYY-MM-DD
            var arrayFieldDate = ["date_creation >=", "date_creation <=", "date_limit >=", "date_limit <="] ;
            for (var i_arrayFieldDate = 0 ; i_arrayFieldDate < arrayFieldDate.length ; i_arrayFieldDate++) {
                if (filtre[arrayFieldDate[i_arrayFieldDate]] != undefined) {
                    filtre[arrayFieldDate[i_arrayFieldDate]] = filtre[arrayFieldDate[i_arrayFieldDate]].getFullYear() + "-" + (filtre[arrayFieldDate[i_arrayFieldDate]].getMonth() + 1) + "-" + filtre[arrayFieldDate[i_arrayFieldDate]].getDate();
                }
            }


            // convert , to . for numeric
            var arrayFieldNumeric = ["total_ht >", "total_ht <", "total_ttc >", "total_ttc <", "due >", "due <"] ;
            for (var i_arrayFieldNumeric = 0 ; i_arrayFieldNumeric < arrayFieldNumeric.length ; i_arrayFieldNumeric++) {
                if (filtre[arrayFieldNumeric[i_arrayFieldNumeric]] != undefined) {
                    filtre[arrayFieldNumeric[i_arrayFieldNumeric]] = filtre[arrayFieldNumeric[i_arrayFieldNumeric]].replace(",", ".").replace(" ", "") ;
                }
            }




            var formatted_filters = angular.toJson(filtre);
            zhttp.crm.invoice.get_all(src_id, src, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.invoices = response.data.invoices;

                    for (var i = 0; i < $scope.invoices.length; i++) {
                        $scope.invoices[i].date_creation = $scope.invoices[i].date_creation !== "0000-00-00 00:00:00" ? new Date($scope.invoices[i].date_creation) : 0;
                        $scope.invoices[i].date_limit = $scope.invoices[i].date_limit !== "0000-00-00 00:00:00" ? new Date($scope.invoices[i].date_limit) : 0;
                        $scope.invoices[i].global_discount = parseFloat($scope.invoices[i].global_discount);
                        $scope.invoices[i].probability = parseFloat($scope.invoices[i].probability);
                    }

                    $scope.total = response.data.total;

                    $rootScope.invoices.ids = response.data.ids;
                    $rootScope.invoices.src_id = src_id;
                    $rootScope.invoices.src = src;
                }
            });
        }

        function goTo(id){
            $location.url('/ng/com_zeapps_crm/invoice/'+id);
        }

        function add(invoice) {
            var data = invoice;

            if(data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_creation = 0;
            }

            if(data.date_limit) {
                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_limit = 0;
            }

            var formatted_data = angular.toJson(data);
            zhttp.crm.invoice.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    $rootScope.invoices.ids.unshift(response.data);
                    $location.url("/ng/com_zeapps_crm/invoice/" + response.data);
                }
            });
        }

        function edit(invoice){
            var data = invoice;

            if(data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_creation = 0;
            }

            if(data.date_limit) {
                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));
            }
            else{
                data.date_limit = 0;
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.invoice.save(formatted_data).then(function(response){
                if(response.data && response.data != "false"){
                    toasts('success', "Les informations du devis ont bien été mises a jour");
                }
                else{
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du devis");
                }
            });
        }

		function del(invoice){
			zhttp.crm.invoice.del(invoice.id).then(function(response){
				if(response.data && response.data != "false"){
                    $scope.invoices.splice($scope.invoices.indexOf(invoice), 1);
                    $rootScope.invoices.ids.splice($rootScope.invoices.ids.indexOf(invoice.id), 1);
				}
			});
		}


	}]);