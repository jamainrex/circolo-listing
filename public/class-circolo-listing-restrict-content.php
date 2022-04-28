<?php

use  Carbon\CarbonInterface ;

class Circolo_Listing_Restrict_Content
{
    public  $protection_checks ;
    public  $protection_type ;
    public  $user_post_info ;
    public  $current_user ;
    public  $product_id ;
    protected  $post_id ;
    protected  $author_id;
    protected  $integrations ;
    protected  $should_track_pageview ;
    protected  $available_templates ;
    public final function __construct( $post_id = null, $should_track_pageview = true )
    {
        $this->should_track_pageview = $should_track_pageview;

        //The Post ID is null because we use this class for displaying shortcodes as well as within the loop
        if ( is_null( $post_id ) ) {
            $this->post_id = get_the_ID();
            $this->author_id = get_the_author_ID();
        } else {
            $this->post_id = $post_id;
            $this->author_id = get_post_field( 'post_author', $post_id );
        }
        
        $this->current_user = wp_get_current_user();
        $this->protection_checks = [
            'check_if_logged_in',
            'check_if_owner_user',
            'check_if_protected',
            'check_if_should_show_paywall',
            'check_if_admin_call',
            'check_if_purchased',
            'check_if_admin_user_have_access',
            'check_if_user_role_has_access',
            'check_if_has_access'
        ];
        $this->available_templates = [
            'expiration-status' => 'expiration-status.php',
            'pageview-status'   => 'pageview-status.php',
        ];
        $this->product_id = get_post_meta( $this->post_id, CIRCOLO_LISTING_SLUG . '_product_id', true );
        $this->protection_type = Circolo_Listing_Helper::is_protected( $this->post_id );
    }
    
    public function register_shortcodes()
    {
        add_shortcode( 'wc-circolo-listing-status', [ $this, 'process_status_shortcode' ] );
    }
    
    /**
     * Function used to set to not track page view
     *
     * @param  bool  $track
     */
    public function set_track_pageview( bool $track = true )
    {
        $this->should_track_pageview = $track;
    }
    
    /*
    |--------------------------------------------------------------------------
    | Protection Checks
    |--------------------------------------------------------------------------
    |
    | Each of these functions go along with the $protection_checks
    | We loop through each one and test
    |
    */
    public function check_if_admin_call() : bool
    {
        
        if ( is_admin() || !$this->post_id ) {
            return true;
        }
        
        return false;
    }
    
    public function check_if_admin_user_have_access() : bool
    {
        if ( is_super_admin() ) {
            return true;
        }
        
        return false;
    }
    
    public function check_if_user_role_has_access() : bool
    {
        $allowed_user_roles = [];
        $current_user_roles = $this->get_current_user_roles();
        foreach ( $current_user_roles as $role ) {
            if ( in_array( $role, $allowed_user_roles ) ) {
                return true;
            }
        }
        return false;
    }

    public function check_if_owner_user() : bool
    {
        if( $this->current_user->ID == $this->author_id ) {
            return true;
        }

        return false;
    }
    
    public function check_if_purchased() : bool
    {
        if ( wc_customer_bought_product( $this->current_user->user_email, $this->current_user->ID, trim( $this->product_id ) ) ) {
            return true;
        }

        return false;
    }
    
    public function check_if_logged_in() : bool
    {
        $logged_in = is_user_logged_in();
        return $logged_in;
    }
    
    public function check_if_has_access() : bool
    {
        switch ( $this->protection_type ) {
            case 'standard':
                // Since we already check to see if they purchased the product standard protection returns true all the time.
                // Since we already check to see if they purchased the product standard protection returns true all the time.
            case 'page-view':
                return $this->has_access_page_view_protection__premium_only();
            case 'expire':
                return $this->has_access_expiry_protection__premium_only();
        }
        return true;
    }
    
    public function check_if_protected() : bool
    {
        if ( !$this->protection_type ) {
            return false;
        }

        return true;
    }
    
    public function check_if_should_show_paywall() : bool
    {
        switch ( $this->protection_type ) {
            case 'standard':
            case 'page-view':
            case 'expire':
                return true;
        }
        return true;
    }
    
    public function can_user_view_content() : bool
    {
        $check_results = [];
        foreach ( (array) $this->protection_checks as $check ) {
            $check_results[$check] = $this->{$check}();
        }
        if ( $check_results['check_if_owner_user'] || $check_results['check_if_admin_call'] || !$check_results['check_if_protected'] || !$check_results['check_if_should_show_paywall'] || $check_results['check_if_admin_user_have_access'] || $check_results['check_if_user_role_has_access'] || $check_results['check_if_has_access'] ) {
            return false;
        }
        
        if ( isset( $_GET['wc_cl_debug'] ) && "true" === $_GET['wc_cl_debug'] ) {
            echo  '<pre>' ;
            echo  '<h5>Post ID = ' . $this->post_id . '</h5>' ;
            var_dump( $check_results );
            echo  '</pre>' ;
        }
        
        return true;
    }
    
    /**
     * @param $unfiltered_content
     *
     * @return string
     */
    public function show_paywall( $unfiltered_content ) : string
    {
        return '<div class="wc_ppp_paywall">' . $this->get_paywall_content( $unfiltered_content ) . '</div>';
    }
    
    /**
     * @param $unfiltered_content
     *
     * @return string
     */
    public function show_content( $unfiltered_content ) : string
    {
        $show_warnings = get_post_meta( $this->post_id, CIRCOLO_LISTING_SLUG . '_show_warnings', true );
        if ( 'expire' === $this->protection_type && $this->check_if_owner_user && !is_admin() && !$this->check_if_admin_user_have_access() && apply_filters( 'wc_pay_per_post_enable_javascript_expiration_refresh', true ) ) {
            $this->countdown_refresh();
        }
        
        if ( $show_warnings && $this->check_if_owner_user && !is_admin() ) {
            
            $position = apply_filters( 'wc_circolo-listing_show_warnings_position', 'top' );
            switch ( $this->protection_type ) {
                case 'page-view':
                    $template_file = do_shortcode( '[wc-circolo-listing-status template="page-view"]' );
                    break;
                case 'expire':
                    $template_file = do_shortcode( '[wc-circolo-listing-status template="expiration-status"]' );
                    break;
                default:
                    return $unfiltered_content;
            }
            
            if ( 'top' === $position ) {
                return $template_file . $unfiltered_content;
            } else {
                return $unfiltered_content . $template_file;
            }
        
        }
        
        return $unfiltered_content;
    }
    
    public function is_expired( $post_id ) : bool
    {
        return !$this->has_access_expiry_protection__premium_only( $post_id );
    }
    
    public function process_status_shortcode( $atts )
    {
        $template = 'pageview-status';
        if ( isset( $atts['template'] ) && array_key_exists( $atts['template'], $this->available_status_templates() ) ) {
            $template = $atts['template'];
        }
        switch ( $template ) {
            case 'pageview-status':
                return $this->shortcode_pageview_status( $template );
            case 'expiration-status':
                return $this->shortcode_expiration_status( $template );
        }
        return false;
        //invalid template
    }
    
    protected function get_current_user_roles() : array
    {
        return (array) $this->current_user->roles;
    }
    
    /**
     * @param $unfiltered_content
     *
     * @return string
     */
    protected function get_paywall_content( $unfiltered_content ) : string
    {
        global  $product_ids ;
        $default_paywall_content = get_option( CIRCOLO_LISTING_SLUG . '_restricted_content_default', _x( "<h1>Oops, Restricted Content</h1><p>We are sorry but this post is restricted to folks that have purchased this page.</p>[products ids='{{product_id}}']", 'wc_pay_per_post' ) );
        $override_paywall_content = get_post_meta( $this->post_id, CIRCOLO_LISTING_SLUG . '_restricted_content_override', true );
        $override_paywall_content = apply_filters(
            'wc_circolo_listing_override_paywall_content',
            $override_paywall_content,
            10,
            1
        );
        $paywall_content = ( empty($override_paywall_content) ? $default_paywall_content : $override_paywall_content );
        $return_content = Circolo_Listing_Helper::replace_tokens( $paywall_content, $product_ids, $unfiltered_content );
        return wpautop( do_shortcode( $return_content ) );
    }
    
    /**
     * @param $frequency
     * @param $date
     *
     * @return int
     */
    protected function get_time_difference( $frequency, $date ) : int
    {
        $current_time = Circolo_Listing_Helper::current_time();
        $diff_method = Circolo_Listing_Helper::carbon_diff_method( $frequency );
        return $date->copy()->{$diff_method}( $current_time, CarbonInterface::DIFF_RELATIVE_TO_NOW );
    }
    
    protected function countdown_refresh()
    {
        ?>
            <script>
                const countDownDate = new Date('<?php 
        echo  $this->user_post_info['expiration_date']->format( Circolo_Listing_Helper::date_time_format() ) ;
        ?>').getTime();
                const x = setInterval(function () {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;
                    //console.log('remaining', Math.floor(distance / 1000 / 60));
                    if (distance < 0) {
                        clearInterval(x)
                        location.reload()
                    }
                }, 1000);
            </script>
            <?php 
    }
    
    protected function shortcode_pageview_status( $template )
    {
        ob_start();
        $user_info = $this->user_post_info;
       
        $number_of_allowed_pageviews = get_post_meta( $this->post_id, CIRCOLO_LISTING_SLUG . '_page_view_restriction', true );
        /** @noinspection PhpIncludeInspection */
        require Circolo_Listing_Helper::locate_template( $this->available_templates[$template], '', CIRCOLO_LISTING_SLUG . 'public/partials/' );
        return ob_get_clean();
    }
    
    protected function shortcode_expiration_status( $template )
    {
        $user_info = $this->user_post_info;
        
        if ( !is_null( $user_info['last_purchase_date'] ) ) {
            ob_start();
            /** @noinspection PhpIncludeInspection */
            require Circolo_Listing_Helper::locate_template( $this->available_templates[$template], '', WC_PPP_PATH . 'public/partials/' );
            return ob_get_clean();
        }
        
        return false;
    }
    
    /**
     * @return string[]
     */
    private function available_status_templates() : array
    {
        return [
            'pageview-status'   => 'pageview-status.php',
            'expiration-status' => 'expiration-status.php',
        ];
    }

}