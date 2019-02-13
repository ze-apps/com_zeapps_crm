<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            Date encaissement : @{{ payment.date_payment || "-"  | date:'dd/MM/yyyy' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            Montant : @{{ payment.total | currency:'€':2 }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            Type de paiement : @{{ payment.type_payment_label }}
        </div>
    </div>
    <div class="row" ng-if="payment_lines.length">
        <div class="col-md-12">
            <table class="table table-hover table-condensed table-responsive" ng-show="payment_lines.length">
                <thead>
                <tr>
                    <th>Date Facture</th>
                    <th>N° Facture</th>
                    <th>Objet</th>
                    <th class="text-right">Montant Facture</th>
                    <th class="text-right">Montant Encaissé</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="line in payment_lines">
                    <td>@{{ line.invoice_data.date_creation || "-"  | date:'dd/MM/yyyy' }}</td>
                    <td>@{{ line.invoice_data.numerotation }}</td>
                    <td>@{{ line.invoice_data.libelle }}</td>
                    <td class="text-right">@{{ line.invoice_data.total_ttc | currency:'€':2 }}</td>
                    <td class="text-right">@{{ line.amount | currency:'€':2 }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">Fermer</button>
</div>