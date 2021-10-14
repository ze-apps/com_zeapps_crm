<div id="breadcrumb">{{ __t("Price list") }} : @{{ grille_tarif.label }}</div>


<div id="content">

    <div class="row">
        <div class="col-md-12">


            <table class="table table-hover table-condensed table-responsive" ng-show="categories.length">
                <thead>
                <tr>
                    <th>{{ __t("Label") }}</th>
                    <th>{{ __t("% discount") }}% Remise</th>
                    <th>{{ __t("Accounting Account") }}</th>
                    <th>{{ __t("Taxe rate") }}</th>
                    <th> </th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="categorie in categories">
                    <td>@{{ espace(categorie.niveau) }}@{{  categorie.name }}</td>
                    <td><input type="text" ng-model="categorie.taux_remise" ng-if="categorie.id > 0" /></td>
                    <td><input type="text" ng-model="categorie.compte_compta" ng-if="categorie.id > 0" /></td>

                    <td><select ng-model="categorie.id_taxe" class="form-control" ng-if="categorie.id > 0">
                            <option ng-repeat="taxe in taxes | filter:{ active : 1 }" ng-value="@{{taxe.id}}">
                                @{{ taxe.label }}
                            </option>
                        </select></td>
                    <td><ze-btn fa="save" color="success" hint="{{ __t("Save") }}" direction="left" ng-click="save(categorie)" ng-if="categorie.id > 0"></ze-btn></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>