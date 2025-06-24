<div class="splash-image" id="updatePasswordSplash">
    <img src="{{ url('/') }}/public/images/upload-logo.png" alt="update logo" />
</div>

<!--fileupload-->
<div class="form-group row">
    <div class="col-12">
        <div class="dropzone dz-clickable text-center file-upload-box" id="fileupload_landlord_logo">
            <div class="dz-default dz-message">
                <div>
                    <h4>{{ cleanLang(__('lang.drag_drop_file')) }}</h4>
                </div>
                @if(request('logo_size') == 'logo-large')
                <div class="p-t-10"><small>{{ cleanLang(__('lang.allowed_file_types')) }}: (jpg|png)</small></div>
                <div class=""><small>{{ cleanLang(__('lang.best_image_dimensions')) }}: (185px X 45px)</small></div>
                @endif
                @if(request('logo_size') == 'logo-small')
                <div class="p-t-10"><small>{{ cleanLang(__('lang.allowed_file_types')) }}: (jpg|png)</small></div>
                <div class=""><small>{{ cleanLang(__('lang.best_image_dimensions')) }}: (45px X 45px)</small></div>
                @endif
                @if(request('logo_size') == 'favicon')
                <div class="p-t-10"><small>{{ cleanLang(__('lang.allowed_file_types')) }}: (ico)</small></div>
                <div class=""><small>{{ cleanLang(__('lang.best_image_dimensions')) }}: (16px X 16px)</small></div>
                @endif
            </div>
        </div>
    </div>
</div>


<!--section js resource-->
<span class="hidden" id="js-settings-logos-modal" data-size="{{ request('logo_size') }}">placeholder</span>