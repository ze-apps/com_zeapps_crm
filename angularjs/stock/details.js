app.controller("ComZeappsCrmStockDetailsCtrl", ["$scope", "$routeParams", "$rootScope", "zeHttp", "toasts", "menu",
    function ($scope, $routeParams, $rootScope, zhttp, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_stock");

        $rootScope.current_warehouse = $rootScope.current_warehouse || $rootScope.user.id_warehouse;

        $scope.filters = {
            main: [
                {
                    format: 'select',
                    field: 'id_warehouse',
                    type: 'text',
                    label: __t("Warehouse"),
                    options: []
                }
            ]
        };

        $scope.filter_model = {
            'id_warehouse': $rootScope.current_warehouse
        };

        $scope.templateMvt = '/com_zeapps_crm/stock/form_mvt';
        $scope.templateTransfert = '/com_zeapps_crm/stock/form_transfert';


        zhttp.crm.warehouse.get_all().then(function (response) {
            if (response.data && response.data != "false") {
                $scope.filters.main[0].options = response.data;
            }

            getStocks();
        });


        $scope.shownMvtForm = false;
        var scales = {
            month: [],
            dates: [],
            date: [],
            days: []
        };
        $scope.selectedScale = "month";
        $scope.labels = [];
        $scope.data = [
            []
        ];
        $scope.navigationState = "chart";
        $scope.page = {
            current: 1
        };
        $scope.pageSize = 15;
        $scope.total = 0;

        $scope.postits = [];


        $scope.getStocks = getStocks;
        $scope.loadList = loadList;
        $scope.edit = edit;
        $scope.changeScaleTo = changeScaleTo;
        $scope.backgroundOf = backgroundOf;
        $scope.setIgnoredTo = setIgnoredTo;
        $scope.addMvt = addMvt;
        $scope.addTransfert = addTransfert;


        function getStocks() {
            var id_warehouse = $scope.filter_model.id_warehouse || "";

            $rootScope.current_warehouse = $scope.filter_model.id_warehouse;

            zhttp.crm.product_stock.get($routeParams.id, id_warehouse).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.total = response.data.total;
                    $scope.page.current = 1;
                    $scope.product_stock = response.data.product_stock;
                    $scope.product_stock.price_unit_stock = parseFloat(response.data.product_stock.price_unit_stock);
                    $scope.product_stock.resupply_delay = parseInt(response.data.product_stock.resupply_delay);

                    angular.forEach($scope.product_stock.movements, function (mvt) {
                        mvt.date_mvt = new Date(mvt.date_mvt);
                    });

                    calcTimeLeft($scope.product_stock.qty, $scope.product_stock.avg);
                    parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.qty);

                    generatePostits();
                }
            });
        }

        function loadList() {
            var id_warehouse = $scope.filter_model.id_warehouse || "";
            var offset = ($scope.page.current - 1) * $scope.pageSize;

            zhttp.crm.product_stock.get_mvt($routeParams.id, id_warehouse, $scope.pageSize, offset).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.total = response.data.total;
                    $scope.product_stock.movements = response.data.stock_movements;

                    angular.forEach($scope.product_stock.movements, function (mvt) {
                        mvt.date_mvt = new Date(mvt.date_mvt);
                    });
                }
            });
        }

        function edit() {
            var formatted_data = angular.toJson($scope.product_stock);

            zhttp.crm.product_stock.save(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    calcTimeLeft($scope.product_stock.qty, $scope.product_stock.avg);
                    parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.qty);

                    generatePostits();
                }
            });
        }

        function addMvt(stock_mvt) {
            stock_mvt.id_warehouse = $scope.filter_model.id_warehouse;
            stock_mvt.id_product = $scope.product_stock.id;

            if (stock_mvt.date_mvt) {
                var y = stock_mvt.date_mvt.getFullYear();
                var M = stock_mvt.date_mvt.getMonth() + 1;
                var d = stock_mvt.date_mvt.getDate();

                stock_mvt.date_mvt_field = y + "-" + M + "-" + d;
            }

            var formatted_data = angular.toJson(stock_mvt);

            zhttp.crm.product_stock.add_mvt(formatted_data).then(function (response) {
                if (response.data && response.data != "false") {
                    getStocks();
                }
            });
        }

        function addTransfert(transfert) {
            if (transfert.src !== transfert.trgt) {
                transfert.id_product = $scope.product_stock.id;

                if (transfert.date_mvt) {
                    var y = transfert.date_mvt.getFullYear();
                    var M = transfert.date_mvt.getMonth() + 1;
                    var d = transfert.date_mvt.getDate();

                    transfert.date_mvt_field = y + "-" + M + "-" + d ;
                }

                var formatted_data = angular.toJson(transfert);
                zhttp.crm.product_stock.add_transfert(formatted_data).then(function (response) {
                    if (response.data && response.data != "false") {
                        getStocks();
                    }
                });
            } else {
                toasts("warning", __t("You cannot make transfers to the departure warehouse"));
            }
        }

        function setIgnoredTo(mvt, value) {
            mvt.ignored = value;

            zhttp.crm.product_stock.ignore_mvt(mvt.id, value, $scope.product_stock.id, $scope.filter_model.id_warehouse).then(function (response) {
                if (response.data && response.data != "false") {
                    $scope.product_stock.avg = response.data;
                    calcTimeLeft($scope.product_stock.qty, $scope.product_stock.avg);
                    generatePostits() ;
                }
            });
        }

        function backgroundOf(mvt) {
            return mvt.qty > 0 ? "bg-success" : "bg-danger";
        }

        function changeScaleTo(scale) {
            $scope.selectedScale = scale;
            parseMovements($scope.product_stock.last[$scope.selectedScale], $scope.product_stock.qty);
        }

        function calcTimeLeft(total, avg) {
            if (avg > 0) {
                var timeleft = total / avg;


                if (timeleft > 0) {
                    $scope.product_stock.timeleft = moment().to(moment().add(timeleft, "days"));
                    $scope.product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
                    $scope.product_stock.colorRupture = "";
                } else {
                    $scope.product_stock.timeleft = __t("Out of order");
                    $scope.product_stock.dateRupture = moment().add(timeleft, "days").format("DD/MM/YYYY");
                    $scope.product_stock.colorRupture = "#cd0000";
                }

                if ($scope.filter_model.id_warehouse > 0) {
                    $scope.product_stock.timeResupply = moment().to(moment().add(timeleft, "days").subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit));
                    $scope.product_stock.dateResupply = moment().add(timeleft, "days").subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit).format("DD/MM/YYYY");
                    $scope.product_stock.colorResupply = moment().isBefore(moment().add(timeleft, "days").subtract($scope.product_stock.resupply_delay, $scope.product_stock.resupply_unit), "day") ? "" : "#cd0000";
                }
            } else {
                $scope.product_stock.timeleft = "-";
                $scope.product_stock.dateRupture = "";
                $scope.product_stock.timeResupply = "-";
                $scope.product_stock.dateResupply = "";
                $scope.product_stock.colorRupture = "";
                $scope.product_stock.colorResupply = "";
            }
        }

        function parseMovements(mvts, total) {
            if (scales.month.length === 0) {
                set12Months();
                set3Months();
                set1Months();
                setDays();
            }

            $scope.labels = scales[$scope.selectedScale];

            var balance = [];
            var i = scales[$scope.selectedScale].length;
            $scope.data[0] = [];
            while (i-- > 0) {
                balance[i] = 0;
                $scope.data[0].push(0);
            }

            angular.forEach(mvts, function (mvt) {
                balance[moment(mvt.date_mvt)[$scope.selectedScale]()] += parseFloat(mvt.qty);
            });

            if ($scope.selectedScale !== "date" && $scope.selectedScale !== "dates")
                balance.unshift.apply(balance, balance.splice(moment().get($scope.selectedScale) + 1, balance.length));

            var count = scales[$scope.selectedScale].length;
            $scope.data[0][--count] = total || 0;
            while (count-- > 0) {
                $scope.data[0][count] = parseFloat($scope.data[0][count + 1]) - parseFloat(balance[count + 1]);
            }
        }

        function set12Months() {
            var count = 12;
            while (count-- > 0) {
                scales.month.push(moment().month(moment().get("month") - count).format("MMMM YYYY"));
            }
        }

        function set3Months() {
            var count = 90;
            while (count-- > 0) {
                scales.dates.push(moment().date(moment().get("date") - count).format("dddd Do"));
            }
        }

        function set1Months() {
            var count = 30;
            while (count-- > 0) {
                scales.date.push(moment().date(moment().get("date") - count).format("dddd Do"));
            }
        }

        function setDays() {
            var count = 7;
            while (count-- > 0) {
                scales.days.push(moment().day(moment().get("day") - count).format("dddd Do"));
            }
        }

        function generatePostits() {
            $scope.postits = [
                {
                    value: $scope.product_stock.qty || 0,
                    legend: __t("Stock"),
                    filter: 'number'
                },
                {
                    value: $scope.product_stock.price_unit_stock,
                    legend: __t("Unit value"),
                    filter: 'currency'
                },
                {
                    value: $scope.product_stock.price_unit_stock * $scope.product_stock.qty,
                    legend: __t("Total stock value"),
                    filter: 'currency'
                },
                {
                    value: $scope.product_stock.timeleft +
                        "<small>" + ($scope.product_stock.dateRupture ? ' (' + $scope.product_stock.dateRupture + ')' : '') + "</small>",
                    color: $scope.product_stock.colorRupture,
                    legend: __t("Provisional date of termination")
                }
            ];

            if ($scope.filter_model.id_warehouse) {
                $scope.postits.push(
                    {
                        value: $scope.product_stock.timeResupply +
                            "<small>" + ($scope.product_stock.dateResupply ? ' (' + $scope.product_stock.dateResupply + ')' : '') + "</small>",
                        color: $scope.product_stock.colorResupply,
                        legend: __t("Supplier order before")
                    }
                );
            }
        }
    }]);