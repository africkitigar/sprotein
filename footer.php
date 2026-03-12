</div>
<!-- /body-content -->
</div>
<!-- /wrapper -->


<!-- footer -->
<footer class="footer" role="contentinfo">
  <div class="footer-top">
    <div class="container">
      <div class="footer-widget">
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('widget-area-1')) ?>
      </div>

        <?php if (has_nav_menu('footer-menu')) {
            wp_nav_menu(array('theme_location' => 'footer-menu'));
        }
        ?> 
    </div><!-- /container -->
  </div><!-- /footer-top -->
  <div class="footer-bottom">
    <div class="container flex flex-vertical-center flex-space-between">
      <div class="copyright">
      Copyright © 2025 Snaga prirode All rights reserved
      </div><!-- /copyright -->

      <?php if (has_nav_menu('footer-menu2')) {
        wp_nav_menu(array('theme_location' => 'footer-menu2'));
      }
      ?>

      <?php get_template_part('template-parts/intesa-cards'); ?>
    </div><!-- /container -->
  </div><!-- /footer-bottom -->
</footer>
<!-- /footer -->


</div>
<!-- /wrapper -->

<?php wp_footer(); ?>

<div class="progress-wrap">
  <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
  </svg>
</div><!-- back to top -->

<?php if(is_front_page()): ?>
<script>
jQuery(function ($) {

    $('.top-picks-cat').on('click', function () {

        const btn = $(this);
        const cat = btn.data('cat');
        const wrapper = btn.closest('.top-picks-section');
        const products = wrapper.find('.top-picks-products');

        wrapper.find('.top-picks-cat').removeClass('active');
        btn.addClass('active');

        products.html(wrapper.find('.skeleton-template').html());

        $.post(
            wc_add_to_cart_params.ajax_url,
            {
                action: 'top_picks_filter',
                cat: cat
            },
            function (response) {

                products.html(response);

            }
        );

    });

});   




document.addEventListener("DOMContentLoaded", function () {





    const slider = document.querySelector('.hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.wp-block-cover');

    const wrapper = document.createElement('div');
    wrapper.className = 'swiper-wrapper';

    slides.forEach(slide => {

        slide.classList.add('swiper-slide');
        wrapper.appendChild(slide);

    });

    slider.classList.add('swiper');
    slider.appendChild(wrapper);

    const pagination = document.createElement('div');
    pagination.className = 'swiper-pagination';

    slider.appendChild(pagination);

    new Swiper(slider, {
        spaceBetween: 24,
        slidesPerView: 1,
        loop: true,
        speed: 2300,
        parallax: true,

        autoplay: {
            delay: 5000,
            disableOnInteraction: false
        },

        pagination: {
            el: pagination,
            clickable: true
        }

    });


/*
    slides.forEach(slide => {

    slide.classList.add('swiper-slide');

    const bg = slide.querySelector('.wp-block-cover__image-background');
    if (bg) {
        bg.setAttribute('data-swiper-parallax', '-20%');
    }

    wrapper.appendChild(slide);

});*/

});
</script>    
<?php endif;?>


<script>

  // Initialize Lenis after the document is fully loaded
  document.addEventListener('DOMContentLoaded', function () {
    const searchToggle = document.getElementById('search-toggle');
    const searchBar = document.getElementById('header-search-bar');
     // Otvori/zatvori pretragu
    searchToggle.addEventListener('click', function() {
        searchBar.classList.toggle('active');
    });
    // klik van search bar-a zatvara search
    document.addEventListener('click', function(e) {

        if (
            !searchBar.contains(e.target) &&
            !searchToggle.contains(e.target)
        ) {
            searchBar.classList.remove('active');
        }

    });


    const lenis = new Lenis({
      // Options for Lenis
      duration: 1.2, // Duration of the scroll animation
      easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      orientation: 'vertical', // Scroll orientation
      smoothWheel: true, // Enable smooth scrolling
    });

    // Start the scrolling animation
    function raf(time) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);
  });


</script>

<?php if(is_cart()): ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {

    const cartForm = document.querySelector('form.woocommerce-cart-form');
    if (!cartForm) return;

    function triggerCartUpdate() {
        const updateBtn = cartForm.querySelector('[name="update_cart"]');
        if (!updateBtn) return;

        updateBtn.disabled = false;
        updateBtn.click();
    }

    // Kada se promeni input
    cartForm.addEventListener('change', function (e) {
        if (e.target.classList.contains('qty')) {
            triggerCartUpdate();
        }
    });

    // Kada se klikne plus/minus
    cartForm.addEventListener('click', function (e) {
        if (e.target.classList.contains('plus') || e.target.classList.contains('minus')) {
            
            const qtyInput = e.target.closest('.quantity').querySelector('.qty');
            
            setTimeout(() => {
                qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
            }, 50);
        }
    });

});

</script>
<?php endif; ?>


<?php if(is_product() || is_cart()): ?>
<script>
jQuery(function($){

    function initQuantityButtons() {

        $('.quantity').each(function(){

            if ($(this).find('.plus').length) return;

            const $input = $(this).find('input.qty');

            $(this).prepend("<button type='button' class='minus'>-</button>");
            $(this).append("<button type='button' class='plus'>+</button>");
        });
    }

    function triggerCartUpdate() {
        const $form = $('form.woocommerce-cart-form');
        const $updateBtn = $form.find('[name="update_cart"]');

        if ($updateBtn.length) {
            $updateBtn.prop('disabled', false).trigger('click');
        }
    }

    // INIT NA LOAD
    initQuantityButtons();

    // INIT POSLE SVAKOG AJAX REFRESH-A
    $(document.body).on('updated_cart_totals wc_fragments_refreshed', function(){
        initQuantityButtons();
    });

    // EVENT DELEGATION ZA PLUS/MINUS
    $(document).on('click', '.plus, .minus', function(){

        const $qty = $(this).closest('.quantity').find('input.qty');

        let current = parseInt($qty.val());
        let step = parseInt($qty.attr('step')) || 1;
        let min = parseInt($qty.attr('min')) || 0;

        if ($(this).hasClass('plus')) {
            $qty.val(current + step);
        }

        if ($(this).hasClass('minus') && current > min) {
            $qty.val(current - step);
        }

        $qty.trigger('change');

        setTimeout(function(){
            triggerCartUpdate();
        }, 200);

    });

});

</script>
<script>
  jQuery(document).ready(function ($) {
    $('.variations_form').each(function () {
        var $form = $(this);
        var $variationForm = $form.closest('.variations_form');

        // Create buttons for each select
        $form.find('.variations select').each(function () {
            var $select = $(this);
            var attrName = $select.attr('name');
            var options = $select.find('option');
            var selectedValue = $select.val();
            var buttonsHtml = '';

            options.each(function () {
                var value = $(this).val();
                var label = $(this).text();
                if (value !== '') {
                    buttonsHtml += `<button type="button" class="variation-btn" data-attr="${attrName}" data-value="${value}">${label}</button>`;
                }
            });

            var $wrapper = $('<div class="variation-buttons"></div>').html(buttonsHtml);
            $select.after($wrapper).hide();
        });

        // Handle button click
        $form.on('click', '.variation-btn', function () {
            var $button = $(this);
            if ($button.hasClass('disabled')) return false;
            
            var attrName = $button.data('attr');
            var value = $button.data('value');

            // Set the selected value in the hidden select field
            $form.find(`select[name="${attrName}"]`).val(value).trigger('change');

            // Update active class
            $form.find(`.variation-btn[data-attr="${attrName}"]`).removeClass('active');
            $button.addClass('active');
        });

        // Set initial selection (if any)
        $form.find('.variation-btn').each(function () {
            var $button = $(this);
            var attrName = $button.data('attr');
            var selectedValue = $form.find(`select[name="${attrName}"]`).val();
            if ($button.data('value') === selectedValue) {
                $button.addClass('active');
            }
        });

        // Update button states when variations change
        $form.on('found_variation update_variation_values', function() {
            updateVariationButtons($variationForm);
        });

        // Initialize button states
        setTimeout(function() {
            updateVariationButtons($variationForm);
        }, 100);
    });

    function updateVariationButtons($form) {
        // Reset all buttons
        $form.find('.variation-btn').removeClass('disabled unavailable');
        
        // Get current selections
        var currentSelections = {};
        $form.find('.variations select').each(function() {
            currentSelections[$(this).attr('name')] = $(this).val();
        });
        
        // Get all variations data
        var variations = $form.data('product_variations');
        
        // For each attribute, check which options are available
        $form.find('.variations select').each(function() {
            var $select = $(this);
            var attributeName = $select.attr('name');
            
            $select.find('option').each(function() {
                var value = $(this).val();
                if (!value) return;
                
                var $button = $form.find(`.variation-btn[data-attr="${attributeName}"][data-value="${value}"]`);
                
                // Temporarily select this value to check availability
                var tempSelections = $.extend({}, currentSelections);
                tempSelections[attributeName] = value;
                
                // Check if any variation matches these selections
                var isAvailable = variations.some(function(variation) {
                    return isVariationMatch(variation.attributes, tempSelections);
                });
                
                if (!isAvailable) {
                    $button.addClass('disabled unavailable');
                }
            });
        });
    }
    
    function isVariationMatch(variationAttrs, selectedAttrs) {
        for (var attr in selectedAttrs) {
            if (selectedAttrs[attr] && variationAttrs[attr] !== selectedAttrs[attr]) {
                return false;
            }
        }
        return true;
    }
});
</script>
<?php endif; ?>

</body>

</html>