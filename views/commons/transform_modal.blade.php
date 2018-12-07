<div class="modal-header">
    <h3 class="modal-title">@{{titre}}</h3>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <h4>Souhaitez-vous créer automatiquement les documents suivants:</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.quotes">
                Devis
            </label>
        </div>
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.orders">
                Commande
            </label>
        </div>
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.invoices">
                Facture
            </label>
        </div>
        <div class="col-md-12">
            <label>
                <input type='checkbox' ng-model="form.deliveries">
                Bon de Livraison
            </label>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-danger" ng-click="cancel()">Annuler</button>
    <button type="sumbit" class="btn btn-success" ng-click="transform()">Convertir</button>
</div>