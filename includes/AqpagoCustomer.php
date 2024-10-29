<?php

class WC_Aqpago_Customer
{
    public function __construct() {
		add_action( 'init', array( $this, 'aqpago_add_cards_endpoint') );  
		add_filter( 'query_vars', array( $this, 'aqpago_add_cards_query_vars'), 0 );  
		add_filter( 'woocommerce_account_menu_items', array( $this, 'aqpago_add_cards_link_my_account') );
		add_action( 'woocommerce_account_aqpago-cards_endpoint', array( $this, 'aqpago_add_cards_content') );

		add_action('wp_ajax_aqpago_remove_card', array( $this, 'aqpago_remove_card_ajax')); 
		add_action('wp_ajax_nopriv_aqpago_remove_card', array( $this, 'aqpago_remove_card_ajax'));

		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts_user' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'aqpago_customer_style' ) );
		
		add_action( 'template_redirect', array( $this, 'javascript_variaveis') );
	}
	
	public function javascript_variaveis() {
		if ( !isset( $_GET[ 'js_global' ] ) ) return;
		
		$nonce = wp_create_nonce('aqpago_remove_card');
		$variaveis_javascript = array(
			'aqpago_remove_card' => $nonce, // This function creates a nonce for our request.
			'xhr_url'            => admin_url('admin-ajax.php') // Way to get the url for the queries dynamically.
		);

		$new_array = array();
		foreach( $variaveis_javascript as $var => $value ) $new_array[] = esc_js( $var ) . " : '" . esc_js( $value ) . "'";

		header("Content-type: application/x-javascript");
		printf('var %s = {%s};', 'js_global', implode( ',', $new_array ) );
		exit;
	}
	
	public function payment_scripts_user() {
		wp_register_script( 'woocommerce_user_mask_aqpago', plugins_url( 'assets/js/mask.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.2.3' );
		wp_enqueue_script( 'woocommerce_user_mask_aqpago' );		
		
		wp_register_script( 'woocommerce_user_modal_aqpago', plugins_url( 'assets/js/jquery.modal.min.js', plugin_dir_path( __FILE__ )), array( 'jquery' ), '1.0.1' );
		wp_enqueue_script( 'woocommerce_user_modal_aqpago' );
		
		wp_register_script( 'woocommerce_user_pay_aqpago', plugins_url( 'assets/js/payments.js', plugin_dir_path( __FILE__ )), array( 'jquery' ), '1.0.4' );
		wp_enqueue_script( 'woocommerce_user_pay_aqpago' );
		
		wp_register_script( 'woocommerce_toastr_aqpago', plugins_url( 'assets/js/toastr.min.js', plugin_dir_path( __FILE__ )), array( 'jquery' ), '1.0.2' );
		wp_enqueue_script( 'woocommerce_toastr_aqpago' );
		
		wp_register_script( 'secure-ajax-access', esc_url( add_query_arg( array( 'js_global' => 1 ), site_url() ) ) );
		wp_enqueue_script( 'secure-ajax-access' );
	}
	
	public function aqpago_remove_card_ajax(){ 
		$cards = get_post_meta(get_current_user_id(), '_cards_saves', true);
		$cards = json_decode($cards, true);
		
		if(is_array($cards)) {
			foreach($cards as $digits => $card){
				
				if($card['id'] == sanitize_text_field($_POST['cardId'])) {
					$cards[$digits]['remove'] = true;
				}
				
			}
		}
		
		update_post_meta(get_current_user_id(), '_cards_saves', json_encode($cards));
		
		wp_send_json(array('success' => 'true', 'cardId' => esc_html(sanitize_text_field($_POST['cardId'])))); 
	}
	
	
	public function aqpago_add_cards_endpoint() {
		add_rewrite_endpoint( 'aqpago-cards', EP_ROOT | EP_PAGES );
	}  

	public function aqpago_add_cards_query_vars($vars) {
		$vars[] = 'aqpago-cards';
		return $vars;
	}  
		
	public function aqpago_add_cards_link_my_account( $items ) {
		$items['aqpago-cards'] = 'Meus Cartões';
		
		// Sort based on priority
		uksort($items, function ($a, $b) {
			$priority  = [
				'dashboard' 		=> 1,
				'orders'  			=> 2,
				'aqpago-cards'  	=> 3,
				'downloads' 	 	=> 4,
				'edit-address'  	=> 5,
				'edit-account'  	=> 6,
				'customer-logout'  	=> 7
			];
			
			// Check if priority has been set otherwise set to zero
			$aPriority = $priority[ $a ] ?? 0;
			$bPriority = $priority[ $b ] ?? 0;
			
			// Equal priorities can stay where they are in the array
			if ( $aPriority == $bPriority ) {
				return 0;
			}
			
			// Adjust sort based on which endpoint has more priority
			return ( $aPriority < $bPriority ) ? - 1 : 1;
		});
		
		return $items;
	} 
	
	public function aqpago_add_cards_content() {
		$cards = get_post_meta(get_current_user_id(), '_cards_saves', true);
		$cards = json_decode($cards, true);
		
		if(is_array($cards)) {
			foreach($cards as $digits => $card){
				if(isset($card['remove']) && $card['remove']){
					unset($cards[$digits]);
				}
			}
		}
		
		$flagVisa = plugins_url('assets/images/visa.png', plugin_dir_path( __FILE__ ) );	
		$background_remove = plugins_url('assets/images/box-remove.svg', plugin_dir_path( __FILE__ ) );
		
		wc_get_template(
			'cards.php', array(
				'cards' => $cards,
				'flagVisa' => $flagVisa,
				'background_remove' => $background_remove
			), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path() . 'customer/'
		); 
	}
	
	public function aqpago_customer_style(){
		wp_register_style( 'aqpago-customer', plugins_url( 'assets/css/aqpago-customer.css', plugin_dir_path( __FILE__ ) ) );
		wp_enqueue_style( 'aqpago-customer' );
	}
}
