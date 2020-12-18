<div id="breadcrumb">Bons de livraison</div>
<div id="content">


    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <div class="titleWell" style="font-weight: bold;">
                        {{ __t("Delivery form") }} : @{{ delivery.libelle }}
                    </div>
                    <p class="small" style="font-weight: bold; font-size: 1.2em;">
                        {{ __t("No.") }} @{{ delivery.numerotation }}
                    </p>

                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                        <button type="button" class="btn btn-xs btn-info"
                                ng-click="showDetailsEntreprise = !showDetailsEntreprise">
                            {{ __t("outstanding") }}
                        </button>
                    @endif
                </div>

                <div class="col-md-3">
                    <strong>{{ __t("Billing address") }} :</strong><br>

                    <div ng-show="delivery.billing_address_full_text"
                         ng-bind-html="delivery.billing_address_full_text | nl2br"></div>
                    <div ng-show="delivery.billing_address_full_text && delivery.id_company"><a
                                href="/ng/com_zeapps_contact/companies/@{{ delivery.id_company }}"
                                class="btn btn-info btn-xs">{{ __t("See the company") }}</a></div>

                    <div ng-show="delivery.billing_address_full_text && delivery.id_contact"><a
                                href="/ng/com_zeapps_contact/contacts/@{{ delivery.id_contact }}"
                                class="btn btn-info btn-xs">{{ __t("See contact") }}</a></div>


                    <div ng-hide="delivery.billing_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ delivery.name_company }}</a><br
                                ng-if="delivery.name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ delivery.name_contact }}</a><br
                                ng-if="delivery.name_contact">
                        @{{ delivery.billing_address_1 }}<br ng-if="delivery.billing_address_1">
                        @{{ delivery.billing_address_2 }}<br ng-if="delivery.billing_address_2">
                        @{{ delivery.billing_address_3 }}<br ng-if="delivery.billing_address_3">
                        @{{ delivery.billing_zipcode + ' ' + delivery.billing_city }}<br
                                ng-if="delivery.billing_state != ''">
                        @{{ delivery.billing_state }}<br ng-if="delivery.billing_country_name != ''">
                        @{{ delivery.billing_country_name }}
                    </div>

                </div>

                <div class="col-md-3">
                    <strong>{{ __t("Delivery address") }} :</strong><br>

                    <div ng-show="delivery.delivery_address_full_text"
                         ng-bind-html="delivery.delivery_address_full_text | nl2br"></div>
                    <div ng-show="delivery.delivery_address_full_text && delivery.id_company_delivery"><a
                                href="/ng/com_zeapps_contact/companies/@{{ delivery.id_company_delivery }}"
                                class="btn btn-info btn-xs">{{ __t("See the company") }}</a></div>

                    <div ng-show="delivery.delivery_address_full_text && delivery.id_contact_delivery"><a
                                href="/ng/com_zeapps_contact/contacts/@{{ delivery.id_contact_delivery }}"
                                class="btn btn-info btn-xs">{{ __t("See contact") }}</a></div>

                    <div ng-hide="delivery.delivery_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ delivery.delivery_name_company
                            }}</a><br ng-if="delivery.delivery_name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ delivery.delivery_name_contact
                            }}</a><br
                                ng-if="delivery.delivery_name_contact">
                        @{{ delivery.delivery_address_1 }}<br ng-if="delivery.delivery_address_1">
                        @{{ delivery.delivery_address_2 }}<br ng-if="delivery.delivery_address_2">
                        @{{ delivery.delivery_address_3 }}<br ng-if="delivery.delivery_address_3">
                        @{{ delivery.delivery_zipcode + ' ' + delivery.delivery_city }}<br
                                ng-if="delivery.delivery_state != ''">
                        @{{ delivery.delivery_state }}<br ng-if="delivery.delivery_country_name != ''">
                        @{{ delivery.delivery_country_name }}
                    </div>

                </div>

                <div class="col-md-4" style="text-align: right">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <ze-btn fa="arrow-left" color="primary" hint="{{ __t("Return") }}" direction="left"
                                        ng-click="back()"></ze-btn>

                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                    <ze-btn fa="edit" color="info" hint="{{ __t("Edit") }}" direction="left"
                                            ze-modalform="updateDelivery"
                                            data-edit="delivery"
                                            data-template="templateEdit"
                                            data-title="{{ __t("Modify the delivery note") }}"></ze-btn>
                                @endif

                                <ze-btn fa="download" color="primary" hint="PDF" direction="left"
                                        ng-click="print()"></ze-btn>

                                @if (in_array("com_zeapps_crm_sendemail", $zeapps_right_current_user))
                                    <ze-btn fa="envelope" color="primary" hint="{{ __t("Send by e-mail") }}" direction="left"
                                            ng-click="sendByMail()"></ze-btn>
                                @endif

                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                    <ze-btn fa="copy" color="success" hint="{{ __t("Duplicate") }}" direction="left"
                                            ng-click="transform()"></ze-btn>
                                    <ze-btn fa="lock" color="danger" hint="{{ __t("Close") }}" direction="left"
                                            ng-click="finalize()"
                                            ng-hide="delivery.finalized"></ze-btn>
                                @endif


                                <div class="btn-group btn-group-xs" role="group" ng-if="nb_deliveries > 0">
                                    <button type="button" class="btn btn-default"
                                            ng-class="delivery_first == 0 ? 'disabled' :''" ng-click="first_delivery()"><span
                                                class="fa fa-fw fa-fast-backward"></span></button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="delivery_previous == 0 ? 'disabled' :''"
                                            ng-click="previous_delivery()">
                                        <span class="fa fa-fw fa-chevron-left"></span></button>
                                    <button type="button" class="btn btn-default disabled">
                                        @{{delivery_deliverie}}/@{{nb_deliveries}}
                                    </button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="delivery_next == 0 ? 'disabled' :''"
                                            ng-click="next_delivery()"><span class="fa fa-fw fa-chevron-right"></span>
                                    </button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="delivery_last == 0 ? 'disabled' :''"
                                            ng-click="last_delivery()"><span class="fa fa-fw fa-fast-forward"></span>
                                    </button>
                                </div>
                            </div>
                            <div style="font-size: 4em;">
                                <i class="fa fa-lock-open text-success" ng-hide="delivery.finalized"></i>
                                <i class="fa fa-lock text-danger" ng-show="delivery.finalized"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right" style="margin-top: 50px;">{{ __t("Weight") }} : @{{ delivery.weight | weight }}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div ng-include="hook.template" ng-repeat="hook in hooksComZeappsCRM_DeliveryHeaderRightHook"
                                 style="display: inline-block" class="pull-right">
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
                            <td><a href="/ng/com_zeapps_crm/delivery/@{{credit.id_delivery}}">@{{ credit.numerotation
                                    }}</a></td>
                            <td><a href="/ng/com_zeapps_contact/companies/@{{credit.id_company}}">@{{
                                    credit.name_company }}</a></td>
                            <td><a href="/ng/com_zeapps_contact/contacts/@{{credit.id_contact}}">@{{ credit.name_contact
                                    }}</a></td>
                            <td class="text-right">@{{ credit.total | currency:'€':2 }}</td>
                            <td class="text-right">@{{ credit.paid | currency:'€':2 }}</td>
                            <td class="text-right">@{{ credit.left_to_pay | currency:'€':2 }}</td>
                            <td class="text-right">@{{ credit.due_date | date:'dd/MM/yyyy' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="alert alert-danger" role="alert"
             ng-show="(delivery.id_price_list != company.id_price_list && company.id) || (!company.id && delivery.id_price_list != contact.id_price_list && contact.id)">
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
            <li ng-class="navigationState =='email' ? 'active' : ''"><a href="#" ng-click="setTab('email')">{{ __t("Email") }}</a>
            </li>
        </ul>

        <div ng-show="navigationState =='body'">
            <div class="row">
                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                    <div class="col-md-12 text-right">
                    <span class="form-inline">
                        <label>{{ __t("Product code") }} :</label>
                        <span class="input-group">
                            <input type="text" class="form-control input-sm" ng-model="codeProduct"
                                   ng-keydown="keyEventaddFromCode($event)">
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                    </span>
                        <ze-btn fa="tags" color="success" hint="{{ __t("product") }}" always-on="true" ng-click="addLine()"></ze-btn>
                        <ze-btn fa="dollar-sign" color="info" hint="{{ __t("subtotal") }}" always-on="true"
                                ng-click="addSubTotal()"></ze-btn>
                        <ze-btn fa="comments" color="warning" hint="{{ __t("comment") }}" always-on="true"
                                ze-modalform="addComment"
                                data-title="{{ __t("Add a comment") }}"
                                data-template="deliveryCommentTplUrl"></ze-btn>
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
                        <tbody @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))ui-sortable="sortable"
                               class="sortableContainer" @endif ng-model="lines">
                        <tr ng-repeat="line in lines"
                            ng-class="[line.type == 'subTotal' ? 'sous-total info' : '', line.type == 'comment' ? 'warning' : '']"
                            data-id="@{{ line.id }}">

                            <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.ref }}
                            </td>

                            <td ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                <strong>@{{ line.designation_title }} <span
                                            ng-if="line.designation_desc">:</span></strong><br>
                                <span class="text-wrap">@{{ line.designation_desc }}</span>
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.qty | number }}
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.price_unit | currency }}
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.taxeLabel != "*" ? (line.taxeLabel != 0 ? (line.taxeLabel | currency:'%':2) :
                                ''): "*" }}
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.discount != 0 && line.discount_prohibited == 0 ? ((0-line.discount) |
                                currency:'%':2) : ''}}
                                <span ng-show="line.discount > line.maximum_discount_allowed" class="text-danger"><br><i
                                            class="fas fa-exclamation-triangle"></i> {{ __t("Discount max. authorized") }} : @{{ line.maximum_discount_allowed }} %</span>
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.total_ht | currency:'€':2 }}
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.total_ttc | currency:'€':2 }}
                            </td>

                            <td colspan="6" class="text-right" ng-if="line.type == 'subTotal'">
                                Sous-Total
                            </td>

                            <td class="text-right" ng-if="line.type == 'subTotal'">
                                @{{ subtotalHT($index) | currency:'€':2 }}
                            </td>
                            <td class="text-right" ng-if="line.type == 'subTotal'">
                                @{{ subtotalTTC($index) | currency:'€':2 }}
                            </td>

                            <td colspan="8" class="text-wrap" ng-if="line.type == 'comment'">@{{ line.designation_desc
                                }}
                            </td>

                            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                <td class="text-right">
                                    <span ng-if="line.type === 'product' || line.type === 'service' || line.type === 'pack'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}"
                                                ze-modalform="editLine"
                                                data-edit="line"
                                                data-title="{{ __t("Edit the line of the delivery") }}"
                                                data-template="deliveryLineTplUrl"></ze-btn>
                                    </span>
                                    <span ng-show="line.type === 'comment'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="{{ __t("Edit") }}"
                                                ze-modalform="editComment"
                                                data-edit="line"
                                                data-title="{{ __t("Edit a comment") }}"
                                                data-template="deliveryCommentTplUrl"></ze-btn>
                                    </span>
                                    <ze-btn fa="trash" color="danger" direction="left" hint="{{ __t("Delete") }}"
                                            ng-click="deleteLine(line)" ze-confirmation ng-if="line"></ze-btn>
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
                            <td>@{{ tableTax.base_tax | currency:'€':2 }}</td>
                            <td class="text-right">@{{ tableTax.rate_tax | currency:'%':2 }}</td>
                            <td class="text-right">@{{ tableTax.amount_tax | currency:'€':2 }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-5 col-md-offset-2 divPrice">
                    <div class="well well-sm">
                        <div ng-if="delivery.total_discount > 0">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ __t("Total before tax before discount") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ delivery.total_prediscount_ht | currency:'€':2 }}
                                </div>
                            </div>
                            <hr>

                            <div class="row" ng-if="delivery.global_discount > 0">
                                <div class="col-md-6">
                                    {{ __t("Global discount") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    -@{{ delivery.global_discount | number:2 }}%
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ __t("Total discounts (excluding taxes)") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ delivery.total_discount_ht | currency:'€':2 }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ __t("Total discounts (all taxes included)") }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ delivery.total_discount_ttc | currency:'€':2 }}
                                </div>
                            </div>

                            <hr>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                {{ __t("Total duty") }}
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ delivery.total_ht | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{ __t("Total taxes") }}
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ delivery.total_tva | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                {{ __t("Total All taxes included") }}
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ delivery.total_ttc | currency:'€':2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-show="navigationState=='header'">
            <strong>{{ __t("Client reference") }} :</strong>
            @{{ delivery.reference_client }}
            <br/>
            <strong>{{ __t("Creation date") }} :</strong>
            @{{ delivery.date_creation || "-" | date:'dd/MM/yyyy' }}
            <br/>
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>{{ __t("Payment terms") }} :</strong>
            @{{ delivery.label_modality }}
        </div>

        <div ng-show="navigationState=='activity'">
            @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 15px;">
                        <div class="pull-right">
                            <ze-btn data-fa="plus" data-hint="{{ __t("Activity") }}" always-on="true" data-color="success"
                                    ze-modalform="addActivity"
                                    data-template="deliveryActivityTplUrl"
                                    data-title="{{ __t("Create an activity") }}"></ze-btn>
                        </div>
                    </div>
                </div>
            @endif
            <div class="card_document" ng-repeat="activity in activities | orderBy:['-deadline','-id']">
                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card_document-head clearfix">
                                <div class="pull-right">
                                    @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                        <ze-btn data-fa="edit" data-hint="{{ __t("Edit") }}" data-direction="left" data-color="info"
                                                ze-modalform="editActivity"
                                                data-edit="activity"
                                                data-template="deliveryActivityTplUrl"
                                                data-title="{{ __t("Edit activity") }}"></ze-btn>
                                        <ze-btn data-fa="trash" data-hint="{{ __t("Delete") }}" data-direction="left"
                                                data-color="danger" ng-click="deleteActivity(activity)"
                                                ze-confirmation></ze-btn>
                                    @endif
                                </div>
                                <strong>@{{ activity.label_type ? activity.label_type + " : " : "" }}@{{
                                    activity.libelle }}</strong><br>
                                {{ __t("Deadline") }} : @{{ activity.deadline || "-" | date:'dd/MM/yyyy' }} - @{{ activity.status
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
                            <ze-btn data-fa="plus" data-hint="{{ __t("Document") }}" always-on="true" data-color="success"
                                    ze-modalform="addDocument"
                                    data-template="deliveryDocumentTplUrl"
                                    data-title="{{ __t("Add document") }}"></ze-btn>
                        </div>
                    @endif
                    <div class="card_document" ng-repeat="document in documents | orderBy:['-date','-id']">
                        <div class="card_document-head clearfix">
                            <div class="pull-right">
                                @if (in_array("com_zeapps_crm_write", $zeapps_right_current_user))
                                    <ze-btn data-fa="edit" data-hint="{{ __t("Edit") }}" data-direction="left" data-color="info"
                                            ze-modalform="editDocument"
                                            data-edit="document"
                                            data-template="deliveryDocumentTplUrl"
                                            data-title="{{ __t("Edit document") }}"></ze-btn>
                                    <ze-btn data-fa="trash" data-hint="{{ __t("Delete") }}" data-direction="left" data-color="danger"
                                            ng-click="deleteDocument(document)" ze-confirmation></ze-btn>
                                @endif
                            </div>
                            <i class="fa fa-fw fa-file"></i>
                            <a ng-href="@{{ document.path }}" class="text-primary" target="_blank">
                                <strong>@{{ document.label }}</strong>
                            </a>
                        </div>
                        <div class="card_document-body" ng-if="document.description">@{{ document.description }}</div>
                        <div class="card_document-footer text-muted">
                            {{ __t("Sent by") }} <strong>@{{ document.name_user }}</strong> le <strong>@{{ document.date |
                                date:'dd/MM/yyyy' }}</strong> à <strong>@{{ document.date || "-" | date:'HH:mm'
                                }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-if="navigationState=='email'">
            <div ng-include="'/zeapps/email/list_partial'"
                 ng-init="module = 'com_zeapps_crm'; id = 'deliveries_' + delivery.id"></div>
        </div>

    </form>

</div>