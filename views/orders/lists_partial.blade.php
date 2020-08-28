<div ng-controller="ComZeappsCrmOrderListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters"
                        data-update="loadList"></ze-filters>

            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <ze-btn fa="plus" color="success" hint="Commande" always-on="true"
                        ze-modalform="add"
                        data-template="templateOrder"
                        data-title="Créer une nouvelle commande"></ze-btn>
            @endif
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="orders.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date de création</th>
                    <th>Destinataire</th>
                    <th>Libelle</th>
                    <th class="text-right">Total HT</th>
                    <th class="text-right">Total TTC</th>
                    <th>Date limite</th>
                    <th>Responsable</th>
                    <th class="text-right">%</th>
                    <th>Statut</th>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <th></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="order in orders">
                    <td ng-click="goTo(order.id)">@{{order.numerotation}}</td>
                    <td ng-click="goTo(order.id)">@{{order.date_creation || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(order.id)">

                        @{{order.name_company}}
                        <span ng-if="order.name_company && order.name_contact">-</span>
                        @{{order.name_contact ? order.name_contact : ''}}

                    </td>
                    <td ng-click="goTo(order.id)">@{{order.libelle}}</td>
                    <td ng-click="goTo(order.id)" class="text-right">@{{order.total_ht | currency:'€':2}}</td>
                    <td ng-click="goTo(order.id)" class="text-right">@{{order.total_ttc | currency:'€':2}}</td>
                    <td ng-click="goTo(order.id)">@{{order.date_limit || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(order.id)">@{{order.name_user_account_manager}}</td>
                    <td ng-click="goTo(order.id)" class="text-right">@{{order.probability | number:2}}</td>
                    <td ng-click="goTo(order.id)"><span class="text-danger"
                                                        ng-show="order.finalized">Clôturée</span><span
                                class="text-success" ng-show="!order.finalized">Ouvert</span></td>

                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <td class="text-right">
                            <ze-btn fa="edit" color="info" direction="left" hint="Editer"
                                    ze-modalform="edit"
                                    data-edit="order"
                                    data-title="Editer la commande"
                                    data-template="templateOrder"></ze-btn>
                            <span ng-show="!order.finalized">
                                <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(order)"
                                        ze-confirmation></ze-btn>
                            </span>
                        </td>
                    @endif
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

</div>