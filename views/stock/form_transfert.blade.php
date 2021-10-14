<div ng-controller="ComZeAppsCrmStockTransfertFormCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>{{ __t("Label") }}</label>
                <input class="form-control" type="text" ng-model="form.label">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __t("Departure warehouse") }}</label>
                <select ng-model="form.src" class="form-control">
                    <option ng-repeat="warehouse in warehouses" value="@{{warehouse.id}}">
                        @{{warehouse.label}}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <label>{{ __t("Arrival warehouse") }}</label>
            <select ng-model="form.trgt" class="form-control">
                <option ng-repeat="warehouse in warehouses" value="@{{warehouse.id}}">
                    @{{warehouse.label}}
                </option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __t("Quantity") }}</label>
                <input class="form-control" type="number" ng-model="form.qty">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __t("Date") }}</label>
                <input class="form-control" type="date" ng-model="form.date_mvt">
            </div>
        </div>
    </div>
</div>