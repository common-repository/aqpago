<div id="scaner-modal" class="modal modal-ticket">
	<img src="data:image/png;base64,<?php echo esc_attr( $bar_code_img ) ?>" style="height: 81px;width: 100%;max-width: 656px;margin: auto;">
</div>

<section class="woocommerce-order-details order-aqpago">
	
	<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Vencimento:', 'woocommerce' ) ?> <strong><?php echo esc_html( date("d/m/Y", strtotime($payment['expiration_date'])) ) ?></strong>
		</li>
		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Valor:', 'woocommerce' ) ?> <strong>R$ <?php echo esc_html( number_format($payment['amount'], 2, ',', '.') ) ?></strong>
		</li>
		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Código de barras:', 'woocommerce' ) ?> 
			<strong>
				<textarea id="ticket_bar_code" disabled ><?php echo esc_html( $payment['ticket_bar_code'] ) ?></textarea>
				<button class="button-copy" onClick="return copyBarCodeTicket();" style="background: rgb(86, 18, 113);">
					<svg id="Grupo_5099" data-name="Grupo 5099" xmlns="http://www.w3.org/2000/svg" width="23" height="21" viewBox="0 0 23 21"><g id="Retângulo_769" data-name="Retângulo 769" transform="translate(5)" fill="#561271" stroke="#fff" stroke-width="2"><rect width="18" height="18" rx="3" stroke="none"/><rect x="1" y="1" width="16" height="16" rx="2" fill="none"/></g><g id="Retângulo_770" data-name="Retângulo 770" transform="translate(0 3)" fill="#561271" stroke="#fff" stroke-width="2"><rect width="18" height="18" rx="3" stroke="none"/><rect x="1" y="1" width="16" height="16" rx="2" fill="none"/></g></svg>
					<span>Copiar</span>
				</button>
			</strong> 
		</li>	
		
		<li class="woocommerce-order-overview__order order ticket-scaner-link">
			
			<?php echo __( 'Ler código de barras:', 'woocommerce' ) ?> 
			<strong style="margin-top: 20px;">
				<a href="#scaner-modal" rel="modal:open" class="button-copy" style="background: rgb(86, 18, 113);text-decoration:none;">
					<svg xmlns="http://www.w3.org/2000/svg" width="15.43" height="11.572" viewBox="0 0 15.43 11.572"><g id="scanner__barcode" transform="translate(0 -3)"><path id="Caminho_10509" data-name="Caminho 10509" d="M21.286,3h-.964a.321.321,0,0,0,0,.643h.964a.644.644,0,0,1,.643.643V5.25a.321.321,0,0,0,.643,0V4.286A1.287,1.287,0,0,0,21.286,3Z" transform="translate(-7.142)" fill="#fff"/><path id="Caminho_10510" data-name="Caminho 10510" d="M.321,5.572A.321.321,0,0,0,.643,5.25V4.286a.644.644,0,0,1,.643-.643H2.25A.321.321,0,1,0,2.25,3H1.286A1.287,1.287,0,0,0,0,4.286V5.25A.321.321,0,0,0,.321,5.572Z" fill="#fff"/><path id="Caminho_10511" data-name="Caminho 10511" d="M22.25,17a.321.321,0,0,0-.321.321v.964a.644.644,0,0,1-.643.643h-.964a.321.321,0,0,0,0,.643h.964a1.287,1.287,0,0,0,1.286-1.286v-.964A.321.321,0,0,0,22.25,17Z" transform="translate(-7.142 -4.999)" fill="#fff"/><path id="Caminho_10512" data-name="Caminho 10512" d="M2.25,18.929H1.286a.644.644,0,0,1-.643-.643v-.964a.321.321,0,0,0-.643,0v.964a1.287,1.287,0,0,0,1.286,1.286H2.25a.321.321,0,0,0,0-.643Z" transform="translate(0 -4.999)" fill="#fff"/><path id="Caminho_10513" data-name="Caminho 10513" d="M3.321,6A.321.321,0,0,0,3,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,3.321,6Z" transform="translate(-1.071 -1.071)" fill="#fff"/><path id="Caminho_10514" data-name="Caminho 10514" d="M13.321,6A.321.321,0,0,0,13,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,13.321,6Z" transform="translate(-4.642 -1.071)" fill="#fff"/><path id="Caminho_10515" data-name="Caminho 10515" d="M20.321,6A.321.321,0,0,0,20,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,20.321,6Z" transform="translate(-7.142 -1.071)" fill="#fff"/><path id="Caminho_10516" data-name="Caminho 10516" d="M6.321,6A.321.321,0,0,0,6,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,6.321,6Z" transform="translate(-2.143 -1.071)" fill="#fff"/><path id="Caminho_10517" data-name="Caminho 10517" d="M8.321,6A.321.321,0,0,0,8,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,8.321,6Z" transform="translate(-2.857 -1.071)" fill="#fff"/><path id="Caminho_10518" data-name="Caminho 10518" d="M10.321,6A.321.321,0,0,0,10,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,10.321,6Z" transform="translate(-3.571 -1.071)" fill="#fff"/><path id="Caminho_10519" data-name="Caminho 10519" d="M15.321,6A.321.321,0,0,0,15,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,15.321,6Z" transform="translate(-5.356 -1.071)" fill="#fff"/><path id="Caminho_10520" data-name="Caminho 10520" d="M18.321,6A.321.321,0,0,0,18,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,18.321,6Z" transform="translate(-6.428 -1.071)" fill="#fff"/><path id="Caminho_10521" data-name="Caminho 10521" d="M18.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,18.321,16Z" transform="translate(-6.428 -4.642)" fill="#fff"/><path id="Caminho_10522" data-name="Caminho 10522" d="M15.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,15.321,16Z" transform="translate(-5.356 -4.642)" fill="#fff"/><path id="Caminho_10523" data-name="Caminho 10523" d="M10.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,10.321,16Z" transform="translate(-3.571 -4.642)" fill="#fff"/><path id="Caminho_10524" data-name="Caminho 10524" d="M8.321,16A.321.321,0,0,0,8,16.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,8.321,16Z" transform="translate(-2.857 -4.642)" fill="#fff"/><path id="Caminho_10525" data-name="Caminho 10525" d="M6.321,16A.321.321,0,0,0,6,16.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,6.321,16Z" transform="translate(-2.143 -4.642)" fill="#fff"/><path id="Caminho_10526" data-name="Caminho 10526" d="M15.108,11H.321a.321.321,0,1,0,0,.643H15.108a.321.321,0,1,0,0-.643Z" transform="translate(0 -2.857)" fill="#fff"/><path id="Caminho_10527" data-name="Caminho 10527" d="M4.179,11.358a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,4.179,11.358Zm1.286,0a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,5.465,11.358Zm1.286,0a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,6.75,11.358Zm-4.5,2.572H1.286a.644.644,0,0,1-.643-.643v-.964a.321.321,0,1,0-.643,0v.964a1.287,1.287,0,0,0,1.286,1.286H2.25a.321.321,0,0,0,0-.643ZM.321,5.572A.321.321,0,0,0,.643,5.25V4.286a.644.644,0,0,1,.643-.643H2.25A.321.321,0,1,0,2.25,3H1.286A1.287,1.287,0,0,0,0,4.286V5.25A.321.321,0,0,0,.321,5.572ZM15.108,8.143H13.5V5.25a.321.321,0,0,0-.643,0V8.143h-.643V5.25a.321.321,0,1,0-.643,0V8.143H10.286V5.25a.321.321,0,1,0-.643,0V8.143H9V5.25a.321.321,0,1,0-.643,0V8.143H7.072V5.25a.321.321,0,0,0-.643,0V8.143H5.786V5.25a.321.321,0,0,0-.643,0V8.143H4.5V5.25a.321.321,0,1,0-.643,0V8.143H2.572V5.25a.321.321,0,0,0-.643,0V8.143H.321a.321.321,0,0,0,0,.643H1.929v3.536a.321.321,0,1,0,.643,0V8.786H3.857v1.607a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786H8.358v3.536a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786h1.286v1.607a.321.321,0,1,0,.643,0V8.786h.643v3.536a.321.321,0,1,0,.643,0V8.786h1.607a.321.321,0,0,0,0-.643ZM14.144,3H13.18a.321.321,0,1,0,0,.643h.964a.644.644,0,0,1,.643.643V5.25a.321.321,0,0,0,.643,0V4.286A1.287,1.287,0,0,0,14.144,3ZM9.965,11.358a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,9.965,11.358ZM15.108,12a.321.321,0,0,0-.321.321v.964a.644.644,0,0,1-.643.643H13.18a.321.321,0,0,0,0,.643h.964a1.287,1.287,0,0,0,1.286-1.286v-.964A.321.321,0,0,0,15.108,12Zm-3.215-.643a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,11.894,11.358Z" fill="#fff"/></g></svg>
					<span>Escanear</span>
				</a>
			</strong>
			
		</li>	
		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Link do boleto:', 'woocommerce' ) ?> <strong><a href="<?php echo esc_url( $payment['ticket_url'] ) ?>" target="_blank"><?php echo __( 'Abrir boleto', 'woocommerce' ) ?></a></strong>
		</li>		
		<li class="woocommerce-order-overview__order order">
			<?php echo __( 'Atenção:', 'woocommerce' ) ?> <strong><?php echo __( 'A compensação do boleto pode levar até 2 dias úteis.', 'woocommerce' ) ?></strong>
		</li>
	</ul>

</section>

<script>
function copyBarCodeTicket() {
	var copyText = document.getElementById("ticket_bar_code");
	copyText.select();
	copyText.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(copyText.value);
	
	jQuery('#ticket_bar_code').val( copyText.value + ' -> Copiado!' );
	jQuery('#ticket_bar_code').css( 'border', '1px solid #4fb843' );
	
	setTimeout(function(){
		jQuery('#ticket_bar_code').val( copyText.value.replace(' -> Copiado!', '') ).change();
		jQuery('#ticket_bar_code').css( 'border', 'none' );
	}, 2000);
}
</script>
