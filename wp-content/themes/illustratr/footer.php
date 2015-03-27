<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Illustratr
 */
?>

</div><!-- #content -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-area clear">
        <?php
        if (has_nav_menu('social')) {
            wp_nav_menu(array(
                'theme_location' => 'social',
                'container_class' => 'menu-social',
                'menu_class' => 'clear',
                'link_before' => '<span class="screen-reader-text">',
                'link_after' => '</span>',
                'depth' => 1,
            ));
        }
        ?>
        <div class="site-info">
                <?php
               /*if(is_front_page()){*/

                ?>

                <div class="socio-icons">
                    <div class="socio-wrap">
                        <a  href="">
                            <!--<div class="socio-facebook"></div>-->
                            <svg class="socio-facebook svg" viewBox="0 0 22 22">
                                <use class="socio-facebook" xlink:href="<?php echo get_site_url().'/wp-content/themes/illustratr/img/facebook.svg#facebook-icon' ?>"></use>
                            </svg>
                             
                        </a>
                        <a href="">
                           <!-- <div class="socio-vimeo"></div>-->
                            <svg class="socio-vimeo svg" viewBox="0 0 22 22">
                                <use xlink:href="<?php echo get_site_url().'/wp-content/themes/illustratr/img/vimeo.svg#socio-vimeo' ?>"></use>
                            </svg>
                        </a>
                        <a href="">
                            <!--<div class="socio-bronx"></div> -->
                            <svg class="socio-bronx svg" viewBox="0 0 22 22">
                                <use xlink:href="<?php echo get_site_url().'/wp-content/themes/illustratr/img/etoile.svg#socio-bronx' ?>">

                                </use>

                            </svg>
                        </a>
                        <a  href="">
                            <!--<div class="socio-insta"></div> -->
                            <svg class="socio-insta svg" viewBox="0 0 22 22">
                                <use xlink:href="<?php echo get_site_url().'/wp-content/themes/illustratr/img/instagram.svg#socio-insta' ?>"></use>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php/* }else'';*/?>

        </div>
        <!-- .site-info -->
    </div>
    <!-- .footer-area -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>