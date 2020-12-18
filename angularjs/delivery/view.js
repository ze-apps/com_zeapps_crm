app.controller("ComZeappsCrmDeliveryViewCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "Upload", "crmTotal", "zeHooks", "toasts", "menu", "$uibModal",
    function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload, crmTotal, zeHooks, toasts, menu, $uibModal) {

        menu("com_ze_apps_sales", "com_zeapps_crm_delivery");

        $scope.$on("comZeappsCrm_triggerDeliveryHook", broadcast);
        $scope.hooks = zeHooks.get("comZeappsCrm_DeliveryHook");

        // to activate hook function
        $scope.hooksComZeappsCRM_DeliveryHeaderRightHook = zeHooks.get("comZeappsCRM_DeliveryHeaderRightHook");

        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        $scope.deliveryLineTplUrl = "/com_zeapps_crm/deliveries/form_line";
        $scope.deliveryCommentTplUrl = "/com_zeapps_crm/crm_commons/form_comment";
        $scope.deliveryActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";
        $scope.deliveryDocumentTplUrl = "/com_zeapps_crm/crm_commons/form_document";
        $scope.templateEdit = "/com_zeapps_crm/deliveries/form_modal";

        $scope.lines = [];

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: sortableStop
        };

        $scope.setTab = setTab;

        $scope.back = back;
        $scope.first_delivery = first_delivery;
        $scope.previous_delivery = previous_delivery;
        $scope.next_delivery = next_delivery;
        $scope.last_delivery = last_delivery;

        $scope.updateStatus = updateStatus;
        $scope.updateDelivery = updateDelivery;
        $scope.transform = transform;
        $scope.finalize = finalize;

        $scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;
        $scope.addLine = addLine;
        $scope.editLine = editLine;
        $scope.addSubTotal = addSubTotal;
        $scope.addComment = addComment;
        $scope.editComment = editComment;

        $scope.deleteLine = deleteLine;

        $scope.subtotalHT = subtotalHT;
        $scope.subtotalTTC = subtotalTTC;

        $scope.addActivity = addActivity;
        $scope.editActivity = editActivity;
        $scope.deleteActivity = deleteActivity;

        $scope.addDocument = addDocument;
        $scope.editDocument = editDocument;
        $scope.deleteDocument = deleteDocument;

        $scope.print = print;
        $scope.sendByMail = sendByMail;


        //////////////////// INIT ////////////////////
        if ($rootScope.deliveries === undefined || $rootScope.deliveries.ids === undefined) {
            $rootScope.deliveries = {};
            $rootScope.deliveries.ids = [];
        } else {
            initNavigation();
        }

        /******* gestion de la tabs *********/
        $scope.navigationState = "body";
        if ($rootScope.comZeappsCrmLastShowTabDelivery) {
            $scope.navigationState = $rootScope.comZeappsCrmLastShowTabDelivery;
        }


        var _id_price_list_before_update = 0;
        var loadDocument = function (idDocument, next) {
            zhttp.crm.delivery.get(idDocument).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.delivery = response.data.delivery;
                    _id_price_list_before_update = $scope.delivery.id_price_list;

                    $scope.credits = response.data.credits;

                    $scope.tableTaxes = response.data.tableTaxes;

                    $scope.activities = response.data.activities || [];
                    angular.forEach($scope.activities, function (activity) {
                        activity.deadline = new Date(activity.deadline);
                    });

                    $scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function (document) {
                        document.date = new Date(document.date);
                    });


                    $scope.delivery.global_discount = parseFloat($scope.delivery.global_discount);
                    $scope.delivery.probability = parseFloat($scope.delivery.probability);
                    $scope.delivery.date_creation = new Date($scope.delivery.date_creation);
                    $scope.delivery.date_limit = new Date($scope.delivery.date_limit);

                    var i;

                    for (i = 0; i < $scope.activities.length; i++) {
                        $scope.activities[i].reminder = new Date($scope.activities[i].reminder);
                    }

                    for (i = 0; i < $scope.documents.length; i++) {
                        $scope.documents[i].created_at = new Date($scope.documents[i].created_at);
                    }

                    var lines = response.data.lines ;
                    if (!lines) {
                        lines = [];
                    }
                    angular.forEach(lines, function (line) {
                        line.price_unit = parseFloat(line.price_unit);
                        line.qty = parseFloat(line.qty);
                        line.discount = parseFloat(line.discount);
                    });
                    $scope.lines = lines;


                    // charge l'entreprise associée à la commande
                    $scope.company = null;
                    if ($scope.delivery.id_company) {
                        zhttp.contact.company.get($scope.delivery.id_company).then(function (response) {
                            if (response.data && response.data != "false") {
                                $scope.company = response.data.company;
                            }
                        });
                    }


                    // charge le contact associé à la commande
                    $scope.contact = null;
                    if ($scope.delivery.id_contact) {
                        zhttp.contact.contact.get($scope.delivery.id_contact).then(function (response) {
                            if (response.data && response.data != "false") {
                                $scope.contact = response.data.contact;
                            }
                        });
                    }


                    // call Callback
                    if (next) {
                        next();
                    }
                }
            });
        };


        if ($routeParams.id && $routeParams.id > 0) {
            loadDocument($routeParams.id);
        }

        // récupération des modèles d'email
        var listeModleEmails = [];
        zhttp.crm.model_email.get_all().then(function(response){
            if(response.data && response.data != "false"){
                listeModleEmails = response.data;
            }
        });

        //////////////////// FUNCTIONS ////////////////////
        function broadcast(event, data) {
            if (data.received) {
                code_exists++;
            } else if (data.found !== undefined && code_exists > 0) {
                if (data.found) {
                    code_exists = 0;
                } else {
                    code_exists--;
                    if (code_exists === 0) {
                        toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de données.");
                    }
                }
            } else {
                $rootScope.$broadcast("comZeappsCrm_dataDeliveryHook",
                    {
                        delivery: $scope.delivery
                    }
                );
            }
        }

        function broadcast_code(code) {
            $rootScope.$broadcast("comZeappsCrm_dataDeliveryHook",
                {
                    code: code
                }
            );
        }

        function setTab(tab) {
            $rootScope.comZeappsCrmLastShowTabDelivery = tab;
            $scope.navigationState = tab;
        }

        function back() {
            if ($rootScope.deliveries.src === undefined || $rootScope.deliveries.src === "deliveries") {
                $location.path("/ng/com_zeapps_crm/delivery/");
            } else if ($rootScope.deliveries.src === 'company') {
                $location.path("/ng/com_zeapps_contact/companies/" + $rootScope.deliveries.src_id);
            } else if ($rootScope.deliveries.src === 'contact') {
                $location.path("/ng/com_zeapps_contact/contacts/" + $rootScope.deliveries.src_id);
            }
        }

        function first_delivery() {
            if ($scope.delivery_first != 0) {
                $location.path("/ng/com_zeapps_crm/delivery/" + $scope.delivery_first);
            }
        }

        function previous_delivery() {
            if ($scope.delivery_previous != 0) {
                $location.path("/ng/com_zeapps_crm/delivery/" + $scope.delivery_previous);
            }
        }

        function next_delivery() {
            if ($scope.delivery_next) {
                $location.path("/ng/com_zeapps_crm/delivery/" + $scope.delivery_next);
            }
        }

        function last_delivery() {
            if ($scope.delivery_last) {
                $location.path("/ng/com_zeapps_crm/delivery/" + $scope.delivery_last);
            }
        }

        function updateStatus() {
            var data = {};

            data.id = $scope.delivery.id;
            data.status = $scope.delivery.status;

            var formatted_data = angular.toJson(data);

            zhttp.crm.delivery.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', "Le status du devis a bien été mis à jour.");
                    loadDocument($routeParams.id);
                } else {
                    toasts('danger', "Il y a eu une erreur lors de la mise a jour du status du devis");
                }
            });
        }

        function transform() {
            zeapps_modal.loadModule("com_zeapps_crm", "transform_document", {}, function (objReturn) {
                if (objReturn) {
                    var formatted_data = angular.toJson(objReturn);
                    zhttp.crm.delivery.transform($scope.delivery.id, formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            zeapps_modal.loadModule("com_zeapps_crm", "transformed_document", response.data);
                        }
                    });
                }
            });
        }

        function finalize() {


            if ((($scope.delivery.id_company && parseInt($scope.delivery.id_company, 10) != 0) || ($scope.delivery.id_contact && parseInt($scope.delivery.id_contact, 10) != 0))) {

                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: "/assets/angular/popupModalDeBase.html",
                    controller: "ZeAppsPopupModalDeBaseCtrl",
                    size: "lg",
                    resolve: {
                        titre: function () {
                            return "Confirmation";
                        },
                        msg: function () {
                            return "Souhaitez-vous clôture ce bon de livraison ?";
                        },
                        action_danger: function () {
                            return "Annuler";
                        },
                        action_primary: function () {
                            return false;
                        },
                        action_success: function () {
                            return "Je confirme la clôture";
                        }
                    }
                });

                modalInstance.result.then(function (selectedItem) {
                    if (selectedItem.action == "success") {
                        zhttp.crm.delivery.finalize($scope.delivery.id).then(function (response) {
                            if (response.data && response.data !== "false") {
                                if (response.data.error) {
                                    toasts('danger', response.data.error);
                                } else {
                                    $scope.delivery.numerotation = response.data.numerotation;
                                    $scope.delivery.finalized = '1';
                                    $scope.delivery.disabled = true;
                                }
                            }
                        });
                    }
                }, function () {
                    //console.log("rien");
                });

            } else {
                var msg_toast = "";

                if ((!$scope.delivery.id_company || parseInt($scope.delivery.id_company, 10) == 0) && (!$scope.delivery.id_contact || parseInt($scope.delivery.id_contact, 10) == 0)) {
                    if (msg_toast != "") {
                        msg_toast += ", ";
                    }
                    msg_toast += "une société ou un contact";
                }

                msg_toast = "Vous devez renseigner (" + msg_toast + ") pour pouvoir clôturer une facture";

                toasts('warning', msg_toast);
            }
        }

        function keyEventaddFromCode($event) {
            if ($event.which === 13) {
                addFromCode();
                setFocus($event.currentTarget);
            } else if ($event.which === 9) {
                addFromCode();
                setFocus($event.currentTarget);
            }
        }

        function setFocus(element) {
            setTimeout(function () {
                jQuery(element).focus();
            }, 500);
        }

        function addFromCode() {
            if ($scope.codeProduct !== "") {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        if (response.data.active) {
                            var line = {
                                id_delivery: $routeParams.id,
                                discount_prohibited: response.data.discount_prohibited,
                                type: response.data.type_product,
                                id_product: response.data.id,
                                ref: response.data.ref,
                                designation_title: response.data.name,
                                designation_desc: response.data.description,
                                qty: 1,
                                discount: 0.00,
                                price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                                id_taxe: parseFloat(response.data.id_taxe),
                                value_taxe: parseFloat(response.data.value_taxe),
                                accounting_number: response.data.accounting_number,
                                sort: $scope.lines.length,

                                maximum_discount_allowed: response.data.maximum_discount_allowed,
                                weight: response.data.weight,

                                total_ht: response.data.price_ht,
                                total_ttc: response.data.price_ttc,
                                price_unit_ht_indicated: response.data.price_ht,
                                price_unit_ttc_subline: response.data.price_ttc,


                                update_price_from_subline: response.data.update_price_from_subline,
                                show_subline: response.data.show_subline,

                                sublines: addSublines(response.data.sublines),

                                priceList: response.data.priceList,
                            };

                            // applique la grille de prix
                            if (response.data.priceList) {
                                angular.forEach(response.data.priceList, function (priceList) {
                                    if (priceList.id_price_list == $scope.delivery.id_price_list) {

                                        if (priceList.accounting_number && priceList.accounting_number != "") {
                                            line.accounting_number = priceList.accounting_number;
                                        }

                                        line.discount = priceList.percentage_discount;
                                        line.price_unit = priceList.price_ht;
                                        line.id_taxe = priceList.id_taxe;
                                        line.value_taxe = priceList.value_taxe;
                                    }
                                });
                            }

                            $scope.codeProduct = "";

                            var formatted_data = angular.toJson(line);
                            zhttp.crm.delivery.line.save(formatted_data).then(function (response) {
                                if (response.data && response.data != "false") {
                                    line.id = response.data;
                                    $scope.lines.push(line);
                                    updateDelivery(null, line.id);
                                }
                            });
                        } else {
                            toasts("danger", "Ce produit n'est plus actif");
                        }
                    } else {
                        if ($scope.hooks.length > 0) {
                            broadcast_code($scope.codeProduct);
                        } else {
                            toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de données.");
                        }
                    }
                });
            }
        }

        function addLine() {
            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function (objReturn) {
                if (objReturn) {
                    if (objReturn.active) {
                        var line = {
                            id_delivery: $routeParams.id,
                            type: objReturn.type_product,
                            id_product: objReturn.id,
                            ref: objReturn.ref,
                            designation_title: objReturn.name,
                            designation_desc: objReturn.description,
                            qty: 1,
                            discount: 0.00,
                            price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
                            id_taxe: parseFloat(objReturn.id_taxe),
                            value_taxe: parseFloat(objReturn.value_taxe),
                            accounting_number: objReturn.accounting_number,
                            sort: $scope.lines.length,

                            maximum_discount_allowed: objReturn.maximum_discount_allowed,
                            weight: objReturn.weight,

                            total_ht: objReturn.price_ht,
                            total_ttc: objReturn.price_ttc,
                            price_unit_ht_indicated: objReturn.price_ht,
                            price_unit_ttc_subline: objReturn.price_ttc,

                            update_price_from_subline: objReturn.update_price_from_subline,
                            show_subline: objReturn.show_subline,

                            sublines: addSublines(objReturn.sublines),

                            priceList: objReturn.priceList,
                        };

                        // applique la grille de prix
                        if (objReturn.priceList) {
                            angular.forEach(objReturn.priceList, function (priceList) {
                                if (priceList.id_price_list == $scope.delivery.id_price_list) {

                                    if (priceList.accounting_number && priceList.accounting_number != "") {
                                        line.accounting_number = priceList.accounting_number;
                                    }

                                    line.discount = priceList.percentage_discount;
                                    line.price_unit = priceList.price_ht;
                                    line.id_taxe = priceList.id_taxe;
                                    line.value_taxe = priceList.value_taxe;
                                }
                            });
                        }

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.delivery.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateDelivery(null, line.id);
                            }
                        });
                    } else {
                        toasts("danger", "Ce produit n'est plus actif");
                    }
                }
            });
        }

        function addSublines(sublines) {
            var dataSublines = [];

            if (sublines) {
                for (var i = 0; i < sublines.length; i++) {
                    var line = {
                        type: sublines[i].type_product,
                        id_product: sublines[i].id,
                        ref: sublines[i].ref,
                        designation_title: sublines[i].name,
                        designation_desc: sublines[i].description,
                        qty: 1,
                        discount: 0.00,
                        price_unit: parseFloat(sublines[i].price_ht) || parseFloat(sublines[i].price_ttc),
                        id_taxe: parseFloat(sublines[i].id_taxe),
                        value_taxe: parseFloat(sublines[i].value_taxe),
                        accounting_number: parseFloat(sublines[i].accounting_number),

                        maximum_discount_allowed: sublines[i].maximum_discount_allowed,
                        weight: sublines[i].weight,

                        total_ht: sublines[i].price_ht,
                        total_ttc: sublines[i].price_ttc,
                        price_unit_ht_indicated: sublines[i].price_ht,
                        price_unit_ttc_subline: sublines[i].price_ttc,

                        sublines: addSublines(sublines[i].sublines),

                        priceList: sublines[i].priceList,

                        update_price_from_subline: sublines[i].update_price_from_subline,
                        show_subline: sublines[i].show_subline,

                        sort: sublines[i].sort
                    };

                    // applique la grille de prix
                    if (sublines[i].priceList) {
                        angular.forEach(sublines[i].priceList, function (priceList) {
                            if (priceList.id_price_list == $scope.delivery.id_price_list) {

                                if (priceList.accounting_number && priceList.accounting_number != "") {
                                    line.accounting_number = priceList.accounting_number;
                                }

                                line.discount = priceList.percentage_discount;
                                line.price_unit = priceList.price_ht;
                                line.id_taxe = priceList.id_taxe;
                                line.value_taxe = priceList.value_taxe;
                            }
                        });
                    }

                    dataSublines.push(line);
                }
            }

            return dataSublines;
        }

        function addSubTotal() {
            var subTotal = {
                id_delivery: $routeParams.id,
                type: "subTotal",
                sort: $scope.lines.length
            };

            var formatted_data = angular.toJson(subTotal);
            zhttp.crm.delivery.line.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    subTotal.id = response.data;
                    $scope.lines.push(subTotal);
                    updateDelivery(null, subTotal.id);
                }
            });
        }

        function addComment(comment) {
            if (comment.designation_desc !== "") {
                var comment = {
                    id_delivery: $routeParams.id,
                    type: "comment",
                    designation_desc: comment.designation_desc,
                    sort: $scope.lines.length
                };

                var formatted_data = angular.toJson(comment);
                zhttp.crm.delivery.line.save(formatted_data).then(function (response) {
                    if (response.data && response.data != "false") {
                        comment.id = response.data;
                        $scope.lines.push(comment);
                    }
                });
            }
        }

        function editComment(comment) {
            var formatted_data = angular.toJson(comment);
            zhttp.crm.delivery.line.save(formatted_data);
        }

        function editLine(lineEdited) {
            updateDelivery(null, lineEdited.id);
        }

        function updateLine(line) {
            $rootScope.$broadcast("comZeappsCrm_deliveryEditTrigger",
                {
                    line: line
                }
            );
        }

        function deleteLine(line) {
            if ($scope.lines.indexOf(line) > -1) {
                zhttp.crm.delivery.line.del(line.id).then(function (response) {
                    if (response.data && response.data != "false") {
                        $scope.lines.splice($scope.lines.indexOf(line), 1);

                        $rootScope.$broadcast("comZeappsCrm_deliveryDeleteTrigger",
                            {
                                id_line: line.id
                            }
                        );

                        updateDelivery(null, line.id);
                    }
                });
            }
        }

        function subtotalHT(index) {
            return crmTotal.sub.HT($scope.lines, index);
        }

        function subtotalTTC(index) {
            return crmTotal.sub.TTC($scope.lines, index);
        }

        function updateDelivery(objOrderToSave, id_line_update) {
            if ($scope.delivery) {
                var nbUpdateDeliveryLine = 0 ;
                var miseAjourImmediateDelivery = true ;
                var updateOrderExecute = function() {
                    var data = $scope.delivery;

                    var y = data.date_creation.getFullYear();
                    var M = data.date_creation.getMonth();
                    var d = data.date_creation.getDate();

                    data.date_creation = new Date(Date.UTC(y, M, d));

                    var y = data.date_limit.getFullYear();
                    var M = data.date_limit.getMonth();
                    var d = data.date_limit.getDate();

                    data.date_limit = new Date(Date.UTC(y, M, d));

                    var formatted_data = angular.toJson(data);
                    zhttp.crm.delivery.save(formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            toasts('success', "Les informations du bon de livraison ont bien été mises a jour");
                        } else {
                            toasts('danger', "Il y a eu une erreur lors de la mise a jour des informations du bon de livraison");
                        }

                        // reaload document
                        loadDocument($routeParams.id);
                    });
                }





                $scope.delivery.global_discount = $scope.delivery.global_discount;

                angular.forEach($scope.lines, function (line) {
                    var updateLineData = false;

                    if (line.id && id_line_update == line.id) {
                        miseAjourImmediateDelivery = false ;
                    }

                    if (line.id && (!id_line_update || id_line_update == line.id)) {
                        updateLineData = true ;
                    }

                    // if must update price list
                    if (_id_price_list_before_update != $scope.delivery.id_price_list) {
                        if (line.priceList) {
                            updateLineData = true ;
                            angular.forEach(line.priceList, function (priceList) {
                                if (priceList.id_price_list == $scope.delivery.id_price_list) {
                                    if (priceList.accounting_number && priceList.accounting_number != "") {
                                        line.accounting_number = priceList.accounting_number;
                                    }

                                    line.maximum_discount_allowed = priceList.maximum_discount_allowed;
                                    line.discount = priceList.percentage_discount;
                                    line.price_unit = priceList.price_ht;
                                    line.id_taxe = priceList.id_taxe;
                                    line.value_taxe = priceList.value_taxe;
                                }
                            });
                        }
                    }



                    if (updateLineData) {
                        updateLine(line);
                    }

                    if (!id_line_update || updateLineData) {
                        nbUpdateDeliveryLine++ ;

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.delivery.line.save(formatted_data).then(function (response) {
                            if (!miseAjourImmediateDelivery) {
                                updateOrderExecute();
                            }
                        });
                    }
                });


                // to save price list state
                _id_price_list_before_update = $scope.delivery.id_price_list;
            }
        }

        function addActivity(activity) {
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            activity.id_delivery = $scope.delivery.id;
            var formatted_data = angular.toJson(activity);

            zhttp.crm.delivery.activity.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    response.data.deadline = new Date(response.data.deadline);
                    $scope.activities.push(response.data);
                }
            });
        }

        function editActivity(activity) {
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            var formatted_data = angular.toJson(activity);

            zhttp.crm.delivery.activity.save(formatted_data);
        }

        function deleteActivity(activity) {
            zhttp.crm.delivery.activity.del(activity.id).then(function (response) {
                if (response.status == 200) {
                    $scope.activities.splice($scope.activities.indexOf(activity), 1);
                }
            });
        }

        function addDocument(document) {
            Upload.upload({
                url: zhttp.crm.delivery.document.upload() + $scope.delivery.id,
                data: document
            }).then(
                function (response) {
                    $scope.progress = false;
                    if (response.data && response.data != "false") {
                        response.data.date = new Date(response.data.date);
                        response.data.id_user = $rootScope.user.id;
                        response.data.name_user = $rootScope.user.firstname[0] + '. ' + $rootScope.user.lastname;
                        $scope.documents.push(response.data);
                        toasts('success', "Les documents ont bien été mis en ligne");
                    } else {
                        toasts('danger', "Il y a eu une erreur lors de la mise en ligne des documents");
                    }
                }
            );
        }

        function editDocument(document) {
            Upload.upload({
                url: zhttp.crm.delivery.document.upload() + $scope.delivery.id,
                data: document
            }).then(
                function (response) {
                    $scope.progress = false;
                    if (response.data && response.data != "false") {
                        response.data.date = new Date(response.data.date);
                        toasts('success', "Les documents ont bien été mis à jour");
                    } else {
                        toasts('danger', "Il y a eu une erreur lors de la mise à jour des documents");
                    }
                }
            );
        }

        function deleteDocument(document) {
            zhttp.crm.delivery.document.del(document.id).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.documents.splice($scope.documents.indexOf(document), 1);
                }
            });
        }


        function sendByMail() {

            var options = {};

            var emailContact = "" ;
            if ($scope.contact && $scope.contact.email && $scope.contact.email.trim() != "") {
                emailContact = $scope.contact.email.trim() ;
            } else if ($scope.company && $scope.company.email && $scope.company.email.trim() != "") {
                emailContact = $scope.company.email.trim() ;
            }
            options.to = [emailContact] ;

            options.subject = "Bon de livraison : " + $scope.delivery.numerotation;

            options.content = "Bonjour,\n"
                + "\n"
                + "veuillez trouver ci-joint notre bon de livraison n° " + $scope.delivery.numerotation
                + "\n"
                + "Cordialement\n"
                + $scope.user.firstname + " " + $scope.user.lastname
            ;

            options.id_model_email = $scope.delivery.id_model_email;

            options.modules = [];
            options.modules.push({module: "com_zeapps_crm", id: "deliveries_" + $scope.delivery.id});

            if ($scope.delivery.id_contact) {
                options.modules.push({module: "com_zeapps_contact", id: "contacts_" + $scope.delivery.id_contact});
            }

            if ($scope.delivery.id_company) {
                options.modules.push({module: "com_zeapps_contact", id: "compagnies_" + $scope.delivery.id_company});
            }


            options.attachments = [];
            options.templates = [];
            options.data_templates = [];

            angular.forEach(listeModleEmails, function (template) {
                if (template.to_delivery) {
                    options.templates.push({
                        id: template.id,
                        name: template.name,
                        default_to: template.default_to,
                        subject: template.subject,
                        message: template.message,
                        attachments: angular.fromJson(template.attachments)
                    });
                }
            });


            options.data_templates.push({tag: "[type_doc]", value: "Bon de livraison"});
            options.data_templates.push({tag: "[company]", value: $scope.delivery.name_company});
            options.data_templates.push({tag: "[contact]", value: $scope.delivery.name_contact});
            options.data_templates.push({tag: "[number_doc]", value: $scope.delivery.numerotation});
            options.data_templates.push({tag: "[amount]", value: $scope.delivery.total_ttc});
            options.data_templates.push({tag: "[amount_without_taxes]", value: $scope.delivery.total_ht});
            options.data_templates.push({tag: "[reference]", value: $scope.delivery.reference_client});
            options.data_templates.push({tag: "[label_doc]", value: $scope.delivery.libelle});
            options.data_templates.push({tag: "[doc_manager]", value: $scope.delivery.name_user_account_manager});

            zhttp.crm.delivery.pdf.make($scope.delivery.id).then(function (response) {
                if (response.data && response.data != "false") {
                    var url_file = angular.fromJson(response.data);
                    options.attachments.push({file: url_file, url: "/" + url_file, name: "delivery.pdf"});

                    zeapps_modal.loadModule("zeapps", "email_writer", options, function (objReturn) {
                        if (objReturn) {
                        }
                    });
                }
            });
        }

        function print() {
            zhttp.crm.delivery.pdf.make($scope.delivery.id).then(function (response) {
                if (response.data && response.data != "false") {
                    window.document.location.href = "/" + angular.fromJson(response.data);
                }
            });
        }

        function initNavigation() {

            // calcul le nombre de résultat
            if ($rootScope.deliveries) {
                $scope.nb_deliveries = $rootScope.deliveries.ids.length;


                // calcul la position du résultat actuel
                $scope.delivery_order = 0;
                $scope.delivery_first = 0;
                $scope.delivery_previous = 0;
                $scope.delivery_next = 0;
                $scope.delivery_last = 0;

                for (var i = 0; i < $rootScope.deliveries.ids.length; i++) {
                    if ($rootScope.deliveries.ids[i] == $routeParams.id) {
                        $scope.delivery_order = i + 1;
                        if (i > 0) {
                            $scope.delivery_previous = $rootScope.deliveries.ids[i - 1];
                        }

                        if ((i + 1) < $rootScope.deliveries.ids.length) {
                            $scope.delivery_next = $rootScope.deliveries.ids[i + 1];
                        }
                    }
                }

                // recherche la première facture de la liste
                if ($rootScope.deliveries.ids[0] != undefined) {
                    if ($rootScope.deliveries.ids[0] != $routeParams.id) {
                        $scope.delivery_first = $rootScope.deliveries.ids[0];
                    }
                } else
                    $scope.delivery_first = 0;

                // recherche la dernière facture de la liste
                if ($rootScope.deliveries.ids[$rootScope.deliveries.ids.length - 1] != undefined) {
                    if ($rootScope.deliveries.ids[$rootScope.deliveries.ids.length - 1] != $routeParams.id) {
                        $scope.delivery_last = $rootScope.deliveries.ids[$rootScope.deliveries.ids.length - 1];
                    }
                } else
                    $scope.delivery_last = 0;
            } else {
                $scope.nb_deliveries = 0;
            }
        }

        function sortableStop(event, ui) {

            var data = {};
            var pushedLine = false;
            data.id = $(ui.item[0]).attr("data-id");

            for (var i = 0; i < $scope.lines.length; i++) {
                if ($scope.lines[i].id == data.id && !pushedLine) {
                    data.oldSort = $scope.lines[i].sort;
                    data.sort = i;
                    $scope.lines[i].sort = data.sort;
                    pushedLine = true;
                } else if (pushedLine) {
                    $scope.lines[i].sort++;
                }
            }

            var formatted_data = angular.toJson(data);
            zhttp.crm.delivery.line.position(formatted_data);
        }

    }]);