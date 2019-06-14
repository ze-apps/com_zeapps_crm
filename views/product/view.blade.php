<div id="breadcrumb">Produits</div>
<div id="content">

    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"></zeapps-happylittletree>
            </div>
        </div>

        <div class="col-md-9">
            <div class="clearfix">
                <div class="pull-right" ng-show="currentBranch.id != -1">
                    <span ng-show="currentBranch.branches">
                        <ze-btn fa="eye" color="info" hint="Montrer les sous-catégories" always-on="true" ng-hide="isSubCatOpen()" ng-click="openSubCats()"></ze-btn>
                        <ze-btn fa="eye-slash" color="info" hint="Masquer les sous-catégories" always-on="true" ng-show="isSubCatOpen()" ng-click="closeSubCats()"></ze-btn>
                    </span>
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_category/@{{ currentBranch.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Sous-categorie</a>
                    <a class='btn btn-xs btn-success' ng-href='/ng/com_zeapps_crm/product/new_product/@{{ currentBranch.id || 0 }}'><span class='fa fa-plus' aria-hidden='true'></span> Produit</a>
                </div>
                <h3 class="text-capitalize active-category-title">
                    @{{ currentBranch.name }}
                    <a class="btn btn-info btn-xs" ng-href="/ng/com_zeapps_crm/product/category/@{{ currentBranch.id }}/edit" ng-show="currentBranch.id > 0">
                        <span class="fas fa-fw fa-edit"></span>
                    </a>
                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete_category(currentBranch.id)" ng-show="currentBranch.id > 0">
                        <span class="fa fa-fw fa-trash"></span>
                    </button>
                </h3>
                <div class="row" ng-show="isSubCatOpen()">
                    <div class="col-md-12">
                        <h5>Sous-Categories</h5>
                        <ul ui-sortable="sortableOptions" id="sortable" class="branch-list list-unstyled col-md-4" ng-model="currentBranch.branches">
                            <li id="@{{ branch.id }}" class="branch branch-sortable" ng-repeat="branch in currentBranch.branches">
                                <span class="glyphicon glyphicon-resize-vertical" aria-hidden="true"></span>
                                @{{ branch.name }} <i>(@{{ branch.nb_products }} produit<span ng-show="branch.nb_products > 1">s</span>)</i>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 clearfix">
                        <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>
                        <h5>
                            Produits
                        </h5>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-center" ng-show="total > pageSize">
                                    <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
                                        class="pagination-sm" boundary-links="true" max-size="9"
                                        previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
                                </div>
                            </div>
                        </div>

                        <table class="table table-hover table-condensed table-responsive" ng-show="products.length > 0">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Référence</th>
                                <th>Nom du produit</th>
                                <th>Compte comptable</th>
                                <th class="text-right">Prix HT</th>
                                <th class="text-right">TVA (%)</th>
                                <th class="text-right">Prix TTC</th>
                                <th class="text-center">Actif</th>
                                <th class="text-center">Remise interdite</th>
                                <th class="text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="leaf" ng-repeat="product in products | filter:quicksearch | orderBy: 'name'">
                                <td ng-click="goTo(product.id)">
                                    <i class="fa fa-tag" ng-if="product.type_product != 'pack'"></i>
                                    <i class="fa fa-tags" ng-if="product.type_product == 'pack'"></i>
                                </td>
                                <td ng-click="goTo(product.id)">@{{ product.ref }}</td>
                                <td ng-click="goTo(product.id)">@{{ product.name }}</td>
                                <td ng-click="goTo(product.id)"><span ng-if="product.type_product != 'pack'">@{{ product.accounting_number }}</span></td>
                                <td ng-click="goTo(product.id)" class="text-right">@{{ product.price_ht | currency:'€':2 }}</td>
                                <td ng-click="goTo(product.id)" class="text-right"><span ng-if="product.type_product != 'pack'">@{{ product.value_taxe | currency:'%':2 }}</span></td>
                                <td ng-click="goTo(product.id)" class="text-right">@{{ product.price_ttc | currency:'€':2 }}</td>
                                <td ng-click="goTo(product.id)" class="text-center"><span ng-if="product.active">OUI</span><span ng-if="!product.active">NON</span></td>
                                <td ng-click="goTo(product.id)" class="text-center"><span ng-if="product.discount_prohibited">OUI</span></td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-xs btn-danger" ng-click="delete(product)">
                                        <i class="fa fa-trash fa-fw"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
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
        </div>
    </div>

</div>