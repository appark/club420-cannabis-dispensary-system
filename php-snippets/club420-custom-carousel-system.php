// Club420 BlazeSlider - REAL-TIME SCHEDULING (CLEAN PRODUCTION VERSION)
add_shortcode('club420_deals_carousel', 'club420_blazeslider_adaptation');

function club420_blazeslider_adaptation($atts) {
    // FORCE fresh data for ALL users - treat non-logged-in like logged-in for cache bypass
    $original_user = wp_get_current_user();
    $was_logged_out = !is_user_logged_in();
    
    if ($was_logged_out) {
        // Temporarily fake being logged in to bypass caching
        wp_set_current_user(1);
    }
    
    $atts = shortcode_atts(array(
        'store' => 'current'
    ), $atts);
    
    $categories = array(
        'flower' => array('name' => 'Flower Deals', 'slug' => 'flower'),
        'preroll' => array('name' => 'Preroll Deals', 'slug' => 'preroll'),
        'cartridge' => array('name' => 'Cartridge Deals', 'slug' => 'cartridge'),
        'edible' => array('name' => 'Edible Deals', 'slug' => 'edible'),
        'extract' => array('name' => 'Extract Deals', 'slug' => 'extract')
    );
    
    static $club420_slider_id = 1;
    $output = '';
    
    foreach ($categories as $cat_key => $category) {
        $output .= '<h3 style="font-size: 28px; font-weight: 700; margin: 40px 0 30px 0; color: #333;">' . esc_html($category['name']) . '</h3>';
        $output .= '<div class="blaze-slider" id="club420_slider'.$club420_slider_id.'" data-category="' . esc_attr($cat_key) . '">';
        $output .= '<div class="my-structure">';
        $output .= '<span class="blaze-prev" aria-label="Go to previous slide">';
        $output .= '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
        $output .= '<path d="M15 18L9 12L15 6" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        $output .= '</svg></span>';
        $output .= '<span class="blaze-next" aria-label="Go to next slide">';
        $output .= '<svg width="38" height="38" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
        $output .= '<path d="M9 18L15 12L9 6" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        $output .= '</svg></span>';
        $output .= '</div>';
        $output .= '<div class="blaze-container"><div class="blaze-track-container"><div class="club420-blaze-track blaze-track">';
        
        // Check current store filter
        $store_location = isset($_GET['store_filter']) ? sanitize_text_field($_GET['store_filter']) : 'all';
        
        // Get products with basic query
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 50,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $category['slug'],
                )
            ),
            'orderby' => 'date',
            'order' => 'DESC',
            'club420_carousel_query' => true,
            'cache_results' => false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false
        );
        
        // Add basic store availability filter
        if ($store_location !== 'all') {
            $url_key = ($store_location === 'davis') ? '_club420_davis_url' : '_club420_dixon_url';
            $args['meta_query'] = array(
                array(
                    'key' => $url_key,
                    'value' => '',
                    'compare' => '!='
                )
            );
        }
        
        $products = new WP_Query($args);
        $displayed_count = 0;
        
        if ($products->have_posts()) {
            while ($products->have_posts() && $displayed_count < 12) {
                $products->the_post();
                global $product;
                
                if (!$product || !$product->is_visible()) {
                    continue;
                }
                
                $product_id = get_the_ID();
                
                // Check if product should show using real-time scheduling
                if ($store_location !== 'all') {
                    // FORCE fresh check for non-logged-in users
                    if (!is_user_logged_in()) {
                        clean_post_cache($product_id);
                        wp_cache_delete($product_id, 'posts');
                    }
                    
                    $should_show = club420_should_product_show($product_id, $store_location);
                    
                    if (!$should_show) {
                        continue;
                    }
                }
                
                // Product passed all checks - display it
                $output .= '<div class="post_card">';
                $output .= '<a href="' . esc_url(get_permalink()) . '" style="position: relative;">';
                $output .= woocommerce_get_product_thumbnail();
                $output .= club420_get_yith_badge_html(get_the_ID());
                $output .= '</a>';
                $output .= '<span class="wwo_card_details">';
                $output .= '<a class="p_title" href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a>';
                $output .= '</span>';
                $output .= '</div>';
                
                $displayed_count++;
            }
            wp_reset_postdata();
        }
        
        $output .= '</div></div></div></div>';
        $club420_slider_id++;
    }
    
    // Restore original user state
    if ($was_logged_out) {
        wp_set_current_user(0);
    }
    
    return $output;
}

/**
 * Real-time scheduling logic - reads directly from database
 */
function club420_should_product_show($product_id, $store_location) {
    global $wpdb;
    
    // Read scheduling data directly from database (bypasses all caching)
    $visibility_key = "_club420_{$store_location}_visibility";
    $enabled_key = "_club420_{$store_location}_enabled";
    $start_key = "_club420_{$store_location}_schedule_start";
    $end_key = "_club420_{$store_location}_schedule_end";
    
    $meta_data = $wpdb->get_results($wpdb->prepare("
        SELECT meta_key, meta_value 
        FROM {$wpdb->postmeta} 
        WHERE post_id = %d 
        AND meta_key IN (%s, %s, %s, %s)
    ", $product_id, $visibility_key, $enabled_key, $start_key, $end_key), ARRAY_A);
    
    $visibility = '';
    $enabled = '';
    $start_time = '';
    $end_time = '';
    
    foreach ($meta_data as $meta) {
        switch ($meta['meta_key']) {
            case $visibility_key:
                $visibility = $meta['meta_value'];
                break;
            case $enabled_key:
                $enabled = $meta['meta_value'];
                break;
            case $start_key:
                $start_time = $meta['meta_value'];
                break;
            case $end_key:
                $end_time = $meta['meta_value'];
                break;
        }
    }
    
    $current_utc_time = current_time('timestamp', true);
    
    switch ($visibility) {
        case 'always':
            return true;
            
        case 'disabled':
            return false;
            
        case 'scheduled':
            if (!$start_time || !$end_time) {
                return false;
            }
            return ($current_utc_time >= $start_time && $current_utc_time <= $end_time);
            
        default:
            return ($enabled === 'yes');
    }
}

function club420_get_yith_badge_html($product_id) {
    if (!function_exists('yith_wcbm_get_product_badges')) {
        return '';
    }
    
    $badges = yith_wcbm_get_product_badges($product_id);
    
    if (empty($badges)) {
        return '';
    }
    
    $badge_html = '';
    foreach ($badges as $badge) {
        if (isset($badge['text']) && !empty($badge['text'])) {
            $badge_html .= '<span class="club420-yith-badge">';
            $badge_html .= esc_html($badge['text']);
            $badge_html .= '</span>';
        }
    }
    
    return $badge_html;
}

function club420_blazeslider_styles() {
    if (!is_front_page() && !is_shop() && !is_product_category()) {
        return;
    }
    
    echo '<style>
.blaze-slider{--slides-to-show:4;--slide-gap:20px;direction:ltr}
.blaze-container{position:relative}
.blaze-track-container{overflow:hidden}
.blaze-track{will-change:transform;touch-action:pan-y;display:flex;gap:var(--slide-gap);--slide-width:calc((100% - (var(--slides-to-show) - 1) * var(--slide-gap)) / var(--slides-to-show));box-sizing:border-box}
.blaze-track>*{box-sizing:border-box;width:var(--slide-width);flex-shrink:0}

.blaze-slider {
    position: relative;
    margin: 0 10px;
}

.my-structure {
    pointer-events: none;
    position: absolute;
    inset: -20px -40px;
    z-index: 2;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
}

.my-structure span {
    pointer-events: all;
    cursor: pointer;
    transition: all .3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
}

.my-structure svg {
    transition: opacity 0.3s ease;
}

.my-structure span:hover svg {
    opacity: 0.7;
}

.club420-blaze-track .post_card {
    position: relative;
    max-width: 500px;
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.club420-blaze-track .post_card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.club420-blaze-track .post_card .container-image-and-badge img {
    width: 92%;
    height: auto;
    margin: 18px auto 8px auto;
    object-fit: contain;
}

.club420-blaze-track .post_card .container-image-and-badge {
    text-align: center;
}

.club420-blaze-track .wwo_card_details {
    display: flex;
    flex-direction: column;
    padding: 8px 15px 15px 15px;
}

.club420-blaze-track .p_title {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    text-decoration: none;
    color: #2c2c2c;
    text-align: left;
    line-height: 1.3;
}

.club420-yith-badge {
    z-index: 3;
    position: absolute;
    top: 8px;
    left: 8px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: #fff;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
    border: 2px solid rgba(255, 255, 255, 0.2);
    white-space: nowrap;
}

@media (max-width: 900px) {
    .blaze-slider {
        --slides-to-show: 2;
        margin: 0 10px;
    }
    
    .my-structure {
        inset: -15px -35px;
    }
}

@media (max-width: 500px) {
    .blaze-slider {
        --slides-to-show: 1.5;
        margin: 0 15px;
    }
    
    .my-structure {
        inset: -10px -35px;
    }
    
    .my-structure span {
        width: 35px;
        height: 35px;
    }
    
    .my-structure svg {
        width: 30px;
        height: 30px;
    }
    
    .club420-blaze-track .post_card .container-image-and-badge img {
        width: 94%;
        margin: 12px auto 8px auto;
    }
}
</style>';
}
add_action('wp_head', 'club420_blazeslider_styles', 30);

function club420_blazeslider_scripts() {
    if (!is_front_page() && !is_shop() && !is_product_category()) {
        return;
    }
    
    echo '<script>
var BlazeSlider=function(){"use strict";const t="start";class e{constructor(t,e){this.config=e,this.totalSlides=t,this.isTransitioning=!1,n(this,t,e)}next(t=1){if(this.isTransitioning||this.isStatic)return;const{stateIndex:e}=this;let n=0,i=e;for(let e=0;e<t;e++){const t=this.states[i];n+=t.next.moveSlides,i=t.next.stateIndex}return i!==e?(this.stateIndex=i,[e,n]):void 0}prev(t=1){if(this.isTransitioning||this.isStatic)return;const{stateIndex:e}=this;let n=0,i=e;for(let e=0;e<t;e++){const t=this.states[i];n+=t.prev.moveSlides,i=t.prev.stateIndex}return i!==e?(this.stateIndex=i,[e,n]):void 0}}function n(t,e,n){t.stateIndex=0,function(t){const{slidesToScroll:e,slidesToShow:n}=t.config,{totalSlides:i,config:s}=t;if(i<n&&(s.slidesToShow=i),!(i<=n)&&(e>n&&(s.slidesToScroll=n),i<e+n)){const t=i-n;s.slidesToScroll=t}}(t),t.isStatic=e<=n.slidesToShow,t.states=function(t){const{totalSlides:e}=t,{loop:n}=t.config,i=function(t){const{slidesToShow:e,slidesToScroll:n,loop:i}=t.config,{isStatic:s,totalSlides:o}=t,r=[],a=o-1;for(let t=0;t<o;t+=n){const n=t+e-1;if(n>a){if(!i){const t=a-e+1,n=r.length-1;(0===r.length||r.length>0&&r[n][0]!==t)&&r.push([t,a]);break}{const e=n-o;r.push([t,e])}}else r.push([t,n]);if(s)break}return r}(t),s=[],o=i.length-1;for(let t=0;t<i.length;t++){let r,a;n?(r=t===o?0:t+1,a=0===t?o:t-1):(r=t===o?o:t+1,a=0===t?0:t-1);const l=i[t][0],c=i[r][0],d=i[a][0];let u=c-l;c<l&&(u+=e);let f=l-d;d>l&&(f+=e),s.push({page:i[t],next:{stateIndex:r,moveSlides:u},prev:{stateIndex:a,moveSlides:f}})}return s}(t)}function i(t){if(t.onSlideCbs){const e=t.states[t.stateIndex],[n,i]=e.page;t.onSlideCbs.forEach((e=>e(t.stateIndex,n,i)))}}function s(t){t.offset=-1*t.states[t.stateIndex].page[0],o(t),i(t)}function o(t){const{track:e,offset:n,dragged:i}=t;e.style.transform=0===n?`translate3d(${i}px,0px,0px)`:`translate3d(  calc( ${i}px + ${n} * (var(--slide-width) + ${t.config.slideGap})),0px,0px)`}function r(t){t.track.style.transitionDuration=`${t.config.transitionDuration}ms`}function a(t){t.track.style.transitionDuration="0ms"}const l=10,c=()=>"ontouchstart"in window;function d(t){const e=this,n=e.slider;if(!n.isTransitioning){if(n.dragged=0,e.isScrolled=!1,e.startMouseClientX="touches"in t?t.touches[0].clientX:t.clientX,!("touches"in t)){(t.target||e).setPointerCapture(t.pointerId)}a(n),p(e,"addEventListener")}}function u(t){const e=this,n="touches"in t?t.touches[0].clientX:t.clientX,i=e.slider.dragged=n-e.startMouseClientX,s=Math.abs(i);s>5&&(e.slider.isDragging=!0),s>15&&t.preventDefault(),e.slider.dragged=i,o(e.slider),!e.isScrolled&&e.slider.config.loop&&i>l&&(e.isScrolled=!0,e.slider.prev())}function f(){const t=this,e=t.slider.dragged;t.slider.isDragging=!1,p(t,"removeEventListener"),t.slider.dragged=0,o(t.slider),r(t.slider),t.isScrolled||(e<-1*l?t.slider.next():e>l&&t.slider.prev())}const h=t=>t.preventDefault();function p(t,e){t[e]("contextmenu",f),c()?(t[e]("touchend",f),t[e]("touchmove",u)):(t[e]("pointerup",f),t[e]("pointermove",u))}const g={slideGap:"20px",slidesToScroll:1,slidesToShow:1,loop:!0,enableAutoplay:!1,stopAutoplayOnInteraction:!0,autoplayInterval:3e3,autoplayDirection:"to left",enablePagination:!0,transitionDuration:300,transitionTimingFunction:"ease",draggable:!0};function v(t){const e={...g};for(const n in t)if(window.matchMedia(n).matches){const i=t[n];for(const t in i)e[t]=i[t]}return e}function S(){const t=this.index,e=this.slider,n=e.stateIndex,i=e.config.loop,s=Math.abs(t-n),o=e.states.length-s,r=s>e.states.length/2&&i;t>n?r?e.prev(o):e.next(s):r?e.next(o):e.prev(s)}function m(t,e=t.config.transitionDuration){t.isTransitioning=!0,setTimeout((()=>{t.isTransitioning=!1}),e)}function x(e,n){const i=e.el.classList,s=e.stateIndex,o=e.paginationButtons;e.config.loop||(0===s?i.add(t):i.remove(t),s===e.states.length-1?i.add("end"):i.remove("end")),o&&e.config.enablePagination&&(o[n].classList.remove("active"),o[s].classList.add("active"))}function y(e,i){const s=i.track;i.slides=s.children,i.offset=0,i.config=e,n(i,i.totalSlides,e),e.loop||i.el.classList.add(t),e.enableAutoplay&&!e.loop&&(e.enableAutoplay=!1),s.style.transitionProperty="transform",s.style.transitionTimingFunction=i.config.transitionTimingFunction,s.style.transitionDuration=`${i.config.transitionDuration}ms`;const{slidesToShow:r,slideGap:a}=i.config;i.el.style.setProperty("--slides-to-show",r+""),i.el.style.setProperty("--slide-gap",a),i.isStatic?i.el.classList.add("static"):e.draggable&&function(t){const e=t.track;e.slider=t;const n=c()?"touchstart":"pointerdown";e.addEventListener(n,d),e.addEventListener("click",(e=>{(t.isTransitioning||t.isDragging)&&(e.preventDefault(),e.stopImmediatePropagation(),e.stopPropagation())}),{capture:!0}),e.addEventListener("dragstart",h)}(i),function(t){if(!t.config.enablePagination||t.isStatic)return;const e=t.el.querySelector(".blaze-pagination");if(!e)return;t.paginationButtons=[];const n=t.states.length;for(let i=0;i<n;i++){const s=document.createElement("button");t.paginationButtons.push(s),s.textContent="",s.ariaLabel=`${i+1} of ${n}`,e.append(s),s.slider=t,s.index=i,s.onclick=S}t.paginationButtons[0].classList.add("active")}(i),function(t){const e=t.config;if(!e.enableAutoplay)return;const n="to left"===e.autoplayDirection?"next":"prev";t.autoplayTimer=setInterval((()=>{t[n]()}),e.autoplayInterval),e.stopAutoplayOnInteraction&&t.el.addEventListener(c()?"touchstart":"mousedown",(()=>{clearInterval(t.autoplayTimer)}),{once:!0})}(i),function(t){const e=t.el.querySelector(".blaze-prev"),n=t.el.querySelector(".blaze-next");e&&(e.onclick=()=>{t.prev()}),n&&(n.onclick=()=>{t.next()})}(i),o(i)}return class extends e{constructor(t,e){const n=t.querySelector(".blaze-track"),i=n.children,s=e?v(e):{...g};super(i.length,s),this.config=s,this.el=t,this.track=n,this.slides=i,this.offset=0,this.dragged=0,this.isDragging=!1,this.el.blazeSlider=this,this.passedConfig=e;const o=this;n.slider=o,y(s,o);let r=!1,a=0;window.addEventListener("resize",(()=>{if(0===a)return void(a=window.innerWidth);const t=window.innerWidth;a!==t&&(a=t,r||(r=!0,setTimeout((()=>{o.refresh(),r=!1}),200)))}))}next(t){if(this.isTransitioning)return;const e=super.next(t);if(!e)return void m(this);const[n,l]=e;x(this,n),m(this),function(t,e){const n=requestAnimationFrame;t.config.loop?(t.offset=-1*e,o(t),setTimeout((()=>{!function(t,e){for(let n=0;n<e;n++)t.track.append(t.slides[0])}(t,e),a(t),t.offset=0,o(t),n((()=>{n((()=>{r(t),i(t)}))}))}),t.config.transitionDuration)):s(t)}(this,l)}prev(t){if(this.isTransitioning)return;const e=super.prev(t);if(!e)return void m(this);const[n,l]=e;x(this,n),m(this),function(t,e){const n=requestAnimationFrame;if(t.config.loop){a(t),t.offset=-1*e,o(t),function(t,e){const n=t.slides.length;for(let i=0;i<e;i++){const e=t.slides[n-1];t.track.prepend(e)}}(t,e);const s=()=>{n((()=>{r(t),n((()=>{t.offset=0,o(t),i(t)}))}))};t.isDragging?c()?t.track.addEventListener("touchend",s,{once:!0}):t.track.addEventListener("pointerup",s,{once:!0}):n(s)}else s(t)}(this,l)}stopAutoplay(){clearInterval(this.autoplayTimer)}destroy(){this.track.removeEventListener(c()?"touchstart":"pointerdown",d),this.stopAutoplay(),this.paginationButtons?.forEach((t=>t.remove())),this.el.classList.remove("static"),this.el.classList.remove(t)}refresh(){const t=this.passedConfig?v(this.passedConfig):{...g};this.destroy(),y(t,this)}onSlide(t){return this.onSlideCbs||(this.onSlideCbs=new Set),this.onSlideCbs.add(t),()=>this.onSlideCbs.delete(t)}}}();

document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        const sliders = document.querySelectorAll("[id^=\"club420_slider\"]");
        
        sliders.forEach(function(slider) {
            new BlazeSlider(slider, {
                all: {
                    enableAutoplay: true,
                    autoplayInterval: 3000,
                    autoplayDirection: "to left",
                    stopAutoplayOnInteraction: true,
                    transitionDuration: 300,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    slideGap: "20px",
                    loop: true,
                    enablePagination: false,
                    transitionTimingFunction: "ease",
                    draggable: true
                },
                "(max-width: 900px)": {
                    slidesToShow: 2,
                    autoplayInterval: 4000,
                },
                "(max-width: 500px)": {
                    slidesToShow: 1.5,
                    autoplayInterval: 4000,
                },
            });
        });
        
    }, 1500);
});
</script>';
}
add_action('wp_footer', 'club420_blazeslider_scripts', 25);
