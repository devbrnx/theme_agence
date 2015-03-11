(function ($) {

    /*
     * Resize portfolio-wrapper for full width on small screens.
     */
    function calc() {

        $('.portfolio-wrapper').each(function () {
            if ($(window).width() < 768) {
                $(this).css('width', '100%').css('width', '+=40px');
                if ($('body').hasClass('rtl')) {
                    $(this).css('margin-right', '-20px');
                } else {
                    $(this).css('margin-left', '-20px');
                }
            } else if ($(window).width() < 960) {
                $(this).css('width', '100%').css('width', '+=120px');
                if ($('body').hasClass('rtl')) {
                    $(this).css('margin-right', '-60px');
                } else {
                    $(this).css('margin-left', '-60px');
                }
            } else {
                $(this).css({
                    'width': '',
                    'margin-right': '',
                    'margin-left': ''
                });
            }
        });

    }

    $(window).load(function () {

        /*
         * Wrap portfolio-featured-image in a div.
         */
        $('.portfolio-featured-image').each(function () {
            $(this).wrap('<div class="portfolio-thumbnail" />');
        });

        calc();

         

        $(window).resize(function () {

            // Force layout correction after 1500 milliseconds
            setTimeout(function () {
                calc();
                portfolio_wrapper.masonry();
            }, 1500);

        });

         

    });

})(jQuery);
