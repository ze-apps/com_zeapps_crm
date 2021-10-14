<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>


<div class="modal-body">
    <div class="row">

        <div class="col-md-3">
            <div class="root modal-root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"</zeapps-happylittletree>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="text-capitalize active-category-title">
                @{{ currentBranch.name }}
            </h3>

            <div class="row">
                <div class="col-md-12">
                    <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center" ng-show="total > pageSize">
                        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
                            class="pagination-sm" boundary-links="true" max-size="9"
                            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                    </div>
                </div>
            </div>

            <table class="table table-striped table-condensed">
                <tr>
                    <th></th>
                    <th>{{ __t("Reference") }}</th>
                    <th>{{ __t("Product Name") }}</th>
                    <th>{{ __t("Out of taxes price") }}</th>
                    <th>{{ __t("Active") }}</th>
                </tr>
                <tr class="leaf" ng-repeat="product in products | orderBy: 'name'" ng-click="select_product(product)">
                    <td>
                        <i class="fa fa-tag" ng-if="product.type_product != 'pack'"></i>
                        <i class="fa fa-tags" ng-if="product.type_product == 'pack'"></i>
                    </td>
                    <td>@{{ product.ref }}</td>
                    <td>@{{ product.name }}</td>
                    <td>@{{ product.price_ht | currency }}</td>
                    <td><span ng-if="product.active">{{ __t("YES") }}</span><span ng-if="!product.active">{{ __t("NO") }}</span></td>
                </tr>
            </table>

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center" ng-show="total > pageSize">
                        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
                            class="pagination-sm" boundary-links="true" max-size="9"
                            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()">{{ __t("Cancel") }}</button>
</div>