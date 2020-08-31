<div class="row">
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-xs" ng-click="changeScaleTo('month')" ng-class="selectedScale === 'month' ? 'btn-success' : 'btn-info';">
            {{ __t("Year") }}
        </button>
        <button type="button" class="btn btn-xs btn-info" ng-click="changeScaleTo('dates')" ng-class="selectedScale === 'dates' ? 'btn-success' : 'btn-info';">
            {{ __t("3 months") }}
        </button>
        <button type="button" class="btn btn-xs btn-info" ng-click="changeScaleTo('date')" ng-class="selectedScale === 'date' ? 'btn-success' : 'btn-info';">
            {{ __t("1 month") }}
        </button>
        <button type="button" class="btn btn-xs btn-info" ng-click="changeScaleTo('days')" ng-class="selectedScale === 'days' ? 'btn-success' : 'btn-info';">
            {{ __t("Week") }}
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
        <canvas id="base" chart-line
                class="chart-line"
                chart-data="data"
                chart-labels="labels">
        </canvas>
    </div>
</div>