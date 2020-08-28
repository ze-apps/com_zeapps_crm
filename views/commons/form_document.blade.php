<div ng-controller="ZeAppsCrmModalFormDocumentCtrl">
    <button type="button" class="btn btn-xs btn-success" ngf-select="upload($files)" >
        {{ __t("Choose a document") }}
    </button>
    <div class="form-group">
        <label>{{ __t("Document title") }}</label>
        <input type="text" class="form-control" ng-model="form.label">
    </div>
    <div class="form-group">
        <textarea class="form-control" ng-model="form.description" rows="6" placeholder="{{ __t("Description...") }}"></textarea>
    </div>
</div>