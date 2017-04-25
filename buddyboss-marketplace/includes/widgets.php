<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Taxonomy API: Walker_Category_Checklist class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

class BM_Widget extends WC_Widget
{
    public function update( $new_instance, $old_instance ) {
        $new_instance['product_cat'] = $_POST['tax_input']['product_cat'];
        return $new_instance;
    }

    /**
     * Outputs the settings update form.
     *
     * @param array $instance
     */
    public function form($instance)
    {

        if (empty($this->settings)) {
            return;
        }

        $selected_cats = $instance['product_cat'];

        foreach ($this->settings as $key => $setting) {

            $class = isset($setting['class']) ? $setting['class'] : '';
            $value = isset($instance[$key]) ? $instance[$key] : $setting['std'];

            switch ($setting['type']) {

                case 'text' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <input class="widefat <?php echo esc_attr($class); ?>"
                               id="<?php echo esc_attr($this->get_field_id($key)); ?>"
                               name="<?php echo $this->get_field_name($key); ?>" type="text"
                               value="<?php echo esc_attr($value); ?>"/>
                    </p>
                    <?php
                    break;

                case 'number' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <input class="widefat <?php echo esc_attr($class); ?>"
                               id="<?php echo esc_attr($this->get_field_id($key)); ?>"
                               name="<?php echo $this->get_field_name($key); ?>" type="number"
                               step="<?php echo esc_attr($setting['step']); ?>"
                               min="<?php echo esc_attr($setting['min']); ?>"
                               max="<?php echo esc_attr($setting['max']); ?>" value="<?php echo esc_attr($value); ?>"/>
                    </p>
                    <?php
                    break;

                case 'select' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <select class="widefat <?php echo esc_attr($class); ?>"
                                id="<?php echo esc_attr($this->get_field_id($key)); ?>"
                                name="<?php echo $this->get_field_name($key); ?>">
                            <?php foreach ($setting['options'] as $option_key => $option_value) : ?>
                                <option
                                    value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $value); ?>><?php echo esc_html($option_value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <?php
                    break;

                case 'product_cats_dropdown' :
                    $product_cat_list = array();
                    foreach(get_terms('product_cat') as $term) {
                        $product_cat_list[$term->term_id] = $term->name;
                    }
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <select class="widefat <?php echo esc_attr($class); ?>"
                                id="<?php echo esc_attr($this->get_field_id($key)); ?>"
                                name="<?php echo $this->get_field_name($key); ?>">
                                <option
                                    value="0" <?php selected(0, $value); ?>><?php _e('- None', 'buddyboss-marketplace') ?></option>
                            <?php foreach ($product_cat_list as $option_key => $option_value) : ?>
                                <option
                                    value="<?php echo esc_attr($option_key); ?>" <?php selected($option_key, $value); ?>><?php echo esc_html($option_value); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <?php
                    break;

                case 'product_cats_list' :
//                    $walker = new BM_Walker_Category_Checklist();
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <ul class="category_lists">
                        <?php wp_terms_checklist(0, array(
                            'taxonomy' => 'product_cat',
                            'selected_cats' => $selected_cats
//                            'walker' => $walker
                        )); ?>
                        </ul>
                    </p>
                    <?php
                    break;

                case 'textarea' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                        <textarea class="widefat <?php echo esc_attr($class); ?>"
                                  id="<?php echo esc_attr($this->get_field_id($key)); ?>"
                                  name="<?php echo $this->get_field_name($key); ?>" cols="20"
                                  rows="3"><?php echo esc_textarea($value); ?></textarea>
                        <?php if (isset($setting['desc'])) : ?>
                            <small><?php echo esc_html($setting['desc']); ?></small>
                        <?php endif; ?>
                    </p>
                    <?php
                    break;

                case 'checkbox' :
                    ?>
                    <p>
                        <input class="checkbox <?php echo esc_attr($class); ?>"
                               id="<?php echo esc_attr($this->get_field_id($key)); ?>"
                               name="<?php echo esc_attr($this->get_field_name($key)); ?>" type="checkbox"
                               value="1" <?php checked($value, 1); ?> />
                        <label for="<?php echo $this->get_field_id($key); ?>"><?php echo $setting['label']; ?></label>
                    </p>
                    <?php
                    break;

                // Default: run an action
                default :
                    do_action('woocommerce_widget_field_' . $setting['type'], $key, $value, $setting, $instance);
                    break;
            }
        }
    }
}

/**
 * Product Categories Widget.
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class BM_Widget_Product_Categories extends BM_Widget {

    /**
     * Category ancestors.
     *
     * @var array
     */
    public $cat_ancestors;

    /**
     * Current Category.
     *
     * @var bool
     */
    public $current_cat;

    /**
     * Variable to store category permalinks, retrieved from database.
     * This is to attempt to reduce database queries.
     * 
     * @var array 
     */
    private $_category_links = array();
    
    /**
     * Constructor.
     */
    public function __construct() {
        $this->widget_cssclass    = 'woocommerce bm_widget_product_categories';
        $this->widget_description = __( 'A panel dropdown showing product categories. Can be used only in MarketPanel widget area.', 'buddyboss-marketplace' );
        $this->widget_id          = 'marketplace_product_categories';
        $this->widget_name        = __( 'MarketPanel', 'buddyboss-marketplace' );
        $this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => __( 'Product Categories', 'buddyboss-marketplace' ),
                'label' => __( 'Menu Title', 'buddyboss-marketplace' )
            ),
            'categories' => array(
                'type'  => 'product_cats_list',
                'std'   => 0,
                'label' => __( 'Categories to display. Empty categories will not display. Parent categories will be used as section headers.', 'buddyboss-marketplace' )
            ),
            'view_all' => array(
                'type'  => 'checkbox',
                'std'   => '0',
                'label' => __( 'Show "View All" links', 'buddyboss-marketplace' )
            ),
            'optimized' => array(
                'type'  => 'checkbox',
                'std'   => '0',
                'label' => __( 'Optimize database queries: May result in incorrect display in some cases. Uncheck this option if so.', 'buddyboss-marketplace' )
            ),
            'featured_category1' => array(
                'type'  => 'product_cats_dropdown',
                'std'   => 0,
                'label' => __( 'The First Featured Product Category', 'buddyboss-marketplace' )
            ),
            'featured_category2' => array(
                'type'  => 'product_cats_dropdown',
                'std'   => 0,
                'label' => __( 'The Second Featured Product Category', 'buddyboss-marketplace' )
            ),
            'pro_orderby' => array(
                'type'  => 'select',
                'std'   => 'newest',
                'label' => __( 'Display Product based on:', 'buddyboss-marketplace' ),
                'options' => array(
                    'newest' => __( 'Latest Product', 'buddyboss-marketplace' ),
                    'popular' => __( 'Most Popular Product', 'buddyboss-marketplace' )
                )
            )
        );

        parent::__construct();
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        $optimized = isset( $instance['optimized'] ) ? $instance['optimized'] : $this->settings['optimized']['std'];
        if( $optimized ){
            $this->widget_optimized( $args, $instance );
        } else {
            $this->widget_unoptimized( $args, $instance );
        }
    }
    
    public function widget_optimized( $args, $instance ) {
        global $wp_query, $post;

        $categories           = isset( $instance['product_cat'] ) ? $instance['product_cat'] : array();
        $view_all             = isset( $instance['view_all'] ) ? $instance['view_all'] : $this->settings['view_all']['std'];
        $featured_category1    = isset( $instance['featured_category1'] ) ? $instance['featured_category1'] : $this->settings['featured_category1']['std'];
        $featured_category2    = isset( $instance['featured_category2'] ) ? $instance['featured_category2'] : $this->settings['featured_category2']['std'];
        $pro_orderby    = isset( $instance['pro_orderby'] ) ? $instance['pro_orderby'] : $this->settings['pro_orderby']['std'];
        $featured_categories = array();
        if($featured_category1) {
            $featured_categories[] = $featured_category1;
        }
        if($featured_category2) {
            $featured_categories[] = $featured_category2;
        }
        $count_cats = count($featured_categories);
//        $list_args          = array( 'taxonomy' => 'product_cat', 'hide_empty' => false );

        // Setup Current Category
        $this->current_cat   = false;
        $this->cat_ancestors = array();

        if ( is_tax( 'product_cat' ) ) {

            $this->current_cat   = $wp_query->queried_object;
            $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );

        } elseif ( is_singular( 'product' ) ) {

            $product_category = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent' ) );

            if ( $product_category ) {
                $this->current_cat   = end( $product_category );
                $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );
            }

        }

        $this->widget_start( $args, $instance );

        include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php' );
        
        // 1. get all terms
        $categories_o = get_terms( 'product_cat', array( 'include' => $categories, 'orderby' => 'include' ) );

        $first_level_categories = array();
        foreach( $categories_o as $category_outer ){
            $is_first_level = true;
            foreach( $categories_o as $category_inner ){
                if( $category_outer->parent == $category_inner->term_id ){
                    $is_first_level = false;
                    break;
                }
            }
            
            if( $is_first_level ){
                $first_level_categories[] = $category_outer;
            }
        }

        $layout_class = 'width';
        $width = 0;

        if( $featured_category1 ) {
            $width += 1;
            $layout_class = 'width-'.$width;
        }

        if( $featured_category2 ) {
            $width += 1;
            $layout_class = 'width-'.$width;
        }

        echo '<ul class="product-categories '.$layout_class.'">';

        $hover_class_added = false;
        
        foreach( $first_level_categories as $category ){
            //check if this has any child category
            $has_child_category = false;
            foreach ( $categories_o as $c ){
                if( $c->parent == $category->term_id ){
                    $has_child_category = true;
                    break;
                }
            }
            
            $class = 'cat';
            if( $has_child_category ){
                $class .= ' cat-parent';
                if(!$hover_class_added) {
                    $class .= ' hovered';
                    $hover_class_added = true;
                }
            }
            if( $this->current_cat && ( $this->current_cat->term_id == $category->term_id || in_array($category->term_id, $this->cat_ancestors ) ) ){
                $class .= ' current';
            }
            
            echo sprintf('<li class="'.$class.'"><a href="%s">%s</a>', $this->_get_category_link($category), apply_filters('get_term', $category->name));

            echo $this->_list_categories_recursive( $categories_o, $category, $this->current_cat, $view_all );

            echo '</li>';
        }

        echo '</ul>';
        
        if($featured_category1 || $featured_category2) {

            $output = "<div class='menu-latest-product'>";

            $title_part = _n( ' Product', ' Products', $count_cats, 'buddyboss-marketplace' );

            $query_args = array();

            if( 'newest' == $pro_orderby ) {
                $output .= "<h3>".__('Latest', 'buddyboss-marketplace').$title_part."</h3>";

            } else {
                $output .= "<h3>".__('Most Popular', 'buddyboss-marketplace')."</h3>";
            }

            foreach($featured_categories as $featured_category) {

                if($featured_category) {
                    $query_args = array(
                        'post_type'           => 'product',
                        'post_status'         => 'publish',
                        'ignore_sticky_posts' => 1,
                        'posts_per_page'      => 1,
                        'orderby'             => 'date',
                        'order'               => 'desc',
                        'tax_query'           => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'terms'    => $featured_category,
                                'field'    => 'term_id'
                            )
                        )
                    );

                    if( 'popular' == $pro_orderby ) {

                        $highgest_products = array();
                        $products_favorited_count = array();

                        $products_favorited_count = get_option('products_favorited_count');

                        if(is_array($products_favorited_count)){
                            asort($products_favorited_count);
                        }

                        $val = end($products_favorited_count);

                        $key = key($products_favorited_count);

                        if($key) {
                            $highgest_products[] = $key;
                            $query_args['post__in'] = $highgest_products;
                        }
                    }

                    global $woocommerce_loop;

                    $products                    = new WP_Query( $query_args );

                    if ( !$products->have_posts() && 'popular' == $pro_orderby ) {
                        unset( $query_args['post__in'] );
                        $products = new WP_Query( $query_args );
                    }

                    ob_start();

                    if ( $products->have_posts() ) : ?>

                        <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                            <?php
                            global $product;
                            $classes = array();
                            if(!$product->get_price_html()) {
                                $classes[] = 'no-price';
                            } ?>
                            <div <?php post_class( $classes ); ?>>

                                <div class="loop-product-image">
                                    <a class="image-link" href="<?php the_permalink(); ?>">
                                    <?php woocommerce_template_loop_product_thumbnail(); ?>
                                    </a>

                                    <div class="product-buttons">
                                        <?php woocommerce_template_loop_price(); ?>
                                        <?php
//                                        do_action('bm_menu_product_actions');
                                        /**
                                         * woocommerce_after_shop_loop_item hook
                                         *
                                         * @hooked woocommerce_template_loop_add_to_cart - 10
                                         */
                                        do_action( 'woocommerce_after_shop_loop_item' );

                                        ?>
                                    </div>
                                </div>
                                <a href="<?php the_permalink(); ?>">
                                <?php
                                woocommerce_template_loop_product_title();
                                ?>
                                </a>

                            </div <?php post_class(); ?>>

                        <?php endwhile; // end of the loop. ?>

                    <?php endif;

                    woocommerce_reset_loop();
                    wp_reset_postdata();

                    $output .= ob_get_contents();
                    ob_end_clean();

                }
            }

            $output .= "</div>";
            echo $output;
        }

        $this->widget_end( $args );
    }
    
    public function widget_unoptimized( $args, $instance ) {

        global $wp_query, $post;

        $categories           = isset( $instance['product_cat'] ) ? $instance['product_cat'] : array();
        $view_all             = isset( $instance['view_all'] ) ? $instance['view_all'] : $this->settings['view_all']['std'];
        $featured_category1    = isset( $instance['featured_category1'] ) ? $instance['featured_category1'] : $this->settings['featured_category1']['std'];
        $featured_category2    = isset( $instance['featured_category2'] ) ? $instance['featured_category2'] : $this->settings['featured_category2']['std'];
        $pro_orderby    = isset( $instance['pro_orderby'] ) ? $instance['pro_orderby'] : $this->settings['pro_orderby']['std'];
        $featured_categories = array();
        if($featured_category1) {
            $featured_categories[] = $featured_category1;
        }
        if($featured_category2) {
            $featured_categories[] = $featured_category2;
        }
        $count_cats = count($featured_categories);
//        $list_args          = array( 'taxonomy' => 'product_cat', 'hide_empty' => false );

        // Setup Current Category
        $this->current_cat   = false;
        $this->cat_ancestors = array();

        if ( is_tax( 'product_cat' ) ) {

            $this->current_cat   = $wp_query->queried_object;
            $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );

        } elseif ( is_singular( 'product' ) ) {

            $product_category = wc_get_product_terms( $post->ID, 'product_cat', array( 'orderby' => 'parent' ) );

            if ( $product_category ) {
                $this->current_cat   = end( $product_category );
                $this->cat_ancestors = get_ancestors( $this->current_cat->term_id, 'product_cat' );
            }

        }

        $this->widget_start( $args, $instance );


        include_once( WC()->plugin_path() . '/includes/walkers/class-product-cat-list-walker.php' );

//        Remove child categories
        $to_remove = array();
        
            for ($i = 0; $i < count($categories); $i++) {
                for ($j = 0; $j < count($categories); $j++) {
                    if(term_is_ancestor_of($categories[$i], $categories[$j], 'product_cat')) {
                        $to_remove[] = $categories[$j];
                    } elseif (term_is_ancestor_of($categories[$j], $categories[$i], 'product_cat')) {
                        $to_remove[] = $categories[$i];
                    }
                }
            }

            $to_remove = array_unique($to_remove);
            $first_level_categories = array_diff($categories, $to_remove);

        $list_args = array(
            'orderby'      => 'include',
            'order'        => 'ASC',
            'include'      => $first_level_categories,
            'orderby'      => 'include',
            'pad_counts'   => 1
        );

        $layout_class = 'width';
        $width = 0;

        if( $featured_category1 ) {
            $width += 1;
            $layout_class = 'width-'.$width;
        }

        if( $featured_category2 ) {
            $width += 1;
            $layout_class = 'width-'.$width;
        }

        echo '<ul class="product-categories '.$layout_class.'">';

//        wp_list_categories( apply_filters( 'marketplace_product_categories_widget_args', $list_args ) );

        $terms = get_terms( 'product_cat', apply_filters( 'marketplace_product_categories_widget_args', $list_args ) );

        $hover_class_added = false;

        foreach ($terms as $category) {

            $subcategories_args = array(
                'child_of' => $category->term_id,
                'include' => $categories
            );
            
            $subcategories = get_terms('product_cat', apply_filters( 'marketplace_menu_subcategories_terms_args', $subcategories_args ) );

            $class = 'cat';
            if(!empty($subcategories)){
                $class .= ' cat-parent';
                if(!$hover_class_added) {
                    $class .= ' hovered';
                    $hover_class_added = true;
                }
            }
            if( $this->current_cat && ( $this->current_cat->term_id == $category->term_id || in_array($category->term_id, $this->cat_ancestors ) ) ){
                $class .= ' current';
            }
            echo sprintf('<li class="'.$class.'"><a href="%s">%s</a>', get_category_link($category->term_id), apply_filters('get_term', $category->name));

            echo '<ul class="children">';
//            foreach ($subcategories as $subcategory) {
//                echo sprintf('<li><a href="%s">%s</a></li>', get_category_link($subcategory->term_id), apply_filters('get_term', $subcategory->name));
//            }

            $list_cat_args = array(
                'title_li' => '',
                'show_option_none' => '',
                'child_of' => $category->term_id,
                'include' => $categories,
                'pad_counts' => 1,
                'taxonomy' => 'product_cat',
                'current_category' => ( $this->current_cat ) ? $this->current_cat->term_id : '',
                'current_category_ancestors' => $this->cat_ancestors
            );

            wp_list_categories( apply_filters( 'marketplace_list_categories_widget_args', $list_cat_args ) );

            if($view_all) {
                printf('<li class="view-all"><a href="%s">%s</a></li>', get_category_link($category->term_id), __('All ', 'buddyboss-marketplace').apply_filters('get_term', $category->name));
            }
            echo '</ul>';


            echo '</li>';
        }

        echo '</ul>';

        if($featured_category1 || $featured_category2) {

            $output = "<div class='menu-latest-product'>";

            $title_part = _n( ' Product', ' Products', $count_cats, 'buddyboss-marketplace' );

            $query_args = array();

            if( 'newest' == $pro_orderby ) {
                $output .= "<h3>".__('Latest', 'buddyboss-marketplace').$title_part."</h3>";

            } else {
                $output .= "<h3>".__('Most Popular', 'buddyboss-marketplace')."</h3>";
            }

            foreach($featured_categories as $featured_category) {

                if($featured_category) {
                    $query_args = array(
                        'post_type'           => 'product',
                        'post_status'         => 'publish',
                        'ignore_sticky_posts' => 1,
                        'posts_per_page'      => 1,
                        'orderby'             => 'date',
                        'order'               => 'desc',
                        'tax_query'           => array(
                            array(
                                'taxonomy' => 'product_cat',
                                'terms'    => $featured_category,
                                'field'    => 'term_id'
                            )
                        )
                    );

                    if( 'popular' == $pro_orderby ) {

                        $highgest_products = array();
                        $products_favorited_count = array();

                        $products_favorited_count = get_option('products_favorited_count');

                        if(is_array($products_favorited_count)){
                            asort($products_favorited_count);
                        }

                        $val = end($products_favorited_count);

                        $key = key($products_favorited_count);

                        if($key) {
                            $highgest_products[] = $key;
                            $query_args['post__in'] = $highgest_products;
                        }
                    }

                    global $woocommerce_loop;

                    $products                    = new WP_Query( $query_args );

                    if ( !$products->have_posts() && 'popular' == $pro_orderby ) {
                        unset( $query_args['post__in'] );
                        $products = new WP_Query( $query_args );
                    }

                    ob_start();

                    if ( $products->have_posts() ) : ?>

                        <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                            <?php
                            global $product;
                            $classes = array();
                            if(!$product->get_price_html()) {
                                $classes[] = 'no-price';
                            } ?>
                            <div <?php post_class( $classes ); ?>>

                                <div class="loop-product-image">
                                    <a class="image-link" href="<?php the_permalink(); ?>">
                                    <?php woocommerce_template_loop_product_thumbnail(); ?>
                                    </a>

                                    <div class="product-buttons">
                                        <?php woocommerce_template_loop_price(); ?>
                                        <?php
//                                        do_action('bm_menu_product_actions');
                                        /**
                                         * woocommerce_after_shop_loop_item hook
                                         *
                                         * @hooked woocommerce_template_loop_add_to_cart - 10
                                         */
                                        do_action( 'woocommerce_after_shop_loop_item' );

                                        ?>
                                    </div>
                                </div>
                                <a href="<?php the_permalink(); ?>">
                                <?php
                                woocommerce_template_loop_product_title();
                                ?>
                                </a>

                            </div <?php post_class(); ?>>

                        <?php endwhile; // end of the loop. ?>

                    <?php endif;

                    woocommerce_reset_loop();
                    wp_reset_postdata();

                    $output .= ob_get_contents();
                    ob_end_clean();

                }
            }

            $output .= "</div>";
            echo $output;
        }

        $this->widget_end( $args );
    }
    
    /**
     * Function to recursively print sub categories of the given category.
     * 
     * @param array $all_categories array of WP_Term object which the result set is restricted to
     * @param WP_Term $current_category
     * @return string
     */
    protected function _list_categories_recursive( $all_categories, $parent_category, $request_current_category=false, $view_all=false ){
        $html = "";
        $has_children = false;
        foreach( $all_categories as $c ){
            if( $c->parent == $parent_category->term_id ){
                $has_children = true;
                $classes = "cat-item cat-item-{$c->term_id}";
                $classes .= !empty( $request_current_category ) && $request_current_category->term_id == $c->term_id ? ' current-cat' : '';
                $html .= sprintf( "<li class='%s'><a href='%s'>%s</a>", $classes, $this->_get_category_link( $c ), $c->name );
                
                $html .= $this->_list_categories_recursive( $all_categories, $c, $request_current_category, false );
                
                $html .= "</li>";
            }
        }
        
        if( $has_children ){
            if( $view_all ){
                $html .= sprintf('<li class="view-all"><a href="%s">%s</a></li>', $this->_get_category_link( $parent_category ), __('All ', 'buddyboss-marketplace') . $parent_category->name );
            }
            
            $html = "<ul class='children'>" . $html . "</ul>";
        }
        
        return $html;
    }
    
    /**
     * Get permalink of a given term.
     * 
     * @param WP_Term $term
     * @return string
     */
    protected function _get_category_link( $term ){
        //check if we already have the permalink of immediate parent.
        //if so, we can simply append current term's slug to parent's url
        //this will save unnecessary db query.
        $url = "";
        if( $term->parent != 0 && isset( $this->_category_links[$term->parent] ) ){
            $url = trailingslashit( $this->_category_links[$term->parent] ) . $term->slug . '/';
        } else {
            $url = get_category_link( $term->term_id );
        }
        
        $this->_category_links[$term->term_id] = $url;
        return $url;
    }
}
