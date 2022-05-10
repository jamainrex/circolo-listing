<?php

use CIRCOLO\Circolo_Listing_Restrict_Content;

class Circolo_Listing_Shortcodes
{
    public static function register_shortcodes()
    {
        add_shortcode( 'woocommerce-circolo-listing', [ __CLASS__, 'process_shortcode' ] );
        add_shortcode( 'wc-circolo-listing', [ __CLASS__, 'process_shortcode' ] );
        add_shortcode( 'wc-circolo-listing-category-products', [ __CLASS__, 'category_products' ] );
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
        
        //echo '<pre>'.print_r($products, true).'</pre>';
        require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/shortcode-products.php';
        wp_reset_postdata();
    
        return '<div class="woocommerce">' . ob_get_clean() . '</div>';
    }

}