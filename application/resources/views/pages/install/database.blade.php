<div class="setup-inner-steps setup-requirements">

    <form class="form-horizontal" id="setupForm" name="setupForm">


        <div class="alert alert-info m-b-25">
            <h5 class="text-info m-b-20"><i class="sl-icon-info"></i> Database Configuration</h5>
            <ul class="p-b-0">
                <li class="m-b-14">The CRM needs to be able to <span class="font-weight-500 text-default">create
                        new databases dynamically</span></li>
                <li class="m-b-14">A new database is created for every new customer account</li>
                <li>Select which method which the CRM will use for creating new databases</li>
            </ul>
        </div>


        <div class="x-title text-center p-t-10 p-b-15">
            <h4 class="text-info">Database Creation Method</h4>
        </div>

        <div class="form-group row align-items-center m-b-40">
            <div class="col-lg-2">
            </div>
            <div class="col-lg-8">
                <select class="select2-basic form-control form-control-sm select2-preselected" id="database_config_type"
                    name="database_config_type" data-placeholder="Select Option">
                    <option></option>
                    <option value="direct">MySQL User - (User must have permission to create databases)</option>
                    <option value="cpanel">Cpanel API - (If your web hosting control panel is Cpanel)</option>
                    <option value="plesk">Plesk API - (If your web hosting control panel is Plesk)</option>
                </select>
            </div>
            <div class="col-lg-2">
            </div>
        </div>
    </form>


    @include('pages.install.api.mysql')
    @include('pages.install.api.cpanel')
    @include('pages.install.api.plesk')



</div>