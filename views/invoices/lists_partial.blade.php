<div ng-controller="ComZeappsCrmInvoiceListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-success btn-xs" ng-click="export()" ng-show="total <= 50000">{{ __t("Export") }}</button>
            <button type="button" class="btn btn-default btn-xs" disabled="disabled" ng-show="total > 50000">{{ __t("Export impossible (+ 50000 results)") }}</button>
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters"
                        data-update="loadList"></ze-filters>

            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <ze-btn fa="plus" color="success" hint="{{ __t("Invoice") }}" always-on="true"
                        ze-modalform="add"
                        data-template="templateInvoice"
                        data-title="{{ __t("Create a new invoice") }}"></ze-btn>
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
            <table class="table table-hover table-condensed table-responsive" ng-show="invoices.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __t("Creation date") }}</th>
                    <th>{{ __t("Recipient") }}</th>
                    <th>{{ __t("Label") }}</th>
                    <th class="text-right">{{ __t("Total duty") }}</th>
                    <th class="text-right">{{ __t("Total All taxes included") }}</th>
                    <th class="text-right">{{ __t("Balance") }}</th>
                    <th>{{ __t("Deadline") }}</th>
                    <th>{{ __t("Manager") }}</th>
                    <th>{{ __t("Status") }}</th>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <th></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="invoice in invoices">
                    <td ng-click="goTo(invoice.id)">@{{invoice.numerotation}}</td>
                    <td ng-click="goTo(invoice.id)">@{{invoice.date_creation | dateConvert:'date' }}</td>
                    <td ng-click="goTo(invoice.id)">

                        @{{invoice.name_company}}
                        <span ng-if="invoice.name_company && invoice.name_contact">-</span>
                        @{{invoice.name_contact ? invoice.name_contact : ''}}

                    </td>
                    <td ng-click="goTo(invoice.id)">@{{invoice.libelle}}</td>
                    <td ng-click="goTo(invoice.id)" class="text-right">@{{invoice.total_ht | currencyConvert }}</td>
                    <td ng-click="goTo(invoice.id)" class="text-right">@{{invoice.total_ttc | currencyConvert }}</td>
                    <td ng-click="goTo(invoice.id)" class="text-right"><span
                                ng-class="invoice.due > 0 ? 'text-danger':''" ng-if="invoice.due != 0">@{{invoice.due | currencyConvert}}</span>
                    </td>
                    <td ng-click="goTo(invoice.id)">@{{invoice.date_limit | dateConvert:'date' }}</td>
                    <td ng-click="goTo(invoice.id)">@{{invoice.name_user_account_manager}}</td>
                    <td ng-click="goTo(invoice.id)"><span class="text-danger"
                                                          ng-show="invoice.finalized">{{ __t("Closed") }}</span><span
                                class="text-success" ng-show="!invoice.finalized">{{ __t("Open") }}</span></td>

                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <td class="text-right">
                            <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}"
                                    ze-modalform="edit"
                                    data-edit="invoice"
                                    data-title="{{ __t("Edit invoice") }}"
                                    data-template="templateInvoice"></ze-btn>
                            <span ng-show="!invoice.finalized">
                                <ze-btn fa="trash" color="danger" hint="{{ __t("Delete") }}" direction="left" ng-click="delete(invoice)"
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