<div id="breadcrumb">{{ __t("Products") }}</div>
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
                <li ng-class="navigationState =='body' ? 'active' : ''"><a href="#" ng-click="setTab('body')">{{ __t("Information") }}</a></li>
                <li ng-class="navigationState =='tarif' ? 'active' : ''"><a href="#" ng-click="setTab('tarif')">{{ __t("Prices") }}</a></li>
                <li ng-class="navigationState =='article_pack' ? 'active' : ''" ng-show="form.type_product=='pack'"><a href="#" ng-click="setTab('article_pack')">{{ __t("Pack items") }}</a></li>
                <!--<li ng-class="navigationState =='achat' ? 'active' : ''"><a href="#" ng-click="setTab('achat')">{{ __t("Purchase") }}</a></li>-->
            </ul>



            <div class="well" ng-show="navigationState =='body'">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __t("Type of product") }} <span class="required">*</span></label>
                            <select ng-model="form.type_product" class="form-control"
                                    ng-required="true">
                                <option ng-repeat="type_product in type_products" value="@{{type_product.id}}">
                                    @{{ type_product.label }}
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="form-group">
                            <label>
                                {{ __t("Active") }}
                            </label>
                            <input type="checkbox" ng-model="form.active"
                                   ng-true-value="1"
                                   ng-false-value="0"
                                   ng-checked="form.active*1">
                            <br>
                            <label>
                                {{ __t("Discount prohibited") }}
                            </label>
                            <input type="checkbox" ng-model="form.discount_prohibited"
                                   ng-true-value="1"
                                   ng-false-value="0"
                                   ng-checked="form.discount_prohibited*1">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __t("Discount max. authorized") }} <span class="required">*</span></label>
                            <input type="text" ng-model="form.maximum_discount_allowed" class="form-control" ng-required="true">
                        </div>
                    </div>


                </div>


                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>{{ __t("Reference") }} <span class="required">*</span></label>
                            <input type="text" ng-model="form.ref" class="form-control" ng-required="true">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <label>{{ __t("Product Name") }} <span class="required">*</span></label>
                            <input type="text" ng-model="form.name" class="form-control" ng-required="true">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("Category") }} <span class="required">*</span></label>

                            <select ng-model="form.id_cat" ng-change="" class="form-control">
                                <option ng-repeat="tree in tree_select" ng-value="@{{tree.id}}"
                                        ng-bind-html="tree.name | trusted"/>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("Weight (in grams)") }}</label>
                            <input type="text" ng-model="form.weight" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __t("Description") }}</label>
                            <textarea type="text" ng-model="form.description" class="form-control" rows="7"></textarea>
                        </div>
                    </div>
                </div>
            </div>







            <div class="well" ng-show="navigationState =='tarif'">

                <div class="row" ng-show="price_lists.length >= 2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("Out of taxes price") }} <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" min="0" step="0.01" ng-model="form.price_ht" class="form-control"
                                       ng-change="updatePrice(-1, 'ttc')" ng-required="true"
                                       ng-disabled="form.type_product=='pack' && form.update_price_from_subline"
                                       ng-value="form.price_ht">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("Amount including taxes") }} <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="text" min="0" step="0.01" ng-model="form.price_ttc" class="form-control"
                                       ng-change="updatePrice(-1, 'ht')"
                                       ng-required="true"
                                       ng-disabled="form.type_product=='pack' && form.update_price_from_subline"
                                       ng-value="form.price_ttc">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" ng-hide="form.type_product=='pack' || price_lists.length >= 2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __t("VAT") }} <span class="required">*</span></label>
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
                            <label>{{ __t("Accounting Account") }}</label>
                            <span ze-modalsearch="loadAccountingNumber"
                                  data-onloadmodal="openModalAccountingNumber"
                                  data-onloadmodalparam="-1"
                                  data-http="accountingNumberHttp"
                                  data-model="form.accounting_number"
                                  data-fields="accountingNumberFields"
                                  data-template-new="accountingNumberTplNew"
                                  data-title="{{ __t("Choose an accounting account") }}"></span>
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
                                           ng-change="updatePriceSubLine()"> {{ __t("Automatically calculate the sale price") }}
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="form.show_subline"
                                           ng-true-value="1"
                                           ng-false-value="0"
                                           ng-checked="form.show_subline*1"> {{ __t("Show detail in document") }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>







                <div ng-show="price_lists.length >= 2" ng-repeat="price_list in price_lists " style="border-bottom: solid 1px #000">
                    <h3>{{ __t("Price") }} : @{{ price_list.label }} <span ng-if="price_list.type_pricelist == 1 && form.priceList[price_list.id].percentage_discount">({{ __t("Discount") }} @{{ form.priceList[price_list.id].percentage_discount }} %)</span></h3>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __t("Out of taxes price") }} <span class="required" ng-if="price_list.type_pricelist == 0">*</span>
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ (form.priceList[price_list.id].price_ht * (1-form.priceList[price_list.id].percentage_discount/100)) | currencyConvert }}</span>
                                </label>

                                <div class="input-group" ng-if="price_list.type_pricelist == 0">
                                    <input type="text" ng-model="form.priceList[price_list.id].price_ht" class="form-control"
                                           ng-change="updatePrice(price_list.id, 'ttc')" ng-required="true"
                                           ng-disabled="form.type_product=='pack' && form.update_price_from_subline">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __t("Amount including taxes") }} <span class="required" ng-if="price_list.type_pricelist == 0">*</span>
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ (form.priceList[price_list.id].price_ttc * (1-form.priceList[price_list.id].percentage_discount/100)) | currencyConvert }}</span>
                                </label>

                                <div class="input-group" ng-if="price_list.type_pricelist == 0">
                                    <input type="text" ng-model="form.priceList[price_list.id].price_ttc" class="form-control"
                                           ng-change="updatePrice(price_list.id, 'ht')"
                                           ng-required="true"
                                           ng-disabled="form.type_product=='pack' && form.update_price_from_subline">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row" ng-hide="form.type_product=='pack'">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __t("Tax") }} <span class="required" ng-if="price_list.type_pricelist == 0">*</span>
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
                                <label>{{ __t("Accounting Account") }}
                                    <span ng-if="price_list.type_pricelist == 1"> : @{{ form.priceList[price_list.id].accounting_number }}</span>
                                </label>
                                <span ze-modalsearch="loadAccountingNumber"
                                      data-onloadmodal="openModalAccountingNumber"
                                      data-onloadmodalparam="price_list.id"
                                      data-http="accountingNumberHttp"
                                      data-model="form.priceList[price_list.id].accounting_number"
                                      data-fields="accountingNumberFields"
                                      data-template-new="accountingNumberTplNew"
                                      data-title="{{ __t("Choose an accounting account") }}"
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
                    <span class="required">*</span> {{ __t("Required fields") }}
                </div>
            </div>




            <!-- Article composant le pack -->
            <div class="well" ng-show="form.type_product=='pack' && navigationState =='article_pack'">
                <h3>{{ __t("Pack items") }}</h3>

                <div class="row">
                    <div class="col-md-12 text-right">
                    <span class="form-inline">
                        <label>{{ __t("Product code") }} :</label>
                        <span class="input-group">
                            <input type="text" class="form-control input-sm inputCodeProduct" ng-model="codeProduct"
                                   ng-keydown="keyEventaddFromCode($event)">
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                    </span>
                        <ze-btn fa="tags" color="success" hint="{{ __t("Product") }}" always-on="true" ng-click="addLine()"></ze-btn>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __t("Designation") }}</th>
                                <th class="text-right">{{ __t("Qty") }}</th>
                                <th class="text-right">{{ __t("Unit price excluding taxes") }}</th>
                                <th class="text-right">{{ __t("Tax") }}</th>
                                <th class="text-right">{{ __t("Out of taxes price") }}</th>
                                <th class="text-right">{{ __t("Amount including taxes") }}</th>
                                <th class="text-right">{{ __t("Type") }}</th>
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
                                <td class="text-right">@{{ (line.price_ht * line.quantite) | currencyConvert }}</td>
                                <td class="text-right">@{{ (line.price_ht * line.quantite) * (1+(line.value_taxe/100)) | currencyConvert }}</td>
                                <td class="text-right">@{{ line.type_product }}</td>

                                <td class="text-right">
                                    <ze-btn fa="trash" color="danger" direction="left" hint="{{ __t("Delete") }}"
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
                        <th>{{ __t("Date") }}</th>
                        <th>{{ __t("Provider") }}</th>
                        <th class="text-right">{{ __t("Amount") }}</th>
                        <th class="text-right">{{ __t("Out of taxes price") }}</th>
                        <th class="text-right">{{ __t("Tax rate") }}</th>
                        <th class="text-right">{{ __t("Amount including taxes") }}</th>
                        <th class="text-right">{{ __t("Actions") }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="leaf" ng-repeat="supplierPurchase in supplierPurchases">
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)">@{{ supplierPurchase.date_purchase }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)">@{{ supplierPurchase.supplier }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.quantity | currency:'':0 }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.price_ht | currencyConvert }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.value_taxe | currency:'%':2 }}</td>
                        <td ng-click="goToSupplierPurchase(supplierPurchase.id)" class="text-right">@{{ supplierPurchase.price_ttc | currencyConvert }}</td>
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