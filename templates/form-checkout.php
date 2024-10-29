<style>
.payment_box.payment_method_aqpago {
	background: <?php echo esc_attr($backgroundColor)  ?> !important;
}
.payment_box.payment_method_aqpago:before {
    border-bottom-color: <?php echo esc_attr($backgroundColor)  ?> !important;
	background: <?php echo esc_attr($backgroundColor)  ?> !important';
}
.payment_method_aqpago label img {
	display: none !important;
}
.payment_method_aqpago {
	padding: 0px !important;
}
</style>


<script>
var	amount_total = '<?php echo esc_js($cart_total); ?>';
var totalInstallmentMax = '<?php echo esc_js($installments) ;?>';
var flagVisa = '<img class="visa-flag" style="width:80%;" src="<?php echo esc_url( $flagVisa ) ?>" />';
var	totalSavedCards = <?php echo esc_js($totalSavedCards); ?>;
var updateMulti= false;

jQuery(document).ready(function(){
	setTimeout(() => {
		if (window.AQPAGOSECTION.getSessionID() == null) {
			console.log('reload session...');
			window.AQPAGOSECTION.setPublicToken('<?php echo esc_js($public_token) ?>');
		}
	}, 8000);

	window.jQuery("#aqpago_cc_cid").focusin(function(){
		AQPAGO.onCodehasFocus();
	});
	window.jQuery("#aqpago_cc_cid").focusout(function(){
		AQPAGO.onCodeFocusOut();
	});
	
	window.jQuery( "#aqpago_cc_cid" ).keyup(function() {
		window.jQuery('#card-code').val( jQuery(this).val() );
	});	
	window.jQuery( "#billing_email" ).keyup(function() {
		window.jQuery('.email-text').html( jQuery(this).val() );
	});
	window.jQuery( "#billing_phone" ).keyup(function() {
		window.jQuery('.phone-text').html( jQuery(this).val() );
	});
	
	window.jQuery('.description-installment').html(
		installMap[1].option + ' de ' + AQPAGO.formatPriceWithCurrency(installMap[1].price) + ' ' + installMap[1].fees
	); 
	
	jQuery("#aqpago_installments option").each(function() {
		jQuery(this).remove();
	});
	
	Object.entries(installMap).forEach(([install, data]) => {
		jQuery('#aqpago_installments').append(jQuery('<option>', {
			value: install,
			text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
		}));
		
		jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
	});
	
	window.jQuery("#aqpago_one_installments").on('change', function() {
		if (type_payment == 'credit') {
			var vlOneFee = ((amount_total / (100 - installMap[jQuery(this).val()].tax)) * 100);
		} else {
			var vlOneFee = ((amount_one / (100 - installMap[jQuery(this).val()].tax)) * 100);
		}
		
		var vlPcFee = vlOneFee / jQuery(this).val();
		
		if (card_one) {
			cards[card_one].installment = jQuery(this).val();
		}
		
		jQuery("#aqpago_one_cc_card_installments").val(jQuery(this).val()).change();
		jQuery("#aqpago_installments_oneCard").val(jQuery(this).val()).change();
		jQuery('#one-card-bottom strong').html(jQuery(this).val() + 'x');
		jQuery('#one-card-bottom span').html( AQPAGO.formatPriceWithCurrency(vlPcFee) );
	});	
	
	window.jQuery("#aqpago_cc_multiple_val_oneCard").on('change', function() {		
		var valuePrice = jQuery(this).val().replace('.', '');
		valuePrice = valuePrice.replace(',', '.');
		var instOne = jQuery("#aqpago_installments_oneCard").val();
		
		jQuery("#aqpago_installments_oneCard option").each(function() {
			jQuery(this).remove();
		});
		
		Object.entries(installMap).forEach(([install, data]) => {
			var vlFee1 = ((valuePrice / (100 - data.tax)) * 100);
			var vlPc1 = vlFee1 / install;
			
			
			jQuery('#aqpago_installments_oneCard').append(jQuery('<option>', {
				value: install,
				text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
			}));
			
			if (instOne == install) {
				jQuery('.description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees);				
			}
		});
		
		jQuery("#aqpago_installments_oneCard").val(instOne).change();
		
	});
	
	window.jQuery("#aqpago_cc_multiple_val_twoCard").on('change', function() {		
		var valuePrice = jQuery(this).val().replace('.', '');
		valuePrice = valuePrice.replace(',', '.');
		var instTwo = jQuery("#aqpago_installments_twoCard").val();
		
		jQuery("#aqpago_installments_twoCard option").each(function() {
			jQuery(this).remove();
		});
		
		Object.entries(installMap).forEach(([install, data]) => {
			var vlFee2 = ((valuePrice / (100 - data.tax)) * 100);
			var vlPc2 = vlFee2 / install;
			
			jQuery('#aqpago_installments_twoCard').append(jQuery('<option>', {
				value: install,
				text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
			}));
			
			if (instTwo == install) {
				jQuery('.description-installment-2').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);				
			}
		});
		
		jQuery("#aqpago_installments_twoCard").val(instTwo).change();
	});
	
	window.jQuery("#aqpago_two_installments").on('change', function() {	
		if (type_payment == 'credit') {
			var vlTowFee = ((amount_total / (100 - installMap[jQuery(this).val()].tax)) * 100);
		} else {
			var vlTowFee = ((amount_two / (100 - installMap[jQuery(this).val()].tax)) * 100);
		}
		
		var vlPcFee = vlTowFee / jQuery(this).val();
		
		if (card_two) {
			cards[card_two].installment = jQuery(this).val();
		}
		
		jQuery("#aqpago_two_cc_card_installments").val(jQuery(this).val()).change();
		jQuery("#aqpago_installments_twoCard").val(jQuery(this).val()).change();
		jQuery('#two-card-bottom strong').html(jQuery(this).val() + 'x');
		jQuery('#two-card-bottom span').html( AQPAGO.formatPriceWithCurrency(vlPcFee) );
	});
	
	window.jQuery("#aqpago_cc_multiple_val").on('change', function() {
		var valuePrice = jQuery(this).val().replace('.', '');
		valuePrice = valuePrice.replace(',', '.');
		
		var optInst = jQuery("#aqpago_installments").val();

		jQuery("#aqpago_installments option").each(function() {
			jQuery(this).remove();
		});
		
		jQuery('.installment-view').html('');
		
		Object.entries(installMap).forEach(([install, data]) => {
			var vlFee = ((valuePrice / (100 - data.tax)) * 100);
			var vlPc = vlFee / install;
			
			jQuery('#aqpago_installments').append(jQuery('<option>', {
				value: install,
				text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc) + ' ' + data.fees
			}));
			
			jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(vlPc) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(vlFee) + '</b></p>');
		
			if (jQuery('#aqpago_installments').val() == install) {
				jQuery('.description-installment').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc) + ' ' + data.fees);
			}
		});
		
		jQuery('#aqpago_installments').val(optInst).change();
	});
	
	window.jQuery( "#aqpago_installments" ).on('change', function() {
		var instSel = jQuery(this).val();
		
		if (type_payment == 'credit') {
			jQuery('.description-installment').html(
				installMap[instSel].option + ' de ' + AQPAGO.formatPriceWithCurrency(installMap[instSel].price) + ' ' + installMap[instSel].fees
			); 
			
			jQuery('.aqbank-card-grand-total').html(
				AQPAGO.formatPriceWithCurrency(installMap[instSel].total)
			);
		
		} else {
			jQuery('.description-installment').html(
				jQuery("#aqpago_installments option:selected").text()
			); 
		}
	});

	window.jQuery( "#aqpago_installments_oneCard" ).on('change', function() {
		var instSel = jQuery(this).val();
		
		if (type_payment == 'credit') {
			jQuery('.description-installment-1').html(
				installMap[instSel].option + ' de ' + AQPAGO.formatPriceWithCurrency(installMap[instSel].price) + ' ' + installMap[instSel].fees
			); 
			
			jQuery('.aqbank-card-grand-total').html(
				AQPAGO.formatPriceWithCurrency(installMap[instSel].total)
			);
		
		} else {
			jQuery('.description-installment-1').html(
				jQuery("#aqpago_installments_oneCard option:selected").text()
			); 
		}
	});
	
	window.jQuery( "#aqpago_installments_twoCard" ).on('change', function() {
		var instSel = jQuery(this).val();
		
		if (type_payment == 'credit') {
			jQuery('.description-installment-2').html(
				installMap[instSel].option + ' de ' + AQPAGO.formatPriceWithCurrency(installMap[instSel].price) + ' ' + installMap[instSel].fees
			); 
			
			jQuery('.aqbank-card-grand-total').html(
				AQPAGO.formatPriceWithCurrency(installMap[instSel].total)
			);
		
		} else {
			jQuery('.description-installment-2').html(
				jQuery("#aqpago_installments_twoCard option:selected").text()
			); 
		}
	});
	
	AQPAGO.savedCardsDetails();
	
	window.jQuery('.aqbank-card-grand-total').html( AQPAGO.formatPriceWithCurrency( amount_total ) );
	window.jQuery('#aqpago_cc_number').mask('0000 0000 0000 0000000');
	window.jQuery('#aqpago_cc_cid').mask('0000');
	window.jQuery('#aqpago_documento').mask('000.000.000-00');
	window.jQuery('#woocommerce_aqpago_min_total_installments').mask('000.000.000,00', {reverse: true});
	window.jQuery('#aqpago_cc_multiple_val').mask('000.000.000,00', {reverse: true});
	window.jQuery('.aqbank-input-valor').mask('000.000.000,00', {reverse: true});

	jQuery('#aqpago_cc_owner').on('keyup keypress blur change', function(){
		jQuery('.card-name').html(jQuery(this).val());
	});
	
	jQuery('#aqpago_cc_number').on('keyup keypress blur change', function(){
		value = jQuery(this).val().replace(/\s+/g, '');
		var result;
		var valCard;
		
		var Bandeira	= AQPAGO.setPaymentFlag(value);
		var Maxkey		= 19; 
		var digitos		= value.length;
		
		if (value === '' || value === null) {
			jQuery(".card-number").html( '0000 0000 0000 0000' );
		}
		
		jQuery('#img-flag-card').html(
			AQPAGO.getFlagSvg( Bandeira )
		);
		jQuery('.flag-card').show();
		
		jQuery('#img-flag-card').show();
	});


	if (amount_total < 2) {
		AQPAGO.blockMultiCredit(true);
	} else {
		AQPAGO.blockMultiCredit(false);
	}
	
	/** ticket min 10 **/
	if (amount_total < 10) {
		AQPAGO.blockTicket(true);
	} else {
		AQPAGO.blockTicket(false);
	}
	
	/** multi ticket min 11 **/
	if (amount_total < 11) {
		AQPAGO.blockMultiTicket(true);
	} else {
		AQPAGO.blockMultiTicket(false);
	}
	
	if (card_one) {
		jQuery('#aqpago_card_one').val( card_one );
		jQuery('#aqpago_one_cc_card_value').val( amount_one );
		
		if (cards[card_one].card_id) {
			jQuery('#aqpago_saved_first').val( 'true' );
			jQuery('#aqpago_one_cc_card_id').val( cards[card_one].card_id );
			jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
			jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );	
		} else {
			jQuery('#aqpago_saved_first').val( '' );
			jQuery('#aqpago_one_cc_card_number').val( cards[card_one].number );
			jQuery('#aqpago_one_cc_card_owner').val( cards[card_one].owerName );
			jQuery('#aqpago_one_cc_card_month').val( cards[card_one].expiration_month );
			jQuery('#aqpago_one_cc_card_year').val( cards[card_one].expiration_year );
			jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
			jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );
			jQuery('#aqpago_one_cc_card_taxvat').val( cards[card_one].taxvat );				
		}
		
		jQuery('#one-middle-number-card').html( cards[card_one].number.substr(-4, 4) );
		AQPAGO.setBandeiraInfo('#one-li-form-payment .li-number-card .img-flag', cards[card_one].flag, 'info');
	} else {
		jQuery('#aqpago_saved_first').val( '' );
		jQuery('#aqpago_one_cc_card_number').val( jQuery('#aqpago_cc_number').val() );
		jQuery('#aqpago_one_cc_card_owner').val( jQuery('#aqpago_cc_owner').val() );
		jQuery('#aqpago_one_cc_card_month').val( jQuery('#aqpago_expiration').val() );
		jQuery('#aqpago_one_cc_card_year').val( jQuery('#aqpago_expiration_yr').val() );
		jQuery('#aqpago_one_cc_card_cid').val( jQuery('#aqpago_cc_cid').val() );
		jQuery('#aqpago_one_cc_card_installments').val( jQuery('#aqpago_installments').val() );
		jQuery('#aqpago_one_cc_card_taxvat').val( jQuery('#aqpago_documento').val() );
		jQuery('#aqpago_one_cc_card_value').val( amount_one );
	}
	
	if (card_two) {
		jQuery('#aqpago_card_two').val( card_two );
		jQuery('#aqpago_two_cc_card_value').val( amount_two );
		
		if (cards[card_one].card_id) {
			jQuery('#aqpago_saved_second').val( 'true' );
			jQuery('#aqpago_one_cc_card_id').val( cards[card_one].card_id );
			jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
			jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );	
		} else {
			jQuery('#aqpago_saved_second').val( '' );
			jQuery('#aqpago_two_cc_card_number').val( cards[card_two].number );
			jQuery('#aqpago_two_cc_card_owner').val( cards[card_two].owerName );
			jQuery('#aqpago_two_cc_card_month').val( cards[card_two].expiration_month );
			jQuery('#aqpago_two_cc_card_year').val( cards[card_two].expiration_year );
			jQuery('#aqpago_two_cc_card_cid').val( cards[card_two].securityCode );
			jQuery('#aqpago_two_cc_card_installments').val( cards[card_two].installment );
			jQuery('#aqpago_two_cc_card_taxvat').val( cards[card_two].taxvat );
		
		}
		
		jQuery('#two-middle-number-card').html( cards[card_two].number.substr(-4, 4) );
		AQPAGO.setBandeiraInfo('#two-li-form-payment .li-number-card .img-flag', cards[card_two].flag, 'info');
	}
	
	if (type_payment == 'ticket_multiple') {
		jQuery('#aqpago_ticket_value').val( amount_ticket );
	}
	
	<?php if(!isset($aqpagoJson['id'])): ?>
	if (type_payment != '') {
		
		if (typeof amount_one === "undefined") {
		} else {
			if(amount_one) {
				amount_one 		= (amount_total / 2).toFixed(2);
				amount_two 		= (amount_total - amount_one);
				amount_ticket 	= (amount_total - amount_one);
				
				jQuery('#aqpago_cc_multiple_val').val( AQPAGO.formatPrice( amount_one ) );
				
				if (type_payment == 'credit_multiple') {
					toastr.info('Valor do frete modificado, confira os novos valores para pagamento com 2 cartões!','Atenção', {extendedTimeOut: 3000,tapToDismiss:true});
					
				}
				if (type_payment == 'ticket_multiple') {
					toastr.info('Valor do frete modificado, confira os novos valores para pagamento com 2 cartões!','Atenção', {extendedTimeOut: 3000,tapToDismiss:true});
				}
			}
		}
		
		AQPAGO.setPaymentMethod(type_payment);
	}
	<?php endif; ?>
	
	window.jQuery('#place_order').click(function() {
		jQuery('#aqpago_session').val( window.AQPAGOSECTION.getSessionID() );

		if (window.jQuery('#payment_method_aqpago').is(':checked')) {
			if(type_payment) {
				
			} else {
				toastr.error('Selecione uma forma de pagamento!','Selecione o meio!', {extendedTimeOut: 3000,tapToDismiss:true});
				return false;
			}
		}

		if (window.jQuery('#payment_method_aqpago').val() == 'aqpago') {
			if (type_payment == 'credit' && card_saved) {
				if (!saved_card_one) {
					toastr.error('Você precisa finalizar o processo de preenchimento do cartão','Sem cartão adicionado!', {extendedTimeOut: 3000,tapToDismiss:true});
					return false;
				}
			} else if (type_payment == 'credit') {
				
				if (!card_one) {
					if (jQuery('#aqpago_cc_number').val() == '') {
						toastr.error('Número do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					} else if (jQuery('#aqpago_expiration').val() == '') {
						toastr.error('Mês da validade do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					} else if (jQuery('#aqpago_expiration_yr').val() == '') {
						toastr.error('Ano da validade do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					} else if (jQuery('#aqpago_cc_cid').val() == '') {
						toastr.error('Código do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					}	
					else if(jQuery('#aqpago_cc_owner').val() == '') {
						toastr.error('Nome do proprietário do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					} else if (jQuery('#aqpago_documento').val() == '') {
						toastr.error('CPF do dono do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					} else if (!AQPAGO.isValidCPF(jQuery('#aqpago_documento').val())) {
						toastr.error('CPF do dono do cartão é obrigatório!','Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
						return false;
					}	
				}	
			}
			
			if (card_one) {
				jQuery('#aqpago_card_one').val( card_one );
				jQuery('#aqpago_one_cc_card_value').val( amount_one );
				
				if (cards[card_one].card_id) {
					jQuery('#aqpago_saved_first').val( 'true' );
					jQuery('#aqpago_one_cc_card_id').val( cards[card_one].card_id );
					jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
					jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );
				} else {
					jQuery('#aqpago_saved_first').val( '' );
					jQuery('#aqpago_one_cc_card_number').val( cards[card_one].number );
					jQuery('#aqpago_one_cc_card_owner').val( cards[card_one].owerName );
					jQuery('#aqpago_one_cc_card_month').val( cards[card_one].expiration_month );
					jQuery('#aqpago_one_cc_card_year').val( cards[card_one].expiration_year );
					jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
					jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );
					jQuery('#aqpago_one_cc_card_taxvat').val( cards[card_one].taxvat );
				}
			} else {
				jQuery('#aqpago_saved_first').val( '' );
				jQuery('#aqpago_one_cc_card_number').val( jQuery('#aqpago_cc_number').val() );
				jQuery('#aqpago_one_cc_card_owner').val( jQuery('#aqpago_cc_owner').val() );
				jQuery('#aqpago_one_cc_card_month').val( jQuery('#aqpago_expiration').val() );
				jQuery('#aqpago_one_cc_card_year').val( jQuery('#aqpago_expiration_yr').val() );
				jQuery('#aqpago_one_cc_card_cid').val( jQuery('#aqpago_cc_cid').val() );
				jQuery('#aqpago_one_cc_card_installments').val( jQuery('#aqpago_installments').val() );
				jQuery('#aqpago_one_cc_card_taxvat').val( jQuery('#aqpago_documento').val() );
				jQuery('#aqpago_one_cc_card_value').val( amount_one );
			}
			
			if (card_two) {
				jQuery('#aqpago_card_two').val( card_two );
				jQuery('#aqpago_two_cc_card_value').val( amount_two );
				
				if (cards[card_two].card_id) {
					jQuery('#aqpago_saved_second').val( 'true' );
					jQuery('#aqpago_two_cc_card_id').val( cards[card_two].card_id );
					jQuery('#aqpago_two_cc_card_cid').val( cards[card_two].securityCode );
					jQuery('#aqpago_two_cc_card_installments').val( cards[card_two].installment );
				} else {
					jQuery('#aqpago_saved_second').val( '' );
					jQuery('#aqpago_two_cc_card_number').val( cards[card_two].number );
					jQuery('#aqpago_two_cc_card_owner').val( cards[card_two].owerName );
					jQuery('#aqpago_two_cc_card_month').val( cards[card_two].expiration_month );
					jQuery('#aqpago_two_cc_card_year').val( cards[card_two].expiration_year );
					jQuery('#aqpago_two_cc_card_cid').val( cards[card_two].securityCode );
					jQuery('#aqpago_two_cc_card_installments').val( cards[card_two].installment );
					jQuery('#aqpago_two_cc_card_taxvat').val( cards[card_two].taxvat );
				}
			}
			
			if (type_payment == 'credit_multiple') {
				if (!card_one) {
					toastr.error('Você precisa finalizar o processo de preenchimento do cartão','Sem cartão adicionado!', {extendedTimeOut: 3000,tapToDismiss:true});
					return false;
				} else if (!card_two) {
					toastr.error('Você precisa finalizar o processo de preenchimento do cartão','Sem cartão adicionado!', {extendedTimeOut: 3000,tapToDismiss:true});
					return false;
				}
			}
			
			if (type_payment == 'ticket_multiple') {
				if (select_card) {
					jQuery('#aqpago_selected').val( select_card );
					
					if (select_card == card_one) {
						jQuery('#aqpago_card_one').val( card_one );
						jQuery('#aqpago_two_cc_card_value').val( amount_one );
						
						if (cards[card_one].card_id) {
							jQuery('#aqpago_saved_first').val( 'true' );
							jQuery('#aqpago_one_cc_card_id').val( cards[card_one].card_id );
							jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
							jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );
						} else {
							jQuery('#aqpago_saved_first').val( '' );
							jQuery('#aqpago_one_cc_card_number').val( cards[card_one].number );
							jQuery('#aqpago_one_cc_card_owner').val( cards[card_one].owerName );
							jQuery('#aqpago_one_cc_card_month').val( cards[card_one].expiration_month );
							jQuery('#aqpago_one_cc_card_year').val( cards[card_one].expiration_year );
							jQuery('#aqpago_one_cc_card_cid').val( cards[card_one].securityCode );
							jQuery('#aqpago_one_cc_card_installments').val( cards[card_one].installment );
							jQuery('#aqpago_one_cc_card_taxvat').val( cards[card_one].taxvat );
						}
					}
					
					if (select_card == card_two) {
						jQuery('#aqpago_card_two').val( card_two );
						jQuery('#aqpago_two_cc_card_value').val( amount_two );
						
						if (cards[card_two].card_id) {
							jQuery('#aqpago_saved_second').val( 'true' );
							jQuery('#aqpago_two_cc_card_id').val( cards[card_two].card_id );
							jQuery('#aqpago_two_cc_card_cid').val( cards[card_two].securityCode );
							jQuery('#aqpago_two_cc_card_installments').val( cards[card_two].installment );
						} else {
							jQuery('#aqpago_saved_second').val( '' );
							jQuery('#aqpago_two_cc_card_number').val( cards[card_two].number );
							jQuery('#aqpago_two_cc_card_owner').val( cards[card_two].owerName );
							jQuery('#aqpago_two_cc_card_month').val( cards[card_two].expiration_month );
							jQuery('#aqpago_two_cc_card_year').val( cards[card_two].expiration_year );
							jQuery('#aqpago_two_cc_card_cid').val( cards[card_two].securityCode );
							jQuery('#aqpago_two_cc_card_installments').val( cards[card_two].installment );
							jQuery('#aqpago_two_cc_card_taxvat').val( cards[card_two].taxvat );
						}
					}
				} else {
					if (!card_one) {
						toastr.error('Você precisa finalizar o processo de preenchimento do cartão','Sem cartão adicionado!', {extendedTimeOut: 3000,tapToDismiss:true});
						return false;
					}
				}
				
				jQuery('#aqpago_ticket_value').val( amount_ticket );
			}
			
			jQuery('#aqpago_updatemulti').val( updateMulti );
			
			jQuery('.box-select-card-custom').slideUp(1);
			
			return true;
		}
	});
});

jQuery(document).ready(function($) {
    $('form.checkout').on( 'checkout_error', function() {
		
	});
	
    $( document.body ).on( 'checkout_error', function() {
		var errosLi = $('.woocommerce-error').find('li');
		var error_text = $('.woocommerce-error').find('li').first().text();
		
		$('.woocommerce-error li').each(function(index, val) {
			if ($(this).text().trim().substr(0, 11) == 'ORDER_PAID#') {
				
				var erroText = $(this).text().trim().replace('ORDER_PAID#','');
				$(this).text( erroText );
				
				if (card_one == $(this).text().trim().substr(0, 8)) {
					process_success = true;
					
					if (!card_one_success) {
						card_one_success = true;
					}
					
					$('#aqpago_cc_multiple_val_oneCard').prop( "disabled", true );
					$('#aqpago_cc_multiple_val_twoCard').prop( "disabled", true );
					
					$('#one-li-form-payment').addClass('aqpago-success');
					$('#one-li-form-payment .text-edit').hide();
					$('#aqpago_one_installments').prop('disabled', 'disabled');
					$('#edit-one').hide();
				}				
				if (card_two == $(this).text().trim().substr(0, 8)) {
					process_success = true;
					
					if (!card_two_success) {
						card_two_success = true;
					}
					
					$('#aqpago_cc_multiple_val_oneCard').prop( "disabled", true );
					$('#aqpago_cc_multiple_val_twoCard').prop( "disabled", true );
					
					$('#two-li-form-payment').addClass('aqpago-success');
					$('#two-li-form-payment .text-edit').hide();
					$('#aqpago_two_installments').prop('disabled', 'disabled');
					$('#edit-two').hide();
				}
				
				$(this).remove();
			} else if ($(this).text().trim().substr(0, 15) == 'ORDER_NOT_PAID#') {
				var erroText = $(this).text().trim().replace('ORDER_NOT_PAID#','');
				$(this).text( erroText );
				
				if (card_one == $(this).text().trim().substr(0, 8)) {
					process_erro = true;
					process_erro_type = type_payment;
					
					$('#one-li-form-payment').addClass('aqpago-erro');
					$('#aqpago_one_cc_card_erro').val(true);
					erroText = $(this).text().trim().replace(card_one+'#','');
				}				
				if (card_two == $(this).text().trim().substr(0, 8)) {
					process_erro = true;
					process_erro_type = type_payment;
					
					$('#two-li-form-payment').addClass('aqpago-erro');
					$('#aqpago_two_cc_card_erro').val(true);
					erroText = $(this).text().trim().replace(card_two+'#','');
				}

				
				$(this).text( erroText );
			}
		});
    });
	
	jQuery('.close-modal').click(function() { 
		return false;
	});
	
	<?php if(isset($aqpagoJson['id'])): ?>
		
		<?php if($aqpagoJson['type'] == 'multi_credit' && $aqpagoJson['status'] != 'ORDER_NOT_PAID'): ?>
			
			<?php if(is_array($aqpagoJson['payments'])): ?>
			
				<?php foreach($aqpagoJson['payments'] as $k => $pay): ?>
					
					<?php if($pay['status'] == 'succeeded' || $pay['status'] == 'pre_authorized'): ?>
						AQPAGO.setPaymentMethod('credit_multiple');
						
						jQuery('#list-new').click();
						jQuery('#aqpago_cc_multiple_val').val( AQPAGO.formatPrice( "<?php echo esc_attr( $pay['amount'] ) ?>" ) );
						jQuery('#aqpago_cc_number').val( "<?php echo esc_attr( $pay['credit_card']['first4_digits'] ) . esc_attr( $pay['credit_card']['last4_digits'] ) ?>" );
						
						AQPAGO.setCardData('one');
						
						jQuery('#one-li-form-payment').addClass('aqpago-success');
						jQuery('#one-li-form-payment .text-edit').hide();
						jQuery('#aqpago_one_installments').val("<?php echo esc_attr( $pay['installments'] ) ?>").change();
						jQuery('#aqpago_one_installments').prop('disabled', 'disabled');
						jQuery('#aqpago_cc_multiple_val').prop('readonly', true);
						jQuery('#edit-one').hide();
						jQuery('.aqbank_type_payment_li_box.credit').remove();
						jQuery('.aqbank_type_payment_li_box.ticket').remove();
						jQuery('#aqbank-multi-pagamento-valor .img-edit').remove();
						
						updateMulti = true;
						
						amount_one = <?php echo esc_attr( $pay['amount'] ) ?>;
						amount_two = <?php echo esc_attr( $aqpagoJson['amount'] ) ?> - amount_one;
						amount_ticket = <?php echo esc_attr( $aqpagoJson['amount'] ) ?> - amount_one;
						
						<?php if(($aqpagoJson['amount'] - $pay['amount']) < 10): ?>
							jQuery('.aqbank_type_payment_li_box.ticket_multiple').remove();
						<?php endif; ?>
					<?php endif; ?>
					
				<?php endforeach; ?>
			
			<?php endif; ?>

		<?php endif; ?>
		
		
	<?php endif; ?>	
});

function saveNewDataCardOne() {
	jQuery('.modal-button-save').text('Salvando...');
	
	setTimeout(function(){
		if (AQPAGO.saveCardOneEdit(true)) {
			if (saved_card_one) jQuery('#aqpago_saved_first').val('');
			jQuery.modal.close();
			jQuery('.modal-button-save').text('Salvar dados');
		} else {
			jQuery('.modal-button-save').text('Salvar dados');
		}
	}, 500);
}

function saveNewDataCardTwo() {
	jQuery('.modal-button-save').text('Salvando...');
	
	setTimeout(function(){
		if (AQPAGO.saveCardTwoEdit(true)) {
			if (saved_card_two) jQuery('#aqpago_saved_second').val('');
			jQuery.modal.close();
			jQuery('.modal-button-save').text('Salvar dados');
		} else {
			jQuery('.modal-button-save').text('Salvar dados');
		}
	}, 500);
}
</script>

<fieldset id="wc-<?php echo esc_attr( $id ); ?>-cc-form" class="wc-credit-card-form wc-payment-form wc-aqpago-box" style="background:transparent;">
	
	<?php do_action( 'woocommerce_credit_card_form_start', $id ); ?>
	
	<div class="payment-method payment-method-aqbank">
		
		<div class="payment-method-content aqbank-payment-description">
			<span>Escolha a</span>
			<strong><span>melhor</span></strong>
			<span>forma de</span>
			<strong><span>pagamento</span></strong>
		</div>
		
		<div id="list-new" class="aqbank-add-new-card"><svg version="1.1" id="Camada_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 159.5 58.9" style="enable-background:new 0 0 159.5 58.9;" xml:space="preserve"><style type="text/css">.st0{fill:#4F076B;}.st1{fill:#FFFFFF;}.st2{enable-background:new    ;}</style><path class="st0" d="M6.4,0h146.7c3.5,0,6.4,2.6,6.4,5.8v47.4c0,3.2-2.9,5.8-6.4,5.8H6.4c-3.5,0-6.4-2.6-6.4-5.8V5.8C0,2.6,2.9,0,6.4,0z"></path><g transform="translate(14.523 11.959)"><g transform="translate(0 14.275)"><path class="st1" d="M30.4,0H1.9C0.5,0,0,1,0,2.3v1.4h32.3V2.3C32.3,1,31.8,0,30.4,0z"></path><path class="st1" d="M0,17.7C0,19,0.5,20,1.9,20h28.5c1.4,0,1.9-1,1.9-2.3V7.5H0V17.7z M16,10.1h14.2c0.2,0,0.4,0.2,0.4,0.5c0,0.2-0.2,0.3-0.4,0.4H16c-0.2,0-0.4-0.2-0.4-0.5C15.7,10.3,15.8,10.1,16,10.1L16,10.1z M16,12.3h14.2c0.2,0,0.4,0.2,0.4,0.5c0,0.2-0.2,0.4-0.4,0.4H16c-0.2,0-0.4-0.2-0.4-0.5C15.6,12.4,15.8,12.3,16,12.3L16,12.3z M16,14.4h14.2c0.2,0,0.4,0.2,0.4,0.5c0,0.2-0.2,0.3-0.4,0.4H16c-0.2,0-0.4-0.2-0.4-0.5C15.7,14.6,15.8,14.4,16,14.4L16,14.4z M16,16.6h14.2c0.2,0,0.4,0.2,0.4,0.5c0,0.2-0.2,0.3-0.4,0.4H16c-0.2,0-0.4-0.2-0.4-0.5C15.7,16.7,15.8,16.6,16,16.6L16,16.6z M1.9,10.9C2,10.4,2.4,10,2.9,10h9.4c0.5,0,0.9,0.4,0.9,0.8v4.4c0,0.5-0.4,0.9-0.9,0.9H2.9c-0.5,0-0.9-0.4-0.9-0.9V10.9z"></path></g><path class="st1" d="M36.8,16.4V11h-6.1c-1,0-1.8-0.8-1.8-1.8s0.8-1.8,1.8-1.8h6.1V2c0-1.1,0.9-2,2-2c1.1,0,2,0.9,2,2v5.4h6.1c1,0,1.8,0.8,1.8,1.8c0,1-0.8,1.8-1.8,1.8h-6.1v5.4c0,1.1-0.9,2-2,2C37.7,18.3,36.8,17.5,36.8,16.4C36.8,16.4,36.8,16.4,36.8,16.4L36.8,16.4z"></path><g transform="translate(65.673 7.175)"><g class="st2"><path class="st1" d="M3.2,0.5h0.3l3,6.4l0,0.1H5.8L5.1,5.5H1.5L0.9,7H0.2l0-0.1L3.2,0.5z M3.3,1.7L1.8,4.9h2.9L3.3,1.7z"></path><path class="st1" d="M7.6,0.5h2.5c1,0,1.8,0.3,2.5,0.9c0.6,0.6,0.9,1.4,0.9,2.3c0,1-0.3,1.8-0.9,2.4C11.9,6.7,11.1,7,10.1,7H7.6V0.5z M8.3,6.4h1.8c0.8,0,1.4-0.2,1.9-0.7c0.5-0.5,0.7-1.1,0.7-1.9c0-0.8-0.2-1.4-0.7-1.9c-0.5-0.5-1.1-0.7-1.9-0.7H8.3V6.4z"></path><path class="st1" d="M14.9,7V0.5h0.7V7H14.9z"></path><path class="st1" d="M20.3,7.1c-0.9,0-1.7-0.3-2.4-1c-0.7-0.6-1-1.4-1-2.4s0.3-1.8,1-2.4c0.7-0.6,1.4-1,2.4-1c0.5,0,1,0.1,1.5,0.3c0.5,0.2,0.9,0.5,1.2,1v0.1l-0.5,0.4h-0.1c-0.2-0.3-0.6-0.6-0.9-0.8S20.8,1,20.4,1c-0.8,0-1.4,0.3-1.9,0.8C18,2.3,17.7,3,17.7,3.7c0,0.8,0.3,1.4,0.8,1.9c0.5,0.5,1.1,0.8,1.9,0.8c0.4,0,0.8-0.1,1.2-0.3c0.4-0.2,0.7-0.4,0.9-0.8h0.1L23,5.8v0.1c-0.3,0.4-0.7,0.7-1.2,1C21.4,7,20.9,7.1,20.3,7.1z"></path><path class="st1" d="M24.3,7V0.5H25V7H24.3z"></path><path class="st1" d="M32.3,6.2c-0.7,0.6-1.5,1-2.4,1c-1,0-1.8-0.3-2.4-1c-0.7-0.6-1-1.4-1-2.4s0.3-1.8,1-2.4c0.7-0.6,1.5-1,2.4-1c1,0,1.8,0.3,2.4,1c0.7,0.6,1,1.4,1,2.4S32.9,5.5,32.3,6.2z M27.9,5.7c0.5,0.5,1.2,0.8,1.9,0.8c0.8,0,1.4-0.3,1.9-0.8c0.5-0.5,0.8-1.2,0.8-1.9c0-0.8-0.3-1.4-0.8-1.9C31.2,1.3,30.6,1,29.8,1c-0.8,0-1.4,0.3-1.9,0.8c-0.5,0.5-0.8,1.2-0.8,1.9C27.1,4.5,27.4,5.2,27.9,5.7z"></path><path class="st1" d="M39.2,5.7V0.5h0.7v6.6h-0.3l-4.2-5.2V7h-0.7V0.5H35L39.2,5.7z"></path><path class="st1" d="M44,0.5h0.3l3,6.4l0,0.1h-0.7l-0.7-1.5h-3.5L41.7,7H41l0-0.1L44,0.5z M44.1,1.7l-1.5,3.2h2.9L44.1,1.7z"></path><path class="st1" d="M53.1,2.4c0,0.5-0.1,0.9-0.4,1.2c-0.2,0.3-0.6,0.5-1.1,0.6l1.5,2.8l0,0.1h-0.7L51,4.3h-0.1h-1.7V7h-0.7V0.5h2.4c0.7,0,1.3,0.2,1.7,0.5S53.1,1.8,53.1,2.4z M51,1.1h-1.9v2.5H51c0.4,0,0.8-0.1,1-0.4c0.2-0.2,0.4-0.5,0.4-0.9c0-0.4-0.1-0.7-0.4-0.9C51.8,1.3,51.5,1.1,51,1.1z"></path></g><g class="st2"><path class="st1" d="M5.4,22.1c-1.4,0-2.5-0.5-3.5-1.4c-0.9-0.9-1.4-2-1.4-3.4S1,14.9,1.9,14c0.9-0.9,2.1-1.3,3.5-1.3c0.8,0,1.5,0.2,2.1,0.5c0.7,0.3,1.2,0.8,1.7,1.3v0.2l-1.3,1.1H7.8c-0.6-0.7-1.4-1.1-2.3-1.1c-0.8,0-1.4,0.3-1.9,0.8c-0.5,0.5-0.8,1.2-0.8,2s0.3,1.4,0.8,2c0.5,0.5,1.2,0.8,1.9,0.8c0.9,0,1.7-0.4,2.3-1.1h0.2l1.3,1.1v0.2c-0.5,0.6-1,1-1.7,1.3C6.9,22,6.2,22.1,5.4,22.1z"></path><path class="st1" d="M13.7,12.9H15l4.2,8.9L19.1,22h-2.2l-0.7-1.6h-3.8L11.7,22H9.6l-0.1-0.2L13.7,12.9z M14.3,15.8L13,18.8h2.6L14.3,15.8z"></path><path class="st1" d="M25.9,18.3l1.9,3.5L27.7,22h-2.2l-1.8-3.4h-1.3V22h-2.2v-9.1h3.9c1.1,0,2,0.3,2.6,0.8c0.6,0.5,0.9,1.2,0.9,2.1c0,0.6-0.2,1.1-0.5,1.5C26.9,17.7,26.5,18,25.9,18.3z M24.2,14.7h-1.8V17h1.8c0.4,0,0.7-0.1,0.9-0.3c0.2-0.2,0.4-0.5,0.4-0.9c0-0.4-0.1-0.7-0.4-0.9C24.9,14.8,24.6,14.7,24.2,14.7z"></path><path class="st1" d="M36.3,14.7h-2.9V22h-2.2v-7.3h-2.9v-1.8h7.9V14.7z"></path><path class="st1" d="M39.6,12.9h1.3l4.2,8.9L45,22h-2.2l-0.7-1.6h-3.8L37.6,22h-2.1l-0.1-0.2L39.6,12.9z M37.9,11.7h-0.1l-0.1-0.1v-1.4C38,9.7,38.4,9.5,39,9.5c0.3,0,0.7,0.1,1.4,0.3s1,0.3,1.2,0.3c0.5,0,0.8-0.2,1-0.5h0.1l0.1,0.1V11c-0.3,0.4-0.7,0.6-1.3,0.6c-0.3,0-0.8-0.1-1.4-0.3s-1-0.3-1.2-0.3C38.4,11.1,38.1,11.3,37.9,11.7z M40.3,15.8l-1.3,3.1h2.6L40.3,15.8z"></path><path class="st1" d="M50.3,22.1c-1.4,0-2.6-0.4-3.6-1.3c-1-0.9-1.4-2-1.4-3.4s0.5-2.5,1.4-3.4c0.9-0.9,2.1-1.3,3.6-1.3c1.4,0,2.6,0.4,3.6,1.3c1,0.9,1.4,2,1.4,3.4s-0.5,2.5-1.4,3.4C52.9,21.7,51.7,22.1,50.3,22.1z M47.5,17.4c0,0.8,0.3,1.4,0.8,2s1.2,0.8,2,0.8c0.8,0,1.4-0.3,2-0.8c0.5-0.5,0.8-1.2,0.8-2s-0.3-1.4-0.8-2c-0.5-0.5-1.2-0.8-2-0.8c-0.8,0-1.4,0.3-2,0.8C47.8,16,47.5,16.6,47.5,17.4z"></path></g></g></g></svg></div>
		
		<div class="payment-method-content aqbank_type_payment">
			<?php if(in_array('credit', $enable_for_methods)): ?>
			<div class="aqbank_type_payment_li">
				<div class="aqbank_type_payment_li_box credit" onClick="return AQPAGO.setPaymentMethod('credit');">
					<input type="radio" name="aqbank_payment_type[method]" value="credit" checked="" class="radio aqbank-type-radio" style="display: none;" id="aqpago_aqbank_payment_credit">
					<div class="aqbank-ico"><svg id="Grupo_5018" data-name="Grupo 5018" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="62.933" height="48.025" viewBox="0 0 62.933 48.025"><defs><radialGradient id="radial-gradient" cx="0.5" cy="0.5" r="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#ac73c2"></stop><stop offset="1" stop-color="#a564bf"></stop></radialGradient></defs><g id="Grupo_2557" data-name="Grupo 2557" transform="translate(0 10.459)"><g id="Grupo_2553" data-name="Grupo 2553"><path id="Caminho_1645" data-name="Caminho 1645" d="M582.409,327.693H532.864c-2.359,0-3.3,1.932-3.3,4.3v2.63H585.7v-2.63C585.7,329.625,584.773,327.693,582.409,327.693Z" transform="translate(-529.561 -327.693)" fill="#4f076b"></path><path id="Caminho_1646" data-name="Caminho 1646" d="M529.561,352.482c0,2.364.943,4.3,3.3,4.3h49.545c2.364,0,3.295-1.937,3.295-4.3V333.238H529.561Zm27.816-14.3h24.609a.787.787,0,0,1,0,1.547H557.377a.787.787,0,0,1,0-1.547Zm0,4.025h24.609a.787.787,0,0,1,0,1.548H557.377a.787.787,0,0,1,0-1.548Zm0,4.023h24.609a.787.787,0,0,1,0,1.547H557.377a.786.786,0,0,1,0-1.547Zm0,4.025h24.609a.787.787,0,0,1,0,1.547H557.377a.787.787,0,0,1,0-1.547Zm-24.45-10.662a1.589,1.589,0,0,1,1.588-1.59h16.339a1.589,1.589,0,0,1,1.588,1.59v8.187a1.59,1.59,0,0,1-1.588,1.593H534.515a1.59,1.59,0,0,1-1.588-1.593Z" transform="translate(-529.561 -319.217)" fill="#4f076b"></path></g></g><g id="Grupo_5015" data-name="Grupo 5015" transform="translate(33.267)"><circle id="Elipse_1" data-name="Elipse 1" cx="14.833" cy="14.833" r="14.833" fill="url(#radial-gradient)"></circle><text id="_12x" data-name="12x" transform="translate(3.584 18.646)" fill="#fff" font-size="14" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">12x</tspan></text></g></svg></div> 

					<div class="payment_li_text">
						<strong><span>Cartão</span></strong>
						<span class="text-light"><span>de Crédito</span></span>
					</div>
					<div class="aqbank-arrow-right"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991"><path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"></path></svg></div>
					<span class="change-text"></span>
				</div>
			</div>
			<?php endif; ?>
			<?php if(in_array('credit_multiple', $enable_for_methods)): ?>
			<div class="aqbank_type_payment_li aqbank_set_multi_credit">
				<div class="aqbank_type_payment_li_box credit_multiple" onClick="return AQPAGO.setPaymentMethod('credit_multiple');">
					<input type="radio" name="aqbank_payment_type[method]" value="credit_multiple" checked="" class="radio aqbank-type-radio" style="display: none;" id="aqpago_aqbank_payment_credit_multiple">
					<div class="aqbank-ico"><svg id="Grupo_6489" data-name="Grupo 6489" xmlns="http://www.w3.org/2000/svg" width="27.73" height="26.612" viewBox="0 0 27.73 26.612"><path id="Caminho_9528" data-name="Caminho 9528" d="M43.09,423.214a1.848,1.848,0,0,0-2.5-.8l-17.929,9.252a1.849,1.849,0,0,0-.8,2.5l.571,1.113,21.223-10.954Z" transform="translate(-21.66 -422.209)" fill="#561271"></path><path id="Caminho_9529" data-name="Caminho 9529" d="M24.83,435.686c0-4.605,0-4.482,0-4.6l-1.877.968Z" transform="translate(-20.799 -416.317)" fill="#561271"></path><path id="Caminho_9530" data-name="Caminho 9530" d="M44.523,432.641a1.858,1.858,0,0,0-.083-1.506l-3.123-6.051-14.046,7.249C44.741,432.333,43.752,432.217,44.523,432.641Z" transform="translate(-17.935 -420.301)" fill="#561271"></path><path id="Caminho_9531" data-name="Caminho 9531" d="M45.327,429.63H26.6a2.464,2.464,0,0,0-2.328,2.572v9.124A2.46,2.46,0,0,0,26.6,443.9H45.327a2.46,2.46,0,0,0,2.329-2.569V432.2A2.464,2.464,0,0,0,45.327,429.63Zm-12,10.6.29-.276-.28-.288a.17.17,0,1,1,.243-.238l.283.286.283-.279a.173.173,0,0,1,.245,0,.176.176,0,0,1,0,.245l-.286.278.28.289a.171.171,0,0,1-.245.238l-.285-.286-.283.278a.173.173,0,0,1-.243,0A.176.176,0,0,1,33.331,440.225Zm1.449,0,.285-.276-.275-.288a.171.171,0,1,1,.245-.238l.28.286.286-.279a.169.169,0,0,1,.241,0,.16.16,0,0,1,.047.107c0,.116-.018.106-.334.416l.28.289a.148.148,0,0,1,.043.1.249.249,0,0,0,0,.035.155.155,0,0,1-.052.1.173.173,0,0,1-.243,0l-.278-.286-.286.278a.171.171,0,0,1-.241,0A.173.173,0,0,1,34.78,440.225Zm1.256.1c.013-.088.068-.113.338-.373l-.28-.288a.172.172,0,0,1,.007-.244.168.168,0,0,1,.236.007l.28.286.288-.279c.166-.165.4.091.24.245l-.288.278.283.289a.173.173,0,0,1-.25.238l-.278-.286-.288.278a.171.171,0,0,1-.241,0,.135.135,0,0,1-.043-.1A.473.473,0,0,0,36.037,440.322Zm2.557-.647-.288.278.281.289c.16.16-.088.394-.246.238l-.281-.286-.283.278a.172.172,0,0,1-.241-.246l.285-.276-.28-.288a.172.172,0,0,1,.25-.238l.278.286.288-.279a.165.165,0,0,1,.238,0A.175.175,0,0,1,38.594,439.674Zm-9.895.707c0-.033,0,0,0-.052.01-.1.067-.12.336-.381-.288-.3-.326-.311-.324-.411a.169.169,0,0,1,.291-.115l.278.286.289-.279c.158-.165.4.09.236.245l-.285.278.278.289c.161.156-.08.394-.245.238l-.28-.286-.288.278A.171.171,0,0,1,28.7,440.382Zm-2.705-.156.29-.276c-.022-.021-.281-.281-.3-.3a.171.171,0,0,1,.26-.221l.281.286.286-.279a.171.171,0,1,1,.238.245c-.255.248-.188.185-.283.278,0,0,.178.181.281.289.156.16-.092.394-.25.238l-.278-.286-.286.278a.173.173,0,0,1-.243,0A.17.17,0,0,1,25.994,440.225Zm1.451,0,.286-.276-.062-.062-.138-.143-.082-.083a.176.176,0,0,1,0-.244.174.174,0,0,1,.241.007c.1.1.273.276.281.286l.181-.178.1-.1a.173.173,0,0,1,.243,0,.18.18,0,0,1,0,.245l-.288.278c.336.346.314.293.326.424a.173.173,0,0,1-.293.1l-.276-.286-.289.278a.171.171,0,1,1-.238-.246Zm3.807-.551-.281.278.275.289c.161.155-.078.4-.246.238l-.276-.286-.288.278a.171.171,0,0,1-.238-.246l.286-.276-.281-.288a.171.171,0,0,1,.246-.238l.281.286.285-.279a.171.171,0,1,1,.238.245Zm14.307,0-.29.278.28.289c.163.16-.093.394-.241.238l-.283-.286-.283.278a.174.174,0,0,1-.245-.246l.288-.276-.278-.288a.17.17,0,1,1,.243-.238l.28.286.288-.279a.169.169,0,0,1,.241,0A.175.175,0,0,1,45.558,439.674Zm-2.507.551.285-.276-.278-.288a.17.17,0,0,1,.243-.238l.281.286.286-.279a.172.172,0,0,1,.241.245l-.288.278.28.289c.161.16-.092.394-.243.238l-.285-.286-.281.278A.172.172,0,0,1,43.051,440.225Zm-1.306,0,.288-.276-.28-.288a.171.171,0,1,1,.245-.238l.28.286.285-.279a.171.171,0,0,1,.289.09c-.013.141.048.062-.336.433.265.271.318.3.328.386a.173.173,0,0,1-.293.141l-.278-.286-.288.278A.172.172,0,0,1,41.745,440.225Zm-1.447,0,.288-.276-.28-.288a.17.17,0,1,1,.243-.238l.28.286.286-.279a.172.172,0,0,1,.241.245l-.288.278.278.289c.16.153-.083.4-.243.238l-.281-.286-.285.278a.172.172,0,1,1-.24-.246Zm4.015-2.862H41.539a1.335,1.335,0,0,1-1.336-1.333v-1.258a1.335,1.335,0,0,1,1.336-1.331h2.774a1.335,1.335,0,0,1,1.334,1.331v1.258A1.335,1.335,0,0,1,44.312,437.363Z" transform="translate(-19.926 -417.283)" fill="#45364b"></path></svg></div> 
					<div class="payment_li_text">
						<strong><span>2 Cartões</span></strong>
						<span class="text-light"><span>de Crédito</span></span>
					</div>
					<div class="aqbank-arrow-right"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991"><path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"></path></svg></div>
					<span class="change-text"></span>
				</div>
				<span class="ticket-info-tool" style="display: none;">
					<span class="multi-valor-tooltip">
						<a href="#" class="tooltip-toggle">
							<svg xmlns="http://www.w3.org/2000/svg" class="img-tool-tip" width="20" height="21" viewBox="0 0 20 21">
								<g id="Grupo_4195" data-name="Grupo 4195" transform="translate(-1848 -373)">
									<circle id="Elipse_104" data-name="Elipse 104" cx="10" cy="10" r="10" transform="translate(1848 374)" fill="#2b2a2a"></circle>
									<text id="_" data-name="?" transform="translate(1855 388)" fill="#fff" font-size="14" font-family="SegoeUI, Segoe UI"><tspan x="0" y="0">?</tspan></text>
								</g>
							</svg>
						</a>
						<span class="tooltip-content"><span>O valor minímo para usar está forma é de R$ 2,00</span></span>
					</span>
				</span>
			</div>	
			<?php endif; ?>
			<?php if(in_array('ticket_multiple', $enable_for_methods)): ?>
			<div class="aqbank_type_payment_li aqbank_set_multi_ticket">
				<div class="aqbank_type_payment_li_box ticket_multiple" onClick="return AQPAGO.setPaymentMethod('ticket_multiple');">
					<input type="radio" name="aqbank_payment_type[method]" value="ticket_multiple" checked="" class="radio aqbank-type-radio" style="display: none;" id="aqpago_aqbank_payment_ticket_multiple">
					<div class="aqbank-ico"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="27.936" height="28.588" viewBox="0 0 27.936 28.588"><defs><clipPath id="clip-path"><rect id="Retângulo_76" data-name="Retângulo 76" width="21.362" height="17.089" fill="#561271"></rect></clipPath></defs><g id="Grupo_6497" data-name="Grupo 6497" transform="translate(-49.551 -517.359)"><g id="Grupo_6491" data-name="Grupo 6491" transform="translate(49.551 522.164) rotate(-13)"><rect id="Retângulo_55" data-name="Retângulo 55" width="0.712" height="11.749" transform="translate(1.78 2.136)" fill="#561271"></rect><rect id="Retângulo_56" data-name="Retângulo 56" width="1.068" height="9.969" transform="translate(3.56 2.136)" fill="#561271"></rect><rect id="Retângulo_57" data-name="Retângulo 57" width="0.712" height="9.969" transform="translate(5.696 2.136)" fill="#561271"></rect><rect id="Retângulo_58" data-name="Retângulo 58" width="1.068" height="9.969" transform="translate(7.121 2.136)" fill="#561271"></rect><rect id="Retângulo_59" data-name="Retângulo 59" width="0.712" height="9.969" transform="translate(8.901 2.136)" fill="#561271"></rect><g id="Grupo_112" data-name="Grupo 112" transform="translate(0 0)"><g id="Grupo_111" data-name="Grupo 111" clip-path="url(#clip-path)"><path id="Caminho_68" data-name="Caminho 68" d="M.357,0A.336.336,0,0,0,0,.356v1.78H.713V.712H2.137V0Z" transform="translate(-0.001 0)" fill="#561271"></path><rect id="Retângulo_60" data-name="Retângulo 60" width="0.712" height="11.749" transform="translate(10.325 2.136)" fill="#561271"></rect><rect id="Retângulo_61" data-name="Retângulo 61" width="1.068" height="9.969" transform="translate(13.529 2.136)" fill="#561271"></rect><rect id="Retângulo_62" data-name="Retângulo 62" width="0.712" height="9.969" transform="translate(15.665 2.136)" fill="#561271"></rect><rect id="Retângulo_63" data-name="Retângulo 63" width="1.068" height="9.969" transform="translate(17.089 2.136)" fill="#561271"></rect><rect id="Retângulo_64" data-name="Retângulo 64" width="0.712" height="11.749" transform="translate(18.869 2.136)" fill="#561271"></rect><path id="Caminho_69" data-name="Caminho 69" d="M.713,53.078V51.654H0v1.78a.336.336,0,0,0,.356.356h1.78v-.712Z" transform="translate(-0.001 -36.701)" fill="#561271"></path><path id="Caminho_70" data-name="Caminho 70" d="M67.838,51.654v1.424H66.414v.712h1.78a.336.336,0,0,0,.356-.356v-1.78Z" transform="translate(-47.189 -36.701)" fill="#561271"></path><path id="Caminho_71" data-name="Caminho 71" d="M68.194,0h-1.78V.712h1.424V2.136h.712V.356A.336.336,0,0,0,68.194,0" transform="translate(-47.189 0)" fill="#561271"></path><rect id="Retângulo_65" data-name="Retângulo 65" width="0.712" height="1.068" transform="translate(3.204 12.817)" fill="#561271"></rect><rect id="Retângulo_66" data-name="Retângulo 66" width="0.712" height="1.068" transform="translate(4.628 12.817)" fill="#561271"></rect><rect id="Retângulo_67" data-name="Retângulo 67" width="0.712" height="1.068" transform="translate(6.052 12.817)" fill="#561271"></rect><rect id="Retângulo_68" data-name="Retângulo 68" width="0.712" height="1.068" transform="translate(7.476 12.817)" fill="#561271"></rect><rect id="Retângulo_69" data-name="Retângulo 69" width="0.712" height="1.068" transform="translate(8.901 12.817)" fill="#561271"></rect><rect id="Retângulo_70" data-name="Retângulo 70" width="0.712" height="1.068" transform="translate(11.749 12.817)" fill="#561271"></rect><rect id="Retângulo_71" data-name="Retângulo 71" width="0.712" height="1.068" transform="translate(13.173 12.817)" fill="#561271"></rect><rect id="Retângulo_72" data-name="Retângulo 72" width="0.712" height="1.068" transform="translate(14.597 12.817)" fill="#561271"></rect><rect id="Retângulo_73" data-name="Retângulo 73" width="0.712" height="1.068" transform="translate(16.021 12.817)" fill="#561271"></rect><rect id="Retângulo_74" data-name="Retângulo 74" width="0.712" height="1.068" transform="translate(17.445 12.817)" fill="#561271"></rect><rect id="Retângulo_75" data-name="Retângulo 75" width="0.712" height="9.969" transform="translate(12.105 2.136)" fill="#561271"></rect></g></g></g><g id="Grupo_6490" data-name="Grupo 6490" transform="translate(49.756 519.334)"><path id="Caminho_9531" data-name="Caminho 9531" d="M45.327,429.63H26.6a2.464,2.464,0,0,0-2.328,2.572v9.124A2.46,2.46,0,0,0,26.6,443.9H45.327a2.46,2.46,0,0,0,2.329-2.569V432.2A2.464,2.464,0,0,0,45.327,429.63Zm-12,10.6.29-.276-.28-.288a.17.17,0,1,1,.243-.238l.283.286.283-.279a.173.173,0,0,1,.245,0,.176.176,0,0,1,0,.245l-.286.278.28.289a.171.171,0,0,1-.245.238l-.285-.286-.283.278a.173.173,0,0,1-.243,0A.176.176,0,0,1,33.331,440.225Zm1.449,0,.285-.276-.275-.288a.171.171,0,1,1,.245-.238l.28.286.286-.279a.169.169,0,0,1,.241,0,.16.16,0,0,1,.047.107c0,.116-.018.106-.334.416l.28.289a.148.148,0,0,1,.043.1.249.249,0,0,0,0,.035.155.155,0,0,1-.052.1.173.173,0,0,1-.243,0l-.278-.286-.286.278a.171.171,0,0,1-.241,0A.173.173,0,0,1,34.78,440.225Zm1.256.1c.013-.088.068-.113.338-.373l-.28-.288a.172.172,0,0,1,.007-.244.168.168,0,0,1,.236.007l.28.286.288-.279c.166-.165.4.091.24.245l-.288.278.283.289a.173.173,0,0,1-.25.238l-.278-.286-.288.278a.171.171,0,0,1-.241,0,.135.135,0,0,1-.043-.1A.473.473,0,0,0,36.037,440.322Zm2.557-.647-.288.278.281.289c.16.16-.088.394-.246.238l-.281-.286-.283.278a.172.172,0,0,1-.241-.246l.285-.276-.28-.288a.172.172,0,0,1,.25-.238l.278.286.288-.279a.165.165,0,0,1,.238,0A.175.175,0,0,1,38.594,439.674Zm-9.895.707c0-.033,0,0,0-.052.01-.1.067-.12.336-.381-.288-.3-.326-.311-.324-.411a.169.169,0,0,1,.291-.115l.278.286.289-.279c.158-.165.4.09.236.245l-.285.278.278.289c.161.156-.08.394-.245.238l-.28-.286-.288.278A.171.171,0,0,1,28.7,440.382Zm-2.705-.156.29-.276c-.022-.021-.281-.281-.3-.3a.171.171,0,0,1,.26-.221l.281.286.286-.279a.171.171,0,1,1,.238.245c-.255.248-.188.185-.283.278,0,0,.178.181.281.289.156.16-.092.394-.25.238l-.278-.286-.286.278a.173.173,0,0,1-.243,0A.17.17,0,0,1,25.994,440.225Zm1.451,0,.286-.276-.062-.062-.138-.143-.082-.083a.176.176,0,0,1,0-.244.174.174,0,0,1,.241.007c.1.1.273.276.281.286l.181-.178.1-.1a.173.173,0,0,1,.243,0,.18.18,0,0,1,0,.245l-.288.278c.336.346.314.293.326.424a.173.173,0,0,1-.293.1l-.276-.286-.289.278a.171.171,0,1,1-.238-.246Zm3.807-.551-.281.278.275.289c.161.155-.078.4-.246.238l-.276-.286-.288.278a.171.171,0,0,1-.238-.246l.286-.276-.281-.288a.171.171,0,0,1,.246-.238l.281.286.285-.279a.171.171,0,1,1,.238.245Zm14.307,0-.29.278.28.289c.163.16-.093.394-.241.238l-.283-.286-.283.278a.174.174,0,0,1-.245-.246l.288-.276-.278-.288a.17.17,0,1,1,.243-.238l.28.286.288-.279a.169.169,0,0,1,.241,0A.175.175,0,0,1,45.558,439.674Zm-2.507.551.285-.276-.278-.288a.17.17,0,0,1,.243-.238l.281.286.286-.279a.172.172,0,0,1,.241.245l-.288.278.28.289c.161.16-.092.394-.243.238l-.285-.286-.281.278A.172.172,0,0,1,43.051,440.225Zm-1.306,0,.288-.276-.28-.288a.171.171,0,1,1,.245-.238l.28.286.285-.279a.171.171,0,0,1,.289.09c-.013.141.048.062-.336.433.265.271.318.3.328.386a.173.173,0,0,1-.293.141l-.278-.286-.288.278A.172.172,0,0,1,41.745,440.225Zm-1.447,0,.288-.276-.28-.288a.17.17,0,1,1,.243-.238l.28.286.286-.279a.172.172,0,0,1,.241.245l-.288.278.278.289c.16.153-.083.4-.243.238l-.281-.286-.285.278a.172.172,0,1,1-.24-.246Zm4.015-2.862H41.539a1.335,1.335,0,0,1-1.336-1.333v-1.258a1.335,1.335,0,0,1,1.336-1.331h2.774a1.335,1.335,0,0,1,1.334,1.331v1.258A1.335,1.335,0,0,1,44.312,437.363Z" transform="translate(-19.926 -417.283)" fill="#45364b"></path></g></g></svg></div> 
					<div class="payment_li_text">
						<strong><span>Cartão &amp; Boleto</span></strong>
						<span class="text-light"><span>Bancário</span></span>
					</div>
					<div class="aqbank-arrow-right"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991"><path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"></path></svg></div>
					<span class="change-text"></span>
				</div>
				<span class="ticket-info-tool" style="display: none;">
					<span class="multi-valor-tooltip">
						<a href="#" class="tooltip-toggle">
							<svg xmlns="http://www.w3.org/2000/svg" class="img-tool-tip" width="20" height="21" viewBox="0 0 20 21">
								<g id="Grupo_4195" data-name="Grupo 4195" transform="translate(-1848 -373)">
									<circle id="Elipse_104" data-name="Elipse 104" cx="10" cy="10" r="10" transform="translate(1848 374)" fill="#2b2a2a"></circle>
									<text id="_" data-name="?" transform="translate(1855 388)" fill="#fff" font-size="14" font-family="SegoeUI, Segoe UI"><tspan x="0" y="0">?</tspan></text>
								</g>
							</svg>
						</a>
						<span class="tooltip-content"><span>O valor minímo para usar está forma é de R$ 11,00</span></span>
					</span>			
				</span>
			</div>
			<?php endif; ?>
			<?php if(in_array('pix', $enable_for_methods)): ?>
			<!-- Pix metodo --><!--
			<div class="aqbank_type_payment_li aqbank_set_pix">
				<div class="aqbank_type_payment_li_box pix" onClick="return AQPAGO.setPaymentMethod('pix');">
					<input type="radio" name="aqbank_payment_type[method]" value="pix" checked="" class="radio aqbank-type-radio" style="display: none;" id="aqpago_aqbank_payment_pix">
					<div class="aqbank-ico">
						<svg xmlns="http://www.w3.org/2000/svg" width="38.269" height="38.267" viewBox="0 0 38.269 38.267"><g id="Grupo_4159" data-name="Grupo 4159" transform="translate(0 0)"><g id="g992" transform="translate(7.76 21.585)"><path id="path994" d="M-128.161,537.8a5.591,5.591,0,0,1-3.977-1.646l-5.739-5.739a1.087,1.087,0,0,0-1.506,0l-5.762,5.762a5.585,5.585,0,0,1-3.975,1.644h-1.132l7.27,7.27a5.816,5.816,0,0,0,8.223,0l7.291-7.291Z" transform="translate(150.253 -530.113)" fill="#fff"/></g><g id="g996" transform="translate(7.758)"><path id="path998" d="M-149.12,529.565a5.587,5.587,0,0,1,3.975,1.646l5.762,5.762a1.066,1.066,0,0,0,1.506,0l5.739-5.742a5.593,5.593,0,0,1,3.977-1.644h.687l-7.291-7.291a5.813,5.813,0,0,0-8.22,0l0,0-7.268,7.27Z" transform="translate(150.254 -520.594)" fill="#fff"/></g><g id="g1000" transform="translate(0 10.593)"><path id="path1002" d="M-117.1,529.691l-4.406-4.4a.871.871,0,0,1-.313.063h-2a3.961,3.961,0,0,0-2.782,1.15l-5.739,5.739a2.752,2.752,0,0,1-3.894,0l0,0L-142,526.478a3.955,3.955,0,0,0-2.78-1.152h-2.469a.842.842,0,0,1-.3-.061l-4.422,4.424a5.813,5.813,0,0,0,0,8.22l0,0,4.422,4.422a.888.888,0,0,1,.3-.059h2.463a3.959,3.959,0,0,0,2.78-1.154l5.762-5.76a2.821,2.821,0,0,1,3.9,0l5.739,5.74a3.959,3.959,0,0,0,2.78,1.152h2a.831.831,0,0,1,.313.064l4.406-4.406a5.812,5.812,0,0,0,0-8.22v0" transform="translate(153.675 -525.265)" fill="#fff"/></g></g></svg>
					</div> 
					<div class="payment_li_text">
						<strong><span>PIX</span></strong>
						<span class="text-light"><span>Transferência</span></span>
					</div>
					<div class="aqbank-arrow-right"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991"><path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"></path></svg></div>
					<span class="change-text"></span>
				</div>
			</div>		-->	
			<?php endif; ?>
			<?php if(in_array('ticket', $enable_for_methods)): ?>
			<div class="aqbank_type_payment_li aqbank_set_ticket">
				<div class="aqbank_type_payment_li_box ticket no-border" onClick="return AQPAGO.setPaymentMethod('ticket');">
					<input type="radio" name="aqbank_payment_type[method]" value="ticket" class="radio aqbank-type-radio" style="display: none;" id="aqpago_aqbank_payment_ticket">
					<div class="aqbank-ico"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="41.77" height="27.569" viewBox="0 0 41.77 27.569"><defs><clipPath id="clip-path"></clipPath></defs><g id="Grupo_4161" data-name="Grupo 4161" transform="translate(-4.177 -5.012)"><rect id="Retângulo_55" data-name="Retângulo 55" width="1.67" height="27.569" transform="translate(4.177 5.012)" fill="#4f076b"></rect><rect id="Retângulo_56" data-name="Retângulo 56" width="2.506" height="23.392" transform="translate(8.354 5.012)" fill="#4f076b"></rect><rect id="Retângulo_57" data-name="Retângulo 57" width="1.671" height="23.392" transform="translate(13.367 5.012)" fill="#4f076b"></rect><rect id="Retângulo_58" data-name="Retângulo 58" width="2.506" height="23.392" transform="translate(16.709 5.012)" fill="#4f076b"></rect><rect id="Retângulo_59" data-name="Retângulo 59" width="1.671" height="23.392" transform="translate(20.885 5.012)" fill="#4f076b"></rect><rect id="Retângulo_60" data-name="Retângulo 60" width="1.67" height="27.569" transform="translate(24.227 5.012)" fill="#4f076b"></rect><rect id="Retângulo_61" data-name="Retângulo 61" width="2.506" height="23.392" transform="translate(31.745 5.012)" fill="#4f076b"></rect><rect id="Retângulo_62" data-name="Retângulo 62" width="1.67" height="23.392" transform="translate(36.758 5.012)" fill="#4f076b"></rect><rect id="Retângulo_63" data-name="Retângulo 63" width="2.506" height="23.392" transform="translate(40.1 5.012)" fill="#4f076b"></rect><rect id="Retângulo_64" data-name="Retângulo 64" width="1.67" height="27.569" transform="translate(44.277 5.012)" fill="#4f076b"></rect><rect id="Retângulo_65" data-name="Retângulo 65" width="1.671" height="2.506" transform="translate(7.519 30.074)" fill="#4f076b"></rect><rect id="Retângulo_66" data-name="Retângulo 66" width="1.671" height="2.506" transform="translate(10.86 30.074)" fill="#4f076b"></rect><rect id="Retângulo_67" data-name="Retângulo 67" width="1.671" height="2.506" transform="translate(14.202 30.074)" fill="#4f076b"></rect><rect id="Retângulo_68" data-name="Retângulo 68" width="1.671" height="2.506" transform="translate(17.543 30.074)" fill="#4f076b"></rect><rect id="Retângulo_69" data-name="Retângulo 69" width="1.671" height="2.506" transform="translate(20.885 30.074)" fill="#4f076b"></rect><rect id="Retângulo_70" data-name="Retângulo 70" width="1.67" height="2.506" transform="translate(27.569 30.074)" fill="#4f076b"></rect><rect id="Retângulo_71" data-name="Retângulo 71" width="1.671" height="2.506" transform="translate(30.91 30.074)" fill="#4f076b"></rect><rect id="Retângulo_72" data-name="Retângulo 72" width="1.671" height="2.506" transform="translate(34.252 30.074)" fill="#4f076b"></rect><rect id="Retângulo_73" data-name="Retângulo 73" width="1.671" height="2.506" transform="translate(37.593 30.074)" fill="#4f076b"></rect><rect id="Retângulo_74" data-name="Retângulo 74" width="1.671" height="2.506" transform="translate(40.935 30.074)" fill="#4f076b"></rect><rect id="Retângulo_75" data-name="Retângulo 75" width="1.671" height="23.392" transform="translate(28.404 5.012)" fill="#4f076b"></rect></g></svg></div> 
					<div class="payment_li_text">
						<strong><span>Boleto</span></strong>
						<span class="text-light"><span>Bancário</span></span>
					</div>
					<div class="aqbank-arrow-right"><svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991"><path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"></path></svg></div>
					<span class="change-text"></span>
				</div>
				<span class="ticket-info-tool" style="display: none;">
					<span class="multi-valor-tooltip">
						<a href="#" class="tooltip-toggle">
							<svg xmlns="http://www.w3.org/2000/svg" class="img-tool-tip" width="20" height="21" viewBox="0 0 20 21">
								<g id="Grupo_4195" data-name="Grupo 4195" transform="translate(-1848 -373)">
									<circle id="Elipse_104" data-name="Elipse 104" cx="10" cy="10" r="10" transform="translate(1848 374)" fill="#2b2a2a"></circle>
									<text id="_" data-name="?" transform="translate(1855 388)" fill="#fff" font-size="14" font-family="SegoeUI, Segoe UI"><tspan x="0" y="0">?</tspan></text>
								</g>
							</svg>
						</a>
						<span class="tooltip-content"><span>O valor minímo para usar está forma é de R$ 10,00</span></span>
					</span>
				</span>
			</div>
			<?php endif; ?>
		</div>
		
		<div style="clear: both;"></div>
		
		<div class="aqbank-box-integral">
			
			<div class="payment-method-content payment-method-content-cc">
				<div id="aqbank_payment_card" class="aqbank_payment_card">
					<fieldset class="fieldset card_one payment items aqpago-box-all-payments ccardaqpago" id="payment_form_aqpago">
						
						<div class="box-select-card-title">
							<div class="icon-card-clean"><svg xmlns="http://www.w3.org/2000/svg" width="25.826" height="15.755" viewBox="0 0 25.826 15.755"><path id="Caminho_9540" data-name="Caminho 9540" d="M47.526,429.63H26.843a2.722,2.722,0,0,0-2.571,2.841v10.077a2.717,2.717,0,0,0,2.571,2.837H47.526a2.717,2.717,0,0,0,2.573-2.837V432.471A2.721,2.721,0,0,0,47.526,429.63Zm-13.249,11.7.32-.305-.309-.318a.188.188,0,1,1,.268-.263l.312.316.312-.309a.191.191,0,0,1,.27,0,.2.2,0,0,1,0,.27l-.316.307.309.32a.189.189,0,0,1-.27.263l-.314-.316-.312.307a.191.191,0,0,1-.268,0A.194.194,0,0,1,34.277,441.331Zm1.6,0,.314-.305-.3-.318a.188.188,0,1,1,.27-.263l.309.316.316-.309a.187.187,0,0,1,.266,0,.177.177,0,0,1,.051.118c0,.129-.02.118-.369.459l.309.32a.164.164,0,0,1,.048.109.275.275,0,0,0,0,.039.171.171,0,0,1-.057.116.191.191,0,0,1-.268,0l-.307-.316-.316.307a.188.188,0,0,1-.266,0A.191.191,0,0,1,35.878,441.331Zm1.387.107c.015-.1.075-.125.373-.412l-.309-.318a.19.19,0,0,1,.007-.27.185.185,0,0,1,.261.007l.309.316.318-.309c.184-.182.437.1.265.27l-.318.307.312.32a.191.191,0,0,1-.276.263L37.9,441.3l-.318.307a.189.189,0,0,1-.266,0,.149.149,0,0,1-.048-.109A.523.523,0,0,0,37.265,441.438Zm2.824-.715-.318.307.311.32c.176.176-.1.436-.272.263L39.5,441.3l-.312.307a.19.19,0,0,1-.266-.272l.314-.305-.309-.318a.19.19,0,0,1,.276-.263l.307.316.318-.309a.183.183,0,0,1,.263,0A.193.193,0,0,1,40.089,440.723Zm-10.928.781c0-.037,0,0,0-.057.011-.107.074-.132.371-.421-.318-.327-.36-.344-.358-.454a.186.186,0,0,1,.322-.127l.307.316.32-.309c.175-.182.443.1.261.27l-.314.307.307.32c.178.173-.088.436-.27.263L29.8,441.3l-.318.307A.189.189,0,0,1,29.162,441.5Zm-2.988-.173.32-.305c-.024-.024-.311-.31-.329-.336a.189.189,0,0,1,.287-.244l.311.316.316-.309a.188.188,0,1,1,.263.27c-.281.274-.208.2-.312.307,0,0,.2.2.311.32.173.176-.1.436-.276.263l-.307-.316-.316.307a.191.191,0,0,1-.268,0A.188.188,0,0,1,26.174,441.331Zm1.6,0,.316-.305-.068-.068-.153-.158-.09-.092a.2.2,0,0,1,.006-.27.192.192,0,0,1,.266.007c.108.11.3.305.311.316l.2-.2.114-.112a.191.191,0,0,1,.268,0,.2.2,0,0,1,0,.27l-.318.307c.371.382.347.323.36.468a.191.191,0,0,1-.323.114l-.305-.316-.32.307a.189.189,0,1,1-.263-.272Zm4.2-.608-.311.307.3.32c.178.171-.086.439-.272.263L31.4,441.3l-.318.307a.189.189,0,0,1-.263-.272l.316-.305-.311-.318a.189.189,0,0,1,.272-.263l.311.316.314-.309a.188.188,0,1,1,.263.27Zm15.8,0-.32.307.309.32c.18.176-.1.436-.266.263l-.312-.316-.312.307a.192.192,0,0,1-.27-.272l.318-.305-.307-.318a.188.188,0,1,1,.268-.263l.309.316.318-.309a.187.187,0,0,1,.266,0A.193.193,0,0,1,47.781,440.723Zm-2.769.608.314-.305-.307-.318a.188.188,0,0,1,.268-.263l.311.316.316-.309a.19.19,0,0,1,.266.27l-.318.307.309.32c.178.176-.1.436-.268.263l-.314-.316-.311.307A.19.19,0,0,1,45.012,441.331Zm-1.442,0,.318-.305-.309-.318a.188.188,0,1,1,.27-.263l.309.316.314-.309a.189.189,0,0,1,.32.1c-.015.156.053.068-.371.478.292.3.351.331.362.426a.191.191,0,0,1-.323.156l-.307-.316-.318.307A.19.19,0,0,1,43.57,441.331Zm-1.6,0,.318-.305-.309-.318a.188.188,0,1,1,.268-.263l.309.316.316-.309a.19.19,0,0,1,.266.27l-.318.307.307.32c.176.169-.092.439-.268.263l-.311-.316-.314.307a.19.19,0,1,1-.265-.272Zm4.434-3.16H43.342a1.475,1.475,0,0,1-1.476-1.472V435.31a1.474,1.474,0,0,1,1.476-1.47H46.4a1.474,1.474,0,0,1,1.474,1.47V436.7A1.474,1.474,0,0,1,46.4,438.171Z" transform="translate(-24.272 -429.63)" fill="#561271"></path></svg></div><span>Selecione o</span> <strong><span>cartão</span></strong>
						</div>
						
						<div class="box-select-card box-select-card-custom"></div>
						
						<div class="aqbank_payment_integral">
							<div class="aqbank_payment_integral_box">
								<div id="one-li-form-payment" class="li-form-payment" style="display: none;">
									<div class="li-position-card">
										<div><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40.439" height="55" viewBox="0 0 40.439 55"><defs><filter id="Caminho_9599" x="0" y="0" width="40.439" height="55" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="blur"></feGaussianBlur><feFlood flood-opacity="0.161"></feFlood><feComposite operator="in" in2="blur"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g id="Grupo_6541" data-name="Grupo 6541" transform="translate(-70.951 -387)"><g transform="matrix(1, 0, 0, 1, 70.95, 387)" filter="url(#Caminho_9599)"><path id="Caminho_9599-2" data-name="Caminho 9599" d="M18.5,0C30.893,0,40.939,8.283,40.939,18.5S30.893,37,18.5,37Z" transform="translate(-9.5 6)" fill="#561271"></path></g><text id="_1_" data-name="1ยบ" transform="translate(83 416)" fill="#fff" font-size="15" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">1º</tspan></text></g></svg></div>
									</div>
									<div class="li-number-card">
										<div class="top">
											<div id="img-one" class="img-flag"></div>
										</div>
										<div id="one-middle-number-card" class="middle-number-card"></div>
									</div>
									<div class="li-installment-card">
										<div class="top"><span>PARCELAS</span></div>
										<select class="select input-text parcelas" id="aqpago_one_installments">
											<?php for($i=1;$i<=$installments;$i++): ?>
												<option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ) ?>x</option>						
											<?php endfor; ?>
										</select>
									</div>
									<div class="li-amount-card">
										<div class="top"><span>VALOR</span></div>
										<div id="one-grand-total-view" class="amount-card"></div>
									</div>
									<a href="#onecard-modal" id="edit-one" rel="modal:open" class="edit-one"><svg xmlns="http://www.w3.org/2000/svg" width="21.711" height="20.482" viewBox="0 0 21.711 20.482"><g id="Edit_icon-icons.com_71853" transform="translate(-110.9 -133.444)"><g id="Grupo_6545" data-name="Grupo 6545" transform="translate(110.9 133.626)"><path id="Caminho_9602" data-name="Caminho 9602" d="M128.454,156.3H114.5a3.535,3.535,0,0,1-3.6-3.456V139.456A3.535,3.535,0,0,1,114.5,136h8.511a1.547,1.547,0,1,1,0,3.092H114.5a.371.371,0,0,0-.379.363v13.382a.371.371,0,0,0,.379.363h13.951a.371.371,0,0,0,.379-.363v-7.932a1.613,1.613,0,0,1,3.224,0v7.939A3.535,3.535,0,0,1,128.454,156.3Z" transform="translate(-110.9 -136)" fill="#561271"></path></g><g id="Grupo_6549" data-name="Grupo 6549" transform="translate(120.509 133.444)"><g id="Grupo_6546" data-name="Grupo 6546" transform="translate(1.454 2.003)"><rect id="Retângulo_1078" data-name="Retângulo 1078" width="3.617" height="8.243" transform="translate(2.475 8.279) rotate(-133.189)" fill="#561271"></rect></g><g id="Grupo_6547" data-name="Grupo 6547" transform="translate(7.895 0)"><path id="Caminho_9603" data-name="Caminho 9603" d="M324.331,133.619l1.727,1.84a.549.549,0,0,1-.021.775l-1.549,1.457L322,135.047l1.549-1.457A.558.558,0,0,1,324.331,133.619Z" transform="translate(-322 -133.444)" fill="#561271"></path></g><g id="Grupo_6548" data-name="Grupo 6548" transform="translate(0 8.055)"><path id="Caminho_9604" data-name="Caminho 9604" d="M211.923,246.8l2.473,2.636-3.5.8Z" transform="translate(-210.9 -246.8)" fill="#561271"></path></g></g></g></svg></a>
									<div id="one-card-bottom" class="bottom">
										<strong>1x</strong> <span>R$0,00</span>
									</div>
									<div class="text-edit">
										<span>VOCÊ PODE EDITAR O 1º PAGAMENTO</span>
									</div>
									
								</div>
								
								<div id="two-li-form-payment" class="li-form-payment" style="display: none;">
									<div class="li-position-card">
										<div><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="39.368" height="53.233" viewBox="0 0 39.368 53.233"><defs><filter id="Caminho_9599" x="0" y="0" width="39.368" height="53.233" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="blur"></feGaussianBlur><feFlood flood-opacity="0.161"></feFlood><feComposite operator="in" in2="blur"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g id="Grupo_6541" data-name="Grupo 6541" transform="translate(9 6)"><g transform="matrix(1, 0, 0, 1, -9, -6)" filter="url(#Caminho_9599)"><path id="Caminho_9599-2" data-name="Caminho 9599" d="M18.5,0C30.3,0,39.868,7.887,39.868,17.617S30.3,35.233,18.5,35.233Z" transform="translate(-9.5 6)" fill="#561271"></path></g><text id="_2_" data-name="2ยบ" transform="translate(3.172 21.612)" fill="#fff" font-size="13" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">2º</tspan></text></g></svg></div>
									</div>
									<div class="li-number-card">
										<div class="top">
											<div id="img-two" class="img-flag"></div>
										</div>
										<div id="two-middle-number-card" class="middle-number-card"></div>
									</div>
									<div class="li-installment-card">
										<div class="top"><span>PARCELAS</span></div>
										<select class="select input-text parcelas" id="aqpago_two_installments">
											<?php for($i=1;$i<=$installments;$i++): ?>
												<option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ) ?>x</option>						
											<?php endfor; ?>
										</select>
									</div>
									<div class="li-amount-card">
										<div class="top"><span>VALOR</span></div>
										<div id="two-grand-total-view" class="amount-card"></div>
									</div>
									<a href="#twocard-modal" rel="modal:open" id="edit-two" class="edit-one"><svg xmlns="http://www.w3.org/2000/svg" width="21.711" height="20.482" viewBox="0 0 21.711 20.482"><g id="Edit_icon-icons.com_71853" transform="translate(-110.9 -133.444)"><g id="Grupo_6545" data-name="Grupo 6545" transform="translate(110.9 133.626)"><path id="Caminho_9602" data-name="Caminho 9602" d="M128.454,156.3H114.5a3.535,3.535,0,0,1-3.6-3.456V139.456A3.535,3.535,0,0,1,114.5,136h8.511a1.547,1.547,0,1,1,0,3.092H114.5a.371.371,0,0,0-.379.363v13.382a.371.371,0,0,0,.379.363h13.951a.371.371,0,0,0,.379-.363v-7.932a1.613,1.613,0,0,1,3.224,0v7.939A3.535,3.535,0,0,1,128.454,156.3Z" transform="translate(-110.9 -136)" fill="#561271"></path></g><g id="Grupo_6549" data-name="Grupo 6549" transform="translate(120.509 133.444)"><g id="Grupo_6546" data-name="Grupo 6546" transform="translate(1.454 2.003)"><rect id="Retângulo_1078" data-name="Retângulo 1078" width="3.617" height="8.243" transform="translate(2.475 8.279) rotate(-133.189)" fill="#561271"></rect></g><g id="Grupo_6547" data-name="Grupo 6547" transform="translate(7.895 0)"><path id="Caminho_9603" data-name="Caminho 9603" d="M324.331,133.619l1.727,1.84a.549.549,0,0,1-.021.775l-1.549,1.457L322,135.047l1.549-1.457A.558.558,0,0,1,324.331,133.619Z" transform="translate(-322 -133.444)" fill="#561271"></path></g><g id="Grupo_6548" data-name="Grupo 6548" transform="translate(0 8.055)"><path id="Caminho_9604" data-name="Caminho 9604" d="M211.923,246.8l2.473,2.636-3.5.8Z" transform="translate(-210.9 -246.8)" fill="#561271"></path></g></g></g></svg></a>
									<div id="two-card-bottom" class="bottom">
										<strong>1x</strong> <span>R$0,00</span>
									</div>
									<div class="text-edit">
										<span>VOCÊ PODE EDITAR O 2º PAGAMENTO</span>
									</div>
									
								</div>
								
								<div id="three-li-form-payment" class="li-form-payment" style="display: none;">
									<div class="li-position-card">
										<div>
											<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="39.368" height="53.233" viewBox="0 0 39.368 53.233"><defs><filter id="Caminho_9599" x="0" y="0" width="39.368" height="53.233" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="blur"></feGaussianBlur><feFlood flood-opacity="0.161"></feFlood><feComposite operator="in" in2="blur"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g id="Grupo_6541" data-name="Grupo 6541" transform="translate(9 6)"><g transform="matrix(1, 0, 0, 1, -9, -6)" filter="url(#Caminho_9599)"><path id="Caminho_9599-2" data-name="Caminho 9599" d="M18.5,0C30.3,0,39.868,7.887,39.868,17.617S30.3,35.233,18.5,35.233Z" transform="translate(-9.5 6)" fill="#561271"></path></g><text id="_2_" data-name="2ยบ" transform="translate(3.172 21.612)" fill="#fff" font-size="13" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">2º</tspan></text></g></svg>
										</div>
									</div>
									<div class="li-number-card">
										<div class="icon-boleto-multi"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="41.77" height="27.569" viewBox="0 0 41.77 27.569"><defs><clipPath id="clip-path"></clipPath></defs><g id="Grupo_4161" data-name="Grupo 4161" transform="translate(-4.177 -5.012)"><rect id="Retângulo_55" data-name="Retângulo 55" width="1.67" height="27.569" transform="translate(4.177 5.012)" fill="#4f076b"></rect><rect id="Retângulo_56" data-name="Retângulo 56" width="2.506" height="23.392" transform="translate(8.354 5.012)" fill="#4f076b"></rect><rect id="Retângulo_57" data-name="Retângulo 57" width="1.671" height="23.392" transform="translate(13.367 5.012)" fill="#4f076b"></rect><rect id="Retângulo_58" data-name="Retângulo 58" width="2.506" height="23.392" transform="translate(16.709 5.012)" fill="#4f076b"></rect><rect id="Retângulo_59" data-name="Retângulo 59" width="1.671" height="23.392" transform="translate(20.885 5.012)" fill="#4f076b"></rect><rect id="Retângulo_60" data-name="Retângulo 60" width="1.67" height="27.569" transform="translate(24.227 5.012)" fill="#4f076b"></rect><rect id="Retângulo_61" data-name="Retângulo 61" width="2.506" height="23.392" transform="translate(31.745 5.012)" fill="#4f076b"></rect><rect id="Retângulo_62" data-name="Retângulo 62" width="1.67" height="23.392" transform="translate(36.758 5.012)" fill="#4f076b"></rect><rect id="Retângulo_63" data-name="Retângulo 63" width="2.506" height="23.392" transform="translate(40.1 5.012)" fill="#4f076b"></rect><rect id="Retângulo_64" data-name="Retângulo 64" width="1.67" height="27.569" transform="translate(44.277 5.012)" fill="#4f076b"></rect><rect id="Retângulo_65" data-name="Retângulo 65" width="1.671" height="2.506" transform="translate(7.519 30.074)" fill="#4f076b"></rect><rect id="Retângulo_66" data-name="Retângulo 66" width="1.671" height="2.506" transform="translate(10.86 30.074)" fill="#4f076b"></rect><rect id="Retângulo_67" data-name="Retângulo 67" width="1.671" height="2.506" transform="translate(14.202 30.074)" fill="#4f076b"></rect><rect id="Retângulo_68" data-name="Retângulo 68" width="1.671" height="2.506" transform="translate(17.543 30.074)" fill="#4f076b"></rect><rect id="Retângulo_69" data-name="Retângulo 69" width="1.671" height="2.506" transform="translate(20.885 30.074)" fill="#4f076b"></rect><rect id="Retângulo_70" data-name="Retângulo 70" width="1.67" height="2.506" transform="translate(27.569 30.074)" fill="#4f076b"></rect><rect id="Retângulo_71" data-name="Retângulo 71" width="1.671" height="2.506" transform="translate(30.91 30.074)" fill="#4f076b"></rect><rect id="Retângulo_72" data-name="Retângulo 72" width="1.671" height="2.506" transform="translate(34.252 30.074)" fill="#4f076b"></rect><rect id="Retângulo_73" data-name="Retângulo 73" width="1.671" height="2.506" transform="translate(37.593 30.074)" fill="#4f076b"></rect><rect id="Retângulo_74" data-name="Retângulo 74" width="1.671" height="2.506" transform="translate(40.935 30.074)" fill="#4f076b"></rect><rect id="Retângulo_75" data-name="Retângulo 75" width="1.671" height="23.392" transform="translate(28.404 5.012)" fill="#4f076b"></rect></g></svg></div>
									</div>
									<div class="li-installment-card">
										<div class="top"><span>PAGAR</span></div>
										<div class="middle-text"><span>À VISTA</span></div>
									</div>
									<div class="li-amount-card">
										<div class="top"><span>VALOR</span></div>
										<div id="ticket-grand-total-view" class="amount-card"></div>
									</div>
									<div id="ticket-card-bottom" class="bottom">
										<strong>&nbsp;</strong>
										<span></span>
									</div>
									<div class="text-edit">
										<span>PAGAMENTO VIA BOLETO BANCÁRIO</span>
									</div>
									
								</div>
								
							</div>
						</div>	

						<div class="card-box-all">
							
							<fieldset class="fieldset aqbank-checkout">
								<div class="card-box card-front">
									<div class="background-card-front">
										<div class="aqbank-ico-card-front"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="284" height="203.508" viewBox="0 0 284 203.508"><defs><filter id="a" x="0" y="0" width="284" height="184" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="b"></feGaussianBlur><feFlood flood-opacity="0.439"></feFlood><feComposite operator="in" in2="b"></feComposite><feComposite in="SourceGraphic"></feComposite></filter><linearGradient id="c" x1="0.957" y1="0.357" x2="-0.157" y2="0.705" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#fad07b"></stop><stop offset="1" stop-color="#fbb039"></stop></linearGradient><linearGradient id="d" x1="-1.93" y1="1.956" x2="0.264" y2="0.641" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#825523"></stop><stop offset="1" stop-color="#d0b371"></stop></linearGradient><linearGradient id="e" x1="-0.807" y1="3.217" x2="0.067" y2="1.4" xlink:href="#d"></linearGradient><linearGradient id="f" x1="-1.336" y1="3.036" x2="-0.133" y2="1.18" xlink:href="#d"></linearGradient><linearGradient id="g" x1="-0.13" y1="1.533" x2="0.745" y2="0.099" xlink:href="#d"></linearGradient><linearGradient id="h" x1="-0.332" y1="2.45" x2="0.813" y2="0.071" xlink:href="#d"></linearGradient><linearGradient id="i" x1="-2.002" y1="36.516" x2="-0.385" y2="13.236" xlink:href="#d"></linearGradient><linearGradient id="j" x1="-1.868" y1="34.588" x2="-0.251" y2="11.308" xlink:href="#d"></linearGradient><linearGradient id="k" x1="-0.734" y1="18.317" x2="0.883" y2="-5.026" xlink:href="#d"></linearGradient><linearGradient id="l" x1="-0.601" y1="16.372" x2="1.017" y2="-6.95" xlink:href="#d"></linearGradient><linearGradient id="m" x1="0.019" y1="0.769" x2="1.024" y2="0.207" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#fff"></stop><stop offset="1" stop-color="#666"></stop></linearGradient><radialGradient id="n" cx="0.5" cy="0.5" r="0.5" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#d8d8d8"></stop><stop offset="1" stop-color="#fff"></stop></radialGradient></defs><g transform="translate(-1531 -320.492)"><g transform="translate(20 80.492)"><g transform="matrix(1, 0, 0, 1, 1511, 240)" filter="url(#a)"><rect class="back-card-custom" width="266" height="166" rx="7" transform="translate(9 6)" fill="#561271"></rect></g><g transform="translate(1548.743 272.646)"><g transform="translate(71.16 72.75)"><path d="M93.735,39.307,92.7,36.141h.442l.493,1.562c.139.423.256.809.339,1.181h.009c.088-.367.223-.767.363-1.181l.535-1.562h.437l-1.134,3.166Z" transform="translate(-92.698 -36.118)" fill="#fff"></path><path d="M93.974,38.312l-.33.995h-.418L94.3,36.141h.493l1.079,3.166h-.437l-.335-.995Zm1.037-.321L94.7,37.08c-.07-.2-.121-.4-.167-.576h-.009c-.046.186-.1.381-.158.572l-.311.916Z" transform="translate(-90.771 -36.118)" fill="#fff"></path><path d="M93.895,36.141H94.3v2.822h1.353v.344H93.895Z" transform="translate(-88.33 -36.118)" fill="#fff"></path><path d="M94.781,36.141v3.166h-.409V36.141Z" transform="translate(-86.59 -36.118)" fill="#fff"></path><path d="M94.613,36.2a5.876,5.876,0,0,1,.869-.065,1.812,1.812,0,0,1,1.278.4,1.457,1.457,0,0,1,.451,1.139,1.682,1.682,0,0,1-.456,1.227,1.952,1.952,0,0,1-1.4.456,6.614,6.614,0,0,1-.739-.037Zm.409,2.8a2.58,2.58,0,0,0,.414.023,1.217,1.217,0,0,0,1.348-1.344A1.129,1.129,0,0,0,95.5,36.461a2.316,2.316,0,0,0-.479.042Z" transform="translate(-85.71 -36.136)" fill="#fff"></path><path d="M93.734,37.7h-.962v-.349h2.343V37.7h-.967v2.817h-.414Z" transform="translate(-92.428 -31.695)" fill="#fff"></path><path d="M93.761,37.353v1.325h1.529V37.353H95.7v3.166h-.414V39.036H93.761v1.483h-.409V37.353Z" transform="translate(-90.312 -31.695)" fill="#fff"></path><path d="M94.01,37.413a4.321,4.321,0,0,1,.786-.065,1.285,1.285,0,0,1,.916.26.783.783,0,0,1,.246.6.833.833,0,0,1-.6.809v.009a.792.792,0,0,1,.465.642,5.183,5.183,0,0,0,.242.869h-.418a3.987,3.987,0,0,1-.214-.758c-.093-.437-.26-.6-.632-.614h-.386v1.371H94.01Zm.409,1.441h.418c.437,0,.711-.237.711-.6,0-.409-.293-.586-.725-.59a1.681,1.681,0,0,0-.4.037Z" transform="translate(-87.911 -31.714)" fill="#fff"></path><path d="M94.966,37.353v1.873c0,.707.316,1.009.739,1.009.47,0,.767-.307.767-1.009V37.353h.414V39.2c0,.972-.511,1.371-1.195,1.371-.651,0-1.139-.372-1.139-1.353V37.353Z" transform="translate(-85.933 -31.695)" fill="#fff"></path></g><path class="chip-card" d="M111.853,55.608H82.426a4.994,4.994,0,0,1-4.993-4.993V25.521a4.994,4.994,0,0,1,4.993-4.993h29.427a4.956,4.956,0,0,1,3.528,1.464,5.118,5.118,0,0,1,1.209,1.948,5,5,0,0,1,.256,1.581V50.615A4.994,4.994,0,0,1,111.853,55.608Z" transform="translate(-77.238 -20.337)" fill="url(#c) !important;"></path><g transform="translate(193.217 7.899)"><g transform="translate(0 5.207)"><path d="M119.068,24.747a7.08,7.08,0,0,1,0,5.727,1,1,0,0,0,.344,1.32.969.969,0,0,0,1.32-.344,9.166,9.166,0,0,0,0-7.675.969.969,0,0,0-1.32-.344.984.984,0,0,0-.344,1.316Z" transform="translate(-118.953 -23.306)" fill="#fff"></path></g><g transform="translate(4.031 3.359)"><path d="M119.944,24.449a11.414,11.414,0,0,1,.916,5.355,12.1,12.1,0,0,1-.446,2.543c-.088.293-.181.586-.288.879-.047.126-.093.246-.144.372.1-.237-.014.033-.037.079a1.065,1.065,0,0,0,.367,1.409,1.038,1.038,0,0,0,1.409-.372,13.775,13.775,0,0,0,0-11.306,1.039,1.039,0,0,0-1.409-.367,1.056,1.056,0,0,0-.367,1.409Z" transform="translate(-119.821 -22.909)" fill="#fff"></path></g><g transform="translate(8.574 1.292)"><path d="M120.855,23.724a23.1,23.1,0,0,1,0,13.923c-.4,1.264,1.581,1.808,1.985.544a25,25,0,0,0,0-15.016c-.414-1.251-2.4-.716-1.985.549Z" transform="translate(-120.798 -22.464)" fill="#fff"></path></g><g transform="translate(13.012)"><path d="M121.807,23.529a27.3,27.3,0,0,1,0,16.266c-.418,1.348,1.692,1.925,2.111.581a29.431,29.431,0,0,0,0-17.429c-.418-1.339-2.529-.767-2.111.581Z" transform="translate(-121.752 -22.186)" fill="#fff"></path></g></g><g transform="translate(0.195 6.034)" style="mix-blend-mode:multiply;isolation:isolate"><g transform="translate(12.027 5.016)"><path d="M91.084,22.864H84.311a4.3,4.3,0,0,0-4.291,4.3v6.769a4.3,4.3,0,0,0,4.291,4.3h6.773a4.3,4.3,0,0,0,4.3-4.3V27.16A4.3,4.3,0,0,0,91.084,22.864Zm3.784,11.064a3.787,3.787,0,0,1-3.784,3.784H84.311a3.787,3.787,0,0,1-3.78-3.784V27.16a3.787,3.787,0,0,1,3.78-3.784h6.773a3.787,3.787,0,0,1,3.784,3.784Z" transform="translate(-80.02 -22.864)" fill="url(#d)"></path></g><g transform="translate(24.527)"><path d="M87,29.785A3.786,3.786,0,0,1,83.22,26h-.511A4.3,4.3,0,0,0,87,30.3h10.59v-.511Z" transform="translate(-82.709 -6.387)" fill="url(#e)"></path><path d="M83.969,23.045a4.415,4.415,0,0,0-1.26,3.036v1.492h.511V26.081a3.871,3.871,0,0,1,1.111-2.673A3.748,3.748,0,0,1,87,22.3h10.59v-.511H87A4.25,4.25,0,0,0,83.969,23.045Z" transform="translate(-82.709 -21.785)" fill="url(#f)"></path></g><path d="M88.023,22.3a3.791,3.791,0,0,1,3.784,3.784v1.144h.507V26.081a4.3,4.3,0,0,0-4.291-4.3H77.433V22.3Z" transform="translate(-77.433 -21.785)" fill="url(#g)"></path><path d="M91.059,29.044a4.49,4.49,0,0,0,.7-.916,4.37,4.37,0,0,0,.558-2.12l-.507,0a3.926,3.926,0,0,1-.5,1.873,3.721,3.721,0,0,1-.614.8,4.187,4.187,0,0,1-2.673,1.106H77.433V30.3h10.6A4.736,4.736,0,0,0,91.059,29.044Z" transform="translate(-77.433 -6.391)" fill="url(#h)"></path><g transform="translate(27.131 8.8)"><path d="M83.269,23.678v.511H95.551v-.511Z" transform="translate(-83.269 -23.678)" fill="url(#i)"></path></g><g transform="translate(27.131 15.123)"><path d="M83.269,25.038v.511H95.551v-.511Z" transform="translate(-83.269 -25.038)" fill="url(#j)"></path></g><g transform="translate(0 8.801)"><rect width="12.281" height="0.51" fill="url(#k)"></rect></g><g transform="translate(0 15.122)"><rect width="12.281" height="0.51" fill="url(#l)"></rect></g></g><g transform="translate(0)"><path d="M112.006,55.948H82.579a5.192,5.192,0,0,1-5.188-5.183V25.67a5.192,5.192,0,0,1,5.188-5.183h29.427A5.167,5.167,0,0,1,115.674,22a5.4,5.4,0,0,1,1.251,2.027,5.163,5.163,0,0,1,.265,1.641V50.765A5.188,5.188,0,0,1,112.006,55.948ZM82.579,20.873a4.807,4.807,0,0,0-4.8,4.8V50.765a4.807,4.807,0,0,0,4.8,4.8h29.427a4.8,4.8,0,0,0,4.8-4.8V25.67a4.667,4.667,0,0,0-.242-1.52,5.05,5.05,0,0,0-1.162-1.873,4.764,4.764,0,0,0-3.394-1.4Z" transform="translate(-77.391 -20.487)" fill="url(#m)"></path></g></g></g><ellipse cx="131" cy="8" rx="131" ry="8" transform="translate(1544 508)" fill="url(#n)"></ellipse></g></svg></div>
										
										<div class="card-number">0000 0000 0000 00000</div>
										<div class="card-valid">MM/AAAA</div>
										<div class="card-name"></div>
										<div class="card-flag">
											<div id="img-flag-card"></div>
										</div>
									</div>
									<div class="background-card-back">
										<div class="aqbank-ico-card-back"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="269.748" height="161.627" viewBox="0 0 269.748 161.627"><defs><filter id="a" x="0" y="0" width="269.748" height="161.627" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="b"></feGaussianBlur><feFlood flood-opacity="0.439"></feFlood><feComposite operator="in" in2="b"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g transform="translate(8737 17519.508)"><g transform="matrix(1, 0, 0, 1, -8737, -17519.51)" filter="url(#a)"><rect class="back-card-custom" width="251.748" height="143.627" rx="7" transform="translate(9 6)" fill="#561271"></rect></g><rect width="252" height="32" transform="translate(-8728 -17496)" fill="#363636"></rect><rect width="224" height="24" transform="translate(-8715 -17459)" fill="#fff"></rect><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17455.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17455.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17453.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17450.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17448.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17445.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17441.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17438.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17436.5)" fill="#f1f1f1"></path><path d="M140,.5H0v-1H140Z" transform="translate(-8712.5 -17443.5)" fill="#f1f1f1"></path><g transform="translate(-5.908 -115.126)"><path d="M-466.225,314.84a2.32,2.32,0,0,0-1.642.683l-.537.543.792,1.68a1.536,1.536,0,0,0,2.038.728.805.805,0,0,0,.128-.07l-.173-.166a.353.353,0,0,1-.013-.5.355.355,0,0,1,.5-.016l0,0,.192.185a1.527,1.527,0,0,0,.236-.818v-.728a1.527,1.527,0,0,0-1.527-1.527Z" transform="translate(-8088.653 -17646.525)" fill="#747474"></path><path d="M-476.292,318.425l1.341-1.354-.326-.677Z" transform="translate(-8082.872 -17647.664)" fill="#747474"></path><path d="M-476.521,292.809h-.888V291.62a4.155,4.155,0,0,0-4.151-4.158h-.007a4.153,4.153,0,0,0-4.152,4.154v1.192h-.894a.294.294,0,0,0-.294.294h0v7.423a1.186,1.186,0,0,0,1.184,1.188h8.313a1.186,1.186,0,0,0,1.188-1.184V293.1a.294.294,0,0,0-.294-.294Zm-7.416-1.189a2.37,2.37,0,0,1,2.4-2.338h.006a2.372,2.372,0,0,1,2.338,2.338v1.188h-4.746Zm6.285,7.525a.373.373,0,0,1-.262.108.353.353,0,0,1-.243-.1l-.377-.364a2.137,2.137,0,0,1-1.252.4,2.169,2.169,0,0,1-1.961-1.239l-.7-1.469-2.587,2.614a.3.3,0,0,1-.223.1.321.321,0,0,1-.326-.315v-.005a.256.256,0,0,1,.039-.141l2.21-4.42a.318.318,0,0,1,.281-.179h.006a.315.315,0,0,1,.287.185l.5,1.06.364-.371a2.973,2.973,0,0,1,2.1-.875,2.167,2.167,0,0,1,2.164,2.165v.728a2.169,2.169,0,0,1-.409,1.265l.364.351a.353.353,0,0,1,.014.5h0Z" transform="translate(-8075.092 -17626.463)" fill="#747474"></path></g></g></svg></div>
										
										<input type="text" id="card-code" value="***" placeholder="***" readonly="" maxlength="4" class="cvv-code">
									</div>
								</div>
							</fieldset>
							
							<fieldset class="fieldset aqpago-box-card aqpago-box-card-checkout">
								<div id="installment-modal" class="modal">
									<div class="installment-view"></div>
								</div>
								
								<div class="aqbank-installment-description">
									<a href="#installment-modal" id="installment-view" rel="modal:open" class="link-installment-view"><?php echo esc_html(__('Ver todas as parcelas', 'woocommerce')); ?></a><br/>
									<span class="description-installment"></span>
								</div>
								
								<div class="aqbank-box-parcelas padding-right-5">
									<div class="aqbank-round-parcelas">
										<div class="text-parcelas"><span>TOTAL DE PARCELAS</span></div>
										<div class="aqbank-buttons-parcelas">
											<ul>
												<li>
													<span class="icon-sub" onClick="return AQPAGO.setOrderSub('aqpago-select-installments');" title="Menos"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="47.856" viewBox="0 0 47.856 47.856"><defs><filter id="Elipse_109" x="0" y="0" width="47.856" height="47.856" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="blur"></feGaussianBlur><feFlood flood-opacity="0.161"></feFlood><feComposite operator="in" in2="blur"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g id="Grupo_4193" data-name="Grupo 4193" transform="translate(9 6)"><g transform="matrix(1, 0, 0, 1, -9, -6)" filter="url(#Elipse_109)"><circle id="Elipse_109-2" data-name="Elipse 109" cx="14.928" cy="14.928" r="14.928" transform="translate(9 6)" fill="#68118a"></circle></g><line id="Linha_15" data-name="Linha 15" x2="12.796" transform="translate(8.104 14.928)" fill="none" stroke="#fff" stroke-width="2"></line></g></svg></span>
												</li>
												<li class="number number-aqpago_installments">
													<select class="select input-text card-select aqpago-select-installments" id="aqpago_installments">
														<?php for($i=1;$i<=$installments;$i++): ?>
															<option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ) ?>x</option>						
														<?php endfor; ?>
													</select>						
												</li>
												<li>
													<span class="icon-plus" onClick="return AQPAGO.setOrderSum('aqpago-select-installments');" title="Mais"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="47.856" viewBox="0 0 47.856 47.856"><defs><filter id="Elipse_110" x="0" y="0" width="47.856" height="47.856" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="blur"></feGaussianBlur><feFlood flood-opacity="0.161"></feFlood><feComposite operator="in" in2="blur"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g id="Grupo_4194" data-name="Grupo 4194" transform="translate(9 6)"><g transform="matrix(1, 0, 0, 1, -9, -6)" filter="url(#Elipse_110)"><circle id="Elipse_110-2" data-name="Elipse 110" cx="14.928" cy="14.928" r="14.928" transform="translate(9 6)" fill="#68118a"></circle></g><g id="Grupo_4192" data-name="Grupo 4192" transform="translate(8.53 8.53)"><line id="Linha_17" data-name="Linha 17" x2="12.796" transform="translate(0 6.398)" fill="none" stroke="#fff" stroke-width="2"></line><line id="Linha_18" data-name="Linha 18" x2="12.796" transform="translate(6.398 0) rotate(90)" fill="none" stroke="#fff" stroke-width="2"></line></g></g></svg></span>
												</li>
											</ul>
										</div>
										
									</div>
								</div>
								
								<div id="aqbank-valor-intergal" class="aqbank-box-parcelas padding-left-5">
									<div class="aqbank-box-price">
										<div class="text-parcelas"><span>TOTAL A SER PAGO</span></div>
										<span class="aqbank-card-grand-total"></span>
									</div>
								</div>
								
								<div id="aqbank-multi-pagamento-valor" class="aqbank-box-parcelas aqbank-multi-pagamento-valor">
									<div class="aqbank-round-parcelas">
										<span class="multi-valor-tooltip">
											<a href="#" class="tooltip-toggle">
												<svg xmlns="http://www.w3.org/2000/svg" class="img-tool-tip" width="20" height="21" viewBox="0 0 20 21">
												  <g id="Grupo_4195" data-name="Grupo 4195" transform="translate(-1848 -373)">
													<circle id="Elipse_104" data-name="Elipse 104" cx="10" cy="10" r="10" transform="translate(1848 374)" fill="#2b2a2a"/>
													<text id="_" data-name="?" transform="translate(1855 388)" fill="#fff" font-size="14" font-family="SegoeUI, Segoe UI"><tspan x="0" y="0">?</tspan></text>
												  </g>
												</svg>
											</a>
											<span class="tooltip-content"><span>Digite o valor que irá pagar neste cartão</span></span>
										</span>
										<div class="text-parcelas"><span>Valor do Pagamento</span></div>
										<span class="currency-valor">R$</span>
										<input type="tex" class="aqbank-input-valor" id="aqpago_cc_multiple_val" maxlength="14">
										
										<div class="img-edit"><svg xmlns="http://www.w3.org/2000/svg" width="21.711" height="20.482" viewBox="0 0 21.711 20.482"><g id="Edit_icon-icons.com_71853" transform="translate(-110.9 -133.444)"><g id="Grupo_6545" data-name="Grupo 6545" transform="translate(110.9 133.626)"><path id="Caminho_9602" data-name="Caminho 9602" d="M128.454,156.3H114.5a3.535,3.535,0,0,1-3.6-3.456V139.456A3.535,3.535,0,0,1,114.5,136h8.511a1.547,1.547,0,1,1,0,3.092H114.5a.371.371,0,0,0-.379.363v13.382a.371.371,0,0,0,.379.363h13.951a.371.371,0,0,0,.379-.363v-7.932a1.613,1.613,0,0,1,3.224,0v7.939A3.535,3.535,0,0,1,128.454,156.3Z" transform="translate(-110.9 -136)" fill="#561271"></path></g><g id="Grupo_6549" data-name="Grupo 6549" transform="translate(120.509 133.444)"><g id="Grupo_6546" data-name="Grupo 6546" transform="translate(1.454 2.003)"><rect id="Retângulo_1078" data-name="Retângulo 1078" width="3.617" height="8.243" transform="translate(2.475 8.279) rotate(-133.189)" fill="#561271"></rect></g><g id="Grupo_6547" data-name="Grupo 6547" transform="translate(7.895 0)"><path id="Caminho_9603" data-name="Caminho 9603" d="M324.331,133.619l1.727,1.84a.549.549,0,0,1-.021.775l-1.549,1.457L322,135.047l1.549-1.457A.558.558,0,0,1,324.331,133.619Z" transform="translate(-322 -133.444)" fill="#561271"></path></g><g id="Grupo_6548" data-name="Grupo 6548" transform="translate(0 8.055)"><path id="Caminho_9604" data-name="Caminho 9604" d="M211.923,246.8l2.473,2.636-3.5.8Z" transform="translate(-210.9 -246.8)" fill="#561271"></path></g></g></g></svg></div>
										
									</div>
								</div>
								
								<div style="clear:both;"></div>
								
								<div class="field field-name-lastname required">
									<label class="label" for="aqpago_cc_number">
										<span><span>Número do cartão</span></span>
									</label>
									<div class="control">
										<input type="text" step="0" maxlength="19" name="cc_number" class="input-text cc-card-number" placeholder="0000 0000 0000 0000" autocomplete="function (ns) {
									var storage = getEvents(this);

									if (!storage) {
										return this;
									}

									storage.forEach(function (handlers, name) {
										handlers = handlers.filter(function (handler) {
											return !ns ? false : handler.ns !== ns;
										});

										handlers.length ?
											storage.set(name, handlers) :
											storage.delete(name);
									});

									return this;
								}" id="aqpago_cc_number" title="Número do cartão">
									</div>
								</div>
								
								
								<div>
								
									<div class="field valid_month_checkout required">
										<label for="valid_month" class="label">
											<span><span>Validade</span></span>
										</label>
										<div class="control"> 
												
												<select name="cc_exp_month" id="aqpago_expiration" class="select select-month input-text card-select valid_month">
													<option value="">Mês</option>
													<option value="1">01 - janeiro</option>
													<option value="2">02 - fevereiro</option>
													<option value="3">03 - março</option>
													<option value="4">04 - abril</option>
													<option value="5">05 - maio</option>
													<option value="6">06 - junho</option>
													<option value="7">07 - julho</option>
													<option value="8">08 - agosto</option>
													<option value="9">09 - setembro</option>
													<option value="10">10 - outubro</option>
													<option value="11">11 - novembro</option>
													<option value="12">12 - dezembro</option>
												</select>
												
												<select name="cc_exp_year" id="aqpago_expiration_yr" class="select select-year card-select valid_year">
													<option value="">Ano</option>
													<?php for($i=date('Y');$i<=(date('Y')+15);$i++): ?>
														<option value="<?php echo esc_attr( $i ) ?>"><?php echo esc_html( $i ) ?></option>
													<?php endfor; ?>
												</select>
										
										</div>
									</div>
									
									<div class="field card_cvv_img">
										<div class="aqbank-card-cvv"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="116.901" height="74.425" viewBox="0 0 116.901 74.425"><defs><filter id="Retângulo_906" x="0" y="0" width="116.901" height="74.425" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"></feOffset><feGaussianBlur stdDeviation="3" result="blur"></feGaussianBlur><feFlood flood-opacity="0.439"></feFlood><feComposite operator="in" in2="blur"></feComposite><feComposite in="SourceGraphic"></feComposite></filter></defs><g id="Grupo_5025" data-name="Grupo 5025" transform="translate(-52.5 -1237.492)"><g id="Grupo_4139" data-name="Grupo 4139" transform="translate(61.5 1243.492)"><g transform="matrix(1, 0, 0, 1, -9, -6)" filter="url(#Retângulo_906)"><rect class="card-background-svg" id="Retângulo_906-2" data-name="Retângulo 906" width="98.901" height="56.425" rx="7" transform="translate(9 6)" fill="#561271"></rect></g><rect id="Retângulo_1107" data-name="Retângulo 1107" width="99" height="12" transform="translate(0 7.871)" fill="#434343"></rect><rect id="Retângulo_1108" data-name="Retângulo 1108" width="28" height="12" rx="3" transform="translate(64 26.871)" fill="#fff"></rect></g></g></svg></div>
									</div>
									
									<div class="field field-name-code-checkout required">
										<label class="label" for="aqpago_cc_cid">
											<span><span>Código</span></span>
										</label>
										<div class="control">
											<input type="text" name="cc_cid" id="aqpago_cc_cid" maxlength="4" autocomplete="off" class="input-number cvv"title="Codigo *" >			
										</div>
									</div>
									
								</div>
								
								<div style="clear: both"></div>
								
								<div class="field field-name-name_card required">
									<label class="label" for="aqpago_cc_owner">
										<span><span>Nome impresso cartão</span></span>
									</label>
									<div class="control">
										<input style="text-transform: uppercase;" type="text" name="cc_owner" class="input-text aqpago-cc-owner" autocomplete="function (ns) {
									var storage = getEvents(this);

									if (!storage) {
										return this;
									}

									storage.forEach(function (handlers, name) {
										handlers = handlers.filter(function (handler) {
											return !ns ? false : handler.ns !== ns;
										});

										handlers.length ?
											storage.set(name, handlers) :
											storage.delete(name);
									});

									return this;
								}" id="aqpago_cc_owner" title="Nome impresso cartão" placeholder="Nome impresso cartão">
									</div>
								</div>

								<div style="clear:both;"></div>

								<div class="field field-name-documento documento-one required">
									<label class="label" for="aqpago_documento">
										<span><span>CPF dono do cartão</span></span>
									</label>
									<div class="control">
										<input type="text" name="cc_documento" placeholder="000.000.000-00" title="CPF dono do cartão" class="input-text required-entry aqpago-document-input" id="aqpago_documento" maxlength="14">
									</div>
								</div>
								
								<div style="clear:both;"></div>
								
								<div class="actions-toolbar grand-total" style="display: none;">
									<span>Você vai pagar</span>
									
									
									<div class="aqbank-grandtotal-card aqbank-relative">
										<span>R$<?php echo esc_html( number_format($cart_total, 2, ',', '.') ) ?></span>
										<div id="parcela-integral" class="aqbank-total-parcelas">/ 1x</div>
									</div>
									
								</div>
										
								<div id="submit-action" class="actions-toolbar" style="display: none;">
									<button data-role="review-save" type="submit" class="action primary checkout aqbank_place_order_button" title="Finalizar Pedido">
										<span>Finalizar Pedido</span>
									</button>
								</div>
								
								<div id="one-action" class="actions-toolbar" style="display: none;">
									<button data-role="review-save" type="button" onClick="return AQPAGO.setCardData('one');" class="action primary checkout aqbank_place_order_button" title="PRÓXIMO">
										<span>PRÓXIMO</span>
									</button>
								</div>
								
								<div id="multi-actions" class="actions-toolbar multi-actions" style="display: none;">
									<button data-role="review-save" type="button" onClick="return AQPAGO.setCardData('one');" class="action primary checkout aqbank_place_order_button" title="PRÓXIMO CARTÃO">
										<span>PRÓXIMO CARTÃO</span>
									</button>
								</div>	
								
								<div id="multi-actions-two" class="actions-toolbar multi-actions" style="display: none;">
									<button data-role="review-save" type="button" onClick="return AQPAGO.setCardData('two');" class="action primary checkout aqbank_place_order_button" title="PRÓXIMO">
										<span>PRÓXIMO</span>
									</button>
								</div>
								
								<div id="multi-actions-one-ticket" class="actions-toolbar multi-actions" style="display: none;">
									<button data-role="review-save" type="button" onClick="return AQPAGO.setCardData('one');" class="action primary checkout aqbank_place_order_button" title="PRÓXIMO">
										<span>PRÓXIMO</span>
									</button>
								</div>
								
								<div style="clear:both;margin-bottom: 20px;"></div>
								
							</fieldset>	
							
						</div>
							
						<div class="aqbank_custom_informations">
							<div class="card-view-address">
								<div class="resume-title">ENDEREÇO DE ENVIO</div>
								<div class="box">
									<div class="icon">
										<div class="round">
											<div>
												<svg xmlns="http://www.w3.org/2000/svg" width="12.746" height="20.183" viewBox="0 0 12.746 20.183"><g id="Grupo_6409" data-name="Grupo 6409" transform="translate(-0.001 0.001)"><path id="Caminho_9514" data-name="Caminho 9514" d="M399.267,231.171c-.037.056-.073.112-.109.168,1.273.388,2.1,1.026,2.1,1.746,0,1.184-2.243,2.144-5.01,2.144s-5.01-.96-5.01-2.144c0-.713.813-1.344,2.064-1.734l-.111-.172c-2.185.331-3.412,1.6-3.308,2.879.126,1.544,2.285,3.007,6.365,3.007s6.211-1.361,6.365-3.007C402.736,232.777,401.721,231.63,399.267,231.171Z" transform="translate(-389.879 -216.883)" fill="#520a6d"></path><path id="Caminho_9515" data-name="Caminho 9515" d="M396.251,233.915s5.8-5.78,5.8-11.214a5.806,5.806,0,1,0-11.611,0C390.445,228.33,396.251,233.915,396.251,233.915ZM391.639,222.7a4.612,4.612,0,1,1,4.612,4.622A4.618,4.618,0,0,1,391.639,222.7Z" transform="translate(-389.879 -216.883)" fill="#520a6d"></path></g></svg>
											</div>
										</div>
									</div>
									<div class="box-body">
										<div class="address-cep"></div>
										<div class="address-line-one"><span></span><span></span><span> </span><span></span><span></span><span></span></div>
									</div>
									<div class="address-link">EDITAR</div>
								</div>
							</div>
							
							<div class="shipping-option">
								<div class="resume-title">MÉTODO DE ENVIO</div>
								<div class="shipping-option-li"><div class="aqbank-shipping-option"><div class="aqbank-shipping-check"><div class="aqbank-shipping-check-round"><svg xmlns="http://www.w3.org/2000/svg" width="20.762" height="20.811" viewBox="0 0 20.762 20.811"><path id="Caminho_9519" data-name="Caminho 9519" d="M1151.334,217.117a10.405,10.405,0,1,0,10.382,10.4A10.392,10.392,0,0,0,1151.334,217.117Zm2.62,12.64-2.041,2.11-1.881,1.943a.944.944,0,0,1-1.338.02L1146.81,232l-1.243-1.208-1.8-1.752a.95.95,0,0,1-.02-1.341l1.055-1.091a.945.945,0,0,1,1.339-.02l3.161,3.073,7.509-7.761a.945.945,0,0,1,1.339-.02l1.088,1.057a.952.952,0,0,1,.02,1.342Z" transform="translate(-1140.953 -217.117)" fill="#4f076b"></path></svg></div></div><div class="aqbank-shipping-text"><strong>GRÁTIS -</strong> Frete grátis</div><div class="aqbank-shipping-price">R$0,00</div></div><div class="shipping-arrow"></div></div>
							</div>
							
							<div class="grandtotal-resume">
								<div class="resume-title">
									<span>DETALHES DE COBRANÇA</span>
								</div>
						
								<div id="grand_total_aqpago" class="grandtotal-box">
									
									<div class="iwd_opc_review_total">
										<div class="iwd_opc_review_total_cell">Valor em compra</div>
										<div class="iwd_opc_review_total_cell"></div>
									</div>

									<div class="iwd_opc_review_total iwd_opc_review_total_shipping">
										<div class="iwd_opc_review_total_cell">Frete</div>
										<div class="iwd_opc_review_total_cell"></div>
									</div>

									<div class="iwd_opc_review_total iwd_opc_grand_total">
										<div class="iwd_opc_review_total_cell">Total</div>
										<div class="iwd_opc_review_total_cell"></div>
									</div>
								</div>
							</div>
						</div>

						<div id="button-finished" class="actions-toolbar multi-actions" style="display: none;">
							<button data-role="review-save" type="button" class="action primary checkout aqbank_place_order_button" title="FINALIZAR PEDIDO">
								<span>FINALIZAR PEDIDO</span>
							</button>
							<div style="clear:both;margin-bottom: 20px;"></div>
						</div>

					</fieldset>

				</div>
			</div>

			<div style="clear: both;"></div>

			<div class="payment-method-content payment-method-content-ticket">
				<div class="aqbank_payment_boleto">

					<fieldset class="fieldset ticket payment items boleto aqpago" id="payment_boleto_form_aqpago">
						<fieldset class="fieldset aqpago-box-boleto aqpago-box-boleto-checkout">	
							
							<div class="actions-toolbar grand-total aqbank-grand-total aqbank-grand-total-boleto">
								
								<div class="aqbank-grandtotal-card aqbank-relative">
									<span>R$<?php echo esc_html( number_format($cart_total, 2, ',', '.') ) ?></span>
								</div>
								<span class="valor-boleto">VALOR DO BOLETO</span>
								
								<div class="boleto-relogio"><svg xmlns="http://www.w3.org/2000/svg" width="61.068" height="61.209" viewBox="0 0 61.068 61.209"><g id="Grupo_5097" data-name="Grupo 5097" transform="translate(-406.185 -600.67)" opacity="0.2"><path id="Caminho_8424" data-name="Caminho 8424" d="M434.95,643.495a1.946,1.946,0,0,0-1.942,1.949v6.908a1.942,1.942,0,1,0,3.885,0v-6.908A1.946,1.946,0,0,0,434.95,643.495Z" transform="translate(1.897 3.029)" fill="#561271"></path><path id="Caminho_8425" data-name="Caminho 8425" d="M436.719,661.879a30.6,30.6,0,1,0-30.534-30.6A30.6,30.6,0,0,0,436.719,661.879Zm0-57.4a26.794,26.794,0,1,1-26.733,26.795A26.795,26.795,0,0,1,436.719,604.481Z" transform="translate(0 0)" fill="#561271"></path><path id="Caminho_8426" data-name="Caminho 8426" d="M421.707,629.765a1.946,1.946,0,0,0-1.944-1.947H412.87a1.947,1.947,0,0,0,0,3.893h6.892A1.945,1.945,0,0,0,421.707,629.765Z" transform="translate(0.335 1.92)" fill="#561271"></path><path id="Caminho_8427" data-name="Caminho 8427" d="M435.1,635.334a4.764,4.764,0,0,0,4.468-6.371l12.863-12.893a1.548,1.548,0,0,0-2.186-2.192l-12.631,12.659a4.755,4.755,0,1,0-2.513,8.8Z" transform="translate(1.709 0.902)" fill="#561271"></path><path id="Caminho_8428" data-name="Caminho 8428" d="M434.95,616.491a1.945,1.945,0,0,0,1.942-1.948v-6.908a1.942,1.942,0,1,0-3.885,0v6.908A1.945,1.945,0,0,0,434.95,616.491Z" transform="translate(1.897 0.355)" fill="#561271"></path><path id="Caminho_8429" data-name="Caminho 8429" d="M448.648,629.765a1.945,1.945,0,0,0,1.944,1.947h6.892a1.947,1.947,0,0,0,0-3.893h-6.892A1.946,1.946,0,0,0,448.648,629.765Z" transform="translate(3.003 1.92)" fill="#561271"></path></g></svg></div>
							</div>
							
							
							
							<div class="actions-toolbar aqbank-infos">
								<span>A compensação do boleto pode levar até 2 dias úteis.</span>
							</div>	
							
							<div class="actions-toolbar aqbank-infos">
								<div class="aqbank-contactar">
									<span>VOCÊ SERÁ NOTIFICADO ATRAVÉS DESTES CONTATOS</span>
								</div>	
							</div>	
							
							<div class="actions-toolbar aqbank-infos">
								<div class="aqbank-contactar">
									<div class="phone">
										<div class="img">
											<div class="boleto-phone"><svg id="Grupo_4190" data-name="Grupo 4190" xmlns="http://www.w3.org/2000/svg" width="16.957" height="27.318" viewBox="0 0 16.957 27.318"><path id="Caminho_8374" data-name="Caminho 8374" d="M368.939,215.851c-.232-.015-.423.121-.42.284a.37.37,0,0,0,.382.311,5.913,5.913,0,0,1,5.512,5.526c.014.207.146.381.31.382s.3-.188.284-.421A6.51,6.51,0,0,0,368.939,215.851Z" transform="translate(-358.051 -215.85)" fill="#561271"></path><path id="Caminho_8375" data-name="Caminho 8375" d="M368.907,217.489a.332.332,0,0,0-.4.279.353.353,0,0,0,.344.313,3.858,3.858,0,0,1,3.491,3.5.353.353,0,0,0,.312.345.332.332,0,0,0,.279-.4A4.448,4.448,0,0,0,368.907,217.489Z" transform="translate(-358.053 -215.41)" fill="#561271"></path><path id="Caminho_8376" data-name="Caminho 8376" d="M370.4,219.218l-7.536.005a2.609,2.609,0,0,0-2.6,2.606l.015,17.831a2.609,2.609,0,0,0,2.6,2.6l7.536-.005a2.61,2.61,0,0,0,2.6-2.608L373,221.821A2.608,2.608,0,0,0,370.4,219.218Zm-5.793,1.246,4.049,0a.2.2,0,0,1,.2.2h0a.2.2,0,0,1-.2.2l-4.05,0a.2.2,0,0,1,0-.4Zm-.528,20.058-1.985,0a.206.206,0,0,1,0-.411l1.986,0a.206.206,0,0,1,0,.412Zm2.571.9a1.108,1.108,0,1,1,1.1-1.109A1.107,1.107,0,0,1,366.652,241.425Zm4.539-.9-1.986,0a.206.206,0,0,1,0-.411l1.986,0a.206.206,0,0,1,0,.412Zm.675-1.939H361.41V222.054h10.456Z" transform="translate(-360.269 -214.944)" fill="#561271"></path></svg></div>
										</div>
										<div class="phone-text"></div>
									</div>
									<div class="email">
										<div class="img">
											<div class="boleto-email"><svg id="Grupo_4189" data-name="Grupo 4189" xmlns="http://www.w3.org/2000/svg" width="23.437" height="18.939" viewBox="0 0 23.437 18.939"><path id="Caminho_8372" data-name="Caminho 8372" d="M231.36,225.138a5.276,5.276,0,0,1-.845-.072V232.9l-5.385-4.334a5.114,5.114,0,0,0-1.447-.817,5.086,5.086,0,0,0,1.447-.816l3.4-2.737a4.217,4.217,0,0,1-1.06-1.3l-3.392,2.729a3.355,3.355,0,0,1-4.232,0l-4.634-3.729h11.866a5.286,5.286,0,0,1-.169-1.682H214.095a2.367,2.367,0,0,0-2.362,2.367v10.8a2.368,2.368,0,0,0,2.362,2.368h15.736a2.369,2.369,0,0,0,2.363-2.368v-8.308A5.135,5.135,0,0,1,231.36,225.138ZM213.411,232.9V222.6l5.386,4.334a5.067,5.067,0,0,0,1.446.816,5.1,5.1,0,0,0-1.446.817Zm1.239,1.161,5.2-4.183a3.352,3.352,0,0,1,4.232,0l5.2,4.183Z" transform="translate(-211.733 -216.803)" fill="#561271"></path><path id="Caminho_8373" data-name="Caminho 8373" d="M230.982,216.9a3.737,3.737,0,1,0,3.728,3.736A3.732,3.732,0,0,0,230.982,216.9Zm-.027,6.477-.39-2.31-2.281-.43,4.659-1.879Z" transform="translate(-211.274 -216.901)" fill="#561271"></path></svg></div>
										</div>
										<div class="email-text"></div>
									</div>
									
								</div>
							</div>
							
							<div class="aqbank_custom_informations">
								<div class="card-view-address">
									<div class="resume-title">ENDEREÇO DE ENVIO</div>
									<div class="box">
										<div class="icon">
											<div class="round">
												<div><svg xmlns="http://www.w3.org/2000/svg" width="12.746" height="20.183" viewBox="0 0 12.746 20.183"><g id="Grupo_6409" data-name="Grupo 6409" transform="translate(-0.001 0.001)"><path id="Caminho_9514" data-name="Caminho 9514" d="M399.267,231.171c-.037.056-.073.112-.109.168,1.273.388,2.1,1.026,2.1,1.746,0,1.184-2.243,2.144-5.01,2.144s-5.01-.96-5.01-2.144c0-.713.813-1.344,2.064-1.734l-.111-.172c-2.185.331-3.412,1.6-3.308,2.879.126,1.544,2.285,3.007,6.365,3.007s6.211-1.361,6.365-3.007C402.736,232.777,401.721,231.63,399.267,231.171Z" transform="translate(-389.879 -216.883)" fill="#520a6d"></path><path id="Caminho_9515" data-name="Caminho 9515" d="M396.251,233.915s5.8-5.78,5.8-11.214a5.806,5.806,0,1,0-11.611,0C390.445,228.33,396.251,233.915,396.251,233.915ZM391.639,222.7a4.612,4.612,0,1,1,4.612,4.622A4.618,4.618,0,0,1,391.639,222.7Z" transform="translate(-389.879 -216.883)" fill="#520a6d"></path></g></svg></div>
											</div>
										</div>
										<div class="box-body">
											<div class="address-cep"></div>
											
											<div class="address-line-one"></div>
										</div>
										<div class="address-link">EDITAR</div>
									</div>
								</div>
								
								<div class="shipping-option">
									<div class="resume-title">MÉTODO DE ENVIO</div>
									
									<div class="shipping-option-li"><div class="aqbank-shipping-option"><div class="aqbank-shipping-check"><div class="aqbank-shipping-check-round"><svg xmlns="http://www.w3.org/2000/svg" width="20.762" height="20.811" viewBox="0 0 20.762 20.811"><path id="Caminho_9519" data-name="Caminho 9519" d="M1151.334,217.117a10.405,10.405,0,1,0,10.382,10.4A10.392,10.392,0,0,0,1151.334,217.117Zm2.62,12.64-2.041,2.11-1.881,1.943a.944.944,0,0,1-1.338.02L1146.81,232l-1.243-1.208-1.8-1.752a.95.95,0,0,1-.02-1.341l1.055-1.091a.945.945,0,0,1,1.339-.02l3.161,3.073,7.509-7.761a.945.945,0,0,1,1.339-.02l1.088,1.057a.952.952,0,0,1,.02,1.342Z" transform="translate(-1140.953 -217.117)" fill="#4f076b"></path></svg></div></div><div class="aqbank-shipping-text"><strong>GRÁTIS -</strong> Frete grátis</div><div class="aqbank-shipping-price">R$0,00</div></div><div class="shipping-arrow"></div></div>
	
								</div>
								<div class="grandtotal-resume">
									<div class="resume-title">
										<span>DETALHES DE COBRANÇA</span>
									</div>
						
									<div id="grand_total_aqpago" class="grandtotal-box">
												
										<div class="iwd_opc_review_total">
											<div class="iwd_opc_review_total_cell">Valor em compra</div>
											<div class="iwd_opc_review_total_cell"></div>
										</div>

										<div class="iwd_opc_review_total iwd_opc_review_total_shipping">
											<div class="iwd_opc_review_total_cell">Frete</div>
											<div class="iwd_opc_review_total_cell"></div>
										</div>

										<div class="iwd_opc_review_total iwd_opc_grand_total">
											<div class="iwd_opc_review_total_cell">Total</div>
											<div class="iwd_opc_review_total_cell"></div>
										</div>

									</div>
								</div>
							</div>
							
							<div style="clear:both;margin-bottom: 20px;"></div>
							
						</fieldset>
						
					</fieldset>
					
				</div>
				
			</div>
			
			<div style="clear: both;"></div>
			
		</div>
		<div style="display:none;">
			<input type="hidden" name="aqpago_session" id="aqpago_session" />
			<input type="hidden" name="aqpago_type_payment" id="aqpago_type_payment" />
			<input type="hidden" name="aqpago_saved_first" id="aqpago_saved_first" />
			<input type="hidden" name="aqpago_saved_second" id="aqpago_saved_second" />
			<input type="hidden" name="aqpago_selected" id="aqpago_selected" />
			<input type="hidden" name="aqpago_updatemulti" id="aqpago_updatemulti" />
			
			<input type="hidden" name="aqpago_card_one" id="aqpago_card_one" />
			<input type="hidden" name="aqpago_card_two" id="aqpago_card_two" />
			
			<input type="hidden" name="aqpago_one_cc_card_id" id="aqpago_one_cc_card_id" />
			<input type="hidden" name="aqpago_one_cc_card_number" id="aqpago_one_cc_card_number" />
			<input type="hidden" name="aqpago_one_cc_card_owner" id="aqpago_one_cc_card_owner" />
			<input type="hidden" name="aqpago_one_cc_card_month" id="aqpago_one_cc_card_month" />
			<input type="hidden" name="aqpago_one_cc_card_year" id="aqpago_one_cc_card_year" />
			<input type="hidden" name="aqpago_one_cc_card_cid" id="aqpago_one_cc_card_cid" />
			<input type="hidden" name="aqpago_one_cc_card_installments" id="aqpago_one_cc_card_installments" />
			<input type="hidden" name="aqpago_one_cc_card_taxvat" id="aqpago_one_cc_card_taxvat" />
			<input type="hidden" name="aqpago_one_cc_card_value" id="aqpago_one_cc_card_value" />
			<input type="hidden" name="aqpago_one_cc_card_erro" id="aqpago_one_cc_card_erro" value="false" />
			
			
			<input type="hidden" name="aqpago_two_cc_card_id" id="aqpago_two_cc_card_id" />
			<input type="hidden" name="aqpago_two_cc_card_number" id="aqpago_two_cc_card_number" />
			<input type="hidden" name="aqpago_two_cc_card_owner" id="aqpago_two_cc_card_owner" />
			<input type="hidden" name="aqpago_two_cc_card_month" id="aqpago_two_cc_card_month" />
			<input type="hidden" name="aqpago_two_cc_card_year" id="aqpago_two_cc_card_year" />
			<input type="hidden" name="aqpago_two_cc_card_cid" id="aqpago_two_cc_card_cid" />
			<input type="hidden" name="aqpago_two_cc_card_installments" id="aqpago_two_cc_card_installments" />
			<input type="hidden" name="aqpago_two_cc_card_taxvat" id="aqpago_two_cc_card_taxvat" />
			<input type="hidden" name="aqpago_two_cc_card_value" id="aqpago_two_cc_card_value" />
			<input type="hidden" name="aqpago_two_cc_card_erro" id="aqpago_two_cc_card_erro" value="false" />
			
			<input type="hidden" name="aqpago_ticket_value" id="aqpago_ticket_value" value="false" />
		</div>
		
	</div>
	
	<div class="clear"></div>
	
	<?php do_action( 'woocommerce_credit_card_form_end', $id ); ?>
	
	<div class="clear"></div>
	
	
</fieldset>

