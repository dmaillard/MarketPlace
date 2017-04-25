<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BuddyBoss_BM_Zoom' ) ) {

	class BuddyBoss_BM_Zoom {

		public function __construct() {

			if( buddyboss_bm()->option( 'product-img-zoom' ) ) {
				return;
			}

			add_filter( 'woocommerce_single_product_image_html', array( &$this, 'bm_apply_zoom_main_image' ), 11, 1 );
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( &$this, 'bm_apply_zoom_thumbnails' ), 11, 1 );

			add_action( 'woocommerce_product_thumbnails', array( &$this, 'bm_add_thumbnails_zoom_scription' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'bm_zoom_scripts' ), 'jquery' );
		}

		function bm_apply_zoom_thumbnails( $thumbnails_html ) {
			$doc = new DOMDocument( '1.0', 'UTF-8' );
			$doc->loadHTML( $thumbnails_html );

			$links = $doc->getElementsByTagName( 'a' );
			if ( $links ) {
				foreach ( $links as $link ) {
					$classes	 = $link->getAttribute( 'class' );
					$link->setAttribute( 'class', $classes . ' bm-zoom-thumb-image-feature' );
					$new_link	 = $doc->saveHTML();
				}
			}

			$html = preg_replace( '~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $new_link );

			return apply_filters( 'bm_zoom_thumbnails_output', $html );
		}

		function bm_add_thumbnails_zoom_scription( $html ) {

			ob_start();

			$bm_zoom_options = 'zoomType:"window", easing : true, zoomWindowWidth: 300, zoomWindowHeight: 300, lensSize: 80, scrollZoom: true';
			if(is_rtl()) {
				$bm_zoom_options .= ',zoomWindowPosition: 11';
			}
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {
					jQuery( '.bm-zoom-thumb-image-feature img' ).each( function () {
						jQuery( this ).attr( 'data-zoom-image', jQuery( this ).parent().attr( 'href' ) );
					} );

					jQuery( '.bm-zoom-thumb-image-feature img' ).elevateZoom( {
						<?php echo apply_filters( 'bm_zoom_options', $bm_zoom_options ); ?>
					} );
				} );
			</script><?php

			$html = ob_get_clean();
			echo apply_filters( 'bm_zoom_thumbnails_scripts_output', $html );
		}

		function bm_apply_zoom_main_image( $link_image ) {

			$doc = new DOMDocument( '1.0', 'UTF-8' );
			$doc->loadHTML( $link_image );

			$links = $doc->getElementsByTagName( 'a' );
			if ( $links ) {
				foreach ( $links as $link ) {
					$classes	 = $link->getAttribute( 'class' );
					$link->setAttribute( 'class', $classes . ' bm-zoom-main-image-feature' );
					$new_link	 = $doc->saveHTML();
				}
			}

			ob_start();
			echo preg_replace( '~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $new_link );

			$bm_zoom_options = 'zoomType:"window", easing : true, zoomWindowWidth: 300, zoomWindowHeight: 300, lensSize: 80, scrollZoom: true';

			if(is_rtl()) {
				$bm_zoom_options .= ',zoomWindowPosition: 11';
			}
			?>
			<script type="text/javascript">

				var $i = 0;
				var newSmallPicture = jQuery( '.bm-zoom-main-image-feature img' ).attr( 'src' );

				/* variations scripting */
				jQuery( window ).load( function ( $ ) {
					function changePicture() {
						if ( ( jQuery( '.bm-zoom-main-image-feature img' ).attr( 'src' ) !== newSmallPicture ) || ( 0 === $i ) ) {

							var newLargePicture = jQuery( '.bm-zoom-main-image-feature' ).attr( 'href' );
							newSmallPicture = jQuery( '.bm-zoom-main-image-feature img' ).attr( 'src' );
							jQuery( '.bm-zoom-main-image-feature img' ).attr( 'data-zoom-image', newLargePicture );
							var ez = jQuery( '.bm-zoom-main-image-feature img' ).data( 'elevateZoom' );
							ez.swaptheimage( newSmallPicture, newLargePicture );

							$i++;
						}
					}

					if ( 0 === $i ) {
						changePicture();
					}

					jQuery( '.bm-zoom-main-image-feature img' ).load( function () {
						changePicture();
					} );
				} );

				/* end of variations */
				jQuery( document ).ready( function ( $ ) {
					jQuery( '.bm-zoom-main-image-feature img' ).attr( 'data-zoom-image', jQuery( '.bm-zoom-main-image-feature' ).attr( 'href' ) );
					jQuery( '.bm-zoom-main-image-feature img' ).elevateZoom( {
						<?php echo apply_filters( 'bm_zoom_options', $bm_zoom_options ); ?>
					} );
				} );
			</script><?php

			$html = ob_get_clean();
			return apply_filters( 'bm_zoom_main_image_output', $html );
		}

		function bm_zoom_scripts() {
			wp_enqueue_script( 'bm-zoom-js', buddyboss_bm()->assets_url . '/js/vendors/jquery.elevateZoom.min.js', array( 'jquery' ), '', true );
		}

	}

	//Disable Zoom effect on Hover for Mobile devices in elevateZoom
//	if ( ! is_phone() ) {
//		$bm_zoom = new BuddyBoss_BM_Zoom;
//	}

}
