<?php
$postsPageID = get_option('page_for_posts');



// Get the categories of the current post
$current_categories = get_the_category();
$category_ids = array();
$parent_cat = 0;

if (!empty($current_categories)) {
  foreach ($current_categories as $category) {
    $category_ids[] = $category->term_id;

    if($category->parent){
      $parent_cat = $category->parent;
       $parent_category = get_category( $category->parent );
        
        // Get the URL of the parent category
       $parent_category_url = get_category_link( $parent_category->term_id );
    }
  }

  if($parent_cat == 0) {
     $first_category = $current_categories[0];
     $parent_category_url = get_category_link( $first_category->term_id );
  }
}//endif




// WP_Query arguments
$args = array(
  'post_type' => array('post'),
  'post_status' => array('publish'),
  'posts_per_page' => 6,
  'post__not_in' => array(get_the_ID()), // Exclude the current post
  'category__in' => $category_ids,       // Fetch posts from the current post's categories
);

// The Query
$query = new WP_Query($args);

// The Loop
if ($query->have_posts()) {
  ?>
  <div class="latest-posts-block">

    <div class="slider-header container">
      <h2>Ostali artikli</h2>
    </div>

    <div class="swiper postsSwiper">
      <div class="swiper-wrapper">
        <?php
        while ($query->have_posts()) {
          $query->the_post();
          ?>
          <div class="swiper-slide">
            <article class="post-in-loop">
              <?php if (has_post_thumbnail()): ?>
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="post-in-loop-image">
                  <?php the_post_thumbnail('large'); ?>
                </a>
              <?php endif; ?>
              <div class="post-in-loop-content">
                <h2>
                  <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                </h2>
                <a class="read-article btn" href="<?php echo get_the_permalink(); ?>">Pročitaj post</a>
              </div>
            </article>
          </div>
          <?php
        } // endwhile
        ?>


      </div>
      <div class="related-posts-nav">
        <div class="swiper-button-prev">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path
              d="M17.8334 11.1664H9.50006L12.2417 8.42468C12.3198 8.34721 12.3818 8.25505 12.4241 8.1535C12.4664 8.05195 12.4882 7.94303 12.4882 7.83302C12.4882 7.72301 12.4664 7.61409 12.4241 7.51254C12.3818 7.41099 12.3198 7.31882 12.2417 7.24135C12.0856 7.08614 11.8744 6.99902 11.6542 6.99902C11.4341 6.99902 11.2229 7.08614 11.0667 7.24135L7.49173 10.8247C7.17874 11.1358 7.00192 11.5584 7.00006 11.9997C7.00412 12.4381 7.18076 12.8573 7.49173 13.1664L11.0667 16.7497C11.1444 16.8268 11.2366 16.8879 11.3379 16.9295C11.4392 16.971 11.5477 16.9922 11.6572 16.9918C11.7667 16.9914 11.875 16.9695 11.976 16.9272C12.077 16.885 12.1687 16.8232 12.2459 16.7455C12.323 16.6678 12.3841 16.5757 12.4257 16.4744C12.4672 16.3731 12.4884 16.2646 12.488 16.1551C12.4876 16.0456 12.4657 15.9372 12.4234 15.8362C12.3812 15.7352 12.3194 15.6435 12.2417 15.5664L9.50006 12.833H17.8334C18.0544 12.833 18.2664 12.7452 18.4227 12.5889C18.5789 12.4327 18.6667 12.2207 18.6667 11.9997C18.6667 11.7787 18.5789 11.5667 18.4227 11.4104C18.2664 11.2541 18.0544 11.1664 17.8334 11.1664Z"
              fill="#101223" />
          </svg>
        </div>
        <div class="swiper-button-next">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path
              d="M16.9997 12.0001C16.9957 11.5617 16.819 11.1426 16.5081 10.8335L12.9331 7.25014C12.7769 7.09493 12.5657 7.00781 12.3456 7.00781C12.1254 7.00781 11.9142 7.09493 11.7581 7.25014C11.68 7.32761 11.618 7.41978 11.5757 7.52133C11.5334 7.62288 11.5116 7.7318 11.5116 7.84181C11.5116 7.95182 11.5334 8.06074 11.5757 8.16229C11.618 8.26384 11.68 8.356 11.7581 8.43347L14.4997 11.1668H6.1664C5.94539 11.1668 5.73343 11.2546 5.57715 11.4109C5.42087 11.5672 5.33307 11.7791 5.33307 12.0001C5.33307 12.2212 5.42087 12.4331 5.57715 12.5894C5.73343 12.7457 5.94539 12.8335 6.1664 12.8335H14.4997L11.7581 15.5751C11.6012 15.731 11.5126 15.9427 11.5118 16.1639C11.511 16.385 11.5981 16.5974 11.7539 16.7543C11.9097 16.9112 12.1215 16.9998 12.3426 17.0006C12.5638 17.0014 12.7761 16.9143 12.9331 16.7585L16.5081 13.1751C16.8211 12.864 16.9979 12.4415 16.9997 12.0001Z"
              fill="#101223" />
          </svg>
        </div>
      </div> <!--/related-posts -->
    </div>
  </div>
  <?php
}

// Restore original Post Data
wp_reset_postdata();
?>

<script>
// Function to calculate the offset dynamically
const calculateOffset = () => {
  const slideWidth = 1520; // Desired slide width in pixels
  const viewportWidth = window.innerWidth; // Viewport width

  // Calculate the offset: (100vw - slideWidth) / 2
  return (viewportWidth - slideWidth) / 2;
};

// Function to initialize or update Swiper
const initSwiper = () => {
  const swiperContainer = document.querySelector(".postsSwiper");
  const nextArrow = document.querySelector('.swiper-button-next');
  const prevArrow = document.querySelector('.swiper-button-prev');
  
  // Destroy existing Swiper instance if it exists
  if (swiperContainer.swiper) {
    swiperContainer.swiper.destroy(true, true);
  }

  // Check total slides
  const totalSlides = swiperContainer.querySelectorAll('.swiper-slide').length;
  const allowSlide = window.innerWidth <= 1200 || totalSlides >= 4;

  // Show/hide navigation arrows based on sliding condition
  if (allowSlide) {
    nextArrow.style.display = 'block';
    prevArrow.style.display = 'block';
  } else {
    nextArrow.style.display = 'none';
    prevArrow.style.display = 'none';
  }

  // Initialize Swiper
  new Swiper(".postsSwiper", {
    slidesPerView: "1.6",
    spaceBetween: 16,
    slidesOffsetBefore: 20,
    slidesOffsetAfter: 20,
    loop: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    allowSlideNext: allowSlide,
    allowSlidePrev: allowSlide,
    // Responsive breakpoints
    breakpoints: {
      767: {
        slidesPerView: "2.2",
      },
      1025: {
        slidesPerView: "3.2",
      },
      1490: {
        slidesPerView: "3.9",
        spaceBetween: 56,
        slidesOffsetBefore: calculateOffset(), // Calculate offset for large screens
      }
    }
  });
};

// Initialize Swiper after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', initSwiper);

// Recalculate and update Swiper on window resize
window.addEventListener('resize', initSwiper);

</script>