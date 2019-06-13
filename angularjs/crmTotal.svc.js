app.factory("crmTotal", [function () {

    var doc = {};
    var lines = [];

    var service = {
        init: init,
        getPriceLine: getPriceLine,
        sub: {
            HT: subtotalHT,
            TTC: subtotalTTC
        },
        line: {
            update: updateSums
        },
        get: {
            totals: {}
        }
    };

    return service;


    function getPriceLine(line, remise, discount_prohibited) {
        var dataPrice = {};
        dataPrice.priceUnitHT = 0;
        dataPrice.priceUnitTTC = 0;
        dataPrice.priceTotalHT = 0;
        dataPrice.priceTotalTTC = 0;
        dataPrice.data = [];

        if (!discount_prohibited) {
            discount_prohibited = 0 ;
        }

        if (line.discount_prohibited) {
            discount_prohibited = 1 ;
        }

        // calcul de la remise
        var remiseLigne = (1 - (parseFloat(line.discount) / 100)) ;
        if (remise && !isNaN(remise)) {
            remiseLigne = remiseLigne * (1 - (parseFloat(remise) / 100)) ;
        }

        if (discount_prohibited) {
            remiseLigne = 1 ;
        }


        // recherche s'il y a des sous ligne
        if (line.sublines && line.sublines.length) {
            // 1) Récupérer les écritures des sous niveaux
            for (var i = 0; i < line.sublines.length; i++) {
                var dataPriceSubline = getPriceLine(line.sublines[i], 0, discount_prohibited);

                for (var i_data = 0; i_data < dataPriceSubline.data.length; i_data++) {
                    dataPrice.data.push(dataPriceSubline.data[i_data]);
                }
            }



            // 2) Calcul le TTC
            var montantHT = 0;
            var montantTTC = 0;
            for (var i_data = 0; i_data < dataPrice.data.length; i_data++) {
                montantHT += dataPrice.data[i_data].price_total_ht ;
                montantTTC += dataPrice.data[i_data].price_total_ttc ;
            }






            // 3) Caculer le ratio
            if (line.update_price_from_subline) {
                dataPrice.priceUnitHT = montantHT;
                dataPrice.priceUnitTTC = montantTTC;
                dataPrice.priceTotalHT = montantHT * line.qty * remiseLigne ;
                dataPrice.priceTotalTTC = montantHT * ((line.value_taxe / 100) + 1) * line.qty * remiseLigne ;

                line.price_unit = dataPrice.priceUnitHT ;
                line.total_ht = dataPrice.priceTotalHT ;
                line.total_ttc = dataPrice.priceTotalTTC ;
            } else {
                if (isNaN(line.price_unit_ttc_subline)) {
                    if (line.price_unit_ttc_subline && line.price_unit_ttc_subline != "") {
                        line.price_unit_ttc_subline = line.price_unit_ttc_subline.replace(",", ".");
                    } else {
                        line.price_unit_ttc_subline = 0 ;
                    }
                }
                dataPrice.priceUnitTTC = line.price_unit_ttc_subline * 1;
                var ratio = dataPrice.priceUnitTTC / montantTTC;
                montantHT = 0;
                var montantHTRemise = 0;
                for (var i_data = 0; i_data < dataPrice.data.length; i_data++) {
                    dataPrice.data[i_data].price_total_ttc = dataPrice.data[i_data].price_total_ttc * ratio;
                    dataPrice.data[i_data].price_total_ht = dataPrice.data[i_data].price_total_ht * ratio;

                    montantHT += dataPrice.data[i_data].price_total_ht ;


                    dataPrice.data[i_data].price_total_ht = dataPrice.data[i_data].price_total_ht * remiseLigne ;
                    dataPrice.data[i_data].price_total_ttc = dataPrice.data[i_data].price_total_ttc * remiseLigne ;
                    montantHTRemise += dataPrice.data[i_data].price_total_ht ;
                }

                dataPrice.priceUnitHT = montantHT;
                dataPrice.priceTotalHT = montantHTRemise * line.qty ;
                dataPrice.priceTotalTTC = dataPrice.priceUnitTTC * line.qty * remiseLigne ;

                line.price_unit = dataPrice.priceUnitHT ;
                line.total_ht = dataPrice.priceTotalHT ;
                line.total_ttc = dataPrice.priceTotalTTC ;
            }
        } else {
            // on applique le prix du produit unitaire
            dataPrice.data.push({
                accounting_number: line.accounting_number,
                id_taxe: line.id_taxe,
                value_taxe: line.value_taxe,
                qty: line.qty,
                price_unit_ht: line.price_unit,
                price_unit_ttc: line.price_unit * ((line.value_taxe / 100) + 1),
                price_total_ht: line.price_unit * line.qty * remiseLigne,
                price_total_ttc: (line.price_unit * line.qty) * ((line.value_taxe / 100) + 1) * remiseLigne,
            });

            dataPrice.priceUnitHT = line.price_unit ;
            dataPrice.priceUnitTTC = line.price_unit * ((line.value_taxe / 100) + 1) ;
            dataPrice.priceTotalHT = line.price_unit * line.qty * remiseLigne ;
            dataPrice.priceTotalTTC = line.price_unit * ((line.value_taxe / 100) + 1) * line.qty * remiseLigne ;

            line.price_unit = dataPrice.priceUnitHT ;
            line.total_ht = dataPrice.priceTotalHT ;
            line.total_ttc = dataPrice.priceTotalTTC ;
        }


        return dataPrice;
    }


    function init(d, l) {
        doc = d;
        lines = l;

        process();
    }

    function process() {
        makeTVAarray();
        calcTotals();
    }

    function makeTVAarray() {
        var tmp = {};

        var data = [] ;
        angular.forEach(lines, function (line) {
            if (line !== undefined && line.type !== "subTotal" && line.type !== "comment" && line.has_detail !== "1") {
                var dataPriceSubline = getPriceLine(line, doc.global_discount);


                var remiseLigne = 1;
                if (line.sublines && line.sublines.length) {
                    remiseLigne = (1 - (parseFloat(line.discount) / 100)) ;
                    if (doc.global_discount && !isNaN(doc.global_discount)) {
                        remiseLigne = remiseLigne * (1 - (parseFloat(doc.global_discount) / 100)) ;
                    }
                }

                for (var i_data = 0; i_data < dataPriceSubline.data.length; i_data++) {
                    dataPriceSubline.data[i_data].price_total_ht = dataPriceSubline.data[i_data].price_total_ht * line.qty * remiseLigne ;
                    data.push(dataPriceSubline.data[i_data]);
                }
            }
        });


        for (var i_data = 0; i_data < data.length; i_data++) {
            if (tmp[data[i_data].id_taxe] === undefined) {
                tmp[data[i_data].id_taxe] = {
                    ht: 0,
                    value_taxe: round2(parseFloat(data[i_data].value_taxe))
                };
            }

            tmp[data[i_data].id_taxe].ht += round2(parseFloat(data[i_data].price_total_ht));
            tmp[data[i_data].id_taxe].value = round2(parseFloat(tmp[data[i_data].id_taxe].ht) * (parseFloat(tmp[data[i_data].id_taxe].value_taxe) / 100));
        }

        service.get.tvas = tmp;
    }

    function calcTotals() {
        calcTotalPreDiscountHT();
        calcTotalPreDiscountTTC();
        calcTotalDiscount();
        calcTotalHT();
        calcTotalTVA();
        calcTotalTTC();
    }

    function calcTotalPreDiscountHT() {
        var t = 0;
        for (var i = 0; i < lines.length; i++) {
            if (lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1") {
                t += round2(parseFloat(lines[i].price_unit) * parseFloat(lines[i].qty));
            }
        }
        service.get.totals.total_prediscount_ht = t;
    }

    function calcTotalPreDiscountTTC() {
        var t = 0;
        for (var i = 0; i < lines.length; i++) {
            if (lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1") {
                t += round2(parseFloat(lines[i].price_unit) * parseFloat(lines[i].qty) * (1 + (parseFloat(lines[i].value_taxe) / 100)));
            }
        }
        service.get.totals.total_prediscount_ttc = t;
    }

    function calcTotalDiscount() {
        var discount = 0;
        var t = 0;
        for (var i = 0; i < lines.length; i++) {
            if (lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1") {
                discount = round2(parseFloat(lines[i].price_unit) * parseFloat(lines[i].qty) * (1 - (1 - parseFloat(lines[i].discount) / 100) * (1 - parseFloat(doc.global_discount) / 100)));
                t += discount;
            }
        }
        service.get.totals.total_discount = t;
    }

    function calcTotalHT() {
        var t = 0;
        for (var i = 0; i < lines.length; i++) {
            if (lines[i] !== undefined && lines[i].type !== "subTotal" && lines[i].type !== "comment" && lines[i].has_detail !== "1") {
                t += round2(parseFloat(lines[i].total_ht));
            }
        }
        service.get.totals.total_ht = t;
    }

    function calcTotalTVA() {
        var t = 0;
        angular.forEach(service.get.tvas, function (tva) {
            t += round2(parseFloat(tva.value));
        });
        service.get.totals.total_tva = t;
    }

    function calcTotalTTC() {
        service.get.totals.total_ttc = parseFloat(service.get.totals.total_ht) + parseFloat(service.get.totals.total_tva);
    }

    function subtotalHT(array, index) {
        var t = 0;
        for (var i = index - 1; i >= 0; i--) {
            if (array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment") {
                t += round2(parseFloat(array[i].total_ht));
            } else if (array[i].type === "subTotal") {
                i = -1;
            }
        }
        return t;
    }

    function subtotalTTC(array, index) {
        var t = 0;
        for (var i = index - 1; i >= 0; i--) {
            if (array[i] !== undefined && array[i].type !== "subTotal" && array[i].type !== "comment") {
                t += round2(parseFloat(array[i].total_ttc));
            } else if (array[i].type === "subTotal") {
                i = -1;
            }
        }
        return t;
    }

    function updateSums(line) {
        line.qty = line.qty || 0;
        line.price_unit = line.price_unit || 0;
        line.discount = line.discount || 0;

        line.total_ht = round2(round2(parseFloat(line.price_unit)) * parseFloat(line.qty) * (1 - (parseFloat(line.discount) / 100)) * (1 - (parseFloat(doc.global_discount) / 100)));
        line.total_ttc = round2(line.total_ht * (1 + (parseFloat(line.value_taxe) / 100)));
    }

    function round2(num) {
        return +(Math.round(num + "e+2") + "e-2");
    }
}]);
