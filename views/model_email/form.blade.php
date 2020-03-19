<div id="content">
    <form>

        <h3>Modèle email</h3>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Nom du modèle <span class="required">*</span></label>
                    <input type="text" class="form-control" ng-model="form.name">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Destinetaire par défaut (séparer les adresses par une virgule)</label>
                    <input type="text" class="form-control" ng-model="form.default_to">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Objet de l'email <span class="required">*</span></label>
                    <input type="text" class="form-control" ng-model="form.subject">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Contenu de l'email <span class="required">*</span></label>
                    <textarea class="form-control" ng-model="form.message" rows="10"></textarea>
                    Vous pouvez insérer les codes suivants pour créer le contenu de l'email<br>
                    [company] : Nom de l'entreprise<br>
                    [contact] : Nom du contact<br>
                    [number_doc] : N° du document<br>
                    [type_doc] : Type de document (devis, commande, facture, bon de livraison)<br>
                    [amount] : Montant toutes taxes comprise du document<br>
                    [amount_without_taxes] : Montant hors taxes du document<br>
                    [reference] : Référence du document<br>
                    [label_doc] : Libellé du document<br>
                    [doc_manager] : Responsable du document<br>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Pièces jointes</label>
                    <button type="file" ngf-select="uploadFiles($file, $invalidFiles)" class="btn btn-success btn-xs"
                            {{-- accept="image/*" ngf-max-height="1000" ngf-max-size="1MB"--}}>
                        Ajouter
                    </button>
                    <br>
                    <div style="font:smaller">@{{errFile.$error}} @{{errFile.$errorParam}}
                        <span class="progress" ng-show="f.progress >= 0 && f.progress < 100">
                          <div style="width:@{{f.progress}}%"
                               ng-bind="f.progress + '%'"></div>
                      </span>
                    </div>
                    @{{errorMsg}}

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Fichier</th>
                            <th>Path</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="attachment in form.attachments">
                            <td>@{{ attachment.name }}</td>
                            <td>@{{ attachment.path }}</td>
                            <td class="text-right"><button type="button" class="btn btn-xs btn-danger" ng-click="deleteFile(attachment.path)"><i class="fa fa-trash"></i> Supprimer</button></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="checkbox" ng-model="form.to_quote"
                               ng-true-value="1" ng-false-value="0" ng-checked="form.to_quote == 1">
                        Pour les devis
                    </label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="checkbox" ng-model="form.to_order"
                               ng-true-value="1" ng-false-value="0" ng-checked="form.to_order == 1">
                        Pour les commandes
                    </label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="checkbox" ng-model="form.to_invoice"
                               ng-true-value="1" ng-false-value="0" ng-checked="form.to_invoice == 1">
                        Pour les factures
                    </label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" class="checkbox" ng-model="form.to_delivery"
                               ng-true-value="1" ng-false-value="0" ng-checked="form.to_delivery == 1">
                        Pour les bons de livraison
                    </label>
                </div>
            </div>
        </div>


        <form-buttons></form-buttons>

    </form>
</div>