<?php get_header();
$postsPageID = get_option('page_for_posts');
$hero_url = get_the_post_thumbnail_url($postsPageID, 'hero');
?>
<div class="page-section">
	<div class="gutenberg">


				<h1 class="wp-block-heading animated fadeInUp moving"><?php echo get_search_query(); ?></h1>

	</div>

	<?php if (have_posts()): ?>
		<div class="container" id="posts-feed">

			<?php get_template_part('template-parts/loop-search'); ?>

			<div class="pagination"><?php greentheme_pagination(); ?></div>


		</div><!-- /container -->
	<?php endif; ?>
</div><!-- /page-section -->

<?php get_footer(); ?>