<?php
if( !defined( 'ABSPATH' ) ) exit();

class BMProductQuestionsHelper{
    protected 
        $_url_key = 'qproduct',
        $_meta_key = 'qproduct_id',
        $_product_id = false;
    
    public static function instance() {
        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been run previously
        if ( null === $instance ) {
            $instance = new BMProductQuestionsHelper();
            
            if( !function_exists( 'bp_is_active' ) || !bp_is_active( 'messages' ) )
                return;
            
            add_action( 'bp_before_messages_compose_content',   array( $instance, 'print_js_subject_suffix' ) );
            add_action( 'messages_message_sent',                array( $instance, 'save_message_meta' ) );
            add_filter( 'bp_get_the_thread_subject',            array( $instance, 'prefix_thread_subject' ) );
        }

        // Always return the instance
        return $instance;
    }
    
    public function is_product_question(){
        /*
         * For compose screen, check the query variable
         * For single message thread, check in message meta
         */
        if( 'messages' == bp_current_component() ){
            $product_id = 0;
            switch( bp_current_action() ){
                case 'compose':
                    $this->_product_id = isset( $_GET[ $this->_url_key ] ) ? intval( $_GET[ $this->_url_key ] ) : false;
                    break;
                case 'view':
                    global $thread_template;
                    $this->_product_id = bp_messages_get_meta( $thread_template->thread->thread_id, $this->_meta_key, true );
                    break;
            }
        }
        
        return $this->_product_id;
    }
    
    public function get_product_title(){
        $product_id = $this->is_product_question();
        if( !$product_id )
            return '';
        
        return get_the_title( $product_id );
    }
    
    public function print_js_subject_suffix(){
        if( ( $title = $this->get_product_title() ) == '' )
            return;
        
        $suffix = __( 'Product', 'woocommerce' ) . " : " . $title;
        echo "<input type='hidden' name='{$this->_meta_key}' value='{$this->_product_id}'>";
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $( '#send_message_form [name="subject"]' ).addClass('has_suffix').after( "<span class='suffix question_title'><?php echo $suffix;?></span><div class='suffix_footer'></div>" );
            });
        </script>
        <?php 
    }
    
    public function save_message_meta( $message ){
        if( isset( $_POST[ $this->_meta_key ] ) && !empty( $_POST[ $this->_meta_key ] ) ){
            bp_messages_update_meta( $message->thread_id, $this->_meta_key, intval( $_POST[ $this->_meta_key ] ) );
        }
    }
    
    public function prefix_thread_subject( $subject ){
        if( ( $title = $this->get_product_title() ) == '' )
            return $subject;
        
        $suffix = __( 'Product', 'woocommerce' ) . " : " . $title;
        return "<span class='prefix question_title'>{$suffix}</span>" . $subject;
    }
}

BMProductQuestionsHelper::instance();//instantiate