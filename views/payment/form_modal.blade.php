<div ng-controller="ComZeappsCrmPaymentFormCtrl">
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
                    <label>Montant de l'encaissement</label>
                    <input type="text" ng-model="form.total" class="form-control">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Type de paiement</label>
                    <select ng-model="form.type_payment" class="form-control">
                        <option ng-repeat="price_list in price_lists" ng-value="@{{price_list.id}}">
                            @{{ price_list.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>



        ici le tableau avec la liste des factures




    </form>
</div>