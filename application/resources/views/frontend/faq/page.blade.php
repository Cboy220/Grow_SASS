<!DOCTYPE html>
<html lang="en">

@include('frontend.layout.header')

<body class="inner-page faq">

    @include('frontend.layout.menu')

    @include('frontend.layout.preloader')

    <!--page heading-->
    <div class="container page-wrapper faq">

        <div class="page-header faq-header text-center">
            <h2>{!! _clean($content->frontend_data_1) !!}</h2>
            <h5>{!! _clean($content->frontend_data_2) !!}</h5>
        </div>


        <!--faq container-->
        <div class="faq-container">

            @foreach($faqs as $faq)
            <div class="each-faq" data-target="faq_{{ $faq->faq_id }}">
                <div class="faq-title">
                    {{ $faq->faq_title }}
                </div>
                <div class="faq-content hidden" id="faq_{{ $faq->faq_id }}">
                    {!! _clean($faq->faq_content) !!}
                </div>
            </div>
            @endforeach

        </div>
    </div>

    @include('frontend.layout.footer')

    @include('frontend.layout.footerjs')
</body>

</html>