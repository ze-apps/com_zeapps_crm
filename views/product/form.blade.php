<div id="breadcrumb">Produits</div>
<div id="content">
    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"
                </zeapps-happylittletree>
            </div>
        </div>

        <form name="newProduct" class="col-md-9">


            <ul role="tablist" class="nav nav-tabs">
                <li ng-class="navigationState =='body' ? 'active' : ''"><a href="#" ng-click="setTab('body')">Information</a></li>
                <li ng-class="navigationState =='tarif' ? 'active' : ''"><a href="#" ng-click="setTab('tarif')">Tarif(s)</a></li>
                <li ng-class="navigationState =='article_pack' ? 'active' : ''" ng-show="form.type_product=='pack'"><a href="#" ng-click="setTab('article_pack')">Articles du pack</a></li>
                <li ng-class="navigationState =='achat' ? 'active' : ''"><a href="#" ng-click="setTab('achat')">Achat</a></li>
            </ul>



            <div class="well" ng-show="navigationState =='body'">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type de produit <span class="required">*</span></label>
                            <select ng-model="form.type_product" class="form-control"
                                    ng-required="true">
                                <option ng-repeat="type_product in type_products" value="@{{type_product.id}}">
                                    @{{ type_product.label }}
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                 Actif
                            </label>
                            <input type="checkbox" ng-model="form.active"
                                   ng-true-value="1"
                                   ng-false-value="0"
                                   ng-checked="form.active*1">
                            <br>
                            <label>
                                Remise interdite
                            </label>
                            <input type="checkbox" ng-model="form.discount_prohibited"
                                   ng-true-value="1"
                                   ng-false-value="0"
                                   ng-checked="form.discount_prohibited*1">


                        </div>
                    </div>


                </div>


                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Reference</label>
                            <input type="text" ng-model="form.ref" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label>Nom du produit <span class="required">*</span></label>
                            <input type="text" ng-model="form.name" class="form-control" ng-required="true">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Catégorie <span class="required">*</span></label>

                            <select ng-model="form.id_cat" ng-change="" class="form-control">
                                <option ng-repeat="tree in tree_select" ng-value="@{{tree.id}}"
                                        ng-bind-html="tree.name | trusted"/>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea type="text" ng-model="form.description" class="form-control" rows="7"></textarea>
                        </div>
                    </div>
                </div>
            </div>







            <div class="well" ng-show="navigationState =='tarif'">

                <div class="row" ng-show="price_lists.length >= 2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prix HT <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" min="0" step="0.01" ng-model="form.price_ht" class="form-control"
                                       ng-change="updatePrice(-1, 'ttc')" ng-required="true"
                                       ng-disabled="form.type_product=='pack' && form.update_price_from_subline"
                                       ng-value="form.price_ht*1">
                                <span class="input-group-addon">€</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Prix TTC <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" min="0" step="0.01" ng-model="form.price_ttc" class="form-control"
                                       ng-change="updatePrice(-1, 'ht')"
                                       ng-required="true"
                                       ng-disabled="form.type_product=='pack' && form.update_price_from_subline"
                                       ng-value="form.price_ttc*1">
                                <span class="input-group-addon">€</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" ng-hide="form.type_product=='pack' || price_lists.length >= 2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>TVA <span class="required">*</span></label>
                            <select ng-model="form.id_taxe" ng-change="updateTaxe(-1);updatePrice(-1, 'ttc')"
                                    class="form-control" ng-required="form.type_product!='pack'">
                                <option ng-repeat="taxe in taxes | filter:{ active : 1 }" ng-value="@{{taxe.id}}">
                                    @{{ taxe.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Compte Comptable</label>
                            <span ze-modalsearch="loadAccountingNumber"
                                  data-onloadmodal="openModalAccountingNumber"
                                  data-onloadmodalparam="-1"
                                  data-http="accountingNumberHttp"
                                  data-model="form.accounting_number"
                                  data-fields="accountingNumberFields"
                                  data-template-new="accountingNumberTplNew"
                                  data-title="Choisir un compte comptable"></span>
                        </div>
                    </div>
                </div>


                <div class="row" ng-show="form.type_product=='pack'">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           ng-model="form.update_price_from_subline"
                                           ng-true-value="1"
                                           ng-false-value="0"
                                           ng-checked="form.update_price_from_subline*1"
                                           ng-change="updatePriceSubLine()"> Calculer automatiquement le prix de vente
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="form.show_subline"
                                           ng-true-value="1"
                                           ng-false-value="0"
                                           ng-checked="form.show_subline*1"> Afficher le detail dans le document
                                </label>
                            </div>
                        </div>
                    </div>
                </div>







                <div ng-show="price_lists.length >= 2" ng-repeat="price_list in price_lists " style="border-bottom: solid 1px #000">
                    <h3>Tarif : @{{ price_list.label }} <span ng-if="price_list.type_pricelist == 1 && form.priceList[price_list.id].percentage_discount">(Remise @{{ form.priceList[price_list.id].percentage_discount }} %)</span></h3>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix HT <span class="required" ng-if="price_list.type_pricelist == 0">*</span>
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ (form.priceList[price_list.id].price_ht * (1-form.priceList[price_list.id].percentage_discount/100)).toFixed(2) }} €</span>
                                </label>

                                <div class="input-group" ng-if="price_list.type_pricelist == 0">
                                    <input type="text" ng-model="form.priceList[price_list.id].price_ht" class="form-control"
                                           ng-change="updatePrice(price_list.id, 'ttc')" ng-required="true"
                                           ng-disabled="form.type_product=='pack' && form.update_price_from_subline">
                                    <span class="input-group-addon">€</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Prix TTC <span class="required" ng-if="price_list.type_pricelist == 0">*</span>
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ (form.priceList[price_list.id].price_ttc * (1-form.priceList[price_list.id].percentage_discount/100)).toFixed(2) }} €</span>
                                </label>

                                <div class="input-group" ng-if="price_list.type_pricelist == 0">
                                    <input type="text" ng-model="form.priceList[price_list.id].price_ttc" class="form-control"
                                           ng-change="updatePrice(price_list.id, 'ht')"
                                           ng-required="true"
                                           ng-disabled="form.type_product=='pack' && form.update_price_from_subline">
                                    <span class="input-group-addon">€</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row" ng-hide="form.type_product=='pack'">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>TVA <span class="required" ng-if="price_list.type_pricelist == 0">*</span>
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ form.priceList[price_list.id].value_taxe }} %</span>
                                </label>

                                <select ng-model="form.priceList[price_list.id].id_taxe" ng-change="updateTaxe(price_list.id);updatePrice(price_list.id, 'ttc')"
                                        class="form-control" ng-required="form.type_product!='pack'"
                                        ng-if="price_list.type_pricelist == 0">
                                    <option ng-repeat="taxe in taxes | filter:{ active : 1 }" ng-value="@{{taxe.id}}">
                                        @{{ taxe.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Compte Comptable
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ form.priceList[price_list.id].accounting_number }}</span>
                                </label>
                                <span ze-modalsearch="loadAccountingNumber"
                                      data-onloadmodal="openModalAccountingNumber"
                                      data-onloadmodalparam="price_list.id"
                                      data-http="accountingNumberHttp"
                                      data-model="form.priceList[price_list.id].accounting_number"
                                      data-fields="accountingNumberFields"
                                      data-template-new="accountingNumberTplNew"
                                      data-title="Choisir un compte comptable"
                                      ng-if="price_list.type_pricelist == 0"></span>
                            </div>
                        </div>
                    </div>

                </div>




            </div>







            <div class="well" ng-show="attributes.length">
                <div class="row">

                    <div class="form-group" ng-repeat="attribute in attributes">
                        <label>@{{ attribute.name }} <span class="required" ng-if="attribute.required">*</span></label>
                        <input type="@{{ attribute.type }}" ng-model="form.extra[attribute.name]"
                               ng-class="attribute.type != 'checkbox' ? 'form-control' : ''"
                               ng-required="attribute.required" ng-if="attribute.type != 'textarea'">
                        <textarea ng-model="form.extra[attribute.name]" class="form-control" rows="3"
                                  ng-required="attribute.required" ng-if="attribute.type == 'textarea'"></textarea>
                    </div>

                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <span class="required">*</span> champs obligatoires
                </div>
            </div>




            <!-- Article composant le pack -->
            <div class="well" ng-show="form.type_product=='pack' && navigationState =='article_pack'">
                <h3>Articles composants le pack</h3>

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
                                <th class="text-right">Montant HT</th>
                                <th class="text-right">Montant TTC</th>
                                <th class="text-right">Type</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody ui-sortable="sortable" class="sortableContainer">
                            <tr ng-repeat="line in form.sublines" data-serialId="@{{ line.serialId }}">
                                <td>@{{ line.ref }}</td>
                                <td>
                                    <strong>@{{ line.name }} <span ng-if="line.description">:</span></strong><br>
                                    <span class="text-wrap">@{{ line.description }}</span>
                                </td>
                                <td class="text-right"><input type="text" ng-model="line.quantite" ng-value="line.quantite | number" ng-change="updatePrice()"></td>
                                <td class="text-right">@{{ line.price_ht | currency }}</td>
                                <td class="text-right">@{{ line.id_taxe != 0 ? (line.value_taxe | currency:'%':2) : ''
                                    }}
                                </td>
                                <td class="text-right">@{{ (line.price_ht * line.quantite) | currency:'€':2 }}</td>
                                <td class="text-right">@{{ (line.price_ht * line.quantite) * (1+(line.value_taxe/100)) | currency:'€':2 }}</td>
                                <td class="text-right">@{{ line.type_product }}</td>

                                <td class="text-right">
                                    <ze-btn fa="trash" color="danger" direction="left" hint="Supprimer"
                                            ng-click="deleteLine(line)" ze-confirmation ng-if="line"></ze-btn>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>






            <div ng-show="navigationState =='achat'">

                <button type="button" class="btn btn-xs btn-success" ng-click="addSupplierPurchase()">
                    <i class="fa fa-plus fa-fw"></i>
                </button>

                <table class="table table-hover table-condensed table-responsive" ng-show="supplierPurchases.length > 0">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fournisseur</th>
                        <th class="text-right">Quantité</th>
                        <th class="text-right">Prix HT</th>
                        <th class="text-right">TVA (%)</th>
                        <th class="text-right">Prix TTC</th>
                        <th class="text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="leaf" ng-repeat="supplierPurchase in supplierPurchases">
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)">@{{ supplierPurchase.date_purchase }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)">@{{ supplierPurchase.supplier }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.quantity | currency:'':0 }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.price_ht | currency:'€':2 }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.value_taxe | currency:'%':2 }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.price_ttc | currency:'€':2 }}</td>
                        <td class="text-right">
                            <button type="button" class="btn btn-xs btn-danger" ng-click="deleteSupplierPurchase(supplierPurchase)">
                                <i class="fa fa-trash fa-fw"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>



            <form-buttons></form-buttons>
        </form>
    </div>

</div>