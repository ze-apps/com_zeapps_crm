<div id="breadcrumb">{{ __t("Products") }}</div>
<div id="content">

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" ng-show="error">@{{ error }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="root">
                <zeapps-happylittletree data-tree="tree.branches" data-update="update"></zeapps-happylittletree>
            </div>
        </div>

        <form name="newCategory" class="col-md-9">
            <div class="well">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __t("Category Name") }} <span class="required">*</span></label>
                            <input type="text" ng-model="form.name" class="form-control" ng-required="true">
                            <input type="hidden" ng-model="form.id_parent">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>{{ __t("Category") }} <span class="required">*</span></label>

                            <select ng-model="form.id_parent" ng-change="" class="form-control">
                                <option ng-repeat="tree in tree_select" ng-value="@{{tree.id}}" ng-bind-html="tree.name | trusted" />
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <span class="required">*</span> {{ __t("Required fields") }}
                </div>
            </div>


            <form-buttons></form-buttons>
        </form>
    </div>

</div>