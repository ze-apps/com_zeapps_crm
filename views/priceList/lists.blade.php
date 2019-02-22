<div id="breadcrumb">Grille de prix</div>



<div id="content">
    <div class="row">
        <div class="col-md-12">
            <ze-btn fa="plus" color="success" hint="Nouvelle grille" always-on="true"
                    ze-modalform="add"
                    data-template="templatePriceList"
                    data-title="Créer une nouvelle grille"></ze-btn>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="priceLists.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Libelle</th>
                    <th>Par defaut</th>
                    <th>Type</th>
                    <th>% Remise</th>
                    <th>Actif</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="priceList in priceLists">
                    <td>@{{priceList.id}}</td>
                    <td>@{{priceList.label}}</td>
                    <td>@{{priceList.default}}</td>
                    <td>@{{priceList.type_pricelist_label}}</td>
                    <td><span ng-if="priceList.type_pricelist == 1">@{{priceList.percentage}}</span></td>
                    <td>@{{priceList.active}}</td>

                    <td class="text-right">
                        <ze-btn fa="th-large" color="success" hint="taux" direction="left" ng-click="taux(priceList.id)" ng-if="priceList.type_pricelist == 1"></ze-btn>
                        <ze-btn fa="edit" color="info" direction="left" hint="Editer"
                                ze-modalform="edit"
                                data-edit="priceList"
                                data-title="Editer"
                                data-template="templatePriceList"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(priceList)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>