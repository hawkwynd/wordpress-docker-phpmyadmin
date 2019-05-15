<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package demonstration
 */

get_header(); ?>

	<div id="primary" class="content-area tables-tabs">
		<main id="main" class="site-main" role="main">
        	<div class="entry-content">

		<?php
            $content_args = array (
                'post_type' => 'table',
                'posts_per_page' => '-1',
                'orderby' => 'menu_order',
                'order' => 'ASC'
            );

            $content_query = new WP_Query($content_args);

            if ( $content_query->have_posts() ) : ?>
			<header class="page-header">
                <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
				<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
            <div id="lptw-tabs-wrapper">
            <div id="lptw-tables-responsive-tabs">
            <?php /* special loop for the tabs headers output */
                $headers_args = array (
                    'post_type' => 'table',
                    'posts_per_page' => '-1',
                    'orderby' => 'menu_order',
                    'order' => 'ASC'
                );

                $headers_query = new WP_Query($headers_args);
                if ( $headers_query->have_posts() ) {
                    $tabs = 1;
                	echo '<ul>'."\n";
                    while ($headers_query->have_posts()) {
                        $headers_query->the_post();
                    	echo '<li><a href="#tabs-'.$tabs.'">' . get_the_title() . '</a></li>'."\n";
                        $tabs++;
                    }
                    echo '</ul>'."\n";
                }
                /* Restore original Post Data */
                wp_reset_postdata();

            $tabs_count = 1;
            ?>
			<?php while ( $content_query->have_posts() ) : $content_query->the_post(); ?>
            <div id="tabs-<?php echo $tabs_count; ?>">
                <?php the_content(); ?>
                <?php
                    $style = get_post_meta( get_the_ID(), 'lptw_table_style', true );
                    echo lptw_advanced_table_json2html (get_the_ID(), $style );
                ?>

            </div>
            <?php $tabs_count++; ?>
			<?php endwhile; ?>
            </div><!-- #lptw-tabs-wrapper -->
            </div><!-- #tabs -->

			<?php the_posts_navigation(); ?>

		<?php else : ?>

			<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'lptw_advanced_tables_domain' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
            </div><!-- .entry-content -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
