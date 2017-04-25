<?php

/**
 * @package WordPress
 * @subpackage MarketPlace
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( 'BuddyBoss_BM_Templates' ) ):
    /**
     *
     * MarketPlace Main Plugin Controller
     * *************************************
     *
     *
     */
    class BuddyBoss_BM_Templates {

        public static function instance() {
            static $instance = null;

            if ( null === $instance ) {
                $instance = new BuddyBoss_BM_Templates();
            }

            return $instance;
        }

        /**
         *  Output product categories check list
         *
         * @since    1.0.0
         * @param 	 int 	$post_id  post_id for this meta if any
         */
        public static function colors_checklist( $post_id ) {

                $args = array(
                    'taxonomy' => 'product_color'
                );

                $field = array(
                    'id'  	=> 'product_color_list',
                    'label' => __( 'Colors', 'buddyboss-marketplace' ),
                );

                WCVendors_Pro_Form_Helper::wcv_terms_checklist( $post_id, $args, $field );

        } // categories_checklist()

        /**
         *  Output product categories drop down
         *
         * @since    1.0.0
         * @param 	 int 	$post_id  post_id for this meta if any
         * @param 	 bool 	$multiple allow mupltiple selection
         */
        public static function colors_dropdown( $post_id, $multiple = false ) {
            $multiple = true;

                $custom_attributes 	= ( $multiple ) ? array( 'multiple' => 'multiple' ) : array();
                $show_option_none 	= ( $multiple ) ? '' : __( 'Colors', 'buddyboss-marketplace' );

                // Product Category Drop down
                WCVendors_Pro_Form_Helper::select2( apply_filters( 'wcv_product_categories',
                        array(
                            'post_id'			=> $post_id,
                            'id' 				=> 'product_color[]',
                            'taxonomy'			=> 'product_color',
                            'show_option_none'	=> $show_option_none,
                            'taxonomy_args'		=> array(
                                'hide_empty'	=> 0,
                                'orderby'		=> 'order',
                            ),
                            'label'	 			=> ( $multiple ) ? __( 'Colors', 'buddyboss-marketplace' ) : __( 'Color', 'buddyboss-marketplace' ),
                            'custom_attributes' => $custom_attributes,
                        )
                    )
                );


        } // categories()

        /**
         *  Output product categories
         *
         * @since    1.0.0
         * @param 	 int 	$post_id  post_id for this meta if any
         * @param 	 bool 	$multiple allow mupltiple selection
         */
        public static function colors( $post_id, $multiple = false ) {

//            self::colors_checklist( $post_id );
            self::colors_dropdown( $post_id );

        } // colors

        public static function bm_get_non_vendor_users() {
            $user_query = new WP_User_Query( array( 'role' => 'vendor', 'fields' => 'ID' ) );
            return $theExcludeString=implode(",",$user_query->get_results());
        }

        public static function bm_user_social_links($id) {
            if(!$id) $id =  bp_get_member_user_id();
            ?>
            <!-- Socials -->
            <div class="btn-group social">

            <?php
            foreach ( buddyboss_get_user_social_array() as $social => $name ):
            $url = buddyboss_get_user_social( $id, $social );
            ?>

                <?php if ( !empty( $url ) ): ?>
                <a class="btn" href="<?php echo $url; ?>" title="<?php echo esc_attr( $name ); ?>" target="_blank"><i class="bb-icon-<?php echo $social; ?>"></i></a>
                <?php endif; ?>

            <?php endforeach; ?>

            </div>
        <?php
        }

        /**
         * Print a list of categories link which the given vendor has added products in.
         *
         * @global $wpdb
         * @param int $vendor_id
         * @return void
         */
        public static function bm_filter_by_category($vendor_id) {
            global $wpdb;

            $current_cat = isset( $_GET['cate'] ) ? (int)$_GET['cate'] : 0;
            $empty = '<p>'.__('No categories in this shop', 'buddyboss-marketplace').'</p>';

            //1. Get all categories in which the vendor has added his/her products in
            $sql = "SELECT t.term_id, t.name, count( p.ID ) AS 'products_count' "
                    . " FROM "
                    .   " {$wpdb->terms} AS t "
                    .   " JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_id = t.term_id "
                    .   " JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id "
                    .   " JOIN {$wpdb->posts} AS p ON tr.object_id = p.ID "
                    . " WHERE 1=1 "
                    .   " AND tt.taxonomy = 'product_cat' "
                    .   " AND p.post_type = 'product' "
                    .   " AND p.post_status = 'publish' "
                    .   " AND p.post_author = %d "
                    . " GROUP BY tt.term_id "
                    . " ORDER BY products_count DESC, t.name ASC ";

            $results = $wpdb->get_results( $wpdb->prepare( $sql, $vendor_id ) );
            if( empty( $results ) || is_wp_error( $results ) ){
                echo $empty;
                return;
            }

            //2. Generate list
            echo '<ul class="product-categories">';
            foreach( $results as $category ){
                $class = $current_cat == $category->term_id ? 'class="current"' : '';
                echo '<li '.$class.'><a href="?cate=' . $category->term_id . '">' . __($category->name, 'woocommerce') . '</a><span>' . $category->products_count . '</span></li>';
            }
            echo '</ul>';
        }

        /**
         * Output a file upload link
         *
         * @since      1.0.0
         * @param      array     $field      file uploader arguments
         * @todo       add filters to allow the field to be hooked into this should not echo html but return it.
         */
        public static function file_uploader( $field ) {

            $field[ 'header_text' ]			= isset( $field[ 'header_text' ] ) 		? $field[ 'header_text' ]	 	: __('Image', 'buddyboss-marketplace' );
            $field[ 'add_text' ] 			= isset( $field[ 'add_text' ] ) 		? $field[ 'add_text' ] 			: __('Add Image', 'buddyboss-marketplace' );
            $field[ 'remove_text' ]			= isset( $field[ 'remove_text' ] ) 		? $field[ 'remove_text' ] 		: __('Remove Image', 'buddyboss-marketplace' );
            $field[ 'image_meta_key' ] 		= isset( $field[ 'image_meta_key' ] ) 	? $field[ 'image_meta_key' ] 	: '_wcv_image_id';
            $field[ 'save_button' ]			= isset( $field[ 'save_button' ] ) 		? $field[ 'save_button' ] 		: __('Add Image', 'buddyboss-marketplace' );
            $field[ 'window_title' ]		= isset( $field[ 'window_title' ] ) 	? $field[ 'window_title' ] 		: __('Select an Image', 'buddyboss-marketplace' );
            $field[ 'value' ]				= isset( $field[ 'value' ] ) 			? $field[ 'value' ] 			: 0;
            $field[ 'size' ]				= isset( $field[ 'size' ] ) 			? $field[ 'size' ] 				: 'full';
            $field[ 'class' ]         		= isset( $field[ 'class'] ) 			? $field[ 'class' ] 			: '';
            $field[ 'wrapper_start' ]		= isset( $field[ 'wrapper_start' ] ) 	? $field[ 'wrapper_start' ] 	: '';
            $field[ 'wrapper_end' ]			= isset( $field[ 'wrapper_end' ] ) 		? $field[ 'wrapper_end' ] 		: '';

            // Get the image src
            $image_src = wp_get_attachment_image_src( $field[ 'value' ], $field[ 'size' ] );

            // see if the array is valid
            $has_image = is_array( $image_src );

            // Container wrapper start if defined start & end required otherwise no output is shown
            if (! empty($field['wrapper_start'] ) && ! empty($field['wrapper_end'] ) ) {
                echo $field['wrapper_start'];
            }

            echo '<div class="wcv-file-uploader wcv-file-uploader'. $field[ 'image_meta_key' ] .' '. $field[ 'class' ] .'">';

            if ( $has_image ) {
                echo '<img src="'. $image_src[0].'" alt="" style="max-width:100%;" />';
            }

            echo '</div>';

            echo '<a class="add-image wcv-file-uploader-add'. $field[ 'image_meta_key' ] . ' ' . ( $has_image ? 'hidden' : '' ) . '" href="#">'.$field[ 'add_text' ].'</a><br />';
            echo '<a class="remove-image wcv-file-uploader-delete' . $field[ 'image_meta_key' ] .' ' . ( !$has_image ? 'hidden' : '' )  . '" href="#" >'.$field[ 'remove_text' ].'</a><br />';
            echo '<input class="wcv-img-id" name="'. $field[ 'image_meta_key'] .'" id="'. $field[ 'image_meta_key'] .'" type="hidden" value="'. esc_attr( $field[ 'value' ] ) .'" data-image_meta_key="'. $field[ 'image_meta_key' ] .'" data-save_button="'. $field[ 'save_button' ] .'" data-window_title="'. $field[ 'window_title' ] .'" />';

            // container wrapper end if defined
            if (! empty($field['wrapper_start'] ) && ! empty($field['wrapper_end'] ) ) {
                echo $field['wrapper_end'];
            }

        } // file_uploader()

        /**
         *  Output store banner uploader
         *
         * @since    1.0.0
         */
        public static function store_banner( ) {

            $value = get_user_meta( get_current_user_id(), '_wcv_store_banner_id', true );

            // Store Banner Image
            // buddyboss-marketplace
            BuddyBoss_BM_Templates::file_uploader( apply_filters( 'wcv_vendor_store_banner', array(
                    'header_text'		=> __('Store Banner', 'buddyboss-marketplace' ),
                    'add_text' 			=> __('Add Store Banner', 'buddyboss-marketplace' ),
                    'remove_text'		=> __('Remove Store Banner', 'buddyboss-marketplace' ),
                    'image_meta_key' 	=> '_wcv_store_banner_id',
                    'save_button'		=> __('Add Store Banner', 'buddyboss-marketplace' ),
                    'window_title'		=> __('Select an Image', 'buddyboss-marketplace' ),
                    'value'				=> $value,
                )
            ) );

        } // store_banne()

        /**
         *  Output store icon uploader
         *
         * @since    1.0.0
         */
        public static function store_icon( ) {

            $value = get_user_meta( get_current_user_id(),  '_wcv_store_icon_id', true );

            // Store Icon
            BuddyBoss_BM_Templates::file_uploader( apply_filters( 'wcv_vendor_store_icon', array(
                    'header_text'		=> __('Store Icon', 'buddyboss-marketplace' ),
                    'add_text' 			=> __('Add Store Icon', 'buddyboss-marketplace' ),
                    'remove_text'		=> __('Remove Store Icon', 'buddyboss-marketplace' ),
                    'image_meta_key' 	=> '_wcv_store_icon_id',
                    'save_button'		=> __('Add Store Icon', 'buddyboss-marketplace' ),
                    'window_title'		=> __('Select an Image', 'buddyboss-marketplace' ),
                    'value'				=> $value,
                    'size'				=> 'thumbnail',
                    'class'				=> 'wcv-store-icon'
                )
            ) );

        } // store_icon()


        public static function store_name( $store_name ) {

            if ( '' == $store_name ) {
                $user_data = get_userdata( get_current_user_id() );
                $store_name = ucfirst( $user_data->display_name ) . __( ' Store', 'buddyboss-marketplace' );
            }

            // Store Name
            WCVendors_Pro_Form_Helper::input( apply_filters( 'wcv_vendor_store_name', array(
                    'id' 				=> '_wcv_store_name',
                    'label' 			=> __( 'Store Name <small>*</small>', 'buddyboss-marketplace' ),
                    'placeholder' 		=> __( 'Your Store Name', 'buddyboss-marketplace' ),
                    'desc_tip' 			=> 'true',
                    'description' 		=> __( 'Your shop name is public and must be unique.', 'buddyboss-marketplace' ),
                    'type' 				=> 'text',
                    'value'				=> $store_name,
                    'custom_attributes' => array(
                        'required' 	=> '',
                    ),
                )
            ) );

        } // store_name()


        /**
         * Output a the product images and hook into media uploader on front end
         *
         * @since      1.1.3
         * @param      int     $post_id      the post id for the files being uploaded
         * @todo       add filters to allow the field to be hooked into this should not echo html but return it.
         */
        public static function product_media_uploader( $post_id ) {

            echo '<div class="all-50 small-100 tiny-100">';

            echo '<h6>'.__('Featured Image', 'buddyboss-marketplace').'</h6>';
            $post_thumb 	= has_post_thumbnail( $post_id );

            echo '<div class="file-upload-wrap featureimg">';
            echo '<div class="wcv-file-uploader wcv-featuredimg" data-title="'.__('Select or Upload a Feature Image', 'buddyboss-marketplace').'" data-button_text="'.__('Set Product Feature Image', 'buddyboss-marketplace').'">';
            if ( $post_thumb ) {
                $image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id), array(150,150) );
                echo '<img src="'.$image_attributes[0].'" width="'.$image_attributes[1].'" height="'.$image_attributes[2].'">';
            }
            echo '</div>';
            echo '<input type="hidden" id="_featured_image_id" name="_featured_image_id" value="'.( $post_thumb ? get_post_thumbnail_id( $post_id) : '' ). '" />';

            echo '<a class="wcv-media-uploader-featured-add add-image ' . ( $post_thumb ? 'hidden' : '' ) . '" href="#" >'.__('Set featured image', 'buddyboss-marketplace').'</a>';
            echo '<a class="wcv-media-uploader-featured-delete remove-image ' . ( !$post_thumb ? 'hidden' : '' )  . '" href="#" >'.__('Remove featured image', 'buddyboss-marketplace').'</a>';

            echo '</div>';
            echo '</div>';

            // Output the image gallery if there are any images.
            $product = new WC_Product( $post_id );
            // Todo change this to use : product_image_gallery
            $attachment_ids = $product->get_gallery_image_ids();

            $gallery_options = apply_filters( 'wcv_product_gallery_options', array(
                    'max_upload' => 4,
                    'notice' => __( 'You have reached the maximum number of gallery images.', 'buddyboss-marketplace' )
                )
            );

            echo '<div class="all-50 small-100 tiny-100" >';

            echo '<h6>'.__('Gallery', 'buddyboss-marketplace').'</h6>';

            echo '<div id="product_images_container" data-gallery_max_upload="'. $gallery_options[ 'max_upload' ] .'" data-gallery_max_notice="'.$gallery_options[ 'notice' ].'">';
            echo '<ul class="product_images inline">';
            if ( sizeof( $attachment_ids ) > 0 ) {
                foreach( $attachment_ids as $attachment_id ) {
                    echo '<li class="wcv-gallery-image" data-attachment_id="' . $attachment_id . '">';
                    echo wp_get_attachment_image( $attachment_id, array(150,150) );
                    echo '<ul class="actions">';
                    echo '<li><a href="#" class="delete" title="delete"><i class="fa fa-times"></i></a></li>';
                    echo '</ul>';
                    echo '</li>';
                }
            }
            echo '<li class="file-upload-wrap productgallery wcv-gallery-image">';
            echo '<p class="wcv-media-uploader-gallery"><a href="#" class="add-image" data-choose="' .__( 'Add Images to Product Gallery', 'buddyboss-marketplace'). '" data-update="' .__( 'Add to gallery', 'buddyboss-marketplace'). '" data-delete="Delete image" data-text="Delete">' .__( 'Add Product Image', 'buddyboss-marketplace'). '</a></p>';
            echo '</li>';
            echo '</ul>';
            echo '<input type="hidden" id="product_image_gallery" name="product_image_gallery" value="'. ( ( sizeof( $attachment_ids ) > 0 ) ? $product->product_image_gallery : '' ). '">';
            echo '</div>';

            echo '</div>';

            echo '<div class="all-100"></div>';

        } // media_uploader ()

        /**
         * Output a woocommerce attribute select
         *
         * @since      1.0.0
         * @param      array     $field      Array defining all field attributes
         * @todo       add filters to allow the field to be hooked into this should not echo html but return it.
         */
        public static function attribute( $post_id ) {

            $basic_options 		= (array) WC_Vendors::$pv_options->get_option( 'hide_product_basic' );

            if ( ! $basic_options[ 'attributes' ] ) {

                // Array of defined attribute taxonomies
                $attribute_taxonomies = wc_get_attribute_taxonomies();

                // If there are any defined attributes display them
                if ( !empty( $attribute_taxonomies ) ) {

                    $i = 0;
                    // Get any set attributes for the product
                    $attributes  = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

                    foreach ($attribute_taxonomies as $product_attribute) {

                        $current_attribute = '';
                        $is_variation = 'no';

                        // If the attributes aren't empty, extract the attribute value for the current product
                        // Does not support multi select at this time
                        // TODO:  Support select2 and multiple attributes
                        if ( ! empty( $attributes ) && array_key_exists( wc_attribute_taxonomy_name( $product_attribute->attribute_name ), $attributes ) ) {
                            // get all terms
                            $current_attribute = wp_get_post_terms( $post_id, wc_attribute_taxonomy_name( $product_attribute->attribute_name ) );
                            $is_variation = $attributes[ wc_attribute_taxonomy_name($product_attribute->attribute_name) ]['is_variation'] ? 'yes' : 'no' ;
                            $current_attribute = reset ( $current_attribute );
                            $current_attribute = $current_attribute->slug;

                        }

                        // Output attribute select
                        self::select( array(
                                'id' 				=> 'attribute_values[' . $i . '][]',
                                'post_id'			=> $post_id,
                                'label' 			=> ucfirst( $product_attribute->attribute_name ),
                                'value' 			=> $current_attribute,
                                'show_option_none' => __( 'Select a ', 'buddyboss-marketplace' ) . ucfirst( $product_attribute->attribute_name ),
                                'taxonomy'			=> wc_attribute_taxonomy_name( $product_attribute->attribute_name ),
                                'taxonomy_args'		=> array(
                                    'hide_empty'	=> 0,
                                    'orderby'		=> $product_attribute->attribute_orderby,
                                ),
                            )
                        );

                        // Output attribute name hidden
                        self::input( array(
                                'post_id'				=> $post_id,
                                'id' 					=> 'attribute_names['.$i.']',
                                'type' 					=> 'hidden',
                                'show_label'			=> false,
                                'value'					=> wc_attribute_taxonomy_name( $product_attribute->attribute_name ),
                            )
                        );
                        $i++;
                    }
                }

                // Support other plugins hooking into attributes
                // Not sure if this will work ?
                do_action( 'woocommerce_product_options_attributes' );

            }

        } //attribute()

    }


endif;
