<?php
/**
 * The template used for displaying projects on project page
 * Filtering capability added
 * @package Illustratr
 */


?>

<?php
// get Jetpack Portfolio taxonomy terms for portfolio filtering
$terms = get_the_terms($post->ID, 'jetpack-portfolio-type');
if ($terms && !is_wp_error($terms)) :

    $filtering_links = array();

    foreach ($terms as $term) {
        $filtering_links[] = $term->slug;
    }
    //$filtering = join(" ", $filtering_links);

    //Add tags inside ID
    $tags = get_the_terms($post->ID, 'jetpack-portfolio-tag');

        $tags_links = array();

        foreach ($tags as $tag) {
            $tags_links[] = $tag->slug;
        }
        //$tags_filtered = join(" ", $tags_links);
        $merge= array_merge($filtering_links,$tags_links);
        $classmerged = join(" ",$merge);

    ?>

    <article id="post-<?php the_ID(); ?>"  <?php post_class($merge);?>>
        <div class="portfolio-thumbnail">
            <a href="<?php the_permalink(); ?>" rel="bookmark" class="image-link" tabindex="-1">
                <?php if ('' != get_the_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('illustratr-portfolio-featured-image'); ?>
                <?php endif; ?>
            </a>
        </div>
    </article><!-- #post-## -->

<?php
endif;