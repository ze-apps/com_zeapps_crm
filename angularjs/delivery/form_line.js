app.controller("ComZeappsCrmDeliveryFormLineCtrl", ["$scope", "zeHttp", "zeapps_modal", "crmTotal",
    function ($scope, zhttp, zeapps_modal, crmTotal) {

        $scope.form.price_unit = $scope.form.price_unit * 1;
        $scope.form.price_unit_ttc_calculated = 0;
        $scope.form.price_unit_ttc_subline = $scope.form.price_unit_ttc_subline * 1;
        $scope.form.qty = $scope.form.qty * 1;
        $scope.form.discount = $scope.form.discount * 1;
        $scope.form.value_taxe = $scope.form.value_taxe * 1;

        $scope.navigationState = "body";

        $scope.accountingNumberHttp = zhttp.crm.accounting_number;
        $scope.accountingNumberTplNew = '/com_zeapps_contact/accounting_numbers/form_modal';
        $scope.accountingNumberFields = [
            {label: 'Numero', key: 'number'},
            {label: 'Libelle', key: 'label'},
            {label: 'Type', key: 'type_label'}
        ];


        $scope.deliveryLineTplUrl = "/com_zeapps_crm/deliveries/form_line";

        $scope.updatePriceUnit = updatePriceUnit ;


        function convertFloat(value) {
            if (value && typeof value == 'string') {
                if (!value.endsWith(',') && !value.endsWith('.')) {
                    value = value.replace(",", ".");
                    value = value * 1;
                }
            }

            return value;
        }

        function updatePriceUnit(typePrice) {
            if (typePrice == 'HT') {
                $scope.form.price_unit = convertFloat($scope.form.price_unit);
                if (!isNaN($scope.form.price_unit)) {
                    $scope.form.price_unit_ttc_calculated = round2($scope.form.price_unit * (1 + $scope.form.value_taxe / 100)) ;
                }
            } else {
                $scope.form.price_unit_ttc_calculated = convertFloat($scope.form.price_unit_ttc_calculated);
                if (!isNaN($scope.form.price_unit_ttc_calculated)) {
                    $scope.form.price_unit = round2($scope.form.price_unit_ttc_calculated / (1 + $scope.form.value_taxe / 100)) ;
                }
            }
        }
        updatePriceUnit('HT');

        $scope.editLine = function () {
            updatePrice();
        };

        $scope.updatePriceSubLine = function () {
            updatePrice();
        };

        $scope.updatePriceSubLineKeyUp = function () {
            delay(function (e) {
                updatePrice();
                $scope.$apply();
            }, 1000);
        };

        var timer = 0;

        function delay(callback, ms) {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        }


        // définition du tableau des sous ligne
        if ($scope.form.sublines == undefined) {
            $scope.form.sublines = [];
        }

        for (var i = 0; i < $scope.form.sublines; i++) {
            $scope.form.sublines.serialId = i + 1;
        }
        var nbElementSubLine = $scope.form.sublines.length + 1;


        $scope.setTab = function (tab) {
            $scope.navigationState = tab;
        };


        $scope.sortable = {
            connectWith: ".sortableContainer",
            disabled: false,
            axis: "y",
            stop: function (event, ui) {
                var indexSort = 0;
                $("tr", $(ui.item[0]).parent()).each(function () {
                    indexSort++;

                    for (var i = 0; i < $scope.form.sublines.length; i++) {
                        if ($scope.form.sublines[i].serialId == $(this).attr("data-serialId")) {
                            $scope.form.sublines[i].sort = indexSort;
                        }
                    }
                });
            }
        };


        $scope.addLine = function () {
            // charge la modal de la liste de produit
            zeapps_modal.loadModule("com_zeapps_crm", "search_product", {}, function (objReturn) {
                if (objReturn) {
                    nbElementSubLine++;

                    if (objReturn.active) {
                        var line = {
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

                            total_ht:objReturn.price_ht,
                            total_ttc:objReturn.price_ttc,
                            price_unit_ht_indicated:objReturn.price_ht,
                            price_unit_ttc_subline:objReturn.price_ttc,


                            update_price_from_subline:objReturn.update_price_from_subline,
                            show_subline:objReturn.show_subline,

                            sublines:addSublines(objReturn.sublines),

                            sort: $scope.form.sublines.length + 1,
                        };
                        $scope.form.sublines.push(line);

                        updatePrice();
                    } else {
                        toasts("danger", "Ce produit n'est plus actif");
                    }
                }
            });
        };


        $scope.deleteLine = function (line) {
            if ($scope.form.sublines.indexOf(line) > -1) {
                for (var i = 0; i < $scope.form.sublines.length; i++) {
                    if ($scope.form.sublines[i].sort > line.sort) {
                        $scope.form.sublines[i].sort--;
                    }
                }

                $scope.form.sublines.splice($scope.form.sublines.indexOf(line), 1);

                updatePrice();
            }
        };


        $scope.addFromCode = addFromCode;
        $scope.keyEventaddFromCode = keyEventaddFromCode;

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

        function addSublines(sublines) {
            var dataSublines = [];

            if (sublines) {
                for(var i = 0 ; i < sublines.length ; i++) {
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

                        total_ht:sublines[i].price_ht,
                        total_ttc:sublines[i].price_ttc,
                        price_unit_ht_indicated:sublines[i].price_ht,
                        price_unit_ttc_subline:sublines[i].price_ttc,


                        update_price_from_subline:sublines[i].update_price_from_subline,
                        show_subline:sublines[i].show_subline,

                        sublines:addSublines(sublines[i].sublines),
                        sort: sublines[i].sort
                    };
                    dataSublines.push(line) ;
                }
            }

            return dataSublines ;
        }

        function addFromCode() {
            if ($scope.codeProduct !== "") {
                var code = $scope.codeProduct;
                zhttp.crm.product.get_code(code).then(function (response) {
                    if (response.data && response.data != "false") {
                        if (response.data.active) {
                            var line = {
                                type: "product",
                                id_product: response.data.id,
                                ref: response.data.ref,
                                designation_title: response.data.name,
                                designation_desc: response.data.description,
                                qty: 1,
                                discount: 0.00,
                                price_unit: parseFloat(response.data.price_ht) || parseFloat(response.data.price_ttc),
                                id_taxe: parseFloat(response.data.id_taxe),
                                value_taxe: parseFloat(response.data.value_taxe),
                                accounting_number: parseFloat(response.data.accounting_number),

                                sublines:addSublines(response.data.sublines),

                                total_ht:response.data.price_ht,
                                total_ttc:response.data.price_ttc,
                                price_unit_ht_indicated:response.data.price_ht,
                                price_unit_ttc_subline:response.data.price_ttc,

                                update_price_from_subline:response.data.update_price_from_subline,
                                show_subline:response.data.show_subline,

                                sort: $scope.form.sublines.length + 1
                            };
                            $scope.form.sublines.push(line);

                            updatePrice();

                            $scope.codeProduct = "";
                        } else {
                            toasts("danger", "Ce produit n'est plus actif");
                        }

                    } else {
                        toasts("danger", "Aucun produit avec le code " + code + " trouvé dans la base de données.");
                    }
                });
            }
        }


        // Update price TTC with subline
        var updatePrice = function () {
            var dataPrice = crmTotal.getPriceLine($scope.form);

            $scope.form.price_unit_ht_indicated = dataPrice.priceUnitHT.toFixed(2);
            $scope.form.price_unit_ttc_subline = dataPrice.priceUnitTTC.toFixed(2);
        };


        $scope.updateTaxe = updateTaxe;
        $scope.loadAccountingNumber = loadAccountingNumber;

        function updateTaxe() {
            angular.forEach($scope.$parent.taxes, function (taxe) {
                if (taxe.id === $scope.form.id_taxe) {
                    $scope.form.value_taxe = taxe.value;
                }
            });
            updatePriceUnit('HT');
        }

        function loadAccountingNumber(accounting_number) {
            if (accounting_number) {
                $scope.form.accounting_number = accounting_number.number;
            } else {
                $scope.form.accounting_number = "";
            }
        }

        function round2(num) {
            return +(Math.round(num + "e+2") + "e-2");
        }

    }]);