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
                    <label>{{ __t("Company") }} <span class="required">**</span></label>
                    <span ze-modalsearch="loadCompany"
                          data-http="companyHttp"
                          data-model="form.name_company"
                          data-fields="companyFields"
                          data-template-new="companyTplNew"
                          data-title="{{ __t("Choose a company") }}"></span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>{{ __t("Person") }} <span class="required">**</span></label>
                    <span ze-modalsearch="loadContact"
                          data-http="contactHttp"
                          data-model="form.name_contact"
                          data-fields="contactFields"
                          data-template-new="contactTplNew"
                          data-title="{{ __t("Choose a person") }}"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Payment amount") }}</label>
                    <input type="text" ng-model="form.total" class="form-control" ng-required="true" ng-blur="updateTotal()">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Terms of Payment") }}</label>
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
                    <label>{{ __t("Cheque number") }}</label>
                    <input type="text" ng-model="form.bank_check_number" class="form-control" ng-required="type_payment_is_check">
                </div>
            </div>
        </div>

        <div class="row" ng-show="type_payment_is_check">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Payment amount") }}</label>
                    <input type="text" ng-model="form.check_issuer" class="form-control" ng-required="type_payment_is_check">
                </div>
            </div>
        </div>


        <div class="row" ng-if="form.invoices.length">
            <div class="col-md-12">
                <table class="table table-hover table-condensed table-responsive">
                    <thead>
                    <tr>
                        <th>{{ __t("Date of invoice") }}</th>
                        <th>{{ __t("Invoice No.") }}</th>
                        <th>{{ __t("Object") }}</th>
                        <th class="text-right">{{ __t("Amount Invoice") }}</th>
                        <th class="text-right">{{ __t("Remaining balance") }}</th>
                        <th class="text-right">{{ __t("Amount received") }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr ng-repeat="invoice in form.invoices">
                        <td>@{{ invoice.date_creation | dateConvert:'date' }}</td>
                        <td>@{{ invoice.numerotation }}</td>
                        <td>@{{ invoice.libelle }}</td>
                        <td class="text-right">@{{ invoice.total_ttc | currencyConvert }}</td>
                        <td class="text-right">@{{ invoice.due | currencyConvert }}</td>
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
                {{ __t("Total invoice balance") }}
                 @{{ total_invoice_due | currencyConvert }}

                <span ng-show="ecart != 0" class="text-danger"> {{ __t("Difference between the total") }} : @{{ ecart | currencyConvert }}, {{ __t("please change the amounts on the invoices") }}</span>
            </div>
        </div>

    </form>
</div>