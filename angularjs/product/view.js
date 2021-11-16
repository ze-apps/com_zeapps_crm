app.controller("ComZeappsCrmProductViewCtrl", ["$scope", "$location", "zeHttp", "$uibModal", "toasts", "menu",
    function ($scope, $location, zhttp, $uibModal, toasts, menu) {

        menu("com_ze_apps_sales", "com_zeapps_crm_product");

        $scope.currentBranch = {};
        $scope.tree = {
            branches: []
        };
        $scope.filters = {
            main: [
                {
                    format: 'input',
                    field: 'ref LIKE',
                    type: 'text',
                    label: __t("Reference")
                },
                {
                    format: 'input',
                    field: 'name LIKE',
                    type: 'text',
                    label: __t("Product Name")
                }
            ]
        };
        $scope.filter_model = {};
        $scope.page = 1;
        $scope.pageSize = 15;


        $scope.update = update;
        $scope.loadList = loadList;
        $scope.goTo = goTo;
        $scope.delete = del;
        $scope.delete_category = delete_category;
        $scope.force_delete_category = force_delete_category;

        var showSubCats = false;
        $scope.isSubCatOpen = function () {
            return showSubCats;
        };
        $scope.openSubCats = function () {
            showSubCats = true;
        };
        $scope.closeSubCats = function () {
            showSubCats = false;
        };

        $scope.sortableOptions = {
            stop: sortableStop
        };



        function update(branch) {
            $scope.currentBranch = branch;
            loadList();
        }

        function loadList() {
            var id = $scope.currentBranch ? $scope.currentBranch.id : 0;
            var offset = ($scope.page - 1) * $scope.pageSize;
            var formatted_filters = angular.toJson($scope.filter_model);

            zhttp.crm.product.modal(id, $scope.pageSize, offset, formatted_filters).then(function (response) {
                if (response.status == 200) {
                    $scope.products = response.data.data;
                    $scope.total = response.data.total;
                }
            });
        }

        function goTo(id) {
            $location.url('/ng/com_zeapps_crm/product/' + id);
        }

        function getTree() {
            zhttp.crm.category.tree().then(function (response) {
                if (response.status == 200) {
                    $scope.tree.branches = response.data;
                    $scope.currentBranch = $scope.tree.branches[0];
                    loadList();
                }
            });
        }
        getTree();

        function sortableStop() {
            var data = {
                categories: []
            };
            for (var i = 0; i < $scope.currentBranch.branches.length; i++) {
                $scope.currentBranch.branches[i].sort = i;
                data.categories[i] = $scope.currentBranch.branches[i];
            }
            zhttp.crm.category.update_order(data).then(function (response) {
                if (response.status != 200) {
                    toasts("danger", __t("There was an error when trying to access the Server, please try again ! If the problem persists contact the administrator of this website."));
                }
            });
        }

        function del(product) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: "/assets/angular/popupModalDeBase.html",
                controller: "ZeAppsPopupModalDeBaseCtrl",
                size: "lg",
                resolve: {
                    titre: function () {
                        return __t("Warning");
                    },
                    msg: function () {
                        return __t("Would you like to permanently remove this product?");
                    },
                    action_danger: function () {
                        return __t("Cancel");
                    },
                    action_primary: function () {
                        return false;
                    },
                    action_success: function () {
                        return __t("I confirm the deletion");
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == "danger") {

                } else if (selectedItem.action == "success") {
                    zhttp.crm.product.del(product.id).then(function (response) {
                        if (response.status == 200) {
                            $scope.products.splice($scope.products.indexOf(product), 1);
                        }
                    });
                }

            }, function () {
            });

        }

        function delete_category(id) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: "/assets/angular/popupModalDeBase.html",
                controller: "ZeAppsPopupModalDeBaseCtrl",
                size: "lg",
                resolve: {
                    titre: function () {
                        return __t("Warning");
                    },
                    msg: function () {
                        return __t("Would you like to permanently delete this category?");
                    },
                    action_danger: function () {
                        return __t("Cancel");
                    },
                    action_primary: function () {
                        return false;
                    },
                    action_success: function () {
                        return __t("I confirm the deletion");
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == "danger") {

                } else if (selectedItem.action == "success") {
                    zhttp.crm.category.del(id).then(function (response) {
                        if (response.status == 200) {
                            if (typeof (response.data.error) === "undefined") {
                                if (response.data.hasProducts) {
                                    $scope.force_delete_category(id);
                                } else {
                                    $scope.currentBranch = response.data;
                                    getTree();
                                }
                            } else {
                                $scope.error = response.data.error;
                            }
                        }
                    });
                }

            }, function () {
            });

        }

        function force_delete_category(id) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: "/assets/angular/popupModalDeBase.html",
                controller: "ZeAppsPopupModalDeBaseCtrl",
                size: "lg",
                resolve: {
                    titre: function () {
                        return __t("Warning");
                    },
                    msg: function () {
                        return __t("The category or one of its subcategories always has products. If you confirm the deletion the products will be archived.");
                    },
                    action_danger: function () {
                        return __t("Cancel");
                    },
                    action_primary: function () {
                        return __t("Archive products & delete category");
                    },
                    action_success: function () {
                        return __t("Delete products & delete category");
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                if (selectedItem.action == "danger") {

                } else if (selectedItem.action == "primary") {
                    zhttp.crm.category.del(id, false).then(function (response) {
                        if (response.status == 200) {
                            $scope.currentBranch = response.data;
                            getTree();
                        }
                    });
                } else if (selectedItem.action == "success") {
                    zhttp.crm.category.del(id, true).then(function (response) {
                        if (response.status == 200) {
                            $scope.currentBranch = response.data;
                            getTree();
                        }
                    });
                }

            }, function () {
            });

        }
    }]);