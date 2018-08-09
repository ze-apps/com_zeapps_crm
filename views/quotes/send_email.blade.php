<div id="breadcrumb">Devis n° @{{ quote.numerotation }}</div>


<div id="content">
    <form>
        <div class="row" ng-controller="ComZeappsCrmQuoteSendEmailCtrl">

            <div class="col-md-12">
                <div class="form-group">
                    <label>Expéditeur</label><br>
                    @{{user.firstname[0]}}. @{{user.lastname}} <@{{user.email}}>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label>Destinataire(s)</label>
                    <input type="text" class="form-control" ng-model="form.to"/>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label>Sujet</label>
                    <input type="text" class="form-control" ng-model="form.subject"/>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" ng-model="form.content" row="10"></textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label>Pièce(s) jointe(s)</label>
                    <ul>
                        <li ng-repeat="attachment in attachments"><a ng-href="@{{ attachment.url | trusted }}"
                                                                     target="_blank">
                                @{{ attachment.name }}</a></li>
                    </ul>
                </div>
            </div>


            <div class="col-md-12">
                <button class="btn btn-default btn-xs" ng-click="cancel()">Annuler</button>
                <button class="btn btn-success" ng-click="send()">Envoyer</button>
            </div>
        </div>
    </form>
</div>