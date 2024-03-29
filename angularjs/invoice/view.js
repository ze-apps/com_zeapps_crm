app.controller("ComZeappsCrmInvoiceViewCtrl", ["$scope", "$routeParams", "$location", "$rootScope", "zeHttp", "zeapps_modal", "Upload", "crmTotal", "zeHooks", "toasts", "menu", "$uibModal",
    function ($scope, $routeParams, $location, $rootScope, zhttp, zeapps_modal, Upload, crmTotal, zeHooks, toasts, menu, $uibModal) {

        menu("com_ze_apps_sales", "com_zeapps_crm_invoice");

        $scope.$on("comZeappsCrm_triggerInvoiceHook", broadcast);
        $scope.hooks = zeHooks.get("comZeappsCrm_InvoiceHook");

        // to activate hook function
        $scope.hooksComZeappsCRM_InvoiceHeaderRightHook = zeHooks.get("comZeappsCRM_InvoiceHeaderRightHook");

        $scope.progress = 0;
        $scope.activities = [];
        $scope.documents = [];

        $scope.invoiceLineTplUrl = "/com_zeapps_crm/invoices/form_line";
        $scope.invoiceCommentTplUrl = "/com_zeapps_crm/crm_commons/form_comment";
        $scope.invoiceActivityTplUrl = "/com_zeapps_crm/crm_commons/form_activity";
        $scope.invoiceDocumentTplUrl = "/com_zeapps_crm/crm_commons/form_document";
        $scope.templateEdit = "/com_zeapps_crm/invoices/form_modal";

        $scope.lines = [];

        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: sortableStop
        };

        $scope.setTab = setTab;

        $scope.goToPayment = goToPayment;
        $scope.back = back;
        $scope.first_invoice = first_invoice;
        $scope.previous_invoice = previous_invoice;
        $scope.next_invoice = next_invoice;
        $scope.last_invoice = last_invoice;

        $scope.updateInvoice = updateInvoice;
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
        if ($rootScope.invoices === undefined || $rootScope.invoices.ids === undefined) {
            $rootScope.invoices = {};
            $rootScope.invoices.ids = [];
        } else {
            initNavigation();
        }

        /******* gestion de la tabs *********/
        $scope.navigationState = "body";
        if ($rootScope.comZeappsCrmLastShowTabInvoice) {
            $scope.navigationState = $rootScope.comZeappsCrmLastShowTabInvoice;
        }


        var _id_price_list_before_update = 0;
        var loadDocument = function (idDocument, next) {
            zhttp.crm.invoice.get(idDocument).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.invoice = response.data.invoice;
                    _id_price_list_before_update = $scope.invoice.id_price_list;

                    $rootScope.$broadcast("comZeappsCrm_dataInvoiceHook",
                        {
                            invoice: $scope.invoice
                        }
                    );

                    $scope.credits = response.data.credits;

                    $scope.tableTaxes = response.data.tableTaxes;

                    $scope.activities = response.data.activities || [];
                    angular.forEach($scope.activities, function (activity) {
                        if (activity.deadline) {
                            activity.deadline = new Date(activity.deadline);
                        }
                    });

                    $scope.documents = response.data.documents || [];
                    angular.forEach($scope.documents, function (document) {
                        if (document.created_at) {
                            document.created_at = new Date(document.created_at);
                        }
                    });

                    $scope.invoice.global_discount = parseFloat($scope.invoice.global_discount);
                    $scope.invoice.probability = parseFloat($scope.invoice.probability);
                    $scope.invoice.date_creation = new Date($scope.invoice.date_creation);
                    $scope.invoice.date_limit = new Date($scope.invoice.date_limit);

                    var i;

                    for (i = 0; i < $scope.activities.length; i++) {
                        if ($scope.activities[i].reminder) {
                            $scope.activities[i].reminder = new Date($scope.activities[i].reminder);
                        }
                    }

                    for (i = 0; i < $scope.documents.length; i++) {
                        if ($scope.documents[i].created_at) {
                            $scope.documents[i].created_at = new Date($scope.documents[i].created_at);
                        }
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
                    if ($scope.invoice.id_company) {
                        zhttp.contact.company.get($scope.invoice.id_company).then(function (response) {
                            if (response.data && response.data != "false") {
                                $scope.company = response.data.company;
                            }
                        });
                    }


                    // charge le contact associé à la commande
                    $scope.contact = null;
                    if ($scope.invoice.id_contact) {
                        zhttp.contact.contact.get($scope.invoice.id_contact).then(function (response) {
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
                        toasts("danger", __t("No product with code ") + code + __t(" found in the database."));
                    }
                }
            } else {
                $rootScope.$broadcast("comZeappsCrm_dataInvoiceHook",
                    {
                        invoice: $scope.invoice
                    }
                );
            }
        }

        function broadcast_code(code) {
            $rootScope.$broadcast("comZeappsCrm_dataInvoiceHook",
                {
                    code: code
                }
            );
        }

        function setTab(tab) {
            $rootScope.comZeappsCrmLastShowTabInvoice = tab;
            $scope.navigationState = tab;
        }

        function goToPayment() {
            $location.url("/ng/com_zeapps_crm/credit_balances/" + $scope.invoice.id);
        }

        function back() {
            if ($rootScope.invoices.src === undefined || $rootScope.invoices.src === "invoices") {
                $location.path("/ng/com_zeapps_crm/invoice/");
            } else if ($rootScope.invoices.src === 'company') {
                $location.path("/ng/com_zeapps_contact/companies/" + $rootScope.invoices.src_id);
            } else if ($rootScope.invoices.src === 'contact') {
                $location.path("/ng/com_zeapps_contact/contacts/" + $rootScope.invoices.src_id);
            }
        }

        function first_invoice() {
            if ($scope.invoice_first != 0) {
                $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_first);
            }
        }

        function previous_invoice() {
            if ($scope.invoice_previous != 0) {
                $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_previous);
            }
        }

        function next_invoice() {
            if ($scope.invoice_next) {
                $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_next);
            }
        }

        function last_invoice() {
            if ($scope.invoice_last) {
                $location.path("/ng/com_zeapps_crm/invoice/" + $scope.invoice_last);
            }
        }

        function transform() {
            zeapps_modal.loadModule("com_zeapps_crm", "transform_document", {
                type_document: 'invoices',
                id: $scope.invoice.id
            }, function (objReturn) {
                if (objReturn) {
                    var formatted_data = angular.toJson(objReturn);
                    zhttp.crm.invoice.transform($scope.invoice.id, formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            zeapps_modal.loadModule("com_zeapps_crm", "transformed_document", response.data);
                        }
                    });
                }
            });
        }

        function finalize() {


            if (($scope.invoice.accounting_number && $scope.invoice.accounting_number != "") && ($scope.invoice.id_modality || parseInt($scope.invoice.id_modality, 10) != 0) && (($scope.invoice.id_company && parseInt($scope.invoice.id_company, 10) != 0) || ($scope.invoice.id_contact && parseInt($scope.invoice.id_contact, 10) != 0))) {

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
                            return __t("Would you like to close this invoice?");
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
                        zhttp.crm.invoice.finalize($scope.invoice.id).then(function (response) {
                            if (response.data && response.data !== "false") {
                                if (response.data.error) {
                                    toasts('danger', response.data.error);
                                } else {
                                    $scope.invoice.numerotation = response.data.numerotation;
                                    $scope.invoice.final_pdf = response.data.final_pdf;
                                    $scope.invoice.finalized = '1';
                                    $scope.sortable.disabled = true;
                                }
                            }
                        });
                    }
                }, function () {
                });


            } else {

                var msg_toast = "";

                if (!$scope.invoice.accounting_number || $scope.invoice.accounting_number == "") {
                    if (msg_toast != "") {
                        msg_toast += ", ";
                    }
                    msg_toast += __t("an accounting account");
                }

                if (!$scope.invoice.id_modality || parseInt($scope.invoice.id_modality, 10) == 0) {
                    if (msg_toast != "") {
                        msg_toast += ", ";
                    }
                    msg_toast += __t("a way topay");
                }

                if ((!$scope.invoice.id_company || parseInt($scope.invoice.id_company, 10) == 0) && (!$scope.invoice.id_contact || parseInt($scope.invoice.id_contact, 10) == 0)) {
                    if (msg_toast != "") {
                        msg_toast += ", ";
                    }
                    msg_toast += __t("a company or a contact");
                }


                msg_toast = __t("You must enter (") + msg_toast + __t(") to be able to close an invoice");


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
            if ($scope.codeProduct !== "" && parseInt($scope.invoice.finalized, 10) == 0) {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        if (response.data.active) {
                            var line = {
                                id_invoice: $routeParams.id,
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
                                    if (priceList.id_price_list == $scope.invoice.id_price_list) {

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
                            zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                                if (response.data && response.data != "false") {
                                    line.id = response.data;
                                    $scope.lines.push(line);
                                    updateInvoice(null, line.id);
                                }
                            });
                        } else {
                            toasts("danger", __t("This product is no longer active"));
                        }
                    } else {
                        if ($scope.hooks.length > 0) {
                            broadcast_code($scope.codeProduct);
                        } else {
                            toasts("danger", __t("No product with code ") + code + __t(" found in the database."));
                        }
                    }
                });
            }
        }

        function addLine() {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                // charge la modal de la liste de produit
                zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function (objReturn) {
                    if (objReturn) {
                        if (objReturn.active) {
                            var line = {
                                id_invoice: $routeParams.id,
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
                                    if (priceList.id_price_list == $scope.invoice.id_price_list) {

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
                            zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                                if (response.data && response.data != "false") {
                                    line.id = response.data;
                                    $scope.lines.push(line);
                                    updateInvoice(null, line.id);
                                }
                            });
                        } else {
                            toasts("danger", __t("This product is no longer active"));
                        }
                    }
                });
            }
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
                            if (priceList.id_price_list == $scope.invoice.id_price_list) {

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
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                var subTotal = {
                    id_invoice: $routeParams.id,
                    type: "subTotal",
                    sort: $scope.lines.length
                };

                var formatted_data = angular.toJson(subTotal);
                zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                    if (response.data && response.data != "false") {
                        subTotal.id = response.data;
                        $scope.lines.push(subTotal);
                        updateInvoice(null, subTotal.id);
                    }
                });
            }
        }

        function addComment(comment) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                if (comment.designation_desc !== "") {
                    var comment = {
                        id_invoice: $routeParams.id,
                        type: "comment",
                        designation_desc: comment.designation_desc,
                        sort: $scope.lines.length
                    };

                    var formatted_data = angular.toJson(comment);
                    zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                        if (response.data && response.data != "false") {
                            comment.id = response.data;
                            $scope.lines.push(comment);
                        }
                    });
                }
            }
        }

        function editComment(comment) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                var formatted_data = angular.toJson(comment);
                zhttp.crm.invoice.line.save(formatted_data);
            }
        }

        function editLine(lineEdited) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                updateInvoice(null, lineEdited.id);
            }
        }

        function updateLine(line) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                $rootScope.$broadcast("comZeappsCrm_invoiceEditTrigger",
                    {
                        line: line
                    }
                );
            }
        }

        function deleteLine(line) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                if ($scope.lines.indexOf(line) > -1) {
                    zhttp.crm.invoice.line.del(line.id).then(function (response) {
                        if (response.data && response.data != "false") {
                            $scope.lines.splice($scope.lines.indexOf(line), 1);

                            $rootScope.$broadcast("comZeappsCrm_invoiceDeleteTrigger",
                                {
                                    id_line: line.id
                                }
                            );

                            $rootScope.$broadcast("comZeappsCrm_dataInvoiceHook",
                                {
                                    invoice: $scope.invoice
                                }
                            );

                            updateInvoice(null, line.id);
                        }
                    });
                }
            }
        }

        function subtotalHT(index) {
            return crmTotal.sub.HT($scope.lines, index);
        }

        function subtotalTTC(index) {
            return crmTotal.sub.TTC($scope.lines, index);
        }

        function editInvoice(invoice) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                angular.forEach($scope.invoice, function (value, key) {
                    if (invoice[key])
                        $scope.invoice[key] = invoice[key];
                });

                updateInvoice();
            }
        }

        function updateInvoice(objOrderToSave, id_line_update) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
                if ($scope.invoice) {
                    var nbUpdateInvoiceLine = 0 ;
                    var miseAjourImmediateInvoice = true ;
                    var updateOrderExecute = function() {
                        var data = $scope.invoice;

                        var y = data.date_creation.getFullYear();
                        var M = data.date_creation.getMonth();
                        var d = data.date_creation.getDate();

                        data.date_creation = new Date(Date.UTC(y, M, d));

                        var y = data.date_limit.getFullYear();
                        var M = data.date_limit.getMonth();
                        var d = data.date_limit.getDate();

                        data.date_limit = new Date(Date.UTC(y, M, d));

                        var formatted_data = angular.toJson(data);
                        zhttp.crm.invoice.save(formatted_data).then(function (response) {
                            if (response.data && response.data != "false") {
                                toasts('success', __t("The information on the invoice has been updated"));
                            } else {
                                toasts('danger', __t("There was an error updating the invoice information"));
                            }
                            // reaload document
                            loadDocument($routeParams.id);
                        });
                    }





                    $scope.invoice.global_discount = $scope.invoice.global_discount;

                    angular.forEach($scope.lines, function (line) {
                        var updateLineData = false;

                        if (line.id && id_line_update == line.id) {
                            miseAjourImmediateInvoice = false ;
                        }

                        if (line.id && (!id_line_update || id_line_update == line.id)) {
                            updateLineData = true ;
                        }

                        // if must update price list
                        if (_id_price_list_before_update != $scope.invoice.id_price_list) {
                            if (line.priceList) {
                                updateLineData = true ;
                                angular.forEach(line.priceList, function (priceList) {
                                    if (priceList.id_price_list == $scope.invoice.id_price_list) {

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
                            nbUpdateInvoiceLine++ ;

                            var formatted_data = angular.toJson(line);
                            zhttp.crm.invoice.line.save(formatted_data).then(function (response) {
                                if (!miseAjourImmediateInvoice) {
                                    updateOrderExecute();
                                }
                            });
                        }
                    });


                    // to save price list state
                    _id_price_list_before_update = $scope.invoice.id_price_list;

                    if (miseAjourImmediateInvoice) {
                        updateOrderExecute();
                    }
                }
            }
        }

        function addActivity(activity) {
            var y = activity.deadline.getFullYear();
            var M = activity.deadline.getMonth();
            var d = activity.deadline.getDate();

            activity.deadline = new Date(Date.UTC(y, M, d));
            activity.id_invoice = $scope.invoice.id;
            var formatted_data = angular.toJson(activity);

            zhttp.crm.invoice.activity.save(formatted_data).then(function (response) {
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

            zhttp.crm.invoice.activity.save(formatted_data);
        }

        function deleteActivity(activity) {
            zhttp.crm.invoice.activity.del(activity.id).then(function (response) {
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
                url: zhttp.crm.invoice.document.upload() + $scope.invoice.id,
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
                url: zhttp.crm.invoice.document.upload() + $scope.invoice.id,
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
            zhttp.crm.invoice.document.del(document.id).then(function (response) {
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


            if (window.location.hostname == 'quiltmania-fr-crm.ze-apps.com') {
                options.subject = "Invoice / Facture / Factuur " + $scope.invoice.numerotation;

                options.content = "Bonjour,\n"
                + "Vous trouverez en annexe la facture de votre achat ou abonnement.\n"
                + "Nous restons à votre disposition pour tout renseignement supplémentaire\n"
                + "\n"
                + "Bien cordialement,\n"
                + $scope.user.firstname + " " + $scope.user.lastname + "\n"
                + "\n"
                + "**************************************************\n"
                + "\n"
                + "Hello,\n"
                + "You will find attached your invoice for your purchase or subscription.\n"
                + "We remain at your disposal for any further information\n"
                + "\n"
                + "Regards\n"
                + $scope.user.firstname + " " + $scope.user.lastname + "\n"
                + "\n"
                + "**************************************************\n"
                + "\n"
                + "Hallo,\n"
                + "In bijlage vindt u uw factuur voor uw aankoop of abonnement.\n"
                + "Wij blijven tot uw beschikking voor verdere informatie\n"
                + "\n"
                + "Met vriendelijke groeten\n"
                + $scope.user.firstname + " " + $scope.user.lastname
                ;
            } else if (window.location.hostname == 'quiltmania-inc-crm.ze-apps.com') {
                options.subject = "Invoice " + $scope.invoice.numerotation;

                options.content = "Hello,\n"
                + "You will find attached your invoice for your purchase or subscription.\n"
                + "We remain at your disposal for any further information\n"
                + "\n"
                + "Regards\n"
                + $scope.user.firstname + " " + $scope.user.lastname + "\n"
                ;
            } else {
                options.subject = __t("Bill: ") + $scope.invoice.numerotation;

                options.content = __t("Hello") + ",\n"
                    + "\n"
                    + __t("please find attached our invoice no.") + $scope.invoice.numerotation
                    + "\n"
                    + __t("Cordially") + "\n"
                    + $scope.user.firstname + " " + $scope.user.lastname
                ;
            }

            options.id_model_email = $scope.invoice.id_model_email;

            options.modules = [];
            options.modules.push({module: "com_zeapps_crm", id: "invoices_" + $scope.invoice.id});

            if ($scope.invoice.id_contact) {
                options.modules.push({module: "com_zeapps_contact", id: "contacts_" + $scope.invoice.id_contact});
            }

            if ($scope.invoice.id_company) {
                options.modules.push({module: "com_zeapps_contact", id: "compagnies_" + $scope.invoice.id_company});
            }


            options.attachments = [];
            options.templates = [];
            options.data_templates = [];

            angular.forEach(listeModleEmails, function (template) {
                if (template.to_invoice) {
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


            options.data_templates.push({tag: "[type_doc]", value: __t("Invoice")});
            options.data_templates.push({tag: "[company]", value: $scope.invoice.name_company});
            options.data_templates.push({tag: "[contact]", value: $scope.invoice.name_contact});
            options.data_templates.push({tag: "[number_doc]", value: $scope.invoice.numerotation});
            options.data_templates.push({tag: "[amount]", value: $scope.invoice.total_ttc});
            options.data_templates.push({tag: "[amount_without_taxes]", value: $scope.invoice.total_ht});
            options.data_templates.push({tag: "[reference]", value: $scope.invoice.reference_client});
            options.data_templates.push({tag: "[label_doc]", value: $scope.invoice.libelle});
            options.data_templates.push({tag: "[doc_manager]", value: $scope.invoice.name_user_account_manager});

            zhttp.crm.invoice.pdf.make($scope.invoice.id).then(function (response) {
                if (response.data && response.data != "false") {
                    let url_file = angular.fromJson(response.data);
                    let fileNamePdf = __t("invoice") + "-" + $scope.invoice.numerotation + ".pdf" ;
                    options.attachments.push({file: url_file, url: "/" + url_file, name: fileNamePdf});

                    zeapps_modal.loadModule("zeapps", "email_writer", options, function (objReturn) {
                        if (objReturn) {
                        }
                    });
                }
            });
        }

        function print() {
            zhttp.crm.invoice.pdf.make($scope.invoice.id).then(function (response) {
                if (response.data && response.data != "false") {
                    window.document.location.href = "/" + angular.fromJson(response.data);
                }
            });
        }

        function initNavigation() {

            // calcul le nombre de résultat
            if ($rootScope.invoices) {
                $scope.nb_invoices = $rootScope.invoices.ids.length;


                // calcul la position du résultat actuel
                $scope.invoice_order = 0;
                $scope.invoice_first = 0;
                $scope.invoice_previous = 0;
                $scope.invoice_next = 0;
                $scope.invoice_last = 0;

                for (var i = 0; i < $rootScope.invoices.ids.length; i++) {
                    if ($rootScope.invoices.ids[i] == $routeParams.id) {
                        $scope.invoice_order = i + 1;
                        if (i > 0) {
                            $scope.invoice_previous = $rootScope.invoices.ids[i - 1];
                        }

                        if ((i + 1) < $rootScope.invoices.ids.length) {
                            $scope.invoice_next = $rootScope.invoices.ids[i + 1];
                        }
                    }
                }

                // recherche la première facture de la liste
                if ($rootScope.invoices.ids[0] != undefined) {
                    if ($rootScope.invoices.ids[0] != $routeParams.id) {
                        $scope.invoice_first = $rootScope.invoices.ids[0];
                    }
                } else
                    $scope.invoice_first = 0;

                // recherche la dernière facture de la liste
                if ($rootScope.invoices.ids[$rootScope.invoices.ids.length - 1] != undefined) {
                    if ($rootScope.invoices.ids[$rootScope.invoices.ids.length - 1] != $routeParams.id) {
                        $scope.invoice_last = $rootScope.invoices.ids[$rootScope.invoices.ids.length - 1];
                    }
                } else
                    $scope.invoice_last = 0;
            } else {
                $scope.nb_invoices = 0;
            }
        }

        function sortableStop(event, ui) {
            if (parseInt($scope.invoice.finalized, 10) == 0) {
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
                zhttp.crm.invoice.line.position(formatted_data);
            }
        }

    }]);