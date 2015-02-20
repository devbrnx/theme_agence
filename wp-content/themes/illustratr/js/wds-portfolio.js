/**
 * Created by Urien on 12/02/2015.
 */

( function( $ ) {
    $( window ).load( function() {
        // init Isotope
        var $container = $('.portfolio-wrapper').isotope({
            itemSelector: '.jetpack-portfolio'
        });

        // store filter for each group
        var filters = {};

        $('.filter-menu').on( 'click', '.filter', function() {
            var $this = $(this);
            // get group key
            var $buttonGroup = $this.parents('.list-jetpack-taxonomies');
            var filterGroup = $buttonGroup.attr('data-filter-group');
            // set filter for group
            filters[ filterGroup ] = $this.attr('data-filter');
            // combine filters
            var filterValue = '';
            for ( var prop in filters ) {
                    filterValue += filters[ prop ];
            }
            // set filter for Isotope
            $container.isotope({ filter: filterValue });
            var iso = $container.data('isotope');
            // count how many filters are available , if 0 =>hide
            if(! iso.filteredItems.length){
                $("#message-box").show();
                //$('.portfolio-wrapper').css('height','320px');
                $('.portfolio-wrapper').each(function () {
                    if ($(window).width() < 768) {
                        $(this).css('min-height', '320px');

                    } else if ($(window).width() < 960) {
                         $(this).css('min-height', '320px');

                    } else if ($(window).width() < 1025){
                        $(this).css('min-height', '320px');
                    }
                });

            }else{
                $("#message-box").hide();
            }

        });

        // change is-checked class on buttons
        $('.list-jetpack-taxonomies').each( function( i, buttonGroup ) {
            var $buttonGroup = $( buttonGroup );
            $buttonGroup.on( 'click', 'li', function() {
                $buttonGroup.find('.is-checked').removeClass('is-checked');
                $( this ).addClass('is-checked');
            });
        });

    } );


} )( jQuery );
 