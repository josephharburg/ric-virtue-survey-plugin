<?php
/**
 * Template Name: Focus Survey Template
 * The template for displaying a survey
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> <?php twentytwentyone_the_html_classes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<div id="page" class="site">
		<div id="content" class="site-content">
			<div id="primary" class="content-area">
				<main id="main" class="site-main">
	<?php
	/* Start the Loop */
	while ( have_posts() ) :
		the_post();
		?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content" style="margin-top: 10%;">
				<?php

				the_content();

				?>
				</div>
		</div>


		<?php

		// If comments are open or there is at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile; // End of the loop.

	?>
				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- #content -->
	</div><!-- #page -->
	<footer>
	<?php wp_footer(); ?>
	</footer>
</body>
</html>
