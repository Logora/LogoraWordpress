<?php
/**
 * Template Name: Logora App Page
 *
 * @package Logora
 * @subpackage Logora App Page
 * @since 1.0.0
 */
while ( have_posts() ) : the_post();
	the_content();
endwhile; // End of the loop.
