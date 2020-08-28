<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4>{{ __t("Documents created during duplication") }} :</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-condensed table-hover table-responsive">
                <tbody>
                <tr ng-show="documents.quotes">
                    <td>
                        {{ __t("Quote") }} {{ __t("No.") }}@{{documents.quotes.numerotation}}
                    </td>
                    <td></td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="{{ __t("Consult") }}" always-on="true" ng-click="goTo('quote', documents.quotes.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="{{ __t("Get PDF") }}" always-on="true" ng-click="pdf('quote', documents.quotes.id)"></ze-btn>
                    </td>
                </tr>
                <tr ng-show="documents.orders">
                    <td>
                        {{ __t("Order") }} {{ __t("No.") }}@{{documents.orders.numerotation}}
                    </td>
                    <td></td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="{{ __t("Consult") }}" always-on="true" ng-click="goTo('order', documents.orders.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="{{ __t("Get PDF") }}" always-on="true" ng-click="pdf('order', documents.orders.id)"></ze-btn>
                    </td>
                </tr>
                <tr ng-show="documents.invoices">
                    <td>
                        {{ __t("Invoice") }} {{ __t("No.") }}@{{documents.invoices.numerotation || "non cloturée"}}
                    </td>
                    <td>
                        <ze-btn fa="lock" color="warning" hint="{{ __t("Close") }}" always-on="true" ng-click="finalize(documents.invoices.id)" ng-if="!documents.invoices.numerotation"></ze-btn>
                    </td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="{{ __t("Consult") }}" always-on="true" ng-click="goTo('invoice', documents.invoices.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="{{ __t("Get PDF") }}" always-on="true" ng-click="pdf('invoice', documents.invoices.id)"></ze-btn>
                    </td>
                </tr>
                <tr ng-show="documents.deliveries">
                    <td>
                        {{ __t("Delivery form") }} {{ __t("No.") }}@{{documents.deliveries.numerotation}}
                    </td>
                    <td></td>
                    <td class="text-center">
                        <ze-btn fa="eye" color="info" hint="{{ __t("Consult") }}" always-on="true" ng-click="goTo('delivery', documents.deliveries.id)"></ze-btn>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="download" color="primary" hint="{{ __t("Get PDF") }}" always-on="true" ng-click="pdf('delivery', documents.deliveries.id)"></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">{{ __t("Cancel") }}</button>
</div>