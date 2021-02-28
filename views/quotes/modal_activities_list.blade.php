<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
        <div class="row">
            <div class="col-md-12" style="margin-bottom: 15px;">
                <div class="pull-right">
                    <ze-btn data-fa="plus" data-hint="{{ __t("Activity") }}" always-on="true" data-color="success"
                            ze-modalform="addActivity"
                            data-template="quoteActivityTplUrl"
                            data-title="{{ __t("Create an activity") }}"></ze-btn>
                </div>
            </div>
        </div>
    @endif


    <table class="table table-striped table-condensed">
        <tr>
            <th></th>
            <th>{{ __t("Date") }}</th>
            <th>{{ __t("Label") }}</th>
            <th>{{ __t("Deadline") }}</th>
            <th>{{ __t("Status") }}</th>
            <th>{{ __t("Manager") }}</th>
            <th></th>
        </tr>
        <tr class="leaf" ng-repeat="activity in activities | orderBy: '-deadline' track by $index">
            <td></td>
            <td>@{{ activity.created_at_info | date:'dd/MM/yyyy' }}</td>
            <td>@{{ activity.libelle }}
            <div>@{{ activity.description }}</div></td>
            <td>@{{ activity.deadline | date:'dd/MM/yyyy' }}</td>
            <td>@{{ activity.status }}</td>
            <td>@{{ activity.name_user }}</td>
            <td class="text-right">
                <ze-btn data-fa="edit" data-hint="{{ __t("Edit") }}" data-direction="left" data-color="info"
                        ze-modalform="editActivity"
                        data-edit="activity"
                        data-template="quoteActivityTplUrl"
                        data-title="{{ __t("Edit activity") }}"></ze-btn>
                <ze-btn data-fa="trash" data-hint="{{ __t("Delete") }}" data-direction="left"
                        data-color="danger" ng-click="deleteActivity(activity)"
                        ze-confirmation></ze-btn>
            </td>
        </tr>
    </table>
</div>

<div class="modal-footer">
    <button class="btn btn-danger" type="button" ng-click="cancel()">{{ __t("Close") }}</button>
</div>