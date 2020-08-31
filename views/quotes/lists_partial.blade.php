<div ng-controller="ComZeappsCrmQuoteListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>

            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <ze-btn fa="plus" color="success" hint="{{ __t("Quote") }}" always-on="true"
                        ze-modalform="add"
                        data-template="templateQuote"
                        data-title="{{ __t("Create a new quote") }}"></ze-btn>
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
            <table class="table table-hover table-condensed table-responsive" ng-show="quotes.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __t("Creation date") }}</th>
                    <th>{{ __t("Recipient") }}</th>
                    <th>{{ __t("Label") }}</th>
                    <th class="text-right">{{ __t("Total duty") }}</th>
                    <th class="text-right">{{ __t("Total All taxes included") }}</th>
                    <th>{{ __t("Deadline") }}</th>
                    <th>{{ __t("Manager") }}</th>
                    <th class="text-right">{{ __t("Probability") }}</th>
                    <th>{{ __t("Status") }}</th>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <th></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="quote in quotes">
                    <td ng-click="goTo(quote.id)">@{{quote.numerotation}}</td>
                    <td ng-click="goTo(quote.id)">@{{quote.date_creation || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(quote.id)">

                        @{{quote.name_company}}
                        <span ng-if="quote.name_company && quote.name_contact">-</span>
                        @{{quote.name_contact ? quote.name_contact : ''}}

                    </td>
                    <td ng-click="goTo(quote.id)">@{{quote.libelle}}</td>
                    <td ng-click="goTo(quote.id)" class="text-right">@{{quote.total_ht | currency:'€':2}}</td>
                    <td ng-click="goTo(quote.id)" class="text-right">@{{quote.total_ttc | currency:'€':2}}</td>
                    <td ng-click="goTo(quote.id)">@{{quote.date_limit || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(quote.id)">@{{quote.name_user_account_manager}}</td>
                    <td ng-click="goTo(quote.id)" class="text-right">@{{quote.probability | number:2}}</td>
                    <td ng-click="goTo(quote.id)">@{{ showStatus(quote.status) }}</td>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <td class="text-right">
                            <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}"
                                    ze-modalform="edit"
                                    data-edit="quote"
                                    data-title="{{ __t("Edit quote") }}"
                                    data-template="templateQuote"></ze-btn>
                            <ze-btn fa="trash" color="danger" hint="{{ __t("Delete") }}" direction="left" ng-click="delete(quote)" ze-confirmation></ze-btn>
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