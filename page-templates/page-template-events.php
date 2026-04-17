<?php /* Template Name: Landing Template */ get_header(); ?>
<style>
    header,footer, .header-bottom {
        display: none;
    }
</style>
<div class="page-content">
    <?php if ( function_exists( 'the_custom_logo' ) ) : ?>
        <div class="sp-logo">
            <?php the_custom_logo(); ?>
        </div>
    <?php endif; ?>
    <div class="gutenberg">
       <?php if (have_posts()):
                while (have_posts()):
                    the_post(); ?>

                    <?php the_content(); ?>

        <?php endwhile; endif; ?> 
    </div><!-- /gutenberg -->
</div><!-- /page-section -->
<?php get_footer(); ?>

<style>
    :root {
    --sp-bg: #f7f7f7;
    --sp-white: #ffffff;
    --sp-text: #1f1f1f;
    --sp-muted: #666666;
    --sp-border: #e8e8e8;
    --sp-accent: #f25555;
    --sp-accent-dark: #df4343;
    --sp-soft: #fff1f1;
    --sp-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    --sp-radius: 24px;
    --sp-radius-sm: 16px;
    --sp-container: 1180px;
}

    .body-content{margin:0!important; border-radius: 0!important;}
    body {background-color: #fff;}
    .sp-logo {
        background-color: rgb(28, 33, 39);
        padding: 12px;
        text-align: center;
    }
    .footer-landing {
        background-color: rgb(28, 33, 39);
        color: #fff;
    }
    h1, h2 {
        font-weight: 700;
    }
    .landing-hero, .faqs, #poruci {
            background: radial-gradient(circle at top right, rgba(242, 85, 85, 0.12), transparent 30%), linear-gradient(to bottom, #ffffff, #f8f8f8);
    }
    .landing-hero .wp-block-image,
    .before-after .wp-block-image  {
        border: 1px solid var(--sp-border);
        border-radius: var(--sp-radius);
        box-shadow: var(--sp-shadow);
        overflow: hidden;
    }
    .subtitle {
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: .6;
        font-size: 14px;
    }
    .wp-block-accordion-panel-is-layout-flow {
        padding-bottom: 24px;
    }

    #ukusi-container {
    margin-top: 15px;
    display: grid;
    gap: 10px;
}

.ukus-item select {
    width: 100%;
    padding: 10px;
    border-radius: 10px;
}
 .ukus-select:invalid {
    border: 1px solid #f25555;
}   
.sp-form-note {
    font-size: 13px;
    color: #555;
    text-align: center;
    background: #fff3f3;
    padding: 10px 12px;
    border-radius: 10px;
}
.wpcf7-form {
    background: var(--sp-white);
    border: 1px solid var(--sp-border);
    border-radius: var(--sp-radius);
    box-shadow: var(--sp-shadow);
    padding: 24px;
    width: auto;
}
.wpcf7-form  .sp-form-grid {
    width: 100%;
}
.wpcf7-submit {
    margin-top: 24px;
}
.wp-block-list {
    list-style: none;
    padding-left: 0!important;
}
.wp-block-list li {
    position: relative;
    padding-left: 20px;
}
.wp-block-list li::before, .sp-list li::before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 0;
    color: var(--sp-accent);
    font-weight: 800;
}
@media (max-width: 767px) {
    .body-content h1 {
        font-size: 30px!important;
    }
    body h3 {
        font-size: 20px!important;
    }
    body p {
        margin-bottom: 15px!important;
    }
    .footer-landing {
        font-size: 14px!important
    }
}
</style>
<script>
 document.addEventListener('DOMContentLoaded', function () {

    const gallery = document.querySelector('.wp-block-gallery');

    if (!gallery) return;

    // wrap u swiper strukturu
    const wrapper = document.createElement('div');
    wrapper.classList.add('swiper');

    const swiperWrapper = document.createElement('div');
    swiperWrapper.classList.add('swiper-wrapper');

    const figures = gallery.querySelectorAll('figure');

    figures.forEach(fig => {
        const slide = document.createElement('div');
        slide.classList.add('swiper-slide');

        slide.appendChild(fig.cloneNode(true));
        swiperWrapper.appendChild(slide);
    });

    wrapper.appendChild(swiperWrapper);

    // pagination
    const pagination = document.createElement('div');
    pagination.classList.add('swiper-pagination');
    wrapper.appendChild(pagination);

    gallery.replaceWith(wrapper);

    // init swiper
    new Swiper(wrapper, {
        loop: true,
        spaceBetween: 20,
        slidesPerView: 1.2,

        pagination: {
            el: pagination,
            clickable: true,
        },

        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            }
        }
    });

});   







document.addEventListener('DOMContentLoaded', function () {

    const paketSelect = document.getElementById('paket-select');
    const ukusiContainer = document.getElementById('ukusi-container');
    const summaryField = document.getElementById('order-summary');

    function getCountFromText(text) {
        if (text.includes('1')) return 1;
        if (text.includes('2')) return 2;
        if (text.includes('3')) return 3;
        if (text.includes('4')) return 4;
        return 1;
    }

    function renderUkusi(count) {
        ukusiContainer.innerHTML = '';

        for (let i = 1; i <= count; i++) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('ukus-item');

            wrapper.innerHTML = `
                <label>Ukus ${i}
                    <select class="ukus-select" required>
                        <option value="" disabled selected hidden>Odaberi ukus</option>
                        <option value="Jagoda">Jagoda</option>
                        <option value="Čokolada">Čokolada</option>
                        <option value="Vanila">Vanila</option>
                    </select>
                </label>
            `;

            ukusiContainer.appendChild(wrapper);
        }
    }

    function updateSummary() {
        const paket = paketSelect.value;
        const ukusi = [];

        document.querySelectorAll('.ukus-select').forEach(select => {
            if (select.value) {
                ukusi.push(select.value);
            }
        });

        summaryField.value = paket + ' | Ukusi: ' + ukusi.join(', ');
    }

    // 👉 DEFAULT (1 proizvod)
    renderUkusi(1);
    updateSummary();

    paketSelect.addEventListener('change', function () {
        const count = getCountFromText(this.value);
        renderUkusi(count);
        updateSummary();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('ukus-select')) {
            updateSummary();
        }
    });

    // VALIDACIJA
    document.addEventListener('submit', function(e){

        const selects = document.querySelectorAll('.ukus-select');

        for (let select of selects) {
            if (!select.value) {
                e.preventDefault();
                alert('Molimo izaberite sve ukuse.');
                return;
            }
        }

    });

});
</script>