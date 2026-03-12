<?php get_header();
$postsPageID = get_option('page_for_posts');
$term = get_queried_object();

if ($term->parent == 0) {
	$parent_category_id = get_queried_object_id();
} else {
	$parent_category_id = $term->parent;
}

?>



<div class="gutenberg">
	<div class="wp-block-group hero-banner-blog">
		<div class="wp-block-group__inner-container">
			<h1 class="wp-block-heading has-text-align-center"><?php single_cat_title(); ?></h1>
			<p class="has-text-align-center has-large-font-size"><?php echo $term->description; ?></p>

		</div>
	</div>
</div>



<div class="container">
	<div class="articles-grid">
		<div id="posts-feed" class="posts-grid">
			<?php get_template_part('template-parts/loop'); ?>

		</div>
		<?php if ($wp_query->max_num_pages > 1): ?>
			<div class="load-more-wrap">
				<button id="load-more-posts" class="btn" data-page="1" data-max="<?php echo $wp_query->max_num_pages; ?>">
					Učitaj još
				</button>
			</div>
		<?php endif; ?>
	</div>
</div>


<script>
const loadMoreData = {
    ajaxurl: "<?php echo admin_url('admin-ajax.php'); ?>",
    query: <?php echo json_encode($wp_query->query_vars); ?>
};
</script>

<?php get_footer(); ?>

<script>
	document.addEventListener('DOMContentLoaded', function(){

    const btn = document.getElementById('load-more-posts');
    if(!btn) return;

    let page = 1;
    const max = btn.dataset.max;

    btn.addEventListener('click', function(){

        btn.innerText = 'Učitavanje...';

        const formData = new FormData();
        formData.append('action', 'load_more_posts');
        formData.append('page', page);
        formData.append('query', JSON.stringify(loadMoreData.query));

        fetch(loadMoreData.ajaxurl,{
            method:'POST',
            body:formData
        })
        .then(res => res.text())
        .then(data =>{

            document.querySelector('.home-blog-posts, .posts-grid')
                .insertAdjacentHTML('beforeend',data);

            page++;

            btn.innerText = 'Učitaj još';

            if(page >= max){
                btn.remove();
            }

        });

    });

});
</script>