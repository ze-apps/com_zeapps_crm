<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            {{ __t("Payment date") }} : @{{ payment.date_payment | dateConvert:'date' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{ __t("Amount") }} : @{{ payment.total | currencyConvert }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {{ __t("Mode of payment") }} : @{{ payment.type_payment_label }}
        </div>
    </div>
    <div class="row" ng-if="payment_lines.length">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="payment_lines.length">
                <thead>
                <tr>
                    <th>{{ __t("Date of invoice") }}</th>
                    <th>{{ __t("Invoice No.") }}</th>
                    <th>{{ __t("Object") }}</th>
                    <th class="text-right">{{ __t("Amount Invoice") }}</th>
                    <th class="text-right">{{ __t("Amount received") }}</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="line in payment_lines">
                    <td>@{{ line.invoice_data.date_creation | dateConvert:'date' }}</td>
                    <td>@{{ line.invoice_data.numerotation }}</td>
                    <td>@{{ line.invoice_data.libelle }}</td>
                    <td class="text-right">@{{ line.invoice_data.total_ttc | currencyConvert }}</td>
                    <td class="text-right">@{{ line.amount | currencyConvert }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">{{ __t("Close") }}</button>
</div>