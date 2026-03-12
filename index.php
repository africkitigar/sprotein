<?php get_header();
$postsPageID = get_option('page_for_posts');
$news_post = get_post($postsPageID);
$page_content = apply_filters('the_content', $news_post->post_content);
?>
<div class="page-section blog-index">
	<div class="gutenberg">
		<?php echo apply_filters('the_content', $page_content); ?>
	</div>
	<div class="container posts-grid" id="posts-feed">

		<?php get_template_part('template-parts/loop'); ?>



		
	</div><!-- /container -->

	<div class="container">
		<?php if ($wp_query->max_num_pages > 1): ?>
			<div class="load-more-wrap">
				<button id="load-more-posts" class="btn" data-page="1" data-max="<?php echo $wp_query->max_num_pages; ?>">
					Učitaj još
				</button>
			</div>
		<?php endif; ?>
	</div>
<script>
const loadMoreData = {
    ajaxurl: "<?php echo admin_url('admin-ajax.php'); ?>",
    query: <?php echo json_encode($wp_query->query_vars); ?>
};
</script>
</div><!-- /page-section -->
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