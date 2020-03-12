<div id="breadcrumb">Publicité</div>
<div id="content">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <a class="btn btn-xs btn-success" href="/ng/com_quiltmania_publicite/publicite/new">
                    <i class="fa fa-fw fa-plus"></i> Publication
                </a>
            </div>
            <h3>Publicité</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover table-condensed">
                <thead>
                <tr>
                    <th>Publication</th>
                    <th>Numéro</th>
                    <th>Nb pub</th>
                    <th>total HT</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="publicite in publicites" ng-click="edit(publicite)">
                    <td>@{{ publicite.label_publication }}</td>
                    <td>@{{ publicite.numero }}</td>
                    <td>@{{ publicite.qty }}</td>
                    <td>@{{ publicite.total_ht }}</td>

                    <td class="text-right">
                        <a class="btn btn-info btn-xs" ng-href="/ng/com_quiltmania_publicite/publicite/edit/@{{ publicite.id }}">
                            <i class="fas fa-fw fa-edit"></i>
                        </a>

                        <ze-btn fa="trash" color="danger" hint="Supprimer" direction="left" ng-click="delete(publicite)" ze-confirmation></ze-btn>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>