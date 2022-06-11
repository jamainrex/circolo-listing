<?php

namespace CIRCOLO;

class Circolo_Listing_Favorites 
{

    public $user_id;
    public $current_user;
    public $favorites;

    public function __construct( $user_id = null ) {
        $this->set_user( $user_id );
    }

    public function set_user( $user_id = null ) {

        $this->user_id = $user_id = ( is_null($user_id) ) ? get_current_user_id() : $user_id;
        $this->current_user = get_user_by('id', $user_id);
        $this->favorites = $this->getFavorites( $user_id );

        // filter bad items
        $this->filterFavorite();
    }

    /**
	* Get Logged In User Favorites
	*/
	public function getFavorites( $user_id = null ): array
	{
		$user_id = ( is_null($user_id) ) ? $this->user_id : $user_id;
		$favorites = get_user_meta($user_id, CIRCOLO_LISTING_SLUG . '_favorites', true);
		if ( empty($favorites) ) return [];

		return $favorites;
	}

    public function isFavorite( $listing_id ) {
        $favorites = $this->getUserFavorites();
		if ( in_array( $listing_id, array_keys( $favorites )) ) return true;
		return false;
    }

    /**
	* Update User Meta (logged in only)
	*/
	public function updateUserMeta($favorites)
	{
		if ( !is_user_logged_in() ) return;


		update_user_meta( intval($this->user_id), CIRCOLO_LISTING_SLUG . '_favorites', $favorites );
        $this->set_transient( $favorites );
	}

    public function set_transient( $favorites ) {
        $user_favorites_transient = 'user_favorites_' . $this->user_id;
        set_transient( $user_favorites_transient, $favorites );
    }

    public function setFavorite( $listing ) {
        $this->favorites[$listing->ID] = $listing;        
    }

    public function filterFavorite() {
        $favorites = $this->getUserFavorites();

        $this->favorites = array_filter( $favorites, function( $listing ) {
            return ( /*$listing instanceof WP_Post && */ is_object( $listing ) && $listing->post_type === "circolo_listings" );
        } );
    }

    public function unsetFavorite( $listing_id ) {
        if( $this->isFavorite($listing_id) ) {
            unset( $this->favorites[$listing_id] );
        }     
    }

    public function getUserFavorites(): array {
        return $this->favorites;
    }

	public function addFavorite( $listing_id) {
		$favorites = $this->getUserFavorites();// $this->getFavorites($user_id);
		if ( !in_array( $listing_id, array_keys( $favorites )) && $listing = get_post((int)$listing_id)  ) {
			$this->setFavorite($listing); // $favorites[$listing_id] = $listing;
		}

		$this->updateUserMeta( $this->getUserFavorites() );
	}

    public function removeFavorite( $listing_id) {
        $this->unsetFavorite( $listing_id );
        $this->updateUserMeta( $this->getUserFavorites() );
    }
}