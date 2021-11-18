app.controller("ComZeappsCrmOrderViewCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "Upload", "crmTotal", "zeHooks", "toasts", "menu", "$uibModal",
    function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload, crmTotal, zeHooks, toasts, menu, $uibModal) {

        menu("com_ze_apps_sales", "com_zeapps_crm_order");

        // call all module to get function to control data on finalize
        var listControlFinalize = [];
        zeappsBroadcast.emit("ComZeappsCrmOrderFinalizeControlFunction", {listControlFinalize: listControlFinalize});


        var code_exists = 0;
        var code = "";

        $scope.$on("comZeappsCrm_triggerOrderHook", broadcast);
        $scope.$on("comZeappsCrm_triggerOrderAddLineHook", broadcastOrderAddLineHook);


        // to activate hook function
        $scope.hooksComZeappsCRM_OrderBtnTopBodyHook = zeHooks.get("comZeappsCRM_OrderBtnTopBodyHook");
        $scope.hooksComZeappsCRM_OrderHeaderRightHook = zeHooks.get("comZeappsCRM_OrderHeaderRightHook");



        $scope.qteCodeProduct = 1 ;
        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        $scope.orderLineTplUrl = "/com_zeapps_crm/orders/form_line";
        $scope.orderCommentTplUrl = "/com_zeapps_crm/crm_commons/form_comment";
        $scope.orderActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";
        $scope.orderDocumentTplUrl = "/com_zeapps_crm/crm_commons/form_document";
        $scope.templateEdit = "/com_zeapps_crm/orders/form_modal";

        $scope.lines = [];

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: sortableStop
        };


        $scope.setTab = setTab;

        $scope.back = back;
        $scope.first_order = first_order;
        $scope.previous_order = previous_order;
        $scope.next_order = next_order;
        $scope.last_order = last_order;

        $scope.updateStatus = updateStatus;
        $scope.updateOrder = updateOrder;
        $scope.transform = transform;
        $scope.finalize = finalize;

        $scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;
        $scope.keyEventaddFromCodeQte = keyEventaddFromCodeQte;
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


        $scope.editLigneSpecial = editLigneSpecial ;


        function editLigneSpecial(line) {
            // emit broadcast at finalize
            zeappsBroadcast.emit("ComZeappsCrmOrderEditSpecialLine", {
                "order": $scope.order,
                "zeapps_modal": zeapps_modal,
                "line": line,
                "scope": $scope
            });
        };


        //////////////////// INIT ////////////////////
        if ($rootScope.orders === undefined || $rootScope.orders.ids === undefined) {
            $rootScope.orders = {};
            $rootScope.orders.ids = [];
        } else {
            initNavigation();
        }

        /******* gestion de la tabs *********/
        $scope.navigationState = "body";
        if ($rootScope.comZeappsCrmLastShowTabOrder) {
            $scope.navigationState = $rootScope.comZeappsCrmLastShowTabOrder;
        }


        var _id_price_list_before_update = 0;
        var loadDocument = function (idDocument, next) {
            zhttp.crm.order.get(idDocument).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.order = response.data.order;
                    _id_price_list_before_update = $scope.order.id_price_list;

                    $scope.credits = response.data.credits;

                    $scope.tableTaxes = response.data.tableTaxes;

                    $scope.activities = response.data.activities || [];
                    angular.forEach($scope.activities, function (activity) {
                        activity.deadline = new Date(activity.deadline);
                    });

                    $scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function (document) {
                        document.created_at = new Date(document.created_at);
                    });

                    $scope.order.global_discount = parseFloat($scope.order.global_discount);
                    $scope.order.probability = parseFloat($scope.order.probability);
                    $scope.order.date_creation = new Date($scope.order.date_creation);
                    $scope.order.date_limit = new Date($scope.order.date_limit);

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

                        // Recherche la quantité disponible en stock
                        updateQuantityAvailable(line);
                    });
                    $scope.lines = lines;


                    // charge l'entreprise associée à la commande
                    $scope.company = null;
                    if ($scope.order.id_company) {
                        zhttp.contact.company.get($scope.order.id_company).then(function (response) {
                            if (response.data && response.data != "false") {
                                $scope.company = response.data.company;
                            }
                        });
                    }


                    // charge le contact associé à la commande
                    $scope.contact = null;
                    if ($scope.order.id_contact) {
                        zhttp.contact.contact.get($scope.order.id_contact).then(function (response) {
                            if (response.data && response.data != "false") {
                                $scope.contact = response.data.contact;
                            }
                        });
                    }


                    // envoi les données aux hooks
                    $rootScope.$broadcast("comZeappsCrm_dataOrderHook",
                        {
                            order: $scope.order,
                            lines: $scope.lines,
                        }
                    );


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
                        toasts("danger", __t("No product with code ") + code + __t(" found in the database."));
                    }
                }
            } else {
                $rootScope.$broadcast("comZeappsCrm_dataOrderHook",
                    {
                        order: $scope.order,
                        lines: $scope.lines,
                    }
                );
            }
        }


        function updateQuantityAvailable(line) {
            if ($scope.order.finalized != 1) {
                zhttp.crm.product_stock.get(line.id_product, $scope.order.id_warehouse).then(function (response) {
                    for (const key in $scope.lines) {
                        if (Object.hasOwnProperty.call($scope.lines, key)) {
                            let element = $scope.lines[key];
                            if (element.id_product == line.id_product) {
                                element.qtyInStock = parseFloat(response.data.product_stock.qty);
                                element.typeProductStock = response.data.product_stock.type_product ;
                                $scope.lines[key] = element;
                            }
                        }
                    }
                });
            }
        }

        function broadcast_code(code) {
            $rootScope.$broadcast("comZeappsCrm_dataOrderHook",
                {
                    code: code
                }
            );
        }


        function broadcastOrderAddLineHook(event, line) {
            var isNewLine = true ;
            if (line.id) {
                isNewLine = false ;
            }

            var formatted_data = angular.toJson(line);
            zhttp.crm.order.line.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    if (isNewLine) {
                        line.id = response.data;
                        $scope.lines.push(line);

                        // recherche la quantité disponible en stock
                        updateQuantityAvailable(line);
                    }

                    updateOrder(null, line.id);
                }
            });
        }

        function setTab(tab) {
            $rootScope.comZeappsCrmLastShowTabOrder = tab;
            $scope.navigationState = tab;
        }

        function back() {
            if ($rootScope.orders.src === undefined || $rootScope.orders.src === "orders") {
                $location.path("/ng/com_zeapps_crm/order/");
            } else if ($rootScope.orders.src === 'company') {
                $location.path("/ng/com_zeapps_contact/companies/" + $rootScope.orders.src_id);
            } else if ($rootScope.orders.src === 'contact') {
                $location.path("/ng/com_zeapps_contact/contacts/" + $rootScope.orders.src_id);
            }
        }

        function first_order() {
            if ($scope.order_first != 0) {
                $location.path("/ng/com_zeapps_crm/order/" + $scope.order_first);
            }
        }

        function previous_order() {
            if ($scope.order_previous != 0) {
                $location.path("/ng/com_zeapps_crm/order/" + $scope.order_previous);
            }
        }

        function next_order() {
            if ($scope.order_next) {
                $location.path("/ng/com_zeapps_crm/order/" + $scope.order_next);
            }
        }

        function last_order() {
            if ($scope.order_last) {
                $location.path("/ng/com_zeapps_crm/order/" + $scope.order_last);
            }
        }

        function updateStatus() {
            var data = {};

            data.id = $scope.order.id;
            data.status = $scope.order.status;

            var formatted_data = angular.toJson(data);

            zhttp.crm.order.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    toasts('success', __t("The order status has been updated successfully."));
                    loadDocument($routeParams.id);
                } else {
                    toasts('danger', __t("There was an error updating the order status"));
                }
            });
        }

        function transform() {
            zeapps_modal.loadModule("com_zeapps_crm", "transform_document", {}, function (objReturn) {
                if (objReturn) {
                    var formatted_data = angular.toJson(objReturn);
                    zhttp.crm.order.transform($scope.order.id, formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            zeapps_modal.loadModule("com_zeapps_crm", "transformed_document", response.data);
                        }
                    });
                }
            });
        }

        function finalize() {
            var isValid = true;
            angular.forEach(listControlFinalize, function (controlFinalize) {
                if (controlFinalize($scope.order, toasts) == false) {
                    isValid = false;
                }
            });


            if (isValid) {
                if (($scope.order.id_modality || parseInt($scope.order.id_modality, 10) != 0) && (($scope.order.id_company && parseInt($scope.order.id_company, 10) != 0) || ($scope.order.id_contact && parseInt($scope.order.id_contact, 10) != 0))) {
                    var modalInstance = $uibModal.open({
                        animation: true,
                        templateUrl: "/assets/angular/popupModalDeBase.html",
                        controller: "ZeAppsPopupModalDeBaseCtrl",
                        size: "lg",
                        resolve: {
                            titre: function () {
                                return __t("Confirmation");
                            },
                            msg: function () {
                                return __t("Would you like to close this order?");
                            },
                            action_danger: function () {
                                return __t("Cancel");
                            },
                            action_primary: function () {
                                return false;
                            },
                            action_success: function () {
                                return __t("I confirm the closure");
                            }
                        }
                    });

                    modalInstance.result.then(function (selectedItem) {
                        if (selectedItem.action == "success") {
                            zhttp.crm.order.finalize($scope.order.id).then(function (response) {
                                if (response.data && response.data !== "false") {
                                    if (response.data.error) {
                                        toasts('danger', response.data.error);
                                    } else {
                                        $scope.order.numerotation = response.data.numerotation;
                                        $scope.order.final_pdf = response.data.final_pdf;
                                        $scope.order.finalized = '1';
                                        $scope.sortable.disabled = true;

                                        // emit broadcast at finalize
                                        zeappsBroadcast.emit("ComZeappsCrmOrderFinalize", {
                                            "order": $scope.order,
                                            "zeapps_modal": zeapps_modal
                                        });
                                    }
                                }
                            });
                        }
                    }, function () {
                    });
                } else {
                    var msg_toast = "";

                    if (!$scope.order.id_modality || parseInt($scope.order.id_modality, 10) == 0) {
                        if (msg_toast != "") {
                            msg_toast += ", ";
                        }
                        msg_toast += __t("a way topay");
                    }

                    if ((!$scope.order.id_company || parseInt($scope.order.id_company, 10) == 0) && (!$scope.order.id_contact || parseInt($scope.order.id_contact, 10) == 0)) {
                        if (msg_toast != "") {
                            msg_toast += ", ";
                        }
                        msg_toast += __t("a company or a contact");
                    }


                    msg_toast = __t("You must enter (") + msg_toast + __t(") to be able to close an order");


                    toasts('warning', msg_toast);
                }
            }
        }


        function keyEventaddFromCode($event) {
            if ($event.which === 13) {
                addFromCode();
                setFocus("comZeappsCrmQteCodeProduct", true);
            } else if ($event.which === 9) {
                addFromCode();
                setFocus("comZeappsCrmQteCodeProduct", true);
            }
        }

        function keyEventaddFromCodeQte($event) {
            if ($event.which === 13) {
                addFromCode(true);
                $scope.qteCodeProduct = 1 ;
                setFocus("comZeappsCrmCodeProduct", true);
            } else if ($event.which === 9) {
                addFromCode(true);
                $scope.qteCodeProduct = 1 ;
                setFocus("comZeappsCrmCodeProduct", true);
            }
        }

        function setFocus(element, isById) {
            setTimeout(function () {
                if (isById) {
                    jQuery("#" + element).focus();
                } else {
                    jQuery(element).focus();
                }
            }, 200);
        }

        function addFromCode(save) {
            var qte = convertFloat($scope.qteCodeProduct) ;
            if ($scope.codeProduct !== "") {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        if (save) {
                            if (response.data.active) {
                                var line = {
                                    id_order: $routeParams.id,
                                    type: response.data.type_product,
                                    discount_prohibited: response.data.discount_prohibited,
                                    id_product: response.data.id,
                                    ref: response.data.ref,
                                    designation_title: response.data.name,
                                    designation_desc: response.data.description,
                                    qty: qte,
                                    discount: 0.00,
                                    maximum_discount_allowed: response.data.maximum_discount_allowed,
                                    weight: response.data.weight,
                                    price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                                    id_taxe: parseFloat(response.data.id_taxe),
                                    value_taxe: parseFloat(response.data.value_taxe),
                                    accounting_number: response.data.accounting_number,
                                    sort: $scope.lines.length,

                                    total_ht: response.data.price_ht * qte,
                                    total_ttc: response.data.price_ttc * qte,
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
                                        if (priceList.id_price_list == $scope.order.id_price_list) {

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
                                zhttp.crm.order.line.save(formatted_data).then(function (response) {
                                    if (response.data && response.data != "false") {
                                        line.id = response.data;
                                        $scope.lines.push(line);
                                        updateOrder(null, line.id);

                                        // recherche la quantité disponible en stock
                                        updateQuantityAvailable(line);
                                    }
                                });
                            } else {
                                toasts("danger", __t("This product is no longer active"));
                            }
                        }
                    } else {
                        if ($scope.hooks && $scope.hooks.length > 0) {
                            broadcast_code($scope.codeProduct);
                        } else {
                            setFocus("comZeappsCrmCodeProduct", true);
                            toasts("danger", __t("No product with code ") + code + __t(" found in the database."));
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
                            id_order: $routeParams.id,
                            type: objReturn.type_product,
                            id_product: objReturn.id,
                            ref: objReturn.ref,
                            designation_title: objReturn.name,
                            designation_desc: objReturn.description,
                            qty: 1,
                            discount: 0.00,
                            price_unit: parseFloat(objReturn.price_ht) || parseFloat(objReturn.price_ttc),
                            id_taxe: objReturn.id_taxe,
                            value_taxe: parseFloat(objReturn.value_taxe),
                            accounting_number: parseFloat(objReturn.accounting_number),
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
                                if (priceList.id_price_list == $scope.order.id_price_list) {

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
                        zhttp.crm.order.line.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                line.id = response.data;
                                $scope.lines.push(line);
                                updateOrder(null, line.id);

                                // recherche la quantité disponible en stock
                                updateQuantityAvailable(line);
                            }
                        });
                    } else {
                        toasts("danger", __t("This product is no longer active"));
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
                            if (priceList.id_price_list == $scope.order.id_price_list) {

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
                id_order: $routeParams.id,
                type: "subTotal",
                sort: $scope.lines.length
            };

            var formatted_data = angular.toJson(subTotal);
            zhttp.crm.order.line.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    subTotal.id = response.data;
                    $scope.lines.push(subTotal);
                    updateOrder(null, subTotal.id);
                }
            });
        }

        function addComment(comment) {
            if (comment.designation_desc !== "") {
                var comment = {
                    id_order: $routeParams.id,
                    type: "comment",
                    designation_desc: comment.designation_desc,
                    sort: $scope.lines.length
                };

                var formatted_data = angular.toJson(comment);
                zhttp.crm.order.line.save(formatted_data).then(function (response) {
                    if (response.data && response.data != "false") {
                        comment.id = response.data;
                        $scope.lines.push(comment);
                    }
                });
            }
        }

        function editComment(comment) {
            var formatted_data = angular.toJson(comment);
            zhttp.crm.order.line.save(formatted_data);
        }

        function editLine(lineEdited) {
            updateOrder(null, lineEdited.id);
        }

        function updateLine(line) {
            $rootScope.$broadcast("comZeappsCrm_orderEditTrigger",
                {
                    line: line
                }
            );
        }

        function deleteLine(line) {
            if ($scope.lines.indexOf(line) > -1) {
                zhttp.crm.order.line.del(line.id).then(function (response) {
                    if (response.data && response.data != "false") {
                        $scope.lines.splice($scope.lines.indexOf(line), 1);

                        $rootScope.$broadcast("comZeappsCrm_orderDeleteTrigger",
                            {
                                id_line: line.id
                            }
                        );

                        updateOrder(null, line.id);
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

        function updateOrder(objOrderToSave, id_line_update) {
            if ($scope.order) {
                var nbUpdateOrderLine = 0 ;
                var miseAjourImmediateOrder = true ;
                var updateOrderExecute = function() {
                    var data = $scope.order;

                    var y = data.date_creation.getFullYear();
                    var M = data.date_creation.getMonth();
                    var d = data.date_creation.getDate();

                    data.date_creation = new Date(Date.UTC(y, M, d));

                    var y = data.date_limit.getFullYear();
                    var M = data.date_limit.getMonth();
                    var d = data.date_limit.getDate();

                    data.date_limit = new Date(Date.UTC(y, M, d));

                    var formatted_data = angular.toJson(data);
                    zhttp.crm.order.save(formatted_data).then(function (response) {
                        var messageAlert = "";
                        var typeAlert = "";
                        if (response.data && response.data != "false") {
                            messageAlert = __t("The order information has been updated");
                            typeAlert = "success";
                        } else {
                            messageAlert = __t("There was an error updating the order information");
                            typeAlert = "danger";
                        }

                        // reaload document
                        loadDocument($routeParams.id, function () {
                            toasts(typeAlert, messageAlert);
                        });
                    });
                }

                $scope.order.global_discount = $scope.order.global_discount;

                angular.forEach($scope.lines, function (line) {
                    var updateLineData = false;

                    if (line.id && id_line_update == line.id) {
                        miseAjourImmediateOrder = false ;
                    }

                    if (line.id && (!id_line_update || id_line_update == line.id)) {
                        updateLineData = true ;
                    }

                    // if must update price list
                    if (_id_price_list_before_update != $scope.order.id_price_list) {
                        if (line.priceList) {
                            updateLineData = true ;
                            angular.forEach(line.priceList, function (priceList) {
                                if (priceList.id_price_list == $scope.order.id_price_list) {
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
                        nbUpdateOrderLine++ ;

                        var formatted_data = angular.toJson(line);
                        zhttp.crm.order.line.save(formatted_data).then(function (response) {
                            if (!miseAjourImmediateOrder) {
                                updateOrderExecute();
                            }
                        });
                    }
                });


                // to save price list state
                _id_price_list_before_update = $scope.order.id_price_list;

                if (miseAjourImmediateOrder) {
                    updateOrderExecute();
                }
            }
        }

        function addActivity(activity) {
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            activity.id_order = $scope.order.id;
            var formatted_data = angular.toJson(activity);

            zhttp.crm.order.activity.save(formatted_data).then(function (response) {
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

            zhttp.crm.order.activity.save(formatted_data);
        }

        function deleteActivity(activity) {
            zhttp.crm.order.activity.del(activity.id).then(function (response) {
                if (response.status == 200) {
                    $scope.activities.splice($scope.activities.indexOf(activity), 1);
                }
            });
        }

        function addDocument(document) {
            document.file = document.files[0];
            document.id_user = $rootScope.user.id ;
            document.user_name = $rootScope.user.firstname[0] + '. ' + $rootScope.user.lastname ;
            Upload.upload({
                url: zhttp.crm.order.document.upload() + $scope.order.id,
                data: document
            }).then(
                function (response) {
                    $scope.progress = false;
                    if (response.data && response.data != "false") {
                        response.data.created_at = new Date(response.data.created_at);
                        response.data.id_user = $rootScope.user.id;
                        response.data.user_name = $rootScope.user.firstname[0] + '. ' + $rootScope.user.lastname;
                        $scope.documents.push(response.data);
                        toasts('success', __t("The documents have been uploaded"));
                    } else {
                        toasts('danger', __t("There was an error uploading the documents"));
                    }
                }
            );
        }

        function editDocument(document) {
            if (document.files) {
                document.file = document.files[0];
            }
            Upload.upload({
                url: zhttp.crm.order.document.upload() + $scope.order.id,
                data: document
            }).then(
                function (response) {
                    $scope.progress = false;
                    if (response.data && response.data != "false") {
                        
                        // remplace le contenu dans le tableau
                        for (let index = 0; index < $scope.documents.length; index++) {
                            let documentLoop = $scope.documents[index];
                            if (documentLoop.id == response.data.id) {
                                $scope.documents[index] = response.data ;
                            }
                        }
                        
                        toasts('success', __t("The documents have been updated"));
                    } else {
                        toasts('danger', __t("There was an error updating the documents"));
                    }
                }
            );
        }

        function deleteDocument(document) {
            zhttp.crm.order.document.del(document.id).then(function (response) {
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

            options.subject = __t("Order") + " : " + $scope.order.numerotation;

            options.content = __t("Hello") + ",\n"
                + "\n"
                + __t("please find enclosed our order no.") + $scope.order.numerotation
                + "\n"
                + __t("Cordially") + "\n"
                + $scope.user.firstname + " " + $scope.user.lastname
            ;

            options.id_model_email = $scope.order.id_model_email;

            options.modules = [];
            options.modules.push({module: "com_zeapps_crm", id: "orders_" + $scope.order.id});

            if ($scope.order.id_contact) {
                options.modules.push({module: "com_zeapps_contact", id: "contacts_" + $scope.order.id_contact});
            }

            if ($scope.order.id_company) {
                options.modules.push({module: "com_zeapps_contact", id: "compagnies_" + $scope.order.id_company});
            }


            options.attachments = [];
            options.templates = [];
            options.data_templates = [];

            angular.forEach(listeModleEmails, function (template) {
                if (template.to_order) {
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


            options.data_templates.push({tag: "[type_doc]", value: __t("Order")});
            options.data_templates.push({tag: "[company]", value: $scope.order.name_company});
            options.data_templates.push({tag: "[contact]", value: $scope.order.name_contact});
            options.data_templates.push({tag: "[number_doc]", value: $scope.order.numerotation});
            options.data_templates.push({tag: "[amount]", value: $scope.order.total_ttc});
            options.data_templates.push({tag: "[amount_without_taxes]", value: $scope.order.total_ht});
            options.data_templates.push({tag: "[reference]", value: $scope.order.reference_client});
            options.data_templates.push({tag: "[label_doc]", value: $scope.order.libelle});
            options.data_templates.push({tag: "[doc_manager]", value: $scope.order.name_user_account_manager});

            zhttp.crm.order.pdf.make($scope.order.id).then(function (response) {
                if (response.data && response.data != "false") {
                    let url_file = angular.fromJson(response.data);
                    let fileNamePdf = __t("order") + "-" + $scope.order.numerotation + ".pdf" ;
                    options.attachments.push({file: url_file, url: "/" + url_file, name: fileNamePdf});

                    // récupère les fichiers à ajouter par les autres modules
                    let dataSendEmailAttachment = {
                        idOrder: $scope.order.id,
                        zehttp: zhttp
                    };
                    zeappsBroadcast.emit(
                        "ComZeappsCrmOrderGetAttachments", 
                        dataSendEmailAttachment
                    );

                    if (dataSendEmailAttachment.attachments) {
                        dataSendEmailAttachment.attachments.forEach(element=>{
                            options.attachments.push(element);
                        });
                    }



                    zeapps_modal.loadModule("zeapps", "email_writer", options, function (objReturn) {
                        if (objReturn) {
                        }
                    });
                }
            });
        }

        function convertFloat(value) {
            if (value && typeof value == 'string') {
                if (!value.endsWith(',') && !value.endsWith('.')) {
                    value = value.replace(",", ".");
                    value = value * 1;
                }
            }

            return value;
        }

        function print() {
            zhttp.crm.order.pdf.make($scope.order.id).then(function (response) {
                if (response.data && response.data != "false") {
                    window.document.location.href = "/" + angular.fromJson(response.data);
                }
            });
        }

        function initNavigation() {

            // calcul le nombre de résultat
            if ($rootScope.orders) {
                $scope.nb_orders = $rootScope.orders.ids.length;


                // calcul la position du résultat actuel
                $scope.order_order = 0;
                $scope.order_first = 0;
                $scope.order_previous = 0;
                $scope.order_next = 0;
                $scope.order_last = 0;

                for (var i = 0; i < $rootScope.orders.ids.length; i++) {
                    if ($rootScope.orders.ids[i] == $routeParams.id) {
                        $scope.order_order = i + 1;
                        if (i > 0) {
                            $scope.order_previous = $rootScope.orders.ids[i - 1];
                        }

                        if ((i + 1) < $rootScope.orders.ids.length) {
                            $scope.order_next = $rootScope.orders.ids[i + 1];
                        }
                    }
                }

                // recherche la première facture de la liste
                if ($rootScope.orders.ids[0] != undefined) {
                    if ($rootScope.orders.ids[0] != $routeParams.id) {
                        $scope.order_first = $rootScope.orders.ids[0];
                    }
                } else
                    $scope.order_first = 0;

                // recherche la dernière facture de la liste
                if ($rootScope.orders.ids[$rootScope.orders.ids.length - 1] != undefined) {
                    if ($rootScope.orders.ids[$rootScope.orders.ids.length - 1] != $routeParams.id) {
                        $scope.order_last = $rootScope.orders.ids[$rootScope.orders.ids.length - 1];
                    }
                } else
                    $scope.order_last = 0;
            } else {
                $scope.nb_orders = 0;
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
            zhttp.crm.order.line.position(formatted_data);
        }

    }]);