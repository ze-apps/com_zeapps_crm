<div class="row" ng-controller="ComZeAppsCrmStockMvtFormCtrl">
    <div class="col-md-12">
        <div class="col-md-7">
            <div class="form-group">
                <label>{{ __t("Label") }}</label>
                <input class="form-control" type="text" ng-model="form.label">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>{{ __t("Quantity") }}</label>
                <input class="form-control" type="number" ng-model="form.qty">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>{{ __t("Date") }}</label>
                <input class="form-control" type="date" ng-model="form.date_mvt">
            </div>
        </div>
    </div>
</div>