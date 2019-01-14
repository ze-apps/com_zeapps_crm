app.config(["$provide",
    function ($provide) {
        $provide.decorator("zeHttp", ["$delegate", function ($delegate) {
            var zeHttp = $delegate;

            zeHttp.crm = {
                price_list: {
                    get: get_pricelist,
                    get_all: getAll_pricelist,
                    get_all_admin: getAll_pricelist_admin,
                    get_price_list_type: get_price_list_type,
                    save: save_price_list,
                    del: del_price_list,
                    rate: rate_price_list,
                    save_rate: save_rate_price_list,
                },
                modality: {
                    get: get_modality,
                    get_all: getAll_modality,
                    save: post_modality,
                    del: del_modality
                },
                taxe: {
                    get: get_taxe,
                    get_all: getAll_taxe,
                    save: post_taxe,
                    del: del_taxe
                },
                crm_origin: {
                    get: get_crmOrigin,
                    get_all: getAll_crmOrigin,
                    save: post_crmOrigin,
                    del: del_crmOrigin
                },
                invoice: {
                    get: get_invoice,
                    get_all: getAll_invoice,
                    modal: modal_invoice,
                    save: post_invoice,
                    del: del_invoice,
                    transform: transform_invoice,
                    finalize: finalize_invoice,
                    test: test_invoice,
                    line: {
                        save: save_line_invoice,
                        position: update_linepos_invoice,
                        del: del_line_invoice
                    },
                    line_detail: {
                        save: save_lineDetail_invoice
                    },
                    activity: {
                        save: save_activity_invoice,
                        del: del_activity_invoice
                    },
                    document: {
                        upload: url_document_invoice,
                        del: del_document_invoice
                    },
                    pdf: {
                        get: get_pdf_invoice,
                        make: make_pdf_invoice
                    }
                },
                order: {
                    get: get_order,
                    get_all: getAll_order,
                    modal: modal_order,
                    save: post_order,
                    del: del_order,
                    transform: transform_order,
                    test: test_order,
                    line: {
                        save: save_line_order,
                        position: update_linepos_order,
                        del: del_line_order
                    },
                    line_detail: {
                        save: save_lineDetail_order
                    },
                    activity: {
                        save: save_activity_order,
                        del: del_activity_order
                    },
                    document: {
                        upload: url_document_order,
                        del: del_document_order
                    },
                    pdf: {
                        get: get_pdf_order,
                        make: make_pdf_order
                    }
                },
                delivery: {
                    get: get_delivery,
                    get_all: getAll_delivery,
                    modal: modal_delivery,
                    save: post_delivery,
                    del: del_delivery,
                    transform: transform_delivery,
                    test: test_delivery,
                    line: {
                        save: save_line_delivery,
                        position: update_linepos_delivery,
                        del: del_line_delivery
                    },
                    line_detail: {
                        save: save_lineDetail_delivery
                    },
                    activity: {
                        save: save_activity_delivery,
                        del: del_activity_delivery
                    },
                    document: {
                        upload: url_document_delivery,
                        del: del_document_delivery
                    },
                    pdf: {
                        get: get_pdf_delivery,
                        make: make_pdf_delivery
                    }
                },
                quote: {
                    get: get_quote,
                    get_all: getAll_quote,
                    modal: modal_quote,
                    save: post_quote,
                    del: del_quote,
                    transform: transform_quote,
                    test: test_quote,
                    send_email: send_email,
                    line: {
                        save: save_line_quote,
                        position: update_linepos_quote,
                        del: del_line_quote
                    },
                    line_detail: {
                        save: save_lineDetail_quote
                    },
                    activity: {
                        save: save_activity_quote,
                        del: del_activity_quote
                    },
                    document: {
                        upload: url_document_quote,
                        del: del_document_quote
                    },
                    pdf: {
                        get: get_pdf_quote,
                        make: make_pdf_quote
                    }
                },
                credit_balance: {
                    get: get_creditBalance,
                    get_all: getAll_creditBalance,
                    save: save_creditBalance,
                    save_multiples: saveMultiples_creditBalance,
                    del: delete_creditBalance
                },
                product: {
                    get: get_product,
                    get_code: getCode_product,
                    get_all: getAll_product,
                    modal: modal_product,
                    save: save_product,
                    updateRatio: updateRatio,
                    del: delete_product
                },
                category: {
                    tree: get_categories_tree,
                    get: get_category,
                    save: save_category,
                    update_order: update_category_order,
                    del: delete_category,
                    openTree: recursiveOpening
                },
                product_stock: {
                    get: get_product_stock,
                    get_mvt: get_product_stock_mvt,
                    get_all: getAll_product_stock,
                    save: save_product_stock,
                    del: delete_product_stock,
                    add_mvt: add_mvt,
                    add_transfert: add_transfert,
                    ignore_mvt: ignore_mvt
                },
                warehouse: {
                    get: get_warehouse,
                    get_all: getAll_warehouse,
                    save: save_warehouse,
                    save_all: saveAll_warehouse,
                    del: delete_warehouse
                },
                accounting_number: {
                    modal: modal_accountingNumber,
                    save: save_accountingNumber
                },
                activity_types: {
                    all: all_activityTypes
                },
                potential_orders: {
                    all: all_potentialOrders
                },
                statuts: {
                    getAll: getall_statuts
                }
            };

            zeHttp.config = angular.extend(zeHttp.config || {}, {
                product: {
                    get: {
                        attr: get_product_attr
                    },
                    save: {
                        attr: save_product_attr
                    }
                },
                invoice: {
                    get: {
                        numerotation: get_invoice_numerotation,
                        format: get_invoice_format
                    }
                },
                quote: {
                    get: {
                        numerotation: get_quote_numerotation,
                        format: get_quote_format
                    }
                },
                order: {
                    get: {
                        numerotation: get_order_numerotation,
                        format: get_order_format
                    }
                },
                delivery: {
                    get: {
                        numerotation: get_delivery_numerotation,
                        format: get_delivery_format
                    }
                }
            });

            return zeHttp;


            function getall_statuts() {
                var allStatus = [];
                var status = {};
                status.id = 1;
                status.label = "En cours";
                allStatus.push(status);

                status = {};
                status.id = 2;
                status.label = "Gagné";
                allStatus.push(status);

                status = {};
                status.id = 3;
                status.label = "Perdu";
                allStatus.push(status);

                return allStatus;
            }



            // PRICE LIST
            function get_pricelist(id) {
                return zeHttp.get("/com_zeapps_crm/price-list/get/" + id);
            }

            function getAll_pricelist() {
                return zeHttp.get("/com_zeapps_crm/price-list/getAll");
            }

            function getAll_pricelist_admin() {
                return zeHttp.get("/com_zeapps_crm/price-list/getAllAdmin");
            }

            function get_price_list_type() {
                return zeHttp.get("/com_zeapps_crm/price-list/getPriceListType");
            }

            function save_price_list(data) {
                return zeHttp.post("/com_zeapps_crm/price-list/save", data);
            }

            function del_price_list(id) {
                return zeHttp.delete("/com_zeapps_crm/price-list/delete/" + id);
            }

            function rate_price_list(id_pricelist) {
                return zeHttp.get("/com_zeapps_crm/price-list/rate/" + id_pricelist);
            }

            function save_rate_price_list(data) {
                return zeHttp.post("/com_zeapps_crm/price-list/rate-save", data);
            }











            // MODALITY
            function get_modality(id) {
                return zeHttp.get("/com_zeapps_contact/modalities/get/" + id);
            }

            function getAll_modality() {
                return zeHttp.get("/com_zeapps_contact/modalities/getAll/");
            }

            function post_modality(data) {
                return zeHttp.post("/com_zeapps_contact/modalities/save", data);
            }

            function del_modality(id) {
                return zeHttp.delete("/com_zeapps_contact/modalities/delete/" + id);
            }

            // TAXE
            function get_taxe(id) {
                return zeHttp.get("/com_zeapps_crm/taxes/get/" + id);
            }

            function getAll_taxe() {
                return zeHttp.get("/com_zeapps_crm/taxes/getAll/");
            }

            function post_taxe(data) {
                return zeHttp.post("/com_zeapps_crm/taxes/save", data);
            }

            function del_taxe(id) {
                return zeHttp.delete("/com_zeapps_crm/taxes/delete/" + id);
            }

            // CRM ORIGINS
            function get_crmOrigin(id) {
                return zeHttp.get("/com_zeapps_crm/crm_origins/get/" + id);
            }

            function getAll_crmOrigin() {
                return zeHttp.get("/com_zeapps_crm/crm_origins/getAll/");
            }

            function post_crmOrigin(data) {
                return zeHttp.post("/com_zeapps_crm/crm_origins/save", data);
            }

            function del_crmOrigin(id) {
                return zeHttp.delete("/com_zeapps_crm/crm_origins/delete/" + id);
            }


            // INVOICE
            function test_invoice(data) {
                return zeHttp.post("/com_zeapps_crm/invoices/testFormat", data);
            }

            function get_invoice(id) {
                return zeHttp.get("/com_zeapps_crm/invoices/get/" + id);
            }

            function getAll_invoice(id_project, type, limit, offset, context, filters) {
                id_project = id_project || 0;
                type = type || "";
                return zeHttp.post("/com_zeapps_crm/invoices/getAll/" + id_project + "/" + type + "/" + limit + "/" + offset + "/" + context, filters);
            }

            function modal_invoice(limit, offset, filters) {
                return zeHttp.post("/com_zeapps_crm/invoices/modal/" + limit + "/" + offset, filters);
            }

            function post_invoice(data) {
                return zeHttp.post("/com_zeapps_crm/invoices/save", data);
            }

            function del_invoice(id) {
                return zeHttp.delete("/com_zeapps_crm/invoices/delete/" + id);
            }

            function transform_invoice(id, data) {
                return zeHttp.post("/com_zeapps_crm/invoices/transform/" + id, data);
            }

            function finalize_invoice(id) {
                return zeHttp.post("/com_zeapps_crm/invoices/finalize/" + id);
            }

            function save_line_invoice(data) {
                return zeHttp.post("/com_zeapps_crm/invoices/saveLine", data);
            }

            function update_linepos_invoice(data) {
                return zeHttp.post("/com_zeapps_crm/invoices/updateLinePosition/", data);
            }

            function del_line_invoice(id) {
                return zeHttp.delete("/com_zeapps_crm/invoices/deleteLine/" + id);
            }

            function save_lineDetail_invoice(data) {
                return zeHttp.post("/com_zeapps_crm/invoices/saveLineDetail", data);
            }

            function save_activity_invoice(data) {
                return zeHttp.post("/com_zeapps_crm/invoices/activity", data);
            }

            function del_activity_invoice(id) {
                return zeHttp.post("/com_zeapps_crm/invoices/del_activity/" + id);
            }

            function url_document_invoice() {
                return "/com_zeapps_crm/invoices/uploadDocuments/";
            }

            function del_document_invoice(id) {
                return zeHttp.post("/com_zeapps_crm/invoices/del_document/" + id);
            }

            function get_pdf_invoice() {
                return "/com_zeapps_crm/invoices/getPDF/";
            }

            function make_pdf_invoice(id) {
                return zeHttp.post("/com_zeapps_crm/invoices/makePDF/" + id);
            }


            // ORDER
            function test_order(data) {
                return zeHttp.post("/com_zeapps_crm/orders/testFormat", data);
            }

            function get_order(id) {
                return zeHttp.get("/com_zeapps_crm/orders/get/" + id);
            }

            function getAll_order(id_project, type, limit, offset, context, filters) {
                id_project = id_project || 0;
                type = type || "";
                return zeHttp.post("/com_zeapps_crm/orders/getAll/" + id_project + "/" + type + "/" + limit + "/" + offset + "/" + context, filters);
            }

            function modal_order(limit, offset, filters) {
                return zeHttp.post("/com_zeapps_crm/orders/modal/" + limit + "/" + offset, filters);
            }

            function post_order(data) {
                return zeHttp.post("/com_zeapps_crm/orders/save", data);
            }

            function del_order(id) {
                return zeHttp.delete("/com_zeapps_crm/orders/delete/" + id);
            }

            function transform_order(id, data) {
                return zeHttp.post("/com_zeapps_crm/orders/transform/" + id, data);
            }

            function save_line_order(data) {
                return zeHttp.post("/com_zeapps_crm/orders/saveLine", data);
            }

            function update_linepos_order(data) {
                return zeHttp.post("/com_zeapps_crm/orders/updateLinePosition/", data);
            }

            function del_line_order(id) {
                return zeHttp.delete("/com_zeapps_crm/orders/deleteLine/" + id);
            }

            function save_lineDetail_order(data) {
                return zeHttp.post("/com_zeapps_crm/orders/saveLineDetail", data);
            }

            function save_activity_order(data) {
                return zeHttp.post("com_zeapps_crm/orders/activity", data);
            }

            function del_activity_order(id) {
                return zeHttp.post("com_zeapps_crm/orders/del_activity/" + id);
            }

            function url_document_order() {
                return "/com_zeapps_crm/orders/uploadDocuments/";
            }

            function del_document_order(id) {
                return zeHttp.post("/com_zeapps_crm/orders/del_document/" + id);
            }

            function get_pdf_order() {
                return "/com_zeapps_crm/orders/getPDF/";
            }

            function make_pdf_order(id) {
                return zeHttp.post("/com_zeapps_crm/orders/makePDF/" + id);
            }


            // DELIVERY
            function test_delivery(data) {
                return zeHttp.post("/com_zeapps_crm/deliveries/testFormat", data);
            }

            function get_delivery(id) {
                return zeHttp.get("/com_zeapps_crm/deliveries/get/" + id);
            }

            function getAll_delivery(id_project, type, limit, offset, context, filters) {
                id_project = id_project || 0;
                type = type || "";
                return zeHttp.post("/com_zeapps_crm/deliveries/getAll/" + id_project + "/" + type + "/" + limit + "/" + offset + "/" + context, filters);
            }

            function modal_delivery(limit, offset, filters) {
                return zeHttp.post("/com_zeapps_crm/deliveries/modal/" + limit + "/" + offset, filters);
            }

            function post_delivery(data) {
                return zeHttp.post("/com_zeapps_crm/deliveries/save", data);
            }

            function del_delivery(id) {
                return zeHttp.delete("/com_zeapps_crm/deliveries/delete/" + id);
            }

            function transform_delivery(id, data) {
                return zeHttp.post("/com_zeapps_crm/deliveries/transform/" + id, data);
            }

            function save_line_delivery(data) {
                return zeHttp.post("/com_zeapps_crm/deliveries/saveLine", data);
            }

            function update_linepos_delivery(data) {
                return zeHttp.post("/com_zeapps_crm/deliveries/updateLinePosition/", data);
            }

            function del_line_delivery(id) {
                return zeHttp.delete("/com_zeapps_crm/deliveries/deleteLine/" + id);
            }

            function save_lineDetail_delivery(data) {
                return zeHttp.post("/com_zeapps_crm/deliveries/saveLineDetail", data);
            }

            function save_activity_delivery(data) {
                return zeHttp.post("com_zeapps_crm/deliveries/activity", data);
            }

            function del_activity_delivery(id) {
                return zeHttp.post("com_zeapps_crm/deliveries/del_activity/" + id);
            }

            function url_document_delivery() {
                return "/com_zeapps_crm/deliveries/uploadDocuments/";
            }

            function del_document_delivery(id) {
                return zeHttp.post("/com_zeapps_crm/deliveries/del_document/" + id);
            }

            function get_pdf_delivery() {
                return "/com_zeapps_crm/deliveries/getPDF/";
            }

            function make_pdf_delivery(id) {
                return zeHttp.post("/com_zeapps_crm/deliveries/makePDF/" + id);
            }


            // QUOTE
            function test_quote(data) {
                return zeHttp.post("/com_zeapps_crm/quotes/testFormat", data);
            }

            function get_quote(id) {
                return zeHttp.get("/com_zeapps_crm/quotes/get/" + id);
            }

            function getAll_quote(id_project, type, limit, offset, context, filters) {
                id_project = id_project || 0;
                type = type || "";
                return zeHttp.post("/com_zeapps_crm/quotes/getAll/" + id_project + "/" + type + "/" + limit + "/" + offset + "/" + context, filters);
            }

            function modal_quote(limit, offset, filters) {
                return zeHttp.post("/com_zeapps_crm/quotes/modal/" + limit + "/" + offset, filters);
            }

            function post_quote(data) {
                return zeHttp.post("/com_zeapps_crm/quotes/save", data);
            }

            function send_email(data) {
                return zeHttp.post("/com_zeapps_crm/quotes/send_email_post", data);
            }


            function del_quote(id) {
                return zeHttp.delete("/com_zeapps_crm/quotes/delete/" + id);
            }

            function transform_quote(id, data) {
                return zeHttp.post("/com_zeapps_crm/quotes/transform/" + id, data);
            }

            function save_line_quote(data) {
                return zeHttp.post("/com_zeapps_crm/quotes/saveLine", data);
            }

            function update_linepos_quote(data) {
                return zeHttp.post("/com_zeapps_crm/quotes/updateLinePosition/", data);
            }

            function del_line_quote(id) {
                return zeHttp.delete("/com_zeapps_crm/quotes/deleteLine/" + id);
            }

            function save_lineDetail_quote(data) {
                return zeHttp.post("/com_zeapps_crm/quotes/saveLineDetail", data);
            }

            function save_activity_quote(data) {
                return zeHttp.post("com_zeapps_crm/quotes/activity", data);
            }

            function del_activity_quote(id) {
                return zeHttp.post("com_zeapps_crm/quotes/del_activity/" + id);
            }

            function url_document_quote() {
                return "/com_zeapps_crm/quotes/uploadDocuments/";
            }

            function del_document_quote(id) {
                return zeHttp.post("/com_zeapps_crm/quotes/del_document/" + id);
            }

            function get_pdf_quote() {
                return "/com_zeapps_crm/quotes/getPDF/";
            }

            function make_pdf_quote(id) {
                return zeHttp.post("/com_zeapps_crm/quotes/makePDF/" + id);
            }

            // CREDIT BALANCES
            function get_creditBalance(id) {
                return zeHttp.get('/com_zeapps_crm/credit_balances/get/' + id);
            }

            function getAll_creditBalance(src_id, src, limit, offset, filters) {
                return zeHttp.post('/com_zeapps_crm/credit_balances/all/' + src_id + "/" + src + "/" + limit + "/" + offset, filters);
            }

            function save_creditBalance(data) {
                return zeHttp.post('/com_zeapps_crm/credit_balances/save', data);
            }

            function saveMultiples_creditBalance(data) {
                return zeHttp.post('/com_zeapps_crm/credit_balances/save_multiples', data);
            }

            function delete_creditBalance(id) {
                return zeHttp.delete('/com_zeapps_crm/credit_balances/delete/' + id);
            }


            // PRODUCT
            function get_product(id) {
                return zeHttp.get("/com_zeapps_crm/product/get/" + id);
            }

            function getCode_product(code) {
                return zeHttp.get("/com_zeapps_crm/product/get_code/" + code);
            }

            function getAll_product() {
                return zeHttp.get("/com_zeapps_crm/product/getAll");
            }

            function modal_product(id, limit, offset, filters) {
                return zeHttp.post("/com_zeapps_crm/product/modal/" + id + "/" + limit + "/" + offset, filters);
            }

            function save_product(data) {
                return zeHttp.post("/com_zeapps_crm/product/save", data);
            }

            function updateRatio(data) {
                return zeHttp.get("/com_zeapps_crm/product/updateRatio", data);
            }

            function delete_product(id) {
                return zeHttp.post("/com_zeapps_crm/product/delete/" + id);
            }


            // CATEGORIES
            function get_categories_tree() {
                return zeHttp.get("/com_zeapps_crm/categories/get_tree");
            }

            function get_category(id) {
                return zeHttp.get("/com_zeapps_crm/categories/get/" + id);
            }

            function save_category(data) {
                return zeHttp.post("/com_zeapps_crm/categories/save", data);
            }

            function update_category_order(data) {
                return zeHttp.post("/com_zeapps_crm/categories/update_order", data);
            }

            function delete_category(id, force) {
                if (force === undefined)
                    return zeHttp.post("/com_zeapps_crm/categories/delete/" + id);
                else if (force)
                    return zeHttp.post("/com_zeapps_crm/categories/delete_force/" + id + "/true");
                else
                    return zeHttp.post("/com_zeapps_crm/categories/delete_force/" + id + "/false");

            }

            function recursiveOpening(branch, id) {
                if (angular.isArray(branch.branches)) {
                    for (var i = 0; i < branch.branches.length; i++) {
                        if (recursiveOpening(branch.branches[i], id)) {
                            branch.open = true;
                            return true;
                        }
                    }
                }
                return branch.id == id;
            }


            // PRODUCT STOCKS
            function get_product_stock(id_stock, id_warehouse) {
                id_warehouse = parseInt(id_warehouse) || "";
                return zeHttp.get("/com_zeapps_crm/stock/get/" + id_stock + "/" + id_warehouse);
            }

            function get_product_stock_mvt(id_stock, id_warehouse, limit, offset) {
                id_warehouse = parseInt(id_warehouse) || "";
                return zeHttp.get("/com_zeapps_crm/stock/get_movements/" + id_stock + "/" + id_warehouse + "/" + limit + "/" + offset);
            }

            function getAll_product_stock(limit, offset, context, filters) {
                return zeHttp.post("/com_zeapps_crm/stock/getAll/" + limit + "/" + offset + "/" + context, filters);
            }

            function save_product_stock(data, id_warehouse) {
                id_warehouse = parseInt(id_warehouse) || "";
                return zeHttp.post("/com_zeapps_crm/stock/save/" + id_warehouse, data);
            }

            function delete_product_stock(id) {
                return zeHttp.post("/com_zeapps_crm/stock/delete/" + id);
            }

            function add_mvt(data) {
                return zeHttp.post("/com_zeapps_crm/stock/add_mvt/", data);
            }

            function add_transfert(data) {
                return zeHttp.post("/com_zeapps_crm/stock/add_transfert/", data);
            }

            function ignore_mvt(id, value, id_stock, id_warehouse) {
                id_warehouse = parseInt(id_warehouse) || "";
                return zeHttp.post("/com_zeapps_crm/stock/ignore_mvt/" + id + "/" + value + "/" + id_stock + "/" + id_warehouse);
            }


            // WAREHOUSES
            function get_warehouse(id) {
                return zeHttp.get("/com_zeapps_crm/warehouse/get/" + id);
            }

            function getAll_warehouse() {
                return zeHttp.get("/com_zeapps_crm/warehouse/getAll/");
            }

            function save_warehouse(data) {
                return zeHttp.post("/com_zeapps_crm/warehouse/save/", data);
            }

            function saveAll_warehouse(data) {
                return zeHttp.post("/com_zeapps_crm/warehouse/save_all/", data);
            }

            function delete_warehouse(id) {
                return zeHttp.post("/com_zeapps_crm/warehouse/delete/" + id);
            }


            // ACCOUNTING NUMBERS
            function modal_accountingNumber(limit, offset, filters) {
                return zeHttp.post("/com_zeapps_contact/accounting_numbers/modal/" + limit + "/" + offset, filters)
            }

            function save_accountingNumber(data) {
                return zeHttp.post("/com_zeapps_contact/accounting_numbers/save", data)
            }


            // ACTIVITY TYPES
            function all_activityTypes() {
                return zeHttp.get("/com_zeapps_crm/activity_types/all/");
            }

            // ACTIVITY TYPES
            function all_potentialOrders(limit, offset, context, filters) {
                return zeHttp.post("/com_zeapps_crm/potential_orders/all/" + limit + "/" + offset + "/" + context, filters);
            }


            // CONFIG
            function get_invoice_numerotation() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_invoice_numerotation");
            }

            function get_invoice_format() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_invoice_format");
            }

            function get_quote_numerotation() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_quote_numerotation");
            }

            function get_quote_format() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_quote_format");
            }

            function get_order_numerotation() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_order_numerotation");
            }

            function get_order_format() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_order_format");
            }

            function get_delivery_numerotation() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_delivery_numerotation");
            }

            function get_delivery_format() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_delivery_format");
            }

            function get_product_attr() {
                return zeHttp.get("/com_zeapps_crm/config/get/crm_product_attributes");
            }

            function save_product_attr(data) {
                return zeHttp.config.save(angular.toJson({id: "crm_product_attributes", value: data}));
            }
        }]);
    }]
);