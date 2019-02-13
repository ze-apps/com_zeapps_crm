<div ng-controller="ComZeappsCrmPaymentFormCtrl">
    <form name="formRequired">
        <div ng-hide="true">@{{ form.zeapps_modal_form_isvalid = formRequired.$valid }}</div>

        <!--- pour que ng-required puisse fonctionner dans une modale, il faut obligatoire mettre :
         <form name="formRequired">
         et
         <div ng-hide="true">@{{ form.zeapps_modal_form_isvalid = formRequired.$valid }}</div>
         -->


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
            <div class="col-md-12">
                <div class="form-group">
                    <label>Montant de l'encaissement</label>
                    <input type="text" ng-model="form.total" class="form-control" ng-required="true" ng-blur="updateTotal()">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Modalités de règlement</label>
                    <select ng-model="form.type_payment" class="form-control" ng-required="true" ng-change="updateModality()">
                        <option ng-repeat="modality in modalities" value="@{{modality.id}}" ng-selected="form.type_payment == modality.id" ng-if="modality.situation != 0">
                            @{{ modality.label }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row" ng-show="type_payment_is_check">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Numéro de chèque</label>
                    <input type="text" ng-model="form.bank_check_number" class="form-control" ng-required="type_payment_is_check">
                </div>
            </div>
        </div>

        <div class="row" ng-show="type_payment_is_check">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Montant de l'encaissement</label>
                    <input type="text" ng-model="form.check_issuer" class="form-control" ng-required="type_payment_is_check">
                </div>
            </div>
        </div>


        <div class="row" ng-if="form.invoices.length">
            <div class="col-md-12">
                <table class="table table-hover table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th>Date Facture</th>
                        <th>N° Facture</th>
                        <th>Objet</th>
                        <th class="text-right">Montant Facture</th>
                        <th class="text-right">Solde restant</th>
                        <th class="text-right">Montant Encaissé</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="invoice in form.invoices">
                        <td>@{{ invoice.date_creation || "-"  | date:'dd/MM/yyyy' }}</td>
                        <td>@{{ invoice.numerotation }}</td>
                        <td>@{{ invoice.libelle }}</td>
                        <td class="text-right">@{{ invoice.total_ttc | currency:'€':2 }}</td>
                        <td class="text-right">@{{ invoice.due | currency:'€':2 }}</td>
                        <td class="text-right">
                            <input type="text" ng-model="invoice.amount_payment" class="form-control text-right" ng-blur="updateEcart()">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                Solde total des factures @{{ total_invoice_due | currency:'€':2 }}

                <span ng-show="ecart != 0" class="text-danger"> Ecart entre le total : @{{ ecart | currency:'€':2 }}, veuillez modifier les montants sur les factures</span>
            </div>
        </div>

    </form>
</div>