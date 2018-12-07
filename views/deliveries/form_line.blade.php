<div ng-controller="ComZeappsCrmDeliveryFormLineCtrl">

    <ul role="tablist" class="nav nav-tabs">
        <li ng-class="navigationState =='body' ? 'active' : ''"><a href="#" ng-click="setTab('body')">Détails</a></li>
        <li ng-class="navigationState =='articles' ? 'active' : ''"><a href="#" ng-click="setTab('articles')">Article
                composé</a>
        </li>
    </ul>

    <div ng-show="navigationState =='body'">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Référence</label>
                    <input type="text" class="form-control" ng-model="form.ref">
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label>Désignation</label>
                    <input type="text" class="form-control" ng-model="form.designation_title">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" ng-model="form.designation_desc" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="number" class="form-control" ng-model="form.qty" ng-keyup="updatePriceSubLineKeyUp()">
                </div>
            </div>

            <div class="col-md-6" ng-if="form.sublines.length != 0">
                <div class="form-group">
                    <label class="text-danger">Prix Unit. TTC (tarif cumulé des articles composés)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" ng-model="form.price_unit_ttc_subline" ng-keyup="updatePriceSubLineKeyUp()">
                        <div class="input-group-addon">€</div>
                    </div>
                </div>
                <div>
                    Montant HT : <span>@{{ form.price_unit_ht_indicated }}</span> €
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" ng-model="form.update_price_from_subline" ng-true-value="1" ng-false-value="0" ng-checked="form.update_price_from_subline*1" ng-click="updatePriceSubLine()"> Calculer automatiquement le prix de vente
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" ng-model="form.show_subline"> Afficher le detail dans le document
                    </label>
                </div>
            </div>


            <div class="col-md-3" ng-if="form.sublines.length == 0">
                <div class="form-group">
                    <label>Prix Unit. HT</label>
                    <div class="input-group">
                        <input type="number" class="form-control" ng-model="form.price_unit">
                        <div class="input-group-addon">€</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" ng-if="form.sublines.length == 0">
                <div class="form-group">
                    <label>Taxe</label>
                    <select ng-model="form.id_taxe" class="form-control" ng-change="updateTaxe()">
                        <option ng-repeat="taxe in taxes | filter:{ active : 1 }" ng-value="@{{taxe.id}}">
                            @{{ taxe.label }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Remise</label>
                    <div class="input-group">
                        <input type="number" class="form-control" ng-model="form.discount" ng-keyup="updatePriceSubLineKeyUp()">
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" ng-if="form.sublines.length == 0">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Compte comptable</label>
                    <span   ze-modalsearch="loadAccountingNumber"
                            data-http="accountingNumberHttp"
                            data-model="form.accounting_number"
                            data-fields="accountingNumberFields"
                            data-template-new="accountingNumberTplNew"
                            data-title="Choisir un compte comptable"></span>
                </div>
            </div>
        </div>
    </div>

    <div ng-show="navigationState =='articles'">
        <div class="row">
            <div class="col-md-12 text-right">
                    <span class="form-inline">
                        <label>Code produit :</label>
                        <span class="input-group">
                            <input type="text" class="form-control input-sm inputCodeProduct" ng-model="codeProduct"
                                   ng-keydown="keyEventaddFromCode($event)">
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                    </span>
                <ze-btn fa="tags" color="success" hint="produit" always-on="true" ng-click="addLine()"></ze-btn>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Désignation</th>
                        <th class="text-right">Qte</th>
                        <th class="text-right">P. Unit. HT</th>
                        <th class="text-right">Taxe</th>
                        <th class="text-right">Remise</th>
                        <th class="text-right">Montant HT</th>
                        <th class="text-right">Montant TTC</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody ui-sortable="sortable" class="sortableContainer">
                    <tr ng-repeat="line in form.sublines" data-serialId="@{{ line.serialId }}">
                        <td>@{{ line.ref }}</td>
                        <td>
                            <strong>@{{ line.designation_title }} <span ng-if="line.designation_desc">:</span></strong><br>
                            <span class="text-wrap">@{{ line.designation_desc }}</span>
                        </td>
                        <td class="text-right">@{{ line.qty | number }}</td>
                        <td class="text-right">@{{ line.price_unit | currency }}</td>
                        <td class="text-right">@{{ line.id_taxe != 0 ? (line.value_taxe | currency:'%':2) : '' }}</td>
                        <td class="text-right">@{{ line.discount != 0 ? ((0-line.discount) | currency:'%':2) : ''}}</td>
                        <td class="text-right">@{{ line.total_ht | currency:'€':2 }}</td>
                        <td class="text-right">@{{ line.total_ttc | currency:'€':2 }}</td>

                        <td class="text-right">
                            <span>
                                <ze-btn fa="pencil" color="info" direction="left" hint="editer"
                                        ze-modalform="editLine"
                                        data-edit="line"
                                        data-title="Editer la ligne"
                                        data-template="deliveryLineTplUrl"></ze-btn>
                            </span>
                            <ze-btn fa="trash" color="danger" direction="left" hint="Supprimer"
                                    ng-click="deleteLine(line)" ze-confirmation ng-if="line"></ze-btn>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>