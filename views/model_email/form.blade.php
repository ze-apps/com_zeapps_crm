<div id="content">
    <form>

        <h3>{{ __t("Email template") }}</h3>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Model name") }} <span class="required">*</span></label>
                    <input type="text" class="form-control" ng-model="form.name">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Default recipient (separate addresses with a comma)") }}</label>
                    <input type="text" class="form-control" ng-model="form.default_to">
                    {{ __t("If you do not want the default contacts for the document, please include in the list of emails:") }}<br>
                [excludeOrigin]
                </div>
                
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Email subject") }} <span class="required">*</span></label>
                    <input type="text" class="form-control" ng-model="form.subject">
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Email content") }} <span class="required">*</span></label>
                    <textarea class="form-control" ng-model="form.message" rows="10"></textarea>
                    {{ __t("You can insert the following codes to create the content of the email") }}<br>
                    [company] : {{ __t("Company Name") }}<br>
                    [contact] : {{ __t("Contact Name") }}<br>
                    [number_doc] : {{ __t("Document number") }}<br>
                    [type_doc] : {{ __t("Type of document (quote, order, invoice, delivery note)") }}<br>
                    [amount] : {{ __t("Document amount including all taxes") }}<br>
                    [amount_without_taxes] : {{ __t("Document amount excluding tax") }}<br>
                    [reference] : {{ __t("Document reference") }}<br>
                    [label_doc] : {{ __t("Wording of the document") }}<br>
                    [doc_manager] : {{ __t("Document manager") }}<br>
                    [signature] : {{ __t("Signature") }}<br>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __t("Attachments") }}</label>
                    <button type="file" ngf-select="uploadFiles($file, $invalidFiles)" class="btn btn-success btn-xs"
                            {{-- accept="image/*" ngf-max-height="1000" ngf-max-size="1MB"--}}>
                        {{ __t("Add") }}
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
                            <th>{{ __t("File") }}</th>
                            <th>{{ __t("Path") }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr ng-repeat="attachment in form.attachments">
                            <td>@{{ attachment.name }}</td>
                            <td>@{{ attachment.path }}</td>
                            <td class="text-right"><button type="button" class="btn btn-xs btn-danger" ng-click="deleteFile(attachment.path)"><i class="fa fa-trash"></i> {{ __t("Delete") }}</button></td>
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
                        {{ __t("For quotes") }}
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
                        {{ __t("For orders") }}
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
                        {{ __t("For invoices") }}
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
                        {{ __t("For delivery notes") }}
                    </label>
                </div>
            </div>
        </div>


        <form-buttons></form-buttons>

    </form>
</div>