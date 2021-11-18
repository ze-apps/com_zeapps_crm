<div id="breadcrumb">Devis</div>


<div id="content">
    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <div class="titleWell" style="font-weight: bold;">
                        {{ __t("Quote") }} : @{{ quote.libelle }}
                    </div>
                    <p class="small" style="font-weight: bold; font-size: 1.2em;">
                        {{ __t("No.") }} @{{ quote.numerotation }}
                    </p>
                    <p>
                        <span class="form-group form-inline">
                            <select class="form-control input-sm" ng-model="quote.status" ng-change="updateStatus()">
                                <option ng-repeat="stat in status" ng-value="@{{stat.id}}">@{{ stat.label }}</option>
                            </select>
                        </span>
                        (@{{ quote.probability | number:2 }}%)
                    </p>

                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                    <button type="button" class="btn btn-xs btn-info" ng-click="showDetailsEntreprise = !showDetailsEntreprise">
                        {{ __t("outstanding") }}
                    </button>
                    @endif
                </div>

                <div class="col-md-3">
                    <strong>{{ __t("Billing address") }} :</strong><br>

                    <div ng-show="quote.billing_address_full_text" ng-bind-html="quote.billing_address_full_text | nl2br"></div>
                    <div ng-show="quote.billing_address_full_text && quote.id_company"><a href="/ng/com_zeapps_contact/companies/@{{ quote.id_company }}" class="btn btn-info btn-xs">{{ __t("See the company") }}</a></div>

                    <div ng-show="quote.billing_address_full_text && quote.id_contact"><a href="/ng/com_zeapps_contact/contacts/@{{ quote.id_contact }}" class="btn btn-info btn-xs">{{ __t("See contact") }}</a></div>


                    <div ng-hide="quote.billing_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ quote.name_company }}</a><br ng-if="quote.name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ quote.name_contact }}</a><br ng-if="quote.name_contact">
                        @{{ quote.billing_address_1 }}<br ng-if="quote.billing_address_1">
                        @{{ quote.billing_address_2 }}<br ng-if="quote.billing_address_2">
                        @{{ quote.billing_address_3 }}<br ng-if="quote.billing_address_3">
                        @{{ quote.billing_zipcode + ' ' + quote.billing_city }}<br ng-if="quote.billing_state != ''">
                        @{{ quote.billing_state }}<br ng-if="quote.billing_country_name != ''">
                        @{{ quote.billing_country_name }}
                    </div>

                </div>

                <div class="col-md-3">
                    <strong>{{ __t("Delivery address") }} :</strong><br>

                    <div ng-show="quote.delivery_address_full_text" ng-bind-html="quote.delivery_address_full_text | nl2br"></div>
                    <div ng-show="quote.delivery_address_full_text && quote.id_company_delivery"><a href="/ng/com_zeapps_contact/companies/@{{ quote.id_company_delivery }}" class="btn btn-info btn-xs">{{ __t("See the company") }}</a></div>

                    <div ng-show="quote.delivery_address_full_text && quote.id_contact_delivery"><a href="/ng/com_zeapps_contact/contacts/@{{ quote.id_contact_delivery }}" class="btn btn-info btn-xs">{{ __t("See contact") }}</a></div>

                    <div ng-hide="quote.delivery_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ quote.delivery_name_company
                            }}</a><br ng-if="quote.delivery_name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ quote.delivery_name_contact
                            }}</a><br ng-if="quote.delivery_name_contact">
                        @{{ quote.delivery_address_1 }}<br ng-if="quote.delivery_address_1">
                        @{{ quote.delivery_address_2 }}<br ng-if="quote.delivery_address_2">
                        @{{ quote.delivery_address_3 }}<br ng-if="quote.delivery_address_3">
                        @{{ quote.delivery_zipcode + ' ' + quote.delivery_city }}<br ng-if="quote.delivery_state != ''">
                        @{{ quote.delivery_state }}<br ng-if="quote.delivery_country_name != ''">
                        @{{ quote.delivery_country_name }}
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <ze-btn fa="arrow-left" color="primary" hint="{{ __t("Return") }}" direction="left" ng-click="back()"></ze-btn>

                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                <ze-btn fa="edit" color="info" hint="{{ __t("Edit") }}" direction="left" ze-modalform="updateQuote" data-edit="quote" data-template="templateEdit" data-title="{{ __t("Modify the quote") }}"></ze-btn>
                                @endif



                                <ze-btn fa="download" color="primary" hint="PDF" direction="left" ng-click="print()"></ze-btn>


                                @if (in_array("com_zeapps_crm_sendemail", $zeapps_right_current_user))
                                <ze-btn fa="envelope" color="primary" hint="{{ __t("Send by e-mail") }}" direction="left" ng-click="sendByMail()"></ze-btn>
                                @endif

                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                <ze-btn fa="copy" color="success" hint="{{ __t("Duplicate") }}" direction="left" ng-click="transform()"></ze-btn>
                                @endif

                                <div class="btn-group btn-group-xs" role="group" ng-if="nb_quotes > 0">
                                    <button type="button" class="btn btn-default" ng-class="quote_first == 0 ? 'disabled' :''" ng-click="first_quote()"><span class="fa fa-fw fa-fast-backward"></span></button>
                                    <button type="button" class="btn btn-default" ng-class="quote_previous == 0 ? 'disabled' :''" ng-click="previous_quote()"><span class="fa fa-fw fa-chevron-left"></span></button>
                                    <button type="button" class="btn btn-default disabled">@{{quote_order}}/@{{nb_quotes}}
                                    </button>
                                    <button type="button" class="btn btn-default" ng-class="quote_next == 0 ? 'disabled' :''" ng-click="next_quote()"><span class="fa fa-fw fa-chevron-right"></span></button>
                                    <button type="button" class="btn btn-default" ng-class="quote_last == 0 ? 'disabled' :''" ng-click="last_quote()"><span class="fa fa-fw fa-fast-forward"></span></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right" style="margin-top: 50px;">{{ __t("Weight") }} : @{{ quote.weight | weight }}</div>
                        </div>


                        <div class="col-md-12">
                            <div ng-include="hook.template" ng-repeat="hook in hooksComZeappsCRM_QuoteHeaderRightHook" style="display: inline-block" class="pull-right">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="well" ng-if="showDetailsEntreprise">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-responsive table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __t("Company") }}</th>
                                <th>{{ __t("person") }}</th>
                                <th class="text-right">{{ __t("Total to pay") }}</th>
                                <th class="text-right">{{ __t("Paid") }}</th>
                                <th class="text-right">{{ __t("Left to pay") }}</th>
                                <th class="text-right">{{ __t("Deadline") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="credit in credits">
                                <td><a href="/ng/com_zeapps_crm/invoice/@{{credit.id_invoice}}">@{{ credit.numerotation
                                    }}</a></td>
                                <td><a href="/ng/com_zeapps_contact/companies/@{{credit.id_company}}">@{{
                                    credit.name_company }}</a></td>
                                <td><a href="/ng/com_zeapps_contact/contacts/@{{credit.id_contact}}">@{{ credit.name_contact
                                    }}</a></td>
                                <td class="text-right">@{{ credit.total | currencyConvert }}</td>
                                <td class="text-right">@{{ credit.paid | currencyConvert }}</td>
                                <td class="text-right">@{{ credit.left_to_pay | currencyConvert }}</td>
                                <td class="text-right">@{{ credit.due_date | dateConvert:'date' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="alert alert-danger" role="alert" ng-show="(quote.id_price_list != company.id_price_list && company.id) || (!company.id && quote.id_price_list != contact.id_price_list && contact.id)">
            {{ __t("Please note: the price list applied to this document does not correspond to the customer's price list.") }}
        </div>

        <ul role="tablist" class="nav nav-tabs">
            <li ng-class="navigationState =='body' ? 'active' : ''"><a href="#" ng-click="setTab('body')">{{ __t("Body") }}</a></li>
            <li ng-class="navigationState =='header' ? 'active' : ''"><a href="#" ng-click="setTab('header')">{{ __t("Heading") }}</a>
            </li>
            <li ng-class="navigationState =='condition' ? 'active' : ''"><a href="#" ng-click="setTab('condition')">{{ __t("Terms") }}</a>
            </li>
            <li ng-class="navigationState =='activity' ? 'active' : ''"><a href="#" ng-click="setTab('activity')">{{ __t("Activity") }}</a>
            </li>
            <li ng-class="navigationState =='document' ? 'active' : ''"><a href="#" ng-click="setTab('document')">{{ __t("Documents") }}</a>
            </li>
            <li ng-class="navigationState =='email' ? 'active' : ''"><a href="#" ng-click="setTab('email')">{{ __t("Email") }}</a></li>
        </ul>

        <div ng-show="navigationState =='body'">
            <div class="row">
                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <div class="col-md-12 text-right">
                    <span class="form-inline">
                        <label>{{ __t("Product code") }} :</label>
                        <span class="input-group" ng-keyup>
                            <input type="text" class="form-control input-sm inputCodeProduct" ng-model="codeProduct" ng-keydown="keyEventaddFromCode($event)">
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                    </span>
                    <ze-btn fa="tags" color="success" hint="{{ __t("product") }}" always-on="true" ng-click="addLine()"></ze-btn>
                    <ze-btn fa="dollar-sign" color="info" hint="{{ __t("subtotal") }}" always-on="true" ng-click="addSubTotal()"></ze-btn>
                    <ze-btn fa="comments" color="warning" hint="{{ __t("comment") }}" always-on="true" ze-modalform="addComment" data-title="{{ __t("Add a comment") }}" data-template="quoteCommentTplUrl"></ze-btn>
                </div>
                @endif

                <div class="col-md-12">
                    <table class="table table-striped table-condensed table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __t("Designation") }}</th>
                                <th class="text-right">{{ __t("Qty") }}</th>
                                <th class="text-right">{{ __t("Unit price excluding taxes") }}</th>
                                <th class="text-right">{{ __t("Tax") }}</th>
                                <th class="text-right">{{ __t("Discount") }}</th>
                                <th class="text-right">{{ __t("Out of taxes price") }}</th>
                                <th class="text-right">{{ __t("Amount including taxes") }}</th>
                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))ui-sortable="sortable" class="sortableContainer" @endif ng-model="lines">
                            <tr ng-repeat="line in lines" ng-class="[line.type == 'subTotal' ? 'sous-total info' : '', line.type == 'comment' ? 'warning' : '']" data-id="@{{ line.id }}">

                                <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.ref }}
                                </td>

                                <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    <strong>@{{ line.designation_title }} <span ng-if="line.designation_desc">:</span></strong><br>
                                    <span class="text-wrap">@{{ line.designation_desc }}</span>
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.qty | number }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.price_unit | currency }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.taxeLabel != "*" ? (line.taxeLabel != 0 ? (line.taxeLabel | currency:'%':2) : ''): "*" }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.discount != 0 && line.discount_prohibited == 0 ? ((0-line.discount) | currency:'%':2) : ''}}
                                    <span ng-show="line.discount > line.maximum_discount_allowed" class="text-danger"><br><i class="fas fa-exclamation-triangle"></i> Remise max. autorisée : @{{ line.maximum_discount_allowed }} %</span>
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.total_ht | currencyConvert }}
                                </td>

                                <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                    @{{ line.total_ttc | currencyConvert }}
                                </td>

                                <td colspan="6" class="text-right" ng-if="line.type == 'subTotal'">
                                    Sous-Total
                                </td>

                                <td class="text-right" ng-if="line.type == 'subTotal'">
                                    @{{ subtotalHT($index) | currencyConvert }}
                                </td>
                                <td class="text-right" ng-if="line.type == 'subTotal'">
                                    @{{ subtotalTTC($index) | currencyConvert }}
                                </td>

                                <td colspan="8" class="text-wrap" ng-if="line.type == 'comment'">@{{ line.designation_desc
                                }}
                                </td>

                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                <td class="text-right">
                                    <span ng-if="line.type === 'product' || line.type === 'service' || line.type === 'pack'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}" ze-modalform="editLine" data-edit="line" data-title="{{ __t("Edit the quote line") }}" data-template="quoteLineTplUrl"></ze-btn>
                                    </span>
                                    <span ng-show="line.type === 'comment'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}" ze-modalform="editComment" data-edit="line" data-title="{{ __t("Edit a comment") }}" data-template="quoteCommentTplUrl"></ze-btn>
                                    </span>
                                    <ze-btn fa="trash" color="danger" direction="left" hint="{{ __t("Delete") }}" ng-click="deleteLine(line)" ze-confirmation ng-if="line"></ze-btn>
                                </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th>{{ __t("Tax base") }}</th>
                                <th class="text-right">{{ __t("Tax rate") }}</th>
                                <th class="text-right">{{ __t("Tax amount") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="tableTax in tableTaxes">
                                <td>@{{ tableTax.base_tax | currencyConvert }}</td>
                                <td class="text-right">@{{ tableTax.rate_tax | currency:'%':2 }}</td>
                                <td class="text-right">@{{ tableTax.amount_tax | currencyConvert }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5 col-md-offset-2 divPrice">
                    <div class="well well-sm">
                        <div ng-if="quote.total_discount_ht > 0">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ __t("Total before tax before discount") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_prediscount_ht | currencyConvert }}
                                </div>
                            </div>
                            {{-- <div class="row">--}}
                            {{-- <div class="col-md-6">--}}
                            {{-- Total TTC av remise--}}
                            {{-- </div>--}}
                            {{-- <div class="col-md-6 text-right">--}}
                            {{-- @{{ quote.total_prediscount_ttc | currencyConvert }}--}}
                            {{-- </div>--}}
                            {{-- </div>--}}
                            <hr>
                            <div class="row" ng-if="quote.global_discount > 0">
                                <div class="col-md-6">
                                    {{ __t("Global discount") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    -@{{ quote.global_discount | number:2 }}%
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ __t("Total discounts (excluding taxes)") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_discount_ht | currencyConvert }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ __t("Total discounts (all taxes included)") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_discount_ttc | currencyConvert }}
                                </div>
                            </div>

                            <hr>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                {{ __t("Total duty") }}
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ quote.total_ht | currencyConvert }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{ __t("Total taxes") }}
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ quote.total_tva | currencyConvert }}
                            </div>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                {{ __t("Total All taxes included") }}
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ quote.total_ttc | currencyConvert }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-show="navigationState=='header'">
            <strong>{{ __t("Client reference") }} :</strong>
            @{{ quote.reference_client }}
            <br />
            <strong>{{ __t("Creation date") }} :</strong>
            @{{ quote.date_creation | dateConvert:'date' }}
            <br />
            <strong>{{ __t("Validity date") }} :</strong>
            @{{ quote.date_limit | dateConvert:'date' }}
            <br />
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>{{ __t("Payment terms") }} :</strong>
            @{{ quote.label_modality }}
        </div>

        <div ng-show="navigationState=='activity'">
            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 15px;">
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="{{ __t("Activity") }}" always-on="true" data-color="success" ze-modalform="addActivity" data-template="quoteActivityTplUrl" data-title="{{ __t("Create an activity") }}"></ze-btn>
                    </div>
                </div>
            </div>
            @endif

            <div class="card_document" ng-repeat="activity in activities | orderBy:['-deadline','-id']">
                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card_document-head clearfix">
                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                <div class="pull-right">
                                    <ze-btn data-fa="edit" data-hint="{{ __t("Edit") }}" data-direction="left" data-color="info" ze-modalform="editActivity" data-edit="activity" data-template="quoteActivityTplUrl" data-title="{{ __t("Edit activity") }}"></ze-btn>
                                    <ze-btn data-fa="trash" data-hint="{{ __t("Delete") }}" data-direction="left" data-color="danger" ng-click="deleteActivity(activity)" ze-confirmation></ze-btn>
                                </div>
                                @endif
                                <i class='fas fa-clock text-dark' ng-show="activity.status=='A faire'"></i> <i class='fas fa-check-circle text-success' ng-hide="activity.status=='A faire'"></i> <strong>@{{ activity.label_type ? activity.label_type + " : " : "" }}@{{
                                    activity.libelle }}</strong><br>
                                {{ __t("Deadline") }} : @{{ activity.deadline | dateConvert:'date' }} - @{{ activity.status
                                }}
                            </div>
                            <div class="card_document-body" ng-if="activity.description">@{{ activity.description }}
                            </div>
                            <div class="card_document-footer text-muted">
                                {{ __t("Created by") }} <strong>@{{ activity.name_user }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div ng-show="navigationState=='document'">
            <div class="row">
                <div class="col-md-12">
                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="{{ __t("Document") }}" always-on="true" data-color="success" ze-modalform="addDocument" data-template="quoteDocumentTplUrl" data-title="{{ __t("Add document") }}"></ze-btn>
                    </div>
                    @endif


                    <table class="table table-responsive table-condensed" ng-show="documents.length">
                        <thead>
                            <tr>
                                <th>{{ __t("Name") }}</th>
                                <th>{{ __t("description") }}</th>
                                <th>{{ __t("Sent by") }}</th>
                                <th>{{ __t("date") }}</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="document in documents | orderBy:['-date','-id']">
                                <td>
                                    <a ng-href="@{{ document.path }}" class="text-primary" target="_blank">
                                        <strong>@{{ document.name }}</strong>
                                    </a>
                                </td>
                                <td>
                                    @{{ document.description }}
                                </td>
                                <td>
                                    @{{ document.user_name }}
                                </td>
                                <td>
                                    @{{ document.created_at | dateConvert:'datetime' }}
                                </td>
                                <td class="text-right">
                                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                    <ze-btn data-fa="edit" data-hint="{{ __t("Edit") }}" data-direction="left" data-color="info" ze-modalform="editDocument" data-edit="document" data-template="quoteDocumentTplUrl" data-title="{{ __t("Edit document") }}"></ze-btn>
                                    <ze-btn data-fa="trash" data-hint="{{ __t("Delete") }}" data-direction="left" data-color="danger" ng-click="deleteDocument(document)" ze-confirmation></ze-btn>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div ng-if="navigationState=='email'">
            <div ng-include="'/zeapps/email/list_partial'" ng-init="module = 'com_zeapps_crm'; id = 'quotes_' + quote.id"></div>
        </div>

    </form>

</div>