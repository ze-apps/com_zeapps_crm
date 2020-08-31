<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __t("Label") }}</label>
            <input type="text" ng-model="form.name" class="form-control">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ __t("Type") }}</label>
            <select class="form-control" ng-model="form.type">
                <option value="text">{{ __t("Text") }}</option>
                <option value="number">{{ __t("Digital") }}</option>
                <option value="textarea">{{ __t("Long Text") }}</option>
                <option value="checkbox">{{ __t("Boolean") }}</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>
                <input type="checkbox" ng-model="form.required">
                {{ __t("Mandatory") }}
            </label>
        </div>
    </div>
</div>