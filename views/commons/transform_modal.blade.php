<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4>{{ __t("Would you like to automatically create the following documents") }}:</h4>
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