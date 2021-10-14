<div ng-controller="ZeAppsCrmModalFormActivityCtrl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>{{ __t("title") }}</label>
                <input type="text" class="form-control" ng-model="form.libelle">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>{{ __t("Person responsible") }} <span class="required">*</span></label>
                <span   ze-modalsearch="loadAccountManager"
                        data-http="accountManagerHttp"
                        data-model="form.name_user"
                        data-fields="accountManagerFields"
                        data-title="{{ __t("Choose a person") }}"></span>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __t("Type") }}</label>
                <select class="form-control" ng-model="form.id_type" ng-change="updateType()">
                    <option ng-repeat="type in activity_types" ng-value="@{{type.id}}">
                        @{{type.label}}
                    </option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __t("Deadline") }}</label>
                <input type="date" class="form-control" ng-model="form.deadline">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __t("Status") }}</label>
                <select class="form-control" ng-model="form.status">
                    <option value="A faire">{{ __t("To do") }}</option>
                    <option value="Terminé">{{ __t("Finished") }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <textarea class="form-control" ng-model="form.description" rows="10" placeholder="{{ __t("Description...") }}"></textarea>
            </div>
        </div>
    </div>
</div>