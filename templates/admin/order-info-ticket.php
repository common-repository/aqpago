<div class="box-info-aqpago" style="border-bottom: 1px solid #eee;padding-bottom: 20px;margin-bottom: 10px;">
<?php if(isset($payment)): ?>
    <table>
		<?php if($payment['type'] == 'ticket'): ?>
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'ID do pagamento: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['id'] ) ?> </td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Valor total: ', 'woocommerce' ) ?></b></td><td> R$ <?php echo esc_html( number_format($payment['amount'], 2, ',', '.') ) ?> </td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Código de barras: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['ticket_bar_code'] ) ?></td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Status: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $context->getPaymentStatus( $payment['status'] ) ) ?></td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Vencimento: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( date("d/m/Y", strtotime($payment['expiration_date']) ) ) ?> </td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b>link do boleto</b></td><td><a href='<?php echo esc_url( $payment['ticket_url'] ) ?>' target='_blank' ><?php echo __( 'Abrir boleto', 'woocommerce' ) ?></a></td>
			</tr>  
		<?php endif; ?>
    </table>
<?php endif; ?>

</div>