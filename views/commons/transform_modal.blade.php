<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4>{{ __t("Would you like to automatically create the following documents") }}:</h4>
        </div>
    </div>
    <div class="row" ng-show="showError">
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
                <ul>
                    <li ng-repeat="msg in msgError">@{{ msg }}</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.quotes">
                {{ __t("Quote") }}
            </label>
        </div>
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.orders">
                {{ __t("Order") }}
            </label>
        </div>
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.invoices">
                {{ __t("Invoice") }}
            </label>
        </div>
        
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.deposit_invoices">
                {{ __t("Deposit invoice") }}
            </label>
        </div>
        <div class="col-md-12" ng-show="form.deposit_invoices">
            <div class="form-group">
                <label>{{ __t("Type") }}</label>
                <select class="form-control" ng-model="form.type_deposit">
                    <option ng-repeat="typeDeposit in types_deposit" ng-value="typeDeposit.value">
                        @{{ typeDeposit.label }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label>{{ __t("Value") }}</label>
                <input type="text" class="form-control" ng-model="form.deposit_invoices_value">
            </div>
        </div>


        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.invoice_with_down_payment_deduction" ng-change="loadInvoiceRelated()">
                {{ __t("Invoice with down payment deduction") }}
            </label>
        </div>
        <div class="col-md-1" ng-show="form.invoice_with_down_payment_deduction">

        </div>
        <div class="col-md-11" ng-show="form.invoice_with_down_payment_deduction">
            <label>
                {{ __t("Choose which invoices to deduct") }}
            </label>

            <table class="table table-hover table-condensed table-responsive" ng-show="invoices.length">
                <thead>
                <tr>
                    <th></th>
                    <th>#</th>
                    <th>{{ __t("Creation date") }}</th>
                    <th>{{ __t("Label") }}</th>
                    <th class="text-right">{{ __t("Total duty") }}</th>
                    <th class="text-right">{{ __t("Total All taxes included") }}</th>
                    <th class="text-right">{{ __t("Balance") }}</th>
                    <th>{{ __t("Status") }}</th>
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <th></th>
                    @endif
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="invoice in invoices">
                    <td><input type='checkbox' ng-click="checkInvoice(invoice.id)"></td>
                    <td>@{{invoice.numerotation}}</td>
                    <td>@{{invoice.date_creation | dateConvert:'date' }}</td>
                    <td>@{{invoice.libelle}}</td>
                    <td class="text-right">@{{invoice.total_ht | currencyConvert }}</td>
                    <td class="text-right">@{{invoice.total_ttc | currencyConvert }}</td>
                    <td class="text-right"><span
                                ng-class="invoice.due > 0 ? 'text-danger':''" ng-if="invoice.due != 0">@{{invoice.due | currencyConvert}}</span>
                    </td>
                    <td><span class="text-danger"
                                                          ng-show="invoice.finalized">{{ __t("Closed") }}</span><span
                                class="text-success" ng-show="!invoice.finalized">{{ __t("Open") }}</span></td>
                </tr>
                </tbody>
            </table>
        </div>


        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.credit">
                {{ __t("Credit") }}
            </label>
        </div>
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.deliveries">
                {{ __t("Delivery form") }}
            </label>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">{{ __t("Cancel") }}</button>
    <button type="sumbit" class="btn btn-success" ng-click="transform()">{{ __t("Convert") }}</button>
</div>