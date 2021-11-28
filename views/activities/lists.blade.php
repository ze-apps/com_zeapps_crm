<div id="breadcrumb">{{ __t("Activities") }}</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="activities.length">
                <thead>
                <tr>
                    <th>{{ __t("Info") }}</th>
                    <th>{{ __t("Person responsible") }}</th>
                    <th>{{ __t("Deadline") }}</th>
                    <th>{{ __t("Label") }}</th>
                    <th>{{ __t("Description") }}</th>
                    <th>{{ __t("Status") }}</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="activity in activities track by $index">
                    <td><a href="@{{ activity.url_to_document }}"><span ng-bind-html="activity.info_source | nl2br | trusted"></span></a></td>
                    <td><a href="@{{ activity.url_to_document }}">@{{ activity.name_user }}</a></td>
                    <td><a href="@{{ activity.url_to_document }}">@{{ activity.deadline | dateConvert:'date' }}</a></td>
                    <td><a href="@{{ activity.url_to_document }}">@{{ activity.libelle }}</a></td>
                    <td><a href="@{{ activity.url_to_document }}"><span ng-bind-html="activity.description | nl2br | trusted"></span></a></td>
                    <td><a href="@{{ activity.url_to_document }}">@{{ activity.status }}</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>
</div>