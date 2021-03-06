<?php

use  Carbon\Carbon;
use CIRCOLO\Circolo_Listing_Restrict_Content;
use CIRCOLO\Circolo_Listing_Favorites;

class Circolo_Listing_Helper extends Circolo_Listing
{
    /**
     * @var array
     */
    public static  $protection_types = array(
        'standard',
        'page-view',
        'expire'
    ) ;
    /**
     * @param $post_id
     *
     * @return bool|string
     */
    public static function is_protected( $post_id = null )
    {
        if ( is_null( $post_id ) ) {
            $post_id = get_the_ID();
        }

        $selected = (int) get_post_meta( $post_id, CIRCOLO_LISTING_SLUG . '_product_id' );
        $type = 'post';
        $meta = null;
        //echo '<pre>'.print_r([$post_id, $selected], true).'</pre>';
        if ( !$selected ) {
            return false;
        }
        return self::get_protection_type( $post_id, $type, $meta );
    }
    
    public static function get_protection_type( $id, $type = 'post', $meta = null ) : string
    {
        switch ( $type ) {
            case 'post':
            case 'elementor':
                //TODO this will need to be updated
                $page_view_restriction_enable = (bool) get_post_meta( $id, CIRCOLO_LISTING_SLUG . '_page_view_restriction_enable', true );
                $expire_restriction_enable = (bool) get_post_meta( $id, CIRCOLO_LISTING_SLUG . '_expire_restriction_enable', true );
                break;
        }
        $protection = 'standard';
        if ( $page_view_restriction_enable ) {
            $protection = 'page-view';
        }
        if ( $expire_restriction_enable ) {
            $protection = 'expire';
        }

        return $protection;
    }
    
    /**
     * @param null $post_id
     * @param bool $track_page_view *
     *
     * @return bool
     * The can_user_view_content function returns on whether or not the user should see the paywall.
     * For this that is why we are returning the inverse of the result.
     */
    public static function has_access( $post_id = null, bool $track_page_view = true ) : bool
    {
        $restrict = new Circolo_Listing_Restrict_Content( $post_id, $track_page_view );
        return !$restrict->can_user_view_content();
    }
    
    /**
     * Checks if the user has purchased a product associated with the post
     *
     * @param null $post_id
     * @param bool $track_page_view
     *
     * @return bool
     */
    public static function has_purchased( $post_id = null, bool $track_page_view = true ) : bool
    {
        $restrict = new Circolo_Listing_Restrict_Content( $post_id, $track_page_view );
        return $restrict->check_if_purchased();
    }
    
    /**
     * @return string
     */
    public static function get_no_access_content() : string
    {
        $restrict = new Circolo_Listing_Restrict_Content();
        return $restrict->show_paywall( get_the_content() );
    }
    
    /**
     * @param $type
     *
     * @return bool|string
     */
    public static function protection_display_icon( $type )
    {
        if ( in_array( $type, self::$protection_types, true ) ) {
            switch ( $type ) {
                case 'standard':
                    return '<span class="dashicons dashicons-post-status" title="Standard Purchase Protection" style="color:green"></span>';
                case 'page-view':
                    return '<span class="dashicons dashicons-visibility" title="Page View Protection" style="color:green"></span>';
                case 'expire':
                    return '<span class="dashicons dashicons-backup" title="Expiry Protection" style="color:green"></span>';
            }
        }
        return false;
    }
    
    /**
     * @return Carbon
     */
    public static function current_time() : Carbon
    {
        return Carbon::createFromTimestamp( current_time( 'timestamp' ) );
    }
    
    /**
     * @return string
     */
    public static function date_time_format() : string
    {
        return get_option( 'date_format', true ) . ' ' . get_option( 'time_format', true );
    }

    public static function calculate_expiry_date( $datetime ) : Carbon
    {
        return Carbon::parse( $datetime )->addDays(90);
    }

    public static function get_date_subDays( $days = 2 ) : Carbon
    {
        return Circolo_Listing_Helper::current_time()->subDays( $days );
    }

    public static function days_ago( $datetime )
    {
        return Carbon::parse( $datetime )->diffInDays( Circolo_Listing_Helper::current_time() , false);
    }

    public static function day_time_ago( $datetime, $format = 'U', $gmt = false )
    {
        $time = wp_date( $format, $datetime->getTimestamp(), $gmt ? new DateTimeZone( 'UTC' ) : null );
        return sprintf( esc_html__( '%s ago', 'textdomain' ), human_time_diff( $time, current_time( 'timestamp' ) ) );
    }

    public static function remaining_days( $datetime )
    {
        return Carbon::parse( Carbon::createFromTimestamp( current_time( 'timestamp' ) ))->diffInDays( $datetime , false);
    }

    public static function to_date_timestamp( $datetime )
    {
        return Carbon::parse( $datetime )->timestamp;
    }

    public static function get_users()
    {
        $args = [
            'orderby'=>'user_nicename'
        ];
        return get_users( $args );
    }

    public static function get_countries()
    {
        if( ! class_exists('WC_Countries') )
            return [];

        return WC()->countries->get_countries();
    }
    
    public static function get_protected_posts( $args = null, $transient = 'posts', $bypass_transient = false ) : array
    {
        $transient = CIRCOLO_LISTING_SLUG . '_' . $transient;
        
        if ( is_null( $args ) ) {
            $custom_post_types = ['circolo_listings'];;
            // $custom_post_types = ( empty($custom_post_types) ? [] : $custom_post_types );
            // if ( !is_array( $custom_post_types ) ) {
            //     $custom_post_types = explode( ',', $custom_post_types );
            // }
            $args = [
                'orderby'     => 'post_date',
                'order'       => 'DESC',
                'nopaging'    => true,
                'post_status' => 'publish',
                'post_type'   => $custom_post_types,
            ];
            $args['meta_query'] = [ [
                'key'     => CIRCOLO_LISTING_SLUG . '_product_id',
                'value'   => '',
                'compare' => '!=',
            ] ];
        }
        
        
        if ( $bypass_transient ) {
            $ppp_posts = get_posts( $args );
        } else {
            $ppp_posts = get_transient( $transient );
            
            if ( false === $ppp_posts ) {
                $ppp_posts = get_posts( $args );
                set_transient( $transient, $ppp_posts, apply_filters( 'wc_circolo_listing_transient_time', DAY_IN_SECONDS ) );
            }
        
        }
        
        return (array) $ppp_posts;
    }
    
    public static function get_posts_associated_with_product_id( $product_id ) : array
    {
        $custom_post_types = get_option( CIRCOLO_LISTING_SLUG . '_custom_post_types', [] );
        $custom_post_types = ( empty($custom_post_types) ? [] : $custom_post_types );
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $args = [
            'orderby'     => 'post_date',
            'order'       => 'DESC',
            'nopaging'    => true,
            'post_status' => 'publish',
            'post_type'   => $custom_post_types,
        ];
        $args['meta_query'] = [ [
            'key'     => CIRCOLO_LISTING_SLUG . '_product_id',
            'value'   => sprintf( '^%1$s$|s:%2$u:"%1$s";', $product_id, strlen( $product_id ) ),
            'compare' => 'REGEXP',
        ] ];
        $posts = get_posts( apply_filters( 'wc_circolo_listing_woocommerce_email_args', $args ) );
        $protected_content = [];
        
        if ( $posts ) {
            $interface = 'default';
            switch ( $interface ) {
                case "default":
                default:
                    $protected_content = self::get_posts_associated_with_product_id_interface( $posts );
                    break;
            }
        }
        
        return $protected_content;
    }

    public static function get_post( $post_id )
    {
        $custom_post_type = 'circolo_listings';
        return get_post( $post_id, ARRAY_A);
    }

    public static function get_post_associated_with_product_id( $product_id, $status = 'draft' ) : array
    {
        $custom_post_type = 'circolo_listings';

        $args = [
            'post_status' => $status,
            'post_type'   => $custom_post_type,
            'author' => get_current_user_id(),
            'meta_query' => [
                [
                    'key' => 'circolo_listing_product_id',
                    'value' => $product_id,
                    'compare' => '='
                ]
            ]
        ];

        $posts = get_posts( apply_filters( 'wc_circolo_listing_args', $args ) );
    
        return $posts;
    }

    public static function save_new_listing( $product_id, $cat_slug ) {
        $category = Circolo_Listing_Helper::get_listing_category_by_slug($cat_slug);
        //return $category;
        $has_draft_post_with_product_id = Circolo_Listing_Helper::get_post_associated_with_product_id( $product_id );
        //return $has_draft_post_with_product_id;
		// create new post
		if( empty( $has_draft_post_with_product_id ) ){
			return Circolo_Listing_Helper::create_listing( $product_id, $category->term_id );
		}

        wp_set_post_categories($product_id, [$category]);
		return $has_draft_post_with_product_id[0]->ID;
    }

    public static function create_listing( $product_id, $category ) {
        $custom_post_type = 'circolo_listings';
        $product = wc_get_product( $product_id );

        // Create post object
		$args = array(
			'post_title'    => ' ',
            'post_content'  => ' ',
            'post_type'     => $custom_post_type,
			'post_status'   => 'draft',
			'post_category' => array( $category )
		);
		
		// Insert the post into the database
		if( $post_id = wp_insert_post( $args, true ) ) {
            update_post_meta(
				$post_id,
				'circolo_listing_product_id',
				$product_id
			);

            return $post_id;
        }

        return false;
    }

    public static function get_listing_category_by_slug( $slug ) {
        $category = get_term_by('slug', $slug, 'category');

        return $category;
    }

    public static function user_save_listing() {
        $errors = [];
        if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "circolo_listing_save" && isset($_POST['pid'])) {
            //get the old post:
            $post_to_edit = get_post((int)$_POST['pid']); 
			$owner_id = get_current_user_id();

            $args = [
                'ID' => (int)$_POST['pid'],
                'post_title' => $_POST['title'],
                'post_content' => $_POST['description'],
				'post_excerpt' => $_POST['short_description'],
				'post_author'  => $owner_id,
            ];
    
            //save the edited post and return its ID
            $pid = wp_update_post($args); 

			update_post_meta(
				(int)$_POST['pid'],
				'circolo_listing_owner',
				$owner_id
			);

            //image upload
                if (!function_exists('wp_generate_attachment_metadata')){
                    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
                }
                if ($_FILES) {
					for( $x = 0; $x <= 4; $x++ ){
						if( isset( $_POST['file-'.$x] ) )
							update_post_meta((int)$_POST['pid'],'_image_'.$x, $_POST['file-'.$x]);
						else
							update_post_meta((int)$_POST['pid'],'_image_'.$x, "");
					}

                    foreach ($_FILES as $file => $array) {
						if( $file == 'thumbnail' )
							continue;

						if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
							//$errors[] = "upload error : " . $_FILES[$file]['error'];
							continue;
						}
		
						$fname = explode('-', $file);

                         
                        $attach_id = media_handle_upload( $file, (int)$_POST['pid'] );

						if( $attach_id > 0 && isset( $fname[1] ) && is_numeric( $fname[1] ) ) {
							update_post_meta((int)$_POST['pid'],'_image_'.$fname[1],$attach_id);
						}

						if ($attach_id > 0 && isset( $fname[1] ) && is_numeric( $fname[1] ) && $fname[1] == 0){
							//and if you want to set that image as Post  then use:
							update_post_meta((int)$_POST['pid'],'_thumbnail_id',$attach_id);
						}
                    }   
                }
                
        }

        return $errors;
    }
    
    public static function md_array_diff( $arraya, $arrayb )
    {
        foreach ( $arraya as $keya => $valuea ) {
            if ( in_array( $valuea, $arrayb ) ) {
                unset( $arraya[$keya] );
            }
        }
        return $arraya;
    }
    
    public static function move_to_top( &$array, $key )
    {
        $temp = [
            $key => $array[$key],
        ];
        unset( $array[$key] );
        $array = $temp + $array;
    }
    
    public static function get_the_excerpt( $post_id ) : string
    {
        return get_the_excerpt( $post_id );
    }
    
    protected static function get_posts_associated_with_product_id_interface( $posts ) : array
    {
        $protected_content = [];
        foreach ( $posts as $post ) {
            $protected_content[] = [
                'post_id'    => $post->ID,
                'post_title' => $post->post_title,
                'post_url'   => get_permalink( $post->ID ),
            ];
        }
        return $protected_content;
    }
    
    public static function locate_template( $template_name, $template_path = '', $default_path = '' )
    {
        if ( !$template_path ) {
            $template_path = CIRCOLO_LISTING_TEMPLATE_PATH;
        }
        if ( !$default_path ) {
            $default_path = CIRCOLO_LISTING_PATH;
        }
        // Look within passed path within the theme - this is priority.
        $template = locate_template( [ trailingslashit( $template_path ) . $template_name, $template_name ] );
        // Get default template/.
        if ( !$template ) {
            $template = $default_path . $template_name;
        }
        // Return what we found.
        return apply_filters(
            'wc_circolo_listing_locate_template',
            $template,
            $template_name,
            $template_path
        );
    }
    
    public static function get_all_products() : array
    {
        $args = [
            'post_type'   => [ 'product' ],
            'orderby'     => 'title',
            'post_status' => 'publish',
            'order'       => 'ASC',
            'nopaging'    => true,
        ];
        $products = get_posts( apply_filters( 'wc_circolo_listing_all_product_args', $args ) );
        //TODO: Document this filter
        $return = [];
        foreach ( $products as $product ) {
            $return[] = [
                'ID'         => $product->ID,
                'post_title' => $product->post_title,
            ];
        }
        return $return;
    }

    public static function get_category_products( $category = '' ) : array
    {
        if( isset( $_GET['category'] ) && is_numeric( $_GET['category'] ) )
            $category = wc_clean( wp_unslash( $_GET['category'] ) );

            // $args = array(
            //     'post_type' => 'product',
            //     'post_status' => 'publish',
            //     'ignore_sticky_posts'   => 1,
            //     //'posts_per_page' => $per_page,
            //     'orderby' => 'title',
            //     'order' => 'ASC',
            //     'tax_query'             => array(
            //         array(
            //             'taxonomy'      => 'product_cat',
            //             'terms'         => array( esc_attr($category) ),
            //             'field'         => 'id',
            //             'operator'      => 'IN'
            //         )
            //     )
            // );

            $args = array(
                'status' => 'publish',
                'limit' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'category' => array( esc_attr($category) ),
            );
            
            //echo '<pre>'.print_r($args, true).'</pre>';
            $products = wc_get_products( apply_filters( 'wc_circolo_listing_all_product_args', $args ) );
            
            return $products;
    }

    public static function get_all_categories() : array
    {
        $taxonomy     = 'product_cat';
        $orderby      = 'name';  
        $show_count   = 0;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no  
        $title        = '';  
        $empty        = 0;

        $args = array(
                'taxonomy'     => $taxonomy,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty
        );
        $all_categories = get_categories( $args );

        echo '<pre>'.print_r($all_categories, true).'</pre>';
        return $all_categories;
    }
    
    public static function get_virtual_products() : array
    {
        //TODO Check/test this. Document in 3.0.0
        $args = [
            'post_type'   => [ 'product' ],
            'post_status' => 'publish',
            'orderby'     => 'title',
            'order'       => 'ASC',
            'nopaging'    => true,
            'meta_query'  => [
            'relation' => 'OR',
            [
            'key'     => '_downloadable',
            'value'   => 'yes',
            'compare' => '=',
        ],
            [
            'key'     => '_virtual',
            'value'   => 'yes',
            'compare' => '=',
        ],
            [
            'key'     => '_downloadable',
            'value'   => '1',
            'compare' => '=',
        ],
            [
            'key'     => '_virtual',
            'value'   => '1',
            'compare' => '=',
        ],
        ],
        ];
        $products = get_posts( apply_filters( 'wc_circolo_listing_virtual_product_args', $args ) );
        //echo '<pre>'.print_r($products, true).'</pre>';
        $return = [];
        foreach ( $products as $product ) {
            
            $return[] = [
                'ID'         => $product->ID,
                'post_title' => $product->post_title,
            ];
        }
        return $return;
    }
    
    public static function get_products()
    {
        //Circolo_Listing_Helper::get_all_categories();
        return apply_filters( 'wc_circolo_listing_get_virtual_products', Circolo_Listing_Helper::get_virtual_products() );
        
        // $only_show_virtual_products = (bool) get_option( CIRCOLO_LISTING_SLUG . '_only_show_virtual_products', false );
        
        // if ( $only_show_virtual_products ) {
        //     return apply_filters( 'wc_circolo_listing_get_virtual_products', Woocommerce_Pay_Per_Post_Helper::get_virtual_products() );
        // } else {
        //     return apply_filters( 'wc_circolo_listing_get_all_products', Woocommerce_Pay_Per_Post_Helper::get_all_products() );
        // }
    
    }
    
    public static function replace_tokens( $paywall_content, $product_ids, $unfiltered_content = null )
    {
        $excerpt = apply_filters( 'wc_circolo_listing_modify_excerpt', wp_trim_words( $unfiltered_content ) );
        $parent_id = null;
        if ( isset( $product_ids[0] ) ) {
            $parent_id = wp_get_post_parent_id( $product_ids[0] );
        }
        if ( is_archive() || is_home() || is_front_page() ) {
            //Get Product IDs
            $product_id = get_post_meta( get_the_ID(), CIRCOLO_LISTING_SLUG . '_product_id', true );
            $product_ids = [ $product_id ];
        }
        $return_content = str_replace( '{{product_id}}', implode( ',', (array) $product_ids ), $paywall_content );
        $return_content = str_replace( '{{parent_id}}', $parent_id, $return_content );
        return $return_content;
    }
    
    /**
     * @param $method
     *
     * @return string
     */
    public static function carbon_add_method( $method ) : string
    {
        $methods = [
            'minute' => 'addMinutes',
            'hour'   => 'addHours',
            'day'    => 'addDays',
            'week'   => 'addWeeks',
            'month'  => 'addMonths',
            'year'   => 'addYears',
        ];
        return $methods[$method];
    }
    
    /**
     * @param $method
     *
     * @return string
     */
    public static function carbon_diff_method( $method ) : string
    {
        $methods = [
            'minute' => 'diffInMinutes',
            'hour'   => 'diffInHours',
            'day'    => 'diffInDays',
            'week'   => 'diffInWeeks',
            'month'  => 'diffInMonths',
            'year'   => 'diffInYears',
        ];
        return $methods[$method];
    }
    
    public static function recursive_array_search( $needle, array $haystack )
    {
        $matches = [];
        $iterator = new RecursiveArrayIterator( $haystack );
        $recursive = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::SELF_FIRST );
        foreach ( $recursive as $key => $value ) {
            if ( $key === $needle ) {
                $matches[] = $value;
            }
        }
        $return = [];
        foreach ( $matches as $match => $mvalue ) {
            $return = array_merge( $return, $mvalue );
        }
        return $return ?? false;
    }
    
    public static function flatten( $array ) : array
    {
        $return = [];
        foreach ( $array as $key => $value ) {
            $return[] = $value[0];
        }
        return $return;
    }
    
    public static function array_values_recursive( $array ) : array
    {
        $flat = array();
        foreach ( $array as $value ) {
            
            if ( is_array( $value ) ) {
                $flat = array_merge( $flat, self::array_values_recursive( $value ) );
            } else {
                $flat[] = $value;
            }
        
        }
        return $flat;
    }
    
    public static function get_post_types() : array
    {
        $user_included_post_types = get_option( CIRCOLO_LISTING_SLUG . '_custom_post_types', [] );
        if ( '' === $user_included_post_types || empty($user_included_post_types) ) {
            $user_included_post_types = [];
        }

        $user_included_post_types = array_merge( $user_included_post_types, ['circolo_listings'] );

        return (array) $user_included_post_types;
    }
    
    public static function allowed_roles_for_meta_box() : bool
    {
        $allowed_roles = apply_filters( 'wc_circolo_listing_allowed_roles_for_meta_box', [] );
        
        if ( count( $allowed_roles ) == 0 ) {
            $allow_meta = true;
        } else {
            $allow_meta = false;
            $user = wp_get_current_user();
            
            if ( !is_null( $user ) ) {
                $user_roles = (array) $user->roles;
                foreach ( $user_roles as $role ) {
                    if ( array_key_exists( $role, $allowed_roles ) ) {
                        $allow_meta = true;
                    }
                }
            }
        
        }
        
        return $allow_meta;
    }
    
    public static function search_protected_posts_by_id( $id, $array )
    {
        return array_search( $id, array_column( $array, 'ID' ) );
    }


    public static function post_user_favorites( $user_id = null ) {
        return $userFavorites = new Circolo_Listing_Favorites($user_id);
    }

    public static function get_user_favorites( $user_id = null ): array {
        $user_id = ( is_null($user_id) ) ? get_current_user_id() : $user_id;
        // Get any existing copy of our transient data
        $user_favorites_transient = 'user_favorites_' . $user_id;
        $user_favorites = get_transient( $user_favorites_transient );
        if ( false === ( $user_favorites ) || !is_array($user_favorites) ) {
            // It wasn't there, so regenerate the data and save the transient
            $user_favorites = get_user_meta($user_id, CIRCOLO_LISTING_SLUG . '_favorites', true);
            if( !is_array( $user_favorites ) ) $user_favorites = [];
            set_transient( $user_favorites_transient, $user_favorites );

            return $user_favorites;
        }
        
        // Use the data like you would have normally...
        return $user_favorites;
    }

    public static function add_user_favorite( $listing_id, $user_id = null ) {
        $userFavorites = new Circolo_Listing_Favorites($user_id);
        $userFavorites->addFavorite( $listing_id );
    }

    public static function remove_user_favorite( $listing_id, $user_id = null ) {
        $userFavorites = new Circolo_Listing_Favorites($user_id);
        $userFavorites->removeFavorite( $listing_id );
    }

}