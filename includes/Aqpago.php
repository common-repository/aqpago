<?php

class WC_Aqpago
{	
	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		// Load plugin text domain.
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );
		

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			self::includes();
			
			
			add_action( 'woocommerce_after_checkout_validation', array(__CLASS__, 'aqpago_checkout_field_process'), 10, 2);
			
			add_filter( 'woocommerce_payment_gateways', array(__CLASS__, 'add_aqpago_gateway' ) );
			add_filter('woocommerce_checkout_fields', array(__CLASS__, 'aqpago_woocommerce_billing_fields'));
			add_filter('woocommerce_default_address_fields', array(__CLASS__, 'aqpago_woocommerce_ddress_fields'));
			add_filter( 'woocommerce_general_settings', array(__CLASS__, 'aqpago_woocommerce_general_settings'), 10, 1 ); 
			
		} else {
			add_action( 'admin_notices', array(__CLASS__, 'woocommerce_missing_notice' ) );
		}	
	}
	
	public static function aqpago_admin_save() {
		if(!is_admin()) return false;
		
		if(sanitize_text_field($_REQUEST['post_type']) != 'shop_order') return false;
		
		if(intval($_REQUEST['post_ID'])) {
			$orderId 			= intval($_REQUEST['post_ID']);
			$order 				= new WC_Order( $orderId );
			$currentStatus 		= $order->get_status();
			$requestedStautus 	= sanitize_text_field($_REQUEST['order_status']);
			
			if ($requestedStautus == 'on-hold' && $currentStatus == 'completed') {
				//Do your work here
			}
		}
	}	
	
	/**
	 * Includes.
	 */
	private static function includes() {
		include_once plugin_dir_path(__FILE__) . 'AqpagoGateway.php';
		include_once plugin_dir_path(__FILE__) . 'AqpagoCustomer.php';
		include_once plugin_dir_path(__FILE__) . 'AqpagoOrderInfo.php';
		
		// WC_AQPAGO_PLUGIN_FILE set in aqpago.php
		require_once plugin_dir_path( WC_AQPAGO_PLUGIN_FILE ) . 'library/vendor/autoload.php';
		
		$aqpagoCustomer = new WC_Aqpago_Customer;
		$orderInfo = new WC_Aqpago_Order_Info;
	}
	
	public static function aqpago_woocommerce_general_settings($fields)
	{
		if ( ! class_exists( 'WC_Session' ) ) {
			// WC_AQPAGO_PLUGIN_DIR set in aqpago.php
			include_once(  plugin_dir_path( WC_AQPAGO_PLUGIN_DIR ) . '/woocommerce/includes/abstracts/abstract-wc-session.php' );
		}
		
		WC()->session 	= new WC_Session_Handler;
		WC()->customer 	= new WC_Customer;
		
		$woocommerce_fields_billing = (isset(WC()->checkout->checkout_fields['billing'])) ? WC()->checkout->checkout_fields['billing'] : null;
		
		$options_fieds = array();
		
		$options_fieds_document = array('billing_document' => __( 'Padrão AQPago', 'woocommerce' ));
		$options_fieds_phones 	= array();
		$options_fieds_address 	= array(
			'billing_address_1' => 'Endereço - AQPago', 
			'billing_address_2' => 'Número - AQPago', 
			'billing_address_3' => 'Complemento - AQPago', 
			'billing_address_4' => 'Bairro - AQPago'
		);
		
		if(is_array($woocommerce_fields_billing)) {
			foreach($woocommerce_fields_billing as $field => $values){
				$options_fieds = array_merge($options_fieds, array( $field => __( $values['label'], 'woocommerce' ) ));
				$options_fieds_address = array_merge($options_fieds_address, array( $field => __( $values['label'], 'woocommerce' ) ));
			}
		}
		
		$options_fieds_address = array_merge($options_fieds_address, array(
			'billing_address_2' => 'Número - AQPago', 
			'billing_address_3' => 'Complemento - AQPago', 
			'billing_address_4' => 'Bairro - AQPago'
		));
		
		$options_fieds_document = array_merge($options_fieds_document, $options_fieds);
		$options_fieds_phones = array_merge($options_fieds_phones, $options_fieds);
		
		$settings =
			array(

				array(
					'title' => __( 'AQPago - configuração de campos', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'campos para preenchimento do pedido.', 'woocommerce' ),
					'id'    => 'aqpago_fields',
				),
				
				array(
					'title'    => __( 'Campo de documento', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o documento, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_document',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_document',
					'options'  => $options_fieds_document,
				),
				
				array(
					'title'    => __( 'Campo de telefone', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o telefone, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_phone',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_phone',
					'options'  => $options_fieds_phones,
				),
				
				array(
					'title'    => __( 'Endereço', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o endereço, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_address_street',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_address_1',
					'options'  => $options_fieds_address,
				),
				
				array(
					'title'    => __( 'Número', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o número do endereço, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_address_number',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_address_2',
					'options'  => $options_fieds_address,
				),
				
				array(
					'title'    => __( 'Complemento', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o complemento, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_address_complement',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_address_3',
					'options'  => $options_fieds_address,
				),
				
				array(
					'title'    => __( 'Bairro', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o endereço, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_address_district',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_address_4',
					'options'  => $options_fieds_address,
				),
				
				array(
					'title'    => __( 'Cidade', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita a cidade, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_address_city',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_city',
					'options'  => $options_fieds_address,
				),		
				
				array(
					'title'    => __( 'Estado', 'woocommerce' ),
					'desc'     => __( 'Selecione o campo em que o comprador digita o estado, caso não possua deixar o valor padrão.', 'woocommerce' ),
					'id'       => 'woocommerce_aqpago_address_state',
					'default'  => 'all',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width: 350px;',
					'desc_tip' => false,
					'default' => 'billing_state',
					'options'  => $options_fieds_address,
				),
				
				array(
					'type' => 'sectionend',
					'id'   => 'aqpago_fields',
				)
			);
		
		$settings = array_merge($settings, $fields);
		
		return $settings;
	}
	
	public static function aqpago_checkout_field_process($fields, $errors)
	{
		//$errors->add( 'validation', 'CPF / CNPJ é obrigatório!' );
		
	}
	
	public static function aqpago_woocommerce_ddress_fields($fields)
	{
		$address_number	= (get_option('woocommerce_aqpago_address_number')) ? get_option('woocommerce_aqpago_address_number') : 'billing_address_2';
		$address_district	= (get_option('woocommerce_aqpago_address_district')) ? get_option('woocommerce_aqpago_address_district') : 'billing_address_4';
		
		
		if($address_number == 'billing_address_2') {			
			$fields['address_1']['label'] = __('Endereço', 'woocommerce');
			$fields['address_1']['placeholder'] = __('Nome da rua', 'woocommerce');
			
			$fields['address_2']['label'] = __('Número', 'woocommerce');
			$fields['address_2']['placeholder'] = __('Número', 'woocommerce');
			$fields['address_2']['required'] = true;
			$fields['address_2']['class'][0] = 'form-row-first';
			$fields['address_2']['class'][1] = 'address-field';
			$fields['address_2']['label_class'] = array();
		}
		
		$fields['postcode']['priority'] = 49;
		$fields['postcode']['class'][0] = 'form-row-first';
		$fields['country']['priority'] = 81;
		
		//print_r( $fields );die();
		
		if($address_district == 'billing_address_4') {
			
			
			$fields['city']['class'] = array('form-row-last', 'address-field');
		}
		
		return $fields;
	}
	
	public static function aqpago_woocommerce_billing_fields($fields)
	{
		$document 	= (get_option('woocommerce_aqpago_document')) ? get_option('woocommerce_aqpago_document') : '';
		$phone 		= (get_option('woocommerce_aqpago_phone')) ? get_option('woocommerce_aqpago_phone') : '';
		$address_number		= (get_option('woocommerce_aqpago_address_number')) ? get_option('woocommerce_aqpago_address_number') : 'billing_address_2';
		$address_complement	= (get_option('woocommerce_aqpago_address_complement')) ? get_option('woocommerce_aqpago_address_complement') : 'billing_address_3';
		$address_district	= (get_option('woocommerce_aqpago_address_district')) ? get_option('woocommerce_aqpago_address_district') : 'billing_address_4';
		
		$fields['billing']['billing_email']['priority'] = 9;
		$fields['billing']['billing_phone']['priority'] = 31;
		
		if($document == 'billing_document') {
			$fields['billing']['billing_document'] = array(
				'label' => __('CPF / CNPJ', 'woocommerce'), 
				'placeholder' => _x('Digite o documento...', 'placeholder', 'woocommerce'), 
				'required' => true, 
				'clear' => false,
				'type' => 'text', 
				'priority' => 1
			); 
		}		
		
		if($address_number == 'billing_address_2') {
			
			$fields['billing']['billing_address_1']['label'] = __('Endereço', 'woocommerce');
			$fields['billing']['billing_address_1']['placeholder'] = __('Nome da rua', 'woocommerce');
			
			$fields['billing']['billing_address_2']['label'] = __('Número', 'woocommerce');
			$fields['billing']['billing_address_2']['placeholder'] = __('Número', 'woocommerce');
			$fields['billing']['billing_address_2']['class'][0] = 'form-row-first';
			$fields['billing']['billing_address_2']['class'][1] = 'address-field';
		}		
		
		if($address_complement == 'billing_address_3') {
			$fields['billing']['billing_address_3'] = array(
				'label' => __('Complemento', 'woocommerce'),
				'placeholder' => __('Complemento', 'woocommerce'),
				'required' => false,
				'class' => array('form-row-last', 'address-field'),
				'priority' => 61
				
			);
			$fields['shipping']['shipping_address_3'] = array(
				'label' => __('Complemento', 'woocommerce'),
				'placeholder' => __('Complemento', 'woocommerce'),
				'required' => false,
				'class' => array('form-row-last', 'address-field'),
				'priority' => 61
				
			);
		}	
		
		if($address_district == 'billing_address_4') {
			$fields['billing']['billing_city']['class'] = array('form-row-last', 'address-field');
			$fields['shipping']['shipping_city']['class'] = array('form-row-last', 'address-field');
			
			$fields['billing']['billing_address_4'] = array(
				'label' => __('Bairro', 'woocommerce'),
				'placeholder' => __('Bairro', 'woocommerce'),
				'required' => true,
				'class' => array('form-row-first', 'address-field'),
				'priority' => 62
				
			);			
			$fields['shipping']['shipping_address_4'] = array(
				'label' => __('Bairro', 'woocommerce'),
				'placeholder' => __('Bairro', 'woocommerce'),
				'required' => true,
				'class' => array('form-row-first', 'address-field'),
				'priority' => 62
				
			);
		}
		
		return $fields;
	}
	
	/**
	 * Load the plugin text domain for translation.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce', false, dirname( plugin_basename( WC_AQPAGO_PLUGIN_FILE ) ) . '/languages/' );
	}
	
	/**
	 * Add the gateway to WooCommerce.
	 *
	 * @param  array $methods WooCommerce payment methods.
	 *
	 * @return array Payment methods with Aqpago.
	 */
	public static function add_aqpago_gateway( $methods ) {
		$methods[] = 'WC_Aqpago_Gateway';
		
		return $methods;
	}
	
	/**
	 * Hides the Aqpago with payment method with the customer lives outside Brazil.
	 *
	 * @param array $available_gateways Default Available Gateways.
	 *
	 * @return array New Available Gateways.
	 */
	public static function hide_when_outside_brazil( $available_gateways ) {
		// Remove Aqpago gateway.
		if ( isset( $_REQUEST['country'] ) && 'BR' !== sanitize_text_field($_REQUEST['country']) ) {
			// WPCS: input var ok, CSRF ok.
			unset( $available_gateways['aqpago'] );
		}
		
		return $available_gateways;
	}
	
	/**
	 * Get templates path.
	 *
	 * @return string
	 */
	public static function get_templates_path() {
		return plugin_dir_path( WC_AQPAGO_PLUGIN_FILE ) . 'templates/';
	}
}
