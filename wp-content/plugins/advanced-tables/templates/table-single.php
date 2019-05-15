<?php
/**
 * The template for Table - Custom Post Type
 *
 */

get_header(); ?>

	<div id="primary" class="content-area lptw-advanced-table-container">
		<main id="main" class="site-main table" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            	<header class="entry-header">
            		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            	</header><!-- .entry-header -->

            	<div class="entry-content">
            		<?php the_content(); ?>

                    <?php
                    $style = get_post_meta( $post->ID, 'lptw_table_style', true );
                    echo lptw_advanced_table_json2html (get_the_ID(), $style );

            		wp_link_pages( array(
            		    'before' => '<div class="page-links">' . __( 'Tables:', 'lptw_advanced_tables_domain' ),
            			'after'  => '</div>',
            		) );
            		?>
            	</div><!-- .entry-content -->


            	<footer class="entry-footer">
            		<?php edit_post_link( __( 'Edit', 'lptw_advanced_tables_domain' ), '<span class="edit-link">', '</span>' ); ?>
            	</footer><!-- .entry-footer -->
            </article><!-- #post-## -->


				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
