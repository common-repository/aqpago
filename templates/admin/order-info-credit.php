<div class="box-info-aqpago" style="border-bottom: 1px solid #eee;padding-bottom: 20px;margin-bottom: 10px;">
<?php if(isset($payment)): ?>
    <table>
		<?php if($payment['type'] == 'credit'): ?>
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'ID do pagamento: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['id'] ) ?> </td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Realizado: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( date("d/m/Y", strtotime($payment['created_at'])) . ' às ' . date("H:i", strtotime($payment['created_at'])) ) ?> </td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Valor total: ', 'woocommerce' ) ?></b></td><td> R$ <?php echo esc_html( number_format($payment['amount'], 2, ',', '.') ) ?> </td>
			</tr> 			
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Parcelas: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['installments'] ) ?>x </td>
			</tr> 
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Cartão: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['credit_card']['first4_digits'] ) . ' **** **** ' . esc_html( $payment['credit_card']['last4_digits'] ) ?> </td>
			</tr> 			
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Bandeira: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['credit_card']['flag'] ) ?> </td>
			</tr> 			
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Status: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['status'] ) ?> </td>
			</tr> 			
			<tr>
				<td style="width: 150px;height: 30px;"><b><?php echo __( 'Mensagem: ', 'woocommerce' ) ?></b></td><td> <?php echo esc_html( $payment['message'] ) ?> </td>
			</tr> 			
		<?php endif; ?>		
    </table>
<?php endif; ?>

</div>