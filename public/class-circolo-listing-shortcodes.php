<?php

use CIRCOLO\Circolo_Listing_Restrict_Content;

class Circolo_Listing_Shortcodes
{
    public static function register_shortcodes()
    {
        add_shortcode( 'woocommerce-circolo-listing', [ __CLASS__, 'process_shortcode' ] );
        add_shortcode( 'wc-circolo-listing', [ __CLASS__, 'process_shortcode' ] );
        add_shortcode( 'wc-circolo-listing-category-products', [ __CLASS__, 'category_products' ] );
        add_shortcode( 'wc-circolo-listing-post-details-form', [ __CLASS__, 'post_detail_form' ] );
        add_shortcode( 'circolo-listing-marketplace', [ __CLASS__, 'marketplace_sc' ] );
        $restrict = new Circolo_Listing_Restrict_Content();
        $restrict->register_shortcodes();
    }
    
    public static function process_shortcode( $atts )
    {
        global  $product_ids;
        $template = 'purchased';
        $orderby = 'date';
        $order = 'DESC';
        $transient = '';
        $bypass_transients = false;
        if ( isset( $atts['bypass_transients'] ) && ($atts['bypass_transients'] === 'TRUE' || $atts['bypass_transients'] === true || $atts['bypass_transients'] === 'true') ) {
            $bypass_transients = true;
        }
        if ( isset( $atts['template'] ) && array_key_exists( $atts['template'], self::available_templates() ) ) {
            $template = $atts['template'];
        }
        $custom_post_types = ['circolo_listings'];
        //$custom_post_types = ( empty($custom_post_types) ? [] : $custom_post_types );
        // if ( !is_array( $custom_post_types ) ) {
        //     $custom_post_types = explode( ',', $custom_post_types );
        // }
        $args = [
            'orderby'     => $orderby,
            'order'       => $order,
            'nopaging'    => true,
            'meta_query'  => [ [
            'key'     => CIRCOLO_LISTING_SLUG . '_product_id',
            'value'   => '',
            'compare' => '!=',
        ] ],
            'post_status' => 'publish',
            'post_type'   => $custom_post_types,
        ];
        $get_ppp_args = apply_filters( 'wc_circolo_listing_args', $args );

        $ppp_posts = Circolo_Listing_Helper::get_protected_posts( $get_ppp_args, $transient, $bypass_transients );
        ob_start();
        switch ( $template ) {
            case 'has_access':
                self::shortcode_has_access( $template, $ppp_posts );
                break;
            case 'purchased':
                self::shortcode_purchased( $template, $ppp_posts );
                break;
            case 'remaining':
                self::shortcode_remaining( $template, $ppp_posts );
                break;
            case 'all':
                self::shortcode_all( $template, $ppp_posts );
                break;
        }
        return ob_get_clean();
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_purchased( $template, $ppp_posts )
    {
        $purchased = [];
        
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( Circolo_Listing_Helper::has_purchased( $post->ID, false ) ) {
                    $purchased[] = $post;
                }
            }
            require Circolo_Listing_Helper::locate_template( self::available_templates()[$template], '', CIRCOLO_LISTING_PATH . 'public/partials/' );
        }
    
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_has_access( $template, $ppp_posts )
    {
        $purchased = [];
        
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                
                if ( Circolo_Listing_Helper::has_access( $post->ID, false ) ) {
                    $purchased[] = $post;
                }
            }
            require Circolo_Listing_Helper::locate_template( self::available_templates()[$template], '', CIRCOLO_LISTING_PATH . 'public/partials/' );
        }
    
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_remaining( $template, $ppp_posts )
    {
        $remaining = [];
        
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( !Circolo_Listing_Helper::has_access( $post->ID, false ) ) {
                    $remaining[] = $post;
                }
            }
            require Circolo_Listing_Helper::locate_template( self::available_templates()[$template], '', CIRCOLO_LISTING_PATH . 'public/partials/' );
        }
    
    }
    
    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_all( $template, $ppp_posts )
    {
        require Circolo_Listing_Helper::locate_template( self::available_templates()[$template], '', CIRCOLO_LISTING_PATH . 'public/partials/' );
    }
    
    /**
     * @param $post_id
     *
     * This returns the INVERSE of can_user_view_content()
     *
     * @return bool
     */
    protected static function has_access( $post_id ) : bool
    {
        $restrict = new Circolo_Listing_Restrict_Content( $post_id );
        $restrict->set_track_pageview( false );
        if ( apply_filters( 'wc_circolo_listing_hide_delay_restricted_posts_when_paywall_should_not_be_shown', false ) ) {
            /**
             * We have the following check because if you have delay protection enabled and the post is not suppose to show the paywall for
             * a year after publishing, the posts that show in the purchased content tab or purchased shortcode will output all of the ppp posts
             * that have delay protection even though they are not suppose to show paywall yet.
             */
            if ( 'delay' === Circolo_Listing_Helper::is_protected( $post_id ) ) {
                return $restrict->check_if_should_show_paywall();
            }
        }
        return !$restrict->can_user_view_content();
    }
    
    private static function available_templates() : array
    {
        return [
            'purchased'  => 'shortcode-purchased.php',
            'has_access' => 'shortcode-has_access.php',
            'all'        => 'shortcode-all.php',
            'remaining'  => 'shortcode-remaining.php',
        ];
    }

    public function category_products( $atts ) {

        global $woocommerce_loop;
    
        extract(shortcode_atts(array(
            'category'  => empty( $_GET['category'] ) ? '' : wc_clean( wp_unslash( $_GET['category'] ) )
        ), $atts));

        wp_enqueue_script( CIRCOLO_LISTING_PLUGIN_NAME . '-category-products-js', plugin_dir_url( __FILE__ ) . 'js/circolo-listing-products.js', array( 'jquery' ), CIRCOLO_LISTING_VERSION, false );
        ob_start();
    
        $products = Circolo_Listing_Helper::get_category_products( $category ); // new WP_Query( $args );
        
        //echo '<pre>'.print_r($products[0]->getData(), true).'</pre>';
        require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/shortcode-products.php';
        wp_reset_postdata();
    
        return '<div class="woocommerce">' . ob_get_clean() . '</div>';
    }

    public function post_detail_form( $atts ) {
    
        extract(shortcode_atts(array(
            'post_id'  => empty( $_GET['post_id'] ) ? '' : wc_clean( wp_unslash( $_GET['post_id'] ) )
        ), $atts));

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script( CIRCOLO_LISTING_PLUGIN_NAME . '-post-details-js', plugin_dir_url( __FILE__ ) . 'js/circolo-listing-post-details.js', array( 'jquery' ), CIRCOLO_LISTING_VERSION, false );
        wp_enqueue_style( CIRCOLO_LISTING_PLUGIN_NAME . '-post-details-css', plugin_dir_url( __FILE__ ) . 'css/circolo-listing-post-details.css', array(), CIRCOLO_LISTING_VERSION, 'all' );
        ob_start();

        $errors = [];
        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "f_edit_post" && isset($_POST['pid'])) {
            //get the old post:
            $post_to_edit = get_post((int)$_POST['pid']); 
        
            //echo '<pre>'.print_r($_FILES, true).'</pre>';
            //exit();
            //do you validation
            //...
            //...
        
        
            // Add the content of the form to $post_to_edit array
            //$post_to_edit['post_title'] = $_POST['title'];
            //$post_to_edit['post_content'] = $_POST['description'];
            $args = [
                'ID' => (int)$_POST['pid'],
                'post_title' => $_POST['title'],
                'post_content' => $_POST['description']
            ];
    
            //save the edited post and return its ID
            $pid = wp_update_post($args); 

            //image upload
                if (!function_exists('wp_generate_attachment_metadata')){
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
                }
                if ($_FILES) {
                    foreach ($_FILES as $file => $array) {
                        if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                            $errors[] = "upload error : " . $_FILES[$file]['error'];
                        }
                        $attach_id = media_handle_upload( $file, (int)$_POST['pid'] );
                    }   
                }
                if ($attach_id > 0){
                    //and if you want to set that image as Post  then use:
                    update_post_meta((int)$_POST['pid'],'_thumbnail_id',$attach_id);
                }

            //if( isset( $_FILES['upload'] ) )
            //   Circolo_Listing_Helper::add_custom_image((int)$_POST['pid'], $_FILES['upload']); /*Call image uploader function*/

            if($pid && empty($errors)) {
                wp_redirect( site_url( 'create-a-post' ).'/review-for-approval' );
                exit;
            }
        }

        $cart = WC()->cart->get_cart();
        $cart_item = current($cart);
        $product_id = $cart_item['product_id'];
        $product = wc_get_product( $product_id );

        $categories = get_the_terms( $product->get_id(), 'product_cat' );

        //echo '<pre>'.print_r($categories[0]->slug, true).'</pre>';

        $posts = Circolo_Listing_Helper::get_post_associated_with_product_id( $product_id );
        $circolo_listing = $posts[0];

        $listingImages = [];
        for ($x = 0; $x <= 4; $x++) {
            $image = get_post_meta($circolo_listing->ID, '_image_'.$x);
            //echo "Is Error: " . is_wp_error( $image[0] );
            if( !empty($image[0]) && !is_wp_error( $image[0] ) ){
                $listingImages[$x] = $image;
            }
                //echo '<pre>'.print_r($image, true).'</pre>';

                //$listingImages[$x] = $image;
        }

        
        
        require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/shortcode-post-details-form.php';
        wp_reset_postdata();
    
        return '<div class="woocommerce">' . ob_get_clean() . '</div>';
    }

    public function marketplace_sc($atts ) {
        
        extract(shortcode_atts(array(
            'orderby' => 'date',
            'order' => 'DESC',
            'category' => empty( $_GET['category'] ) ? 'all' : wc_clean( wp_unslash( $_GET['category'] ) )
        ), $atts));

        $custom_post_types = ['circolo_listings'];
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => $custom_post_types,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'paged' => $paged,
            'meta_query' =>
            [ 
                'relation' => 'AND',
                [
                    'key'     => CIRCOLO_LISTING_SLUG . '_date_approved',
                    'value'   => '',
                    'compare' => '!=',
                ] 
            ],
        );

        if( $categoy != 'all' && in_array( $category, ['goods', 'poperty', 'experiences', 'services'] ) ) {
            $args['category_name'] = $category;
        }

        wp_enqueue_style( CIRCOLO_LISTING_PLUGIN_NAME . '-marketplace-css', plugin_dir_url( __FILE__ ) . 'css/circolo-listing-marketplace.css', array(), CIRCOLO_LISTING_VERSION, 'all' );
        ob_start();

        require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/marketplace-header.php';
        $loop = new WP_Query( $args );
        //echo '<pre>'.print_r($loop, true).'</pre>';
        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) : $loop->the_post();
            // YOUR CODE
            $date_approved = get_post_meta( get_the_ID(), CIRCOLO_LISTING_SLUG . '_date_approved', true );
            $date_expire = Circolo_Listing_Helper::calculate_expiry_date($date_approved);
            $date_remaining = Circolo_Listing_Helper::remaining_days($date_expire);

            if( $date_remaining < 0 )
                continue;

            $listing_categories = get_the_category();
            $listing_category = isset( $listing_categories[0] ) ? $listing_categories[0]->name : '';
            $attachment = get_post_meta(get_the_ID(), '_thumbnail_id');
            $featured_image = isset( $attachment[0] ) ? wp_get_attachment_url( $attachment[0] ) : '';
            $days_ago = Circolo_Listing_Helper::days_ago( $date_approved );
            $day_time_ago = Circolo_Listing_Helper::day_time_ago( $date_approved );
            //echo '<pre>'.print_r([ 'image' => $featured_image, 'date_approved'=>$date_approved, 'expire'=>$date_expire, 'remaining' => $date_remaining, 'category' => $listing_category ], true).'</pre>';
            //echo '<pre>'.print_r($day_time_ago, true).'</pre>';
            //echo '<pre>'.print_r(get_post_meta( get_the_ID() ), true).'</pre>';
            require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/marketplace-item.php';
            endwhile;

            $total_pages = $loop->max_num_pages;

            if ($total_pages > 1){

                $current_page = max(1, get_query_var('paged'));

                echo paginate_links(array(
                    'base' => get_pagenum_link(1) . '%_%',
                    'format' => '/page/%#%',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text'    => __('« prev'),
                    'next_text'    => __('next »'),
                ));
            }    
        }

        wp_reset_postdata();
        return '<div class="marketplace-wrapper">' . ob_get_clean() . '</div>';
    }
}