<div id="breadcrumb">{{ __t("Product attributes") }}</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="Attribut" always-on="true"
                    ze-modalform="add"
                    data-template="templateForm"
                    data-title="{{ __t("Add attribute") }}"></ze-btn>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-responsive table-hover table-condensed">
                <thead>
                <tr>
                    <th>
                        {{ __t("Name") }}
                    </th>
                    <th>
                        {{ __t("Type") }}
                    </th>
                    <th class="text-center">
                        {{ __t("Mandatory") }}
                    </th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="attribute in attributes">
                    <td>
                        @{{attribute.name}}
                    </td>
                    <td>
                        @{{attribute.type}}
                    </td>
                    <td class="text-center">
                        <i class="fa fa-fw" ng-class="attribute.required ? 'fa-check text-success' : 'fa-times text-danger'"></i>
                    </td>
                    <td class="text-right">
                        <ze-btn fa="edit" color="info" hint="{{ __t("Edit") }}" direction="left"
                                ze-modalform="edit"
                                data-edit="attribute"
                                data-template="templateForm"
                                data-title="{{ __t("Edit attribute") }}"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="{{ __t("Delete") }}" direction="left" ng-click="delete(attribute)" ze-confirmation></ze-btn>
                    </td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>