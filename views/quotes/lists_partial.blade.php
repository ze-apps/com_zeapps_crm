<div ng-controller="ComZeappsCrmQuoteListsPartialCtrl">
    <div class="row">
        <div class="col-md-12">
            <ze-filters class="pull-right" data-model="filter_model" data-filters="filters" data-update="loadList"></ze-filters>

            <ze-btn fa="plus" color="success" hint="Devis" always-on="true"
                    ze-modalform="add"
                    data-template="templateQuote"
                    data-title="Créer un nouveau devis"></ze-btn>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="quotes.length">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date de création</th>
                    <th>Destinataire</th>
                    <th>Libelle</th>
                    <th class="text-right">Total HT</th>
                    <th class="text-right">Total TTC</th>
                    <th>Date limite</th>
                    <th>Responsable</th>
                    <th class="text-right">Probabilité</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="quote in quotes">
                    <td ng-click="goTo(quote.id)">@{{quote.numerotation}}</td>
                    <td ng-click="goTo(quote.id)">@{{quote.date_creation || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(quote.id)">

                        @{{quote.name_company}}
                        <span ng-if="quote.name_company && quote.name_contact">-</span>
                        @{{quote.name_contact ? quote.name_contact : ''}}

                    </td>
                    <td ng-click="goTo(quote.id)">@{{quote.libelle}}</td>
                    <td ng-click="goTo(quote.id)" class="text-right">@{{quote.total_ht | currency:'€':2}}</td>
                    <td ng-click="goTo(quote.id)" class="text-right">@{{quote.total_ttc | currency:'€':2}}</td>
                    <td ng-click="goTo(quote.id)">@{{quote.date_limit || "-" | date:'dd/MM/yyyy'}}</td>
                    <td ng-click="goTo(quote.id)">@{{quote.name_user_account_manager}}</td>
                    <td ng-click="goTo(quote.id)" class="text-right">@{{quote.probability | number:2}}</td>
                    <td ng-click="goTo(quote.id)">@{{ showStatus(quote.status) }}</td>
                    <td class="text-right">
                        <ze-btn fa="edit" color="info" direction="left" hint="Editer"
                                ze-modalform="edit"
                                data-edit="quote"
                                data-title="Editer le devis"
                                data-template="templateQuote"></ze-btn>
                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(quote)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" ng-show="total > pageSize">
        <ul uib-pagination total-items="total" ng-model="page" items-per-page="pageSize" ng-change="loadList()"
            class="pagination-sm" boundary-links="true" max-size="15"
            previous-text="&lsaquo;" next-text="&rsaquo;" first-text="&laquo;" last-text="&raquo;"></ul>
    </div>

</div>