<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Illustratr
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
                            
				<header class="page-header">
                    <?php $uri=$_SERVER['REQUEST_URI'];?>
					<h1 class="page-title"><?php _e( 'La page <span class="false-url">"', 'illustratr' );
                        echo preg_replace('/^\/|\/$/','',$uri);
                        _e( '"</span> n\'existe pas...', 'illustratr' );
                        ?>
                    </h1>
				</header>

				<div class="page-content not-found-bloc">
					<p><?php _e( 'Il semblerait qu\'aucun contenu ne soit disponible &agrave; cet endroit. <br> Visitez plutôt
					la page d\'accueil en cliquant sur le bouton ci-dessous.', 'illustratr' ); ?></p>
                    <div class="btn-404">
                        <a href="<?php echo get_home_url() ?>"><span>Page d'accueil</span></a>
                    </div>


				</div>
			</section>

		</main>
	</div>
 
<?php get_footer(); ?>