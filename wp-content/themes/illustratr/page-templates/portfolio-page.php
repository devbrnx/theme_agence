<?php
/**
 * Template Name: Portfolio Page Template
 *
 * @package illustratr
 */

get_header(); ?>

    <div id="primary" class="content-area">

        <main id="main" class="site-main" role="main">

            <?php if (!get_theme_mod('illustratr_hide_portfolio_page_content')) : ?>
                <?php while (have_posts()) : the_post(); ?>

                    <?php if ('' != get_the_post_thumbnail()) : ?>
                        <div class="entry-thumbnail">
                            <?php the_post_thumbnail('illustratr-featured-image'); ?>
                        </div><!-- .entry-thumbnail -->
                    <?php endif; ?>

                    <?php /*the_title( '<header class="page-header"><h1 class="page-title">', '</h1></header>' ); */ ?>

                    <div class="page-content">
                        <?php
                        the_content();
                        wp_link_pages(array(
                            'before' => '<div class="page-links"><span class="page-links-title">' . __('Pages:', 'illustratr') . '</span>',
                            'after' => '</div>',
                            'link_before' => '<span>',
                            'link_after' => '</span>',
                        ));
                        ?>
                    </div><!-- .page-content -->

                    <?php edit_post_link(__('Edit', 'illustratr'), '<div class="entry-meta"><span class="edit-link">', '</span></div>'); ?>

                <?php endwhile; // end of the loop. ?>
            <?php endif; ?>

            <?php
            if (get_query_var('paged')) :
                $paged = get_query_var('paged');
            elseif (get_query_var('page')) :
                $paged = get_query_var('page');
            else :
                $paged = 1;
            endif;

            $posts_per_page = get_option('jetpack_portfolio_posts_per_page', '10');
            $args = array(
                'post_type' => 'jetpack-portfolio',
                'posts_per_page' => $posts_per_page,
                'paged' => $paged,
            );
            $project_query = new WP_Query ($args);
            if (post_type_exists('jetpack-portfolio') && $project_query->have_posts()) :
                ?>
                <div class="wrap-content">
                    <div class="filter-menu" >
                        <ul class="list-jetpack-taxonomies" data-filter-group="type">

                            <li id="filter--all" class="filter active" data-filter=""><?php _e( 'Tous types', 'wds_portfolio' ) ?></li>
                            <?php
                            // list terms in a given taxonomy
                            $taxonomy = 'jetpack-portfolio-type';
                            $arguments = array(
                                'order'=>'asc',
                                'orderby'=>'id'
                            );
                            $tax_terms = get_terms( $taxonomy, $arguments );

                            foreach ( $tax_terms as $tax_term ) {
                                echo '<li class="filter" data-filter=".'. $tax_term->slug.'">' . $tax_term->name .'</li>';
                            }
                            ?>
                        </ul>

                        <span class="separator">---</span>
                        <!--Get jetpack-tag taxonomy-->
                        <ul class="list-jetpack-taxonomies" data-filter-group="tags">

                            <li id="filter--all" class="filter active" data-filter=""><?php _e( 'Tous les tags', 'wds_portfolio' ) ?></li>
                            <?php
                            // list terms in a given taxonomy
                            $taxonomy = 'jetpack-portfolio-tag';
                            $arguments = array(
                                'order'=>'asc',
                                'orderby'=>'id'
                            );
                            $tax_terms = get_terms( $taxonomy, $arguments );

                            foreach ( $tax_terms as $tax_term ) {
                                echo '<li class="filter" data-filter=".'. $tax_term->slug.'">' . $tax_term->name .'</li>';
                            }
                            ?>
                        </ul>

                    </div>
                    <div class="portfolio-wrapper">
                        <div id="message-box">
                            <p>Aucun résultat ne correspond à vos critères...</p>
                        </div>

                        <?php /* Start the Loop */ ?>
                        <?php while ($project_query->have_posts()) : $project_query->the_post(); ?>

                            <?php get_template_part('content', 'portfolio'); ?>

                        <?php endwhile; ?>

                    </div><!-- .portfolio-wrapper -->
                    <div class="clear"></div>
                </div><!-- content-wrapper -->

                <?php
                illustratr_paging_nav($project_query->max_num_pages);
                wp_reset_postdata();
                ?>

            <?php else : ?>

                <section class="no-results not-found">
                    <header class="page-header">
                        <h1 class="page-title"><?php _e('Nothing Found', 'illustratr'); ?></h1>
                    </header>
                    <!-- .page-header -->

                    <div class="page-content">
                        <?php if (current_user_can('publish_posts')) : ?>

                            <p><?php printf(__('Ready to publish your first project? <a href="%1$s">Get started here</a>.', 'illustratr'), esc_url(admin_url('post-new.php?post_type=jetpack-portfolio'))); ?></p>

                        <?php else : ?>

                            <p><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'illustratr'); ?></p>
                            <?php get_search_form(); ?>

                        <?php endif; ?>
                    </div>
                    <!-- .page-content -->
                </section><!-- .no-results -->

            <?php endif; ?>

        </main>
        <!-- #main -->
    </div><!-- #primary -->


<?php /*get_sidebar(); */ ?>
<?php get_footer(); ?>