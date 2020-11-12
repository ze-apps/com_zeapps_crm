<div id="breadcrumb">{{ __t("Email template") }}</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <a class="btn btn-xs btn-success" href="/ng/com_zeapps_crm/model_email/new">
                    <i class="fa fa-fw fa-plus"></i> {{ __t("Create a template") }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed">
                <thead>
                <tr>
                    <th>{{ __t("Model name") }}</th>
                    <th>{{ __t("Subject") }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="model_email in model_emails">
                    <td ng-click="edit(model_email)">@{{ model_email.name }}</td>
                    <td ng-click="edit(model_email)">@{{ model_email.subject }}</td>

                    <td class="text-right">

                        <button type="button" class="btn btn-xs btn-success" ng-click="dupliquer(model_email.id)"><i class="fa fa-fw fa-copy"></i></button>

                        <a class="btn btn-info btn-xs" ng-href="/ng/com_zeapps_crm/model_email/edit/@{{ model_email.id }}">
                            <i class="fas fa-fw fa-edit"></i>
                        </a>

                        <ze-btn fa="trash" color="danger" hint="{{ __t("Delete") }}" direction="left" ng-click="delete(model_email)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>