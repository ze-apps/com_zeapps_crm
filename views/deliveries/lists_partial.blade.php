<div ng-controller="ComZeappsCrmDeliveryListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters"
                        data-update="loadList"></ze-filters>

            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <ze-btn fa="plus" color="success" hint="{{ __t("Delivery form") }}" always-on="true"
                        ze-modalform="add"
                        data-template="templateDelivery"
                        data-title="{{ __t("Create a new delivery note") }}"></ze-btn>
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
            <table class="table table-hover table-condensed table-responsive" ng-show="deliveries.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __t("Creation date") }}</th>
                    <th>{{ __t("Recipient") }}</th>
                    <th>{{ __t("Label") }}</th>
                    <th class="text-right">{{ __t("Total duty") }}</th>
                    <th class="text-right">{{ __t("Total All taxes included") }}</th>
                    <th>{{ __t("Manager") }}</th>
                    <th>{{ __t("Status") }}</th>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <th></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="delivery in deliveries">
                    <td ng-click="goTo(delivery.id)">@{{delivery.numerotation}}</td>
                    <td ng-click="goTo(delivery.id)">@{{delivery.date_creation || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(delivery.id)">

                        @{{delivery.name_company}}
                        <span ng-if="delivery.name_company && delivery.name_contact">-</span>
                        @{{delivery.name_contact ? deliverie.name_contact : ''}}

                    </td>
                    <td ng-click="goTo(delivery.id)">@{{delivery.libelle}}</td>
                    <td ng-click="goTo(delivery.id)" class="text-right">@{{delivery.total_ht | currency:'€':2}}</td>
                    <td ng-click="goTo(delivery.id)" class="text-right">@{{delivery.total_ttc | currency:'€':2}}</td>
                    <td ng-click="goTo(delivery.id)">@{{delivery.name_user_account_manager}}</td>
                    <td ng-click="goTo(delivery.id)"><span class="text-danger"
                                                           ng-show="delivery.finalized">Clôturée</span><span
                                class="text-success" ng-show="!delivery.finalized">Ouvert</span></td>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <td class="text-right">
                                <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}"
                                        ze-modalform="edit"
                                        data-edit="delivery"
                                        data-title="{{ __t("Edit the delivery note") }}"
                                        data-template="templateDelivery"></ze-btn>
                                <span ng-show="!delivery.finalized">
                                <ze-btn fa="trash" color="danger" hint="{{ __t("Delete") }}" direction="left"
                                        ng-click="delete(delivery)" ze-confirmation></ze-btn>
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