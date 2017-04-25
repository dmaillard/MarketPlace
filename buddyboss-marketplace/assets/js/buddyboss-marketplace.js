/**
 * MarketPlace
 *
 *
 * This file should load in the footer
 *
 * @author      BuddyBoss
 * @since       MarketPlace (1.0.0)
 * @package     MarketPlace
 *
 * ====================================================================
 *
 * 1. Main BM Functionality
 */
/*!
 * hoverIntent v1.8.0 // 2014.06.29 // jQuery v1.9.1+
 * http://cherne.net/brian/resources/jquery.hoverIntent.html
 *
 * You may use hoverIntent under the terms of the MIT license. Basically that
 * means you are free to use hoverIntent as long as this header is left intact.
 * Copyright 2007, 2014 Brian Cherne
 */
(function($){$.fn.hoverIntent=function(handlerIn,handlerOut,selector){var cfg={interval:100,sensitivity:6,timeout:0};if(typeof handlerIn==="object"){cfg=$.extend(cfg,handlerIn)}else{if($.isFunction(handlerOut)){cfg=$.extend(cfg,{over:handlerIn,out:handlerOut,selector:selector})}else{cfg=$.extend(cfg,{over:handlerIn,out:handlerIn,selector:handlerOut})}}var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if(Math.sqrt((pX-cX)*(pX-cX)+(pY-cY)*(pY-cY))<cfg.sensitivity){$(ob).off("mousemove.hoverIntent",track);ob.hoverIntent_s=true;return cfg.over.apply(ob,[ev])}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=false;return cfg.out.apply(ob,[ev])};var handleHover=function(e){var ev=$.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t)}if(e.type==="mouseenter"){pX=ev.pageX;pY=ev.pageY;$(ob).on("mousemove.hoverIntent",track);if(!ob.hoverIntent_s){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}}else{$(ob).off("mousemove.hoverIntent",track);if(ob.hoverIntent_s){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob)},cfg.timeout)}}};return this.on({"mouseenter.hoverIntent":handleHover,"mouseleave.hoverIntent":handleHover},cfg.selector)}})(jQuery);

/**
 * 1. Main BM Functionality
 * ====================================================================
 */


;(function($){

    /** Input Tips **/
    $('.wcv-form p.tip').each(function(){
        var $icon = $('<i/>', {
            'class': 'fa fa-info-circle'
        }),
            $this = $(this);
        $this.parents('.control-group').css({
            position: 'relative'
        });
        $icon.hover(
            function() {
                $this.css({
                    opacity: 1,
                    visibility: 'visible'
                });
            }, function() {
                $this.css({
                    opacity: 0,
                    visibility: 'hidden'
                });
            }
        );
        $this.parents('.control-group').find('label').after($icon);
    });

    /** Responsive Tables **/
    $('.wcvendors-table-shop_coupon').each(function(){
        var $this = $(this),
            array = [],
            jj = 0;

        $this.find('th').each(function(){
            array.push($(this).text());
        });

        $this.find('td').each(function(){
            $(this).attr( "data-th", array[jj] );
            jj++;
            if( jj == 7 ) {
                jj = 0;
            }
        });
    });

    /** Responsive Tables **/
    $('.wcvendors-table-order').each(function(){
        var $this = $(this),
            array = [],
            jj = 0;

        $('.wcvendors-table-order > thead > tr > th').each(function(){
            array.push($(this).text());
        });

        $('.wcvendors-table-order > tbody > tr > td').each(function(){
            $(this).attr( "data-th", array[jj] );
            jj++;
            if( jj == 6 ) {
                jj = 0;
            }
        });
    });

    /** Responsive Tables **/
    $('table.wcv-order-table').each(function(){
        var $this = $(this),
            array = [],
            jj = 0;

        $this.find('> thead > tr > th').each(function(){
            array.push($(this).text());
        });

        $this.find('> tbody#order_line_items > tr > td:not(:first-child)').each(function( index, val ){

            $(this).attr( "data-th", array[jj] );
            jj++;

            if( jj == 5 ) {
                jj = 0;
            }
        });
    });

    /** Hide "hidden" inputs **/
//    $('.control-group input[type=hidden]').each(function(){
//        if( $(this).parents('#linked_product').length == 0 ){
//            $(this).parents('.control-group').hide();
//        }
//    });

    $('.woocommerce-pagination .current').parent('li').addClass('current');

    /** Shop Filter **/
    $('.filter-dropdown').find('select').change(function(){
        $(this).parents('.buddyboss-select-inner').addClass('loading');
        $(this).closest('form').submit();
    });

    /** Image Uploader **/
    $( document ).click( function ( e ) {
        if($(e.target).hasClass('media-button-select')) {
            var $holder = $('.file-upload-wrap:not(.icon) .wcv-file-uploader');

            var $img = $holder.find('img'),
                src = $img.attr('src'),
                regex = /-\d+(\.\d+)?x\d+(\.\d+)?\./;

            if(src)
            $img.attr( 'src', src.replace(regex, ".") );
        }
    });

    /** Product Carousel **/
    $('.product-main-area .images > a img').click(function(e){
        e.preventDefault();
    });

    $('.product-main-area .thumbnails a').click(function(e){
        e.preventDefault();
        var $this = $(this),
            src = $this.data('ref');
            srcset = $this.find('img').attr('srcset'),
            $main = $this.closest('.images').children('.main-product-image').find('img'),
            regex = /-\d+(\.\d+)?x\d+(\.\d+)?\./;


        $main.css({
            'opacity' : 0
        });

        var downloadingImage = new Image();

        downloadingImage.onload = function(){
            $main.attr('src', src);

            var attr = $main.attr('srcset');

            if(typeof srcset !== 'undefined' && regex.test(src) && typeof attr !== typeof undefined && attr !== false){
                $main.attr('srcset', srcset);
            } else {
                $main.removeAttr('srcset');
            }

            $main.closest('a').attr('href', src.replace(regex, "."));

            $main.css({
                'opacity' : 1
            });
        };

        downloadingImage.src = src;

    });

    // Favorite Products
    $('body').on('click', 'a.bm-product-to-favorites', function(e){
        e.preventDefault();
        var $this = $(this),
            data = $this.data('id');

        $this.removeClass('added');
        $this.addClass('loading');
        $.ajax({
            url: bmVars.ajaxurl,
            type: 'post',
            data: {
                action: 'product_to_favorites',
                product_id: data
            },
            success: function (html) {
                $this.removeClass('loading');
                if(!$this.hasClass('favorited')) {
                    $this.addClass('added');
                    $this.addClass('favorited');
                    $this.attr('data-tooltip', bmVars.added_to_favorites);
                } else {
                    $this.removeClass('favorited');
                    $this.attr('data-tooltip', bmVars.add_to_favorites);
                }

            }
        });
    });

    // Favorite Shops
    $('a.bm-add-to-favs').click(function(e){
        e.preventDefault();
        var $this = $(this),
            data = $this.data('id');

        $this.addClass('loading');
        $.ajax({
            url: bmVars.ajaxurl,
            type: 'post',
            data: {
                action: 'shop_to_favorites',
                vendor_id: data
            },
            success: function (html) {
                $this.removeClass('loading');
                if(!$this.hasClass('favorited')) {
                    $this.addClass('favorited');
                    if($this.hasClass('boss-tooltip')) {
                        $this.attr('data-tooltip', bmVars.added_to_favorites);
                    } else {
                        $this.text(bmVars.added_to_favorites);
                    }
                } else {
                    $this.removeClass('favorited');
                    if($this.hasClass('boss-tooltip')) {
                        $this.attr('data-tooltip', bmVars.add_to_favorites);
                    } else {
                        $this.text(bmVars.add_to_favorites);
                    }
                }
            }
        });
    });

    $('.bm-vc-header h3').each(function(){
        var width = $(this).width();
        $(this).next('div').css({
            'left' : width + 54,
            'right' : 'inherit',
        });
    });

    /** Mobile Subheader **/

    $('#sub-trigger').click(function(e){
        $('.subheader .header-wrapper').slideToggle(300);
    });

    var linkClick = $('nav.subheader .menu > li > b, nav.subheader .nav.menu > li > a');
    linkClick.append('<i></i>');

    var clickable = linkClick.find('i');

    function mobile_submenu() {
        var menu = $('nav.subheader .menu');

        clickable.click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            //if($(this).data( "events").click.length === 1) {
                if (!$(this).parent().hasClass('open')) {
                    $('nav.subheader .menu > li > .sub-menu').slideUp(300);
                    $('nav.subheader .menu > li > b, nav.subheader .nav.menu > li > a').removeClass('open');
                    //toggleClass('fa-chevron-right').toggleClass('fa-chevron-down')
                    $(this).parent().toggleClass('open');
                    $(this).closest('li').children('.sub-menu').slideDown();
                } else {
                    $(this).parent().toggleClass('open');
                    $(this).closest('li').children('.sub-menu').slideUp();
                }
            //}
        });
    }

    mobile_submenu();

    $(window).resize(function(){
        clickable.unbind('click');
        mobile_submenu();
        if($('body').hasClass('is-desktop')){
            clickable.unbind('click');
        }
    });

    $(document).one( 'click', 'a.tabs-tab.shipping', function() {
        var $link = $(".wcv_shipping_rates a.insert");
        //$link.closest('.wcv_shipping_rates').find('tbody').append( $link.data( 'row' ) );
        $link.click();
    });

    setTimeout(function(){
        $('body:not(.buddypress).bm-store-index #content article.store-item').css({
            'opacity': 1
        });
    }, 100);

    // Menu hover
    var $current = $('.sub-menu .product-categories > li.current');

    if($current.length > 0){
        $current.parent('ul.product-categories').find('li.hovered').removeClass('hovered');
        $current.addClass('hovered');
    }

    /* Product Gallery Nav Slider */
    setTimeout(function() {

        var $slider = $( '.flex-control-thumbs' );
        
        $slider.slick({
            dots: false,
            infinite: false,
            slide: 'li',
            prevArrow: '<a class="slidePrev slideNav bb-icon-chevron-left"></a>',
            nextArrow: '<a class="slideNext slideNav bb-icon-chevron-right"></a>',
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: false,
            speed: 500,
        } );

        // On swipe event
        $('.woocommerce-product-gallery a.slideNav').on('click touchend MSPointerUp keyup', function(e) {
           e.stopPropagation();
        });

    }, 1000);


    /* This is what will happen when you hover a product thumb */

    // $( '.bm-thumbnails-wrap[data-hover-effect="true"] .slick-list a' ).on('mouseenter', function(){
    //     $(this).trigger('click');
    // });

    var $hovered = $('.sub-menu .product-categories > li.hovered');

    $('.sub-menu .product-categories > li').hover(
        function() {
            if(!($(this).get(0) === $hovered.get(0) )) {

                $('.sub-menu .product-categories > li').removeClass('hovered');
                $(this).addClass('hovered');
            }
        },
        function () {
            if(!($(this).get(0) === $hovered.get(0) )) {
                $(this).removeClass('hovered');
                $hovered.addClass('hovered');
            }
        }
    );


    $('.product-categories > li > .children li, .menu.nav > li > .sub-menu > li').each(function(index, el) {
        var $li = $(this),
            submenus = $(this).children('ul');
        if(submenus.length) {
            var trigger = $('<i/>', {
                'class': 'fa fa-chevron-down',
            });

            trigger.appendTo($(this).children('a'));
            trigger.click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $all_li = $li.parent().children('li').filter(function( index ) {
                    return $(this).get(0) != $li.get(0);
                });

                $all_li.children('ul').slideUp(200);
                $all_li.children('i').removeClass('open');
                $all_li.removeClass('open');

                $(this).parent().next().slideToggle(200);
                $(this).toggleClass('open');
                $li.toggleClass('open');
            });
        }
    });


    var $ths = $('.wcvendors-table-rating th');

    $('.wcvendors-table-rating tbody tr').each(function(){
        var jj = 0;
        $(this).find('td').each(function(){
            var label = $($ths[jj]).text();
            $(this).attr( "data-th", label );
            jj++;
        });
    });

    $(".is-desktop nav.subheader .menu > li").hoverIntent(
        function(){
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );

    /**
     * Dashboard Dropdown
     */
    var $nav = $('nav.wcv-navigation'),
        $ul = $nav.find('ul'),
        $li = $ul.find('li');

    $span = $('<div/>', {
    });

    $nav.prepend($span);

    $span.click( function ( e ) {
        e.stopPropagation();
        $ul.slideToggle();
    } );

    function cloneText() {
        $li.each( function () {
            if ( $ul.find( '.active' ).length == 1 ) {
                if ( $( this ).hasClass( 'active' ) ) {
                    $span.text( $( this ).text() );
                }
            } else {
                $span.text( $li.first().text() );
            }
        } );
    }

    cloneText();

    $li.click( function () {
        $span.text( $( this ).text() );
    } );

    $('.product-vendor .send-message, .show-owner-widget .send-message').click(function(){
        var redirect = $(this).data('next');
        $.cookie("login_redirect", redirect, {path: '/'});
    });

    // login redirect
    $( document ).ajaxSuccess(function( event, xhr, settings ) {
        if ( settings.url == "?ajax-register" && xhr.status == 200 && settings.data && settings.data.indexOf('as_vendor=true') > -1) {
            $.cookie("login_redirect", 'vendor', {path: '/'});
        }
        // variation selectboxes
        setTimeout(function () {
            Selects.init_select( false, true );
            Selects.populate_select_label( false );
        }, 200);
    });

    // disable some buttons
    $('.loop-product-image .product-buttons a.product_type_simple:not(.add_to_cart_button), .woocommerce ul.products li.type-product .product-item-buttons a.product_type_simple:not(.add_to_cart_button)').click(function(event) {
        event.preventDefault();
    });
})(jQuery);

(function( $ ) {
    'use strict';

    /**
     * Code required to create the charts
     */

    $( window ).load(function() {

        // Only run on dashboard page
        if ( typeof orders_chart_labelBM !== 'undefined' ) {
            var orderdata = {
                labels: orders_chart_labelBM,
                datasets: [
                    {
                        label: "My First dataset",
                        fillColor: "rgba(65,176,216,0.5)",
                        strokeColor: "rgba(116,196,223,1)",
                        highlightFill: "rgba(65,176,216,0.75)",
                        highlightStroke: "rgba(79,181,219,1)",
                        data: orders_chart_data,
                    }
                ]
            };

            var orders_chart_canvas = document.getElementById( "orders_chart" ).getContext( "2d" );
            var ordersBarChart = new Chart( orders_chart_canvas ).Bar( orderdata, { responsive : true } );

        }

        // Only run on dashboard page
        if ( typeof pieDataBM !== 'undefined' ) {


            var red = "#bf616a",
                blue = "#5B90BF",
                orange = "#d08770",
                yellow = "#ebcb8b",
                green = "#a3be8c",
                teal = "#96b5b4",
                pale_blue = "#8fa1b3",
                purple = "#b48ead",
                brown = "#ab7967";


            var data = [],
                barsCount = 50,
                labels = new Array(barsCount),
                updateDelayMax = 500,
                $id = function(id){
                    return document.getElementById(id);
                },
                random = function(max){ return Math.round(Math.random()*100)},
                helpers = Chart.helpers;


            Chart.defaults.global.responsive = true;

            Chart.defaults.global.customTooltips = function(tooltip) {

                // Tooltip Element
                var tooltipEl = $('#chartjs-tooltip');

                // Hide if no tooltip
                if (!tooltip) {
                    tooltipEl.css({
                        opacity: 0
                    });
                    return;
                }

                // Set caret Position
                tooltipEl.removeClass('above below');
                tooltipEl.addClass(tooltip.yAlign);

                var parts = tooltip.text.split(":");
                var innerHtml = '<h3>' + parts[0].trim() + '</h3><b>' + bmVars.currency_symbol + parts[1].trim() + '</b>';
                tooltipEl.html(innerHtml);
                // Set Text
                //tooltipEl.html(tooltip.text);

                // Find Y Location on page
                var top;
                if (tooltip.yAlign == 'above') {
                    top = tooltip.y - tooltip.caretHeight - tooltip.caretPadding;
                } else {
                    top = tooltip.y + tooltip.caretHeight + tooltip.caretPadding;
                }

                tooltipEl.css({
                    opacity: 1,
                });
            };

            var canvas = document.getElementById( "products_chart" ),
                    colours = {
                        "Core": blue,
                        "Line": orange,
                        "Bar": teal,
                        "Polar Area": purple,
                        "Radar": brown,
                        "Doughnut": green
                    };

                var moduleData = pieDataBM;
                //
                var moduleDoughnut = new Chart(canvas.getContext('2d')).Doughnut(moduleData, {
                    //tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
                    //showTooltips: false,
                    //animation: false,
                    //tooltipTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li id=\"<%if(segments[i].id){%><%=segments[i].id%><%}%>\"><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
                });
                //
        }

    });

})( jQuery );

/**
 * plugins/wcvendors-pro-master/public/assets/js/src/tags.js line #65:
 * tokenSeparators: [",", " "],
 *
 * This is problematic, for users can't add a tag which is made up of 2 or more words, since a whitespace is considered as a separator.
 * Initializing tags ui with only ',' as the token separator to fix it.
 * Add the css class of 'enhanced' so that tags ui is not applied again, by wc-vendors-pro
 *
 * Tags ui in wc-vendors-pro
 */
( function( $) {

    if( typeof wcv_tag_search_params == 'undefined' )
        return false;

    function getEnhancedSelectFormatString() {
        var formatString = {
          formatMatches: function( matches ) {
            if ( 1 === matches ) {
              return wcv_tag_search_params.i18n_matches_1;
            }

            return wcv_tag_search_params.i18n_matches_n.replace( '%qty%', matches );
          },
          formatNoMatches: function() {
            return wcv_tag_search_params.i18n_no_matches;
          },
          formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
            return wcv_tag_search_params.i18n_ajax_error;
          },
          formatInputTooShort: function( input, min ) {
            var number = min - input.length;

            if ( 1 === number ) {
              return wcv_tag_search_params.i18n_input_too_short_1;
            }

            return wcv_tag_search_params.i18n_input_too_short_n.replace( '%qty%', number );
          },
          formatInputTooLong: function( input, max ) {
            var number = input.length - max;

            if ( 1 === number ) {
              return wcv_tag_search_params.i18n_input_too_long_1;
            }

            return wcv_tag_search_params.i18n_input_too_long_n.replace( '%qty%', number );
          },
          formatSelectionTooBig: function( limit ) {
            if ( 1 === limit ) {
              return wcv_tag_search_params.i18n_selection_too_long_1;
            }

            return wcv_tag_search_params.i18n_selection_too_long_n.replace( '%qty%', limit );
          },
          formatLoadMore: function( pageNumber ) {
            return wcv_tag_search_params.i18n_load_more;
          },
          formatSearching: function() {
            return wcv_tag_search_params.i18n_searching;
          }
        };

        return formatString;
    }


    $( 'body' )
    .on( 'wcv-search-tag-init', function() {

        // Ajax product tag search box
        $( ':input.wcv-tag-search' ).filter( ':not(.enhanced)' ).each( function() {
          var select2_args = {
            allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
            placeholder: $( this ).data( 'placeholder' ),
            tags:        $( this ).data( 'tags' ),
            tokenSeparators: [","],
            minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '2',
            escapeMarkup: function( m ) {
              return m;
            },
            createSearchChoice: function( term, data ) {
              if ($(data).filter( function() {
                return this.text.localeCompare( term ) === 0;
              }).length === 0) {
                return {
                  id: term,
                  text: term
                };
              }
            },
            ajax: {
                  url:         wcv_tag_search_params.ajax_url,
                  dataType:    'json',
                  quietMillis: 250,
                  data: function( term, page ) {
                      return {
                  term:     term,
                  action:   $( this ).data( 'action' ) || 'wcv_json_search_tags',
                  security: wcv_tag_search_params.nonce
                      };
                  },
                  results: function( data, page ) {
                    var terms = [];
                    if ( data ) {
                  $.each( data, function( id, text ) {
                    terms.push( { id: id, text: text } );
                  });
                }
                      return { results: terms };
                  },
                  cache: true
              }
          };

          if ( $( this ).data( 'multiple' ) === true ) {
            select2_args.multiple = true;
            select2_args.initSelection = function( element, callback ) {
              var data     = $.parseJSON( element.attr( 'data-selected' ) );
              var selected = [];

              $( element.val().split( "," ) ).each( function( i, val ) {
                selected.push( { id: val, text: data[ val ] } );
              });
              return callback( selected );
            };
            select2_args.formatSelection = function( data ) {
              return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
            };
          } else {
            select2_args.multiple = false;
            select2_args.initSelection = function( element, callback ) {
              var data = {id: element.val(), text: element.attr( 'data-selected' )};
              return callback( data );
            };
          }

          select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

          $( this ).select2( select2_args ).addClass( 'enhanced' );
        });

      })

      .trigger( 'wcv-search-tag-init' );

})( jQuery );

(function( $ ) {
    'use strict';

    function viewport() {
        var e = window, a = 'inner';
        if ( !( 'innerWidth' in window ) ) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return { width: e[ a + 'Width' ], height: e[ a + 'Height' ] };
    }
    // Visual Composer rtl hack
    $("document").ready(function() {
        function visual_composer_rtl() {
            $('body.rtl .vc_row[data-vc-full-width]').each(function(){
                var $row = $(this);
                $row.css({
                   'right': -(viewport().width - $('#content').width() - 30 -16 )/2
                });
            });
        }
        visual_composer_rtl();
        $(window).resize(visual_composer_rtl);
    });
})( jQuery );
