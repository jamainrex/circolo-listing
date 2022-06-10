<?php

namespace CIRCOLO;

class Circolo_Listing_Favorites 
{

    public $current_user;

    public final function __construct( $user_id = null ) {
        $this->set_user( $user_id );
    }

    public function set_user( $user_id = null ) {

        $user_id = ( is_null($user_id) ) ? get_current_user_id() : $user_id;

        $this->current_user = get_user_by('id', $user_id);
    }

    /**
	* Get Logged In User Favorites
	*/
	public function getFavorites( $user_id = null )
	{
		$user_id = ( is_null($user_id) ) ? get_current_user_id() : $user_id;
		$favorites = get_user_meta($user_id, CIRCOLO_LISTING_SLUG . '_favorites');
		if ( empty($favorites) ) return [];

		return $favorites;
	}

    public function isFavorite( $listing_id, $user_id = null ) {
        $favorites = $this->getFavorites($user_id);
		if ( in_array( $listing_id, array_keys( $favorites )) ) return true;
		return false;
    }

    /**
	* Update User Meta (logged in only)
	*/
	public function updateUserMeta($favorites)
	{
		if ( !is_user_logged_in() ) return;


		update_user_meta( intval(get_current_user_id()), CIRCOLO_LISTING_SLUG . '_favorites', $favorites );
	}
}