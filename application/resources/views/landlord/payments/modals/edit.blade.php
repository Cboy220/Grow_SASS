<div class="row">
    <div class="col-lg-12">


        <!--payment_amount-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.amount')*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="number" class="form-control form-control-sm" id="payment_amount" name="payment_amount"
                    value="{{ $payment->payment_amount ?? '' }}">
            </div>
        </div>

        <!--date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.date')*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm pickadate" autocomplete="off" name="payment_date"
                    value="{{ runtimeDatepickerDate($payment->payment_date ?? '') }}">
                <input class="mysql-date" type="hidden" name="payment_date" id="payment_date"
                    value="{{ $payment->payment_date ?? '' }}">
            </div>
        </div>


        <!--payment_gateway-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.payment_method')*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm select2-preselected" id="payment_gateway"
                    name="payment_gateway" data-preselected="{{ $payment->payment_gateway ?? ''}}">
                    <option></option>
                    <option value="bank">@lang('lang.bank')</option>
                    <option value="stripe">Stripe</option>
                    <option value="paypal">Paypal</option>
                    <option value="paystack">Paystack</option>
                </select>
            </div>
        </div>


        <!--payment_transaction_id-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.transaction_id')</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="payment_transaction_id"
                    name="payment_transaction_id" value="{{ $payment->payment_transaction_id ?? '' }}">
            </div>
        </div>

        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>