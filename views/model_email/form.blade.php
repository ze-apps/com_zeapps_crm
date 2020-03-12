<div id="content">
    <form>

        *********** TODO ***********<br>
        *********** TODO ***********<br>
        *********** TODO ***********<br>
        *********** TODO ***********<br>
        *********** TODO ***********<br>
        *********** TODO ***********<br>

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Publication <span class="required">*</span></label>
                    <select class="form-control" ng-model="form.id_publication" ng-required="true">
                        <option value="">Choisissez une publication...</option>
                        <option ng-repeat="publication in publications" ng-value="publication.id">
                            @{{publication.label}}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Numéro <span class="required">*</span></label>
                    <input type="number" class="form-control" ng-model="form.numero">
                </div>
            </div>
        </div>

        <form-buttons></form-buttons>

    </form>
</div>