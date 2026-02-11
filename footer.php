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



<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Check for saved theme in localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        body.classList.add(savedTheme);
    } else {
        body.classList.add('light-mode'); // Default to light mode
    }

    // Toggle between dark and light mode
    themeToggle.addEventListener('click', function() {
        if (body.classList.contains('dark-mode')) {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            localStorage.setItem('theme', 'light-mode');
        } else {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark-mode');
        }
    });
});



  document.addEventListener('DOMContentLoaded', function () {
    /*** Venobox on the button */
    // Check if there are any elements with the class .video-btn
    const videoButtons = document.querySelectorAll('.video-btn > a');

    if (videoButtons.length > 0) {
      // Add the required attributes to each <a> element inside .video-btn
      videoButtons.forEach(btn => {
        btn.setAttribute('data-autoplay', 'true');
        btn.setAttribute('data-vbtype', 'video');
      });

      // Initialize Venobox
      new VenoBox({
        selector: '.video-btn > a'
      });
    }
/*** end of Venobox on the button */



    // Select all elements with the class "onscroll-view"
    const onScrollElements = document.querySelectorAll('.onscroll-view');

    // Create a new IntersectionObserver instance
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        // Check if the element is intersecting (in the viewport)
        if (entry.isIntersecting) {
          entry.target.classList.add('in-viewport');
        }/* else {
                entry.target.classList.remove('in-viewport');
            }*/
      });
    }, {
      // Set the threshold to 0.1, which means the callback will be triggered when 10% of the element is in the viewport
      threshold: 0.15
    });

    // Observe each selected element
    onScrollElements.forEach((el) => {
      observer.observe(el);
    });
  });




  // Initialize Lenis after the document is fully loaded
  document.addEventListener('DOMContentLoaded', function () {
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