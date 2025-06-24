<!DOCTYPE html>
<html lang="en">

@include('frontend.layout.header')

<body class="inner-page contact">

    @include('frontend.layout.menu')

    @include('frontend.layout.preloader')


    <!--page heading-->
    <div class="container page-wrapper">


        <!--contact form-->
        <div class="row" id="contact-us-form">
            <div class="col-6">

                <div class="contact-details">

                    <h3>{{ $content->frontend_data_1 }}</h3>

                    <div class="">
                        {!! _clean($content->frontend_data_2) !!}
                    </div>

                </div>

            </div>
            <div class="col-6">

                <div class="contact-form" id="contact-form">
                    <!--contact_name-->
                    <div class="form-group row">
                        <label
                            class="col-sm-12 col-12 text-left control-label col-form-label">@lang('lang.name')</label>
                        <div class="col-sm-12 col-6">
                            <input type="text" class="form-control form-control-sm" id="contact_name"
                                name="contact_name" value="">
                        </div>
                    </div>

                    <!--contact_name-->
                    <div class="form-group row">
                        <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.email')</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control form-control-sm" id="contact_email"
                                name="contact_email" value="">
                        </div>
                    </div>

                    <!--contact_message-->
                    <div class="form-group row">
                        <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.message')</label>
                        <div class="col-sm-12">
                            <textarea class="form-control form-control-sm" rows="8" name="contact_message"
                                id="contact_message"></textarea>
                        </div>
                    </div>

                    <!--form buttons-->
                    <div class="text-right p-t-30">
                        <button type="submit" id="submitButton" class="site_btn waves-effect text-left ajax-request"
                            data-url="{{ url('contact') }}" data-type="form" data-form-id="contact-form"
                            data-ajax-type="post" data-loading-target="main-body"
                            data-on-start-submit-button="disable">{{ $content->frontend_data_4 }}</button>
                    </div>

                </div>
            </div>
        </div>




        <!--thank you-->
        <div class="row hidden" id="contact-us-thank-you">
            <div class="col-12">{!! _clean($content->frontend_data_5) !!}</div>
        </div>
    </div>

    @include('frontend.layout.footer')

    @include('frontend.layout.footerjs')
</body>

</html>