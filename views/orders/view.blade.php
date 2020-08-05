<div id="breadcrumb">Commandes</div>
<div id="content">


    <form>
        <div class="well">
            <div class="row">
                <div class="col-md-2">
                    <div class="titleWell" style="font-weight: bold;">
                        Commande : @{{ order.libelle }}
                    </div>
                    <p class="small" style="font-weight: bold; font-size: 1.2em;">
                        n° @{{ order.numerotation }}
                    </p>

                    <button type="button" class="btn btn-xs btn-info"
                            ng-click="showDetailsEntreprise = !showDetailsEntreprise">
                        @{{ showDetailsEntreprise ? 'Masquer' : 'Voir' }} en cours
                    </button>
                </div>

                <div class="col-md-3">
                    <strong>Adresse de facturation :</strong><br>

                    <div ng-show="order.billing_address_full_text"
                         ng-bind-html="order.billing_address_full_text | nl2br"></div>
                    <div ng-show="order.billing_address_full_text && order.id_company"><a
                                href="/ng/com_zeapps_contact/companies/@{{ order.id_company }}"
                                class="btn btn-info btn-xs">Voir
                            l'entreprise</a></div>

                    <div ng-show="order.billing_address_full_text && order.id_contact"><a
                                href="/ng/com_zeapps_contact/contacts/@{{ order.id_contact }}"
                                class="btn btn-info btn-xs">Voir
                            le contact</a></div>


                    <div ng-hide="order.billing_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ order.name_company }}</a><br
                                ng-if="order.name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ order.name_contact }}</a><br
                                ng-if="order.name_contact">
                        @{{ order.billing_address_1 }}<br ng-if="order.billing_address_1">
                        @{{ order.billing_address_2 }}<br ng-if="order.billing_address_2">
                        @{{ order.billing_address_3 }}<br ng-if="order.billing_address_3">
                        @{{ order.billing_zipcode + ' ' + order.billing_city }}<br ng-if="order.billing_state != ''">
                        @{{ order.billing_state }}<br ng-if="order.billing_country_name != ''">
                        @{{ order.billing_country_name }}
                    </div>

                </div>

                <div class="col-md-3">
                    <strong>Adresse de livraison :</strong><br>

                    <div ng-show="order.delivery_address_full_text"
                         ng-bind-html="order.delivery_address_full_text | nl2br"></div>
                    <div ng-show="order.delivery_address_full_text && order.id_company_delivery"><a
                                href="/ng/com_zeapps_contact/companies/@{{ order.id_company_delivery }}"
                                class="btn btn-info btn-xs">Voir
                            l'entreprise</a></div>

                    <div ng-show="order.delivery_address_full_text && order.id_contact_delivery"><a
                                href="/ng/com_zeapps_contact/contacts/@{{ order.id_contact_delivery }}"
                                class="btn btn-info btn-xs">Voir
                            le contact</a></div>

                    <div ng-hide="order.delivery_address_full_text">
                        <a href="/ng/com_zeapps_contact/companies/@{{ company.id }}">@{{ order.delivery_name_company
                            }}</a><br ng-if="order.delivery_name_company">
                        <a href="/ng/com_zeapps_contact/contacts/@{{ contact.id }}">@{{ order.delivery_name_contact
                            }}</a><br
                                ng-if="order.delivery_name_contact">
                        @{{ order.delivery_address_1 }}<br ng-if="order.delivery_address_1">
                        @{{ order.delivery_address_2 }}<br ng-if="order.delivery_address_2">
                        @{{ order.delivery_address_3 }}<br ng-if="order.delivery_address_3">
                        @{{ order.delivery_zipcode + ' ' + order.delivery_city }}<br ng-if="order.delivery_state != ''">
                        @{{ order.delivery_state }}<br ng-if="order.delivery_country_name != ''">
                        @{{ order.delivery_country_name }}
                    </div>

                </div>

                <div class="col-md-4" style="text-align: right">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <ze-btn fa="arrow-left" color="primary" hint="Retour" direction="left"
                                        ng-click="back()"></ze-btn>
                                <ze-btn fa="edit" color="info" hint="Editer" direction="left"
                                        ze-modalform="updateOrder"
                                        data-edit="order"
                                        data-template="templateEdit"
                                        data-title="Modifier la commande"
                                        ng-hide="order.finalized"></ze-btn>
                                <ze-btn fa="download" color="primary" hint="PDF" direction="left"
                                        ng-click="print()"></ze-btn>
                                <ze-btn fa="envelope" color="primary" hint="Envoyer par email" direction="left"
                                        ng-click="sendByMail()"></ze-btn>
                                <ze-btn fa="copy" color="success" hint="Dupliquer" direction="left"
                                        ng-click="transform()"></ze-btn>
                                <ze-btn fa="lock" color="danger" hint="Clôturer" direction="left" ng-click="finalize()"
                                        ng-hide="order.finalized"></ze-btn>

                                <div class="btn-group btn-group-xs" role="group" ng-if="nb_orders > 0">
                                    <button type="button" class="btn btn-default"
                                            ng-class="order_first == 0 ? 'disabled' :''"
                                            ng-click="first_order()"><span class="fa fa-fw fa-fast-backward"></span>
                                    </button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="order_previous == 0 ? 'disabled' :''" ng-click="previous_order()"><span
                                                class="fa fa-fw fa-chevron-left"></span></button>
                                    <button type="button" class="btn btn-default disabled">
                                        @{{order_order}}/@{{nb_orders}}
                                    </button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="order_next == 0 ? 'disabled' :''"
                                            ng-click="next_order()"><span class="fa fa-fw fa-chevron-right"></span>
                                    </button>
                                    <button type="button" class="btn btn-default"
                                            ng-class="order_last == 0 ? 'disabled' :''"
                                            ng-click="last_order()"><span class="fa fa-fw fa-fast-forward"></span>
                                    </button>
                                </div>
                            </div>
                            <div style="font-size: 4em;">
                                <i class="fa fa-lock-open text-success" ng-hide="order.finalized"></i>
                                <i class="fa fa-lock text-danger" ng-show="order.finalized"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="pull-right" style="margin-top: 50px;">Poids : @{{ order.weight | weight }}</div>
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


        <div class="alert alert-danger" role="alert"
             ng-show="(order.id_price_list != company.id_price_list && company.id) || (!company.id && order.id_price_list != contact.id_price_list && contact.id)">
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
            <li ng-class="navigationState =='email' ? 'active' : ''"><a href="#" ng-click="setTab('email')">Email</a>
            </li>
        </ul>

        <div ng-show="navigationState =='body'">
            <div class="row">
                <div class="col-md-12 text-right" ng-hide="order.finalized">
                    <span class="form-inline">
                        <label>Code produit :</label>
                        <span class="input-group">
                            <input type="text" id="comZeappsCrmCodeProduct" class="form-control input-sm" ng-model="codeProduct"
                                   ng-keydown="keyEventaddFromCode($event)">
                            <span class="input-group-addon" ng-click="addFromCode()">
                                <i class="fa fa-fw fa-plus text-success"></i>
                            </span>
                        </span>
                        <label>Quantité :</label>
                        <span class="input-group">
                            <input type="text" id="comZeappsCrmQteCodeProduct" class="form-control input-sm" ng-model="qteCodeProduct"
                                   ng-keydown="keyEventaddFromCodeQte($event)" style="width: 50px">
                        </span>
                    </span>
                    <ze-btn fa="tags" color="success" hint="produit" always-on="true" ng-click="addLine()"></ze-btn>


                    <div ng-include="hook.template" ng-repeat="hook in hooksComZeappsCRM_OrderBtnTopBodyHook"
                         style="display: inline-block">
                    </div>


                    <ze-btn fa="dollar-sign" color="info" hint="sous-total" always-on="true"
                            ng-click="addSubTotal()"></ze-btn>
                    <ze-btn fa="comments" color="warning" hint="commentaire" always-on="true"
                            ze-modalform="addComment"
                            data-title="Ajouter un commentaire"
                            data-template="orderCommentTplUrl"></ze-btn>
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
                                @{{ line.taxeLabel != "*" ? (line.taxeLabel != 0 ? (line.taxeLabel | currency:'%':2) :
                                ''): "*" }}
                            </td>

                            <td class="text-right" ng-if="line.type != 'subTotal' && line.type != 'comment'">
                                @{{ line.discount != 0 && line.discount_prohibited == 0 ? ((0-line.discount) |
                                currency:'%':2) : ''}}
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
                                 <span ng-if="(line.type != 'product' && line.type != 'service' && line.type != 'pack' && line.type != 'comment' && line.type != 'subTotal') && order.finalized != 1">
                                    <button class="btn btn-info btn-xs" ng-click="editLigneSpecial(line)"><i
                                                class="fa fa-edit"></i></button>
                                </span>

                                <span ng-if="(line.type === 'product' || line.type === 'service' || line.type === 'pack') && order.finalized != 1">
                                        <ze-btn fa="edit" color="info" direction="left" hint="editer"
                                                ze-modalform="editLine"
                                                data-edit="line"
                                                data-title="Editer la ligne de commande"
                                                data-template="orderLineTplUrl"></ze-btn>
                                    </span>
                                <span ng-show="line.type === 'comment'">
                                        <ze-btn fa="edit" color="info" direction="left" hint="editer"
                                                ze-modalform="editComment"
                                                data-edit="line"
                                                data-title="Modifier un commentaire"
                                                data-template="orderCommentTplUrl"
                                                ng-hide="order.finalized"></ze-btn>
                                    </span>
                                <ze-btn fa="trash" color="danger" direction="left" hint="Supprimer"
                                        ng-click="deleteLine(line)" ze-confirmation
                                        ng-if="line && order.finalized != 1"></ze-btn>
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
                        <div ng-if="order.total_discount > 0">
                            <div class="row">
                                <div class="col-md-6">
                                    Total HT av remise
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ order.total_prediscount_ht | currency:'€':2 }}
                                </div>
                            </div>
                            <hr>

                            <div class="row" ng-if="order.global_discount > 0">
                                <div class="col-md-6">
                                    Remise globale
                                </div>
                                <div class="col-md-6 text-right">
                                    -@{{ order.global_discount | number:2 }}%
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    Total remises HT
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ order.total_discount_ht | currency:'€':2 }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    Total remises TTC
                                </div>
                                <div class="col-md-6 text-right">
                                    @{{ order.total_discount_ttc | currency:'€':2 }}
                                </div>
                            </div>

                            <hr>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total HT
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ order.total_ht | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                Total TVA
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ order.total_tva | currency:'€':2 }}
                            </div>
                        </div>

                        <div class="row total">
                            <div class="col-md-6">
                                Total TTC
                            </div>
                            <div class="col-md-6 text-right">
                                @{{ order.total_ttc | currency:'€':2 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div ng-show="navigationState=='header'">
            <strong>Reference Client :</strong>
            @{{ order.reference_client }}
            <br/>
            <strong>Date de création de la commande :</strong>
            @{{ order.date_creation || "-" | date:'dd/MM/yyyy' }}
            <br/>
            <strong>Date de validité de la commande :</strong>
            @{{ order.date_limit || "-" | date:'dd/MM/yyyy' }}
            <br/>
        </div>

        <div ng-show="navigationState=='condition'">
            <strong>Modalités de paiement :</strong>
            @{{ order.label_modality }}
        </div>

        <div ng-show="navigationState=='activity'">
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 15px;">
                    <div class="pull-right">
                        <ze-btn data-fa="plus" data-hint="Activité" always-on="true" data-color="success"
                                ze-modalform="addActivity"
                                data-template="orderActivityTplUrl"
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
                                            data-template="orderActivityTplUrl"
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
                                data-template="orderDocumentTplUrl"
                                data-title="Ajouter un document"></ze-btn>
                    </div>
                    <div class="card_document" ng-repeat="document in documents | orderBy:['-date','-id']">
                        <div class="card_document-head clearfix">
                            <div class="pull-right">
                                <ze-btn data-fa="edit" data-hint="Editer" data-direction="left" data-color="info"
                                        ze-modalform="editDocument"
                                        data-edit="document"
                                        data-template="orderDocumentTplUrl"
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
            <div ng-include="'/zeapps/email/list_partial'"
                 ng-init="module = 'com_zeapps_crm'; id = 'orders_' + order.id"></div>
        </div>

    </form>

</div>