<div class="row">
    <div class="col-lg-12">

        <!--package-->
        <div class="form-group row">
            <label
                class="col-sm-12 text-left control-label col-form-label required">@lang('lang.package')</label>
            <div class="col-sm-12">
                <select class="select2-basic form-control form-control-sm" id="package_id" name="package_id">
                    @foreach($packages as $package)
                    <option value="{{ $package->package_id }}" title="{{ $package->package_subscription_options }}">
                        {{ $package->package_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
</div>