<div ng-controller="ComZeappsCrmPriceListFormCtrl">
    <form name="formRequired">
        <div ng-hide="true">@{{ form.zeapps_modal_form_isvalid = formRequired.$valid }}</div>


        <!--- pour que ng-required puisse fonctionner dans une modale, il faut obligatoire mettre :
         <form name="formRequired">
         et
         <div ng-hide="true">@{{ form.zeapps_modal_form_isvalid = formRequired.$valid }}</div>
         -->




        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Libellé</label>
                    <input type="text" ng-model="form.label" name="test" class="form-control" ng-required="true">
                </div>
            </div>
        </div>




        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Par defaut</label>
                    <select ng-model="form.default" class="form-control" name="id_origin" ng-required="true" ng-change="changeParDefaut()">
                        <option ng-repeat="valOuiNon in listOuiNon" ng-value="@{{valOuiNon.id}}">
                            @{{ valOuiNon.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>





        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Type grille</label>
                    <select ng-model="form.type_pricelist" class="form-control" name="id_origin" ng-required="true">
                        <option ng-repeat="(key, price_list_type) in price_list_types" ng-value="@{{key}}">
                            @{{ price_list_type }}
                        </option>
                    </select>
                </div>
            </div>
        </div>


        <div class="row" ng-if="form.type_pricelist == 1">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Pourcentage de remise par defaut</label>
                    <input type="text" ng-model="form.percentage" name="test" class="form-control">
                </div>
            </div>
        </div>





        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Active</label>
                    <select ng-model="form.active" class="form-control" name="id_origin" ng-required="true">
                        <option ng-repeat="valOuiNon in listOuiNon" ng-value="@{{valOuiNon.id}}">
                            @{{ valOuiNon.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>




    </form>
</div>