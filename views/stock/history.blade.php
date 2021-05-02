<div class="row">
    <div class="col-md-12 text-right" ng-show="filter_model.id_warehouse">
        <ze-btn fa="plus" color="success" hint="{{ __t("Add transfer") }}" always-on="true"
                ze-modalform="addTransfert"
                data-template="templateTransfert"
                data-title="{{ __t("Add a transfer between 2 warehouses") }}"></ze-btn>
        <ze-btn fa="plus" color="success" hint="{{ __t("Add stock movement") }}" always-on="true"
                ze-modalform="addMvt"
                data-template="templateMvt"
                data-title="{{ __t("Add stock movement") }}"></ze-btn>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <span class="text-success">{{ __t("Imports") }}</span> -
        <span class="text-danger">{{ __t("Exports") }}</span><br>
        <span class="text-success"><i class="fa fa-fw fa-eye"></i></span> {{ __t("Label") }}Mouvements pris en comptes dans les statistiques de rupture de stocks et réapprovisionnement<br>
        <span class="text-danger"><i class="fa fa-fw fa-eye-slash"></i></span> {{ __t("Label") }}Mouvements ignorés dans les statistiques de rupture de stocks et réapprovisionnement
    </div>
</div>


<div class="text-center" ng-show="total > pageSize">
    <ul uib-pagination total-items="total" ng-model="page.current" items-per-page="pageSize" ng-change="loadList()"
        class="pagination-sm" boundary-links="true" max-size="15"
        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed table-responsive">
            <thead>
            <tr>
                <th>{{ __t("Label") }}Date</th>
                <th>{{ __t("Label") }}Libellé</th>
                <th class="text-right">{{ __t("Qty") }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="movement in product_stock.movements"
                ng-class="backgroundOf(movement)"
            >
                <td>@{{movement.date_mvt | dateConvert:'date' }}</td>
                <td>@{{movement.label}}</td>
                <td class="text-right">@{{movement.qty}}</td>
                <td class="text-right">
                    <button type="button" class="btn btn-xs btn-success" ng-show="movement.ignored == 0" ng-click="setIgnoredTo(movement, 1)">
                        <i class="fa fa-fw fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-danger" ng-show="movement.ignored == 1" ng-click="setIgnoredTo(movement, 0)">
                        <i class="fa fa-fw fa-eye-slash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="text-center" ng-show="total > pageSize">
    <ul uib-pagination total-items="total" ng-model="page.current" items-per-page="pageSize" ng-change="loadList()"
        class="pagination-sm" boundary-links="true" max-size="15"
        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
</div>