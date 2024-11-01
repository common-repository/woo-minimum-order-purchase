<?php
/**
 * Plugin Name: Woocommerce Minimum Order Purchase
 * Description: Add Easily Woocommerce minimum purchase order value for any product
 * Version: 1.1
 * Author: smscentral
 * Author URI: https://www.smscentral.com.au/
 */


//Our base class
class SMSCentral_Controller{

	public function __construct(){
		define('SMSCentral_VERSION', '1.0');
		define('SMSCentral_PLUGIN_SLUG', 'woocommerce-minimum-order');


		add_action('admin_menu', array($this, 'smscentral_admin_menu') );
		add_action('admin_init', array($this, 'smscentral_admin_init') );
		
		add_action('woocommerce_checkout_process', array($this, 'smscentral_check_cart'), 99 );
		add_action('woocommerce_before_cart', array($this, 'smscentral_check_cart'), 99 );
		
	}

	//Sets up the Admin screen
	function smscentral_admin_menu() {
    	$page = add_submenu_page ( 'woocommerce', __( 'Minimum Order', 'smscentral_admin_page' ), __( 'Minimum Order', 'smscentral_admin_page' ), 'manage_woocommerce', 'woo-min-order-settings', array( &$this, 'smscentral_admin_page' ) );
    }


	function smscentral_admin_init() {
		register_setting( SMSCentral_PLUGIN_SLUG . '-settings-group', 'min_amount' );
		register_setting( SMSCentral_PLUGIN_SLUG . '-settings-group', 'error_message' );
	}

	//here we check actual!
	function smscentral_check_cart( $cart ){
		global $woocommerce;

		//get the min purchase amount from setting
		$amt = get_option('min_amount');
			
			// check the amount
			if ($woocommerce->cart->subtotal < intval ( $amt )) {
				
				// Get the error meesage
				$msg = get_option ( 'error_message' );
				
				// show the error message
				wc_print_notice( $msg, $notice_type = 'error');
				
				// Updated v1.1 to differntiate between cart/other				
				if (is_cart ()) {
					wc_print_notice ( $msg, 'error' );
				} else {
					wc_add_notice ( $msg, 'error' );
				}
			}
	}

	//This is the actual admin page
	function smscentral_admin_page(){ ?>
<div class="wrap">
	<h2>Woocommerce Minimum Order Purchase Setup</h2>

	<p>Easily enter the minimum amount of the order along with the error message you would like to display.</p>
	
	<form method="post" action="options.php">
    <?php settings_fields( SMSCentral_PLUGIN_SLUG . '-settings-group' ); ?>
    <?php do_settings_sections( SMSCentral_PLUGIN_SLUG . '-settings-group' ); ?>
    	<table class="form-table">
			<tr valign="top">
				<th scope="row">Minimum Order Amount</th>
				<td><input type="text" name="min_amount" value="<?php echo esc_attr( get_option('min_amount') ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">Minimum Order Error Message</th>
				<td><input type="text" name="error_message" size='80' value="<?php echo esc_attr( get_option('error_message') ); ?>" /></td>
			</tr>

		</table>
    
    <?php submit_button(); ?>
 
</form>
</div>
<?php
	}
}

//Only load the plugin if woocommerce is present
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  
	$SMSCentral_Controller = new SMSCentral_Controller();
}