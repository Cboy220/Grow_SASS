"use strict";

$(document).ready(function () {

    //top menu - sticky (on page load)
    if ($(window).scrollTop() > 0) {
        if ($('body').hasClass('home-page')) {
            $("#frontend-top-menu").addClass('sticky-menu');
        }
    } else {
        if ($('body').hasClass('home-page')) {
            $("#frontend-top-menu").removeClass('sticky-menu');
        }
    }


    //inner pages
    //top menu - sticky (on page load)
    if ($('body').hasClass('inner-page')) {
        $("#frontend-top-menu").addClass('sticky-menu');
    }

    //remove preloader
    $(".preloader").fadeOut('slow');


    //top menu - sticky (on scrolling)
    $(window).scroll(function () {
        if ($(window).scrollTop() > 0) {
            //for hoe page only
            if ($('body').hasClass('home-page')) {
                if (!$('#frontend-top-menu').hasClass('sticky-menu')) {
                    $("#frontend-top-menu").addClass('sticky-menu');
                }
            }
        } else {
            if ($('body').hasClass('home-page')) {
                $("#frontend-top-menu").removeClass('sticky-menu');
            }
        }
    });

    $(document).on('click', '#mobile-menu-icon', function () {
        $("#mobile-menu").toggle();
    });



    //FAQ TOGGLE
    $(document).on('click', '.each-faq', function () {
        var target = $(this).attr('data-target');
        $(".each-faq").removeClass('active');
        $(this).addClass('active');
        $(".faq-content").hide();
        $("#" + target).fadeIn('slow');
    });

    //TOGGLE PRICE TABLE
    $(document).on('change', '#price-cycle-switch', function () {
        $(".pricing-toggle-period").removeClass('active');
        $(".pricing-table-wrapper").hide()

        if ($(this).is(':checked')) {
            $("#pricing-toggle-yearly").addClass('active');
            $("#pricing-tables-yearly").show()
        } else {
            $("#pricing-toggle-monthly").addClass('active');
            $("#pricing-tables-monthly").show()
        }
    });

});