<div id="breadcrumb">Devis</div>


<div id="content">
    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <div class="titleWell" style="font-weight: bold;">
                        Devis : @{{ quote.libelle }}
                    </div>
                    <p class="small" style="font-weight: bold; font-size: 1.2em;">
                        n° @{{ quote.numerotation }}
                    </p>
                    <p>
                        <span class="form-group form-inline">
                            <select class="form-control input-sm" ng-model="quote.status" ng-change="updateStatus()">
                                <option ng-repeat="stat in status" ng-value="@{{stat.id}}">
                            @{{ stat.label }}
                        </option>
                            </select>
                        </span>
                        (@{{ quote.probability | number:2 }}%)
                    </p>
                    <button type="button" class="btn btn-xs btn-info"
                            ng-click="showDetailsEntreprise = !showDetailsEntreprise">
                        @{{ showDetailsEntreprise ? 'Masquer' : 'Voir' }} en cours
                    </button>
                </div>

                <div class="col-md-3">
                    <strong>Adresse de facturation :</strong><br>

                    <div ng-show="quote.billing_address_full_text"
                         ng-bind-html="quote.billing_address_full_text | nl2br"></div>
                    <div ng-show="quote.billing_address_full_text && quote.id_company"><a
                                href="/ng/com_zeapps_contact/companies/@{{ quote.id_company }}" class="btn btn-info btn-xs">Voir
                            l'entreprise</a></div>

                    <div ng-show="quote.billing_address_full_text && quote.id_contact"><a
                                href="/ng/com_zeapps_contact/contacts/@{{ quote.id_contact }}" class="btn btn-info btn-xs">Voir
                            le contact</a></div>


                    <div ng-hide="quote.billing_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ quote.name_company }}</a><br
                                ng-if="quote.name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ quote.name_contact }}</a><br
                                ng-if="quote.name_contact">
                        @{{ quote.billing_address_1 }}<br ng-if="quote.billing_address_1">
                        @{{ quote.billing_address_2 }}<br ng-if="quote.billing_address_2">
                        @{{ quote.billing_address_3 }}<br ng-if="quote.billing_address_3">
                        @{{ quote.billing_zipcode + ' ' + quote.billing_city }}<br ng-if="quote.billing_state != ''">
                        @{{ quote.billing_state }}<br ng-if="quote.billing_country_name != ''">
                        @{{ quote.billing_country_name }}
                    </div>

                </div>

                <div class="col-md-3">
                    <strong>Adresse de livraison :</strong><br>

                    <div ng-show="quote.delivery_address_full_text"
                         ng-bind-html="quote.delivery_address_full_text | nl2br"></div>
                    <div ng-show="quote.delivery_address_full_text && quote.id_company_delivery"><a
                                href="/ng/com_zeapps_contact/companies/@{{ quote.id_company_delivery }}" class="btn btn-info btn-xs">Voir
                            l'entreprise</a></div>

                    <div ng-show="quote.delivery_address_full_text && quote.id_contact_delivery"><a
                                href="/ng/com_zeapps_contact/contacts/@{{ quote.id_contact_delivery }}" class="btn btn-info btn-xs">Voir
                            le contact</a></div>

                    <div ng-hide="quote.delivery_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ quote.delivery_name_company
                            }}</a><br ng-if="quote.delivery_name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ quote.delivery_name_contact
                            }}</a><br
                                ng-if="quote.delivery_name_contact">
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
                                <ze-btn fa="arrow-left" color="primary" hint="Retour" direction="left"
                                        ng-click="back()"></ze-btn>
                                <ze-btn fa="edit" color="info" hint="Editer" direction="left"
                                        ze-modalform="updateQuote"
                                        data-edit="quote"
                                        data-template="templateEdit"
                                        data-title="Modifier le devis"></ze-btn>
                                <ze-btn fa="download" color="primary" hint="PDF" direction="left" ng-click="print()"></ze-btn>



                                <ze-btn fa="envelope" color="primary" hint="Envoyer par email" direction="left" ng-click="sendByMail()"></ze-btn>


                                <ze-btn fa="copy" color="success" hint="Dupliquer" direction="left"
                                        ng-click="transform()"></ze-btn>

                                <div class="btn-group btn-group-xs" role="group" ng-if="nb_quotes > 0">
                                    <button type="button" class="btn btn-default" ng-class="quote_first == 0 ? 'disabled' :''"
                                            ng-click="first_quote()"><span class="fa fa-fw fa-fast-backward"></span></button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="quote_previous == 0 ? 'disabled' :''" ng-click="previous_quote()"><span
                                                class="fa fa-fw fa-chevron-left"></span></button>
                                    <button type="button" class="btn btn-default disabled">@{{quote_order}}/@{{nb_quotes}}
                                    </button>
                                    <button type="button" class="btn btn-default" ng-class="quote_next == 0 ? 'disabled' :''"
                                            ng-click="next_quote()"><span class="fa fa-fw fa-chevron-right"></span></button>
                                    <button type="button" class="btn btn-default" ng-class="quote_last == 0 ? 'disabled' :''"
                                            ng-click="last_quote()"><span class="fa fa-fw fa-fast-forward"></span></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right" style="margin-top: 50px;">Poids : @{{ quote.weight | weight }}</div>
                        </div>


                        <div class="col-md-12">
                            <div ng-include="hook.template" ng-repeat="hook in hooksComZeappsCRM_QuoteHeaderRightHook"
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
                            <th>N° Facture</th>
                            <th>Entreprise</th>
                            <th>Contact</th>
                            <th class="text-right">Total à payer</th>
                            <th class="text-right">Payé</th>
                            <th class="text-right">Restant à payer</th>
                            <th class="text-right">Date limite</th>
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



        <div class="alert alert-danger" role="alert" ng-show="(quote.id_price_list != company.id_price_list && company.id) || (!company.id && quote.id_price_list != contact.id_price_list && contact.id)">
            Attention : la grille de prix appliquée sur ce document ne correspond pas à la grille de prix du client.
        </div>

        <ul role="tablist" class="nav nav-tabs">
            <li ng-class="navigationState =='body' ? 'active' : ''"><a href="#" ng-click="setTab('body')">Corps</a></li>
            <li ng-class="navigationState =='header' ? 'active' : ''"><a href="#" ng-click="setTab('header')">Entête</a>
            </li>
            <li ng-class="navigationState =='condition' ? 'active' : ''"><a href="#" ng-click="setTab('condition')">Conditions</a>
            </li>
            <li ng-class="navigationState =='activity' ? 'active' : ''"><a href="#" ng-click="setTab('activity')">Activité</a>
            </li>
            <li ng-class="navigationState =='document' ? 'active' : ''"><a href="#" ng-click="setTab('document')">Documents</a>
            </li>
            <li ng-class="navigationState =='email' ? 'active' : ''"><a href="#" ng-click="setTab('email')">Email</a></li>
        </ul>

        <div ng-show="navigationState =='body'">
            <div class="row">
                <div class="col-md-12 text-right">
                    <span class="form-inline">
                        <label>Code produit :</label>
                        <span class="input-group" ng-keyup>
                            <input type="text" class="form-control input-sm inputCodeProduct" ng-model="codeProduct"
                                   ng-keydown="keyEventaddFromCode($event)">
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                    </span>
                    <ze-btn fa="tags" color="success" hint="produit" always-on="true" ng-click="addLine()"></ze-btn>
                    <ze-btn fa="dollar-sign" color="info" hint="sous-total" always-on="true" ng-click="addSubTotal()"></ze-btn>
                    <ze-btn fa="comments" color="warning" hint="commentaire" always-on="true"
                            ze-modalform="addComment"
                            data-title="Ajouter un commentaire"
                            data-template="quoteCommentTplUrl"></ze-btn>
                </div>
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
                        <tbody ui-sortable="sortable" class="sortableContainer" ng-model="lines">
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
                                @{{ line.taxeLabel != "*" ? (line.taxeLabel != 0 ? (line.taxeLabel | currency:'%':2) : ''): "*" }}
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.discount != 0 && line.discount_prohibited == 0 ? ((0-line.discount) | currency:'%':2) : ''}}
                                <span ng-show="line.discount > line.maximum_discount_allowed" class="text-danger"><br><i class="fas fa-exclamation-triangle"></i> Remise max. autorisée : @{{ line.maximum_discount_allowed }} %</span>
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

                            <td class="text-right">
                                    <span ng-if="line.type === 'product' || line.type === 'service' || line.type === 'pack'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="editer"
                                                ze-modalform="editLine"
                                                data-edit="line"
                                                data-title="Editer la ligne du devis"
                                                data-template="quoteLineTplUrl"></ze-btn>
                                    </span>
                                <span ng-show="line.type === 'comment'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="editer"
                                                ze-modalform="editComment"
                                                data-edit="line"
                                                data-title="Modifier un commentaire"
                                                data-template="quoteCommentTplUrl"></ze-btn>
                                    </span>
                                <ze-btn fa="trash" color="danger" direction="left" hint="Supprimer"
                                        ng-click="deleteLine(line)" ze-confirmation ng-if="line"></ze-btn>
                            </td>
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
                            <th>Base TVA</th>
                            <th class="text-right">Taux TVA</th>
                            <th class="text-right">Montant TVA</th>
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
                        <div ng-if="quote.total_discount_ht > 0">
                            <div class="row">
                                <div class="col-md-6">
                                    Total HT av remise
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_prediscount_ht | currency:'€':2 }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    Total TTC av remise
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_prediscount_ttc | currency:'€':2 }}
                                </div>
                            </div>
                            <hr>
                            <div class="row" ng-if="quote.global_discount > 0">
                                <div class="col-md-6">
                                    Remise globale
                                </div>
                                <div class="col-md-6 text-right">
                                    -@{{ quote.global_discount | number:2 }}%
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    Total remises HT
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_discount_ht | currency:'€':2 }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    Total remises TTC
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ quote.total_discount_ttc | currency:'€':2 }}
                                </div>
                            </div>

                            <hr>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total HT
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ quote.total_ht | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                Total TVA
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ quote.total_tva | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total TTC
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ quote.total_ttc | currency:'€':2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-show="navigationState=='header'">
            <strong>Reference Client :</strong>
            @{{ quote.reference_client }}
            <br/>
            <strong>Date de création du devis :</strong>
            @{{ quote.date_creation || "-" | date:'dd/MM/yyyy' }}
            <br/>
            <strong>Date de validité du devis :</strong>
            @{{ quote.date_limit || "-" | date:'dd/MM/yyyy' }}
            <br/>
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>Modalités de paiement :</strong>
            @{{ quote.label_modality }}
        </div>

        <div ng-show="navigationState=='activity'">
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 15px;">
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="Activité" always-on="true" data-color="success"
                                ze-modalform="addActivity"
                                data-template="quoteActivityTplUrl"
                                data-title="Créer une activité"></ze-btn>
                    </div>
                </div>
            </div>
            <div class="card_document" ng-repeat="activity in activities | orderBy:['-date','-id']">
                <div class="well">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card_document-head clearfix">
                                <div class="pull-right">
                                    <ze-btn data-fa="edit" data-hint="Editer" data-direction="left" data-color="info"
                                            ze-modalform="editActivity"
                                            data-edit="activity"
                                            data-template="quoteActivityTplUrl"
                                            data-title="Modifier l'activité"></ze-btn>
                                    <ze-btn data-fa="trash" data-hint="Supprimer" data-direction="left"
                                            data-color="danger" ng-click="deleteActivity(activity)"
                                            ze-confirmation></ze-btn>
                                </div>
                                <strong>@{{ activity.label_type ? activity.label_type + " : " : "" }}@{{
                                    activity.libelle }}</strong><br>
                                Date limite : @{{ activity.deadline || "-" | date:'dd/MM/yyyy' }} - @{{ activity.status
                                }}
                            </div>
                            <div class="card_document-body" ng-if="activity.description">@{{ activity.description }}
                            </div>
                            <div class="card_document-footer text-muted">
                                Créé par <strong>@{{ activity.name_user }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div ng-show="navigationState=='document'">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="Document" always-on="true" data-color="success"
                                ze-modalform="addDocument"
                                data-template="quoteDocumentTplUrl"
                                data-title="Ajouter un document"></ze-btn>
                    </div>
                    <div class="card_document" ng-repeat="document in documents | orderBy:['-date','-id']">
                        <div class="card_document-head clearfix">
                            <div class="pull-right">
                                <ze-btn data-fa="edit" data-hint="Editer" data-direction="left" data-color="info"
                                        ze-modalform="editDocument"
                                        data-edit="document"
                                        data-template="quoteDocumentTplUrl"
                                        data-title="Modifier le document"></ze-btn>
                                <ze-btn data-fa="trash" data-hint="Supprimer" data-direction="left" data-color="danger"
                                        ng-click="deleteDocument(document)" ze-confirmation></ze-btn>
                            </div>
                            <i class="fa fa-fw fa-file"></i>
                            <a ng-href="@{{ document.path }}" class="text-primary" target="_blank">
                                <strong>@{{ document.label }}</strong>
                            </a>
                        </div>
                        <div class="card_document-body" ng-if="document.description">@{{ document.description }}</div>
                        <div class="card_document-footer text-muted">
                            Envoyé par <strong>@{{ document.name_user }}</strong> le <strong>@{{ document.date |
                                date:'dd/MM/yyyy' }}</strong> à <strong>@{{ document.date || "-" | date:'HH:mm'
                                }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-if="navigationState=='email'">
            <div ng-include="'/zeapps/email/list_partial'" ng-init="module = 'com_zeapps_crm'; id = 'quotes_' + quote.id"></div>
        </div>

    </form>

</div>