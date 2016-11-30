<?php

/**
 * Class MPP_Media_Categories_Helper
 */

// exit if file access directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPP_Media_Categories_Helper {

	public function __construct() {
		$this->setup();
	}

	public function setup() {

		add_action( 'mpp_media_added', array( $this, 'save_media_categories' ), 5, 2 );
		add_action( 'mpp_gallery_updated', array( $this, 'update_media_categories' ), 10 );
	}

	public function save_media_categories( $media_id, $gallery_id ) {

		$terms = wp_get_post_terms( $gallery_id, $this->get_taxonomy() );
		$terms = wp_list_pluck( $terms, 'term_id' );

		wp_set_object_terms( $media_id, $terms, $this->get_taxonomy(), false );
	}

	public function update_media_categories( $gallery_id ) {

		if ( ! $gallery_id ) {
			return;
		}

		$attachment_ids = get_children( array( 'post_parent' => $gallery_id, 'post_type' => 'attachment' ) );
		$media_ids      = wp_list_pluck( $attachment_ids, 'ID' );

		if ( ! $media_ids ) {
			return;
		}

		$taxonomy_name = $this->get_taxonomy();
		$terms         = wp_get_post_terms( $gallery_id, $taxonomy_name );
		$terms         = wp_list_pluck( $terms, 'term_id' );

		foreach ( $media_ids as $media_id ) {
			wp_set_object_terms( $media_id, $terms, $taxonomy_name, false );
		}
	}

	public function get_taxonomy() {
		return mpp_gallery_categories()->get_taxonomy_name();
	}

}

new MPP_Media_Categories_Helper();
