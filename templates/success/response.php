<script>
var flagVisa = '<img class="visa-flag" style="width:80%;" src="<?php echo esc_url( $flagVisa ) ?>" />';
</script>
<div class="wc-aqpago-box">	
	<div class="aqbank_resume_order">	
		<div class="title">
			<span><?php echo __( 'RESUMO DO PEDIDO', 'woocommerce' ) ?></span><span class="order-number"><?php echo esc_html( $id ) ?></span>
		</div>
		<div class="grandtotal-resume">
			<div class="grandtotal-box">
				<div id="iwd_opc_review_totals">
				
					<div class="iwd_opc_review_total">
						<div data-bind="i18n: title" class="iwd_opc_review_total_cell">Valor em compra</div>
						<div class="iwd_opc_review_total_cell" data-bind="text: getValue()">R$<?php echo esc_html( number_format($order->get_subtotal(), 2, ',', '.') ) ?></div>
					</div>
					
					<div class="iwd_opc_review_total iwd_opc_review_total_shipping">
						<div class="iwd_opc_review_total_cell" data-bind="i18n: title">Frete</div>
						<div class="iwd_opc_review_total_cell" data-bind="text: getValue()">R$<?php echo esc_html( number_format($order->get_shipping_total(), 2, ',', '.') ) ?></div>
					</div>
					
					<?php if($order->get_discount_total() > 0): ?>
						<div class="iwd_opc_review_total">
							<div data-bind="i18n: title" class="iwd_opc_review_total_cell">Descontos</div>
							<div class="iwd_opc_review_total_cell" data-bind="text: getValue()">-R$<?php echo esc_html( number_format($order->get_discount_total(), 2, ',', '.') ) ?></div>
						</div>
					<?php endif; ?>
					
					<div class="iwd_opc_review_total iwd_opc_grand_total">
						<div class="iwd_opc_review_total_cell" data-bind="i18n: title">Total</div>
						<div class="iwd_opc_review_total_cell">R$<?php echo esc_html( number_format($order->get_total(), 2, ',', '.') ) ?></div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="resume-payment">
			<div class="title">
				<?php echo __( 'DETALHES DO PAGAMENTO', 'woocommerce' ) ?>
			</div>
			
			<div style="clear:both;"></div>
			
			<div class="title-status" style="text-align: center;clear:both;margin: 20px 0px;">
				<span class="order-status">
					<?php echo esc_html( $status ) ?>
				</span>
			</div>
			
			<div class="resume-payment-info" style="margin: 30px 0px;">
				
				<div class="aqbank_payment_integral_box">
					
					<?php if(is_array($response['payments'])): ?>
						<?php foreach($response['payments'] as $k => $payment): ?>
							<?php if($payment['type'] == 'ticket'): ?>
								<div id="scaner-modal" class="modal modal-ticket">
									<img src="data:image/png;base64,<?php echo esc_attr( $context->get_bar_code_img($payment['ticket_bar_code']) ) ?>" style="height: 81px;width: 100%;max-width: 656px;margin: auto;">
								</div>
								
								<div class="li-form-payment li-form-payment-ticket">
									<div class="li-flag-card">
										<div class="middle-number-card">
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="41.77" height="27.569" viewBox="0 0 41.77 27.569"><defs><clipPath id="clip-path"/></defs><g id="Grupo_4161" data-name="Grupo 4161" transform="translate(-4.177 -5.012)"><rect id="Retângulo_55" data-name="Retângulo 55" width="1.67" height="27.569" transform="translate(4.177 5.012)" fill="#4f076b"/><rect id="Retângulo_56" data-name="Retângulo 56" width="2.506" height="23.392" transform="translate(8.354 5.012)" fill="#4f076b"/><rect id="Retângulo_57" data-name="Retângulo 57" width="1.671" height="23.392" transform="translate(13.367 5.012)" fill="#4f076b"/><rect id="Retângulo_58" data-name="Retângulo 58" width="2.506" height="23.392" transform="translate(16.709 5.012)" fill="#4f076b"/><rect id="Retângulo_59" data-name="Retângulo 59" width="1.671" height="23.392" transform="translate(20.885 5.012)" fill="#4f076b"/><rect id="Retângulo_60" data-name="Retângulo 60" width="1.67" height="27.569" transform="translate(24.227 5.012)" fill="#4f076b"/><rect id="Retângulo_61" data-name="Retângulo 61" width="2.506" height="23.392" transform="translate(31.745 5.012)" fill="#4f076b"/><rect id="Retângulo_62" data-name="Retângulo 62" width="1.67" height="23.392" transform="translate(36.758 5.012)" fill="#4f076b"/><rect id="Retângulo_63" data-name="Retângulo 63" width="2.506" height="23.392" transform="translate(40.1 5.012)" fill="#4f076b"/><rect id="Retângulo_64" data-name="Retângulo 64" width="1.67" height="27.569" transform="translate(44.277 5.012)" fill="#4f076b"/><rect id="Retângulo_65" data-name="Retângulo 65" width="1.671" height="2.506" transform="translate(7.519 30.074)" fill="#4f076b"/><rect id="Retângulo_66" data-name="Retângulo 66" width="1.671" height="2.506" transform="translate(10.86 30.074)" fill="#4f076b"/><rect id="Retângulo_67" data-name="Retângulo 67" width="1.671" height="2.506" transform="translate(14.202 30.074)" fill="#4f076b"/><rect id="Retângulo_68" data-name="Retângulo 68" width="1.671" height="2.506" transform="translate(17.543 30.074)" fill="#4f076b"/><rect id="Retângulo_69" data-name="Retângulo 69" width="1.671" height="2.506" transform="translate(20.885 30.074)" fill="#4f076b"/><rect id="Retângulo_70" data-name="Retângulo 70" width="1.67" height="2.506" transform="translate(27.569 30.074)" fill="#4f076b"/><rect id="Retângulo_71" data-name="Retângulo 71" width="1.671" height="2.506" transform="translate(30.91 30.074)" fill="#4f076b"/><rect id="Retângulo_72" data-name="Retângulo 72" width="1.671" height="2.506" transform="translate(34.252 30.074)" fill="#4f076b"/><rect id="Retângulo_73" data-name="Retângulo 73" width="1.671" height="2.506" transform="translate(37.593 30.074)" fill="#4f076b"/><rect id="Retângulo_74" data-name="Retângulo 74" width="1.671" height="2.506" transform="translate(40.935 30.074)" fill="#4f076b"/><rect id="Retângulo_75" data-name="Retângulo 75" width="1.671" height="23.392" transform="translate(28.404 5.012)" fill="#4f076b"/></g></svg>
										</div>
									</div>
									<div class="li-number-card">
										<div class="middle-number-card"></div>
									</div>
									<div class="li-amount-card">
										<div class="amount-card">R$ <?php echo esc_html( number_format($payment['amount'], 2, ',', '.') ) ?></div>
										<span>1X</span>
									</div>
								</div>
								
								<div class="li-form-payment-ticket-infos" style="display: block !important;">
									<a href="#scaner-modal" rel="modal:open" class="li-ticket-open" style="text-decoration: none;">
										<span class="li-ticket-ico">
											<div class="icon-copy" style="height: 20px;">
												<svg xmlns="http://www.w3.org/2000/svg" width="15.43" height="11.572" viewBox="0 0 15.43 11.572"><g id="scanner__barcode" transform="translate(0 -3)"><path id="Caminho_10509" data-name="Caminho 10509" d="M21.286,3h-.964a.321.321,0,0,0,0,.643h.964a.644.644,0,0,1,.643.643V5.25a.321.321,0,0,0,.643,0V4.286A1.287,1.287,0,0,0,21.286,3Z" transform="translate(-7.142)" fill="#fff"/><path id="Caminho_10510" data-name="Caminho 10510" d="M.321,5.572A.321.321,0,0,0,.643,5.25V4.286a.644.644,0,0,1,.643-.643H2.25A.321.321,0,1,0,2.25,3H1.286A1.287,1.287,0,0,0,0,4.286V5.25A.321.321,0,0,0,.321,5.572Z" fill="#fff"/><path id="Caminho_10511" data-name="Caminho 10511" d="M22.25,17a.321.321,0,0,0-.321.321v.964a.644.644,0,0,1-.643.643h-.964a.321.321,0,0,0,0,.643h.964a1.287,1.287,0,0,0,1.286-1.286v-.964A.321.321,0,0,0,22.25,17Z" transform="translate(-7.142 -4.999)" fill="#fff"/><path id="Caminho_10512" data-name="Caminho 10512" d="M2.25,18.929H1.286a.644.644,0,0,1-.643-.643v-.964a.321.321,0,0,0-.643,0v.964a1.287,1.287,0,0,0,1.286,1.286H2.25a.321.321,0,0,0,0-.643Z" transform="translate(0 -4.999)" fill="#fff"/><path id="Caminho_10513" data-name="Caminho 10513" d="M3.321,6A.321.321,0,0,0,3,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,3.321,6Z" transform="translate(-1.071 -1.071)" fill="#fff"/><path id="Caminho_10514" data-name="Caminho 10514" d="M13.321,6A.321.321,0,0,0,13,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,13.321,6Z" transform="translate(-4.642 -1.071)" fill="#fff"/><path id="Caminho_10515" data-name="Caminho 10515" d="M20.321,6A.321.321,0,0,0,20,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,20.321,6Z" transform="translate(-7.142 -1.071)" fill="#fff"/><path id="Caminho_10516" data-name="Caminho 10516" d="M6.321,6A.321.321,0,0,0,6,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,6.321,6Z" transform="translate(-2.143 -1.071)" fill="#fff"/><path id="Caminho_10517" data-name="Caminho 10517" d="M8.321,6A.321.321,0,0,0,8,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,8.321,6Z" transform="translate(-2.857 -1.071)" fill="#fff"/><path id="Caminho_10518" data-name="Caminho 10518" d="M10.321,6A.321.321,0,0,0,10,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,10.321,6Z" transform="translate(-3.571 -1.071)" fill="#fff"/><path id="Caminho_10519" data-name="Caminho 10519" d="M15.321,6A.321.321,0,0,0,15,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,15.321,6Z" transform="translate(-5.356 -1.071)" fill="#fff"/><path id="Caminho_10520" data-name="Caminho 10520" d="M18.321,6A.321.321,0,0,0,18,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,18.321,6Z" transform="translate(-6.428 -1.071)" fill="#fff"/><path id="Caminho_10521" data-name="Caminho 10521" d="M18.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,18.321,16Z" transform="translate(-6.428 -4.642)" fill="#fff"/><path id="Caminho_10522" data-name="Caminho 10522" d="M15.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,15.321,16Z" transform="translate(-5.356 -4.642)" fill="#fff"/><path id="Caminho_10523" data-name="Caminho 10523" d="M10.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,10.321,16Z" transform="translate(-3.571 -4.642)" fill="#fff"/><path id="Caminho_10524" data-name="Caminho 10524" d="M8.321,16A.321.321,0,0,0,8,16.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,8.321,16Z" transform="translate(-2.857 -4.642)" fill="#fff"/><path id="Caminho_10525" data-name="Caminho 10525" d="M6.321,16A.321.321,0,0,0,6,16.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,6.321,16Z" transform="translate(-2.143 -4.642)" fill="#fff"/><path id="Caminho_10526" data-name="Caminho 10526" d="M15.108,11H.321a.321.321,0,1,0,0,.643H15.108a.321.321,0,1,0,0-.643Z" transform="translate(0 -2.857)" fill="#fff"/><path id="Caminho_10527" data-name="Caminho 10527" d="M4.179,11.358a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,4.179,11.358Zm1.286,0a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,5.465,11.358Zm1.286,0a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,6.75,11.358Zm-4.5,2.572H1.286a.644.644,0,0,1-.643-.643v-.964a.321.321,0,1,0-.643,0v.964a1.287,1.287,0,0,0,1.286,1.286H2.25a.321.321,0,0,0,0-.643ZM.321,5.572A.321.321,0,0,0,.643,5.25V4.286a.644.644,0,0,1,.643-.643H2.25A.321.321,0,1,0,2.25,3H1.286A1.287,1.287,0,0,0,0,4.286V5.25A.321.321,0,0,0,.321,5.572ZM15.108,8.143H13.5V5.25a.321.321,0,0,0-.643,0V8.143h-.643V5.25a.321.321,0,1,0-.643,0V8.143H10.286V5.25a.321.321,0,1,0-.643,0V8.143H9V5.25a.321.321,0,1,0-.643,0V8.143H7.072V5.25a.321.321,0,0,0-.643,0V8.143H5.786V5.25a.321.321,0,0,0-.643,0V8.143H4.5V5.25a.321.321,0,1,0-.643,0V8.143H2.572V5.25a.321.321,0,0,0-.643,0V8.143H.321a.321.321,0,0,0,0,.643H1.929v3.536a.321.321,0,1,0,.643,0V8.786H3.857v1.607a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786H8.358v3.536a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786h1.286v1.607a.321.321,0,1,0,.643,0V8.786h.643v3.536a.321.321,0,1,0,.643,0V8.786h1.607a.321.321,0,0,0,0-.643ZM14.144,3H13.18a.321.321,0,1,0,0,.643h.964a.644.644,0,0,1,.643.643V5.25a.321.321,0,0,0,.643,0V4.286A1.287,1.287,0,0,0,14.144,3ZM9.965,11.358a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,9.965,11.358ZM15.108,12a.321.321,0,0,0-.321.321v.964a.644.644,0,0,1-.643.643H13.18a.321.321,0,0,0,0,.643h.964a1.287,1.287,0,0,0,1.286-1.286v-.964A.321.321,0,0,0,15.108,12Zm-3.215-.643a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,11.894,11.358Z" fill="#fff"/></g></svg>
											</div>
										</span>						
										<span><?php echo __( 'ESCANEAR', 'woocommerce' ) ?><span>
									</a>
									
									<div class="li-ticket-copy" onClick="return copyBarCodeTicket();">
										<span class="li-ticket-ico">
											<div class="icon-copy">
												<svg id="Grupo_5099" data-name="Grupo 5099" xmlns="http://www.w3.org/2000/svg" width="23" height="21" viewBox="0 0 23 21"><g id="Retângulo_769" data-name="Retângulo 769" transform="translate(5)" fill="#561271" stroke="#fff" stroke-width="2"><rect width="18" height="18" rx="3" stroke="none"/><rect x="1" y="1" width="16" height="16" rx="2" fill="none"/></g><g id="Retângulo_770" data-name="Retângulo 770" transform="translate(0 3)" fill="#561271" stroke="#fff" stroke-width="2"><rect width="18" height="18" rx="3" stroke="none"/><rect x="1" y="1" width="16" height="16" rx="2" fill="none"/></g></svg>					
											</div>
										</span>
										<span><?php echo __( 'COPIAR', 'woocommerce' ) ?><span>
									</div>
									<div id="ticket_bar_code" class="li-ticket-code"><?php echo esc_html( $payment['ticket_bar_code'] ) ?></div>

									<button type="button" onClick="return window.open('<?php echo esc_url( $payment['ticket_url'] ) ?>', 'ticket');" class="action primary checkout aqbank_place_order_button">
										<span><?php echo __( 'ABRIR BOLETO', 'woocommerce' ) ?></span>
									</button>
								</div>
								
								<div class="li-form-payment-ticket-infos" style="display: block !important;">
									<div class="li-form-payment-ticket-text">
										<span><?php echo __( 'Mediante o pagamento, a compensação do boleto pode levar  até 3 dias úteis.', 'woocommerce' ) ?></span>
									</div>
									<?php if($response['type'] == 'multi_ticket'): ?>
										<div class="li-form-payment-ticket-alert">
											<div class="ticket-alert-left ticket-alert-ico">
												<svg xmlns="http://www.w3.org/2000/svg" width="20" height="28" viewBox="0 0 20 28">
												  <g id="Grupo_6739" data-name="Grupo 6739" transform="translate(-429 -22)">
													<circle id="Elipse_125" data-name="Elipse 125" cx="10" cy="10" r="10" transform="translate(429 29)" fill="#d52c2c"/>
													<text id="_" data-name="!" transform="translate(436 45)" fill="#fff" font-size="21" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">!</tspan></text>
												  </g>
												</svg>
											</div>
											<div class="ticket-alert-right">
												<span class="ticket-alert-text">
													<span><?php echo __( 'Caso não consiga realizar o pagamento do boleto, o valor pago no cartão de credito será estornado.', 'woocommerce' ) ?></span>
												</span>
											</div>
										</div>
									<?php endif; ?>
								</div>
								<script>
									function copyBarCodeTicket() {
										navigator.clipboard.writeText("<?php echo esc_js( $payment['ticket_bar_code'] ) ?>");
										jQuery('#ticket_bar_code').css( "border", "1px solid #4fb843" );
										
										setTimeout(function(){
											jQuery('#ticket_bar_code').css( "border", "1px solid #e5e5e5" );
										}, 2000);
									}
								</script>
							<?php endif; ?>
							<?php if($payment['type'] == 'credit'): ?>
								
								<div class="li-form-payment <?php echo ($payment['status'] != 'succeeded' && $payment['status'] != 'pre_authorized') ? esc_attr( 'li-form-payment-fail' ) : esc_attr( '' ); ?>" style="margin-bottom: 10px;">
									<div class="li-flag-card">
										<div id="<?php echo esc_attr( $payment['id'] ) ?>" class="middle-number-card img-flag flag-<?php echo esc_attr( strtolower($payment['credit_card']['flag']) ) ?>"></div>
									</div>
									<div class="li-number-card">
										<span><?php echo __( 'FINAL', 'woocommerce' ) ?></span>
										<div id="one-middle-number-card" class="middle-number-card">
											<?php echo esc_html( $payment['credit_card']['last4_digits'] ) ?>
										</div>
									</div>
									<div class="li-amount-card">
										
										<div class="amount-card">
											R$ <?php echo esc_html( number_format( round(($payment['amount'] / $payment['installments']), 2), 2, ',', '.') ) ?>
										</div>	
										<span><?php echo esc_html( $payment['installments'] ) ?>x</span>
									</div>
									<small><?php echo ($payment['status'] != 'succeeded' && $payment['status'] != 'pre_authorized') ? '<b>' . esc_html( strtoupper( $context->trans_erros($payment['status']) ) ) . '</b> <span style="color:#000000;">- ' .  esc_html( $context->trans_erros($payment['message']) ) . '</span>'  : esc_html( '' ) ?></small>
								</div>
								
								<script>
									jQuery(document).ready(function($){
										$("#<?php echo esc_js( $payment['id'] ) ?>").html( AQPAGO.getFlagSvg("<?php echo esc_js( strtolower($payment['credit_card']['flag']) ) ?>"), true );
									});
								</script>
							<?php endif; ?>
							
						<?php endforeach; ?>
					<?php endif; ?>
					
				</div>
			</div>
			
		</div>
		
		<div style="clear:both;"></div>
		
		<div class="address">
		
			<?php if ($order->get_shipping_postcode() != ''): ?>

				<div class="title"><span>ENDEREÇO DE ENTREGA</span></div>

				<div class="resume-address">
					<div class="resume-address-ico">
						<div class="address-ico">
							<svg xmlns="http://www.w3.org/2000/svg" width="12.746" height="20.183" viewBox="0 0 12.746 20.183"><g id="Grupo_6409" data-name="Grupo 6409" transform="translate(-0.001 0.001)"><path id="Caminho_9514" data-name="Caminho 9514" d="M399.267,231.171c-.037.056-.073.112-.109.168,1.273.388,2.1,1.026,2.1,1.746,0,1.184-2.243,2.144-5.01,2.144s-5.01-.96-5.01-2.144c0-.713.813-1.344,2.064-1.734l-.111-.172c-2.185.331-3.412,1.6-3.308,2.879.126,1.544,2.285,3.007,6.365,3.007s6.211-1.361,6.365-3.007C402.736,232.777,401.721,231.63,399.267,231.171Z" transform="translate(-389.879 -216.883)" fill="#520a6d"/><path id="Caminho_9515" data-name="Caminho 9515" d="M396.251,233.915s5.8-5.78,5.8-11.214a5.806,5.806,0,1,0-11.611,0C390.445,228.33,396.251,233.915,396.251,233.915ZM391.639,222.7a4.612,4.612,0,1,1,4.612,4.622A4.618,4.618,0,0,1,391.639,222.7Z" transform="translate(-389.879 -216.883)" fill="#520a6d"/></g></svg>
						</div>
					</div>
					<div class="resume-address-text">
						<?php $complement = esc_html( get_post_meta($id, '_shipping_address_3', true) ); ?>
						<?php $district = esc_html( get_post_meta($id, '_shipping_address_4', true) ); ?>
						
						<span><?php echo esc_html( $order->get_shipping_address_1() . ', ' . $order->get_shipping_address_2() . ' ' . $complement ) ?></span><br/>
						<span><?php echo esc_html( $district . ' - ' . $order->get_shipping_city() . ' / ' . $order->get_shipping_state() ) ?></span><br/>
						<strong><?php echo esc_html( $order->get_shipping_postcode() ) ?></strong>
					</div>
				</div>
			<?php endif; ?>
			
			<div class="process-payment" style="clear:both;">
				<div class="process-text">
					<span>PAGAMENTO PROCESSADO PELA EMPRESA</span>
				</div>
				
				<div class="process-logo">
					<div class="process-img">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="219.387" height="74.511" viewBox="0 0 219.387 74.511">
						  <defs>
							<filter id="Caminho_3057" x="54.452" y="13.864" width="48.735" height="48.822" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3058" x="77.865" y="14.313" width="52.135" height="52.354" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-2"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-2"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3059" x="95.837" y="14.747" width="52.338" height="52.121" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-3"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-3"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3060" x="125.319" y="15.102" width="48.738" height="48.825" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-4"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-4"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3061" x="146.886" y="15.426" width="52.398" height="52.463" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-5"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-5"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3062" x="173.176" y="15.879" width="46.21" height="46.21" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-6"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-6"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3054" x="23.44" y="27.425" width="47.138" height="47.086" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-7"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-7"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3055" x="22.729" y="0" width="46.943" height="46.997" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-8"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-8"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Caminho_3056" x="0" y="12.431" width="47.584" height="47.586" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-9"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-9"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
							<filter id="Elipse_7" x="17.087" y="17.191" width="39.976" height="39.976" filterUnits="userSpaceOnUse">
							  <feOffset dy="3" input="SourceAlpha"/>
							  <feGaussianBlur stdDeviation="3" result="blur-10"/>
							  <feFlood flood-opacity="0.161"/>
							  <feComposite operator="in" in2="blur-10"/>
							  <feComposite in="SourceGraphic"/>
							</filter>
						  </defs>
						  <g id="Grupo_2580" data-name="Grupo 2580" transform="translate(-689 -163.468)">
							<g id="Grupo_2578" data-name="Grupo 2578">
							  <g transform="matrix(1, 0, 0, 1, 689, 163.47)" filter="url(#Caminho_3057)">
								<path id="Caminho_3057-2" data-name="Caminho 3057" d="M23.1,9a1.44,1.44,0,0,1,.014,2.038,1.355,1.355,0,0,1-2.067.014L19.979,9.983c-.014.375-.042.791-.1,1.234s-.124.819-.194,1.137a9.994,9.994,0,0,1-.956,2.538A10,10,0,1,1,10.024,0a9.671,9.671,0,0,1,7.055,2.926L23.151,9Zm-5.99,1A6.812,6.812,0,0,0,15.041,4.99,7.011,7.011,0,0,0,9.994,2.883,6.875,6.875,0,0,0,4.979,4.99,6.785,6.785,0,0,0,2.886,10a6.953,6.953,0,0,0,2.093,5.032,7.1,7.1,0,0,0,10.038,0A6.8,6.8,0,0,0,17.108,10Z" transform="translate(77.84 19.86) rotate(46)" fill="#0d0d0d"/>
							  </g>
							  <g transform="matrix(1, 0, 0, 1, 689, 163.47)" filter="url(#Caminho_3058)">
								<path id="Caminho_3058-2" data-name="Caminho 3058" d="M2.823,2.9A9.716,9.716,0,0,1,6.041.708,10.032,10.032,0,0,1,9.857,0a9.547,9.547,0,0,1,7.016,2.912L28.228,14.269a1.783,1.783,0,0,1,.291.375.974.974,0,0,1,.124.375,1.726,1.726,0,0,1-.4,1.262,2.137,2.137,0,0,1-1.428.43.463.463,0,0,1-.25-.083,2.986,2.986,0,0,1-.347-.319L19.8,9.889a10.957,10.957,0,0,1-.054,1.192,9.783,9.783,0,0,1-.208,1.179,11.427,11.427,0,0,1-1,2.5,9.328,9.328,0,0,1-1.608,2.135,9.463,9.463,0,0,1-3.191,2.162,10.88,10.88,0,0,1-3.84.734A9.893,9.893,0,0,1,0,9.888a10.422,10.422,0,0,1,.734-3.84,9.7,9.7,0,0,1,2.178-3.2ZM14.9,14.836a7.136,7.136,0,0,0,1.553-2.274,6.641,6.641,0,0,0,.54-2.7,6.765,6.765,0,0,0-2.066-4.95A6.856,6.856,0,0,0,9.95,2.816,7.077,7.077,0,0,0,2.895,9.871a6.853,6.853,0,0,0,2.093,4.973,7,7,0,0,0,9.946-.014Z" transform="translate(101.1 20.31) rotate(46)" fill="#0d0d0d"/>
							  </g>
							  <g transform="matrix(1, 0, 0, 1, 689, 163.47)" filter="url(#Caminho_3059)">
								<path id="Caminho_3059-2" data-name="Caminho 3059" d="M2.816,2.869A9.535,9.535,0,0,1,6.074.693a10.765,10.765,0,0,1,7.6,0,9.986,9.986,0,0,1,3.258,16.236,9.62,9.62,0,0,1-4.673,2.647c-.43.1-.832.165-1.179.208a8.329,8.329,0,0,1-1.206.042l6.28,6.28a1.462,1.462,0,0,1,.463,1.011,1.492,1.492,0,0,1-1.51,1.484,1.577,1.577,0,0,1-.541-.124,1.538,1.538,0,0,1-.463-.32L2.859,16.914A9.528,9.528,0,0,1,.7,13.669,9.934,9.934,0,0,1,0,9.871,10.47,10.47,0,0,1,.749,6.045,9.65,9.65,0,0,1,2.913,2.8ZM14.934,14.821a7.028,7.028,0,0,0,0-9.955A6.868,6.868,0,0,0,9.96,2.8a7.065,7.065,0,1,0,4.994,12.061Z" transform="translate(125.41 20.75) rotate(46)" fill="#0d0d0d"/>
							  </g>
							  <g transform="matrix(1, 0, 0, 1, 689, 163.47)" filter="url(#Caminho_3060)">
								<path id="Caminho_3060-2" data-name="Caminho 3060" d="M23.1,9a1.438,1.438,0,0,1,.013,2.038,1.59,1.59,0,0,1-1.041.486,1.573,1.573,0,0,1-1.026-.473L19.979,9.983c-.014.375-.042.79-.1,1.234s-.124.818-.194,1.137a9.963,9.963,0,0,1-.956,2.538A10,10,0,1,1,10.025,0,9.672,9.672,0,0,1,17.08,2.926L23.152,9ZM17.11,10a6.812,6.812,0,0,0-2.066-5.006A7.012,7.012,0,0,0,10,2.884,6.86,6.86,0,0,0,4.979,4.991,6.774,6.774,0,0,0,2.886,10a6.945,6.945,0,0,0,2.093,5.032A7.112,7.112,0,0,0,17.113,10Z" transform="translate(148.71 21.1) rotate(46)" fill="#0d0d0d"/>
							  </g>
							  <g transform="matrix(1, 0, 0, 1, 689, 163.47)" filter="url(#Caminho_3061)">
								<path id="Caminho_3061-2" data-name="Caminho 3061" d="M23.47,9.255a7.566,7.566,0,0,1,2.107,6.521,11.164,11.164,0,0,1-3.4,6.837,1.318,1.318,0,0,1-1.026.415,1.388,1.388,0,0,1-1.027-.415,1.44,1.44,0,0,1-.443-1.054,1.32,1.32,0,0,1,.415-1.027A9.625,9.625,0,0,0,21.846,18.2a7.524,7.524,0,0,0,.8-2.658A4.857,4.857,0,0,0,21.394,11.3L20.035,9.944a9.4,9.4,0,0,1-.083,1.247q-.084.625-.207,1.206a9.937,9.937,0,0,1-2.676,4.728,9.764,9.764,0,0,1-7.1,2.967A9.87,9.87,0,0,1,0,10.123a9.7,9.7,0,0,1,2.967-7.1A10.148,10.148,0,0,1,6.253.766,8.944,8.944,0,0,1,10.122,0l1.966.116.873.263.9.347,1,.415.652.375q.435.347.832.694c.264.231.527.471.79.734l6.3,6.3Zm-8.485,5.8A6.974,6.974,0,0,0,17.12,10,6.734,6.734,0,0,0,15.082,5,7.2,7.2,0,0,0,2.894,10.108a7.335,7.335,0,0,0,.5,2.746,7.082,7.082,0,0,0,3.8,3.8,7.335,7.335,0,0,0,2.746.5,6.877,6.877,0,0,0,5.032-2.121Z" transform="translate(172.45 21.43) rotate(46)" fill="#0d0d0d"/>
							  </g>
							  <g transform="matrix(1, 0, 0, 1, 689, 163.47)" filter="url(#Caminho_3062)">
								<path id="Caminho_3062-2" data-name="Caminho 3062" d="M9.969,0A9.975,9.975,0,1,1,0,9.982,9.975,9.975,0,0,1,9.969,0Zm5.018,14.973A7.057,7.057,0,1,0,10,17.08,6.824,6.824,0,0,0,15,14.987Z" transform="translate(196.53 21.88) rotate(46)" fill="#0d0d0d"/>
							  </g>
							</g>
							<g id="Grupo_2579" data-name="Grupo 2579" transform="translate(-34.936 -28.535)">
							  <g id="Grupo_2573" data-name="Grupo 2573">
								<g transform="matrix(1, 0, 0, 1, 723.94, 192)" filter="url(#Caminho_3054)">
								  <path id="Caminho_3054-2" data-name="Caminho 3054" d="M13.448,8.675A14.759,14.759,0,0,1,0,0L4.723,17.623a2.558,2.558,0,0,0,4.312,1.137c.843-.81,6.49-6.451,12.607-12.567a14.688,14.688,0,0,1-8.193,2.483Z" transform="translate(32.44 48.46) rotate(-44)" fill="#642266" fill-rule="evenodd"/>
								</g>
								<g transform="matrix(1, 0, 0, 1, 723.94, 192)" filter="url(#Caminho_3055)">
								  <path id="Caminho_3055-2" data-name="Caminho 3055" d="M8.559,13.4a14.688,14.688,0,0,1-2.475,8.184c6.165-6.17,11.828-11.848,12.537-12.572a2.565,2.565,0,0,0-1.157-4.324C16.123,4.322,8.341,2.236,0,0A14.758,14.758,0,0,1,8.559,13.4Z" transform="translate(31.73 19.47) rotate(-44)" fill="#642266" fill-rule="evenodd"/>
								</g>
								<g transform="matrix(1, 0, 0, 1, 723.94, 192)" filter="url(#Caminho_3056)">
								  <path id="Caminho_3056-2" data-name="Caminho 3056" d="M.1,3.291,4.844,20.974C4.8,20.491,4.771,20,4.771,19.5A14.757,14.757,0,0,1,19.523,4.748c.455,0,.9.022,1.349.062C12.2,2.488,4.16.333,3.343.118A2.573,2.573,0,0,0,.1,3.291Z" transform="translate(9 32.93) rotate(-44)" fill="#642266" fill-rule="evenodd"/>
								</g>
								<g transform="matrix(1, 0, 0, 1, 723.94, 192)" filter="url(#Elipse_7)">
								  <circle id="Elipse_7-2" data-name="Elipse 7" cx="7.771" cy="7.771" r="7.771" transform="translate(26.09 33.99) rotate(-44)" fill="#45b764"/>
								</g>
							  </g>
							</g>
						  </g>
						</svg>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="margin-bottom: 20px;"></div>
<style>
.woocommerce-customer-details .woocommerce-columns--addresses {
	display: none !important;
}
</style>
