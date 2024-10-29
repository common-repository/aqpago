<?php

class WC_Aqpago_Gateway extends WC_Payment_Gateway 
{
	public function __construct() {
		$this->id = "aqpago";
		//$this->icon = '';
		$this->icon = apply_filters( 'woocommerce_pagseguro_icon', plugins_url( '/assets/images/visa.png', plugin_dir_path( __FILE__ ) ) );
		$this->has_fields = true;
		$this->method_title = __( 'AQPago', 'woocommerce' );
		$this->method_description = __( 'Aceite cartão, 2 cartões, cartão mais boleto e boleto.', 'woocommerce' );
		$this->order_button_text = __( 'Finalizar pedido', 'woocommerce' );
		$this->supports = array( 'refunds' );
		
		$this->init_form_fields();
		
		// Load the settings.
		$this->init_settings();

		
		// Define user set variables.
		$this->title     				= $this->get_option( 'title' );
		$this->description 				= $this->get_option( 'description' );
		$this->environment  			= (isset($_POST['woocommerce_aqpago_environment'])) ? sanitize_text_field($_POST['woocommerce_aqpago_environment']) : $this->get_option( 'environment' );		
		$this->multi					= $this->get_option( 'multi', 'yes' );
		$this->document					= $this->get_option( 'document' );
		$this->token  					= (isset($_POST['woocommerce_aqpago_token'])) ? sanitize_text_field($_POST['woocommerce_aqpago_token']) : $this->get_option( 'token' );
		$this->public_token				= $this->get_option( 'public_token' );
		$this->enable_for_methods  		= $this->get_option( 'enable_for_methods' );
		$this->soft_descriptor			= $this->get_option( 'soft_descriptor' );
		$this->method_active			= $this->get_option( 'multi' );
		$this->installments 			= $this->get_option( 'installments' );
		$this->min_total_installments 	= $this->get_option( 'min_total_installments' );
		$this->body_instructions 		= $this->get_option( 'body_instructions' );
		$this->installment_interest_free= $this->get_option( 'installment_interest_free' );
		$this->installment_type 		= $this->get_option( 'installment_type' );
		$this->installment_up_to 		= $this->get_option( 'installment_up_to' );
		$this->tax_1 					= $this->get_option( 'tax_1' );
		$this->tax_2 					= $this->get_option( 'tax_2' );
		$this->tax_3 					= $this->get_option( 'tax_3' );
		$this->tax_4 					= $this->get_option( 'tax_4' );
		$this->tax_5 					= $this->get_option( 'tax_5' );
		$this->tax_6 					= $this->get_option( 'tax_6' );
		$this->tax_7 					= $this->get_option( 'tax_7' );
		$this->tax_8 					= $this->get_option( 'tax_8' );
		$this->tax_9 					= $this->get_option( 'tax_9' );
		$this->tax_10 					= $this->get_option( 'tax_10' );
		$this->tax_11 					= $this->get_option( 'tax_11' );
		$this->tax_12 					= $this->get_option( 'tax_12' );
		
		$this->debug					= $this->get_option( 'debug' );
		$this->instructions				= "Pague com dinheiro na entrega";
		
		$this->field_document			= $this->get_option( 'field_document' );
		$this->field_phone				= $this->get_option( 'field_phone' );
		
		// Active logs.
		if ('yes' === $this->debug ) {
			if (function_exists('wc_get_logger')) {
				$this->log = wc_get_logger();
			} else {
				$this->log = new WC_Logger();
			}
		}
		
		if (!$this->token) {
			add_action( 'admin_notices', array($this, 'aqpago_admin_notification_erro_data_set') );
		}
		

		/** create process Webhook **/
		if (isset($_POST['woocommerce_aqpago_token']) && sanitize_text_field($_POST['woocommerce_aqpago_token']) != '') {
			$this->check_public_token(true);
			$this->check_webhook();
		} else {
			$this->check_public_token(false);
		}
		
		add_action('woocommerce_update_options_payment_gateways_'. $this->id, array ($this, 'process_admin_options'));
		add_action('woocommerce_thankyou_aqpago', array($this, 'aqpago_woocommerce_tankyou'), 1 );
		add_action('woocommerce_order_details_before_order_table', array($this, 'aqpago_order_details_before_order_table'), 10, 4 );
		
		add_action( 'woocommerce_api_aqpago_webhook', array( $this, 'webhook' ) );
		
		add_action('woocommerce_order_status_changed', array( $this, 'woo_order_status_change_custom'), 10, 3);

		// Action hook to load custom JavaScript
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );	
		add_action( 'admin_enqueue_scripts', array( $this, 'payment_admin_scripts' ) );	
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_styles' ) );

		add_action( 'woocommerce_checkout_billing', array( $this, 'payment_load_aqpago_session' ) );
	}
	
	public function payment_load_aqpago_session() {
/* 		wc_get_template(
			'session.php', array(
				'public_token' => $this->public_token,
			), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path()
		); */
	}

	private function check_public_token($force = false) {
		/**
		 * verified public_token is empty
		 */
		if ($this->get_option( 'public_token' ) == '' || $force == true) {
			require_once( plugin_dir_path(__DIR__) . 'sdk/Includes.php' );
			
			$seller_doc 	= preg_replace('/[^0-9]/', '', $this->document);
			$seller_token 	= $this->token;
			$sellerAqpago   = new Aqbank\Apiv2\SellerAqpago($seller_doc, $seller_token, 'modulo woocommerce');
			
			if ($this->environment == 'production') {
				// Ambiente de produção
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::production();
			} else {
				// Ambiente de homologação
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::sandbox();
			}
			
			try {
				$public_token = (new \Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->getPublicToken();
				
				$this->update_option( 'public_token', $public_token);
				$this->public_token = $this->get_option( 'public_token' );
				
			} catch (Exception $e) {
				die( $e->getMessage() );
				
				return;
			}
		}
	}

	public function woo_order_status_change_custom($order_id, $old_status, $new_status ) {
		
		/** Cancel order **/
		if ($new_status == 'cancelled') {
			require_once( plugin_dir_path(__DIR__) . 'sdk/Includes.php' );
			
			$seller_doc 	= preg_replace('/[^0-9]/', '', $this->document);
			$seller_token 	= $this->token;
			$sellerAqpago   = new Aqbank\Apiv2\SellerAqpago($seller_doc, $seller_token, 'modulo woocommerce');
			
			if ($this->environment == 'production') {
				// Ambiente de produção
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::production();
			} else {
				// Ambiente de homologação
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::sandbox();
			}
			
			$aqpagoJson	= get_post_meta($order_id, '_aqpago_response', true);
			$aqpagoJson = json_decode($aqpagoJson, true);
			
			$orderAq = new Aqbank\Apiv2\Aqpago\Order();
			$orderAq->setOrderId($aqpagoJson['id']);
			
			try {
				$response = (new Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->cancelOrder($orderAq);
				
				update_post_meta($order_id, '_aqpago_response', json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT));
				update_post_meta($order_id, '_aqpago_closed', 'true');
				
				if ($this->debug == 'yes') $this->log->info( 'Cancel: ' . json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );
			
			} catch (Exception $e) {
				$errorMessage = $e->getMessage();
				wp_add_inline_script( "aqpago-erro-message", "alert('". esc_js( $errorMessage )."');" );
			}
		}
	}
	
	private function check_webhook() {
		require_once( plugin_dir_path(__DIR__) . 'sdk/Includes.php' );
		
		$seller_doc 	= preg_replace('/[^0-9]/', '', $this->document);
		$seller_token 	= $this->token;
		$sellerAqpago   = new Aqbank\Apiv2\SellerAqpago($seller_doc, $seller_token, 'modulo woocommerce');
		
		if ($this->environment == 'production') {
			// Ambiente de produção
			$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::production();
		} else {
			// Ambiente de homologação
			$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::sandbox();
		}
		
		try {
			$Webhooks = (new \Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->getWebhooks();
		} catch (Exception $e) {
			//die( $e->getMessage() );
			
			return;
		}
		
		$response = json_encode(array_filter($Webhooks->jsonSerialize()));
		$response = json_decode($response, true);
		
		$baseUrl = get_site_url();
		
		$configWebHook = false;
		if (isset($response['data']) && count($response['data'])) {
			foreach ($response['data'] as $k => $hook) {
				if ($hook['url'] == $baseUrl . '/wc-api/aqpago_webhook') {
					$configWebHook = true;
				}
			}
		}
		
		if (!$configWebHook) {
			$webhook = new \Aqbank\Apiv2\Aqpago\Webhook();
			$webhook->setEvent([
				"transation.success",
				"transaction.succeeded",
				"transaction.reversed",
				"transaction.failed",
				"transaction.canceled",
				"transaction.disputed",
				"transaction.charged_back",
				"transaction.pre_authorized"
			])
			->setUrl( $baseUrl . '/wc-api/aqpago_webhook' )
			->setDescription('modulo woocommerce')
			->setMethod('POST');
			
			try {
				$aqpago 	= (new \Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->createWebhook($webhook);
				$response 	= json_encode(array_filter($aqpago->jsonSerialize()));
				$response 	= json_decode($response, true);
				
				if(!isset($response['success']) || !$response['success']){
					add_action( 'admin_notices', array($this, 'aqpago_admin_notification_erro') );
				} else {
					add_action( 'admin_notices', array($this, 'aqpago_admin_notification_success') );
				}
			
			} catch (Exception $e) {
				// die( $e->getMessage() );
				
				return;
			}
		}
	}
	
	public function aqpago_admin_notification_success() {
		$class = 'notice notice-success is-dismissible';
		$message = __( 'Configuração 21de notificação salva com sucesso!', 'woocommerce' );
 
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	
	}
	
	public function aqpago_admin_notification_erro_data_set(){
		$class = 'notice notice-error';
		$message = __( 'AQPAGO token está vazio, para utilizar o pagamento da AQPAGO é necessário configurar os dados.', 'woocommerce' );
 
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
	
	public function aqpago_admin_notification_erro(){
		$class = 'notice notice-error';
		$message = __( 'Verifique seu AQPAGo token, ele pode estar bloqueado ou ter sido alterado!', 'woocommerce' );
 
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
	
	public function aqpago_order_details_before_order_table( $order, $sent_to_admin = '', $plain_text = '', $email = '' ) {
		// Only on "My Account" > "Order View"
		if (is_wc_endpoint_url('view-order')) {
			
			wp_register_style( 'aqpago-toastr', plugins_url( 'assets/css/toastr.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'aqpago-toastr' );
			wp_register_style( 'aqpago-modal-pagamentos', plugins_url( 'assets/css/jquery.modal.min.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'aqpago-modal-pagamentos' );		
			wp_register_style( 'aqpago-pagamentos', plugins_url( 'assets/css/aqpago.css', plugin_dir_path( __FILE__ ) ) );
			wp_enqueue_style( 'aqpago-pagamentos' );
			wp_register_script( 'woocommerce_mask_aqpago', plugins_url( 'assets/js/mask.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.2.3' );
			wp_enqueue_script( 'woocommerce_mask_aqpago' );		
			wp_register_script( 'woocommerce_modal_aqpago', plugins_url( 'assets/js/jquery.modal.min.js', plugin_dir_path( __FILE__ )), array( 'jquery' ), '1.0.1' );
			wp_enqueue_script( 'woocommerce_modal_aqpago' );
			wp_register_script( 'woocommerce_pay_aqpago', plugins_url( 'assets/js/payments.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.4' );
			wp_enqueue_script( 'woocommerce_pay_aqpago' );
			wp_register_script( 'woocommerce_toastr_aqpago', plugins_url( 'assets/js/toastr.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.2' );
			wp_enqueue_script( 'woocommerce_toastr_aqpago' );
		
			
			$this->aqpago_woocommerce_tankyou( $order->get_id() );
		}
	}
	
	public function get_bar_code_img( $ticket_bar_code ) {
		$barCode 	= preg_replace('/^(\d{4})(\d{5})\d{1}(\d{10})\d{1}(\d{10})\d{1}(\d{15})$/', '$1$5$2$3$4', $ticket_bar_code);
		$generator 	= new \Picqer\Barcode\BarcodeGeneratorPNG();
		$bar_code   = $generator->getBarcode($barCode, $generator::TYPE_INTERLEAVED_2_5);
		$bar_code_img = base64_encode($bar_code);		
		
		return $bar_code_img;
	}
	
	public function aqpago_woocommerce_tankyou($order_id) {
		$orderWoocommerce = new WC_Order($order_id);
		$payment_method   = get_post_meta($order_id, '_payment_method', true);
		
		if ($payment_method == 'aqpago') {
			$type       	  = get_post_meta($order_id, '_type_payment', true);
			$response   	  = get_post_meta($order_id, '_aqpago_response', true);
			$response   	  = json_decode($response, true);
			
			if (isset($response['payments'])) {
				$title = "";
				if ($type == 'ticket') {
					$title = __( 'Boleto', 'woocommerce' ); 
				} elseif ($type == 'credit') {
					$title = __( 'Cartão de Crédito', 'woocommerce' ); 
				} elseif ($type == 'credit_multiple') {
					$title = __( '2 Cartões', 'woocommerce' ); 
				} elseif ($type == 'ticket_multiple') {
					$title = __( 'Cartão + Boleto', 'woocommerce' ); 
				}
				
				$flagVisa = plugins_url('assets/images/visa.png', plugin_dir_path( __FILE__ ) );
				
				wc_get_template(
					'response.php', array(
						'id'				=> $order_id,
						'status' 			=> $this->trans_erros( $response['status'] ),
						'order'				=> $orderWoocommerce,
						'flagVisa'			=> $flagVisa,
						'context'			=> $this,
						'response'			=> $response,
					), 
					'woocommerce/aqpago/', 
					WC_Aqpago::get_templates_path() . '/success/'
				);
			}
		}
	}
	
	public function init_form_fields() {
		
		$this->form_fields = array(
			'enabled' => array(
				'title' => __ ('Enable/Disable', 'woocommerce'), 
				'type' => 'checkbox', 
				'label' => __ ('Habilitar AQPago','woocommerce'), 
				'default' => 'yes'
			),
			'title' => array( 
				'title' => __ ('Titulo',' woocommerce'), 
				'type' =>' text ', 
				'description' => __ ('Isso controla o título que o usuário vê durante o checkout. ',' woocommerce'), 
				'default' => __ ('AQPago pagamentos',' woocommerce')
			), 			
			'description' => array( 
				'title' => __ ('Descrição',' woocommerce'), 
				'type' =>' text ', 
				'description' => __ ('Isso controla a descrição que o usuário vê durante o checkout.',' woocommerce'), 
				'default' => __ ('',' woocommerce')

			), 
			'environment' => array(
				'title' => __( 'Ambiente', 'woocommerce' ),
				'type' => 'select',
				'description' => __( 'Utilizar produção para pagamentos reais, sandbox para usar ambiente de teste.', 'woocommerce' ),
				'default' => 'production',
				'class' => 'wc-enhanced-select',
				'options' => array(
					'production'  => __( 'Produção', 'woocommerce' ),
					'sandbox'  => __( 'Sandbox', 'woocommerce' )
				)
			),			
			'document' => array(
				'title' => __( 'Documento', 'woocommerce' ),
				'type' => 'text',
				'description' =>  __( 'Documento do vendendor.', 'woocommerce' ),
				'default' => ''
			),
			'token' => array(
				'title' => __( 'AQPAGO Token', 'woocommerce' ),
				'type' => 'textarea',
				'description' => sprintf( __( 'Obtenha seu token em %s menu Integrações -> Acesso ShopAQPago', 'woocommerce' ), '<a href="https://aqbank.app" target="_blank" >' . __( 'https://aqbank.app', 'woocommerce' ) . '</a>' ),
				'default' => ''
			),
			'enable_for_methods' => array(
				'title'             => __( 'Ativar métodos', 'woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'css'               => 'width: 400px;',
				'default'           => array(0 => 'credit',1 => 'credit_multiple', 2 => 'ticket_multiple', 3 => 'pix', 4 => 'ticket'),
				'description'       => __( 'Meios de pagamento que aparecem para o cliente no checkout.', 'woocommerce' ),
				'options'           => array(
					'credit'  => __( 'Cartão de Crédito', 'woocommerce' ),
					'credit_multiple'  => __( '2 Cartão de Crédito', 'woocommerce' ),
					'ticket_multiple'  => __( 'Cartão & Boleto', 'woocommerce' ),
					'pix'  => __( 'PIX', 'woocommerce' ),
					'ticket'  => __( 'Boleto', 'woocommerce' ),
				),
				'desc_tip'          => true,
				'custom_attributes' => array(
					'data-placeholder' => __( 'Selecione os métodos de pagamento', 'woocommerce' )
				)
			),
			'installments' => array(
				'title' => __( 'Parcelar em até', 'woocommerce' ),
				'type' => 'select',
				'description' => __( 'Define quantas parcelas sua loja ofertará no checkout', 'woocommerce' ),
				'desc_tip' => true,
				'default' => '12',
				'class' => 'wc-enhanced-select',
				'options' => array(
					'1'  => __( '1x', 'woocommerce' ),
					'2'  => __( '2x', 'woocommerce' ),
					'3'  => __( '3x', 'woocommerce' ),
					'4'  => __( '4x', 'woocommerce' ),
					'5'  => __( '5x', 'woocommerce' ),
					'6'  => __( '6x', 'woocommerce' ),
					'7'  => __( '7x', 'woocommerce' ),
					'8'  => __( '8x', 'woocommerce' ),
					'9'  => __( '9x', 'woocommerce' ),
					'10' => __( '10x', 'woocommerce' ),
					'11' => __( '11x', 'woocommerce' ),
					'12' => __( '12x', 'woocommerce' )
				)
			),
			'min_total_installments' => array(
				'title' => __( 'Valor mínimo para parcelamento', 'woocommerce' ),
				'type' => 'text',
				'description' => sprintf( __( 'Define um valor mínimo para cada parcela, deixar como 0 para qualquer valor.', 'woocommerce' )),
				'default' => '0',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
				'class' => 'aqpago-price',
			),
			'installment_interest_free' => array(
				'title' => __( 'Parcelar com juros', 'woocommerce' ),
				'type' => 'select',
				'description' => __( 'Cobrança de juros para o cliente.', 'woocommerce' ),
				'desc_tip' => false,
				'default' => '0',
				'class' => 'wc-enhanced-select',
				'options' => array(
					'0'  => __( 'Não', 'woocommerce' ),
					'1'  => __( 'Sim', 'woocommerce' )
				)
			),
			'installment_type' => array(
				'title' => __( 'Tipo de taxas', 'woocommerce' ),
				'type' => 'select',
				'description' => __( 'Ao utilizar Taxa AQPago será aplicado a taxa do seu plano para cada parcela.', 'woocommerce' ),
				'desc_tip' => false,
				'default' => 'aqpago',
				'class' => 'wc-enhanced-select',
				'options' => array(
					'aqpago'  => __( 'Taxas do meu plano AQPago', 'woocommerce' ),
					'custom'  => __( 'Taxas personalizadas', 'woocommerce' )
				)
			),
			'installment_up_to' => array(
				'title' => __( 'Parcelar com juros a partir de', 'woocommerce' ),
				'type' => 'select',
				'description' => __( 'A cobrança de taxa inicia em, total cobrado será valor do pedido + taxa ', 'woocommerce' ),
				'desc_tip' => false,
				'default' => '0',
				'class' => 'wc-enhanced-select',
				'options' => array(
					'0'  => __( 'Todas as parcelas', 'woocommerce' ),
					'1'  => __( '1x', 'woocommerce' ),
					'2'  => __( '2x', 'woocommerce' ),
					'3'  => __( '3x', 'woocommerce' ),
					'4'  => __( '4x', 'woocommerce' ),
					'5'  => __( '5x', 'woocommerce' ),
					'6'  => __( '6x', 'woocommerce' ),
					'7'  => __( '7x', 'woocommerce' ),
					'8'  => __( '8x', 'woocommerce' ),
					'9'  => __( '9x', 'woocommerce' ),
					'10' => __( '10x', 'woocommerce' ),
					'11' => __( '11x', 'woocommerce' ),
					'12' => __( '12x', 'woocommerce' )
				)
			),
			'tax_1' => array(
				'title' => __( 'Taxa de juros em 1x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 4.20 = 4,2%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '4.20',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_2' => array(
				'title' => __( 'Taxa de juros em 2x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 5.60 = 5,6%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '5.60',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_3' => array(
				'title' => __( 'Taxa de juros em 3x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 6.89 = 6,89%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '6.89',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_4' => array(
				'title' => __( 'Taxa de juros em 4x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 7.31 = 7,31%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '7.31',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_5' => array(
				'title' => __( 'Taxa de juros em 5x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 8.10 = 8,1%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '8.10',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_6' => array(
				'title' => __( 'Taxa de juros em 6x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 9.60 = 9,6%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '9.60',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_7' => array(
				'title' => __( 'Taxa de juros em 7x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 12.33 = 12,33%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '12.33',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_8' => array(
				'title' => __( 'Taxa de juros em 8x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 12.90 = 12,9%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '12.90',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_9' => array(
				'title' => __( 'Taxa de juros em 9x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 13.35 = 13,35%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '13.35',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_10' => array(
				'title' => __( 'Taxa de juros em 10x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 13.80 = 13,8%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '13.80',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_11' => array(
				'title' => __( 'Taxa de juros em 11x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 14.10 = 14,1%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '14.10',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'tax_12' => array(
				'title' => __( 'Taxa de juros em 12x', 'woocommerce' ),
				'type' => 'text',
				'description' => __( 'Exemplo 14.40 = 14,4%', 'woocommerce' ),
				'class' => 'aqpago-display-none aqpago-tax',
				'default' => '14.40',
				'custom_attributes' => array( 'step' => 'any', 'min' => '0' ),
			),
			'debug' => array(
				'title'       => __( 'Gravar Log', 'woocommerce' ),
				'type'        => 'checkbox',
				'label'       => __( 'Ativar log', 'woocommerce' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Gravar log das transações, desativar ao finalizar os testes.', 'woocommerce' ), $this->get_log_view() ),
			)
		);
	}
	
	/**
	 * Payment fields.
	 */
	public function payment_fields() {
		global $woocommerce;
		global $wp;
		
		wp_enqueue_script( 'wc-credit-card-form' );
		
		$description = $this->get_description();
		if ( $description ) {
			$description = strip_tags($description);
			echo esc_html( $description );
		}
		
		
		$cart_total = $this->get_order_total();
		$items = $woocommerce->cart->get_cart();
		$installments = $this->installments;
		
		
		/** Valid total installments **/
		if ($this->min_total_installments > 0) {
			if (substr_count($this->min_total_installments, ".") && substr_count($this->min_total_installments, ",")) {
				$this->min_total_installments = str_replace(".", "", $this->min_total_installments);
				$this->min_total_installments = str_replace(",", ".", $this->min_total_installments);
			} elseif (substr_count($this->min_total_installments, ",") && !substr_count($this->min_total_installments, ".")) {
				$this->min_total_installments = str_replace(",", ".", $this->min_total_installments);
			}
			
			$totalinstallments 	= $cart_total / $this->min_total_installments;
			if (substr_count(".", $totalinstallments)) {
				$totalinstallments 	= explode(".", $totalinstallments);
				$installments		= (int) $totalinstallments[0];
			} else {
				$installments = (int) $totalinstallments;
			}
			
			
			if(!$installments) $installments = $this->installments;
			
			if ($installments > $this->installments) {
				$installments = $this->installments;
			}
		}
		
		if ($installments > 12) $installments = 12;
		if ($installments < 1) $installments = 1;
		
		$flagVisa = plugins_url('assets/images/visa.png', plugin_dir_path( __FILE__ ) );
		$ajaxurl  = admin_url( 'admin-ajax.php' );
		
		$cards = get_post_meta(get_current_user_id(), '_cards_saves', true);
		$cards = strip_tags($cards);
		$cards = json_decode($cards, true);
		
		if (is_array($cards)) {
			foreach ($cards as $digits => $card) {
				$cards[$digits]['card_id'] 		= esc_attr($card['id']);
				$cards[$digits]['four_first'] 	= esc_attr($card['first4_digits']);
				$cards[$digits]['four_last']  	= esc_attr($card['last4_digits']);
				$cards[$digits]['flag']  		= esc_attr(strtolower($card['flag']));
				
				if (isset($card['remove']) && $card['remove']) {
					unset($cards[$digits]);
				} 
			}
		}
		
		if (!is_array($cards) || count($cards) == 0) {
			$cards = 'false';
			$totalSavedCards = 0;
		} else {
			$totalSavedCards = count($cards);
			$cards = json_encode($cards);
		}
		
		// var $cards -> went through strip_tags to remove html tags in line 426
		wp_print_inline_script_tag( 'var savedCards = ' . $cards );
		
		if (is_user_logged_in()) {
			$user_id 	= get_current_user_id(); 
			$customer 	= new WC_Customer( $user_id );
			$last_order = $customer->get_last_order();
			
			if (is_object($last_order)) {
				if (method_exists($last_order, 'get_id')) {
					$order_id	= $last_order->get_id(); 
					$orderWoo 	= new WC_Order( $order_id );
					$closed		= get_post_meta($order_id, '_aqpago_closed', true); 
					
					$aqpagoJson	= get_post_meta($order_id, '_aqpago_response', true);
					$aqpagoJson = json_decode($aqpagoJson, true);
					
					if (isset($aqpagoJson['amount'])) {
						/** Pedido fechado **/
						if ($orderWoo->get_total() != $aqpagoJson['amount'] || $closed == 'true') {
							$aqpagoJson	= false;
						}
					}
				} else {
					$aqpagoJson	= false;
				}
			} else {
				$aqpagoJson	= false;
			}
		} else {
			$aqpagoJson	= false;
		}
		
		if (isset($wp->query_vars['order-pay']) && absint($wp->query_vars['order-pay']) > 0) {
			$order_id = absint($wp->query_vars['order-pay']); // The order ID
			
			$closed		= get_post_meta($order_id, '_aqpago_closed', true); 
			$aqpagoJson	= get_post_meta($order_id, '_aqpago_response', true);
			$aqpagoJson = json_decode($aqpagoJson, true);
			
		}
		
		$installMap = [];
		
		for ($p=1;$p<=$installments;$p++) {
			$installMap[$p]['option'] = $p .  'x';
			$installMap[$p]['fees'] = ($this->installment_interest_free) ? __('sem juros','woocommerce') : '';
			$installMap[$p]['price'] = round(($cart_total / $p), 2);
			$installMap[$p]['total'] = $cart_total;
			$installMap[$p]['tax'] = 0;
		}
		
		if ($this->installment_interest_free) {
			
			$this->installment_up_to = ($this->installment_up_to == 0) ? 1 : $this->installment_up_to;
			
			for ($p=$this->installment_up_to;$p<=$installments;$p++) {
				$_var = 'tax_' . $p;
				$_tax = $this->$_var;
				$_val = ($cart_total  / (100 - $_tax)) * 100;
				
				$installMap[$p]['option'] = $p .  'x';
				$installMap[$p]['fees'] = __('com juros','woocommerce');
				$installMap[$p]['price'] = round(($_val / $p), 2);
				$installMap[$p]['total'] = round($_val, 2);
				$installMap[$p]['tax'] = $_tax;
			}			
		}
		
		wp_print_inline_script_tag( 'var installMap = ' . json_encode($installMap) );
		
		if ($this->enable_for_methods == '') {
			$this->enable_for_methods = array(0 => 'credit');
		}

		$backgroundColor = '#ffffff';
		
		wc_get_template(
			'session.php', array(
				'public_token' => $this->public_token,
			), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path()
		);

		wc_get_template(
			'form-checkout.php', array(
				'id'        			=> $this->id,
				'cart_total'        	=> $cart_total,
				'flagVisa'        		=> $flagVisa,
				'ajaxurl'        		=> $ajaxurl,
				'totalSavedCards'		=> $totalSavedCards,
				'multi'         		=> $this->multi,
				'installments'         	=> $installments,
				'installMap'         	=> $installMap,
				'enable_for_methods'   	=> $this->enable_for_methods,
				'aqpagoJson'         	=> $aqpagoJson,
				'min_total_installments'=> $this->min_total_installments,
				'field_document'		=> $this->field_document,
				'field_phone'			=> $this->field_phone,
				'backgroundColor'       => $backgroundColor,
				'public_token' 			=> $this->public_token,
				'flags'              	=> plugins_url( 'assets/images/aqpago.png', plugin_dir_path( __FILE__ ) ),
			), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path()
		);
		
		wc_get_template(
			'first-card.php', array(
				'id'        			=> $this->id,
				'cart_total'        	=> $cart_total,
				'multi'         		=> $this->multi,
				'installments'         	=> $installments,
				'min_total_installments'=> $this->min_total_installments,
				'field_document'		=> $this->field_document,
				'field_phone'			=> $this->field_phone,
				'flags'              	=> plugins_url( 'assets/images/aqpago.png', plugin_dir_path( __FILE__ ) ),
			), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path() . 'modal/'
		);		
		
		wc_get_template(
			'second-card.php', array(
				'id'        			=> $this->id,
				'cart_total'        	=> $cart_total,
				'multi'         		=> $this->multi,
				'installments'         	=> $installments,
				'min_total_installments'=> $this->min_total_installments,
				'field_document'		=> $this->field_document,
				'field_phone'			=> $this->field_phone,
				'flags'              	=> plugins_url( 'assets/images/aqpago.png', plugin_dir_path( __FILE__ ) ),
			), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path() . 'modal/'
		);
	}
	

	public function payment_styles() {
		wp_register_style( 'aqpago-toastr', plugins_url( 'assets/css/toastr.css', plugin_dir_path( __FILE__ ) ) );
		wp_enqueue_style( 'aqpago-toastr' );
		
		wp_register_style( 'aqpago-modal-pagamentos', plugins_url( 'assets/css/jquery.modal.min.css', plugin_dir_path( __FILE__ ) ) );
		wp_enqueue_style( 'aqpago-modal-pagamentos' );		
		
		wp_register_style( 'aqpago-pagamentos', plugins_url( 'assets/css/aqpago.css', plugin_dir_path( __FILE__ ) ), array(), '7.0.5' );
		wp_enqueue_style( 'aqpago-pagamentos' );
	}
	
	public function payment_admin_scripts() {
		wp_register_script( 'woocommerce_mask_aqpago', plugins_url( 'assets/js/mask.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.2.3' );
		wp_enqueue_script( 'woocommerce_mask_aqpago' );		
		
		wp_register_script( 'woocommerce_admin_aqpago', plugins_url( 'assets/js/admin.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.1' );
		wp_enqueue_script( 'woocommerce_admin_aqpago' );	
	}
	
	public function payment_scripts() {
		wp_register_script( 'woocommerce_mask_aqpago', plugins_url( 'assets/js/mask.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.2.3' );
		wp_enqueue_script( 'woocommerce_mask_aqpago' );		
		
		wp_register_script( 'woocommerce_modal_aqpago', plugins_url( 'assets/js/jquery.modal.min.js', plugin_dir_path( __FILE__ )), array( 'jquery' ), '1.0.1' );
		wp_enqueue_script( 'woocommerce_modal_aqpago' );
		
		wp_register_script( 'woocommerce_pay_aqpago', plugins_url( 'assets/js/payments.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.4' );
		wp_enqueue_script( 'woocommerce_pay_aqpago' );
		
		wp_register_script( 'woocommerce_toastr_aqpago', plugins_url( 'assets/js/toastr.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '1.0.2' );
		wp_enqueue_script( 'woocommerce_toastr_aqpago' );
	}
	
	public function process_refund($order_id, $amount = null, $reason = '') {
		$aqpagoJson	= get_post_meta($order_id, '_aqpago_response', true);
		$aqpagoJson = json_decode($aqpagoJson, true);
		
		if ($this->debug == 'yes') $this->log->info( 'Refund: ' . json_encode($aqpagoJson), array( 'source' => 'aqpago-refund' ) );
		
		if (isset($aqpagoJson['status']) && $aqpagoJson['status'] != 'ORDER_CANCELED') {
			
			require_once( plugin_dir_path(__DIR__) . '../sdk/Includes.php' );
			
			$seller_doc 	= preg_replace('/[^0-9]/', '', $this->document);
			$seller_token 	= $this->token;
			$sellerAqpago   = new Aqbank\Apiv2\SellerAqpago($seller_doc, $seller_token, 'modulo woocommerce');
			
			if ($this->environment == 'production') {
				// Ambiente de produção
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::production();
			} else {
				// Ambiente de homologação
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::sandbox();
			}
			
			$aqpagoJson	= get_post_meta($order_id, '_aqpago_response', true);
			$aqpagoJson = json_decode($aqpagoJson, true);
			
			$orderAq = new Aqbank\Apiv2\Aqpago\Order();
			$orderAq->setOrderId($aqpagoJson['id']);
			
			try {
				$response = (new Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->cancelOrder($orderAq);
				
				if (!is_object($response)) {
					return false;
				} else {
					if ($this->debug == 'yes') $this->log->info( 'Cancel: ' . json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-refund' ) );
					
					if ($response->getStatus()) {
						update_post_meta($order_id, '_aqpago_response', json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT));
						update_post_meta($order_id, '_aqpago_closed', 'true');
						return true;
					} else {
						return false;
					}
				}
				
			} catch (Exception $e) {
				if($this->debug == 'yes') $this->log->info( 'Refund erro: ' . $e->getMessage(), array( 'source' => 'aqpago-refund' ) );
				
				return false;
			}	
		} else {
			return false;
		}
	}	
	
	private function validTaxvat($taxvat) {
	 
		// Extrai somente os números
		$taxvat = preg_replace( '/[^0-9]/is', '', $taxvat );
		 
		// Verifica se foi informado todos os digitos corretamente
		if (strlen($taxvat) != 11) {
			return false;
		}

		// Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
		if (preg_match('/(\d)\1{10}/', $taxvat)) {
			return false;
		}
		
		// Faz o calculo para validar o CPF
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $taxvat[$c] * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($taxvat[$c] != $d) {
				return false;
			}
		}
		
		return true;
	}
	
	public function validate_fields() {
		
		$aqpago_type_payment = sanitize_text_field($_POST['aqpago_type_payment']);
		
		if (empty($aqpago_type_payment) || $aqpago_type_payment == '') {
			wc_add_notice(  __('Selecione uma forma de pagamento!', 'woocommece'), 'error' );
			return false;
		} elseif (
			$aqpago_type_payment != 'credit' && 
			$aqpago_type_payment != 'credit_multiple' &&
			$aqpago_type_payment != 'ticket' &&
			$aqpago_type_payment != 'ticket_multiple'
		) {
			wc_add_notice(  'Tipo de pagamento inválido!', 'error' );
			return false;
		}
		
		if ($this->debug == 'yes') $this->log->info( 'aqpago_type_payment: ' . $aqpago_type_payment, array( 'source' => 'aqpago-validate' ) );
		
		
		// valid credit payment informations
		if ($aqpago_type_payment == 'credit') {
			// saved card payment
			if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first'])) {
				$one_cc_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
				$one_cc_card_cid = intval($_POST['aqpago_one_cc_card_cid']);
				$one_cc_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
				
				if (!$one_cc_card_installments) {
					wc_add_notice(  __('Parcelas do cartão inválido!', 'woocommece'), 'error' );
					return false;
				}				
				if (!$one_cc_card_cid) {
					wc_add_notice(  __('Códogio do cartão inválido!', 'woocommece'), 'error' );
					return false;
				}	
				if (empty($one_cc_card_id)) {
					wc_add_notice(  __('ID do cartão inválido!', 'woocommece'), 'error' );
					return false;
				}
			} else {
				$one_cc_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
				$one_cc_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
				$one_cc_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
				$one_cc_card_month = intval($_POST['aqpago_one_cc_card_month']);
				$one_cc_card_year = intval($_POST['aqpago_one_cc_card_year']);
				$one_cc_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
				$taxvat = preg_replace('/[^0-9]/is', '',$_POST['aqpago_one_cc_card_taxvat']);
				
				// payment with new card
				if (!$one_cc_card_number || strlen($one_cc_card_number) < 12) {
					wc_add_notice(  __('Número do cartão inválido!', 'woocommece'), 'error' );
					return false;
				}	
				if (!$one_cc_card_installments) {
					wc_add_notice(  __('Parcela inválida para o cartão ' . $one_cc_card_number, 'woocommece'), 'error' );
					return false;
				}					
				if (empty($one_cc_card_owner)) {
					wc_add_notice(  __('Nome do proprietário do cartão '.$one_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}				
				if (!$one_cc_card_month || $one_cc_card_month < 1 || $one_cc_card_month > 12) {
					wc_add_notice(  __('Mês de validade do cartão '.$one_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
				if (!$one_cc_card_year || $one_cc_card_year < date('Y')) {
					wc_add_notice(  __('Ano de validade do cartão '.$one_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
				if (!$one_cc_card_cid) {
					wc_add_notice(  __('Código do cartão '.$one_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}	
				if (!$this->validTaxvat($taxvat)) {
					wc_add_notice(  __('CPF do proprietário do cartão '.$one_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
			}
		} elseif ($aqpago_type_payment == 'credit_multiple') { // valid multi credit
			// Multiple payment with error that the customer updated the page!
			if (isset($_POST['aqpago_updatemulti']) && sanitize_text_field($_POST['aqpago_updatemulti']) == 'true') {
				$two_cc_card_value = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
				$two_cc_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_number']);
				$two_cc_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
				$two_cc_card_owner = sanitize_text_field($_POST['aqpago_two_cc_card_owner']);
				$two_cc_card_month = intval($_POST['aqpago_two_cc_card_month']);
				$two_cc_card_year = intval($_POST['aqpago_two_cc_card_year']);
				$two_cc_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
				$taxvat = preg_replace('/[^0-9]/is', '',$_POST['aqpago_two_cc_card_taxvat']);
				
				if (!is_numeric($two_cc_card_value)) {
					wc_add_notice(  __('Valor a pagar no cartão inválido!', 'woocommece'), 'error' );
					return false;
				}	
				if (!$two_cc_card_number || strlen($two_cc_card_number) < 12) {
					wc_add_notice(  __('Número do cartão inválido!', 'woocommece'), 'error' );
					return false;
				}				
				if (!$two_cc_card_installments || $two_cc_card_installments < 1 || $two_cc_card_installments > 12) {
					wc_add_notice(  __('Quantidade de parcela do cartão '.$two_cc_card_number.' inválida!', 'woocommece'), 'error' );
					return false;
				}	
				if (empty($two_cc_card_owner)) {
					wc_add_notice(  __('Nome do proprietário do cartão '.$two_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
				if (!$two_cc_card_month || $two_cc_card_month < 1 || $two_cc_card_month > 12) {
					wc_add_notice(  __('Mês de validade do cartão '.$two_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
				if (!$two_cc_card_year || $two_cc_card_year < date('Y')) {
					wc_add_notice(  __('Ano de validade do cartão '.$two_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
				if (!$two_cc_card_cid) {
					wc_add_notice(  __('Código do cartão '.$two_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}	
				if (!$this->validTaxvat($taxvat)) {
					wc_add_notice(  __('CPF do proprietário do cartão '.$two_cc_card_number.' inválido!', 'woocommece'), 'error' );
					return false;
				}
			}
		} elseif ($aqpago_type_payment == 'ticket_multiple') { // valid multi ticket
			if (isset($_POST['aqpago_updatemulti']) && sanitize_text_field($_POST['aqpago_updatemulti']) == 'true') {
				$aqpago_ticket_value = sanitize_text_field($_POST['aqpago_ticket_value']);
				if (!is_numeric($aqpago_ticket_value)) {
					wc_add_notice(  __('Valor a pagar inválido!', 'woocommece'), 'error' );
					return false;
				}
			}
		} elseif ($aqpago_type_payment == 'ticket') {
			// is valid
		} else {
			wc_add_notice(  __('Tipo de pagamento inválido!', 'woocommece'), 'error' );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 * @return array
	 */
	public function process_payment($order_id) {
		global $woocommerce;
		$orderWoocommerce = new WC_Order( $order_id );
		
		require_once( plugin_dir_path(__DIR__) . 'sdk/Includes.php' );
		
		$customer_id 	= $orderWoocommerce->get_customer_id();
		$cardsSave 		= get_post_meta(get_current_user_id(), '_cards_saves', true);
		$cardsSave 		= json_decode($cardsSave, true);
		
		if (!is_array($cardsSave)) $cardsSave = array();
		
		if ($this->debug == 'yes') $this->log->info( '_POST: ' . strip_tags(json_encode($_POST, JSON_PRETTY_PRINT)), array( 'source' => 'aqpago-pagamentos' ) );
		
		$seller_doc 	= preg_replace('/[^0-9]/', '', $this->document);
		$seller_token 	= $this->token;
		$sellerAqpago   = new Aqbank\Apiv2\SellerAqpago($seller_doc, $seller_token, 'modulo woocommerce');
		
		if ($this->environment == 'production') {
			// Ambiente de produção
			$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::production();
		} else {
			// Ambiente de homologação
			$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::sandbox();
		}
		
		$attempts  = get_post_meta($order_id, '_aqpago_attempts', true);
		$process   = get_post_meta($order_id, '_aqpago_response', true);
		$typeOrder = '';
		
		$aqpago_type_payment = sanitize_text_field($_POST['aqpago_type_payment']);
		
		if ($aqpago_type_payment == 'credit') {
			$typeOrder = 'credit';
		} elseif ($aqpago_type_payment == 'credit_multiple') {
			$typeOrder = 'multi_credit';
		} elseif ($aqpago_type_payment == 'ticket_multiple') {
			$typeOrder = 'multi_ticket';
		} elseif ($aqpago_type_payment == 'ticket') {
			$typeOrder = 'ticket';
		}
		
		$_card_one = sanitize_text_field($_POST['aqpago_card_one']);
		$_card_two = sanitize_text_field($_POST['aqpago_card_two']);
		
		if ($this->debug == 'yes')  $this->log->info( 'Process: ' . $process, array( 'source' => 'aqpago-pagamentos' ) );	
		
		$installMap = [];
		
		for ($p=1;$p<=12;$p++) {
			$installMap[$p]['tax'] = 0;
		}
		if ($this->installment_interest_free) {
			$this->installment_up_to = ($this->installment_up_to == 0) ? 1 : $this->installment_up_to;
			for ($p=$this->installment_up_to;$p<=12;$p++) {
				$_var = 'tax_' . $p;
				$_tax = $this->$_var;
				$installMap[$p]['tax'] = $_tax;
			}			
		}
		
		// tratar erro tiket multiple segunda
		//$process = false;
		
		/** Update Order **/
		if ($process) {
			if ($this->debug == 'yes') $this->log->info( 'Atualizar Pagamento: ' . $process, array( 'source' => 'aqpago-pagamentos' ) );	
			
			$process = json_decode($process, true);
			
			if (!isset($process['id']) || empty($process['id'])) {
				$orderWoocommerce->update_status('cancelled');
				$orderWoocommerce->add_order_note( __('Falha ao realizar pagamento.', 'woothemes') );
				
				// Remove cart
				$woocommerce->cart->empty_cart();
				
				update_post_meta($order_id, '_aqpago_closed', 'true');
				
				// Return thankyou redirect
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $orderWoocommerce )
				);				
			}
			
			/** process fail **/
			$card_one_erro = get_post_meta($order_id, '_card_one_erro', true);
			$card_two_erro = get_post_meta($order_id, '_card_two_erro', true);
			
			/** process sucess **/
			$card_one_success = get_post_meta($order_id, '_card_one_success', true);
			$card_two_success = get_post_meta($order_id, '_card_two_success', true);
			
			// Aqbank\Apiv2\Aqpago\UpdateOrder
			$aqpagoOrder = new Aqbank\Apiv2\Aqpago\UpdateOrder($process['id']);
			
			// credit, multi_credit, ticket, multi_ticket
			if ($aqpago_type_payment == 'credit') {
				/** process card one **/
				
				if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first']) == 'true') {
					$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
					
					
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas do cartão inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_cid) {
						wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_id) {
						wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
						return;
					}
					
					$aqpagoOrder->getOrder()
						->setType($typeOrder)	
						->creditCard(number_format($process['amount'], 2, '.', ''), $one_card_installments)
						->setSecurityCode($one_card_cid )
						->setCardId($one_card_id);
				} else {
					$one_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
					$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
					$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
					$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$taxvat = preg_replace('/[^0-9]/is', '',$_POST['aqpago_one_cc_card_taxvat']);
					
					if (!$one_card_number) {
						wc_add_notice( __('Número do cartão inválido! ' . $one_card_number, 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_installments ||  $one_card_installments < 1 || $one_card_installments > 12) {
						wc_add_notice( __('Quantidade de parcelas inválida para cartão ' . $one_card_number, 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_owner) {
						wc_add_notice( __('Nome do proprietário do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_month || $one_card_month < 1 || $one_card_month > 12) {
						wc_add_notice( __('Mês de validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_year || $one_card_year < date("Y")) {
						wc_add_notice( __('Ano de validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$one_card_cid) {
						wc_add_notice( __('Código do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$this->validTaxvat($taxvat)) {
						wc_add_notice( __('CPF inválido para cartão '.$one_card_number, 'woocommerce'), 'error' );
						return;
					}
					
					$_totalPay = $process['amount'];
					
					$aqpagoOrder->getOrder()
						->setType($typeOrder)					
						->creditCard(number_format($_totalPay, 2, '.', ''), $one_card_installments)
						->setCardNumber($one_card_number)
						->setHolderName($one_card_owner)
						->setExpirationMonth($one_card_month)
						->setExpirationYear($one_card_year)
						->setSecurityCode($one_card_cid)
						->setCpf($taxvat);
				}
				
			} elseif ($aqpago_type_payment == 'credit_multiple') {
				$card_pay  = get_post_meta($order_id, '_card_pay', true);
				$price_pay = get_post_meta($order_id, '_price_pay', true);
				
				// Multiple payment with error that the customer updated the page!
				if (isset($_POST['aqpago_updatemulti']) && sanitize_text_field($_POST['aqpago_updatemulti']) == 'true') {
					$two_card_value = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
					$two_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_number']);
					$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
					$two_card_owner = sanitize_text_field($_POST['aqpago_two_cc_card_owner']);
					$two_card_month = intval($_POST['aqpago_two_cc_card_month']);
					$two_card_year = intval($_POST['aqpago_two_cc_card_year']);
					$two_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
					$taxvat = preg_replace('/[^0-9]/is', '',$_POST['aqpago_two_cc_card_taxvat']);
					
					if (!is_numeric($two_card_value)) {
						wc_add_notice( __('Valor inválido para o cartão ' . $two_card_number, 'woocommerce'), 'error' );
						return;
					}
					if (!$two_card_installments || $two_card_installments < 1 || $two_card_installments > 12) {
						wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
						$this->log->info( 'Quantidade de parcelas inválido! 1 ', array( 'source' => 'aqpago-pagamentos' ) );
						return;
					}
					if (!$two_card_number) {
						wc_add_notice( __('Número do segundo cartão inválido!', 'woocommerce'), 'error' );
						return;
					}					
					if (!$two_card_owner) {
						wc_add_notice( __('Nome do proprietário do cartão ' . $two_card_number . ' inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$two_card_month || $two_card_month < 1 || $two_card_month > 12) {
						wc_add_notice( __('Mês de validade do cartão ' . $two_card_number . ' inválido!', 'woocommerce'), 'error' );
						return;
					}	
					if (!$two_card_year || $two_card_year < date("Y")) {
						wc_add_notice( __('Ano de validade do cartão ' . $two_card_number . ' inválido!', 'woocommerce'), 'error' );
						return;
					}
					if (!$two_card_cid) {
						wc_add_notice( __('Código inválido para o cartão ' . $two_card_number, 'woocommerce'), 'error' );
						return;
					}
					if (!$this->validTaxvat($taxvat)) {
						wc_add_notice( __('CPF inválido para cartão ' . $two_card_number, 'woocommerce'), 'error' );
						return;
					}
					
					$aqpagoOrder->getOrder()
						->setType( $typeOrder )
						->creditCard( number_format($two_card_value, 2, '.', ''), $two_card_installments)
						->setCardNumber( $two_card_number )
						->setHolderName( $two_card_owner )
						->setExpirationMonth( $two_card_month )
						->setExpirationYear( $two_card_year )
						->setSecurityCode( $two_card_cid )
						->setCpf( $taxvat );	
				} else {
					/** Paymento total error **/
					if ($card_one_erro == 'true' || $card_two_erro == 'true') {
						
						if ($card_one_erro == 'true' && $card_one_success != 'true') {
							/** Paymento parcial error, card one error **/
							
							/** process card one **/
							if (isset($_POST['aqpago_one_cc_card_erro']) && sanitize_text_field($_POST['aqpago_one_cc_card_erro']) == 'true') {
								if($card_pay && sanitize_text_field($_POST['aqpago_one_cc_card_erro']) == 'true' && sanitize_text_field($_POST['aqpago_two_cc_card_erro']) == 'false') {
									$totalPay = ($process['amount'] - $price_pay);
								} else {
									if (!is_numeric(sanitize_text_field($_POST['aqpago_one_cc_card_value']))) {
										wc_add_notice( __('Valor incorreto para o pagamento', 'woocommerce'), 'error' );
										return;
									}
									
									$totalPay = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
								}
								
								if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first']) == 'true') {
									$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
									$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
									$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
									
									if (!$one_card_installments) {
										wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
										$this->log->info( 'Quantidade de parcelas inválido! 2 ', array( 'source' => 'aqpago-pagamentos' ) );
										return;		
									}									
									if (!$one_card_cid) {
										wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
										return;		
									}									
									if (!$one_card_id) {
										wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
										return;		
									}
									
									$aqpagoOrder->getOrder()
										->setType( $typeOrder )	
										->creditCard(number_format($totalPay, 2, '.', ''), $one_card_installments)
										->setSecurityCode( $one_card_cid )
										->setCardId( $one_card_id );
								} else {
									$one_card_number =  preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
									$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
									$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
									$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
									$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
									$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
									$taxvat = preg_replace('/[^0-9]/is', '',$_POST['aqpago_one_cc_card_taxvat']);
									
									if (!$one_card_number) {
										wc_add_notice( __('Número do cartão inválido!', 'woocommerce'), 'error' );
										return;		
									}										
									if (!$one_card_installments) {
										wc_add_notice( __('Quantidade de parcelas inválido para o cartão '.$one_card_number, 'woocommerce'), 'error' );
										return;		
									}	
									if (!$one_card_owner) {
										wc_add_notice( __('Nome proprietário do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
										return;									
									}	
									if (!$one_card_month || $one_card_month < 1 || $one_card_month > 12) {
										wc_add_notice( __('Mês da validade do cartão inválido!', 'woocommerce'), 'error' );
										return;		
									}
									if (!$one_card_year || $one_card_year < date("Y")) {
										wc_add_notice( __('Mês da validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
										return;		
									}
									if (!$one_card_cid) {
										wc_add_notice( __('Código do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
										return;	
									}
									if (!$this->validTaxvat($taxvat)) {
										wc_add_notice( __('CPF inválido para cartão ' . $one_card_number, 'woocommerce'), 'error' );
										return;
									}
									
									$aqpagoOrder->getOrder()
										->setType($typeOrder)					
										->creditCard(number_format($totalPay, 2, '.', ''), $one_card_installments)
										->setCardNumber($one_card_number)
										->setHolderName($one_card_owner)
										->setExpirationMonth($one_card_month)
										->setExpirationYear($one_card_year)
										->setSecurityCode($one_card_cid)
										->setCpf($taxvat);
								}
							}
						}
						
						if ($card_two_erro == 'true' && $card_two_success != 'true') {
							/** Paymento parcial error, card two error **/
							/** process card two **/
							if (isset($_POST['aqpago_two_cc_card_erro']) && sanitize_text_field($_POST['aqpago_two_cc_card_erro']) == 'true') {
								if ($card_pay && sanitize_text_field($_POST['aqpago_two_cc_card_erro']) == 'true' && sanitize_text_field($_POST['aqpago_one_cc_card_erro']) == 'false') {
									$totalPay = ($process['amount'] - $price_pay);
								} else {
									if (!is_numeric(sanitize_text_field($_POST['aqpago_two_cc_card_value']))) {
										wc_add_notice( __('Valor inválido para pagamento!', 'woocommerce'), 'error' );
										return;	
									}
									
									$totalPay = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
								}				
								
								if (isset($_POST['aqpago_saved_second']) && sanitize_text_field($_POST['aqpago_saved_second']) == 'true') {
									
									$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
									$two_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
									$two_card_id = sanitize_text_field($_POST['aqpago_two_cc_card_id']);
									
									if (!$two_card_installments) {
										wc_add_notice( __('Quantidade de parcela inválida!', 'woocommerce'), 'error' );
										return;	
									}									
									if (!$two_card_cid || strlen($two_card_cid) < 3) {
										wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
										return;	
									}
									if (!$two_card_id) {
										wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
										return;	
									}
									
									$aqpagoOrder->getOrder()
										->setType( $typeOrder )
										->creditCard(number_format($totalPay, 2, '.', ''), $two_card_installments)
										->setSecurityCode( $two_card_cid )
										->setCardId( $two_card_id );
								} else {
									$two_card_number =  preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_number']);
									$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
									$two_card_owner = sanitize_text_field($_POST['aqpago_two_cc_card_owner']);
									$two_card_month = intval($_POST['aqpago_two_cc_card_month']);
									$two_card_year = intval($_POST['aqpago_two_cc_card_year']);
									$two_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
									$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_taxvat']);
									
									if (!$two_card_number) {
										wc_add_notice( __('Número do cartão inválido!', 'woocommerce'), 'error' );
										return;	
									}	
									if (!$two_card_installments) {
										wc_add_notice( __('Quantidade de parcela inválida para o cartão ' . $two_card_number, 'woocommerce'), 'error' );
										return;	
									}
									if (!$two_card_owner) {
										wc_add_notice( __('Nome proprietário do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
										return;	
									}
									if (!$two_card_month || $two_card_month < 1 || $two_card_month > 12) {
										wc_add_notice( __('Mês da validade do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
										return;	
									}
									if (!$two_card_year || $two_card_year < date("Y")) {
										wc_add_notice( __('Ano da validade do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
										return;	
									}
									if (!$two_card_cid || strlen($two_card_cid) < 3) {
										wc_add_notice( __('Código do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
										return;	
									}	
									if (!$this->validTaxvat($taxvat)) {
										wc_add_notice( __('CPF inválido para cartão ' . $two_card_number, 'woocommerce'), 'error' );
										return;
									}
									
									$aqpagoOrder->getOrder()
										->setType($typeOrder)
										->creditCard(number_format($totalPay, 2, '.', ''), $two_card_installments)
										->setCardNumber($two_card_number)
										->setHolderName($two_card_owner)
										->setExpirationMonth($two_card_month)
										->setExpirationYear($two_card_year)
										->setSecurityCode($two_card_cid)
										->setCpf($taxvat);
								}
							}
						}
					} else {
						/** Process default two cards **/
						
						/** process card one **/
						if (isset($_POST['aqpago_one_cc_card_erro']) && sanitize_text_field($_POST['aqpago_one_cc_card_erro']) == 'true' && $card_one_success != 'true') {
							if ($card_pay && sanitize_text_field($_POST['aqpago_one_cc_card_erro']) == 'true' && sanitize_text_field($_POST['aqpago_two_cc_card_erro']) == 'false') {
								$totalPay = ($process['amount'] - $price_pay);
							} else {
								if (!is_numeric(sanitize_text_field($_POST['aqpago_one_cc_card_value']))) {
									wc_add_notice(__('Valor inválido para pagamento!', 'woocommerce'), 'error');
									return;	
								}
								
								$totalPay = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
							}
							
							if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first']) == 'true') {
								$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
								$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
								$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
								
								if (!$one_card_installments) {
									wc_add_notice(__('Quantidade de parcela inválida!', 'woocommerce'), 'error');
									return;	
								}
								if (!$one_card_cid) {
									wc_add_notice(__('Código do cartão inválido!', 'woocommerce'), 'error');
									return;	
								}								
								if (!$one_card_id) {
									wc_add_notice(__('ID do cartão inválido!', 'woocommerce'), 'error');
									return;	
								}	
								
								$aqpagoOrder->getOrder()
									->setType( $typeOrder )	
									->creditCard(number_format($totalPay, 2, '.', ''), $one_card_installments)
									->setSecurityCode( $one_card_cid )
									->setCardId( $one_card_id );
							} else {
								$one_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
								$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
								$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
								$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
								$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
								$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
								$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_taxvat']);
								
								if (!$one_card_number) {
									wc_add_notice( __('Número do cartão inválido! ', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_installments) {
									wc_add_notice( __('Quantidade de parcela inválida para o cartão '.$one_card_number, 'woocommerce'), 'error' );
									return;	
								}								
								if (!$one_card_owner) {
									wc_add_notice( __('Nome proprietário do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_month || $one_card_month < 1 || $one_card_month > 12) {
									wc_add_notice( __('Mês de validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}								
								if (!$one_card_year || $one_card_year < date("Y")) {
									wc_add_notice( __('Ano de validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_cid) {
									wc_add_notice( __('Código do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$this->validTaxvat($taxvat)) {
									wc_add_notice( __('CPF inválido para cartão ' . $one_card_number, 'woocommerce'), 'error' );
									return;
								}
								
								$aqpagoOrder->getOrder()
									->setType( $typeOrder )					
									->creditCard( number_format($totalPay, 2, '.', ''), $one_card_installments)
									->setCardNumber( $one_card_number )
									->setHolderName( $one_card_owner )
									->setExpirationMonth( $one_card_month )
									->setExpirationYear( $one_card_year )
									->setSecurityCode( $one_card_cid )
									->setCpf( $taxvat );
							}
						}
						
						/** process card two **/
						if (isset($_POST['aqpago_two_cc_card_erro']) && sanitize_text_field($_POST['aqpago_two_cc_card_erro']) == 'true' && $card_two_success != 'true') {
							if ($card_pay && sanitize_text_field($_POST['aqpago_two_cc_card_erro']) == 'true' && sanitize_text_field($_POST['aqpago_one_cc_card_erro']) == 'false') {
								$totalPay = ($process['amount'] - $price_pay);
							} else {
								if (!is_numeric(sanitize_text_field($_POST['aqpago_two_cc_card_value']))) {
									wc_add_notice( __('Valor inválido para pagamento!', 'woocommerce'), 'error' );
									return;	
								}
								
								$totalPay = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
							}					
							
							if (isset($_POST['aqpago_saved_second']) && sanitize_text_field($_POST['aqpago_saved_second']) == 'true') {
								$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
								$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
								$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
								
								if (!$one_card_installments) {
									wc_add_notice( __('Quantidade de parcela inválida!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_cid) {
									wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_id) {
									wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
									return;	
								}	
								
								$aqpagoOrder->getOrder()
									->setType( $typeOrder )
									->creditCard(number_format($totalPay, 2, '.', ''), $one_card_installments)
									->setSecurityCode( $one_card_cid )
									->setCardId( $one_card_id );
							} else {
								$two_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_number']);
								$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
								$two_card_owner = sanitize_text_field($_POST['aqpago_two_cc_card_owner']);
								$two_card_month = intval($_POST['aqpago_two_cc_card_month']);
								$two_card_year = intval($_POST['aqpago_two_cc_card_year']);
								$two_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
								$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_taxvat']);
								
								if (!$two_card_number) {
									wc_add_notice( __('Quantidade de parcela inválida!', 'woocommerce'), 'error' );
									return;	
								}	
								if (!$two_card_installments) {
									wc_add_notice( __('Quantidade de parcela inválida para o cartão ' . $two_card_number, 'woocommerce'), 'error' );
									return;	
								}
								if (!$two_card_owner) {
									wc_add_notice( __('Nome proprietário do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$two_card_month || $two_card_month < 1 || $two_card_month > 12) {
									wc_add_notice( __('Mês da validade do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$two_card_year || $two_card_year < date("Y")) {
									wc_add_notice( __('Ano da validade do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$two_card_cid) {
									wc_add_notice( __('Código do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$this->validTaxvat($taxvat)) {
									wc_add_notice( __('CPF inválido para cartão ' . $two_card_number, 'woocommerce'), 'error' );
									return;
								}
								
								$aqpagoOrder->getOrder()
									->setType( $typeOrder )
									->creditCard( number_format($totalPay, 2, '.', ''), $two_card_installments)
									->setCardNumber( $two_card_number )
									->setHolderName( $two_card_owner )
									->setExpirationMonth( $two_card_month )
									->setExpirationYear( $two_card_year )
									->setSecurityCode( $two_card_cid )
									->setCpf( $taxvat );
							}
						}
					}
				}
			} elseif ($aqpago_type_payment == 'ticket_multiple') {
				$card_pay  = get_post_meta($order_id, '_card_pay', true);
				$price_pay = get_post_meta($order_id, '_price_pay', true);
				
				// Multiple payment with error that the customer updated the page!
				if (isset($_POST['aqpago_updatemulti']) && sanitize_text_field($_POST['aqpago_updatemulti']) == 'true') {						
					
					$aqpago_ticket_value = sanitize_text_field($_POST['aqpago_ticket_value']);
					
					if (!is_numeric($aqpago_ticket_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;
					}
					
					
					// payment by ticket
					$aqpagoOrder->getOrder()
							->setType( $typeOrder )	
							->ticket( number_format($aqpago_ticket_value, 2, '.', ''))
						->setBodyInstructions( $this->body_instructions );
				} else {
					/** Paymento card success **/
					if ($card_one_success == 'true' || $card_two_success == 'true') {
						/** create ticket **/
						if ($price_pay) {
							$totalTicket = ($process['amount'] - $price_pay);
						} else {
							if (!is_numeric(sanitize_text_field($_POST['aqpago_ticket_value']))) {
								wc_add_notice( __('Valor inválido para pagamento!', 'woocommerce'), 'error' );
								return;	
							}
							
							$totalTicket = sanitize_text_field($_POST['aqpago_ticket_value']);
						}
						
						// payment by ticket
						$aqpagoOrder->getOrder()
								->setType( $typeOrder )	
								->ticket( number_format($totalTicket, 2, '.', ''))
							->setBodyInstructions( $this->body_instructions );
					} else {
						/** Process default **/
						
						if (!$card_pay && $card_one_success != 'true' && $card_two_success != 'true') {
							if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first']) == 'true') {
								
								$totalTicket = sanitize_text_field($_POST['aqpago_ticket_value']);
								
								$one_card_value = $process['amount'] - $totalTicket;
								$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
								$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
								$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
								
								if (!is_numeric($one_card_value)) {
									wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_installments) {
									wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
									$this->log->info( 'Quantidade de parcelas inválido! 3 ', array( 'source' => 'aqpago-pagamentos' ) );
									return;	
								}							
								if (!$one_card_cid) {
									wc_add_notice( __('Código do cartão é inválido!', 'woocommerce'), 'error' );
									return;
								}
								if (!$one_card_id) {
									wc_add_notice( __('ID do cartão é inválido!', 'woocommerce'), 'error' );
									return;
								}
								
								$aqpagoOrder->getOrder()
									->setType( $typeOrder )		
									->creditCard(number_format($one_card_value, 2, '.', ''), $one_card_installments)
									->setSecurityCode( $one_card_cid )
									->setCardId( $one_card_id );
							} else {
								$totalTicket = sanitize_text_field($_POST['aqpago_ticket_value']);
								
								$one_card_value = $process['amount'] - $totalTicket;
								$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
								$one_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
								$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
								$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
								$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
								$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
								$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_taxvat']);
								
								if (!is_numeric($one_card_value)) {
									wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_installments) {
									wc_add_notice( __('Quantidade de parcelas inválido para o cartão ' . $one_card_number, 'woocommerce'), 'error' );
									return;	
								}									
								if (!$one_card_number) {
									wc_add_notice( __('Número do cartão inválido!', 'woocommerce'), 'error' );
									return;	
								}					
								if (!$one_card_owner) {
									wc_add_notice( __('Nome proprietário do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;
								}			
								if (!$one_card_month || $one_card_month < 1 || $one_card_month > 12) {
									wc_add_notice( __('Mês de validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!$one_card_year || $one_card_year < date("Y")) {
									wc_add_notice( __('Ano de validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;	
								}
								if (!is_numeric($one_card_cid)) {
									wc_add_notice( __('Código do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
									return;
								}
								if (!$this->validTaxvat($taxvat)) {
									wc_add_notice( __('CPF inválido para cartão ' . $one_card_number, 'woocommerce'), 'error' );
									return;
								}
								
								$aqpagoOrder->getOrder()
									->setType( $typeOrder )					
									->creditCard( number_format($one_card_value, 2, '.', ''), $one_card_installments)
									->setCardNumber( $one_card_number )
									->setHolderName( $one_card_owner )
									->setExpirationMonth( $one_card_month )
									->setExpirationYear( $one_card_year )
									->setSecurityCode( $one_card_cid )
									->setCpf( $taxvat );
							}
						}
						
						if ($price_pay) {
							$totalTicket = ($process['amount'] - $price_pay);
						} else {
							if (!is_numeric(sanitize_text_field($_POST['aqpago_ticket_value']))) {
								wc_add_notice( __('Valor inválido para pagamento!', 'woocommerce'), 'error' );
								return;	
							}
							
							$totalTicket = sanitize_text_field($_POST['aqpago_ticket_value']);
						}
						
						// payment by ticket
						$aqpagoOrder->getOrder()
								->setType( $typeOrder )	
								->ticket( number_format($totalTicket, 2, '.', ''))
							->setBodyInstructions( $this->body_instructions );
					}
				}
				
			} elseif ($aqpago_type_payment == 'ticket') {
				// payment by ticket
				$aqpagoOrder->getOrder()
						->setType( $typeOrder )	
						->ticket( number_format($orderWoocommerce->get_total(), 2, '.', ''))
					->setBodyInstructions( $this->body_instructions );
			}
			
			if ($this->debug == 'yes') $this->log->info( 'Request: ' . json_encode(array_filter($aqpagoOrder->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );	
			
			try {
				$_erro = false;
				$transaction = (new \Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->updateOrder($aqpagoOrder);
				
				if ($this->debug == 'yes') $this->log->info( 'Response: ' . json_encode(array_filter($transaction->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );	
				
			} catch (\Exception $e) {
				$Message 	= $e->getMessage();
				$convert 	= json_decode($Message, true);   
				$_erro 		= true;
				
				if ($this->debug == 'yes')  $this->log->info( 'Message: ' . $Message, array( 'source' => 'aqpago-pagamentos' ) );	
			}
			
			if (!$_erro && !$transaction->getStatus()) {
				$orderWoocommerce->update_status('cancelled');
				$orderWoocommerce->add_order_note( __('Falha ao realizar pagamento.', 'woothemes') );
				$orderWoocommerce->add_order_note( __( $this->trans_erros( $transaction->getMessage() ), 'woothemes') );
				
				$orderAq = new Aqbank\Apiv2\Aqpago\Order(sanitize_text_field($_POST['aqpago_session']));
				$orderAq->setOrderId($process['id']);
				
				try {
					$response = (new Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->cancelOrder($orderAq);
					update_post_meta($order_id, '_aqpago_response', json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT));
					update_post_meta($order_id, '_aqpago_pay_value', $totalPay);
					
					if ($this->debug == 'yes') $this->log->info( 'Cancel: ' . json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );
					
				} catch (Exception $e) {
					$errorMessage = $e->getMessage();
				}
				
				// Remove cart
				$woocommerce->cart->empty_cart();
				
				update_post_meta($order_id, '_aqpago_closed', 'true');
				
				// Return thankyou redirect
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $orderWoocommerce )
				);
			}	
		} else {
			/** Create Order **/
			if ($this->debug == 'yes')  $this->log->info( 'Criar Pagamento', array( 'source' => 'aqpago-pagamentos' ) );	
			if ($this->debug == 'yes')  $this->log->info( 'installMap: ' . json_encode($installMap), array( 'source' => 'aqpago-pagamentos' ) );	
			
			$document_key 	= (get_option('woocommerce_aqpago_document')) ? get_option('woocommerce_aqpago_document') : 'billing_document';
			$phone_key 		= (get_option('woocommerce_aqpago_phone')) ? get_option('woocommerce_aqpago_phone') : 'billing_phone';
			$street_key 	= (get_option('woocommerce_aqpago_address_street')) ? get_option('woocommerce_aqpago_address_street') : 'billing_address_1';
			$number_key 	= (get_option('woocommerce_aqpago_address_number')) ? get_option('woocommerce_aqpago_address_number') : 'billing_address_2';
			$complement_key = (get_option('woocommerce_aqpago_address_complement')) ? get_option('woocommerce_aqpago_address_complement') : 'billing_address_3';
			$district_key 	= (get_option('woocommerce_aqpago_address_district')) ? get_option('woocommerce_aqpago_address_district') : 'billing_address_4';
			$city_key 		= (get_option('woocommerce_aqpago_address_city')) ? get_option('woocommerce_aqpago_address_city') : 'billing_city';
			$state_key 		= (get_option('woocommerce_aqpago_address_state')) ? get_option('woocommerce_aqpago_address_state') : 'billing_state';
			
			$first_name 	= sanitize_text_field( $_POST['billing_first_name'] );
			$last_name 		= sanitize_text_field( $_POST['billing_last_name'] );
			$billing_email 	= sanitize_email( $_POST['billing_email'] );
			$document 		= sanitize_text_field( preg_replace('/[^0-9]/', '', $_POST[ $document_key ]));
			$billing_phone	= sanitize_text_field( preg_replace('/[^0-9]/', '', $_POST[ $phone_key ]));
			$ddd 			= substr($billing_phone, 0, 2);
			$phone 			= substr($billing_phone, 2, strlen($billing_phone));
			
			// adress by shipping active
			if (sanitize_text_field($_POST['ship_to_different_address'])) {
				$street_key 	= str_replace('billing_','shipping_', $street_key);
				$number_key 	= str_replace('billing_','shipping_', $number_key);
				$complement_key = str_replace('billing_','shipping_', $complement_key);
				$district_key 	= str_replace('billing_','shipping_', $district_key);
				$city_key 		= str_replace('billing_','shipping_', $city_key);
				$state_key 		= str_replace('billing_','shipping_', $state_key);
				
				$postcode 			= sanitize_text_field( preg_replace('/[^0-9]/', '', $_POST['shipping_postcode']) );
				$address_street		= sanitize_text_field( $_POST[ $street_key ] );
				$address_number		= sanitize_text_field( $_POST[ $number_key ] );
				$address_comp		= sanitize_text_field( $_POST[ $complement_key ] );
				$address_district	= sanitize_text_field( $_POST[ $district_key ] );
				$address_city		= sanitize_text_field( $_POST[ $city_key ] );
				$address_state		= sanitize_text_field( $_POST[ $state_key ] );
			} else {
				// adress by billing
				$postcode 			= sanitize_text_field( preg_replace('/[^0-9]/', '', $_POST['billing_postcode']) );
				$address_street		= sanitize_text_field( $_POST[ $street_key ] );
				$address_number		= sanitize_text_field( $_POST[ $number_key ] );
				$address_comp		= sanitize_text_field( $_POST[ $complement_key ] );
				$address_district	= sanitize_text_field( $_POST[ $district_key ] );
				$address_city		= sanitize_text_field( $_POST[ $city_key ] );
				$address_state		= sanitize_text_field( $_POST[ $state_key ] );
			}
			
			if (!$first_name) {
				wc_add_notice( __('Nome é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$billing_email) {
				wc_add_notice( __('Email é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$this->validTaxvat($document)) {
				wc_add_notice( __('Documento não é valido!', 'woocommerce'), 'error' );
				return;	
			}			
			if (!$postcode) {
				wc_add_notice( __('Cep é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$address_street) {
				wc_add_notice( __('Endereço é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$address_number) {
				wc_add_notice( __('Número do endereço é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$address_district) {
				wc_add_notice( __('Bairro é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$address_city) {
				wc_add_notice( __('Cidade é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			if (!$address_state) {
				wc_add_notice( __('Estado é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}			
			if (!$ddd || !$phone) {
				wc_add_notice( __('Telefone é obrigatório!', 'woocommerce'), 'error' );
				return;	
			}
			
			
			//$installMap[$p]['tax']
			$_totalPay = $orderWoocommerce->get_total();
			$_totalPayShipping = 0;
			$_totalPayItens = 0;
			
			if (is_array($orderWoocommerce->get_items( 'shipping' ))) {
				foreach ($orderWoocommerce->get_items( 'shipping' ) as $item_id => $item) {
					$_totalPayShipping += $item->get_total();
				}
			}
			foreach ($orderWoocommerce->get_items() as $item_id => $item) {
				$_totalPayItens += $item->get_total();
			}
			
			$_itensArray = [];
			if (sanitize_text_field($_POST['aqpago_type_payment']) == 'credit') {
				$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
				$_tax = ($installMap[$one_card_installments]['tax'] / 100);
				$_calJurosTotalPay = 0;
				
				if($_tax > 0) {
					$_totalPay = $_totalPay / (1 - $_tax);
					$_calJurosTotalPay = $_totalPay - $orderWoocommerce->get_total();
					
					if($_totalPayShipping > 0) {
						//$_calJurosTotalPayShipping = (($_totalPayShipping / (100 * $_tax)) * 100);
						$_calJurosTotalPayShipping = $_totalPayShipping / (1 - $_tax);
						$_calJurosTotalPayShipping = $_calJurosTotalPayShipping - $_totalPayShipping;
						$_totalPayShipping += $_calJurosTotalPayShipping;
					}
					
					if ($this->debug == 'yes')  $this->log->info( '_calJurosTotalPay: ' . $_calJurosTotalPay, array( 'source' => 'aqpago-pagamentos' ) );
					if ($this->debug == 'yes')  $this->log->info( '_totalPayShipping: ' . $_totalPayShipping, array( 'source' => 'aqpago-pagamentos' ) );
					if ($this->debug == 'yes')  $this->log->info( '_calJurosTotalPayShipping: ' . $_calJurosTotalPayShipping, array( 'source' => 'aqpago-pagamentos' ) );
				}
				
				foreach ($orderWoocommerce->get_items() as $item_id => $item) {
					$product 	= $item->get_product();
					$unit_price = $item->get_total();
					$tax_unit 	= 0;
					
					if($_tax > 0) {
						$tax_unit = $unit_price / (1 - $_tax);
						$tax_unit = $tax_unit - $unit_price;
					}
					
					$tax_unit = $tax_unit + $unit_price;
					
					$_itensArray[] = [
						'name' => $item->get_name(),
						'qty' => $item->get_quantity(),
						'price' => $tax_unit,
					];
				}
				
			} elseif (sanitize_text_field($_POST['aqpago_type_payment'])  == 'credit_multiple') {
				$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
				$one_card_value = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
				
				$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
				$two_card_value  = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
				
				$_tax1 = ($installMap[$one_card_installments]['tax'] / 100);
				$_tax2 = ($installMap[$two_card_installments]['tax'] / 100);
				
				if ($this->debug == 'yes')  $this->log->info( '_tax1: ' . $_tax1, array( 'source' => 'aqpago-pagamentos' ) );
				if ($this->debug == 'yes')  $this->log->info( '_tax2: ' . $_tax2, array( 'source' => 'aqpago-pagamentos' ) );
				
				$_calJurosTotalPayOne = 0;
				$_calJurosTotalPayTwo = 0;
				
				if($_tax1 > 0) {
					$_totalPayOne = $one_card_value / (1 - $_tax1);
					$_calJurosTotalPayOne = $_totalPayOne - $one_card_value;
				} else {
					$_totalPayOne = $one_card_value;
				}
				
				if($_tax2 > 0) {
					$_totalPayTwo = $two_card_value / (1 - $_tax2);
					$_calJurosTotalPayTwo = $_totalPayTwo - $two_card_value;
				} else {
					$_totalPayTwo = $two_card_value;
				}
				
				$_totalPay = number_format($_totalPayOne, 2, '.', '') + number_format($_totalPayTwo, 2, '.', '');
				
				if ($this->debug == 'yes')  $this->log->info( '_totalPayOne: ' . $_totalPayOne, array( 'source' => 'aqpago-pagamentos' ) );
				if ($this->debug == 'yes')  $this->log->info( '_totalPayTwo: ' . $_totalPayTwo, array( 'source' => 'aqpago-pagamentos' ) );
				
				foreach ($orderWoocommerce->get_items() as $item_id => $item) {
					$product 	= $item->get_product();
					$unit_price = $item->get_total();
					$tax_unit 	= 0;
					$tax_unit 	= $tax_unit + $unit_price;
					
					$_itensArray[] = [
						'name' => $item->get_name(),
						'qty' => $item->get_quantity(),
						'price' => $unit_price,
					];
				}
				

				$totalProduct = count($_itensArray);
				$totalFees = $_calJurosTotalPayOne + $_calJurosTotalPayTwo;
				$totalComp = $_totalPay - $_totalPayShipping;
				
				if($totalFees > 0) {
					$feesSplit = $totalFees / $totalProduct;
					
					foreach ($_itensArray as $idItem => $_item) {
						$this->log->info('price: ' . $_itensArray[$idItem]['price'], array( 'source' => 'aqpago-pagamentos' ) );
						$this->log->info('feesSplit: ' . $feesSplit, array( 'source' => 'aqpago-pagamentos' ) );
						
						$_itensArray[$idItem]['price'] = $_itensArray[$idItem]['price'] + $feesSplit;
						
						$totalComp = $totalComp - $_itensArray[$idItem]['price'];
					}
					
					if ($this->debug == 'yes') $this->log->info('totalComp: ' . $totalComp, array( 'source' => 'aqpago-pagamentos' ) );
					
					if($totalComp > 0) {
						foreach ($_itensArray as $idItem => $_item) {
							$_itensArray[$idItem]['price'] = $_itensArray[$idItem]['price'] + $totalComp;
							break;
						}
					}
					if($totalComp < 0) {
						foreach ($_itensArray as $idItem => $_item) {
							$_itensArray[$idItem]['price'] = $_itensArray[$idItem]['price'] - $totalComp;
							break;
						}
					}
				}
				
				if ($this->debug == 'yes') {
					$this->log->info( 'Total Pagamento: ' . $_totalPay, array( 'source' => 'aqpago-pagamentos' ) ); 
					$this->log->info( 'ItensArray: ' . json_encode($_itensArray), array( 'source' => 'aqpago-pagamentos' ) ); 
				}
				
			} elseif (sanitize_text_field($_POST['aqpago_type_payment'])  == 'ticket_multiple') {
				$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
				$one_card_value = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
				$aqpago_ticket_value = sanitize_text_field($_POST['aqpago_ticket_value']);
				
				$_tax1 = ($installMap[$one_card_installments]['tax'] / 100);
				$_calJurosTotalPayOne = 0;
				
				if($_tax1 > 0) {
					$_totalPayOne = $one_card_value / (1 - $_tax1);
					
					$_calJurosTotalPayOne = $_totalPayOne - $one_card_value;
				} else {
					$_totalPayOne = $one_card_value;
				}
				
				$_totalPay = number_format($_totalPayOne, 2, '.', '') + number_format($aqpago_ticket_value, 2, '.', '');
				
				foreach ($orderWoocommerce->get_items() as $item_id => $item) {
					$product 	= $item->get_product();
					$unit_price = round(($item->get_total() / $item->get_quantity()), 2);
					$tax_unit 	= 0;
					$tax_unit 	= $tax_unit + $unit_price;
					
					$_itensArray[] = [
						'name' => $item->get_name(),
						'qty' => $item->get_quantity(),
						'price' => $unit_price,
					];
				}
				
				$totalProduct = count($_itensArray);
				$totalFees = $_calJurosTotalPayOne;
				$totalComp = $_totalPay - $_totalPayShipping;				
				
				if($totalFees > 0) {
					$feesSplit = $totalFees / $totalProduct;
					
					foreach ($_itensArray as $idItem => $_item) {
						$this->log->info('price: ' . $_itensArray[$idItem]['price'], array( 'source' => 'aqpago-pagamentos' ) );
						$this->log->info('feesSplit: ' . $feesSplit, array( 'source' => 'aqpago-pagamentos' ) );
						
						$_itensArray[$idItem]['price'] = $_itensArray[$idItem]['price'] + $feesSplit;
						
						$totalComp = $totalComp - $_itensArray[$idItem]['price'];
					}
					
					if ($this->debug == 'yes') $this->log->info('totalComp: ' . $totalComp, array( 'source' => 'aqpago-pagamentos' ) );
					
					if($totalComp > 0) {
						foreach ($_itensArray as $idItem => $_item) {
							$_itensArray[$idItem]['price'] = $_itensArray[$idItem]['price'] + $totalComp;
							break;
						}
					}
					if($totalComp < 0) {
						foreach ($_itensArray as $idItem => $_item) {
							$_itensArray[$idItem]['price'] = $_itensArray[$idItem]['price'] - $totalComp;
							break;
						}
					}
				}
				
				if ($this->debug == 'yes') {
					$this->log->info( 'Total Pagamento: ' . $_totalPay, array( 'source' => 'aqpago-pagamentos' ) ); 
					$this->log->info( 'ItensArray: ' . json_encode($_itensArray), array( 'source' => 'aqpago-pagamentos' ) ); 
				}
				
			} elseif (sanitize_text_field($_POST['aqpago_type_payment'])  == 'ticket') {
				
				foreach ($orderWoocommerce->get_items() as $item_id => $item) {
					$product 	= $item->get_product();
					$unit_price = round(($item->get_total() / $item->get_quantity()), 2);
					// item com quantidade e total batendo
					if ($product->get_price() == $unit_price) {
						$_itensArray[] = [
							'name' => $item->get_name(),
							'qty' => $item->get_quantity(),
							'price' => $unit_price,
						];
					} else {
						// produto com desconto ou taxa utilizar quantidade 1 e enviar total
						$_itensArray[] = [
							'name' => $item->get_name(),
							'qty' => 1,
							'price' => $item->get_total(),
						];
					}
				}
			}
			
			if ($this->debug == 'yes')  $this->log->info( 'Criar request', array( 'source' => 'aqpago-pagamentos' ) );
			
			// Aqbank\Apiv2\Aqpago\Order
			$aqpagoOrder = new Aqbank\Apiv2\Aqpago\Order(sanitize_text_field($_POST['aqpago_session']));
			
			$aqpagoOrder->setReferenceId( $order_id )
					->setPlatform('woocommerce')
					->setAmount( number_format($_totalPay, 2, '.', '') )
					->setType( $typeOrder ); 
			// credit, multi_credit, ticket, multi_ticket
			
			$customer = $aqpagoOrder->customer();
			$customer->setName( $first_name )
				->setLastName($last_name)
				->setEmail( $billing_email )
				->setTaxDocument( $document );
			
			$customer->address()
				->setPostCode( $postcode )
				->setStreet( $address_street )
				->setNumber( $address_number )
				->setComplement( $address_comp )
				->setDistrict( $address_district )
				->setCity( $address_city )
				->setState( $address_state );
			
			$customer->phones()
				->setArea( $ddd )
				->setNumber( $phone );
			
			// Frete opcional
			if (is_array($orderWoocommerce->get_items( 'shipping' ))) {
				foreach ($orderWoocommerce->get_items( 'shipping' ) as $item_id => $item) {
					$aqpagoOrder->shipping( number_format($_totalPayShipping, 2, '.', ''), $item->get_name());
				}
			}
			
			// Get and Loop Over Order Items
			foreach ($_itensArray as $item_id => $item) {
				$aqpagoOrder->items()
					->setName( $item['name'] )
					->setQty( $item['qty'] )
					->setAmount( number_format($item['price'], 2, '.', '') );	
			}
			
			// credit, multi_credit, ticket, multi_ticket
			if (sanitize_text_field($_POST['aqpago_type_payment']) == 'credit') {
				
				if ($this->debug == 'yes')  $this->log->info( 'Credito', array( 'source' => 'aqpago-pagamentos' ) );
				
				if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first']) == 'true') {
					//$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
					
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
						$this->log->info( 'Quantidade de parcelas inválido! 4 ', array( 'source' => 'aqpago-pagamentos' ) );
						return;	
					}
					if (!is_numeric($one_card_cid)) {
						wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_id) {
						wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					
					$aqpagoOrder->creditCard(number_format($_totalPay, 2, '.', ''), $one_card_installments)
						->setSecurityCode($one_card_cid)
						->setCardId($one_card_id);
				} else {
					$one_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
					//$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
					$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
					$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_taxvat']);
					
					if (!$one_card_number) {
						wc_add_notice( __('Número do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}						
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
						$this->log->info( 'Quantidade de parcelas inválido! 5 ', array( 'source' => 'aqpago-pagamentos' ) );
						return;	
					}					
					if (!$one_card_owner) {
						wc_add_notice( __('Nome proprietário do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_month || $one_card_month < 1 || $one_card_month > 12) {
						wc_add_notice( __('Mês da validade do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_year || $one_card_year < date("Y")) {
						wc_add_notice( __('Ano da validade do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!is_numeric($one_card_cid)) {
						wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$this->validTaxvat($taxvat)) {
						wc_add_notice( __('CPF inválido para cartão ' . preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']), 'woocommerce'), 'error' );
						return;
					}					
					
					// creditCard('valor total', 'parcelas')
					$aqpagoOrder->creditCard( number_format($_totalPay, 2, '.', ''), $one_card_installments)
						->setCardNumber( $one_card_number )
						->setHolderName( $one_card_owner )
						->setExpirationMonth( $one_card_month )
						->setExpirationYear( $one_card_year )
						->setSecurityCode( $one_card_cid )
						->setCpf( $taxvat );
				}
				
			} elseif ($aqpago_type_payment == 'credit_multiple') {
				
				if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first']) == 'true') {
					$one_card_value = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
					$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
					
					if (!is_numeric($one_card_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;	
					}	
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
						$this->log->info( 'Quantidade de parcelas inválido! 6 ', array( 'source' => 'aqpago-pagamentos' ) );
						return;	
					}
					if (!is_numeric($one_card_cid)) {
						wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_id) {
						wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					
					$aqpagoOrder->creditCard(number_format($_totalPayOne, 2, '.', ''), $one_card_installments)
						->setSecurityCode( $one_card_cid )
						->setCardId( $one_card_id );
				} else {
					$one_card_value = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
					$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
					$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
					$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
					$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_taxvat']);
					
					if (!is_numeric($one_card_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_number) {
						wc_add_notice( __('Número do cartão inválido! ', 'woocommerce'), 'error' );
						return;	
					}					
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválida para o cartão ' . $one_card_number, 'woocommerce'), 'error' );
						return;	
					}					
					if (!$one_card_owner) {
						wc_add_notice( __('Nome proprietário do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_month) {
						wc_add_notice( __('Mês da validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_year) {
						wc_add_notice( __('Ano da validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!is_numeric($one_card_cid)) {
						wc_add_notice( __('Código do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$this->validTaxvat($taxvat)) {
						wc_add_notice( __('CPF inválido para cartão ' . $one_card_number, 'woocommerce'), 'error' );
						return;
					}	
					
					$aqpagoOrder->creditCard( number_format($_totalPayOne, 2, '.', ''), $one_card_installments)
						->setCardNumber( $one_card_number )
						->setHolderName( $one_card_owner )
						->setExpirationMonth( $one_card_month )
						->setExpirationYear( $one_card_year )
						->setSecurityCode( $one_card_cid )
						->setCpf( $taxvat );
				}
				
				if (isset($_POST['aqpago_saved_second']) && sanitize_text_field($_POST['aqpago_saved_second'])) {
					$two_card_value = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
					$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
					$two_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
					$two_card_id = sanitize_text_field($_POST['aqpago_two_cc_card_id']);
					
					if (!is_numeric($two_card_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;	
					}	
					if (!$two_card_installments || $two_card_installments < 1 || $two_card_installments > 12) {
						wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
						$this->log->info( 'Quantidade de parcelas inválido! 7 ', array( 'source' => 'aqpago-pagamentos' ) );
						return;	
					}
					if (!is_numeric($two_card_cid)) {
						wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$two_card_id) {
						wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					
					$aqpagoOrder->creditCard(number_format($_totalPayTwo, 2, '.', ''), $two_card_installments)
						->setSecurityCode( $two_card_cid )
						->setCardId( $two_card_id );
				} else {
					$two_card_value = sanitize_text_field($_POST['aqpago_two_cc_card_value']);
					$two_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_number']);
					$two_card_installments = intval($_POST['aqpago_two_cc_card_installments']);
					$two_card_owner = sanitize_text_field($_POST['aqpago_two_cc_card_owner']);
					$two_card_month = intval($_POST['aqpago_two_cc_card_month']);
					$two_card_year = intval($_POST['aqpago_two_cc_card_year']);
					$two_card_cid = sanitize_text_field($_POST['aqpago_two_cc_card_cid']);
					$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_two_cc_card_taxvat']);
					
					if (!is_numeric($two_card_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;	
					}	
					if (!$two_card_number) {
						wc_add_notice( __('Número do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$two_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválida para o cartão ' . $two_card_number, 'woocommerce'), 'error' );
						return;	
					}					
					if (!$two_card_owner) {
						wc_add_notice( __('Nome proprietário do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$two_card_month || $two_card_month < 1 || $two_card_month > 12) {
						wc_add_notice( __('Mês da validade do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}					
					if (!$two_card_year || $two_card_year < date("Y")) {
						wc_add_notice( __('Mês da validade do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!is_numeric($two_card_cid)) {
						wc_add_notice( __('Código do cartão '.$two_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$this->validTaxvat($taxvat)) {
						wc_add_notice( __('CPF inválido para cartão ' . $two_card_number, 'woocommerce'), 'error' );
						return;
					}
					
					$aqpagoOrder->creditCard( number_format($_totalPayTwo, 2, '.', ''), $two_card_installments)
						->setCardNumber( $two_card_number )
						->setHolderName( $two_card_owner )
						->setExpirationMonth( $two_card_month )
						->setExpirationYear( $two_card_year )
						->setSecurityCode( $two_card_cid )
						->setCpf( $taxvat );
				}
				
			} elseif ($aqpago_type_payment == 'ticket_multiple') {
				
				if (isset($_POST['aqpago_saved_first']) && sanitize_text_field($_POST['aqpago_saved_first'])) {
					$one_card_value = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
					$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$one_card_id = sanitize_text_field($_POST['aqpago_one_cc_card_id']);
					
					if (!is_numeric($one_card_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;	
					}	
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválido!', 'woocommerce'), 'error' );
						$this->log->info( 'Quantidade de parcelas inválido! 8 ', array( 'source' => 'aqpago-pagamentos' ) );
						return;	
					}
					if (!is_numeric($one_card_cid)) {
						wc_add_notice( __('Código do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_id) {
						wc_add_notice( __('ID do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					
					$aqpagoOrder->creditCard(number_format($_totalPayOne, 2, '.', ''), $one_card_installments)
						->setSecurityCode( $one_card_cid )
						->setCardId( $one_card_id );
				} else {
					$one_card_value = sanitize_text_field($_POST['aqpago_one_cc_card_value']);
					$one_card_installments = intval($_POST['aqpago_one_cc_card_installments']);
					$one_card_number = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_number']);
					$one_card_owner = sanitize_text_field($_POST['aqpago_one_cc_card_owner']);
					$one_card_month = intval($_POST['aqpago_one_cc_card_month']);
					$one_card_year = intval($_POST['aqpago_one_cc_card_year']);
					$one_card_cid = sanitize_text_field($_POST['aqpago_one_cc_card_cid']);
					$taxvat = preg_replace('/[^0-9]/', '', $_POST['aqpago_one_cc_card_taxvat']);
					
					if (!is_numeric($one_card_value)) {
						wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
						return;	
					}	
					if (!$one_card_number) {
						wc_add_notice( __('Número do cartão inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_installments) {
						wc_add_notice( __('Quantidade de parcelas inválida para o cartão ' . $one_card_number, 'woocommerce'), 'error' );
						return;	
					}					
					if (!$one_card_owner) {
						wc_add_notice( __('Nome proprietário do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_month || $one_card_month < 1 || $one_card_month > 12) {
						wc_add_notice( __('Mês da validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$one_card_year || $one_card_year < date("Y")) {
						wc_add_notice( __('Mês da validade do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!is_numeric($one_card_cid)) {
						wc_add_notice( __('Código do cartão '.$one_card_number.' inválido!', 'woocommerce'), 'error' );
						return;	
					}
					if (!$this->validTaxvat($taxvat)) {
						wc_add_notice( __('CPF inválido para cartão ' . $one_card_number, 'woocommerce'), 'error' );
						return;
					}					
					
					$aqpagoOrder->creditCard( number_format($_totalPayOne, 2, '.', ''), $one_card_installments)
						->setCardNumber( $one_card_number )
						->setHolderName( $one_card_owner )
						->setExpirationMonth( $one_card_month )
						->setExpirationYear( $one_card_year )
						->setSecurityCode( $one_card_cid )
						->setCpf( $taxvat );
				}
				
				$aqpago_ticket_value = sanitize_text_field($_POST['aqpago_ticket_value']);
				
				if (!is_numeric($aqpago_ticket_value)) {
					wc_add_notice( __('Valor inválido!', 'woocommerce'), 'error' );
					return;	
				}	
				
				// payment by ticket
				$aqpagoOrder->ticket( number_format($aqpago_ticket_value, 2, '.', '') )
					->setBodyInstructions( $this->body_instructions );
			} elseif ($aqpago_type_payment == 'ticket') {
				// payment by ticket
				$aqpagoOrder->ticket( number_format($orderWoocommerce->get_total(), 2, '.', '') )
					->setBodyInstructions( $this->body_instructions );
			}
			
			if ($this->debug == 'yes') $this->log->info('Request: ' . json_encode(array_filter($aqpagoOrder->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );	
			
			try {
				$transaction = (new Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->createOrder($aqpagoOrder);
			
				if ($this->debug == 'yes') $this->log->info( 'Response: ' . json_encode(array_filter($transaction->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );	
			
			} catch (Exception $e) {
				$Message = $e->getMessage();
				$convert = json_decode($Message, true);
			}	
		}
		
		if (!is_object($transaction)) {
			if ($this->debug == 'yes') {
				$this->log->info( 
					'Response erro: ' . json_encode(array_filter($Message), JSON_PRETTY_PRINT), 
					array( 'source' => 'aqpago-pagamentos' ) 
				);			
				$this->log->info( 
					'Request: ' . json_encode(array_filter($convert), JSON_PRETTY_PRINT), 
					array( 'source' => 'aqpago-pagamentos' ) 
				);	
			}
			
			if (is_array($convert['error'])) {
				foreach ($convert['error'] as $key => $erros) {
					if (is_array($erros)) {
						foreach ($erros as $tag => $list) {
							if (is_array($list)) {
								foreach ($list as $ps => $messages) {
									if (is_array($list)) {
										foreach ($messages as $message) {
											wc_add_notice( __( ucfirst( $this->trans_erros($key) ), 'woocommerce') . ': ' . __( $this->trans_erros( $message ), 'woocommerce'), 'error' );
										}
									} else {
										wc_add_notice( __( ucfirst( $this->trans_erros($key)), 'woocommerce') . ': ' . __( $this->trans_erros( $message ), 'woocommerce'), 'error' );
									}
								}
							} else {
								wc_add_notice( __( ucfirst( $this->trans_erros($key) ), 'woocommerce') . ': ' . __( json_encode($messages), 'woocommerce'), 'error' );
							}
						}
					} else {
						wc_add_notice( __( ucfirst( $this->trans_erros($key) ), 'woocommerce') . ': ' . __( json_encode($erros), 'woocommerce'), 'error' );
					}
				}
			} else {
				wc_add_notice( __( $this->trans_erros($convert['error']), 'woocommerce'), 'error' );
			}
			
			return;
		}
		
		/** Order Create **/
		if ($transaction->getStatus()) {
			/** Payment exist update **/
			if ($process) {
				update_post_meta($order_id, '_type_payment', sanitize_text_field($_POST['aqpago_type_payment']));
				update_post_meta($order_id, '_aqpago_response', json_encode(array_filter($transaction->jsonSerialize()), JSON_PRETTY_PRINT));
				update_post_meta($order_id, '_aqpago_attempts', ($attempts + 1) );
			} else {
				add_post_meta($order_id, '_type_payment', sanitize_text_field($_POST['aqpago_type_payment']));
				add_post_meta($order_id, '_aqpago_order_id', $transaction->getId());
				add_post_meta($order_id, '_aqpago_response', json_encode(array_filter($transaction->jsonSerialize()), JSON_PRETTY_PRINT));
				add_post_meta($order_id, '_aqpago_attempts', 1);
			}
		}
		
		if ($transaction->getStatus() && $transaction->getStatus() != 'ORDER_NOT_PAID') {
			
			$erros_ids 		= get_post_meta($order_id, '_erros_ids');
			$erros_create 	= ($erros_ids) ? true : false;
			$erros_ids 		= json_decode($erros_ids, true);
			$msg_erros 		= array();
			
			// colocar cartão com erro
			if (is_array($transaction->getPayments())) {
				foreach ($transaction->getPayments() as $k => $pay) {
					if ($pay->getType() == 'credit') {
						if ($pay->getStatus() == 'succeeded' || $pay->getStatus() == 'pre_authorized') {								
							if (!isset($cardsSave[$pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()])) {
								$cardsSave[$pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()] = array(
									'id' => $pay->getCreditCard()->getId(),
									'first4_digits' => $pay->getCreditCard()->getFirst4Digits(),
									'last4_digits' => $pay->getCreditCard()->getLast4Digits(),
									'holder_name' => $pay->getCreditCard()->getHolderName(),
									'flag' => $pay->getCreditCard()->getFlag(),
									'expiration_month' => $pay->getCreditCard()->getExpirationMonth(),
									'expiration_year' => $pay->getCreditCard()->getExpirationYear(),
								);
							}							
						}
					}
				}
			}
			
			/** Save card customer **/
			update_post_meta(get_current_user_id(), '_cards_saves', json_encode($cardsSave));
			
			if (sanitize_text_field($_POST['aqpago_type_payment']) == 'ticket' || sanitize_text_field($_POST['aqpago_type_payment']) == 'ticket_multiple') {
				// Mark as on-hold
				
				if ($transaction->getStatus() == 'ORDER_WAITING') {
					$orderWoocommerce->update_status('on-hold');
					$orderWoocommerce->add_order_note( __('Boleto gerado aguardando confirmação de pagamento.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_IN_ANALYSIS') {
					$orderWoocommerce->update_status('on-hold');
					$orderWoocommerce->add_order_note( __('Pagamento em análise.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_PAID') {
					$orderWoocommerce->update_status('processing');
					$orderWoocommerce->add_order_note( __('Pagamento recebido.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_PARTIAL_PAID') {
					$orderWoocommerce->update_status('on-hold');
					$orderWoocommerce->add_order_note( __('Pago parcialmente, aguardando pagamento total.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_CANCELED') {
					$orderWoocommerce->update_status('canceled ');
					$orderWoocommerce->add_order_note( __('Pagamento cancelado.', 'woothemes') );
				} else {
					$orderWoocommerce->update_status('failed');
					$orderWoocommerce->add_order_note( __('Falha ao realizar pagamento 4.', 'woothemes') );
				}
			} else {
				if ($transaction->getStatus() == 'ORDER_IN_ANALYSIS') {
					$orderWoocommerce->update_status('on-hold');
					$orderWoocommerce->add_order_note( __('Pagamento em análise.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_PAID') {
					$orderWoocommerce->update_status('processing');
					$orderWoocommerce->add_order_note( __('Pagamento recebido.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_WAITING') {
					$orderWoocommerce->update_status('on-hold');
					$orderWoocommerce->add_order_note( __('Aguardando pagamento.', 'woothemes') );
				} elseif ($transaction->getStatus() == 'ORDER_PARTIAL_PAID') {
					//$orderWoocommerce->update_status('on-hold');
					$orderWoocommerce->add_order_note( __('Pago parcialmente, aguardando pagamento total.', 'woothemes') );					
					
					// colocar cartão com erro
					if (is_array($transaction->getPayments())) {
						foreach ($transaction->getPayments() as $k => $pay) {
							
							if ($pay->getType() == 'credit') {
								if (!isset($erros_ids[ $pay->getId() ]) && $pay->getStatus() != 'succeeded' && $pay->getStatus() != 'pre_authorized') {
									$erros_ids[ $pay->getId() ] = $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits();
									
									wc_add_notice( 'ORDER_NOT_PAID#'. sanitize_text_field( $pay->getCreditCard()->getFirst4Digits() ) . sanitize_text_field( $pay->getCreditCard()->getLast4Digits() ) .'#' . '**** **** **** ' . sanitize_text_field( $pay->getCreditCard()->getLast4Digits() ) . ' ' . __( $this->trans_erros( $pay->getMessage() ), 'woocommerce'), 'error' );
								
									$orderWoocommerce->add_order_note( 
										__( 
											sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . ' **** **** ' . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . ' ' . $this->trans_erros( $pay->getMessage() ), 
											'woothemes'
										) 
									);						
								} else {
									wc_add_notice( '**** **** **** ' . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . ' processado com sucesso!', 'success' );
									wc_add_notice( 'ORDER_PAID#' . sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . '#', 'error' );							
									
									$cardsSave[$pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()] = array(
										'id' => $pay->getCreditCard()->getId(),
										'first4_digits' => $pay->getCreditCard()->getFirst4Digits(),
										'last4_digits' => $pay->getCreditCard()->getLast4Digits(),
										'holder_name' => $pay->getCreditCard()->getHolderName(),
										'flag' => $pay->getCreditCard()->getFlag(),
										'expiration_month' => $pay->getCreditCard()->getExpirationMonth(),
										'expiration_year' => $pay->getCreditCard()->getExpirationYear(),
									);
										
									if ($_card_one == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
										update_post_meta($order_id, '_card_one_success', 'true');
									}
									
									if ($_card_two == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
										update_post_meta($order_id, '_card_two_success', 'true');
									}
								
								
									if ($pay->getStatus() == 'succeeded' || $pay->getStatus() == 'pre_authorized') {
										update_post_meta($order_id, '_price_pay', sanitize_text_field($pay->getAmount()));
										update_post_meta($order_id, '_card_pay', sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . sanitize_text_field($pay->getCreditCard()->getLast4Digits()));
									}								
								}
								
								if ($pay->getStatus() != 'succeeded' && $pay->getStatus() != 'pre_authorized') {
									
									if ($_card_one == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
										update_post_meta($order_id, '_card_one_erro', 'true');
									}
									
									if ($_card_two == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
										update_post_meta($order_id, '_card_two_erro', 'true');
									}
									
								}
							}
						}
						
						
						if ($erros_create) {
							update_post_meta($order_id, '_erros_ids', sanitize_text_field(json_encode($erros_ids)));
						} else {
							add_post_meta($order_id, '_erros_ids', sanitize_text_field(json_encode($erros_ids)));
						}
					}
			
					return;
				} elseif ($transaction->getStatus() == 'ORDER_CANCELED') {
					$orderWoocommerce->update_status('canceled ');
					$orderWoocommerce->add_order_note( __('Pagamento cancelado.', 'woothemes') );
				} else {
					$orderWoocommerce->update_status('failed');
					$orderWoocommerce->add_order_note( __('Falha ao realizar pagamento 5.', 'woothemes') );
				}
				
			}
			
			// Remove cart
			$woocommerce->cart->empty_cart();
			
			update_post_meta($order_id, '_aqpago_closed', 'true');
			
			// Return thankyou redirect
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $orderWoocommerce )
			);
			
		} else {
			
			// colocar cartão com erro
			if (is_array($transaction->getPayments())) {
				foreach ($transaction->getPayments() as $k => $pay) {
					if ($pay->getType() == 'credit') {
						if ($pay->getStatus() == 'succeeded' || $pay->getStatus() == 'pre_authorized') {								
							if (!isset($cardsSave[$pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()])) {
								$cardsSave[$pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()] = array(
									'id' => $pay->getCreditCard()->getId(),
									'first4_digits' => $pay->getCreditCard()->getFirst4Digits(),
									'last4_digits' => $pay->getCreditCard()->getLast4Digits(),
									'holder_name' => $pay->getCreditCard()->getHolderName(),
									'flag' => $pay->getCreditCard()->getFlag(),
									'expiration_month' => $pay->getCreditCard()->getExpirationMonth(),
									'expiration_year' => $pay->getCreditCard()->getExpirationYear(),
								);
							}							
						}
						
						if ($this->debug == 'yes') $this->log->info( 'getStatus card: ' . sanitize_text_field($pay->getStatus()), array( 'source' => 'aqpago-pagamentos' ) );	
						
						if ($pay->getStatus() != 'succeeded' && $pay->getStatus() != 'pre_authorized') {
							
							if ($this->debug == 'yes') {
								$this->log->info( 
									'getCreditCard card: ' . sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . sanitize_text_field($pay->getCreditCard()->getLast4Digits()), 
									array( 'source' => 'aqpago-pagamentos' ) 
								);							
								$this->log->info( 
									'_card_one: ' . sanitize_text_field($_card_one), 
									array( 'source' => 'aqpago-pagamentos' ) 
								);	
								$this->log->info( 
									'_card_two: ' . sanitize_text_field($_card_two), 
									array( 'source' => 'aqpago-pagamentos' ) 
								);
							}
							
							if ($_card_one == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
								update_post_meta($order_id, '_card_one_erro', 'true');
								if($this->debug == 'yes') $this->log->info( 'update_post_meta: _card_one_erro', array( 'source' => 'aqpago-pagamentos' ) );	
							}
							
							if ($_card_two == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
								update_post_meta($order_id, '_card_two_erro', 'true');
								if($this->debug == 'yes') $this->log->info( '_card_two_erro: _card_two_erro', array( 'source' => 'aqpago-pagamentos' ) );							
							}
							
						}
					}
				}
			}
			
			/** Save card customer **/
			update_post_meta(get_current_user_id(), '_cards_saves', json_encode($cardsSave));
			
			$erros_ids 		= get_post_meta($order_id, '_erros_ids');
			$erros_create 	= ($erros_ids) ? true : false;
			$erros_ids 		= json_decode($erros_ids, true);
			$msg_erros 		= array();
			
			$orderWoocommerce->update_status('failed');
			if ($transaction->getStatus() == 'ORDER_NOT_PAID') {
				
				$orderWoocommerce->add_order_note(
					__('Pedido não pago.', 'woothemes') 
				);
				
				if (is_array($transaction->getPayments())) {
					foreach ($transaction->getPayments() as $k => $pay) {
						
						if ($pay->getType() == 'credit') {
							if (!isset($erros_ids[ $pay->getId() ]) && $pay->getStatus() != 'succeeded' && $pay->getStatus() != 'pre_authorized') {
								$erros_ids[ $pay->getId() ] = $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits();
								
								wc_add_notice( 'ORDER_NOT_PAID#'. sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) .'#' . '**** **** **** ' . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . ' '. $this->trans_erros( $pay->getMessage() ), 'error' );
								
								$orderWoocommerce->add_order_note( 
									__( 
										sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . ' **** **** ' . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . ' ' . $this->trans_erros( $pay->getMessage() ), 
										'woothemes'
									) 
								);
							} else {
								wc_add_notice( '**** **** **** ' . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . ' processado com sucesso!', 'success' );
								wc_add_notice( 'ORDER_PAID#' . sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . sanitize_text_field($pay->getCreditCard()->getLast4Digits()) . '#', 'error' );							
								
								$cards_saves[$pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()] = sanitize_text_field($pay->getCreditCard()->getId());
								
								if ($_card_one == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
									update_post_meta($order_id, '_card_one_success', 'true');
								}
								
								if ($_card_two == $pay->getCreditCard()->getFirst4Digits() . $pay->getCreditCard()->getLast4Digits()) {
									update_post_meta($order_id, '_card_two_success', 'true');
								}
								
								if ($pay->getStatus() == 'succeeded' || $pay->getStatus() == 'pre_authorized') {
									update_post_meta($order_id, '_price_pay', sanitize_text_field($pay->getAmount()));
									update_post_meta($order_id, '_card_pay', sanitize_text_field($pay->getCreditCard()->getFirst4Digits()) . sanitize_text_field($pay->getCreditCard()->getLast4Digits()));
								}
							}
							
						}
					}
					
					if ($erros_create) {
						update_post_meta($order_id, '_erros_ids', json_encode($erros_ids));
					} else {
						add_post_meta($order_id, '_erros_ids', json_encode($erros_ids));
					}
				}
				
			} else {
				wc_add_notice( $this->trans_erros( 'Failed to process payment' ), 'error' );
				$orderWoocommerce->add_order_note( __( $this->trans_erros( 'Failed to process payment' ) , 'woothemes') );
			}
			
			if ($attempts >= 4) {
				$orderWoocommerce->update_status('cancelled');
				$orderWoocommerce->add_order_note( __( 'limite de tentativas atingido.' , 'woothemes') );
				
				
				$orderAq = new Aqbank\Apiv2\Aqpago\Order(sanitize_text_field($_POST['aqpago_session']));
				$orderAq->setOrderId($process['id']);

				$response = (new Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->cancelOrder($orderAq);
					
				if ($this->debug == 'yes') $this->log->info( 'Cancel: ' . json_encode(array_filter($response->jsonSerialize()), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-pagamentos' ) );
				
				// Remove cart
				$woocommerce->cart->empty_cart();
				
				update_post_meta($order_id, '_aqpago_closed', 'true');
				
				// Return thankyou redirect
				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url( $orderWoocommerce )
				);	
			}
			
			return;
		}
	}
	
	public function webhook() {
		$entityBody = file_get_contents('php://input');
		
		if ($this->debug == 'yes') $this->log->info( 'entityBody: ' . json_encode($entityBody, JSON_PRETTY_PRINT), array( 'source' => 'aqpago-webhook' ) );	

		$convert 	= json_decode($entityBody, true);
		$order_id = $convert['order_id'];
		
		if ($this->debug == 'yes') {
			$this->log->info( 
				'convert: ' . json_encode($convert, JSON_PRETTY_PRINT), 
				array( 'source' => 'aqpago-webhook' ) 
			);			
			$this->log->info( 
				'order_id: ' . $order_id, 
				array( 'source' => 'aqpago-webhook' ) 
			);
		}
		
		if (empty($order_id)) {
			if($this->debug == 'yes') $this->log->info( 'order_id vazio: ' . $order_id, array( 'source' => 'aqpago-webhook' ) );
			
			return;
		}
		
		if (isset($order_id) && $order_id) {
			require_once( plugin_dir_path(__DIR__) . 'sdk/Includes.php' );
			
			$seller_doc 	= preg_replace('/[^0-9]/', '', $this->document);
			$seller_token 	= $this->token;
			$sellerAqpago   = new Aqbank\Apiv2\SellerAqpago($seller_doc, $seller_token, 'modulo woocommerce');
			
			if ($this->environment == 'production') {
				// Ambiente de produção
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::production();
			} else {
				// Ambiente de homologação
				$environment = Aqbank\Apiv2\Aqpago\Request\AqpagoEnvironment::sandbox();
			}
			
			$aqpagoconsult 	= (new Aqbank\Apiv2\Aqpago\Aqpago($sellerAqpago, $environment))->getOrder($order_id);
			
			if ($this->debug == 'yes') {
				$this->log->info( 'AQPAGO WEBHOOK', array( 'source' => 'aqpago-webhook' ) );
				$this->log->info( 'AQPAGO ORDER: ' . json_encode($aqpagoconsult->jsonSerialize(), JSON_PRETTY_PRINT), array( 'source' => 'aqpago-webhook' ) );
			}
			
			$response = json_decode($aqpagoconsult->jsonSerialize(), true);
			
			if ($this->debug == 'yes') $this->log->info( 'AQPAGO ORDER: ' . $aqpagoconsult->getId(), array( 'source' => 'aqpago-webhook' ) );	
			
			if ($aqpagoconsult->getId()) {
				
				$reference_id 		= $aqpagoconsult->getReferenceId();
				$orderWoocommerce 	= wc_get_order( $reference_id );
				$aqpagoResponse 	= get_post_meta($reference_id, '_aqpago_response', true);
				$aqpagoResponse 	= json_decode($aqpagoResponse, true);
				
				if ($this->debug == 'yes') $this->log->info( 'aqpagoResponse id: ' . $aqpagoResponse['id'], array( 'source' => 'aqpago-webhook' ) );
				
				if (strtoupper($aqpagoResponse['id']) == strtoupper($aqpagoconsult->getId())) {
					// process order new status		
							
					if ($this->debug == 'yes') $this->log->info( 'getStatus: ' . $aqpagoconsult->getStatus(), array( 'source' => 'aqpago-webhook' ) );
						
					switch ($aqpagoconsult->getStatus()) {
						case 'ORDER_PAID': 
							if ($orderWoocommerce->get_status() != 'processing') {
								$orderWoocommerce->update_status('processing');
								$orderWoocommerce->add_order_note( __('Atualizado por notificação!', 'woothemes') );
								$orderWoocommerce->add_order_note( __('Pagamento recebido.', 'woothemes') );
							}
						break;
						case 'ORDER_IN_ANALYSIS': 
							if ($orderWoocommerce->get_status() != 'on-hold') {
								$orderWoocommerce->update_status('on-hold');
								$orderWoocommerce->add_order_note( __('Atualizado por notificação!', 'woothemes') );
								$orderWoocommerce->add_order_note( __('Pagamento em análise.', 'woothemes') );
							}
						break;
						case 'ORDER_WAITING':
							if ($orderWoocommerce->get_status() != 'on-hold') {
								$orderWoocommerce->update_status('on-hold');
								$orderWoocommerce->add_order_note( __('Atualizado por notificação!', 'woothemes') );
								$orderWoocommerce->add_order_note( __('Aguardando pagamento.', 'woothemes') );
							}
						break;
						case 'ORDER_WAITING':
							if ($orderWoocommerce->get_status() != 'on-hold') {
								$orderWoocommerce->update_status('on-hold');
								$orderWoocommerce->add_order_note( __('Atualizado por notificação!', 'woothemes') );
								$orderWoocommerce->add_order_note( __('Pago parcialmente, aguardando pagamento total.', 'woothemes') );
							}
						break;
						case 'ORDER_CANCELED':
						case 'ORDER_REVERSED':
						case 'ORDER_CHARGE_BACK':
						case 'ORDER_DISPUTE':
						case 'ORDER_FAILED':
							if ($orderWoocommerce->get_status() != 'cancelled') {
								$orderWoocommerce->update_status('cancelled');
								$orderWoocommerce->add_order_note( __('Atualizado por notificação!', 'woothemes') );
								$orderWoocommerce->add_order_note( __('Status atualizado para: ' . $this->trans_erros( $aqpagoconsult->getStatus() ), 'woothemes') );
							}
						break;
					}
					
					update_post_meta($reference_id, '_aqpago_response', json_encode(array_filter($aqpagoconsult->jsonSerialize()), JSON_PRETTY_PRINT));
				} else {
					if($this->debug == 'yes') $this->log->info( 'Erro ao receber notificação woocommerce pedido ID: ' . $reference_id . ' ORDER ID WP:  ' . $aqpagoResponse['id'] . ' ORDER ID API: ' .  $aqpagoconsult->getId(), array( 'source' => 'aqpago-webhook-erro' ) );
				}
			} else {
				if($this->debug == 'yes') $this->log->info( 'Falha ao consultar orderm: '.  json_encode($aqpagoconsult->jsonSerialize()), array( 'source' => 'aqpago-webhook-erro' ) );
			}
		} else {
			if ($this->debug == 'yes') $this->log->info( '_POST: '.  json_encode($_POST), array( 'source' => 'aqpago-webhook-erro' ) );
		}
	}
	
	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view() {
		if (defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.2', '>=' )) {
			return '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'woocommerce' ) . '</a>';
		}

		return '<code>woocommerce/logs/' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>';
	}
	
	public function trans_erros($erro) {
		$erroTerms = array(
			'processing' 							=> 'pago',
			'cancelled' 							=> 'cancelado',
			'canceled' 								=> 'cancelado',
			'on-hold' 								=> 'aguardando',
			'failed' 								=> 'Falha',
			'succeeded' 							=> 'Pago',
			'pre_authorized' 						=> 'Em análise',
			'update limit reached.' 				=> 'limite de tentativas atingido.',
			'payment processed.' 					=> 'Pagamento processado.',
			'customer' 								=> 'comprador',
			'The name field is required.' 			=> 'O nome é obrigatório.',
			'The email field is required.' 			=> 'O email é obrigatório.',
			'The postcode field is required.' 		=> 'O cep é obrigatório.',
			'The street field is required.' 		=> 'O endereço é obrigatório.',
			'The number field is required.' 		=> 'O número é obrigatório.',
			'The district field is required.' 		=> 'O bairro é obrigatório.',
			'The city field is required.' 			=> 'O cidade é obrigatório.',
			'The state field is required.' 			=> 'O estado é obrigatório.',
			'The tax document field is required.' 	=> 'O Documento é obrigatório.',
			'The area field is required.' 			=> 'O telefone é obrigatório.',
			'The phone field is required.' 			=> 'O telefone é obrigatório.',
			'Card without enough background.' 		=> 'Cartão sem fundos suficiente.',
			'Expired credit card.' 					=> 'Cartão de crédito vencido.',
			'Failed to process payment!' 			=> 'Falha ao processar pagamento!',
			'Failed to process payment!' 			=> 'Falha ao processar pagamento!',
			'This credit card is not valid, please review your card details.' => 'Este cartão de crédito não é válido, verifique os detalhes do seu cartão.',
			'Credit card process is temporarily unavailable at the specified location. Please try again later. If the problem persists, please contact Technical Support (support@pagzoop.com).'=>'O processo de cartão de crédito está temporariamente indisponível no local especificado. Por favor, tente novamente mais tarde. Se o problema persistir, entre em contato com o Suporte Técnico.',
			'ORDER_CREATE'=>'Pagamento criado',
			'ORDER_WAITING'=>'Aguardando pagamento',
			'ORDER_IN_ANALYSIS'=>'Pagamento em análise',
			'ORDER_NOT_PAID'=>'Não pago',
			'ORDER_PAID'=>'Pagamento recebido',
			'ORDER_PARTIAL_PAID'=>'Pago parcialmente',
			'ORDER_CANCELED'=>'Pagamento cancelado',
			'ORDER_REVERSED'=>'Pagamento devolvido',
			'ORDER_PARTIAL_REVERSED'=>'Pagamento devolvido parcial',
			'ORDER_CHARGE_BACK'=>'Charge Back',
			'ORDER_DISPUTE'=>'Pagamento em disputa',
			'ORDER_FAILED'=>'Pagamento com falha',
			'The card number must be between 12 and 19 digits.'=>'O número do cartão deve ter entre 12 e 19 dígitos.',
			'The cpf must be a number.'=>'O CPF deve ser um número.',
			'The cpf must be 11 digits.'=>'O CPF deve ter 11 dígitos.',
		);
		
		$varString = (isset($erroTerms[$erro])) ? $erroTerms[$erro] : $erro;
		$varString = str_ireplace('order total different from the sum of payments, total paid:', 'total do pedido diferente da soma dos pagamentos, total pago:', $varString);
		$varString = str_ireplace('order total:', 'total do pedido:', $varString);
		
		return $varString;
	}
}
