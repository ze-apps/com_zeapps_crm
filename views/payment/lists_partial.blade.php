<div ng-controller="ComZeappsCrmPaymentListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>

            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <ze-btn fa="plus" color="success" hint="Encaissement" always-on="true"
                        ze-modalform="add"
                        data-template="templatePayment"
                        data-title="Ajouter un nouvel encaissement"></ze-btn>
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
            <table class="table table-hover table-condensed table-responsive" ng-show="payments.length">
                <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Montant</th>
                    <th>Type encaissement</th>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                    <th></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="payment in payments">
                    <td ng-click="goTo(payment.id)">@{{ payment.date_payment || "-"  | date:'dd/MM/yyyy' }}</td>
                    <td ng-click="goTo(payment.id)" class="text-right">@{{ payment.total | currency:'€':2 }}</td>
                    <td ng-click="goTo(payment.id)">@{{ payment.type_payment_label }}</td>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <td class="text-right">
                            <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(payment)" ze-confirmation></ze-btn>
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