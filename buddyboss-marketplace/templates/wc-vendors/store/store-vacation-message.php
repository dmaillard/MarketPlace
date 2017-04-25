<?php
/**
 * Display the store vacation message
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php if ( $vacation_mode ) : ?>
<div class="wcv-store-msg">
    <div>
        <?php echo $vacation_msg; ?>
    </div>
</div>
<?php endif; ?>
