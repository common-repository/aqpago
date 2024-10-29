<?php

class WC_Aqpago_Order_Info 
{
    public function __construct() {
        add_action( 'add_meta_boxes', array($this, 'aqpago_woocommerce_meta_payment') );
	}
	
    public function aqpago_woocommerce_meta_payment() {
        if ( get_the_ID() && get_post_type( get_the_ID() ) == 'shop_order' ) {
            $orderWoocommerce = new WC_Order( get_the_ID() ); 
			$payment_method   = get_post_meta( get_the_ID(), '_payment_method', true );
			
            if($payment_method == 'aqpago') {
                $type       = get_post_meta(get_the_ID(), '_type_payment', true);
                $response   = get_post_meta(get_the_ID(), '_aqpago_response', true);
                $response   = json_decode($response, true);

                if($type) {
                    $title = "";
                    if($type == 'ticket'){
                        $title = __( 'Boleto', 'woocommerce' ); 
                    }
					else if($type == 'credit') {
						$title = __( 'Cartão de Crédito', 'woocommerce' ); 
					}
					else if($type == 'credit_multiple') {
						$title = __( '2 Cartões', 'woocommerce' ); 
					}
					else if($type == 'ticket_multiple') {
						$title = __( 'Cartão + Boleto', 'woocommerce' ); 
					}
					
                    $title .= " - " . $this->getOrderStatus( $response['status'] );


                    add_meta_box(
                        'aqpago-woocommerce-payment-info',
                        $title,
                        array($this, 'aqpago_fields_info'),
                        'shop_order',
                        'normal',
                        'core'
                    );
                }
            }
        }
    }
	
    public function aqpago_fields_info() {
        $orderWoocommerce = new WC_Order( get_the_ID() );
        
        if($orderWoocommerce->get_payment_method() == 'aqpago') {
			
            $response   = get_post_meta(get_the_ID(), '_aqpago_response', true);
            $response   = json_decode($response, true);
			
			wc_get_template(
				'aqpago.php', array(
					'title' =>  __( null, 'woocommerce' ),
					'response'	=> $response,
					'type'		=> $response['type'],
					'status'	=> $response['status']
				), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path() . '/success/'
			);
			
			if(isset($response['payments'])) {
				
				echo "<h3>" . esc_html( __( 'AQPago', 'woocommerce' ) ) . "</h3>";
				
				foreach($response['payments'] as $k => $payment){
					
					$payment['status'] = $this->trans_info($response['payments'][$k]['status']);
					$payment['message'] = $this->trans_info($response['payments'][$k]['message']);
					
					if($payment['type'] == 'ticket') {
						wc_get_template(
							'order-info-ticket.php', array(
								'context' => $this,
								'response' => $response,
								'payment' => $payment,
							), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path() . '/admin/'
						);
					}
					else if($payment['type'] == 'credit') {
						wc_get_template(
							'order-info-credit.php', array(
								'context' => $this,
								'response' => $response,
								'payment' => $payment,
							), 'woocommerce/aqpago/', WC_Aqpago::get_templates_path() . '/admin/'
						);
					}
				}
			}
        }
    }
	
    public function getPaymentStatus($status) {
        $payment_status = array(
            'succeeded' => 'Pago',
            'failed'=> 'Falhado',
            'pending'=> 'Pendente',
            'canceled'=> 'Cancelado',
            'pre_authorized'=> 'Em análise',
            'reversed'=> 'Revertido',
            'refunded'=> 'Devolvido',
            'new'=> 'Novo',
            'partial_refunded'=> 'Revertido parcial',
            'dispute'=> 'Disputa',
            'charge_back' => 'Charge Back',
        );

       return (isset($payment_status[ $status ])) ?  $payment_status[ $status ] : $status;
    }

    public function getOrderStatus($status) {
        $order_status = array(
            'ORDER_CREATE'          => 'Criado',
            'ORDER_WAITING'         => 'Aguardando',
            'ORDER_IN_ANALYSIS'     => 'Em análise',
            'ORDER_NOT_PAID'        => 'Não pago',
            'ORDER_PAID'            => 'Pago',
            'ORDER_PARTIAL_PAID'    => 'Pago parcial',
            'ORDER_CANCELED'        => 'Cancelado',
            'ORDER_REVERSED'        => 'Revertido',
            'ORDER_PARTIAL_REVERSED'=> 'Revertido parcial',
            'ORDER_CHARGE_BACK'     => 'Charge back',
            'ORDER_DISPUTE'         => 'Disputa aberta',
            'ORDER_FAILED'          => 'Falha'
        );

        return (isset($order_status[ $status ])) ?  $order_status[ $status ] : $status;
    }
	
	public function trans_info($var) {
		$Terms = array(
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
		);
		
		return (isset($Terms[$var])) ? $Terms[$var] : $var;
	}
}
