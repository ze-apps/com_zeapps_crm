<div ng-controller="ComZeappsCrmInvoiceFormCtrl">
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
                    <label>Grille de tarif</label>
                    <select ng-model="form.id_price_list" class="form-control">
                        <option ng-repeat="price_list in price_lists" ng-value="@{{price_list.id}}">
                            @{{ price_list.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Libellé de la facture</label>
                    <input type="text" ng-model="form.libelle" name="test" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Canal</label>
                    <select ng-model="form.id_origin" class="form-control" name="id_origin">
                        <option ng-repeat="crm_origin in crm_origins" ng-value="@{{crm_origin.id}}">
                            @{{ crm_origin.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Gestionnaire de la facture <span class="required">*</span></label>
                    <span ze-modalsearch="loadAccountManager"
                          data-http="accountManagerHttp"
                          data-model="form.name_user_account_manager"
                          data-fields="accountManagerFields"
                          data-title="Choisir une entreprise"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Statut</label>
                    <select class="form-control" ng-model="form.status">
                        <option ng-repeat="stat in status" ng-value="@{{stat.id}}">
                            @{{ stat.label }}
                        </option>
                    </select>
                </div>
            </div>


        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Société <span class="required">**</span></label>
                    <span ze-modalsearch="loadCompany"
                          data-http="companyHttp"
                          data-model="form.name_company"
                          data-fields="companyFields"
                          data-template-new="companyTplNew"
                          data-title="Choisir une entreprise"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Contact <span class="required">**</span></label>
                    <span ze-modalsearch="loadContact"
                          data-http="contactHttp"
                          data-model="form.name_contact"
                          data-fields="contactFields"
                          data-template-new="contactTplNew"
                          data-title="Choisir un contact"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Compte comptable</label>
                    <span ze-modalsearch="loadAccountingNumber"
                          data-http="accountingNumberHttp"
                          data-model="form.accounting_number"
                          data-fields="accountingNumberFields"
                          data-template-new="accountingNumberTplNew"
                          data-title="Choisir un compte comptable"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Remise globale</label>
                    <input type="number" min="0" ng-model="form.global_discount" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Date de création <span class="required">*</span></label>
                    <input type="date" ng-model="form.date_creation" name="date_creation" ng-change="updateDateLimit()" class="form-control"
                           ng-required="true">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Date de validité <span class="required">*</span></label>
                    <input type="date" ng-model="form.date_limit" name="date_limit" class="form-control" ng-required="true">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Modalités de règlement</label>
                    <select ng-model="form.id_modality" class="form-control" ng-change="updateModality()">
                        <option ng-repeat="modality in modalities" value="@{{modality.id}}" ng-selected="form.id_modality == modality.id">
                            @{{ modality.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Référence client</label>
                    <input type="text" ng-model="form.reference_client" class="form-control">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Entrepôts <span class="required">*</span></label>
                    <select ng-model="form.id_warehouse" name="id_warehouse" class="form-control" ng-required="true">
                        <option ng-repeat="warehouse in warehouses" ng-value="@{{warehouse.id}}">
                            @{{ warehouse.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <span class="required">** au moins un des deux champs est requis</span>
            </div>
        </div>
    </form>
</div>