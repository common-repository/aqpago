<section class="woocommerce-order-details order-aqpago">
	
	<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Valor:', 'woocommerce' ) ?> <strong>R$ <?php echo esc_html( number_format($payment['amount'], 2, ',', '.') ) ?></strong>
		</li>
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Cartão:', 'woocommerce' ) ?> <strong><?php echo esc_html( $payment['credit_card']['first4_digits'] ) . ' **** **** ' . esc_html( $payment['credit_card']['last4_digits'] ) ?></strong>
		</li>
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Proprietário:', 'woocommerce' ) ?> <strong><?php echo esc_html( $payment['credit_card']['holder_name'] ) ?></strong>
		</li>
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Bandeira:', 'woocommerce' ) ?> <strong><?php echo esc_html( $payment['credit_card']['flag'] ) ?></strong>
		</li>		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Status:', 'woocommerce' ) ?> <strong><?php echo esc_html( $payment['status'] ) ?></strong>
		</li>
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Mensagem:', 'woocommerce' ) ?> <strong><?php echo esc_html( $payment['message'] ) ?></strong>
		</li>		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Realizado:', 'woocommerce' ) ?> <strong><?php echo esc_html( date("d/m/Y", strtotime($payment['created_at'])) . ' às ' . date("H:i", strtotime($payment['created_at'])) ) ?></strong>
		</li>
		
		<?php if($type == 'multi_ticket'): ?>
			<li class="woocommerce-order-overview__order order">
				<?php echo __( 'Atenção:', 'woocommerce' ) ?> <strong><?php echo __( 'Caso não consiga realizar o pagamento do boleto, o valor pago no cartão de credito será estornado.', 'woocommerce' ) ?></strong>
			</li>
		<?php endif; ?>
	</ul>
	
</section>

