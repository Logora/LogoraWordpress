<?php
/**
 * Template Name: Logora App Page
 *
 * @package Logora
 * @subpackage Logora App Page
 * @since 1.0.0
 */
get_header();
?>
	<section id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="entry-content">
                <?php
                    while ( have_posts() ) : the_post();
                        the_content();
                    endwhile; // End of the loop.
                ?>
             </div>    
		</main>
	</section>
<?php
get_footer();