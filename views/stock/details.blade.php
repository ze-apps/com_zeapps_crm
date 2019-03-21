<div id="breadcrumb">Stocks</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters"
                        data-update="getStocks"></ze-filters>
            <h3>
                @{{ product_stock.ref ? product_stock.ref + ' - ' : '' }}@{{product_stock.name }}
            </h3>
        </div>
    </div>

    <ze-postits postits="postits"></ze-postits>

    <ul class="nav nav-tabs">
        <li ng-class="navigationState === 'chart' ? 'active' : ''">
            <a href="#" ng-click="navigationState = 'chart'">Graphique</a>
        </li>
        <li ng-class="navigationState === 'history' ? 'active' : ''">
            <a href="#" ng-click="navigationState = 'history'">Historique</a>
        </li>
    </ul>

    <div ng-include="'/com_zeapps_crm/stock/' + navigationState"></div>
</div>