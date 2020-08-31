<div id="breadcrumb">{{ __t("Stocks") }}</div>
<div id="content">
    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"></zeapps-happylittletree>
            </div>
        </div>
        <div class="col-md-9">
            <form>

                <div class="row">
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success btn-sm" ng-click="export()">{{ __t("Export") }}</button>
                        <button type="button" class="btn btn-default btn-sm" ng-click="activerInventaire()" ng-show="modeInventaire==false">{{ __t("Activate inventory") }}</button>
                        <button type="button" class="btn btn-danger btn-sm" ng-click="desactiverInventaire()" ng-show="modeInventaire">{{ __t("Deactivate inventory") }}</button>
                    </div>
                    <div class="col-md-8">
                        <ze-filters data-model="filter_model" data-filters="filters"
                                    data-update="loadList"></ze-filters>
                    </div>
                </div>

                <div class="text-center" ng-show="total > pageSize">
                    <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize"
                        ng-change="loadList()"
                        class="pagination-sm" boundary-links="true" max-size="15"
                        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-condensed table-responsive"
                               ng-show="product_stocks.length">
                            <thead>
                            <tr>
                                <th>{{ __t("Ref") }}</th>
                                <th>{{ __t("Label") }}</th>
                                <th class="text-right">{{ __t("Qty") }}</th>
                                <th class="text-right">{{ __t("Unit value") }}</th>
                                <th class="text-right">{{ __t("Stock Value") }}</th>
                                <th class="text-right">{{ __t("Date Outage") }}</th>
                                <th class="text-right" ng-if="filter_model.id_warehouse">{{ __t("Estimated replenishment date") }}
                                </th>
                                <th ng-show="modeInventaire">{{ __t("Qty raised") }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="product_stock in product_stocks">
                                <td ng-click="goTo(product_stock.id)">
                                    @{{product_stock.ref}}
                                </td>
                                <td ng-click="goTo(product_stock.id)">
                                    @{{product_stock.name}}
                                </td>
                                <td class="text-right" ng-click="goTo(product_stock.id)">
                                    <span ng-class="product_stock.qty < 0 ? 'text-danger' : (product_stock.qty > 0 ? 'text-success' : 'text-info')">
                                        @{{product_stock.qty || 0 | number:2}}
                                    </span>
                                </td>
                                <td class="text-right" ng-click="goTo(product_stock.id)">
                                    @{{product_stock.price_unit_stock | currency:'€':2}}
                                </td>
                                <td class="text-right" ng-click="goTo(product_stock.id)">
                                    <span ng-class="(product_stock.price_unit_stock * product_stock.qty) < 0 ? 'text-danger' : (product_stock.qty > 0 ? 'text-success' : 'text-info')" ng-if="(product_stock.price_unit_stock * product_stock.qty) != 0">
                                        @{{ (product_stock.price_unit_stock * product_stock.qty) | currency:'€':2}}
                                    </span>
                                </td>
                                <td class="text-right" ng-click="goTo(product_stock.id)">
                                    <span ng-class="product_stock.classRupture">
                                        @{{ product_stock.timeleft }} @{{ product_stock.dateRupture ? ' (' +  product_stock.dateRupture + ')' : '' }}
                                    </span>
                                </td>
                                <td class="text-right" ng-click="goTo(product_stock.id)"
                                    ng-if="filter_model.id_warehouse">
                                    <span ng-class="product_stock.classResupply">
                                        @{{ product_stock.timeResupply }}@{{ product_stock.dateResupply ? ' (' +  product_stock.dateResupply + ')' : '' }}
                                    </span>
                                </td>
                                <td ng-show="modeInventaire"><input type="text" class="form-control" ng-model="product_stock.qty_inventaire" ng-keyup="keyEventInventaire()"></td>
                            </tr>
                            <tr ng-show="showSaveInventaire">
                                <td colspan="8" class="text-right"><button type="button" class="btn btn-success btn-sm" ng-click="enregistrer_inventaire()">{{ __t("Save inventory") }}</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-center" ng-show="total > pageSize">
                    <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize"
                        ng-change="loadList()"
                        class="pagination-sm" boundary-links="true" max-size="15"
                        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                </div>

            </form>
        </div>
    </div>


</div>