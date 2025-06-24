<!--first_name-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.first_name')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="first_name" name="first_name"
            value="{{ $user->first_name ?? '' }}">
    </div>
</div>

<!--last_name-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.last_name')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="last_name" name="last_name"
            value="{{ $user->last_name ?? '' }}">
    </div>
</div>


<!--email-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.last_name')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="email" name="email"
            value="{{ $user->email ?? '' }}">
    </div>
</div>

<div class="line"></div>

<!--password-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.password') (@lang('lang.optional'))</label>
    <div class="col-sm-12">
        <input type="password" class="form-control form-control-sm" id="password" name="password"
            value="">
    </div>
</div>