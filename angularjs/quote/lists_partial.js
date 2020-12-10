app.controller("ComZeappsCrmQuoteListsPartialCtrl", ["$scope", "$location", "$rootScope", "zeHttp", "$timeout", "toasts",
    function ($scope, $location, $rootScope, zhttp, $timeout, toasts) {

        if (!$rootScope.quotes) {
            $rootScope.quotes = {};
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
                    format: 'select',
                    field: 'id_user_account_manager',
                    type: 'text',
                    label: 'Manager',
                    options: []
                },
                {
                    format: 'select',
                    field: 'status',
                    type: 'text',
                    label: 'Statut',
                    options: []
                },
                {
                    format: 'select',
                    field: 'probability >=',
                    type: 'text',
                    label: 'Probabilité >=',
                    options: [
                        {id:10, label:"10%"},
                        {id:20, label:"20%"},
                        {id:30, label:"30%"},
                        {id:40, label:"40%"},
                        {id:50, label:"50%"},
                        {id:60, label:"60%"},
                        {id:70, label:"70%"},
                        {id:80, label:"80%"},
                        {id:90, label:"90%"},
                        {id:100, label:"100%"}
                    ]
                },
                {
                    format: 'select',
                    field: 'probability <=',
                    type: 'text',
                    label: 'Probabilité <=',
                    options: [
                        {id:10, label:"10%"},
                        {id:20, label:"20%"},
                        {id:30, label:"30%"},
                        {id:40, label:"40%"},
                        {id:50, label:"50%"},
                        {id:60, label:"60%"},
                        {id:70, label:"70%"},
                        {id:80, label:"80%"},
                        {id:90, label:"90%"},
                        {id:100, label:"100%"}
                    ]
                },
                {
                    format: 'input',
                    field: 'date_activite >=',
                    type: 'date',
                    label: 'Date prochaine : Début',
                    size: 3
                },
                {
                    format: 'input',
                    field: 'date_activite <=',
                    type: 'date',
                    label: 'Fin',
                    size: 3
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
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;
        $scope.total = 0;
        $scope.templateQuote = '/com_zeapps_crm/quotes/form_modal';


        // remonte la liste des managers
        $scope.filters.main[4].options = [];
        zhttp.app.user.all().then(function (response) {
            if (response.data && response.data != "false") {
                angular.forEach(response.data, function (user) {
                    $scope.filters.main[4].options.push({ id: user.id, label: user.firstname + " " + user.lastname });
                });
            }
        });

        // remonte le statut dans les filtres
        $scope.status_quote = [];
        $scope.filters.main[5].options = [];
        zhttp.crm.statuts.getAll().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.status_quote = response.data ;
                angular.forEach(response.data, function (status) {
                    $scope.filters.main[5].options.push({ id: status.id, label: status.label });
                });
            }
        });


        









        var src = "quotes";
        var src_id = 0;

        $scope.loadList = loadList;
        $scope.goTo = goTo;
        $scope.add = add;
        $scope.edit = edit;
        $scope.delete = del;

        $scope.$on("comZeappsContact_dataEntrepriseHook", function (event, data) {
            if ($scope.id_company !== data.id_company) {
                $scope.id_company = data.id_company;
                src = "company";
                src_id = data.id_company;

                loadList(true);
            }
        });
        $scope.$emit("comZeappsContact_triggerEntrepriseHook", {});

        $scope.$on("comZeappsContact_dataContactHook", function (event, data) {
            if ($scope.id_contact !== data.id_contact) {
                $scope.id_contact = data.id_contact;
                $scope.id_company = data.id_company;
                src = "contact";
                src_id = data.id_contact;

                loadList(true);
            }
        });
        $scope.$emit("comZeappsContact_triggerContactHook", {});




        $scope.status = [];
        zhttp.crm.statuts.getAll().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.status = response.data;
            }
        });

        $scope.showStatus = function (id_statut) {
            var label_statut = "" ;

            angular.forEach($scope.status, function (status) {
                if (status.id == id_statut) {
                    label_statut = status.label ;
                }
            });

            return label_statut;
        };



        $scope.updateProbability = function (quote) {
            var data = {};
            data.id = quote.id;
            data.probability = quote.probability;
            var formatted_data = angular.toJson(data);

            zhttp.crm.quote.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', "Le status du devis a bien été mis à jour.");
                } else {
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour du status du devis");
                }
            });
        };


        $scope.updateStatus = function (quote) {
            var data = {};
            data.id = quote.id;
            data.status = quote.status;
            var formatted_data = angular.toJson(data);

            zhttp.crm.quote.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', "Le status du devis a bien été mis à jour.");
                } else {
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour du status du devis");
                }
            });
        };

        






        $timeout(function () { // Making sure the default call is only triggered after the potential broadcast from a hook
            if (src_id === 0) {
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
            var arrayFieldNumeric = ["total_ht >", "total_ht <", "total_ttc >", "total_ttc <"] ;
            for (var i_arrayFieldNumeric = 0 ; i_arrayFieldNumeric < arrayFieldNumeric.length ; i_arrayFieldNumeric++) {
                if (filtre[arrayFieldNumeric[i_arrayFieldNumeric]] != undefined) {
                    filtre[arrayFieldNumeric[i_arrayFieldNumeric]] = filtre[arrayFieldNumeric[i_arrayFieldNumeric]].replace(",", ".").replace(" ", "") ;
                }
            }

            var formatted_filters = angular.toJson(filtre);
            zhttp.crm.quote.get_all(src_id, src, $scope.pageSize, offset, context, formatted_filters).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.quotes = response.data.quotes;

                    for (var i = 0; i < $scope.quotes.length; i++) {
                        $scope.quotes[i].date_creation = $scope.quotes[i].date_creation !== "0000-00-00 00:00:00" ? new Date($scope.quotes[i].date_creation) : 0;
                        $scope.quotes[i].date_limit = $scope.quotes[i].date_limit !== "0000-00-00 00:00:00" ? new Date($scope.quotes[i].date_limit) : 0;
                        $scope.quotes[i].global_discount = parseFloat($scope.quotes[i].global_discount);
                        $scope.quotes[i].probability = parseFloat($scope.quotes[i].probability);
                        $scope.quotes[i].activities = [];
                        $scope.quotes[i].activities_color = "label-default";
                        $scope.quotes[i].activities_next_date = "";
                        zhttp.crm.quote.activity.getAll($scope.quotes[i].id).then(function (responseActivite) {
                            if (responseActivite.data && responseActivite.data != "false") {
                                if (responseActivite.data.length >= 1) {
                                    var idQuoteToUpdate = responseActivite.data[0].id_quote
                                    for (var iQuote = 0; iQuote < $scope.quotes.length; iQuote++) {
                                        if ($scope.quotes[iQuote].id == idQuoteToUpdate) {
                                            $scope.quotes[iQuote].activities = responseActivite.data;
                                            
                                            var contenuPopover = "" ;

                                            // verifie s'il y a des activités à faire
                                            angular.forEach(responseActivite.data, function (activiteCheck) {
                                                if (activiteCheck.status == "A faire") {
                                                    $scope.quotes[iQuote].activities_color = "label-success";
                                                    var dActivite = new Date(Date.parse(activiteCheck.deadline));
                                                    if ($scope.quotes[iQuote].activities_next_date == "" || $scope.quotes[iQuote].activities_next_date > dActivite) {
                                                        $scope.quotes[iQuote].activities_next_date = dActivite;
                                                    }
                                                }

                                                contenuPopover += "<div style='padding:5px 0px;border-bottom:solid 1px #cccccc'>" ;
                                                if (activiteCheck.status == "A faire") {
                                                    contenuPopover += "<i class='fas fa-clock text-dark'></i> ";
                                                } else {
                                                    contenuPopover += "<i class='fas fa-check-circle text-success'></i> ";
                                                }
                                                
                                                var dActivite = new Date(Date.parse(activiteCheck.deadline));
                                                var dateFR = new Intl.DateTimeFormat('fr').format(dActivite);
                                                contenuPopover += dateFR + " : " + activiteCheck.libelle;
                                                contenuPopover += "</div>";
                                            });

                                            $scope.quotes[iQuote].popover = contenuPopover ;


                                            $('[data-trigger="hover"]').popover({html:true});
                                        }
                                    }
                                }
                            }
                        });
                    }

                    $scope.total = response.data.total;

                    $rootScope.quotes.ids = response.data.ids;
                    $rootScope.quotes.src_id = src_id;
                    $rootScope.quotes.src = src;
                }
            });
        }

        function goTo(id) {
            $location.url('/ng/com_zeapps_crm/quote/' + id);
        }

        function add(quote) {
            var data = quote;

            if (data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            } else {
                data.date_creation = 0;
            }

            if (data.date_limit) {
                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));
            } else {
                data.date_limit = 0;
            }

            var formatted_data = angular.toJson(data);
            zhttp.crm.quote.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    $rootScope.quotes.ids.unshift(response.data);
                    $location.url("/ng/com_zeapps_crm/quote/" + response.data);
                }
            });
        }

        function edit(quote) {
            var data = quote;

            if (data.date_creation) {
                var y = data.date_creation.getFullYear();
                var M = data.date_creation.getMonth();
                var d = data.date_creation.getDate();

                data.date_creation = new Date(Date.UTC(y, M, d));
            } else {
                data.date_creation = 0;
            }

            if (data.date_limit) {
                var y = data.date_limit.getFullYear();
                var M = data.date_limit.getMonth();
                var d = data.date_limit.getDate();

                data.date_limit = new Date(Date.UTC(y, M, d));
            } else {
                data.date_limit = 0;
            }

            var formatted_data = angular.toJson(data);

            zhttp.crm.quote.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', "Les informations du devis ont bien été mises a jour");
                } else {
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du devis");
                }
            });
        }

        function del(quote) {
            zhttp.crm.quote.del(quote.id).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.quotes.splice($scope.quotes.indexOf(quote), 1);
                    $rootScope.quotes.ids.splice($rootScope.quotes.ids.indexOf(quote.id), 1);
                }
            });
        }
    }]);