<!DOCTYPE html>
<html lang="en">

@include('frontend.layout.header')

<body class="inner-page faq">

    @include('frontend.layout.menu')

    @include('frontend.layout.preloader')

    <!--page heading-->
    <div class="container page-wrapper pages-wrapper faq">

        @if($content->page_show_title == 'yes')
        <div class="pages-header">
            <h4>{{ $content->page_title ?? '' }}</h4>
        </div>
        @endif


        <!--faq container-->
        <div class="pages-container">

            {!! $content->page_content ?? '' !!}

        </div>
    </div>

    @include('frontend.layout.footer')

    @include('frontend.layout.footerjs')
</body>

</html>