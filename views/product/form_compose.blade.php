<div id="breadcrumb">{{ __t("Products") }}</div>
<div id="content">
    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"</zeapps-happylittletree>
            </div>
        </div>

        <form name="newProduct" class="col-md-9">
            <div class="well">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __t("Reference") }}</label>
                            <input type="text" ng-model="form.ref" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>{{ __t("Product Name") }} <span class="required">*</span></label>
                            <input type="text" ng-model="form.name" class="form-control" ng-required="true">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>{{ __t("Stored product") }}</label>
                            <div class="input-group">
                                <input type="text" ng-model="form.name_stock" class="form-control" disabled>

                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" ng-click="removeProductStock()"
                                            ng-show="form.id_stock != 0 && form.name_stock != undefined">x
                                    </button>
                                    <button class="btn btn-default" type="button" ng-click="loadProductStock()">...</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("Cumulated price all taxes included") }} <span class="required">*</span></label>
                            <input type="number" ng-model="form.price_ttc" ng-change="updatePrice()" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("Accounting Account") }}</label>
                            <span   ze-modalsearch="loadAccountingNumber"
                                    data-http="accountingNumberHttp"
                                    data-model="form.accounting_number"
                                    data-fields="accountingNumberFields"
                                    data-template-new="accountingNumberTplNew"
                                    data-title="{{ __t("Choose an accounting account") }}"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" ng-model="form.auto">
                                {{ __t("Automatic price update") }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __t("Description") }}</label>
                            <input type="text" ng-model="form.description" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <span class="required">*</span> {{ __t("Required fields") }}
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-sm btn-success pull-right" ng-click="ajouter_ligne()">{{ __t("Add item") }}</button>
                    <h4>Liste</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-stripped table-condensed">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ __t("Reference") }}</th>
                                <th>{{ __t("Name") }}</th>
                                <th class="text-right">{{ __t("Quantity") }}</th>
                                <th class="text-right">{{ __t("Out of taxes price") }}Prix HT</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="line in form.lines">
                                <td>
                                    <i class="fa fa-tag" ng-if="line.product.compose == 0"></i>
                                    <i class="fa fa-tags" ng-if="line.product.compose != 0"></i>
                                </td>
                                <td>@{{ line.product.ref }}</td>
                                <td>@{{ line.product.name }}</td>

                                <td class="text-right" ng-hide="lineForm.index == $index">@{{ line.quantite }}</td>
                                <td class="text-right" ng-show="lineForm.index == $index">
                                    <input type="number" class="form-control input-sm" ng-model="lineForm.quantite">
                                </td>

                                <td class="text-right">@{{ line.product.price_ht }}</td>

                                <td class="text-right" ng-hide="lineForm.index == $index">
                                    <button type="button" class="btn btn-xs btn-info" ng-click="edit(line)" ng-hide="lineForm.index != undefined">
                                        <i class="fas fa-edit fa-fw"></i>
                                    </button>
                                </td>
                                <td class="text-right" ng-show="lineForm.index == $index">
                                    <button type="button" class="btn btn-xs btn-success" ng-click="validate(line)">
                                        <i class="fa fa-check fa-fw"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-danger" ng-click="cancelEdit()">
                                        <i class="fa fa-times fa-fw"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <form-buttons></form-buttons>
        </form>
    </div>

</div>