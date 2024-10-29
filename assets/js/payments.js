jQuery(window).on('load', function(){
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
	
	window.jQuery('#aqpago_cc_number').mask('0000 0000 0000 0000000');
	window.jQuery('#aqpago_cc_cid').mask('0000');
	window.jQuery('#billing_postcode').mask('00000-000');
	window.jQuery('#aqpago_documento').mask('000.000.000-00');
	window.jQuery('#woocommerce_aqpago_min_total_installments').mask('000.000.000,00', {reverse: true});
	window.jQuery('#aqpago_cc_multiple_val').mask('000.000.000,00', {reverse: true});
	
	var behavior = function (val) {
		return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
	},
	options = {
		onKeyPress: function (val, e, field, options) {
			field.mask(behavior.apply({}, arguments), options);
		}
	};
	window.jQuery('#billing_phone').mask(behavior, options);
});

var process_payment= false;
var type_payment= '';

var	process_success= false;
var	process_erro= false;
var	process_erro_type= false;

var	use_two_cards= false;
var	card_saved= false;
var	saved_card_one= false;
var	saved_card_two= false;

var	set_one_card= false;
var	set_two_card= false;
var	set_credit_multi= false;

var	amount_one= '';
var	amount_two= '';
var	amount_ticket= '';

var	set_credit_one= false;
var	card_one_erro= false;
var	card_two_erro= false;

var	card_one_success= false;
var	card_two_success= false;

var	card_two_erro= false;

var cards= [];
var	add_card= false;
var	card_one= false;
var	card_two= false;
var	select_card= false;
var show_saved_card= false;

var AQPAGO = {
	setPaymentMethod: function(method) {
		
		jQuery('.box-select-card-title').slideUp();
		
		if(amount_total < 2 && method == 'credit_multiple') {
			type_payment = '';
		
			return false;
		}
		if(method == 'ticket_multiple' || method == 'ticket') {
			if(amount_total < 10 && method == 'ticket') {
				
				use_two_cards = false;
				
				jQuery('.credit').slideDown('100');
				jQuery('.ticket').slideDown('100');
				jQuery('.credit_multiple').slideDown('100');
				jQuery('.ticket_multiple').slideDown('100');
				
				jQuery('.change-text').html('');
				
				type_payment = '';
				
				return false;
			}
			
			if(amount_total < 11 && method == 'ticket_multiple') {
				use_two_cards = false;
				
				jQuery('.credit').slideDown('100');
				jQuery('.ticket').slideDown('100');
				jQuery('.credit_multiple').slideDown('100');
				jQuery('.ticket_multiple').slideDown('100');
		
				jQuery('.change-text').html('');
				
				type_payment = '';
			
				return false;
			} 					
		}
		
		if(jQuery('.aqbank_type_payment .ticket-info-tool').length){
			jQuery('.aqbank_type_payment .ticket-info-tool').slideUp();
		}
		
		if(amount_one == ''){
			amount_one = (amount_total / 2);
			jQuery('#aqpago_cc_multiple_val').val( this.formatPrice( amount_one ) );
		}
		if(amount_two == ''){
			amount_two = (amount_total / 2);
		}
		if(amount_ticket == ''){
			amount_ticket = (amount_total / 2);
		}
		
		jQuery('.fieldset.aqbank-checkout').slideDown();
		//jQuery('.field-name-lastname, .valid_month_checkout, .field-name-name_card, .field-not, .field-name-documento').slideDown();
		
		jQuery('.box-select-card').slideUp();
		jQuery('.box-select-card-li').slideUp();
		
		jQuery('#one-li-form-payment .li-position-card svg tspan').html('1º'); 
		jQuery('#two-li-form-payment .li-position-card svg tspan').html('2º');
		
		/** boleto **/
		jQuery('#three-li-form-payment svg tspan').html('2º');
		
		
		if(jQuery('.active-arrow').length && type_payment != '') {
			use_two_cards = false;
			
			jQuery('.aqbank_type_payment_li .change-text').html();
			
			jQuery('.' + method).removeClass('no-border');
			jQuery('.aqbank-arrow-right').removeClass('active-arrow');
			jQuery('.aqbank_type_payment').removeClass('aqbank_payment_active');
			jQuery('.ticket').addClass('no-border');
			
			if (process_erro_type == 'credit_multiple' && process_erro && process_success) {
				jQuery('.aqbank_type_payment_li_box.credit').slideUp(1);
				jQuery('.aqbank_type_payment_li_box.ticket').slideUp(1);
			} else {
				jQuery('.credit').slideDown('100');
				jQuery('.ticket').slideDown('100');
				jQuery('.pix').slideDown('100');
			}
			
			jQuery('.credit_multiple').slideDown('100');
			jQuery('.ticket_multiple').slideDown('100');
			
			jQuery('#aqbank-valor-intergal').slideUp(); 
			jQuery('#aqbank-multi-pagamento-valor').slideUp();
			
			if (method == 'credit') {
				jQuery('.card_one').slideUp('100'); 
			} else if (method == 'ticket') {
				jQuery('.aqpago-box-boleto-checkout .actions-toolbar').slideUp();
				jQuery('.aqpago-box-boleto-checkout .button-finished').slideUp();
				jQuery('.aqbank_payment_boleto').slideUp('100'); 
			} else if (method == 'credit_multiple') {
				jQuery('.aqbank_payment_integral').slideUp('100'); 
				jQuery('.card_one').slideUp('100'); 
			} else if (method == 'ticket_multiple') {
				jQuery('.aqbank_payment_integral').slideUp('100'); 
				jQuery('.card_one').slideUp('100'); 
			} else if (method == 'pix') {
				jQuery('.aqbank_payment_integral').slideUp('100'); 
				jQuery('.card_one').slideUp('100'); 
			}
			
			if (add_card) {
				jQuery('#list-new').slideUp();
			}
		
			jQuery('.change-text').html('');
			jQuery('.aqbank-payment-description').slideDown('100');
			
			jQuery('#aqpago_type_payment').val();
			
			if (amount_total < 11) {
				jQuery('.aqbank_type_payment_li_box.ticket_multiple').addClass("aqbank_disable_method"); 
				jQuery('.aqbank_set_multi_ticket .ticket-info-tool').slideDown();
			} 
			if (amount_total < 10) {
				jQuery('.aqbank_type_payment_li_box.ticket').addClass("aqbank_disable_method"); 
				jQuery('.aqbank_set_ticket .ticket-info-tool').slideDown();
			}
			
			type_payment = '';
			process_payment = false;
			return false;
		}
		
		type_payment = method;
		
		jQuery('#aqpago_type_payment').val( type_payment );
		
		jQuery('#one-action').slideUp();
		jQuery('#multi-actions').slideUp();
		jQuery('.aqbank-infos').slideUp();
		
		jQuery('#aqbank-valor-intergal').slideDown();
		jQuery('#aqbank-multi-pagamento-valor').slideDown();
		
		jQuery('.aqbank-payment-description').slideUp('100');
		jQuery('.li-form-payment').slideUp();
		jQuery('.payment-method-aqbank .actions-toolbar').slideUp();
		
		
		if (method != 'credit') jQuery('.credit').slideUp('100');
		if (method != 'ticket') jQuery('.ticket').slideUp('100');
		if (method != 'credit_multiple') jQuery('.credit_multiple').slideUp('100');
		if (method != 'pix') jQuery('.pix').slideUp('100');
		if (method != 'ticket_multiple') jQuery('.ticket_multiple').slideUp('100');
		
		jQuery('.aqbank_type_payment').addClass('aqbank_payment_active');
		jQuery('.aqbank-arrow-right').addClass('active-arrow');
		jQuery('.' + method).addClass('no-border');
		
		jQuery('.aqbank_type_payment_li .change-text').html('ALTERAR');
		jQuery('.aqbank_custom_informations').slideDown(); 
		
		
		if (add_card && method != 'ticket') {
			if (saved_card_one && !saved_card_two) {
				if (!card_two) {
					if (jQuery('.box-select-card-li').length <= 1) {

					} else {
						if (totalSavedCards > 0) jQuery('#list-new').slideDown('100');
					}
				}	
			} else {			
				if (totalSavedCards > 0) jQuery('#list-new').slideDown('100');
			}
		}
		
		
		if (method == 'credit') {
			this.setCreditMethod();
		} else if (method == 'credit_multiple') {
			this.setCreditMultipleMethod();
		} else if (method == 'ticket_multiple') {
			this.setTicketMultipleMethod();
		} else if (method == 'ticket') {
			this.setTicketMethod();
		}
	},
	
	setTicketMethod: function() {
		use_two_cards = false;
		
		jQuery('.box-select-card').slideUp();
		jQuery('.box-select-card-li').slideUp();
		jQuery('.aqbank-add-new-card').slideUp();
		
		typePayment = type_payment;
		
		jQuery('#one-action').slideUp();
		jQuery('#multi-actions').slideUp();
		jQuery('#aqbank-valor-intergal').slideDown();
		jQuery('#aqbank-multi-pagamento-valor').slideDown();
		
		jQuery('.aqbank_custom_informations').slideDown(); 
		
		jQuery('#multi-actions-one-ticket').slideUp();
		jQuery('.payment-method-content-cc').slideUp();
		jQuery('.payment-method-content-pix').slideUp();
		jQuery('.payment-method-content-ticket').slideDown();
		jQuery('.aqbank-infos').slideDown();
		
		
		if (jQuery('#billing_phone').length) {
			jQuery('.phone-text').html( jQuery('#billing_phone').val() );	
		}
		if (jQuery('#billing_email').length) {
			if (jQuery('#billing_email').val() != '') {
				jQuery('.email-text').html( jQuery('#billing_email').val() );
			}
		}
		
		/*
		if (customer.isLoggedIn()) {
			if (this.getPhoneInput() == 'telephone') {
				jQuery('.phone-text').html( quote.shippingAddress().telephone );
			} else {
				jQuery('.phone-text').html( quote.shippingAddress().this.getPhoneInput() );
			}
			jQuery('.email-text').html( customer.customerData.email );
		}
		*/
		
		jQuery('.aqpago-box-boleto-checkout .actions-toolbar').slideDown();
		/* jQuery('.aqpago-box-boleto-checkout .button-finished').slideDown(); */
		
		process_payment = true;
		
		setTimeout(function(){ 
			jQuery('.aqbank_payment_boleto').slideDown('100'); 
		}, 500);
	},
				
	
	setTicketMultipleMethod: function(){
		use_two_cards = false;
		
		jQuery('.li-position-card svg tspan').html('1º'); 
		jQuery('#three-li-form-payment svg tspan').html('2º');
		
		jQuery('.modal-credit-amount').slideUp();
		jQuery('.payment-method-content-ticket').slideUp();
		jQuery('.payment-method-content-pix').slideUp();
		
		jQuery('.payment-method-content-cc').slideDown();
		jQuery('.modal-edit-amount').slideDown();
		jQuery('.modal-edit-amount').slideDown();
		jQuery('#multi-actions-one-ticket').slideDown();
		jQuery('#aqbank-valor-intergal').slideUp();
		
		if (amount_ticket) {
			
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val(
				this.formatPrice( amount_ticket )
			);
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val(
				this.formatPrice( amount_ticket )
			);
			
			jQuery('#ticket-grand-total-view').html(
				this.formatPriceWithCurrency( amount_ticket )
			);
			jQuery('#ticket-card-bottom span').html(
				this.formatPriceWithCurrency( amount_ticket )
			);
		} 
		
		if (card_one) {
			jQuery('.aqbank_custom_informations').slideDown('100');
			
			jQuery('.card-box-all').slideUp();
			jQuery('#ticket-grand-total-view').html(
				this.formatPrice( amount_ticket )
			);
			
			jQuery('#ticket-card-bottom').html(
				this.formatPrice( amount_ticket )
			);
			
			jQuery('.box-select-card').slideDown();
			jQuery('#three-li-form-payment').slideDown('100');
			
			var oneValue = amount_one;
			
			jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val(
				this.formatPrice( amount_one )
			);
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_one )
			);
			
			jQuery('#ticket-card-bottom span').html(
				this.formatPriceWithCurrency( (amount_one / cards[ card_one ].installment ) )
			);						
				
				
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val(
				this.formatPrice( amount_ticket )
			);
			
				
			jQuery('#ticket-grand-total-view').html(
				this.formatPriceWithCurrency( amount_ticket )
			);
			
			jQuery('#ticket-card-bottom span').html(
				this.formatPriceWithCurrency( amount_ticket )
			);
			
			jQuery('#one-li-form-payment').slideDown('100');
			jQuery('.box-select-card').slideDown('100');
			/* jQuery('#button-finished').slideDown('100'); */
			
			jQuery('#two-li-form-payment').slideUp();
			jQuery('#three-li-form-payment').slideDown();
			
			var instOne = jQuery('#aqpago_one_installments').val();
			jQuery("#aqpago_one_installments option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var valPrice = ((amount_one / (100 - data.tax)) * 100);
				data.price = valPrice / install;
				data.total = valPrice;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
				
				if(install == instOne) {
					jQuery('.description-installment-1').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					);
					
					jQuery('#one-card-bottom strong').html(install + 'x');
					jQuery('#one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(data.price));
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne).change();
		} else {
			var instOne = jQuery('#aqpago_one_installments').val();
			
			jQuery("#aqpago_installments option, #aqpago_installments_oneCard option").each(function() {
				jQuery(this).remove();
			});
			
			jQuery('.installment-view').html('');
			
			var valuePrice = jQuery('#aqpago_cc_multiple_val').val().replace('.', '');
			valuePrice = valuePrice.replace(',', '.');
			
			Object.entries(installMap).forEach(([install, data]) => {
				var valPrice = ((valuePrice / (100 - data.tax)) * 100);
				data.price = valPrice / install;
				data.total = valPrice;
				
				jQuery('#aqpago_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
				
				if (install == jQuery('#aqpago_installments').val()) {
					jQuery('.description-installment').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					); 
					
					jQuery('#one-card-bottom strong').html(install + 'x');
					jQuery('#one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(data.price));
				}					
				if (install == jQuery('#aqpago_installments_oneCard').val()) {
					jQuery('.description-installment-1').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					); 
				}			
			});
			
			jQuery('#button-finished').slideUp(1);
			jQuery('.aqbank_custom_informations').slideUp(1);
			
			/** existe cartão salvo **/
			if (savedCards) {
				jQuery('.card-box-all').slideUp();
				jQuery('.box-select-card-title').slideDown();
				jQuery('.box-select-card').slideDown();
				jQuery('.box-select-card-li').slideDown();
				if (!updateMulti) jQuery('#list-new').slideDown('100');
			}
		}
		
		
		/** só digitou o primeiro cartão **/
		if (card_one && !card_two) {
			jQuery('.box-select-card').slideDown();
			jQuery('#three-li-form-payment').slideDown('100');
		}
		
		/** dois cartões digitados **/
		if (card_one && card_two) {
			/** cartão já foi selecionado **/
			if (select_card) {
				/******* select com dois cartões ********/
				jQuery('#one-li-form-payment').slideUp(1);
				jQuery('.box-select-card-li').slideUp(1);
				jQuery('.box-select-card-title').slideUp(1);
				jQuery('#button-finished').slideUp();
				
				jQuery('.box-select-card').slideDown('100');
				/***************/

				jQuery('#list-' + select_card ).slideDown('100');
				jQuery('#one-li-form-payment').slideDown('100');
				/* jQuery('#button-finished').slideDown('100'); */
				
				if (totalSavedCards > 1) {
					jQuery('#list-' + card_one).slideDown('100');
					jQuery('.box-select-card-custom').slideDown('100');
				}
			} else {
				if (card_one_success) {
					jQuery('#one-li-form-payment').slideUp(1);
					jQuery('.box-select-card-li').slideUp(1);
					jQuery('.box-select-card-title').slideUp(1);
					jQuery('#button-finished').slideUp();
					
					jQuery('.box-select-card').slideUp(1);
					/***************/
					
					if (totalSavedCards > 1) {
						jQuery('#list-' + card_one).slideUp(1);
						jQuery('.box-select-card-custom').slideUp(1);
					} else {
						jQuery('#list-' + card_one ).slideDown('100');
					}
					jQuery('#one-li-form-payment').slideDown('100');
					
				

				} else if (card_two_success) {
					jQuery('#one-li-form-payment').slideUp(1);
					jQuery('.box-select-card-li').slideUp(1);
					jQuery('.box-select-card-title').slideUp(1);
					jQuery('#button-finished').slideUp();
					
					jQuery('.box-select-card').slideUp(1);
					/***************/
					
					if (totalSavedCards > 1) {
						jQuery('#list-' + card_two).slideUp(1);
						jQuery('.box-select-card-custom').slideUp(1);
					} else {
						jQuery('#list-' + card_two ).slideDown('100');
					}
					
					jQuery('#one-li-form-payment').slideDown('100');
				} else {
					/** cartão ainda não selecionado **/
					jQuery('#one-li-form-payment').slideUp(1);
					jQuery('#two-li-form-payment').slideUp(1);
					jQuery('#button-finished').slideUp(1);
					
					jQuery('#ticket-li-form-payment').slideDown('100');
					jQuery('.box-select-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
					/**********/
				}
			}
		}
		
		
		/** existe 1 cartão aprovado **/
		if (card_one_erro || card_two_erro) {
			jQuery('.box-select-card-title').slideUp();
			jQuery('.box-select-card-custom').slideUp();
			
			jQuery('.aqbank_custom_informations').slideDown('100');
			
			//card-view-address
			if (!card_one_erro) {
				jQuery('#one-li-form-payment').slideDown();
				/* jQuery('#button-finished').slideDown(); */
			}
			
			if (!card_two_erro) {
				jQuery('#two-li-form-payment').slideDown();
				/* jQuery('#button-finished').slideDown(); */
			}						
		}


		/** Cartão salvo selecionado **/
		if (saved_card_one || select_card) {
			/** Não digitou o código de segurança **/
			if (!cards[ card_one ].securityCode) {
				select_card = false;
				card_one = false;
			
				jQuery('.box-select-card-li-arrow').removeClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideUp();
				
				jQuery('.aqbank_custom_informations').slideUp();
				jQuery('.aqbank_payment_integral').slideUp();
				jQuery('.li-form-payment').slideUp();
				jQuery('#button-finished').slideUp();
				
				jQuery('#multi-actions-one-ticket').slideDown('100');
				if (!updateMulti) jQuery('#list-new').slideDown('100');
				jQuery('.box-select-card-title').slideDown('100');
				jQuery('.box-select-card').slideDown('100');
				jQuery('.box-select-card-li').slideDown('100');
				
			}
		}

		
	
		if (card_one_success || card_two_success) {
			if (card_one_success) {
				jQuery('#one-li-form-payment').slideDown();
				jQuery('#two-li-form-payment').slideUp();
			} else if (card_two_success) {
				jQuery('#two-li-form-payment').slideDown();
				jQuery('#one-li-form-payment').slideUp();
			}
			
			jQuery('.box-select-card.box-select-card-custom').slideUp(1);
		}

			
		setTimeout(function(){ 
			jQuery('.aqbank_payment_integral').slideDown('100'); 
			jQuery('.card_one').slideDown('100'); 
		}, 500);
	},
	
	setCreditMultipleMethod: function(){
		use_two_cards = true;
		
		jQuery('#three-li-form-payment').slideUp(1);
		jQuery('#multi-actions-one-ticket').slideUp();
		jQuery('.modal-credit-amount').slideUp();
		jQuery('.modal-edit-amount').slideDown();
		
		jQuery('.payment-method-content-ticket').slideUp();
		jQuery('.payment-method-content-pix').slideUp();
		jQuery('.payment-method-content-cc').slideDown();
		
		/** Não digitou ou escolheu os dois cartões **/
		if (!card_two) {
			jQuery('#multi-actions').slideUp();
			jQuery('#button-finished').slideUp();
			jQuery('.card-box-all').slideDown('100');
		} 
		
		/** primeiro cartão negativo **/
		if (!card_one) {
			jQuery("#aqpago_installments option, #aqpago_installments_oneCard option").each(function() {
				jQuery(this).remove();
			});
			
			jQuery('.installment-view').html('');
			
			var valuePrice = jQuery('#aqpago_cc_multiple_val').val().replace('.', '');
			valuePrice = valuePrice.replace(',', '.');
			
			Object.entries(installMap).forEach(([install, data]) => {
				var valPrice = ((valuePrice / (100 - data.tax)) * 100);
				data.price = valPrice / install;
				data.total = valPrice;
				
				jQuery('#aqpago_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
				
				if (install == jQuery('#aqpago_installments').val()) {
					jQuery('.description-installment').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					); 
				}					
				if (install == jQuery('#aqpago_installments_oneCard').val()) {
					jQuery('.description-installment-1').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					); 
				}			
			});
			
			jQuery('#multi-actions').slideDown();
		}

		/** primeiro cartão ok **/
		if (card_one) {
			jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val(
				this.formatPrice( amount_one )
			);
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_one )
			);
			

			jQuery('#one-card-bottom span').html(
				this.formatPriceWithCurrency( ( amount_one / cards[card_one].installment ) )
			);
			
			jQuery('#one-li-form-payment').slideDown();
			
			var instOne = jQuery('#aqpago_one_installments').val();
			jQuery("#aqpago_one_installments option, #aqpago_installments_oneCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var valPrice = ((amount_one / (100 - data.tax)) * 100);
				data.price = valPrice / install;
				data.total = valPrice;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
				
				if(install == instOne) {
					jQuery('.description-installment-1').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					);
					
					jQuery('#one-card-bottom strong').html(install + 'x');
					jQuery('#one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(data.price));
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne).change();
			
		} else {
			if (!card_two) {
				/** existe cartão salvo **/
				if (savedCards) {
					jQuery('.one-li-form-payment').slideUp();
					jQuery('.card-box-all').slideUp();
					jQuery('.box-select-card-title').slideDown();
					jQuery('.box-select-card').slideDown();
					jQuery('.box-select-card-li').slideDown();
					jQuery('#list-new').slideDown('100');
				}
			}
		}

		/** segundo cartão ok **/
		if (card_two) {
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val(
				this.formatPrice( amount_two )
			);
			
			jQuery('#two-grand-total-view').html(
				this.formatPriceWithCurrency( amount_two )
			);
			
			jQuery('#two-card-bottom span').html(
				this.formatPriceWithCurrency( ( amount_two / cards[card_two].installment ) )
			);
			
			jQuery('#two-li-form-payment').slideDown();
			
			
			var instTwo = jQuery('#aqpago_two_installments').val();
			jQuery("#aqpago_two_installments option, #aqpago_installments_twoCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var valPrice = ((amount_two / (100 - data.tax)) * 100);
				data.price = valPrice / install;
				data.total = valPrice;
				
				jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
				
				if(install == instTwo) {
					jQuery('.description-installment-2').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					);
					
					jQuery('#two-card-bottom strong').html(install + 'x');
					jQuery('#two-card-bottom span').html(AQPAGO.formatPriceWithCurrency(data.price));
				}
			});
			
			jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').val(instTwo).change();
		}
		
		jQuery('.modal-edit-amount').slideDown();
		jQuery('#aqbank-valor-intergal').slideUp();
		
		/** Show two cards for pay **/
		if (type_payment == 'credit_multiple' && card_one && card_two) {
			this.showPayTwoCard();
		}
		
		if (card_one && !card_two) {
			jQuery('#list-new').slideUp(1);
			jQuery('#multi-actions-two').slideDown();
		} else if (card_one && card_two) {
			jQuery('#list-new').slideUp(1);
		}
		
		if (saved_card_one && !saved_card_two) {
			if (!card_two) {
				jQuery('.box-select-card-li-arrow').removeClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideUp();
				
				
				
				jQuery('.card-box-all').slideUp(1);
				jQuery('.card_cvv_img').slideUp(1);
				
				jQuery('#list-' + card_one ).slideUp();
				
				if (jQuery('.box-select-card-li').length <= 1) {
					jQuery('#list-new').slideUp(1);
					jQuery('.box-select-card-li').slideUp(1);
					this.setNewCard();
				} else {
					jQuery('#one-li-form-payment').slideUp(1);
					jQuery('.box-select-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
				}
			}	
		} 
		
		setTimeout(function(){ 
			jQuery('.aqbank_payment_integral').slideDown('100'); 
			jQuery('.card_one').slideDown('100'); 
		}, 500);
		
	},
	
	showPayTwoCard: function(){
		var self = this;
		
		/********** Multiple Ticket ********/
		jQuery('#ticket-grand-total-view').html(
			this.formatPriceWithCurrency( amount_ticket )
		);
		
		jQuery('#ticket-card-bottom span').html(
			this.formatPriceWithCurrency( amount_ticket )

		);

		/***********************************/
		
		jQuery('.card-box-all').slideUp('100');
		
		jQuery('#multi-actions').slideUp();
		jQuery('#two-payment-right-empty').slideUp();
		jQuery('#img-flag-card').slideUp();
		
		jQuery('#multi-actions-two').slideDown();
		jQuery('#two-payment-right-full').slideDown();
		
		jQuery('#one-li-form-payment').slideDown('100');
		jQuery('#two-li-form-payment').slideDown('100');
		/*jQuery('#button-finished').slideDown('100'); */
		
		/************/
		jQuery('.grandtotal-box').html(
			jQuery('#iwd_opc_review_totals').html()
		);
		/************/
		
		var HtmlCardOne = "";
		HtmlCardOne = "<div id='list-" + card_one + "' class='box-select-card-li box-select-card-one one-li-form-payment'>"
							+ "<div class='box-select-card-float box-select-card-li-flag " + jQuery('#one-li-form-payment .li-number-card .img-flag').attr("class") + "'>"
							+ this.getFlagSvg( cards[ card_one ].flag )
							+ "</div>"
							+ "<div class='box-select-card-float box-select-card-li-number'>"
							+ cards[ card_one ].number.substr(0, 4) + " XXXX XXXX " + cards[ card_one ].number.substr(-4, 4)
							+ "</div>"
							+ "<div class='box-select-card-float box-select-card-li-arrow'>"
							+ "<span style='position: absolute;'>EDITAR</span>"
							+ "<div>" + this.getArrowRight() + "</div>"
							+ "</div>"
						+ "</div>";
		
		var HtmlCardTwo = "";
		HtmlCardTwo = "<div id='list-" + card_two + "' class='box-select-card-li box-select-card-two two-li-form-payment'>"
						+ "<div class='box-select-card-float box-select-card-li-flag " + jQuery('#two-li-form-payment .li-number-card .img-flag').attr("class") + "'>"
						+ this.getFlagSvg( cards[ card_two ].flag )
						+ "</div>"
						+ "<div class='box-select-card-float box-select-card-li-number'>"
						+ cards[ card_two ].number.substr(0, 4) + " XXXX XXXX " + cards[ card_two ].number.substr(-4, 4)
						+ "</div>"
						+ "<div class='box-select-card-float box-select-card-li-arrow'>"
						+ "<span style='position: absolute;'>EDITAR</span>"
						+ "<div>" + this.getArrowRight() + "</div>"
						+ "</div>"
					+ "</div>";
		
		

		
		
		var oneCard = card_one;
		var twoCard = card_two;
		
		if(jQuery('#list-' + card_one).length == 0) {
			/*** Select card one *****/
			jQuery('.box-select-card').prepend( HtmlCardOne );
			/** load Click function **/
			jQuery('#list-' + oneCard ).on('click', function() {
				card_saved = false;
				return self.setCardId( oneCard );
			});
			/*** Select card one *****/
		}
		if(jQuery('#list-' + card_two).length == 0) {
			/*** Select card two *****/
			jQuery('.box-select-card').prepend( HtmlCardTwo );
			/** load Click function **/
			jQuery('#list-' + twoCard ).on('click', function() {
				card_saved = false;
				return self.setCardId( twoCard );
			});
			/*** Select card two *****/
		}
	},
	/***** set card *******/
	setCardId: function(cardId){
		
		jQuery('.box-select-card-li').slideUp();
		
		//var cards 		= this.cards();
		var card 		= cards[ cardId ];
		var oldFisrt 	= card_one;
		var oldTwo 		= card_two;
		
		if(select_card){
			
			jQuery('.box-select-card-li-arrow').removeClass('active-arrow');
			jQuery('.' + cardId + ' .box-select-card-li-arrow' ).removeClass('active-arrow-custom');
			jQuery('.' + cardId).slideUp();
			jQuery('.box-select-card-li-arrow span').slideUp();
			
			select_card = false;
			
			jQuery('.li-form-payment').slideUp('100');
			
			jQuery('#button-finished').slideUp('100');
			jQuery('.box-select-card-title').slideDown('100');
			jQuery('.box-select-card-li').slideDown('100');
		}
		else {
			/** Processo normal sem cartão salvo **/
			jQuery('.box-select-card-li-arrow').addClass('active-arrow');
			jQuery('.' + cardId + ' .box-select-card-li-arrow').addClass('active-arrow-custom');
			jQuery('.box-select-card-title').slideUp('100');
			
			jQuery('.box-select-card-li-arrow span').slideDown();
			jQuery('.' + cardId).slideDown();
			jQuery('#' + cardId).slideDown('100');
			
			/*jQuery('#button-finished').slideDown('100'); */
			
			card_one = cardId;
			
			/** primeiro cartão passa para a segunda posição se o cartão selecionado for o segundo cartão digitado **/
			if(oldTwo == cardId){
				card_two = oldFisrt;
				
				cards[ oldFisrt ].installment = jQuery('#' + this.getCode() + '_one_installments').val();
				
				var cardTwo = cards[ oldFisrt ];
				
				/**** Modal two card **/
				jQuery('#two-middle-number-card').html( cardTwo.number.substr(-4, 4) );
				this.setBandeiraInfo('#two-li-form-payment .li-number-card .img-flag', cardTwo.flag, 'info');
				jQuery('#' + this.getCode() + '_cc_number_cardTwo').val( cardTwo.number );
				jQuery('#' + this.getCode() + '_cc_owner_cardTwo').val( cardTwo.owerName );
				jQuery('#' + this.getCode() + '_expiration_cardTwo').val( cardTwo.expiration_month );
				jQuery('#' + this.getCode() + '_expiration_yr_cardTwo').val( cardTwo.expiration_year );
				jQuery('#' + this.getCode() + '_cc_cid_cardTwo').val( cardTwo.securityCode );
				jQuery('#' + this.getCode() + '_documento_cardTwo').val( cardTwo.taxvat );
				jQuery('#' + this.getCode() + '_installments_cardTwo').val( cardTwo.installment ).change();
				jQuery('#not_cardTwo').prop('checked', cardTwo.imOwer );
				/***********/
				
				jQuery('#two-middle-number-card').html( cardTwo.number.substr(-4, 4) );
				jQuery('#two-card-bottom span').html( cardTwo.number.substr(-4, 4) );
				
				jQuery('#' + this.getCode() + '_two_installments').val( cardTwo.installment ).change();
				this.setBandeiraInfo('#two-li-form-payment  .li-number-card .img-flag', cardTwo.flag, 'info');
				
				jQuery('#two-card-bottom strong').html( cardTwo.installment + 'x' );
				jQuery('#two-card-bottom span').html(
					this.formatPriceWithCurrency( 
						( amount_two / cardTwo.installment )
					)
				);
				
				jQuery('#two-grand-total-view').html(
					this.formatPriceWithCurrency( amount_two )
				);
				
			}
			
			cards[ cardId ].installment = jQuery('#' + this.getCode() + '_one_installments').val();
			
			/**** Modal one card **/
			jQuery('#one-middle-number-card').html( card.number.substr(-4, 4) );
			this.setBandeiraInfo('#one-li-form-payment  .li-number-card .img-flag', card.flag, 'info');
			jQuery('#' + this.getCode() + '_cc_number_cardOne').val( card.number );
			jQuery('#' + this.getCode() + '_cc_owner_cardOne').val( card.owerName );
			jQuery('#' + this.getCode() + '_expiration_cardOne').val( card.expiration_month );
			jQuery('#' + this.getCode() + '_expiration_yr_cardOne').val( card.expiration_year );
			jQuery('#' + this.getCode() + '_cc_cid_cardOne').val( card.securityCode );
			jQuery('#' + this.getCode() + '_documento_cardOne').val( card.taxvat ).change();
			jQuery('#' + this.getCode() + '_installments_cardOne').val( card.installment ).change();
			//jQuery('#not_cardOne').prop('checked', card.imOwer );
			/***********/
			
			jQuery('#one-middle-number-card').html( card.number.substr(-4, 4) );
			
			//jQuery('#one-card-bottom strong').html( card.number.substr(-4, 4) );
			//jQuery('#one-card-bottom span').html( card.number.substr(-4, 4) );
			
			jQuery('#' + this.getCode() + '_two_installments').val( card.installment ).change();
			this.setBandeiraInfo('#one-li-form-payment  .li-number-card .img-flag', card.flag, 'info');
			
			jQuery('#one-card-bottom strong').html( card.installment + 'x' );
			
			if(type_payment == 'credit_multiple' || type_payment == 'ticket_multiple') {
				jQuery('#one-card-bottom span').html(
					this.formatPriceWithCurrency( ( amount_one / card.installment ) )
				);

				jQuery('#one-grand-total-view').html(
					this.formatPriceWithCurrency( amount_one )
				);

			}
			else {
				jQuery('#one-card-bottom span').html(
					this.formatPriceWithCurrency( (amount_total / card.installment ) )
				);				
				
				jQuery('#one-grand-total-view').html(
					this.formatPriceWithCurrency( amount_total )
				);
			}
			
			select_card = cardId;
			
			jQuery('#list-' + cardId ).slideDown('100');
			jQuery('#one-li-form-payment').slideDown('100');
			
			if(type_payment == 'ticket_multiple') {
				jQuery('#three-li-form-payment').slideDown('100');
			}
			
		}
		
		if(card_one_success || card_two_success) {
			jQuery('.box-select-card.box-select-card-custom').slideUp(1);
		}
	},
	/************/
	getFlagSvg: function(flag, response = false) {		

		if(flag == 'mastercard') {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="29.156" height="18.02" viewBox="0 0 29.156 18.02"><g id="mastercard-seeklogo.com" transform="translate(0 0)"><rect id="Retângulo_1" data-name="Retângulo 1" width="7.884" height="14.165" transform="translate(10.636 1.927)" fill="#ff5f00"/><g id="Grupo_6645" data-name="Grupo 6645"><path id="Caminho_3" data-name="Caminho 3" d="M11.137,9.01a9.034,9.034,0,0,1,3.429-7.083A9.006,9.006,0,1,0,9.01,18.019a8.954,8.954,0,0,0,5.556-1.927A9,9,0,0,1,11.137,9.01Z" fill="#eb001b"/><path id="Caminho_4" data-name="Caminho 4" d="M142.549,9.01a9,9,0,0,1-14.566,7.083,9.03,9.03,0,0,0,0-14.165A9,9,0,0,1,142.549,9.01Z" transform="translate(-113.393)" fill="#f79e1b"/></g></g></svg>';
		}
		else if(flag == 'amex') {
			return'<svg xmlns="http://www.w3.org/2000/svg" width="165.415" height="57.501" viewBox="0 0 165.415 57.501"><g id="american-express" transform="translate(0 -164.4)"><path id="Caminho_10" data-name="Caminho 10" d="M43.2,192.552h5.776L46.088,185.2Z" transform="translate(-29.022 -13.973)" fill="#2fabf7"/><path id="Caminho_11" data-name="Caminho 11" d="M246.552,187.863a5.118,5.118,0,0,0-2.1-.263H239.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,246.552,187.863Z" transform="translate(-160.694 -15.586)" fill="#228fe0"/><path id="Caminho_12" data-name="Caminho 12" d="M142.046,164.4v3.151l-1.575-3.151h-12.34v3.151l-1.575-3.151h-16.8a15.985,15.985,0,0,0-7.352,1.575V164.4H90.584v1.575A7.57,7.57,0,0,0,85.6,164.4H43.323l-2.888,6.564L37.546,164.4H24.156v3.151L22.58,164.4H11.29L6.039,176.74,0,190.394H13.391l1.575-4.2h3.676l1.575,4.2H35.446v-3.151l1.313,3.151h7.614l1.313-3.151v3.151h36.5v-6.827h.525c.525,0,.525,0,.525.788v5.776h18.9v-1.575a15.729,15.729,0,0,0,7.089,1.575H117.1l1.575-4.2h3.676l1.575,4.2h15.229v-3.938l2.363,3.938h12.34V164.4ZM53.3,186.455H48.837V172.014l-6.3,14.441H38.6l-6.3-14.441v14.441H23.368l-1.838-3.938H12.6l-1.575,4.2H6.039l7.877-18.642H20.48l7.352,17.592V168.076h7.089l5.776,12.6,5.251-12.6H53.3Zm17.854-14.441H60.915v3.413h9.977V179.1H60.915v3.676h10.24v3.938h-14.7V168.076h14.7Zm19.692,7.614a6.569,6.569,0,0,1,.788,3.413v3.676H87.171v-2.363a6.254,6.254,0,0,0-.788-3.676c-.788-.788-1.575-.788-3.151-.788H78.506v6.827H74.043V168.076H84.02c2.363,0,3.938,0,5.251.788a4.474,4.474,0,0,1,2.1,4.2,5.227,5.227,0,0,1-3.151,4.989A4.993,4.993,0,0,1,90.847,179.629Zm7.877,6.827H94.26V167.813h4.464Zm51.725,0h-6.3l-8.4-13.916v13.916h-8.927l-1.575-3.938h-9.19l-1.575,4.2h-4.989c-2.1,0-4.726-.525-6.3-2.1s-2.363-3.676-2.363-7.089a10.762,10.762,0,0,1,2.363-7.352c1.313-1.575,3.676-2.1,6.564-2.1h4.2v3.938h-4.2a4.842,4.842,0,0,0-3.413,1.05,6.165,6.165,0,0,0-1.313,4.2c0,2.1.263,3.413,1.313,4.464a4.185,4.185,0,0,0,3.151,1.05h1.838l6.039-14.441h6.564l7.352,17.592V168.338h6.564L145.46,181.2V168.338h4.464v18.117Z" fill="#0571c1"/><g id="Grupo_3" data-name="Grupo 3" transform="translate(66.166 171.227)"><path id="Caminho_13" data-name="Caminho 13" d="M358.4,192.552h6.039l-2.888-7.352Z" transform="translate(-306.938 -185.2)" fill="#228fe0"/><path id="Caminho_14" data-name="Caminho 14" d="M208.427,292.166V277.2l-6.827,7.352Z" transform="translate(-201.6 -247.005)" fill="#228fe0"/></g><path id="Caminho_15" data-name="Caminho 15" d="M136.8,282.8v3.413h9.715v3.676H136.8v3.938h10.765l4.989-5.514-4.726-5.514Z" transform="translate(-91.902 -79.541)" fill="#2fabf7"/><path id="SVGCleanerId_0" d="M241.514,282.8H236v4.726h5.776c1.575,0,2.626-.788,2.626-2.363A2.648,2.648,0,0,0,241.514,282.8Z" transform="translate(-158.544 -79.541)" fill="#228fe0"/><path id="Caminho_16" data-name="Caminho 16" d="M238.805,272.215V260.4H227.778a8.615,8.615,0,0,0-5.514,1.575V260.4H210.186c-1.838,0-4.2.525-5.251,1.575V260.4H183.667v1.575c-1.575-1.313-4.464-1.575-5.776-1.575H163.713v1.575c-1.313-1.313-4.464-1.575-6.039-1.575H141.92l-3.676,3.938-3.413-3.938H111.2v25.731h23.106l3.676-3.938,3.413,3.938h14.178v-6.039h1.838a15.972,15.972,0,0,0,6.039-.788v7.089h11.815v-6.827h.525c.788,0,.788,0,.788.788v6.039h35.709c2.363,0,4.726-.525,6.039-1.575v1.575h11.29c2.363,0,4.726-.263,6.3-1.313h0a8.938,8.938,0,0,0,4.2-7.877A10.055,10.055,0,0,0,238.805,272.215Zm-81.394,4.2H152.16v6.3h-8.4l-5.251-6.039-5.514,6.039H115.664V264.076h17.592l5.251,6.039,5.514-6.039h13.916c3.413,0,7.352,1.05,7.352,6.039C165.025,275.366,161.349,276.416,157.411,276.416Zm26.256-1.05a5.958,5.958,0,0,1,.788,3.413v3.676h-4.464v-2.363c0-1.05,0-2.888-.788-3.676-.525-.788-1.575-.788-3.151-.788h-4.726v6.827h-4.464V263.813h9.977c2.1,0,3.938,0,5.251.788a4.644,4.644,0,0,1,2.363,4.2,5.227,5.227,0,0,1-3.151,4.989A4.491,4.491,0,0,1,183.667,275.366Zm18.117-7.614h-10.24v3.413h9.977v3.676h-9.977v3.676h10.24v3.938h-14.7V263.813h14.7Zm11.028,14.7h-8.4v-3.938h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.219,2.219,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-5.514,6.3-5.514h8.665v4.464h-8.139a3.894,3.894,0,0,0-1.838.263c-.525.263-.525.788-.525,1.313,0,.788.525,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363c2.363,0,3.938.525,4.989,1.575a5.435,5.435,0,0,1,1.313,3.938C219.638,280.617,217.275,282.455,212.812,282.455Zm22.58-1.838a7.657,7.657,0,0,1-5.514,1.838h-8.4v-3.938h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.219,2.219,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-5.514,6.3-5.514h8.665v4.464H228.04a3.893,3.893,0,0,0-1.838.263c-.525.263-.525.788-.525,1.313,0,.788.263,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363c2.363,0,3.938.525,4.989,1.575a.257.257,0,0,1,.263.263,6.028,6.028,0,0,1,1.05,3.676A5.344,5.344,0,0,1,235.392,280.617Z" transform="translate(-74.704 -64.492)" fill="#0571c1"/><path id="SVGCleanerId_1" d="M302.552,283.863a5.119,5.119,0,0,0-2.1-.263H295.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,302.552,283.863Z" transform="translate(-198.314 -80.078)" fill="#228fe0"/><g id="Grupo_4" data-name="Grupo 4" transform="translate(66.166 171.227)"><path id="Caminho_17" data-name="Caminho 17" d="M246.552,187.863a5.118,5.118,0,0,0-2.1-.263H239.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,246.552,187.863Z" transform="translate(-226.86 -186.812)" fill="#228fe0"/><path id="Caminho_18" data-name="Caminho 18" d="M358.4,192.552h6.039l-2.888-7.352Z" transform="translate(-306.938 -185.2)" fill="#228fe0"/><path id="Caminho_19" data-name="Caminho 19" d="M208.427,292.166V277.2l-6.827,7.352Z" transform="translate(-201.6 -247.005)" fill="#228fe0"/></g><g id="Grupo_5" data-name="Grupo 5" transform="translate(77.456 203.259)"><path id="SVGCleanerId_0_1_" d="M241.514,282.8H236v4.726h5.776c1.575,0,2.626-.788,2.626-2.363A2.648,2.648,0,0,0,241.514,282.8Z" transform="translate(-236 -282.8)" fill="#228fe0"/></g><g id="Grupo_6" data-name="Grupo 6" transform="translate(96.886 203.522)"><path id="SVGCleanerId_1_1_" d="M302.552,283.863a5.119,5.119,0,0,0-2.1-.263H295.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,302.552,283.863Z" transform="translate(-295.2 -283.6)" fill="#228fe0"/></g><g id="Grupo_7" data-name="Grupo 7" transform="translate(0 164.4)"><path id="Caminho_20" data-name="Caminho 20" d="M155.836,281.93l-3.676-3.938v4.464H143.5l-5.251-6.039-5.776,6.039H115.138V264.076H132.73l5.514,6.039,2.626-3.151-6.564-6.564H111.2v25.731h23.106l3.938-3.938,3.413,3.938h14.178Z" transform="translate(-74.704 -228.892)" fill="#2fabf7"/><path id="Caminho_21" data-name="Caminho 21" d="M53.825,190.131l-3.413-3.676H48.837V184.88L44.9,180.941l-2.626,5.514H38.6l-6.3-14.441v14.441H23.368l-1.838-3.938H12.6l-1.838,3.938H6.039l7.877-18.379H20.48l7.352,17.592V168.076H31.77L28.094,164.4H24.156v3.151L22.843,164.4H11.29L6.039,176.74,0,190.131H13.653l1.575-3.938H18.9l1.838,3.938h14.7V186.98l1.313,3.151h7.614l1.313-3.151v3.151Z" transform="translate(0 -164.4)" fill="#2fabf7"/><path id="Caminho_22" data-name="Caminho 22" d="M118.6,197.4l-4.2-4.2,3.151,6.827Z" transform="translate(-76.854 -183.748)" fill="#2fabf7"/></g><g id="Grupo_8" data-name="Grupo 8" transform="translate(25.994 164.663)"><path id="Caminho_23" data-name="Caminho 23" d="M278.375,283.206a9.6,9.6,0,0,0,4.2-7.089l-3.676-3.676a7.768,7.768,0,0,1,.525,2.626,5.344,5.344,0,0,1-1.575,3.938,7.657,7.657,0,0,1-5.514,1.838h-8.4V276.9h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.22,2.22,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-4.989,5.514-5.514l-2.888-2.888c-.525.263-.788.525-1.05.525V258H252.906c-1.838,0-4.2.525-5.251,1.575V258h-21.53v1.575c-1.575-1.313-4.464-1.575-5.776-1.575H206.17v1.575c-1.313-1.313-4.464-1.575-6.039-1.575H184.377l-3.676,3.938L177.288,258H174.4l7.877,7.877,3.938-4.2h13.916c3.413,0,7.352,1.05,7.352,6.039,0,5.251-3.676,6.3-7.614,6.3h-5.251v3.938l3.938,3.938v-3.938h1.313a15.972,15.972,0,0,0,6.039-.788v7.089h11.815V277.43h.525c.788,0,.788,0,.788.788v6.039h35.709c2.363,0,4.726-.525,6.039-1.575v1.575h11.29a10.417,10.417,0,0,0,6.3-1.05Zm-52.25-9.452a5.958,5.958,0,0,1,.788,3.413v3.676h-4.464V278.48c0-1.05,0-2.888-.788-3.676-.525-.788-1.575-.788-3.151-.788h-4.726v6.827h-4.464V262.2H219.3c2.1,0,3.938,0,5.251.788a4.644,4.644,0,0,1,2.363,4.2,5.227,5.227,0,0,1-3.151,4.989A4.49,4.49,0,0,1,226.125,273.754Zm18.117-7.614H234v3.413h9.977v3.676H234V276.9h10.24v3.938h-14.7V262.2h14.7Zm11.028,14.7h-8.4V276.9h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.22,2.22,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-5.514,6.3-5.514h8.665V266.4h-8.139a3.894,3.894,0,0,0-1.838.263c-.525.263-.525.788-.525,1.313,0,.788.525,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363c2.363,0,3.938.525,4.989,1.575a5.435,5.435,0,0,1,1.313,3.938C262.1,279.005,259.733,280.843,255.269,280.843Z" transform="translate(-143.155 -227.543)" fill="#228fe0"/><path id="Caminho_24" data-name="Caminho 24" d="M459.2,285.175c0,.788.263,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363a7.625,7.625,0,0,1,3.676.788l-3.938-3.938h-2.363a3.893,3.893,0,0,0-1.838.263A2.006,2.006,0,0,0,459.2,285.175Z" transform="translate(-334.483 -244.741)" fill="#228fe0"/><path id="Caminho_25" data-name="Caminho 25" d="M431.2,240.4l.525.788h.263Z" transform="translate(-315.672 -215.719)" fill="#228fe0"/><path id="Caminho_26" data-name="Caminho 26" d="M387.2,196.4l4.464,10.765v-6.3Z" transform="translate(-286.113 -186.16)" fill="#228fe0"/><path id="Caminho_27" data-name="Caminho 27" d="M135.388,184.1h.525c.525,0,.525,0,.525.788v5.776h18.9v-1.575a15.729,15.729,0,0,0,7.089,1.575h7.877l1.575-4.2h3.676l1.575,4.2h15.229v-2.626l-3.676-3.676v2.888h-8.927l-1.313-4.2h-9.19l-1.575,4.2h-4.989c-2.1,0-4.726-.525-6.3-2.1s-2.363-3.676-2.363-7.089a10.762,10.762,0,0,1,2.363-7.352c1.313-1.575,3.676-2.1,6.564-2.1h4.2v3.938h-4.2a4.842,4.842,0,0,0-3.413,1.05,6.165,6.165,0,0,0-1.313,4.2c0,2.1.263,3.413,1.313,4.464a4.185,4.185,0,0,0,3.151,1.05h1.838l6.039-14.441H173.2l-3.676-3.676h-6.827a15.986,15.986,0,0,0-7.352,1.575V165.2H143.79v1.575A7.57,7.57,0,0,0,138.8,165.2H96.529l-2.888,6.564L90.753,165.2H79.2l3.676,3.676h5.251l4.464,9.715,1.575,1.575,4.726-11.553h7.352v18.642H101.78V172.814l-4.464,10.5,7.614,7.614h30.195Zm12.078-15.491h4.464v18.642h-4.464Zm-23.106,3.938h-10.24v3.413H124.1v3.676h-9.977v3.676h10.24v3.938h-14.7V168.613h14.7Zm7.352,14.441h-4.464V168.351h9.977c2.363,0,3.938,0,5.251.788a4.474,4.474,0,0,1,2.1,4.2,5.227,5.227,0,0,1-3.151,4.989,3.437,3.437,0,0,1,2.1,1.575,6.569,6.569,0,0,1,.788,3.413v3.676h-4.464V184.63a6.254,6.254,0,0,0-.788-3.676c-.263-.525-1.05-.525-2.626-.525h-4.726v6.564Z" transform="translate(-79.2 -165.2)" fill="#228fe0"/></g></g></svg>';
		}
		else if(flag == 'hipercard') {
			return '<svg id="hipercard-29" xmlns="http://www.w3.org/2000/svg" width="172.818" height="75.223" viewBox="0 0 172.818 75.223"><path id="Caminho_28" data-name="Caminho 28" d="M47.91,267.92H30c-7.913.373-14.383,3.562-16.252,10.135-.973,3.427-1.509,7.192-2.272,10.745C7.607,306.87,4.173,325.435.47,343.143H139.914c10.78,0,18.182-2.279,20.18-10.834.93-3.977,1.821-8.472,2.709-12.843,3.458-17.027,6.935-34.05,10.484-51.547Z" transform="translate(-0.47 -267.92)" fill="#822124"/><path id="Caminho_29" data-name="Caminho 29" d="M118.615,401.447c.75-.519,1.717-2.868.614-3.849a2.081,2.081,0,0,0-1.75-.261,2.106,2.106,0,0,0-1.487.787c-.475.647-.911,2.593-.173,3.323S118.138,401.775,118.615,401.447Zm-10.635-3.826c-.539,3.5-1.146,6.928-1.755,10.356-3.911.042-7.9.192-11.672-.088.712-3.354,1.222-6.916,1.929-10.268h-4.22c-1.508,8.557-2.879,17.248-4.563,25.626H92c.674-4.3,1.305-8.645,2.194-12.725a104.978,104.978,0,0,1,11.584.088c-.726,4.246-1.6,8.34-2.282,12.637h4.3c1.383-8.681,2.839-17.289,4.564-25.626Zm60.584,7.234c-3.333-1.347-5.953.928-7.168,3.06.275-.949.389-2.059.612-3.06h-3.332c-.813,6.3-2.009,12.213-3.147,18.185h3.76c.519-3.544.754-8.323,1.923-11.715.934-2.709,3.378-5.014,6.916-3.759.04-1,.327-1.763.427-2.711Zm2.1,14a9.11,9.11,0,0,1-.346-3.5c.194-2.528,1.115-5.6,2.536-7,1.961-1.923,5.833-1.6,8.921-.519.1-1.037.3-1.969.437-2.974-5.064-.826-9.871-.311-12.419,2.362-2.514,2.605-4.143,8.622-2.988,12.417,1.354,4.43,7.421,4.668,12.333,2.974.218-.89.334-1.883.519-2.8-2.678,1.381-7.81,2.111-9-.97Zm42.243-13.911c-3.323-1.661-6.089,1.126-7.168,2.8.306-.864.325-2.006.61-2.886h-3.33q-1.343,9.362-3.235,18.18h3.847a41.056,41.056,0,0,1,.875-6.556c.8-5.047,1.983-10.583,7.868-8.918.2-.854.277-1.822.519-2.623Zm-98.154.118c-.1.016-.093.137-.086.259-.818,6.126-1.928,11.961-3.112,17.72H115.3c.9-6.236,1.938-12.341,3.235-18.185l-3.76.206Zm33.081-.463a9.821,9.821,0,0,0-6.648,2.711c-2,2.109-3.631,6.772-3.148,11.02.678,6.051,8.223,5.842,14.257,4.374.1-1.065.361-1.973.519-2.974-2.486.93-6.8,2.227-9.36.612-1.935-1.226-1.935-4.317-1.312-7,4.054-.13,8.271-.105,12.333,0,.258-1.9.994-3.977.346-5.861-.852-2.483-3.894-3.126-6.994-2.886Zm3.586,6.663h-8.835a5.214,5.214,0,0,1,4.985-4.378c2.709-.1,4.649.994,3.849,4.372Zm-17.867-6.205c-3.173-1.191-7.04.232-8.717,1.585,0,.059-.04.067-.09.073l.09-.073a.049.049,0,0,0,0-.016c.028-.581.233-.987.261-1.57h-3.228c-1.343,8.946-2.939,17.635-4.62,26.247H121c.543-3.352.9-6.888,1.656-10.028.864,3.3,6.447,2.671,8.807,1.4C136.332,420.042,140.087,407.514,133.557,405.06ZM130.6,419.729c-2.012,2.13-6.959,2.1-7.346-1.489-.173-1.556.411-3.2.7-4.81s.5-3.2.787-4.635c1.981-2.421,7.806-2.713,8.4,1.312.514,3.491-.87,7.856-2.536,9.622ZM226.893,397c-.322,2.823-.752,5.533-1.311,8.126-9.217-2.918-14.869,3.864-14.767,12.234a6.594,6.594,0,0,0,1.311,4.369c1.745,1.971,6.743,2.445,9.262.785A6.489,6.489,0,0,0,222.7,421.2c.244-.3.629-1.1.7-.864a18.745,18.745,0,0,0-.346,2.708h3.408c.657-9.421,2.687-17.462,4.193-26.038ZM218.5,421.127c-2.531.054-3.79-1.513-3.849-4.111-.1-4.551,1.9-9.6,5.949-10.059a9.865,9.865,0,0,1,4.635.7C223.968,412.768,224.426,421,218.5,421.127Zm-32.449-16.28c-.185,1.037-.47,1.98-.692,2.974,2.22-.557,9.131-2.263,9.8.692a5.185,5.185,0,0,1-.437,2.8c-6.248-.591-11.342.446-12.682,4.9-.9,2.982.1,5.916,2.011,6.743,3.681,1.577,8.159-.23,9.71-2.711a11.3,11.3,0,0,0-.263,2.8h3.237a50.364,50.364,0,0,1,.961-8.4c.406-2.376,1.171-4.727,1.049-6.822-.278-4.8-8.231-3.1-12.682-2.974Zm6.127,14.8c-1.938,1.9-7.379,2.436-6.822-2.1.462-3.767,4.564-4.568,9.008-4.025C194.035,415.586,193.656,418.2,192.18,419.649Z" transform="translate(-72.618 -374.683)" fill="#fff"/></svg>';
		}		
		else if(flag == 'jcb') {
			return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104.253" height="78.243" viewBox="0 0 104.253 78.243"><defs><linearGradient id="linear-gradient" x1="-0.749" y1="0.885" x2="1.828" y2="0.885" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#007940"/><stop offset="0.228" stop-color="#00873f"/><stop offset="0.743" stop-color="#40a737"/><stop offset="1" stop-color="#5cb531"/></linearGradient><linearGradient id="linear-gradient-2" x1="-0.058" y1="0.541" x2="0.831" y2="0.541" xlink:href="#linear-gradient"/><linearGradient id="linear-gradient-3" x1="-0.818" y1="1.102" x2="1.995" y2="1.102" xlink:href="#linear-gradient"/><linearGradient id="linear-gradient-4" x1="0.191" y1="0.541" x2="1.094" y2="0.541" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#1f286f"/><stop offset="0.475" stop-color="#004e94"/><stop offset="0.826" stop-color="#0066b1"/><stop offset="1" stop-color="#006fbc"/></linearGradient><linearGradient id="linear-gradient-5" x1="0.059" y1="0.54" x2="0.937" y2="0.54" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#6c2c2f"/><stop offset="0.174" stop-color="#882730"/><stop offset="0.573" stop-color="#be1833"/><stop offset="0.859" stop-color="#dc0436"/><stop offset="1" stop-color="#e60039"/></linearGradient></defs><g id="g6321" transform="translate(-26.794 80.328)"><g id="g6323" transform="translate(26.794 -80.328)"><g id="g6327" transform="translate(72.081 0)"><path id="path6338" d="M234.976,139.667H242.5c.215,0,.717-.072.932-.072a3.357,3.357,0,0,0,2.651-3.368,3.478,3.478,0,0,0-2.651-3.368,3.785,3.785,0,0,0-.932-.072h-7.523Z" transform="translate(-228.527 -92.162)" fill="url(#linear-gradient)"/><path id="path6349" d="M231.695,29.509a13.042,13.042,0,0,0-13.041,13.041V56.091h18.414a7.368,7.368,0,0,1,1.29.072c4.156.215,7.237,2.365,7.237,6.09,0,2.938-2.078,5.446-5.947,5.947v.143c4.227.287,7.452,2.651,7.452,6.305,0,3.941-3.583,6.52-8.312,6.52H218.582V107.68h19.131A13.042,13.042,0,0,0,250.754,94.64V29.509H231.695Z" transform="translate(-218.582 -29.509)" fill="url(#linear-gradient-2)"/><path id="path6360" d="M245.151,110.076a3.048,3.048,0,0,0-2.651-3.081c-.143,0-.5-.072-.716-.072h-6.807v6.305h6.807a2,2,0,0,0,.716-.072,3.048,3.048,0,0,0,2.651-3.081Z" transform="translate(-228.527 -76.471)" fill="url(#linear-gradient-3)"/></g><path id="path6371" d="M48.45,29.509A13.042,13.042,0,0,0,35.409,42.549V74.721a25.788,25.788,0,0,0,11.249,2.938c4.514,0,6.95-2.723,6.95-6.449V56.02H64.786V71.138c0,5.875-3.654,10.676-16.05,10.676a55,55,0,0,1-13.4-1.648v27.443H54.468A13.042,13.042,0,0,0,67.509,94.568V29.509H48.45Z" transform="translate(-35.337 -29.509)" fill="url(#linear-gradient-4)"/><path id="path6384" d="M140.183,29.509a13.042,13.042,0,0,0-13.041,13.041V59.6c3.3-2.794,9.028-4.586,18.271-4.156a58.141,58.141,0,0,1,10.246,1.576V62.54a24.8,24.8,0,0,0-9.888-2.866c-7.022-.5-11.249,2.938-11.249,8.956,0,6.09,4.227,9.53,11.249,8.956a26.024,26.024,0,0,0,9.888-2.866v5.517a56.757,56.757,0,0,1-10.246,1.576c-9.243.43-14.975-1.361-18.271-4.156v30.094h19.131a13.042,13.042,0,0,0,13.041-13.041v-65.2H140.183Z" transform="translate(-91.03 -29.509)" fill="url(#linear-gradient-5)"/></g></g></svg>';
		}		
		else if(flag == 'elo') {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="213.684" height="78.243" viewBox="0 0 213.684 78.243">   <g id="Page-1" transform="translate(-0.9 -0.1)">     <g id="elo" transform="translate(0.9 0.1)">       <path id="Shape" d="M85.6,17.387a22.689,22.689,0,0,1,7.284-1.175A23.016,23.016,0,0,1,115.443,34.64l15.776-3.222A39.115,39.115,0,0,0,80.5,2.114Z" transform="translate(-53.781 -0.1)" fill="#fff100"/>       <path id="Shape-2" data-name="Shape" d="M14.092,88.007,24.766,75.923a23.035,23.035,0,0,1,0-34.473L14.092,29.4a39.143,39.143,0,0,0,0,58.607Z" transform="translate(-0.9 -19.565)" fill="#00a3df"/>       <path id="Shape-3" data-name="Shape" d="M115.376,130.4A23.085,23.085,0,0,1,85.5,147.62l-5.1,15.273a39.156,39.156,0,0,0,50.753-29.27Z" transform="translate(-53.715 -86.663)" fill="#ee4023"/>       <g id="Group" transform="translate(96.3 9.869)">         <path id="Shape-4" data-name="Shape" d="M34.5,45.177A13.962,13.962,0,0,1,24.46,49.2a13.648,13.648,0,0,1-7.25-2.182l-5.236,8.324a23.916,23.916,0,0,0,29.4-3.122ZM25.03,11.241A23.872,23.872,0,0,0,4.656,48.131l43.234-18.5A23.9,23.9,0,0,0,25.03,11.241ZM10.7,36.618a13.663,13.663,0,0,1-.1-1.678A14.055,14.055,0,0,1,24.863,21.11a13.9,13.9,0,0,1,10.506,5ZM61.484.5V46.587l7.989,3.323-3.793,9.1-7.922-3.29a8.84,8.84,0,0,1-3.894-3.29,10.307,10.307,0,0,1-1.544-5.706V.5Z" transform="translate(-0.793 -0.5)"/>         <path id="Shape-5" data-name="Shape" d="M229.035,43.14a14.116,14.116,0,0,1,18.227,10.54L256.9,51.7a23.875,23.875,0,0,0-23.4-19.1,24.217,24.217,0,0,0-7.552,1.208ZM217.656,74.323l6.512-7.351a14.065,14.065,0,0,1,0-21.046l-6.512-7.351a23.857,23.857,0,0,0,0,35.748Zm29.606-15.038a14.014,14.014,0,0,1-18.227,10.506l-3.122,9.331A23.864,23.864,0,0,0,256.9,61.266Z" transform="translate(-139.511 -21.825)"/>       </g>     </g>   </g> </svg>';
		}
		else if(flag == 'aura') {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="113.914" height="75.223" viewBox="0 0 113.914 75.223">   <g id="Grupo_1" data-name="Grupo 1" transform="translate(-216.472 -257.148)">     <path id="Caminho_5" data-name="Caminho 5" d="M216.472,257.148H330.263l.123,35.462c-13.738-5.226-57.444-17.729-113.914-2.147Z" transform="translate(0)" fill="#04267b" fill-rule="evenodd"/>     <path id="Caminho_6" data-name="Caminho 6" d="M216.472,379.832H330.263v-28.37c-20.522-6.869-57-17.16-113.791-2.579Z" transform="translate(0 -47.461)" fill="#fe0" fill-rule="evenodd"/>     <path id="Caminho_7" data-name="Caminho 7" d="M330.263,337.156c-20.522-6.869-57-17.16-113.791-2.579V323.618c56.471-15.582,100.176-3.078,113.914,2.147Z" transform="translate(0 -33.154)" fill="#e50019" fill-rule="evenodd"/>     <path id="Caminho_8" data-name="Caminho 8" d="M299.378,299.509c2.817-7.782,9.26-14.5,20.456-14.417s18.667,6.459,20.686,14.83C326.086,298.419,316.47,298.158,299.378,299.509Z" transform="translate(-46.659 -15.726)" fill="#fe0" fill-rule="evenodd"/>     <path id="Caminho_9" data-name="Caminho 9" d="M274.618,381.716l7.621-11.354h2.827l8.121,11.354h-2.993l-2.315-3.44h-8.3l-2.176,3.44Zm5.723-4.663h6.73L285,373.91a23.642,23.642,0,0,1-1.4-2.355,10.3,10.3,0,0,1-1.072,2.17l-2.18,3.328Zm26.173,4.663v-1.208a6.929,6.929,0,0,1-4.564,1.393,9.717,9.717,0,0,1-2.374-.278,4.425,4.425,0,0,1-1.636-.7,2.24,2.24,0,0,1-.753-1.034,4.223,4.223,0,0,1-.148-1.3v-5.094h2.434v4.56a4.814,4.814,0,0,0,.148,1.472,1.711,1.711,0,0,0,.975.864,4.78,4.78,0,0,0,1.843.312,6.445,6.445,0,0,0,2.055-.32,2.636,2.636,0,0,0,1.358-.875,2.722,2.722,0,0,0,.4-1.607v-4.407h2.434v8.224Zm8.087,0v-8.224h2.19v1.245a4.618,4.618,0,0,1,1.552-1.153,4.247,4.247,0,0,1,1.562-.28,7.512,7.512,0,0,1,2.5.449l-.841,1.3a5.447,5.447,0,0,0-1.783-.3,3.6,3.6,0,0,0-1.437.275,1.807,1.807,0,0,0-.905.764,3.306,3.306,0,0,0-.407,1.626v4.307Zm20.754-1.015a11.792,11.792,0,0,1-2.605.93,12.787,12.787,0,0,1-2.689.27,8.049,8.049,0,0,1-3.64-.661,1.949,1.949,0,0,1-1.27-1.692,1.548,1.548,0,0,1,.48-1.105,3.419,3.419,0,0,1,1.256-.8,8.647,8.647,0,0,1,1.755-.457c.476-.071,1.2-.143,2.162-.209a37.119,37.119,0,0,0,4.346-.481c.009-.19.014-.312.014-.362a1.23,1.23,0,0,0-.693-1.2,6.3,6.3,0,0,0-2.771-.473,7,7,0,0,0-2.54.343,2.276,2.276,0,0,0-1.211,1.222l-2.379-.188a2.989,2.989,0,0,1,1.067-1.412,5.615,5.615,0,0,1,2.152-.83,16.31,16.31,0,0,1,3.261-.291,14.714,14.714,0,0,1,2.989.249,4.935,4.935,0,0,1,1.7.623,2.024,2.024,0,0,1,.757.949,4.679,4.679,0,0,1,.12,1.285v1.858a11.127,11.127,0,0,0,.157,2.458,2.422,2.422,0,0,0,.615.989H335.84a2.087,2.087,0,0,1-.484-1.015Zm-.2-3.114a29.3,29.3,0,0,1-3.977.529,15.088,15.088,0,0,0-2.125.277,2.219,2.219,0,0,0-.961.455.871.871,0,0,0-.341.661c0,.373.249.682.739.93a4.955,4.955,0,0,0,2.157.37,8.168,8.168,0,0,0,2.5-.352,3.432,3.432,0,0,0,1.612-.965,2.125,2.125,0,0,0,.393-1.393Z" transform="translate(-32.724 -63.716)" fill="#04267b" stroke="#04267b" stroke-miterlimit="22.926" stroke-width="0.216"/>   </g> </svg>';
		}		
		else if(flag == 'hiper') {
			return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="93.481" height="75.223" viewBox="0 0 93.481 75.223">   <defs>     <clipPath id="clip-path">       <path id="Caminho_31" data-name="Caminho 31" d="M7,6h93.481V81.223H7ZM7,6" transform="translate(-7 -6)"/>     </clipPath>   </defs>   <g id="surface1" transform="translate(-7 -6)">     <g id="Grupo_9" data-name="Grupo 9" transform="translate(7 6)" clip-path="url(#clip-path)">       <path id="Caminho_30" data-name="Caminho 30" d="M18.228,19.461,70.815,7.321A14.36,14.36,0,0,1,87.988,18.051l12.141,52.587a14.289,14.289,0,0,1-2.176,11.336H17.966L7.5,36.633A14.36,14.36,0,0,1,18.228,19.461Zm0,0" transform="translate(-7.105 -6.764)" fill="#f07c00" fill-rule="evenodd"/>     </g>     <path id="Caminho_32" data-name="Caminho 32" d="M77.679,203.125H73.345v-9.446H64.5v9.446H60.172V181.313H64.5v8.517h8.84v-8.517h4.334Zm0,0" transform="translate(-42.574 -140.37)" fill="#fff" fill-rule="evenodd"/>     <path id="Caminho_33" data-name="Caminho 33" d="M169.987,174.956a2.366,2.366,0,0,1-.213,1.009,2.613,2.613,0,0,1-1.43,1.379,2.587,2.587,0,0,1-1.031.207,2.411,2.411,0,0,1-1-.207,2.634,2.634,0,0,1-.818-.555,2.7,2.7,0,0,1-.561-.824,2.53,2.53,0,0,1-.2-1.009,2.583,2.583,0,0,1,.2-1.02,2.723,2.723,0,0,1,.561-.83,2.644,2.644,0,0,1,.818-.555,2.411,2.411,0,0,1,1-.207,2.587,2.587,0,0,1,1.031.207,2.6,2.6,0,0,1,1.43,1.385A2.415,2.415,0,0,1,169.987,174.956Zm0,0" transform="translate(-126.295 -133.188)" fill="#ffec00" fill-rule="evenodd"/>     <path id="Caminho_34" data-name="Caminho 34" d="M175.834,231.4v-5.041l-.218,0a8.692,8.692,0,0,1-3.151-.56,7.208,7.208,0,0,1-.689-.3c-2.589-1.332-3.892-4.057-4.171-7.892v-7.264h4.171c0,2.035.122,5.505,0,8.217a6.658,6.658,0,0,0,.409,2.058,4.122,4.122,0,0,0,.869,1.424,3.364,3.364,0,0,0,1.284.829,4.522,4.522,0,0,0,1.5.272v-12.8H178.4a1.179,1.179,0,0,1,.667.179.892.892,0,0,1,.353.56l.347,1.43a10.355,10.355,0,0,1,1.032-1,6.293,6.293,0,0,1,1.177-.79,6.191,6.191,0,0,1,1.362-.51,6.6,6.6,0,0,1,1.581-.18,5.113,5.113,0,0,1,2.382.561,5.4,5.4,0,0,1,1.872,1.615c1.477,1.99,1.659,3.108,1.659,5.633a11.12,11.12,0,0,1-.493,3.385,8.288,8.288,0,0,1-1.4,2.7,6.5,6.5,0,0,1-2.175,1.788,6.2,6.2,0,0,1-2.848.65,5.617,5.617,0,0,1-2.253-.4A5.911,5.911,0,0,1,180,224.869V231.4Zm7.657-18.605a3.906,3.906,0,0,0-1.973.466A5.543,5.543,0,0,0,180,214.622v7.225a3.571,3.571,0,0,0,1.356,1.06,4.044,4.044,0,0,0,1.57.3,3.348,3.348,0,0,0,1.469-.319,3.057,3.057,0,0,0,1.143-.976,4.858,4.858,0,0,0,.729-1.665,10.081,10.081,0,0,0,.258-2.411,11.078,11.078,0,0,0-.213-2.338,4.754,4.754,0,0,0-.611-1.558,2.389,2.389,0,0,0-.953-.874A2.849,2.849,0,0,0,183.492,212.8Zm0,0" transform="translate(-128.593 -163.364)" fill="#fff" fill-rule="evenodd"/>     <path id="Caminho_35" data-name="Caminho 35" d="M303.651,210.285a7.838,7.838,0,0,1,2.842.5,6.337,6.337,0,0,1,2.232,1.447c.625.628,1.113.875,1.463,1.792a8.8,8.8,0,0,1,.527,3.151,4.9,4.9,0,0,1-.051.779,1.41,1.41,0,0,1-.157.488.583.583,0,0,1-.291.252,1.3,1.3,0,0,1-.471.072h-9.753a7.72,7.72,0,0,0,.426,2.058,4.108,4.108,0,0,0,.869,1.424,3.36,3.36,0,0,0,1.283.829,4.625,4.625,0,0,0,1.643.275,4.859,4.859,0,0,0,1.564-.224,8.43,8.43,0,0,0,1.171-.477c.331-.168.628-.326.891-.476a1.6,1.6,0,0,1,.774-.219.921.921,0,0,1,.757.375l1.2,1.542a6.545,6.545,0,0,1-1.5,1.307,8.206,8.206,0,0,1-1.7.824,10.017,10.017,0,0,1-1.783.438,12.654,12.654,0,0,1-1.743.129c-7.469,0-9.6-8.591-5.92-13.912a5.585,5.585,0,0,1,2.455-1.732A8,8,0,0,1,303.651,210.285Zm.084,2.438a3.32,3.32,0,0,0-2.5.93,4.712,4.712,0,0,0-1.166,2.618h6.9a4.611,4.611,0,0,0-.191-1.345,3.129,3.129,0,0,0-.588-1.133,2.934,2.934,0,0,0-1.009-.785A3.409,3.409,0,0,0,303.735,212.723Zm0,0" transform="translate(-231.526 -163.567)" fill="#fff" fill-rule="evenodd"/>     <path id="Caminho_36" data-name="Caminho 36" d="M383.348,226.119V210.346H385.8a1.349,1.349,0,0,1,.891.23,1.26,1.26,0,0,1,.343.8l.213,1.659a7.055,7.055,0,0,1,1.721-2.2,3.511,3.511,0,0,1,2.282-.807,2.877,2.877,0,0,1,1.76.511l-.315,2.589a.574.574,0,0,1-.2.415.736.736,0,0,1-.443.123,4.633,4.633,0,0,1-.706-.073,5.313,5.313,0,0,0-.852-.072,3.021,3.021,0,0,0-1.025.163,2.473,2.473,0,0,0-.807.488,3.4,3.4,0,0,0-.634.779,7.026,7.026,0,0,0-.51,1.048v10.119Zm0,0" transform="translate(-301.335 -163.364)" fill="#fff" fill-rule="evenodd"/>   </g> </svg>';
		}		
		else if(flag == 'banescard') {
			return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="61" height="37" viewBox="0 0 61 37">   <image id="banescard" width="61" height="37" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAlCAYAAADx5+EfAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAACipJREFUeJztmFdQVGkahmd3aqtmay+2aueG7j5NFhBBFFBHDNANkjOIhFGSGBAEJSfJYGDMCCiKESMgKnQjiI6O1hjGHTPQ3TpbtbV7OVN7sVeu777nNAYw7Tqg1q5/1Vd0Ouf8z/+l9+Ozzz6tT+v/b/Xozey69XLnj9cEx26dwrJHZ/rHsYTu583/1a2XfaQm/wftrlYnHNUMyiK6B0z+9KuhtXqz87w5PnbTiKaTQauT/71HJ2RrB4XfvUdo+bB9GGgJnN4nfLHmgfnn4wZ94Kodspv8kNngh4yGAJo/8vZ4YbPW5YNAG03+T82QwnfcoLMILKhrYe5dhQkBZbDyL+X7KshV1ag6Nuu9eP5laHrcYPJXrU4xYVygY8tjIHjWYkldJLb3uWDrWRcErk6GwqMaqZtC0KVToO2eORr7nbClewZ29E3BkZtW0kbb7phh17eTcJjv916ZiC2aaWi+6PDsoE4PCdh5wRFb+bl4fcc902cp1HrdBtt6+DytK3aL1wzJR4LrTR4zzBeNA7QcnstTJS9v1sxAl0GBM3oF4spjIdDTubvn4cRtCySunw+HiEKYepVhYmgRQnKTcHpQQPnhuXCJyUVY4SLMSk7j96WYFpeNliv2OHlfidJWDzhF5cLcp5TXFyF1cwifKWDvd5Pgm76UUVUCC58yTI/NQQMPU6OXjw7zxjGHFr1oFVBCT1djcmQhpkTnwT6ccN6V8M1IwalBU4K5wy60EGlbgrH3e1sE5yVKkdHQ64yYshjIPCvhtWIZCvep+HcJZIyQlA1hWMYoEVSV/E001na4YUFJPK+rQdZOf7gvToNCVYH1nbNQ0+aG2YmZSKiOQjsPeFSY978DtGnvm6D3XraHXF0B+7BCRBUnIKpoEXwIa+lXCdev89B+zwz7vrdDfosXylvVKNzrhdnJGVBy883fToZX6hIoeP02HkAXNxlTES1BZzf6Yc7idN67Cikbw5HV7Ie4yji+r8b8wq/hz2coPCowj4e0cnsgyg6rsJ8F9cygYmR+6+Xn3wFaOPUm6JL9aqlghebHs0eain0Sx++aYWZCFotZHbYy59LrAzApvAA2AUU8iCxY+pfD0rccTf2ODOVVDNEidOmVzF851KnL6MEqbOh0g2NEAUznVcKBEeQQWYDJUfmYuiAXy74JwbazU+GTnsK0KoWpZwVco7Nx6IbNy0Xt3aAV9a+FZmiH5CTQU7VYvcsHrddscYhWecyNeVvM/KxFSl0EASsQWxmN08zFxnNOsAlcA/eUNBQf8OBBlEHFmtD9SM5NT8BkQjlE5OPg9Ylwmp/PfM9B15AZ2u9aIKPeD4lrw1HWqkJU6UJGhys6H1ggppT1gxFQckAFjUH+66E1ekXuc8Ex0trp0dmJK6X8nMLNusVnYmb8KjjQq1b+ZYgoSETBHm9Y87VbUgbSG4IYzkvpSYYo02DxhnB6qZrVPw5dj2RoYrhbBZQyr5ejc0BAUHYSPV2FsPwEBOUksZitQUBmEtPEA+a+lTysNEkTzGZ+W/iVob5vqrGQScVs2N4ROrRbr3hCw2g7+mcrhOYmYu6StBHmzaq6mps5cnMCDv0wQSpWYs5PX5iF+JpouPM3qxv9sWxjGNTceCk91/VQhh3npsBjaTpW7gjg/U1Qz/chuclS1IjhHUjwlsuT0HbbHEs3hsA5Lhc2wYWETkfq1mDWBIGgipH2TtBDgg1bxN9eBf3cBPQ8tMH5nzxxTheC/kcqUQoaw4ybN6aDCUNvON9GmdQFDCOt2zAsOsRwZRvsNjyXt+I9tcP36zFIbUmqJZoxg9Yp/0CwC6+C1RjMcdbggj6dH7ruhaP9VhCO3wyG5n4keh/av6ySXqGann7+VrX1KvU1/Lr5ghOCmQrF+z3QNaj89dDiopRbqREhR1nfQyf0PkjAkWvzcPCaBw5cV/GvGid+CECvYabRs6+CfA34f2yjrl1zQC1V/Jxd86TiOibQ5x4JnzN8Bo3F4fkNe/QW6BtcyIrtPmwqmhqtV9XoHQrlA18MN7m0obpTM7B0QygSayOwvmMGTlJWiqmwhworu8kX8RQYy5mvO9iSRFnZ8t1E5DVzeOlyxWpW73wqvKOs8rm7vJFUOx9lR9wpWmIkaKmQjRW0uDRDsuLR0KJd+ksYPe2JQ1c9aCocu+GPzlvh6L2XitYrfjh111nq3+Jv83d7wzEyD5Pn51Ja5sCBxW3VDn8c/9EGbsnpcOTnc1mJ7UJK8FV8FvZcckDmdn9YsmpPi82CbUCxpNw82csnUH66sp050yYGF1HslOPYHStj2o0Z9ICgZJj/PBq6z2CP07eiGNKB6L4fi/7BDOzR1mB59Vp4JmxDQUM5waey4lqzChdiUkQeak7OxJazrjCjsJiTtAK1bbMwKymTFd0PHXctEVsRB6VXFda2zUZUYZw0uMzloWTxexWBBU+2MXaNpgtTkM6qbUZxMiksH2dY8LqkPj1G0OLS6gRv2i8vQlOxETQFG1prEZ2/nmJjA0Othhs1mrXvejR0x1FYBFG5rUUGW9UZg8AWJaBjwJLiwopCxA5L68IQTk3usWQFrNlz7YJLUM7QnR6bLfX/lkuOUro4RWVLquzgDTsJrpkTmE1gCfwyFkuDjjjwjCm0ZlD2e95oN+3xi+AdNyJhG7gWMooOmUct5DSFaO5Gy9yci/iqOOmz2nY3aWPH7lhjk+Yr1J124xiaBJfoLCysiMGqhgAKFDGcc6i83BnapZzYYjhSmhPAnNFRQgGUgRPU9KIIqTjiQQFTgfjayOGWNsbQRm/LHWhXtC8UqT69C1TJmwhdRegaCfwpsGjhmdVYuWkxJ6Z1SN0SRmBbpG8PguBdDHVaijRJxdcuwKkBC2zmISjEqYuDSF6zL5S8Z/ZOH6lVaQ2mPJAi1oUCNF50Qiu9PSshXVJjeRxojJDjAC0uVtUvtTrZberyJ8YQV+Dkj2okVayjZ9ZJ/0WZG78JqevK0HIhhhueQc9YYQ4lqym/E6cq5TweRH0gBxJXbjwTZl7VsA/haMpwlvFwkjl/R5fGSDO5OIEZhYoJD43jprqS96iENUda66A1cOY1O89PfrlyjyW0uAg9h62mjx43glMNddyehibtAmzvXIRDl33QNcDNMn9FNSWG9G5W48IWb6xu8kPV8TkcO01xmmJCLEhZjQEo2ueJ+v4pqD7mjl0XHQnrhIrjs9F+3+yZqjtxy1wKe7Hqrz/phm/YAjd3T0PnfdPxh5bAh0ycCHvDGE5GefhcNDwNteEB4CVxI3/LdybDEtbEKEvf8L+wEc8cb2hxUXX9VqtXrqLXf3nrBv4rezYlvWy619l7gpbAB4Uv+FB/wrbRfn7tBj6EjRf003VmQP4Fcz2Fhe0a7fEHB34f0C8u5rotbQnhTzH8f9IahCc9BiW0+leMf/8r0OI690D9mx698GWPXmnb81DpTGgX0biZ92d6+cT3Cv1pfVofx/o3u5936rqtQ7MAAAAASUVORK5CYII="/> </svg>';
		}
		else if(flag == 'visa') {
			if(response == 'true'){
				return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="174.633" height="56.393" viewBox="0 0 174.633 56.393">   <defs>     <linearGradient id="linear-gradient" x1="0.46" y1="1.02" x2="0.549" gradientUnits="objectBoundingBox">       <stop offset="0" stop-color="#222357"/>       <stop offset="1" stop-color="#254aa5"/>     </linearGradient>   </defs>   <g id="visa-seeklogo.com" transform="translate(0 0)">     <path id="Caminho_1" data-name="Caminho 1" d="M90.316-64.64c-.1,7.855,7,12.239,12.35,14.846,5.5,2.674,7.341,4.389,7.32,6.78-.042,3.66-4.384,5.275-8.448,5.338a29.48,29.48,0,0,1-14.489-3.445L84.5-29.17c3.288,1.515,9.376,2.837,15.69,2.895,14.82,0,24.516-7.315,24.568-18.658.058-14.395-19.911-15.192-19.774-21.626.047-1.951,1.909-4.033,5.988-4.562a26.563,26.563,0,0,1,13.913,2.438l2.48-11.563a37.822,37.822,0,0,0-13.2-2.423c-13.949,0-23.76,7.415-23.839,18.029m60.877-17.032a6.431,6.431,0,0,0-6,4l-21.17,50.546h14.809l2.947-8.144h18.1l1.71,8.144h13.052l-11.39-54.547h-12.05m2.072,14.735,4.274,20.483h-11.7l7.431-20.483m-80.9-14.735L60.688-27.125H74.8L86.467-81.672H72.361m-20.876,0L36.8-44.545,30.855-76.114a6.578,6.578,0,0,0-6.508-5.559H.336L0-80.088c4.929,1.07,10.53,2.8,13.923,4.641,2.077,1.127,2.669,2.113,3.351,4.793l11.253,43.53H43.441L66.3-81.672H51.485" transform="translate(0 82.668)" fill="url(#linear-gradient)"/>   </g> </svg>';
			}
			else {
				return flagVisa;
			}
		}
		else if(flag == 'diners') {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="102.575" height="81.264" viewBox="0 0 102.575 81.264">   <g id="__x0023_Layer_x0020_1" transform="translate(0 0)">     <path id="Caminho_37" data-name="Caminho 37" d="M60.092,81.264c22.211.106,42.483-18.114,42.483-40.279C102.575,16.746,82.3-.008,60.092,0H40.977C18.5-.008,0,16.751,0,40.985,0,63.155,18.5,81.37,40.977,81.264Z" fill="#0079be"/>     <path id="Caminho_38" data-name="Caminho 38" d="M61.257,20.83a37.2,37.2,0,1,0,37.193,37.2A37.2,37.2,0,0,0,61.257,20.83Zm-23.571,37.2h0a23.615,23.615,0,0,1,15.132-22v44A23.6,23.6,0,0,1,37.686,58.031ZM69.693,80.04h0V36.025a23.564,23.564,0,0,1,0,44.015Z" transform="translate(-20.19 -17.471)" fill="#fff"/>     <path id="Caminho_76" data-name="Caminho 76" d="M60.092,81.264c22.211.106,42.483-18.114,42.483-40.279C102.575,16.746,82.3-.008,60.092,0H40.977C18.5-.008,0,16.751,0,40.985,0,63.155,18.5,81.37,40.977,81.264Z" fill="#0079be"/>     <path id="Caminho_77" data-name="Caminho 77" d="M61.257,20.83a37.2,37.2,0,1,0,37.193,37.2A37.2,37.2,0,0,0,61.257,20.83Zm-23.571,37.2h0a23.615,23.615,0,0,1,15.132-22v44A23.6,23.6,0,0,1,37.686,58.031ZM69.693,80.04h0V36.025a23.564,23.564,0,0,1,0,44.015Z" transform="translate(-20.19 -17.471)" fill="#fff"/>   </g> </svg>';
		}
		else {
			return '&nbsp;';
		}
	},
	setBandeiraInfo: function(el, flag, type = 'card') {
		jQuery(el).removeClass('flag-visa');
		jQuery(el).removeClass('flag-mastercard');
		jQuery(el).removeClass('flag-elo');
		jQuery(el).removeClass('flag-diners');
		jQuery(el).removeClass('flag-amex');
		jQuery(el).removeClass('flag-aura');
		jQuery(el).removeClass('flag-jcb');
		jQuery(el).removeClass('flag-hiper');
		jQuery(el).removeClass('flag-hipercard');
		jQuery(el).removeClass('flag-banescard');
		
		if(flag == 'mastercard') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" width="29.156" height="18.02" viewBox="0 0 29.156 18.02"><g id="mastercard-seeklogo.com" transform="translate(0 0)"><rect id="Retângulo_1" data-name="Retângulo 1" width="7.884" height="14.165" transform="translate(10.636 1.927)" fill="#ff5f00"/><g id="Grupo_6645" data-name="Grupo 6645"><path id="Caminho_3" data-name="Caminho 3" d="M11.137,9.01a9.034,9.034,0,0,1,3.429-7.083A9.006,9.006,0,1,0,9.01,18.019a8.954,8.954,0,0,0,5.556-1.927A9,9,0,0,1,11.137,9.01Z" fill="#eb001b"/><path id="Caminho_4" data-name="Caminho 4" d="M142.549,9.01a9,9,0,0,1-14.566,7.083,9.03,9.03,0,0,0,0-14.165A9,9,0,0,1,142.549,9.01Z" transform="translate(-113.393)" fill="#f79e1b"/></g></g></svg>');
			jQuery(el).addClass('flag-mastercard');
		}
		else if(flag == 'amex') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" width="165.415" height="57.501" viewBox="0 0 165.415 57.501"><g id="american-express" transform="translate(0 -164.4)"><path id="Caminho_10" data-name="Caminho 10" d="M43.2,192.552h5.776L46.088,185.2Z" transform="translate(-29.022 -13.973)" fill="#2fabf7"/><path id="Caminho_11" data-name="Caminho 11" d="M246.552,187.863a5.118,5.118,0,0,0-2.1-.263H239.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,246.552,187.863Z" transform="translate(-160.694 -15.586)" fill="#228fe0"/><path id="Caminho_12" data-name="Caminho 12" d="M142.046,164.4v3.151l-1.575-3.151h-12.34v3.151l-1.575-3.151h-16.8a15.985,15.985,0,0,0-7.352,1.575V164.4H90.584v1.575A7.57,7.57,0,0,0,85.6,164.4H43.323l-2.888,6.564L37.546,164.4H24.156v3.151L22.58,164.4H11.29L6.039,176.74,0,190.394H13.391l1.575-4.2h3.676l1.575,4.2H35.446v-3.151l1.313,3.151h7.614l1.313-3.151v3.151h36.5v-6.827h.525c.525,0,.525,0,.525.788v5.776h18.9v-1.575a15.729,15.729,0,0,0,7.089,1.575H117.1l1.575-4.2h3.676l1.575,4.2h15.229v-3.938l2.363,3.938h12.34V164.4ZM53.3,186.455H48.837V172.014l-6.3,14.441H38.6l-6.3-14.441v14.441H23.368l-1.838-3.938H12.6l-1.575,4.2H6.039l7.877-18.642H20.48l7.352,17.592V168.076h7.089l5.776,12.6,5.251-12.6H53.3Zm17.854-14.441H60.915v3.413h9.977V179.1H60.915v3.676h10.24v3.938h-14.7V168.076h14.7Zm19.692,7.614a6.569,6.569,0,0,1,.788,3.413v3.676H87.171v-2.363a6.254,6.254,0,0,0-.788-3.676c-.788-.788-1.575-.788-3.151-.788H78.506v6.827H74.043V168.076H84.02c2.363,0,3.938,0,5.251.788a4.474,4.474,0,0,1,2.1,4.2,5.227,5.227,0,0,1-3.151,4.989A4.993,4.993,0,0,1,90.847,179.629Zm7.877,6.827H94.26V167.813h4.464Zm51.725,0h-6.3l-8.4-13.916v13.916h-8.927l-1.575-3.938h-9.19l-1.575,4.2h-4.989c-2.1,0-4.726-.525-6.3-2.1s-2.363-3.676-2.363-7.089a10.762,10.762,0,0,1,2.363-7.352c1.313-1.575,3.676-2.1,6.564-2.1h4.2v3.938h-4.2a4.842,4.842,0,0,0-3.413,1.05,6.165,6.165,0,0,0-1.313,4.2c0,2.1.263,3.413,1.313,4.464a4.185,4.185,0,0,0,3.151,1.05h1.838l6.039-14.441h6.564l7.352,17.592V168.338h6.564L145.46,181.2V168.338h4.464v18.117Z" fill="#0571c1"/><g id="Grupo_3" data-name="Grupo 3" transform="translate(66.166 171.227)"><path id="Caminho_13" data-name="Caminho 13" d="M358.4,192.552h6.039l-2.888-7.352Z" transform="translate(-306.938 -185.2)" fill="#228fe0"/><path id="Caminho_14" data-name="Caminho 14" d="M208.427,292.166V277.2l-6.827,7.352Z" transform="translate(-201.6 -247.005)" fill="#228fe0"/></g><path id="Caminho_15" data-name="Caminho 15" d="M136.8,282.8v3.413h9.715v3.676H136.8v3.938h10.765l4.989-5.514-4.726-5.514Z" transform="translate(-91.902 -79.541)" fill="#2fabf7"/><path id="SVGCleanerId_0" d="M241.514,282.8H236v4.726h5.776c1.575,0,2.626-.788,2.626-2.363A2.648,2.648,0,0,0,241.514,282.8Z" transform="translate(-158.544 -79.541)" fill="#228fe0"/><path id="Caminho_16" data-name="Caminho 16" d="M238.805,272.215V260.4H227.778a8.615,8.615,0,0,0-5.514,1.575V260.4H210.186c-1.838,0-4.2.525-5.251,1.575V260.4H183.667v1.575c-1.575-1.313-4.464-1.575-5.776-1.575H163.713v1.575c-1.313-1.313-4.464-1.575-6.039-1.575H141.92l-3.676,3.938-3.413-3.938H111.2v25.731h23.106l3.676-3.938,3.413,3.938h14.178v-6.039h1.838a15.972,15.972,0,0,0,6.039-.788v7.089h11.815v-6.827h.525c.788,0,.788,0,.788.788v6.039h35.709c2.363,0,4.726-.525,6.039-1.575v1.575h11.29c2.363,0,4.726-.263,6.3-1.313h0a8.938,8.938,0,0,0,4.2-7.877A10.055,10.055,0,0,0,238.805,272.215Zm-81.394,4.2H152.16v6.3h-8.4l-5.251-6.039-5.514,6.039H115.664V264.076h17.592l5.251,6.039,5.514-6.039h13.916c3.413,0,7.352,1.05,7.352,6.039C165.025,275.366,161.349,276.416,157.411,276.416Zm26.256-1.05a5.958,5.958,0,0,1,.788,3.413v3.676h-4.464v-2.363c0-1.05,0-2.888-.788-3.676-.525-.788-1.575-.788-3.151-.788h-4.726v6.827h-4.464V263.813h9.977c2.1,0,3.938,0,5.251.788a4.644,4.644,0,0,1,2.363,4.2,5.227,5.227,0,0,1-3.151,4.989A4.491,4.491,0,0,1,183.667,275.366Zm18.117-7.614h-10.24v3.413h9.977v3.676h-9.977v3.676h10.24v3.938h-14.7V263.813h14.7Zm11.028,14.7h-8.4v-3.938h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.219,2.219,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-5.514,6.3-5.514h8.665v4.464h-8.139a3.894,3.894,0,0,0-1.838.263c-.525.263-.525.788-.525,1.313,0,.788.525,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363c2.363,0,3.938.525,4.989,1.575a5.435,5.435,0,0,1,1.313,3.938C219.638,280.617,217.275,282.455,212.812,282.455Zm22.58-1.838a7.657,7.657,0,0,1-5.514,1.838h-8.4v-3.938h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.219,2.219,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-5.514,6.3-5.514h8.665v4.464H228.04a3.893,3.893,0,0,0-1.838.263c-.525.263-.525.788-.525,1.313,0,.788.263,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363c2.363,0,3.938.525,4.989,1.575a.257.257,0,0,1,.263.263,6.028,6.028,0,0,1,1.05,3.676A5.344,5.344,0,0,1,235.392,280.617Z" transform="translate(-74.704 -64.492)" fill="#0571c1"/><path id="SVGCleanerId_1" d="M302.552,283.863a5.119,5.119,0,0,0-2.1-.263H295.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,302.552,283.863Z" transform="translate(-198.314 -80.078)" fill="#228fe0"/><g id="Grupo_4" data-name="Grupo 4" transform="translate(66.166 171.227)"><path id="Caminho_17" data-name="Caminho 17" d="M246.552,187.863a5.118,5.118,0,0,0-2.1-.263H239.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,246.552,187.863Z" transform="translate(-226.86 -186.812)" fill="#228fe0"/><path id="Caminho_18" data-name="Caminho 18" d="M358.4,192.552h6.039l-2.888-7.352Z" transform="translate(-306.938 -185.2)" fill="#228fe0"/><path id="Caminho_19" data-name="Caminho 19" d="M208.427,292.166V277.2l-6.827,7.352Z" transform="translate(-201.6 -247.005)" fill="#228fe0"/></g><g id="Grupo_5" data-name="Grupo 5" transform="translate(77.456 203.259)"><path id="SVGCleanerId_0_1_" d="M241.514,282.8H236v4.726h5.776c1.575,0,2.626-.788,2.626-2.363A2.648,2.648,0,0,0,241.514,282.8Z" transform="translate(-236 -282.8)" fill="#228fe0"/></g><g id="Grupo_6" data-name="Grupo 6" transform="translate(96.886 203.522)"><path id="SVGCleanerId_1_1_" d="M302.552,283.863a5.119,5.119,0,0,0-2.1-.263H295.2v4.2h5.251a5.119,5.119,0,0,0,2.1-.263,2.044,2.044,0,0,0,.788-1.838A1.46,1.46,0,0,0,302.552,283.863Z" transform="translate(-295.2 -283.6)" fill="#228fe0"/></g><g id="Grupo_7" data-name="Grupo 7" transform="translate(0 164.4)"><path id="Caminho_20" data-name="Caminho 20" d="M155.836,281.93l-3.676-3.938v4.464H143.5l-5.251-6.039-5.776,6.039H115.138V264.076H132.73l5.514,6.039,2.626-3.151-6.564-6.564H111.2v25.731h23.106l3.938-3.938,3.413,3.938h14.178Z" transform="translate(-74.704 -228.892)" fill="#2fabf7"/><path id="Caminho_21" data-name="Caminho 21" d="M53.825,190.131l-3.413-3.676H48.837V184.88L44.9,180.941l-2.626,5.514H38.6l-6.3-14.441v14.441H23.368l-1.838-3.938H12.6l-1.838,3.938H6.039l7.877-18.379H20.48l7.352,17.592V168.076H31.77L28.094,164.4H24.156v3.151L22.843,164.4H11.29L6.039,176.74,0,190.131H13.653l1.575-3.938H18.9l1.838,3.938h14.7V186.98l1.313,3.151h7.614l1.313-3.151v3.151Z" transform="translate(0 -164.4)" fill="#2fabf7"/><path id="Caminho_22" data-name="Caminho 22" d="M118.6,197.4l-4.2-4.2,3.151,6.827Z" transform="translate(-76.854 -183.748)" fill="#2fabf7"/></g><g id="Grupo_8" data-name="Grupo 8" transform="translate(25.994 164.663)"><path id="Caminho_23" data-name="Caminho 23" d="M278.375,283.206a9.6,9.6,0,0,0,4.2-7.089l-3.676-3.676a7.768,7.768,0,0,1,.525,2.626,5.344,5.344,0,0,1-1.575,3.938,7.657,7.657,0,0,1-5.514,1.838h-8.4V276.9h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.22,2.22,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-4.989,5.514-5.514l-2.888-2.888c-.525.263-.788.525-1.05.525V258H252.906c-1.838,0-4.2.525-5.251,1.575V258h-21.53v1.575c-1.575-1.313-4.464-1.575-5.776-1.575H206.17v1.575c-1.313-1.313-4.464-1.575-6.039-1.575H184.377l-3.676,3.938L177.288,258H174.4l7.877,7.877,3.938-4.2h13.916c3.413,0,7.352,1.05,7.352,6.039,0,5.251-3.676,6.3-7.614,6.3h-5.251v3.938l3.938,3.938v-3.938h1.313a15.972,15.972,0,0,0,6.039-.788v7.089h11.815V277.43h.525c.788,0,.788,0,.788.788v6.039h35.709c2.363,0,4.726-.525,6.039-1.575v1.575h11.29a10.417,10.417,0,0,0,6.3-1.05Zm-52.25-9.452a5.958,5.958,0,0,1,.788,3.413v3.676h-4.464V278.48c0-1.05,0-2.888-.788-3.676-.525-.788-1.575-.788-3.151-.788h-4.726v6.827h-4.464V262.2H219.3c2.1,0,3.938,0,5.251.788a4.644,4.644,0,0,1,2.363,4.2,5.227,5.227,0,0,1-3.151,4.989A4.49,4.49,0,0,1,226.125,273.754Zm18.117-7.614H234v3.413h9.977v3.676H234V276.9h10.24v3.938h-14.7V262.2h14.7Zm11.028,14.7h-8.4V276.9h8.4a2.215,2.215,0,0,0,1.838-.525,1.9,1.9,0,0,0,0-2.626,2.22,2.22,0,0,0-1.575-.525c-4.2-.263-9.19,0-9.19-5.776,0-2.626,1.575-5.514,6.3-5.514h8.665V266.4h-8.139a3.894,3.894,0,0,0-1.838.263c-.525.263-.525.788-.525,1.313,0,.788.525,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363c2.363,0,3.938.525,4.989,1.575a5.435,5.435,0,0,1,1.313,3.938C262.1,279.005,259.733,280.843,255.269,280.843Z" transform="translate(-143.155 -227.543)" fill="#228fe0"/><path id="Caminho_24" data-name="Caminho 24" d="M459.2,285.175c0,.788.263,1.05,1.05,1.313a3.33,3.33,0,0,0,1.575.263h2.363a7.625,7.625,0,0,1,3.676.788l-3.938-3.938h-2.363a3.893,3.893,0,0,0-1.838.263A2.006,2.006,0,0,0,459.2,285.175Z" transform="translate(-334.483 -244.741)" fill="#228fe0"/><path id="Caminho_25" data-name="Caminho 25" d="M431.2,240.4l.525.788h.263Z" transform="translate(-315.672 -215.719)" fill="#228fe0"/><path id="Caminho_26" data-name="Caminho 26" d="M387.2,196.4l4.464,10.765v-6.3Z" transform="translate(-286.113 -186.16)" fill="#228fe0"/><path id="Caminho_27" data-name="Caminho 27" d="M135.388,184.1h.525c.525,0,.525,0,.525.788v5.776h18.9v-1.575a15.729,15.729,0,0,0,7.089,1.575h7.877l1.575-4.2h3.676l1.575,4.2h15.229v-2.626l-3.676-3.676v2.888h-8.927l-1.313-4.2h-9.19l-1.575,4.2h-4.989c-2.1,0-4.726-.525-6.3-2.1s-2.363-3.676-2.363-7.089a10.762,10.762,0,0,1,2.363-7.352c1.313-1.575,3.676-2.1,6.564-2.1h4.2v3.938h-4.2a4.842,4.842,0,0,0-3.413,1.05,6.165,6.165,0,0,0-1.313,4.2c0,2.1.263,3.413,1.313,4.464a4.185,4.185,0,0,0,3.151,1.05h1.838l6.039-14.441H173.2l-3.676-3.676h-6.827a15.986,15.986,0,0,0-7.352,1.575V165.2H143.79v1.575A7.57,7.57,0,0,0,138.8,165.2H96.529l-2.888,6.564L90.753,165.2H79.2l3.676,3.676h5.251l4.464,9.715,1.575,1.575,4.726-11.553h7.352v18.642H101.78V172.814l-4.464,10.5,7.614,7.614h30.195Zm12.078-15.491h4.464v18.642h-4.464Zm-23.106,3.938h-10.24v3.413H124.1v3.676h-9.977v3.676h10.24v3.938h-14.7V168.613h14.7Zm7.352,14.441h-4.464V168.351h9.977c2.363,0,3.938,0,5.251.788a4.474,4.474,0,0,1,2.1,4.2,5.227,5.227,0,0,1-3.151,4.989,3.437,3.437,0,0,1,2.1,1.575,6.569,6.569,0,0,1,.788,3.413v3.676h-4.464V184.63a6.254,6.254,0,0,0-.788-3.676c-.263-.525-1.05-.525-2.626-.525h-4.726v6.564Z" transform="translate(-79.2 -165.2)" fill="#228fe0"/></g></g></svg>');
			jQuery(el).addClass('flag-amex');
		}
		else if(flag == 'hipercard') {
			jQuery(el).html('<svg id="hipercard-29" xmlns="http://www.w3.org/2000/svg" width="172.818" height="75.223" viewBox="0 0 172.818 75.223"><path id="Caminho_28" data-name="Caminho 28" d="M47.91,267.92H30c-7.913.373-14.383,3.562-16.252,10.135-.973,3.427-1.509,7.192-2.272,10.745C7.607,306.87,4.173,325.435.47,343.143H139.914c10.78,0,18.182-2.279,20.18-10.834.93-3.977,1.821-8.472,2.709-12.843,3.458-17.027,6.935-34.05,10.484-51.547Z" transform="translate(-0.47 -267.92)" fill="#822124"/><path id="Caminho_29" data-name="Caminho 29" d="M118.615,401.447c.75-.519,1.717-2.868.614-3.849a2.081,2.081,0,0,0-1.75-.261,2.106,2.106,0,0,0-1.487.787c-.475.647-.911,2.593-.173,3.323S118.138,401.775,118.615,401.447Zm-10.635-3.826c-.539,3.5-1.146,6.928-1.755,10.356-3.911.042-7.9.192-11.672-.088.712-3.354,1.222-6.916,1.929-10.268h-4.22c-1.508,8.557-2.879,17.248-4.563,25.626H92c.674-4.3,1.305-8.645,2.194-12.725a104.978,104.978,0,0,1,11.584.088c-.726,4.246-1.6,8.34-2.282,12.637h4.3c1.383-8.681,2.839-17.289,4.564-25.626Zm60.584,7.234c-3.333-1.347-5.953.928-7.168,3.06.275-.949.389-2.059.612-3.06h-3.332c-.813,6.3-2.009,12.213-3.147,18.185h3.76c.519-3.544.754-8.323,1.923-11.715.934-2.709,3.378-5.014,6.916-3.759.04-1,.327-1.763.427-2.711Zm2.1,14a9.11,9.11,0,0,1-.346-3.5c.194-2.528,1.115-5.6,2.536-7,1.961-1.923,5.833-1.6,8.921-.519.1-1.037.3-1.969.437-2.974-5.064-.826-9.871-.311-12.419,2.362-2.514,2.605-4.143,8.622-2.988,12.417,1.354,4.43,7.421,4.668,12.333,2.974.218-.89.334-1.883.519-2.8-2.678,1.381-7.81,2.111-9-.97Zm42.243-13.911c-3.323-1.661-6.089,1.126-7.168,2.8.306-.864.325-2.006.61-2.886h-3.33q-1.343,9.362-3.235,18.18h3.847a41.056,41.056,0,0,1,.875-6.556c.8-5.047,1.983-10.583,7.868-8.918.2-.854.277-1.822.519-2.623Zm-98.154.118c-.1.016-.093.137-.086.259-.818,6.126-1.928,11.961-3.112,17.72H115.3c.9-6.236,1.938-12.341,3.235-18.185l-3.76.206Zm33.081-.463a9.821,9.821,0,0,0-6.648,2.711c-2,2.109-3.631,6.772-3.148,11.02.678,6.051,8.223,5.842,14.257,4.374.1-1.065.361-1.973.519-2.974-2.486.93-6.8,2.227-9.36.612-1.935-1.226-1.935-4.317-1.312-7,4.054-.13,8.271-.105,12.333,0,.258-1.9.994-3.977.346-5.861-.852-2.483-3.894-3.126-6.994-2.886Zm3.586,6.663h-8.835a5.214,5.214,0,0,1,4.985-4.378c2.709-.1,4.649.994,3.849,4.372Zm-17.867-6.205c-3.173-1.191-7.04.232-8.717,1.585,0,.059-.04.067-.09.073l.09-.073a.049.049,0,0,0,0-.016c.028-.581.233-.987.261-1.57h-3.228c-1.343,8.946-2.939,17.635-4.62,26.247H121c.543-3.352.9-6.888,1.656-10.028.864,3.3,6.447,2.671,8.807,1.4C136.332,420.042,140.087,407.514,133.557,405.06ZM130.6,419.729c-2.012,2.13-6.959,2.1-7.346-1.489-.173-1.556.411-3.2.7-4.81s.5-3.2.787-4.635c1.981-2.421,7.806-2.713,8.4,1.312.514,3.491-.87,7.856-2.536,9.622ZM226.893,397c-.322,2.823-.752,5.533-1.311,8.126-9.217-2.918-14.869,3.864-14.767,12.234a6.594,6.594,0,0,0,1.311,4.369c1.745,1.971,6.743,2.445,9.262.785A6.489,6.489,0,0,0,222.7,421.2c.244-.3.629-1.1.7-.864a18.745,18.745,0,0,0-.346,2.708h3.408c.657-9.421,2.687-17.462,4.193-26.038ZM218.5,421.127c-2.531.054-3.79-1.513-3.849-4.111-.1-4.551,1.9-9.6,5.949-10.059a9.865,9.865,0,0,1,4.635.7C223.968,412.768,224.426,421,218.5,421.127Zm-32.449-16.28c-.185,1.037-.47,1.98-.692,2.974,2.22-.557,9.131-2.263,9.8.692a5.185,5.185,0,0,1-.437,2.8c-6.248-.591-11.342.446-12.682,4.9-.9,2.982.1,5.916,2.011,6.743,3.681,1.577,8.159-.23,9.71-2.711a11.3,11.3,0,0,0-.263,2.8h3.237a50.364,50.364,0,0,1,.961-8.4c.406-2.376,1.171-4.727,1.049-6.822-.278-4.8-8.231-3.1-12.682-2.974Zm6.127,14.8c-1.938,1.9-7.379,2.436-6.822-2.1.462-3.767,4.564-4.568,9.008-4.025C194.035,415.586,193.656,418.2,192.18,419.649Z" transform="translate(-72.618 -374.683)" fill="#fff"/></svg>');
			jQuery(el).addClass('flag-hipercard');
		}		
		else if(flag == 'jcb') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104.253" height="78.243" viewBox="0 0 104.253 78.243"><defs><linearGradient id="linear-gradient" x1="-0.749" y1="0.885" x2="1.828" y2="0.885" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#007940"/><stop offset="0.228" stop-color="#00873f"/><stop offset="0.743" stop-color="#40a737"/><stop offset="1" stop-color="#5cb531"/></linearGradient><linearGradient id="linear-gradient-2" x1="-0.058" y1="0.541" x2="0.831" y2="0.541" xlink:href="#linear-gradient"/><linearGradient id="linear-gradient-3" x1="-0.818" y1="1.102" x2="1.995" y2="1.102" xlink:href="#linear-gradient"/><linearGradient id="linear-gradient-4" x1="0.191" y1="0.541" x2="1.094" y2="0.541" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#1f286f"/><stop offset="0.475" stop-color="#004e94"/><stop offset="0.826" stop-color="#0066b1"/><stop offset="1" stop-color="#006fbc"/></linearGradient><linearGradient id="linear-gradient-5" x1="0.059" y1="0.54" x2="0.937" y2="0.54" gradientUnits="objectBoundingBox"><stop offset="0" stop-color="#6c2c2f"/><stop offset="0.174" stop-color="#882730"/><stop offset="0.573" stop-color="#be1833"/><stop offset="0.859" stop-color="#dc0436"/><stop offset="1" stop-color="#e60039"/></linearGradient></defs><g id="g6321" transform="translate(-26.794 80.328)"><g id="g6323" transform="translate(26.794 -80.328)"><g id="g6327" transform="translate(72.081 0)"><path id="path6338" d="M234.976,139.667H242.5c.215,0,.717-.072.932-.072a3.357,3.357,0,0,0,2.651-3.368,3.478,3.478,0,0,0-2.651-3.368,3.785,3.785,0,0,0-.932-.072h-7.523Z" transform="translate(-228.527 -92.162)" fill="url(#linear-gradient)"/><path id="path6349" d="M231.695,29.509a13.042,13.042,0,0,0-13.041,13.041V56.091h18.414a7.368,7.368,0,0,1,1.29.072c4.156.215,7.237,2.365,7.237,6.09,0,2.938-2.078,5.446-5.947,5.947v.143c4.227.287,7.452,2.651,7.452,6.305,0,3.941-3.583,6.52-8.312,6.52H218.582V107.68h19.131A13.042,13.042,0,0,0,250.754,94.64V29.509H231.695Z" transform="translate(-218.582 -29.509)" fill="url(#linear-gradient-2)"/><path id="path6360" d="M245.151,110.076a3.048,3.048,0,0,0-2.651-3.081c-.143,0-.5-.072-.716-.072h-6.807v6.305h6.807a2,2,0,0,0,.716-.072,3.048,3.048,0,0,0,2.651-3.081Z" transform="translate(-228.527 -76.471)" fill="url(#linear-gradient-3)"/></g><path id="path6371" d="M48.45,29.509A13.042,13.042,0,0,0,35.409,42.549V74.721a25.788,25.788,0,0,0,11.249,2.938c4.514,0,6.95-2.723,6.95-6.449V56.02H64.786V71.138c0,5.875-3.654,10.676-16.05,10.676a55,55,0,0,1-13.4-1.648v27.443H54.468A13.042,13.042,0,0,0,67.509,94.568V29.509H48.45Z" transform="translate(-35.337 -29.509)" fill="url(#linear-gradient-4)"/><path id="path6384" d="M140.183,29.509a13.042,13.042,0,0,0-13.041,13.041V59.6c3.3-2.794,9.028-4.586,18.271-4.156a58.141,58.141,0,0,1,10.246,1.576V62.54a24.8,24.8,0,0,0-9.888-2.866c-7.022-.5-11.249,2.938-11.249,8.956,0,6.09,4.227,9.53,11.249,8.956a26.024,26.024,0,0,0,9.888-2.866v5.517a56.757,56.757,0,0,1-10.246,1.576c-9.243.43-14.975-1.361-18.271-4.156v30.094h19.131a13.042,13.042,0,0,0,13.041-13.041v-65.2H140.183Z" transform="translate(-91.03 -29.509)" fill="url(#linear-gradient-5)"/></g></g></svg>');
			jQuery(el).addClass('flag-jcb');
		}		
		else if(flag == 'elo') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" width="213.684" height="78.243" viewBox="0 0 213.684 78.243">   <g id="Page-1" transform="translate(-0.9 -0.1)">     <g id="elo" transform="translate(0.9 0.1)">       <path id="Shape" d="M85.6,17.387a22.689,22.689,0,0,1,7.284-1.175A23.016,23.016,0,0,1,115.443,34.64l15.776-3.222A39.115,39.115,0,0,0,80.5,2.114Z" transform="translate(-53.781 -0.1)" fill="#fff100"/>       <path id="Shape-2" data-name="Shape" d="M14.092,88.007,24.766,75.923a23.035,23.035,0,0,1,0-34.473L14.092,29.4a39.143,39.143,0,0,0,0,58.607Z" transform="translate(-0.9 -19.565)" fill="#00a3df"/>       <path id="Shape-3" data-name="Shape" d="M115.376,130.4A23.085,23.085,0,0,1,85.5,147.62l-5.1,15.273a39.156,39.156,0,0,0,50.753-29.27Z" transform="translate(-53.715 -86.663)" fill="#ee4023"/>       <g id="Group" transform="translate(96.3 9.869)">         <path id="Shape-4" data-name="Shape" d="M34.5,45.177A13.962,13.962,0,0,1,24.46,49.2a13.648,13.648,0,0,1-7.25-2.182l-5.236,8.324a23.916,23.916,0,0,0,29.4-3.122ZM25.03,11.241A23.872,23.872,0,0,0,4.656,48.131l43.234-18.5A23.9,23.9,0,0,0,25.03,11.241ZM10.7,36.618a13.663,13.663,0,0,1-.1-1.678A14.055,14.055,0,0,1,24.863,21.11a13.9,13.9,0,0,1,10.506,5ZM61.484.5V46.587l7.989,3.323-3.793,9.1-7.922-3.29a8.84,8.84,0,0,1-3.894-3.29,10.307,10.307,0,0,1-1.544-5.706V.5Z" transform="translate(-0.793 -0.5)"/>         <path id="Shape-5" data-name="Shape" d="M229.035,43.14a14.116,14.116,0,0,1,18.227,10.54L256.9,51.7a23.875,23.875,0,0,0-23.4-19.1,24.217,24.217,0,0,0-7.552,1.208ZM217.656,74.323l6.512-7.351a14.065,14.065,0,0,1,0-21.046l-6.512-7.351a23.857,23.857,0,0,0,0,35.748Zm29.606-15.038a14.014,14.014,0,0,1-18.227,10.506l-3.122,9.331A23.864,23.864,0,0,0,256.9,61.266Z" transform="translate(-139.511 -21.825)"/>       </g>     </g>   </g> </svg>');
			jQuery(el).addClass('flag-elo');
		}
		else if(flag == 'aura') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" width="113.914" height="75.223" viewBox="0 0 113.914 75.223">   <g id="Grupo_1" data-name="Grupo 1" transform="translate(-216.472 -257.148)">     <path id="Caminho_5" data-name="Caminho 5" d="M216.472,257.148H330.263l.123,35.462c-13.738-5.226-57.444-17.729-113.914-2.147Z" transform="translate(0)" fill="#04267b" fill-rule="evenodd"/>     <path id="Caminho_6" data-name="Caminho 6" d="M216.472,379.832H330.263v-28.37c-20.522-6.869-57-17.16-113.791-2.579Z" transform="translate(0 -47.461)" fill="#fe0" fill-rule="evenodd"/>     <path id="Caminho_7" data-name="Caminho 7" d="M330.263,337.156c-20.522-6.869-57-17.16-113.791-2.579V323.618c56.471-15.582,100.176-3.078,113.914,2.147Z" transform="translate(0 -33.154)" fill="#e50019" fill-rule="evenodd"/>     <path id="Caminho_8" data-name="Caminho 8" d="M299.378,299.509c2.817-7.782,9.26-14.5,20.456-14.417s18.667,6.459,20.686,14.83C326.086,298.419,316.47,298.158,299.378,299.509Z" transform="translate(-46.659 -15.726)" fill="#fe0" fill-rule="evenodd"/>     <path id="Caminho_9" data-name="Caminho 9" d="M274.618,381.716l7.621-11.354h2.827l8.121,11.354h-2.993l-2.315-3.44h-8.3l-2.176,3.44Zm5.723-4.663h6.73L285,373.91a23.642,23.642,0,0,1-1.4-2.355,10.3,10.3,0,0,1-1.072,2.17l-2.18,3.328Zm26.173,4.663v-1.208a6.929,6.929,0,0,1-4.564,1.393,9.717,9.717,0,0,1-2.374-.278,4.425,4.425,0,0,1-1.636-.7,2.24,2.24,0,0,1-.753-1.034,4.223,4.223,0,0,1-.148-1.3v-5.094h2.434v4.56a4.814,4.814,0,0,0,.148,1.472,1.711,1.711,0,0,0,.975.864,4.78,4.78,0,0,0,1.843.312,6.445,6.445,0,0,0,2.055-.32,2.636,2.636,0,0,0,1.358-.875,2.722,2.722,0,0,0,.4-1.607v-4.407h2.434v8.224Zm8.087,0v-8.224h2.19v1.245a4.618,4.618,0,0,1,1.552-1.153,4.247,4.247,0,0,1,1.562-.28,7.512,7.512,0,0,1,2.5.449l-.841,1.3a5.447,5.447,0,0,0-1.783-.3,3.6,3.6,0,0,0-1.437.275,1.807,1.807,0,0,0-.905.764,3.306,3.306,0,0,0-.407,1.626v4.307Zm20.754-1.015a11.792,11.792,0,0,1-2.605.93,12.787,12.787,0,0,1-2.689.27,8.049,8.049,0,0,1-3.64-.661,1.949,1.949,0,0,1-1.27-1.692,1.548,1.548,0,0,1,.48-1.105,3.419,3.419,0,0,1,1.256-.8,8.647,8.647,0,0,1,1.755-.457c.476-.071,1.2-.143,2.162-.209a37.119,37.119,0,0,0,4.346-.481c.009-.19.014-.312.014-.362a1.23,1.23,0,0,0-.693-1.2,6.3,6.3,0,0,0-2.771-.473,7,7,0,0,0-2.54.343,2.276,2.276,0,0,0-1.211,1.222l-2.379-.188a2.989,2.989,0,0,1,1.067-1.412,5.615,5.615,0,0,1,2.152-.83,16.31,16.31,0,0,1,3.261-.291,14.714,14.714,0,0,1,2.989.249,4.935,4.935,0,0,1,1.7.623,2.024,2.024,0,0,1,.757.949,4.679,4.679,0,0,1,.12,1.285v1.858a11.127,11.127,0,0,0,.157,2.458,2.422,2.422,0,0,0,.615.989H335.84a2.087,2.087,0,0,1-.484-1.015Zm-.2-3.114a29.3,29.3,0,0,1-3.977.529,15.088,15.088,0,0,0-2.125.277,2.219,2.219,0,0,0-.961.455.871.871,0,0,0-.341.661c0,.373.249.682.739.93a4.955,4.955,0,0,0,2.157.37,8.168,8.168,0,0,0,2.5-.352,3.432,3.432,0,0,0,1.612-.965,2.125,2.125,0,0,0,.393-1.393Z" transform="translate(-32.724 -63.716)" fill="#04267b" stroke="#04267b" stroke-miterlimit="22.926" stroke-width="0.216"/>   </g> </svg>');
			jQuery(el).addClass('flag-aura');
		}		
		else if(flag == 'hiper') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="93.481" height="75.223" viewBox="0 0 93.481 75.223">   <defs>     <clipPath id="clip-path">       <path id="Caminho_31" data-name="Caminho 31" d="M7,6h93.481V81.223H7ZM7,6" transform="translate(-7 -6)"/>     </clipPath>   </defs>   <g id="surface1" transform="translate(-7 -6)">     <g id="Grupo_9" data-name="Grupo 9" transform="translate(7 6)" clip-path="url(#clip-path)">       <path id="Caminho_30" data-name="Caminho 30" d="M18.228,19.461,70.815,7.321A14.36,14.36,0,0,1,87.988,18.051l12.141,52.587a14.289,14.289,0,0,1-2.176,11.336H17.966L7.5,36.633A14.36,14.36,0,0,1,18.228,19.461Zm0,0" transform="translate(-7.105 -6.764)" fill="#f07c00" fill-rule="evenodd"/>     </g>     <path id="Caminho_32" data-name="Caminho 32" d="M77.679,203.125H73.345v-9.446H64.5v9.446H60.172V181.313H64.5v8.517h8.84v-8.517h4.334Zm0,0" transform="translate(-42.574 -140.37)" fill="#fff" fill-rule="evenodd"/>     <path id="Caminho_33" data-name="Caminho 33" d="M169.987,174.956a2.366,2.366,0,0,1-.213,1.009,2.613,2.613,0,0,1-1.43,1.379,2.587,2.587,0,0,1-1.031.207,2.411,2.411,0,0,1-1-.207,2.634,2.634,0,0,1-.818-.555,2.7,2.7,0,0,1-.561-.824,2.53,2.53,0,0,1-.2-1.009,2.583,2.583,0,0,1,.2-1.02,2.723,2.723,0,0,1,.561-.83,2.644,2.644,0,0,1,.818-.555,2.411,2.411,0,0,1,1-.207,2.587,2.587,0,0,1,1.031.207,2.6,2.6,0,0,1,1.43,1.385A2.415,2.415,0,0,1,169.987,174.956Zm0,0" transform="translate(-126.295 -133.188)" fill="#ffec00" fill-rule="evenodd"/>     <path id="Caminho_34" data-name="Caminho 34" d="M175.834,231.4v-5.041l-.218,0a8.692,8.692,0,0,1-3.151-.56,7.208,7.208,0,0,1-.689-.3c-2.589-1.332-3.892-4.057-4.171-7.892v-7.264h4.171c0,2.035.122,5.505,0,8.217a6.658,6.658,0,0,0,.409,2.058,4.122,4.122,0,0,0,.869,1.424,3.364,3.364,0,0,0,1.284.829,4.522,4.522,0,0,0,1.5.272v-12.8H178.4a1.179,1.179,0,0,1,.667.179.892.892,0,0,1,.353.56l.347,1.43a10.355,10.355,0,0,1,1.032-1,6.293,6.293,0,0,1,1.177-.79,6.191,6.191,0,0,1,1.362-.51,6.6,6.6,0,0,1,1.581-.18,5.113,5.113,0,0,1,2.382.561,5.4,5.4,0,0,1,1.872,1.615c1.477,1.99,1.659,3.108,1.659,5.633a11.12,11.12,0,0,1-.493,3.385,8.288,8.288,0,0,1-1.4,2.7,6.5,6.5,0,0,1-2.175,1.788,6.2,6.2,0,0,1-2.848.65,5.617,5.617,0,0,1-2.253-.4A5.911,5.911,0,0,1,180,224.869V231.4Zm7.657-18.605a3.906,3.906,0,0,0-1.973.466A5.543,5.543,0,0,0,180,214.622v7.225a3.571,3.571,0,0,0,1.356,1.06,4.044,4.044,0,0,0,1.57.3,3.348,3.348,0,0,0,1.469-.319,3.057,3.057,0,0,0,1.143-.976,4.858,4.858,0,0,0,.729-1.665,10.081,10.081,0,0,0,.258-2.411,11.078,11.078,0,0,0-.213-2.338,4.754,4.754,0,0,0-.611-1.558,2.389,2.389,0,0,0-.953-.874A2.849,2.849,0,0,0,183.492,212.8Zm0,0" transform="translate(-128.593 -163.364)" fill="#fff" fill-rule="evenodd"/>     <path id="Caminho_35" data-name="Caminho 35" d="M303.651,210.285a7.838,7.838,0,0,1,2.842.5,6.337,6.337,0,0,1,2.232,1.447c.625.628,1.113.875,1.463,1.792a8.8,8.8,0,0,1,.527,3.151,4.9,4.9,0,0,1-.051.779,1.41,1.41,0,0,1-.157.488.583.583,0,0,1-.291.252,1.3,1.3,0,0,1-.471.072h-9.753a7.72,7.72,0,0,0,.426,2.058,4.108,4.108,0,0,0,.869,1.424,3.36,3.36,0,0,0,1.283.829,4.625,4.625,0,0,0,1.643.275,4.859,4.859,0,0,0,1.564-.224,8.43,8.43,0,0,0,1.171-.477c.331-.168.628-.326.891-.476a1.6,1.6,0,0,1,.774-.219.921.921,0,0,1,.757.375l1.2,1.542a6.545,6.545,0,0,1-1.5,1.307,8.206,8.206,0,0,1-1.7.824,10.017,10.017,0,0,1-1.783.438,12.654,12.654,0,0,1-1.743.129c-7.469,0-9.6-8.591-5.92-13.912a5.585,5.585,0,0,1,2.455-1.732A8,8,0,0,1,303.651,210.285Zm.084,2.438a3.32,3.32,0,0,0-2.5.93,4.712,4.712,0,0,0-1.166,2.618h6.9a4.611,4.611,0,0,0-.191-1.345,3.129,3.129,0,0,0-.588-1.133,2.934,2.934,0,0,0-1.009-.785A3.409,3.409,0,0,0,303.735,212.723Zm0,0" transform="translate(-231.526 -163.567)" fill="#fff" fill-rule="evenodd"/>     <path id="Caminho_36" data-name="Caminho 36" d="M383.348,226.119V210.346H385.8a1.349,1.349,0,0,1,.891.23,1.26,1.26,0,0,1,.343.8l.213,1.659a7.055,7.055,0,0,1,1.721-2.2,3.511,3.511,0,0,1,2.282-.807,2.877,2.877,0,0,1,1.76.511l-.315,2.589a.574.574,0,0,1-.2.415.736.736,0,0,1-.443.123,4.633,4.633,0,0,1-.706-.073,5.313,5.313,0,0,0-.852-.072,3.021,3.021,0,0,0-1.025.163,2.473,2.473,0,0,0-.807.488,3.4,3.4,0,0,0-.634.779,7.026,7.026,0,0,0-.51,1.048v10.119Zm0,0" transform="translate(-301.335 -163.364)" fill="#fff" fill-rule="evenodd"/>   </g> </svg>');
			jQuery(el).addClass('flag-hiper');
		}		
		else if(flag == 'banescard') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="61" height="37" viewBox="0 0 61 37">   <image id="banescard" width="61" height="37" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAlCAYAAADx5+EfAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAACipJREFUeJztmFdQVGkahmd3aqtmay+2aueG7j5NFhBBFFBHDNANkjOIhFGSGBAEJSfJYGDMCCiKESMgKnQjiI6O1hjGHTPQ3TpbtbV7OVN7sVeu777nNAYw7Tqg1q5/1Vd0Ouf8z/+l9+Ozzz6tT+v/b/Xozey69XLnj9cEx26dwrJHZ/rHsYTu583/1a2XfaQm/wftrlYnHNUMyiK6B0z+9KuhtXqz87w5PnbTiKaTQauT/71HJ2RrB4XfvUdo+bB9GGgJnN4nfLHmgfnn4wZ94Kodspv8kNngh4yGAJo/8vZ4YbPW5YNAG03+T82QwnfcoLMILKhrYe5dhQkBZbDyL+X7KshV1ag6Nuu9eP5laHrcYPJXrU4xYVygY8tjIHjWYkldJLb3uWDrWRcErk6GwqMaqZtC0KVToO2eORr7nbClewZ29E3BkZtW0kbb7phh17eTcJjv916ZiC2aaWi+6PDsoE4PCdh5wRFb+bl4fcc902cp1HrdBtt6+DytK3aL1wzJR4LrTR4zzBeNA7QcnstTJS9v1sxAl0GBM3oF4spjIdDTubvn4cRtCySunw+HiEKYepVhYmgRQnKTcHpQQPnhuXCJyUVY4SLMSk7j96WYFpeNliv2OHlfidJWDzhF5cLcp5TXFyF1cwifKWDvd5Pgm76UUVUCC58yTI/NQQMPU6OXjw7zxjGHFr1oFVBCT1djcmQhpkTnwT6ccN6V8M1IwalBU4K5wy60EGlbgrH3e1sE5yVKkdHQ64yYshjIPCvhtWIZCvep+HcJZIyQlA1hWMYoEVSV/E001na4YUFJPK+rQdZOf7gvToNCVYH1nbNQ0+aG2YmZSKiOQjsPeFSY978DtGnvm6D3XraHXF0B+7BCRBUnIKpoEXwIa+lXCdev89B+zwz7vrdDfosXylvVKNzrhdnJGVBy883fToZX6hIoeP02HkAXNxlTES1BZzf6Yc7idN67Cikbw5HV7Ie4yji+r8b8wq/hz2coPCowj4e0cnsgyg6rsJ8F9cygYmR+6+Xn3wFaOPUm6JL9aqlghebHs0eain0Sx++aYWZCFotZHbYy59LrAzApvAA2AUU8iCxY+pfD0rccTf2ODOVVDNEidOmVzF851KnL6MEqbOh0g2NEAUznVcKBEeQQWYDJUfmYuiAXy74JwbazU+GTnsK0KoWpZwVco7Nx6IbNy0Xt3aAV9a+FZmiH5CTQU7VYvcsHrddscYhWecyNeVvM/KxFSl0EASsQWxmN08zFxnNOsAlcA/eUNBQf8OBBlEHFmtD9SM5NT8BkQjlE5OPg9Ylwmp/PfM9B15AZ2u9aIKPeD4lrw1HWqkJU6UJGhys6H1ggppT1gxFQckAFjUH+66E1ekXuc8Ex0trp0dmJK6X8nMLNusVnYmb8KjjQq1b+ZYgoSETBHm9Y87VbUgbSG4IYzkvpSYYo02DxhnB6qZrVPw5dj2RoYrhbBZQyr5ejc0BAUHYSPV2FsPwEBOUksZitQUBmEtPEA+a+lTysNEkTzGZ+W/iVob5vqrGQScVs2N4ROrRbr3hCw2g7+mcrhOYmYu6StBHmzaq6mps5cnMCDv0wQSpWYs5PX5iF+JpouPM3qxv9sWxjGNTceCk91/VQhh3npsBjaTpW7gjg/U1Qz/chuclS1IjhHUjwlsuT0HbbHEs3hsA5Lhc2wYWETkfq1mDWBIGgipH2TtBDgg1bxN9eBf3cBPQ8tMH5nzxxTheC/kcqUQoaw4ybN6aDCUNvON9GmdQFDCOt2zAsOsRwZRvsNjyXt+I9tcP36zFIbUmqJZoxg9Yp/0CwC6+C1RjMcdbggj6dH7ruhaP9VhCO3wyG5n4keh/av6ySXqGann7+VrX1KvU1/Lr5ghOCmQrF+z3QNaj89dDiopRbqREhR1nfQyf0PkjAkWvzcPCaBw5cV/GvGid+CECvYabRs6+CfA34f2yjrl1zQC1V/Jxd86TiOibQ5x4JnzN8Bo3F4fkNe/QW6BtcyIrtPmwqmhqtV9XoHQrlA18MN7m0obpTM7B0QygSayOwvmMGTlJWiqmwhworu8kX8RQYy5mvO9iSRFnZ8t1E5DVzeOlyxWpW73wqvKOs8rm7vJFUOx9lR9wpWmIkaKmQjRW0uDRDsuLR0KJd+ksYPe2JQ1c9aCocu+GPzlvh6L2XitYrfjh111nq3+Jv83d7wzEyD5Pn51Ja5sCBxW3VDn8c/9EGbsnpcOTnc1mJ7UJK8FV8FvZcckDmdn9YsmpPi82CbUCxpNw82csnUH66sp050yYGF1HslOPYHStj2o0Z9ICgZJj/PBq6z2CP07eiGNKB6L4fi/7BDOzR1mB59Vp4JmxDQUM5waey4lqzChdiUkQeak7OxJazrjCjsJiTtAK1bbMwKymTFd0PHXctEVsRB6VXFda2zUZUYZw0uMzloWTxexWBBU+2MXaNpgtTkM6qbUZxMiksH2dY8LqkPj1G0OLS6gRv2i8vQlOxETQFG1prEZ2/nmJjA0Othhs1mrXvejR0x1FYBFG5rUUGW9UZg8AWJaBjwJLiwopCxA5L68IQTk3usWQFrNlz7YJLUM7QnR6bLfX/lkuOUro4RWVLquzgDTsJrpkTmE1gCfwyFkuDjjjwjCm0ZlD2e95oN+3xi+AdNyJhG7gWMooOmUct5DSFaO5Gy9yci/iqOOmz2nY3aWPH7lhjk+Yr1J124xiaBJfoLCysiMGqhgAKFDGcc6i83BnapZzYYjhSmhPAnNFRQgGUgRPU9KIIqTjiQQFTgfjayOGWNsbQRm/LHWhXtC8UqT69C1TJmwhdRegaCfwpsGjhmdVYuWkxJ6Z1SN0SRmBbpG8PguBdDHVaijRJxdcuwKkBC2zmISjEqYuDSF6zL5S8Z/ZOH6lVaQ2mPJAi1oUCNF50Qiu9PSshXVJjeRxojJDjAC0uVtUvtTrZberyJ8YQV+Dkj2okVayjZ9ZJ/0WZG78JqevK0HIhhhueQc9YYQ4lqym/E6cq5TweRH0gBxJXbjwTZl7VsA/haMpwlvFwkjl/R5fGSDO5OIEZhYoJD43jprqS96iENUda66A1cOY1O89PfrlyjyW0uAg9h62mjx43glMNddyehibtAmzvXIRDl33QNcDNMn9FNSWG9G5W48IWb6xu8kPV8TkcO01xmmJCLEhZjQEo2ueJ+v4pqD7mjl0XHQnrhIrjs9F+3+yZqjtxy1wKe7Hqrz/phm/YAjd3T0PnfdPxh5bAh0ycCHvDGE5GefhcNDwNteEB4CVxI3/LdybDEtbEKEvf8L+wEc8cb2hxUXX9VqtXrqLXf3nrBv4rezYlvWy619l7gpbAB4Uv+FB/wrbRfn7tBj6EjRf003VmQP4Fcz2Fhe0a7fEHB34f0C8u5rotbQnhTzH8f9IahCc9BiW0+leMf/8r0OI690D9mx698GWPXmnb81DpTGgX0biZ92d6+cT3Cv1pfVofx/o3u5936rqtQ7MAAAAASUVORK5CYII="/> </svg>');
			jQuery(el).addClass('flag-banescard');
		}
		else if(flag == 'visa') {
			if(type == 'card') {
				jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="61" height="37" viewBox="0 0 61 37">   <image id="visa_white" width="61" height="37" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD0AAAAlCAMAAADGOREtAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAe9QTFRFAAAA////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////HabMXAAAAKV0Uk5TABQcDhYQAxgLAicwLiMMAQgbCkSXxcejs8RpE6hHV9/48tl6EmTCYyy1+v9PNS3t8DmWnCnnEcyVvJQEVP3BIbaMmYj+16G37EuOmLLkFQ3q9gdo0SRy0tXA/E4FXKzcLz3jCbmCDz93qR6tZ1bvpd4d9xlbha9t0PWmMVHITHCD7hfbBoY2PFnU6112olC0gSBf1rpF4kaNO6CPVUj7yzMlMhomAA2OnwAAAgJJREFUeJztk/1XDGEUx2+zNTXYVsvOJtu+JFb7ZRWJNYbKikRKWayS0C7Jy67EShK1XlJEkfcRf6j7bD8/+6MfnP2eM+fc+d75nHufe58hKqigf60iRciWi4sVpUQtVZQyIq1ozdp19nIHu+srFMUpoTdsdOm67q7gUKvUN1Vt9uh6Nalej88fqNlSS6Ru3aa7gtLq2+tCQBUHO4CaneFdqG/QdiOnPdxT414OPPLmm/YB+4nKI8AB46CJQ4e9IZjNLa2RIHd+JMr00TxnbwOOqRQ+jlA7nQA6bCeBU1zW2Ul0ugvdgP+MnO4Bes9SDDjXSeeBHuMCEL/Yl8v1A5cGgMtyejCAK9VXrwFDjkQcSNJ1bjZwY1jkbgIj3NstOT0cgT/m7cLtEhoEok1k3BEzu9tPlEojdG8UuN8npY0hHu8YP+R4APiEFXxoApmE1gE8ovF6VNbKiz8GJgLAEyrjVTULx1HMHJIpPnLb5NMpDNjl9DOxFTyfJrsL5gzZVPay7Lx4aa4uHmZSTmuv+IN4jKjhNaZm6c3cyPzoWx6c4gbSmUyGr9OknKZ3TPsWiN4DH8LG4mq97pmlj4h+ymazy4A7D/05DXzhi/UVWJwuHYuLVia+Ge4oXCL9/Qd+5qET45YlfpQFy/pFaorfrJXfKtkt64/BtnPFasxDF1TQ/6i/mQ9xJO6Xt3cAAAAASUVORK5CYII="/> </svg>');
			}	
			if(type == 'info') {
				jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="174.633" height="56.393" viewBox="0 0 174.633 56.393">   <defs>     <linearGradient id="linear-gradient" x1="0.46" y1="1.02" x2="0.549" gradientUnits="objectBoundingBox">       <stop offset="0" stop-color="#222357"/>       <stop offset="1" stop-color="#254aa5"/>     </linearGradient>   </defs>   <g id="visa-seeklogo.com" transform="translate(0 0)">     <path id="Caminho_1" data-name="Caminho 1" d="M90.316-64.64c-.1,7.855,7,12.239,12.35,14.846,5.5,2.674,7.341,4.389,7.32,6.78-.042,3.66-4.384,5.275-8.448,5.338a29.48,29.48,0,0,1-14.489-3.445L84.5-29.17c3.288,1.515,9.376,2.837,15.69,2.895,14.82,0,24.516-7.315,24.568-18.658.058-14.395-19.911-15.192-19.774-21.626.047-1.951,1.909-4.033,5.988-4.562a26.563,26.563,0,0,1,13.913,2.438l2.48-11.563a37.822,37.822,0,0,0-13.2-2.423c-13.949,0-23.76,7.415-23.839,18.029m60.877-17.032a6.431,6.431,0,0,0-6,4l-21.17,50.546h14.809l2.947-8.144h18.1l1.71,8.144h13.052l-11.39-54.547h-12.05m2.072,14.735,4.274,20.483h-11.7l7.431-20.483m-80.9-14.735L60.688-27.125H74.8L86.467-81.672H72.361m-20.876,0L36.8-44.545,30.855-76.114a6.578,6.578,0,0,0-6.508-5.559H.336L0-80.088c4.929,1.07,10.53,2.8,13.923,4.641,2.077,1.127,2.669,2.113,3.351,4.793l11.253,43.53H43.441L66.3-81.672H51.485" transform="translate(0 82.668)" fill="url(#linear-gradient)"/>   </g> </svg>');
			}	
			jQuery(el).addClass('flag-visa');
		}
		else if(flag == 'diners') {
			jQuery(el).html('<svg xmlns="http://www.w3.org/2000/svg" width="102.575" height="81.264" viewBox="0 0 102.575 81.264">   <g id="__x0023_Layer_x0020_1" transform="translate(0 0)">     <path id="Caminho_37" data-name="Caminho 37" d="M60.092,81.264c22.211.106,42.483-18.114,42.483-40.279C102.575,16.746,82.3-.008,60.092,0H40.977C18.5-.008,0,16.751,0,40.985,0,63.155,18.5,81.37,40.977,81.264Z" fill="#0079be"/>     <path id="Caminho_38" data-name="Caminho 38" d="M61.257,20.83a37.2,37.2,0,1,0,37.193,37.2A37.2,37.2,0,0,0,61.257,20.83Zm-23.571,37.2h0a23.615,23.615,0,0,1,15.132-22v44A23.6,23.6,0,0,1,37.686,58.031ZM69.693,80.04h0V36.025a23.564,23.564,0,0,1,0,44.015Z" transform="translate(-20.19 -17.471)" fill="#fff"/>     <path id="Caminho_76" data-name="Caminho 76" d="M60.092,81.264c22.211.106,42.483-18.114,42.483-40.279C102.575,16.746,82.3-.008,60.092,0H40.977C18.5-.008,0,16.751,0,40.985,0,63.155,18.5,81.37,40.977,81.264Z" fill="#0079be"/>     <path id="Caminho_77" data-name="Caminho 77" d="M61.257,20.83a37.2,37.2,0,1,0,37.193,37.2A37.2,37.2,0,0,0,61.257,20.83Zm-23.571,37.2h0a23.615,23.615,0,0,1,15.132-22v44A23.6,23.6,0,0,1,37.686,58.031ZM69.693,80.04h0V36.025a23.564,23.564,0,0,1,0,44.015Z" transform="translate(-20.19 -17.471)" fill="#fff"/>   </g> </svg>');
			jQuery(el).addClass('flag-diners');
		}
	},
	getIconScanner: function() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="15.43" height="11.572" viewBox="0 0 15.43 11.572"><g id="scanner__barcode" transform="translate(0 -3)"><path id="Caminho_10509" data-name="Caminho 10509" d="M21.286,3h-.964a.321.321,0,0,0,0,.643h.964a.644.644,0,0,1,.643.643V5.25a.321.321,0,0,0,.643,0V4.286A1.287,1.287,0,0,0,21.286,3Z" transform="translate(-7.142)" fill="#fff"/><path id="Caminho_10510" data-name="Caminho 10510" d="M.321,5.572A.321.321,0,0,0,.643,5.25V4.286a.644.644,0,0,1,.643-.643H2.25A.321.321,0,1,0,2.25,3H1.286A1.287,1.287,0,0,0,0,4.286V5.25A.321.321,0,0,0,.321,5.572Z" fill="#fff"/><path id="Caminho_10511" data-name="Caminho 10511" d="M22.25,17a.321.321,0,0,0-.321.321v.964a.644.644,0,0,1-.643.643h-.964a.321.321,0,0,0,0,.643h.964a1.287,1.287,0,0,0,1.286-1.286v-.964A.321.321,0,0,0,22.25,17Z" transform="translate(-7.142 -4.999)" fill="#fff"/><path id="Caminho_10512" data-name="Caminho 10512" d="M2.25,18.929H1.286a.644.644,0,0,1-.643-.643v-.964a.321.321,0,0,0-.643,0v.964a1.287,1.287,0,0,0,1.286,1.286H2.25a.321.321,0,0,0,0-.643Z" transform="translate(0 -4.999)" fill="#fff"/><path id="Caminho_10513" data-name="Caminho 10513" d="M3.321,6A.321.321,0,0,0,3,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,3.321,6Z" transform="translate(-1.071 -1.071)" fill="#fff"/><path id="Caminho_10514" data-name="Caminho 10514" d="M13.321,6A.321.321,0,0,0,13,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,13.321,6Z" transform="translate(-4.642 -1.071)" fill="#fff"/><path id="Caminho_10515" data-name="Caminho 10515" d="M20.321,6A.321.321,0,0,0,20,6.321v7.072a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,20.321,6Z" transform="translate(-7.142 -1.071)" fill="#fff"/><path id="Caminho_10516" data-name="Caminho 10516" d="M6.321,6A.321.321,0,0,0,6,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,6.321,6Z" transform="translate(-2.143 -1.071)" fill="#fff"/><path id="Caminho_10517" data-name="Caminho 10517" d="M8.321,6A.321.321,0,0,0,8,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,8.321,6Z" transform="translate(-2.857 -1.071)" fill="#fff"/><path id="Caminho_10518" data-name="Caminho 10518" d="M10.321,6A.321.321,0,0,0,10,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,10.321,6Z" transform="translate(-3.571 -1.071)" fill="#fff"/><path id="Caminho_10519" data-name="Caminho 10519" d="M15.321,6A.321.321,0,0,0,15,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,15.321,6Z" transform="translate(-5.356 -1.071)" fill="#fff"/><path id="Caminho_10520" data-name="Caminho 10520" d="M18.321,6A.321.321,0,0,0,18,6.321v5.143a.321.321,0,1,0,.643,0V6.321A.321.321,0,0,0,18.321,6Z" transform="translate(-6.428 -1.071)" fill="#fff"/><path id="Caminho_10521" data-name="Caminho 10521" d="M18.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,18.321,16Z" transform="translate(-6.428 -4.642)" fill="#fff"/><path id="Caminho_10522" data-name="Caminho 10522" d="M15.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,15.321,16Z" transform="translate(-5.356 -4.642)" fill="#fff"/><path id="Caminho_10523" data-name="Caminho 10523" d="M10.321,16a.321.321,0,0,0-.321.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,10.321,16Z" transform="translate(-3.571 -4.642)" fill="#fff"/><path id="Caminho_10524" data-name="Caminho 10524" d="M8.321,16A.321.321,0,0,0,8,16.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,8.321,16Z" transform="translate(-2.857 -4.642)" fill="#fff"/><path id="Caminho_10525" data-name="Caminho 10525" d="M6.321,16A.321.321,0,0,0,6,16.321v.643a.321.321,0,0,0,.643,0v-.643A.321.321,0,0,0,6.321,16Z" transform="translate(-2.143 -4.642)" fill="#fff"/><path id="Caminho_10526" data-name="Caminho 10526" d="M15.108,11H.321a.321.321,0,1,0,0,.643H15.108a.321.321,0,1,0,0-.643Z" transform="translate(0 -2.857)" fill="#fff"/><path id="Caminho_10527" data-name="Caminho 10527" d="M4.179,11.358a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,4.179,11.358Zm1.286,0a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,5.465,11.358Zm1.286,0a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,6.75,11.358Zm-4.5,2.572H1.286a.644.644,0,0,1-.643-.643v-.964a.321.321,0,1,0-.643,0v.964a1.287,1.287,0,0,0,1.286,1.286H2.25a.321.321,0,0,0,0-.643ZM.321,5.572A.321.321,0,0,0,.643,5.25V4.286a.644.644,0,0,1,.643-.643H2.25A.321.321,0,1,0,2.25,3H1.286A1.287,1.287,0,0,0,0,4.286V5.25A.321.321,0,0,0,.321,5.572ZM15.108,8.143H13.5V5.25a.321.321,0,0,0-.643,0V8.143h-.643V5.25a.321.321,0,1,0-.643,0V8.143H10.286V5.25a.321.321,0,1,0-.643,0V8.143H9V5.25a.321.321,0,1,0-.643,0V8.143H7.072V5.25a.321.321,0,0,0-.643,0V8.143H5.786V5.25a.321.321,0,0,0-.643,0V8.143H4.5V5.25a.321.321,0,1,0-.643,0V8.143H2.572V5.25a.321.321,0,0,0-.643,0V8.143H.321a.321.321,0,0,0,0,.643H1.929v3.536a.321.321,0,1,0,.643,0V8.786H3.857v1.607a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786H8.358v3.536a.321.321,0,1,0,.643,0V8.786h.643v1.607a.321.321,0,1,0,.643,0V8.786h1.286v1.607a.321.321,0,1,0,.643,0V8.786h.643v3.536a.321.321,0,1,0,.643,0V8.786h1.607a.321.321,0,0,0,0-.643ZM14.144,3H13.18a.321.321,0,1,0,0,.643h.964a.644.644,0,0,1,.643.643V5.25a.321.321,0,0,0,.643,0V4.286A1.287,1.287,0,0,0,14.144,3ZM9.965,11.358a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,9.965,11.358ZM15.108,12a.321.321,0,0,0-.321.321v.964a.644.644,0,0,1-.643.643H13.18a.321.321,0,0,0,0,.643h.964a1.287,1.287,0,0,0,1.286-1.286v-.964A.321.321,0,0,0,15.108,12Zm-3.215-.643a.321.321,0,0,0-.321.321v.643a.321.321,0,1,0,.643,0v-.643A.321.321,0,0,0,11.894,11.358Z" fill="#fff"/></g></svg>';
	},
	setCreditMethod: function() {
		use_two_cards = false;
		
		jQuery('.li-position-card svg tspan').html('1º'); 
		jQuery('#three-li-form-payment svg tspan').html('2º');
		
		jQuery('#three-li-form-payment').slideUp(1);
		jQuery('#multi-actions-one-ticket').slideUp();
		jQuery('.payment-method-content-ticket').slideUp();
		jQuery('.payment-method-content-pix').slideUp();
		jQuery('.payment-method-content-cc').slideDown();
		
		jQuery('.modal-edit-amount').slideUp();
		jQuery('.modal-credit-amount').slideDown();
		
		jQuery('.aqbank-card-grand-total').html(
			this.formatPriceWithCurrency( amount_total )
		);
		
		
		jQuery("#aqpago_installments option").each(function() {
			jQuery(this).remove();
		});
		
		jQuery('.installment-view').html('');
		Object.entries(installMap).forEach(([install, data]) => {
			var valPrice = ((amount_total / (100 - data.tax)) * 100);
			data.price = valPrice / install;
			data.total = valPrice;
			
			jQuery('#aqpago_installments').append(jQuery('<option>', {
				value: install,
				text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
			}));
			
			jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
			
			if(install == jQuery('#aqpago_installments').val()) {
				jQuery('.description-installment').html(
					data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				);
			}
		});
		
		if (card_one) {
			jQuery('#two-li-form-payment').slideUp();
			jQuery('.card-box-all').slideUp();
			jQuery('.aqbank_payment_integral').slideDown();
			
			/*jQuery('#button-finished').slideDown();*/
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_total )
			);
			
			jQuery('#one-card-bottom span').html(
				this.formatPriceWithCurrency( (amount_total / cards[card_one].installment ) )
			);
			
			jQuery('#one-li-form-payment').slideDown();
			/*jQuery('#button-finished').slideDown('100');*/
			
			var instOne = jQuery('#aqpago_one_installments').val();
			jQuery("#aqpago_one_installments option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var valPrice = ((amount_total / (100 - data.tax)) * 100);
				data.price = valPrice / install;
				data.total = valPrice;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(data.price) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(data.total) + '</b></p>');
				
				if(install == instOne) {
					jQuery('.description-installment-1').html(
						data.option + ' de ' + AQPAGO.formatPriceWithCurrency(data.price) + ' ' + data.fees
					);
					
					jQuery('#one-card-bottom strong').html(install + 'x');
					jQuery('#one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(data.price));
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne).change();
			
		} else {
			/** existe cartão salvo **/
			if (savedCards) {
				jQuery('#list-new').slideDown();
				
				jQuery('.card-box-all').slideUp();
				jQuery('.box-select-card-title').slideDown();
				jQuery('.box-select-card').slideDown();
				jQuery('.box-select-card-li').slideDown();
			} else {
				/** não tem cartão **/
				/* jQuery('#one-action').slideDown(); */
				jQuery('.card-box-all').slideDown();
			}
		}
		
		if (card_two) {
			jQuery('#two-grand-total-view').html(
				amount_total
			);
			
			jQuery('#two-card-bottom span').html(
				(amount_total / cards[card_two].installment )
			);
			
			/** exite cartão selecionado **/
			if (select_card) {
				/******* select com dois cartões ********/
				jQuery('#one-li-form-payment').slideUp(1);
				jQuery('.box-select-card-li').slideUp(1);
				jQuery('.box-select-card-title').slideUp(1);
				
				jQuery('#button-finished').slideUp();
				
				jQuery('.box-select-card').slideDown('100');
				/***************/
				
				jQuery('#list-' + select_card ).slideDown('100');
				jQuery('#one-li-form-payment').slideDown('100');
				/*jQuery('#button-finished').slideDown('100');*/
			} else {
				/******* select com dois cartões ********/
				jQuery('#one-li-form-payment').slideUp(1);
				jQuery('#button-finished').slideUp();
				
				jQuery('.box-select-card').slideDown('100');
				jQuery('.box-select-card-title').slideDown('100');
				jQuery('.box-select-card-li').slideDown('100');
				/***************/
			}	
		}
		
		/** Cartão salvo selecionado **/
		if (saved_card_one || select_card) {
			/** Não digitou o código de segurança **/
			if (!cards[ card_one ].securityCode) {
				
				select_card = false;
				card_one = false;
				
				jQuery('.box-select-card-li-arrow').removeClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideUp();
				
				jQuery('.aqbank_custom_informations').slideUp();
				jQuery('.aqbank_payment_integral').slideUp();
				jQuery('#button-finished').slideUp();
				
				jQuery('#list-new').slideDown('100');
				jQuery('.box-select-card-title').slideDown('100');
				jQuery('.box-select-card').slideDown('100');
				jQuery('.box-select-card-li').slideDown('100');
				
			} else {
				jQuery('#list-new').slideUp();				
				jQuery('.box-select-card-li-arrow').addClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideDown();
				jQuery('.box-select-card').slideDown();
				jQuery('#list-' + saved_card_one).slideDown();
			}
		}
		
		/** cartão selecionado da lista e lista possui mais de um cartão **/
		if (set_one_card && totalSavedCards > 1) {
			jQuery('.box-select-card-custom').slideDown();
			jQuery('#list-'+card_one).slideDown();
			jQuery('#list-'+card_two).slideUp(1);
			
		}
		
		jQuery('.modal-credit-amount').slideDown();
		jQuery('#aqbank-multi-pagamento-valor').slideUp();
		
		process_payment = true;
		
		setTimeout(function(){  
			jQuery('.card_one').slideDown('100'); 
		}, 500);
	},
	isValidCPF: function(cpf) {
		if (typeof cpf !== "string") return false
		cpf = cpf.replace(/[\s.-]*/igm, '')
		if (
			!cpf ||
			cpf.length != 11 ||
			cpf == "00000000000" ||
			cpf == "11111111111" ||
			cpf == "22222222222" ||
			cpf == "33333333333" ||
			cpf == "44444444444" ||
			cpf == "55555555555" ||
			cpf == "66666666666" ||
			cpf == "77777777777" ||
			cpf == "88888888888" ||
			cpf == "99999999999" 
		) {
			return false
		}
		var soma = 0
		var resto
		for (var i = 1; i <= 9; i++) 
			soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i)
		resto = (soma * 10) % 11
		if ((resto == 10) || (resto == 11))  resto = 0
		if (resto != parseInt(cpf.substring(9, 10)) ) return false
		soma = 0
		for (var i = 1; i <= 10; i++) 
			soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i)
		resto = (soma * 10) % 11
		if ((resto == 10) || (resto == 11))  resto = 0
		if (resto != parseInt(cpf.substring(10, 11) ) ) return false
		return true
	},
	validDataCardFull: function(card){
		/** cartão digitado **/
		if(card['card_id'] == null || card['card_id'] == false) {
			if(card['number'] == '' || card['number'].length == 8) {
				return 'Número do cartão é obrigatório!';
			}
			else if(card['expiration_month'] == '') {
				return 'Mês da validade do cartão é obrigatório!';
			}
			else if(card['expiration_year'] == '') {
				return 'Ano da validade do cartão é obrigatório!';
			}
			else if(card['securityCode'] == '') {
				return 'Código do cartão é obrigatório!';
			}
			else if(card['owerName'] == '') {
				return 'Nome do proprietário do cartão é obrigatório!';
			}
			else if(card['taxvat'] == '') {
				return 'CPF do dono do cartão é obrigatório!';
			}		
			else if(!this.isValidCPF(card['taxvat'])) {
				return 'CPF do dono do cartão inválido!';
			}
			else {
				return true;
			}
		}
		else {
			/** cartão salvo **/
			if(card['securityCode'] == '') {
				return 'Código do cartão é obrigatório!';
			}
			else {
				return true;
			}
		}
	},
	
	setCardData: function(position){
		var installments 		= jQuery('#' + this.getCode() + '_installments').val();
		var ccNumber 			= jQuery('#' + this.getCode() + '_cc_number').val().replace(/[^0-9]/g,'');
		var expiration_month 	= jQuery('#' + this.getCode() + '_expiration').val();
		var expiration_year 	= jQuery('#' + this.getCode() + '_expiration_yr').val();
		var securityCode	 	= jQuery('#' + this.getCode() + '_cc_cid').val();
		var owerName		 	= jQuery('#' + this.getCode() + '_cc_owner').val();
		var cardIndex			= ccNumber.substr(0, 4) + '' + ccNumber.substr(-4, 4);
		var fourDigits			= ccNumber.substr(-4, 4);
		var imOwer 				= (jQuery('#not').is(":checked")) ? true : false;
		var flag	 			= this.setPaymentFlag(ccNumber);
		var taxvat 				= jQuery('#aqpago_documento').val().replace(/[^0-9]/g,'');
		
		/** Cartão Salvo **/
		if (card_saved) {
			cardIndex 					= card_saved;
			var card 					= [];
			card 						= cards[cardIndex];
			card['card_id'] 			= cards[cardIndex].card_id;
			card['installment'] 		= installments;
			card['number'] 				= cards[cardIndex].number;
			card['expiration_month'] 	= null;
			card['expiration_year'] 	= null;
			card['securityCode'] 		= securityCode;
			card['owerName'] 			= null;
			card['flag'] 				= cards[cardIndex].flag;
			card['taxvat'] 				= taxvat;
			card['imOwer'] 				= null;
			card['saved'] 				= true;
			
			var validCard = this.validDataCardFull(card);
			if (validCard !== true) {
				toastr.error(validCard,'Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
				return false;
			} 
			
			cards[cardIndex] 			= card;
			flag 						= cards[cardIndex].flag;
			
			if (position == 'one') {
				jQuery('#aqpago_saved_first').val( 'true' );
				saved_card_one = card['card_id'];
				
				jQuery('.aqban-modal-one-card .field-saved-card').html(
					"<div class='card-one-set'>"
					+ "<div class='box-select-card-float box-select-card-li-flag' style='float: left;margin-right: 10px;width: 50px;'>"
					+ this.getFlagSvg( cards[cardIndex].flag.toLowerCase() )
					+ "</div>"
					+ "<div class='box-select-card-float box-select-card-li-number'>"
					+ cardIndex.substr(0, 4) + " XXXX XXXX " + cardIndex.substr(-4, 4)
					+ "</div>"
					+ "<div class='box-select-card-float box-select-card-li-arrow'>"
					+ "</div>"
					+ "</div>"
				);
				
				
			}
			if (position == 'two') {
				jQuery('#aqpago_saved_second').val( 'true' );
				saved_card_two = card['card_id'];
			}
		} else {
			var card 					= [];
			card['card_id'] 			= false;
			card['installment'] 		= installments;
			card['number'] 				= ccNumber;
			card['expiration_month'] 	= expiration_month;
			card['expiration_year'] 	= expiration_year;
			card['securityCode'] 		= securityCode;
			card['owerName'] 			= owerName;
			card['flag'] 				= flag;
			card['taxvat'] 				= taxvat;
			card['imOwer'] 				= imOwer;
			card['saved'] 				= false;
			
			
			var validCard = this.validDataCardFull(card);
			if(validCard !== true) {
				toastr.error(validCard,'Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
				return false;
			} 
			
			cards[cardIndex] 			= card;
			
			if (position == 'one') {
				jQuery('#aqpago_saved_first').val( '' );
			}			
			if (position == 'two') {
				jQuery('#aqpago_saved_second').val( '' );
			}
		}
		
		/** remove erro **/
		if (position == 'one') {
			jQuery('#one-li-form-payment').removeClass( 'aqpago-erro' );
		}			
		if (position == 'two') {
			jQuery('#two-li-form-payment').removeClass( 'aqpago-erro' );
		}
		
		this.setBandeiraInfo('#' + position + '-li-form-payment .li-number-card .img-flag', flag, 'info');
		
		jQuery('.card-box-all').slideUp('100');					
		jQuery('#not_card' + this.capitalizeFirstLetter(position)).prop('checked', imOwer);
		if (imOwer) {
			jQuery('.documento-' + position + '-card' + this.capitalizeFirstLetter(position)).slideUp('100');
		} else {
			jQuery('.documento-' + position + '-card'  + this.capitalizeFirstLetter(position)).slideDown('100');
		}
		
		/** Alterar quando for one e two valor **/
		var amountOption = amount_total;
		
		/** Crédito primeiro cartão **/
		if (type_payment == 'credit' && position == 'one') {
			card_one = cardIndex;
			amountOption = amount_total;
			
			jQuery('#' + this.getCode() + '_cc_number').val('').change();
			jQuery('#' + this.getCode() + '_cc_owner').val('').change();
			jQuery('#' + this.getCode() + '_expiration').val('').change();
			jQuery('#' + this.getCode() + '_expiration_yr').val('').change();
			jQuery('#' + this.getCode() + '_cc_cid').val('').change();
			jQuery('#' + this.getCode() + '_installments').val('1').change();
			jQuery('#' + this.getCode() + '_cc_multiple_val').val(
				this.formatPrice( amount_two )
				
			);

			jQuery('#' + this.getCode() + '_one_installments').val( installments );

			/** ***/
			jQuery("#aqpago_one_installments option, #aqpago_installments_oneCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee = ((amount_total / (100 - data.tax)) * 100);
				var vlPc = vlFee / install;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc) + ' ' + data.fees
				}));
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(installments).change();
			/** ***/	
		}
		
		/** Multi Crédito primeiro cartão **/
		if (type_payment == 'credit_multiple' && position == 'one') {
			var valOne 	= jQuery('#' + this.getCode() + '_cc_multiple_val').val();
			valOne 		= this.customValValidate(valOne);
			var valTwo 	= amount_total - valOne;
			
			/** valor do primeiro cartão maior que total - 1 **/
			if (parseFloat( parseFloat( valOne ).toFixed(2) ) > parseFloat( parseFloat( (amount_total - 1) ).toFixed(2) )){
				valOne = parseFloat( amount_total - 1 ).toFixed(2);
				valTwo = amount_total - valOne;
				
				toastr.error('O valor não pode ser maior que ' + this.formatPriceWithCurrency( valOne ) ,'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
			} else if ( parseFloat(parseFloat( amount_total - 1 ).toFixed(2) ) <= parseFloat(0.00)) {
				valOne = 1.00;
				valTwo = amount_total - valOne;
				
				toastr.error('O valor deve ser maior que ' + this.formatPriceWithCurrency( '1.00' ) ,'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
			} else if(parseFloat(valOne) < parseFloat(1.00)) {
				valOne = 1.00;
				valTwo = amount_total - valOne;
				
				toastr.error('O valor deve ser maior ou igual a ' + this.formatPriceWithCurrency( '1.00' ) ,'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
			}
			
			jQuery('#' + this.getCode() + '_cc_multiple_val').val( this.formatPrice(valOne) ).change();
			
			/** Salva valores um e dois, cartão um ok **/
			amount_one 		= valOne;
			amount_two 		= valTwo;
			amount_ticket 	= valTwo;
			card_one 		= cardIndex;
			amountOption 	= amount_one;

			var instOne = jQuery('#aqpago_one_installments').val();
			var instTwo = jQuery('#aqpago_two_installments').val();
			
			/** ***/
			jQuery("#aqpago_installments option, #aqpago_one_installments option").each(function() {
				jQuery(this).remove();
			});
			
			jQuery('.installment-view').html('');
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee1 = ((amount_one / (100 - data.tax)) * 100);
				var vlPc1 = vlFee1 / install;
				
				jQuery('#aqpago_one_installments').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
				}));
				
				var vlFee2 = ((amount_two / (100 - data.tax)) * 100);
				var vlPc2 = vlFee2 / install;
				
				jQuery('#aqpago_installments').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(vlPc2) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(vlFee2) + '</b></p>');
				
				if(jQuery('#aqpago_installments').val() == install) {
					jQuery('.description-installment, .description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne);
			jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').val(instTwo);
			/** ***/
			
			jQuery('#' + this.getCode() + '_cc_number').val('').change();
			jQuery('#' + this.getCode() + '_cc_owner').val('').change();
			jQuery('#' + this.getCode() + '_expiration').val('').change();
			jQuery('#' + this.getCode() + '_expiration_yr').val('').change();
			jQuery('#' + this.getCode() + '_cc_cid').val('').change();
			jQuery('#' + this.getCode() + '_installments').val('1').change();
			jQuery('#' + this.getCode() + '_cc_multiple_val').val(
				this.formatPrice( amount_two )
			);

			jQuery('#' + this.getCode() + '_one_installments').val( installments );
			jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val( 
				this.formatPrice( amount_one ) 
			);			
			
			if (card_two) {
				jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val(
					this.formatPrice( amount_two ) 
				);
				/*
				jQuery('#two-card-bottom strong').html( cards[ card_two ].installment + 'x' );
				jQuery('#two-card-bottom span').html( 
					this.formatPriceWithCurrency( (amount_two / cards[ card_two ].installment).toFixed(2) ) 
				); */
			} else {
				set_two_card = true;
			}
		}
		
		/** Multi Crédito segundo cartão **/
		if(type_payment == 'credit_multiple' && position == 'two') {
			var valTwo 	= jQuery('#' + this.getCode() + '_cc_multiple_val').val();
			valTwo 		= this.customValValidate(valTwo);
			var valOne 	= amount_total - valTwo;
			
			/** valor do primeiro cartão maior que total - 1 **/
			if(parseFloat(valTwo) > parseFloat( parseFloat( amount_total - 1 ).toFixed(2) ) ){
				valTwo = parseFloat( amount_total - 1 ).toFixed(2);
				valOne = amount_total - valTwo;
				
				toastr.error('O valor não pode ser maior que ' + this.formatPriceWithCurrency( valTwo ) ,'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
			}
			else if(parseFloat(valTwo) < parseFloat(1.00)){
				valTwo = 1.00;
				valOne = amount_total - valTwo;
				
				toastr.error('O valor deve ser maior ou igual a ' + this.formatPriceWithCurrency( '1.00' ) ,'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
			}
			
			/** Salva valores um e dois, cartão dois ok **/
			amount_one 		= valOne;
			amount_two 		= valTwo;
			amount_ticket 	= valTwo;
			card_two	 	= cardIndex;
			amountOption 	= amount_two;
			
			
			var instOne = jQuery('#aqpago_one_installments').val();
			var instTwo = jQuery('#aqpago_two_installments').val();
			
			/** ***/
			jQuery("#aqpago_installments option, #aqpago_one_installments option, #aqpago_installments_oneCard option, #aqpago_two_installments option, #aqpago_installments_twoCard option").each(function() {
				jQuery(this).remove();
			});
			
			jQuery('.installment-view').html('');
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee1 = ((amount_one / (100 - data.tax)) * 100);
				var vlPc1 = vlFee1 / install;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
				}));
				
				if(instOne == install) {
					jQuery('.description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees);
					jQuery('#one-card-bottom strong').html(instOne + 'x');
					jQuery('#one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc1));
				}
				
				var vlFee2 = ((amount_two / (100 - data.tax)) * 100);
				var vlPc2 = vlFee2 / install;
				
				jQuery('#aqpago_installments, #aqpago_two_installments, #aqpago_installments_twoCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(vlPc2) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(vlFee2) + '</b></p>');
				
				if(jQuery('#aqpago_installments').val() == install) {
					jQuery('.description-installment').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);
				}
				
				if (instTwo == install) {
					jQuery('.description-installment-2').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);
					jQuery('#two-card-bottom strong').html(instTwo + 'x');
					jQuery('#two-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc2));
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne);
			jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').val(instTwo);
			/** ***/
			
			jQuery('#' + this.getCode() + '_two_installments').val( installments );
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val( 
				this.formatPrice( amount_two )
			);
			
			jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val( 
				this.formatPrice( amount_one ) 
			);
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_one )
			);
			
			/*
			jQuery('#one-card-bottom strong').html( cards[ card_one ].installment + 'x' );
			jQuery('#one-card-bottom span').html( 
				this.formatPriceWithCurrency( (amount_one / cards[ card_one ].installment ) )
			); */	
		}
		
		/** Multi boleto primeiro cartão **/
		if(type_payment == 'ticket_multiple' && position == 'one') {
			var valOne 	= jQuery('#' + this.getCode() + '_cc_multiple_val').val();
			valOne 		= this.customValValidate(valOne);
			var valTwo 	= amount_total - valOne;
			
			if(parseFloat(valTwo) < parseFloat(10.00)) {
				var totalVal = amount_total;
				valTwo = 10.00;
				valOne = totalVal - valTwo;
				toastr.error('O valor do boleto não pode ser menor que R$10,00','Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
			}
			
			/** Salva valores um e dois, cartão um ok **/
			amount_one 		= valOne;
			amount_two 		= valTwo;
			amount_ticket 	= valTwo;
			card_one 		= cardIndex;
			amountOption 	= amount_one;
			
			jQuery('#' + this.getCode() + '_cc_number').val('').change();
			jQuery('#' + this.getCode() + '_cc_owner').val('').change();
			jQuery('#' + this.getCode() + '_expiration').val('').change();
			jQuery('#' + this.getCode() + '_expiration_yr').val('').change();
			jQuery('#' + this.getCode() + '_cc_cid').val('').change();
			jQuery('#' + this.getCode() + '_installments').val('1').change();
			jQuery('#' + this.getCode() + '_cc_multiple_val').val(
				this.formatPrice( amount_two )
			);

			jQuery('#' + this.getCode() + '_one_installments').val( installments );
			jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val( 
				this.formatPrice( amount_one ) 
			);
			
			

			var instOne = jQuery('#aqpago_one_installments').val();
			var instTwo = jQuery('#aqpago_two_installments').val();
			
			/** ***/
			jQuery("#aqpago_installments option, #aqpago_one_installments option").each(function() {
				jQuery(this).remove();
			});
			
			jQuery('.installment-view').html('');
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee1 = ((amount_one / (100 - data.tax)) * 100);
				var vlPc1 = vlFee1 / install;
				
				jQuery('#aqpago_one_installments').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
				}));
				
				var vlFee2 = ((amount_two / (100 - data.tax)) * 100);
				var vlPc2 = vlFee2 / install;
				
				jQuery('#aqpago_installments').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(vlPc2) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(vlFee2) + '</b></p>');
				
				if(instOne == install) {
					jQuery('.description-installment, .description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees);
					jQuery('#one-card-bottom strong').html( instOne + 'x' );
					jQuery('#one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc1));				
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne);
			jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').val(instTwo);
			/** ***/
			
			/*
			jQuery('#one-card-bottom strong').html( cards[ card_one ].installment + 'x' );
			jQuery('#one-card-bottom span').html( 
				this.formatPriceWithCurrency( (amount_one / cards[ card_one ].installment ).toFixed(2) ) 
			); */
		
			jQuery('#three-card-bottom strong').html( '1x' );
			jQuery('#three-card-bottom span').html( 
				this.formatPriceWithCurrency( amount_ticket )
			);
		}
		
		/** Modal one Card **/
		jQuery('#' + this.getCode() + '_cc_multiple_val_' + position + 'Card').val(
			this.formatPrice( amountOption )
		);
		
		/** Não é cartão salvo **/
		if(!card_saved) jQuery('#' + this.getCode() + '_cc_number_card' + this.capitalizeFirstLetter(position)).val( card['number'].replace(/\s+/g, '') );
		jQuery('#' + this.getCode() + '_cc_owner_card' + this.capitalizeFirstLetter(position)).val( card['owerName'] );
		jQuery('#' + this.getCode() + '_expiration_card' + this.capitalizeFirstLetter(position)).val( card['expiration_month'] );
		jQuery('#' + this.getCode() + '_expiration_yr_card' + this.capitalizeFirstLetter(position)).val( card['expiration_year'] );
		jQuery('#' + this.getCode() + '_cc_cid_card' + this.capitalizeFirstLetter(position)).val( card['securityCode'] );
		jQuery('#' + this.getCode() + '_documento_card' + this.capitalizeFirstLetter(position)).val( card['taxvat'] );
		jQuery('#' + this.getCode() + '_installments_' + position + 'Card').val( card['installment'] );
		/***********/
		
		/*
		jQuery('#' + position + '-card-bottom strong').html( installments + 'x' );
		jQuery('#' + position + '-card-bottom span').html( 
			this.formatPriceWithCurrency( (amountOption / installments).toFixed(2) )
		);		*/
		
		jQuery('#ticket-card-bottom strong').html( '1x' );
		jQuery('#ticket-card-bottom span').html( 
			this.formatPriceWithCurrency( amount_ticket )
		);
		
		jQuery('#' + position + '-middle-number-card').html( fourDigits );
		jQuery('#' + position + '-grand-total-view').html( 
			this.formatPriceWithCurrency( amountOption )
		);

		if(card_saved){
			if(position == 'one'){
				
				jQuery('#one-middle-number-card').html( cardIndex.substr(-4, 4) );
				
				if(type_payment == 'ticket_multiple') {
					jQuery('#list-new').slideUp('100');
					jQuery('.box-select-card-li').slideUp('100');
					jQuery('.box-select-card').slideDown('100');
					jQuery('#list-' + cardIndex).slideDown('100');
				}
			}
			if(position == 'two'){
				jQuery('.box-select-card-title').slideUp('100');
				jQuery('.box-select-card').slideUp('100');
				
				jQuery('#two-middle-number-card').html( cardIndex.substr(-4, 4) );
			}
		}
		
		if(type_payment == 'credit'){
			/** salva cartão **/
			card_one = cardIndex;
			this.showPayCard();
		}
		else if(type_payment == 'credit_multiple' && position == 'one'){
			jQuery('#' + this.getCode() + '_installments_cardTwo').val(1).change();
			this.showPayOneCard();
		}
		else if(type_payment == 'credit_multiple' && position == 'two'){
			this.showPayTwoCard();
		}
		else if(type_payment == 'ticket_multiple' && position == 'one') {
			card_one = cardIndex;
			this.showPayTicketMulti();
		}
		
		/** Abrir e fechar campos do modal **/
		if(type_payment == 'credit' || type_payment == 'credit_multiple' || type_payment == 'ticket_multiple'){
			/** Cartão um salvo selecionado **/
			if(saved_card_one){
				jQuery('.aqban-modal-one-card .field-name-lastname, .aqban-modal-one-card .valid_month_checkout, .aqban-modal-one-card .field-name-name_card, .aqban-modal-one-card .field-not, .aqban-modal-one-card .field-name-documento, .aqban-modal-one-card').slideUp();
				jQuery('.aqban-modal-one-card .modal-new-card').slideDown();
			}
			else {
				jQuery('.aqban-modal-one-card .field-name-lastname, .aqban-modal-one-card .valid_month_checkout, .aqban-modal-one-card .field-name-name_card, .aqban-modal-one-card .field-not, .aqban-modal-one-card .field-name-documento, .aqban-modal-one-card').slideDown();
				jQuery('.aqban-modal-one-card .modal-new-card').slideUp();
			}

			/** Cartão dois salvo selecionado **/
			if(saved_card_two){
				jQuery('.aqban-modal-two-card .field-name-lastname, .aqban-modal-two-card .valid_month_checkout, .aqban-modal-two-card .field-name-name_card, .aqban-modal-two-card .field-not, .aqban-modal-two-card .field-name-documento, .aqban-modal-one-card').slideUp();
				jQuery('.aqban-modal-two-card .modal-new-card').slideDown();
			}
			else {
				jQuery('.aqban-modal-two-card .field-name-lastname, .aqban-modal-two-card .valid_month_checkout, .aqban-modal-two-card .field-name-name_card, .aqban-modal-two-card .field-not, .aqban-modal-two-card .field-name-documento, .aqban-modal-one-card').slideDown();
				jQuery('.aqban-modal-two-card .modal-new-card').slideUp();
			}
			
			jQuery('.aqban-modal-one-card').slideDown();
		}		
	},
	showPayTicketMulti: function(){
		var self  = this;
		
		jQuery('#ticket-grand-total-view').html(
			this.formatPriceWithCurrency( amount_ticket )
		);
		
		jQuery('#ticket-card-bottom span').html(
			this.formatPriceWithCurrency( amount_ticket )
		);

		jQuery('.card-box-all').slideUp('100');
		
		jQuery('#' + this.getCode() + '_one_installments').val( cards[card_one].installment );
		
		/*
		jQuery('#one-card-bottom strong').html( cards[card_one].installment + 'x' );
		jQuery('#one-card-bottom span').html( 
			this.formatPriceWithCurrency( (amount_one / cards[card_one].installment) )
			
		); */
		
		/** Modal **/
		jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val( 
			this.formatPrice( amount_one )
		);
		
		jQuery('#' + this.getCode() + '_cc_number_cardOne').val( cards[card_one].number );
		jQuery('#' + this.getCode() + '_cc_owner_cardOne').val( cards[card_one].owerName );
		jQuery('#' + this.getCode() + '_expiration_cardOne').val( cards[card_one].expiration_month );
		jQuery('#' + this.getCode() + '_expiration_yr_cardOne').val( cards[card_one].expiration_year );
		jQuery('#' + this.getCode() + '_cc_cid_cardOne').val( cards[card_one].securityCode );
		jQuery('#' + this.getCode() + '_documento_cardOne').val( cards[card_one].taxvat ).change();
		jQuery('#' + this.getCode() + '_installments_oneCard').val( cards[ card_one ].installment ).change();
		/***********/
		
		jQuery('#one-middle-number-card').html( cards[card_one].number.substr(-4, 4) );
		jQuery('#one-grand-total-view').html( 
			this.formatPriceWithCurrency( amount_one )
		);
		
		jQuery('#multi-actions').slideUp();
		jQuery('#one-payment-right-empty').slideUp();
		jQuery('#img-flag-card').slideUp();
		jQuery('#multi-actions-two').slideDown();
		jQuery('#one-payment-right-full').slideDown();
		jQuery('#one-li-form-payment').slideDown();
		
		/************/
		jQuery('.grandtotal-box').html(
			jQuery('#iwd_opc_review_totals').html()
		);
		/************/
		
		jQuery('.aqbank_custom_informations').slideDown();
		jQuery('.box-select-card').slideUp();

		/** abrir lista de seleção de cartão **/
		if(totalSavedCards > 1) {
			jQuery('#list-' + card_one).slideDown('100');
			jQuery('.box-select-card-custom').slideDown('100');
		}
		
		setTimeout(function(){ 	
			jQuery('#three-li-form-payment').slideDown('100');
		}, 500);
	},
	showPayOneCard: function(){
		/********** Multiple Ticket Values ********/
		jQuery('#ticket-grand-total-view').html(
			this.formatPriceWithCurrency( amount_ticket )
		);

		jQuery('#ticket-card-bottom span').html(
			this.formatPriceWithCurrency( amount_ticket )
		);
		
		/***********************************/
		
		jQuery('.card-box-all').slideUp('100');
		
		jQuery('#multi-actions').slideUp();
		jQuery('#one-payment-right-empty').slideUp();
		jQuery('#img-flag-card').slideUp();
		jQuery('#multi-actions-two').slideDown();
		jQuery('#one-payment-right-full').slideDown();
		jQuery('#one-li-form-payment').slideDown();
		
		/************/
		jQuery('.grandtotal-box').html(
			jQuery('#iwd_opc_review_totals').html()
		);
		/************/
		
		if(show_saved_card && card_one && !card_two){
			jQuery('.box-select-card-li-arrow').removeClass('active-new');
			jQuery('.box-select-card-li-arrow span').slideUp();
			
			jQuery('.card-box-all').slideUp(1);
			
			if(totalSavedCards == 1) {
				this.setNewCard();
				
				return false;
			}
			else {
				jQuery('#one-li-form-payment').slideUp(1);
				
				jQuery('.card_cvv_img').slideDown('100');
				jQuery('.box-select-card-li').slideDown('100');
				jQuery('.box-select-card').slideDown('100');
				jQuery('.box-select-card-title').slideDown('100');
				jQuery('#list-' + card_one ).slideUp();
				jQuery('#list-new').slideDown('100');
			}
		}
		else {
		
			setTimeout(function(){ 
				jQuery('.card-box-all').slideDown('100');
			}, 500);
		}
	},
	setNewCardModalOne: function() {
		saved_card_one = false;
		
		if(type_payment == 'credit' && card_saved) {
			card_saved = false;
		}
		
		jQuery('.aqban-modal-one-card .input-number.cvv').val('');
		jQuery('.aqban-modal-one-card .field-name-lastname, .aqban-modal-one-card .valid_month_checkout, .aqban-modal-one-card .field-name-name_card, .aqban-modal-one-card .field-not, .aqban-modal-one-card .field-name-documento, .aqban-modal-one-card').slideDown();
		jQuery('.aqban-modal-one-card .modal-new-card').slideUp();
		jQuery('.card-one-set').slideUp();
	},
	setNewCardModalTwo: function() {
		saved_card_two = false;
		
		jQuery('.aqban-modal-two-card .input-number.cvv').val('');
		jQuery('.aqban-modal-two-card .field-name-lastname, .aqban-modal-two-card .valid_month_checkout, .aqban-modal-two-card .field-name-name_card, .aqban-modal-two-card .field-not, .aqban-modal-two-card .field-name-documento, .aqban-modal-one-card').slideDown();
		jQuery('.aqban-modal-two-card .modal-new-card').slideUp();
		jQuery('.card-two-set').slideUp();
	},
	setNewCard: function() {
		card_saved = false;
		
		jQuery('#list-new').slideUp(1);
		jQuery('.card_cvv_img').slideUp(1);
		jQuery('.fieldset.aqbank-checkout').slideDown(1);
		jQuery('.field-name-lastname, .valid_month_checkout, .field-name-name_card, .field-not, .field-name-documento').slideDown(1);
		
		
		if(saved_card_one){
			jQuery('.aqban-modal-one-card .field-name-lastname, .aqban-modal-one-card .valid_month_checkout, .aqban-modal-one-card .field-name-name_card, .aqban-modal-one-card .field-not, .aqban-modal-one-card .field-name-documento').slideUp(1);
		}
		
		if(saved_card_two){
			jQuery('.aqban-modal-two-card .field-name-lastname, .aqban-modal-two-card .valid_month_checkout, .aqban-modal-two-card .field-name-name_card, .aqban-modal-two-card .field-not, .aqban-modal-two-card .field-name-documento').slideUp(1);
		}
		
		if(type_payment == 'credit_multiple'){
			if(card_one){
				jQuery('.box-select-card-title').slideUp();
				jQuery('.box-select-card-li').slideUp();
				
				jQuery('.card-box-all').slideDown('100');
			}
			else {
				
				jQuery('.box-select-card-title').slideUp();
				jQuery('.box-select-card-li').slideUp();
				
				if(type_payment == 'credit'){
					jQuery('#one-action').slideDown();
				}
				
				jQuery('.card-box-all').slideDown('100');

			}
			
		}
		else if(type_payment == 'ticket_multiple'){
				jQuery('.box-select-card-title').slideUp();
				jQuery('.box-select-card-li').slideUp();
				
				if(type_payment == 'credit'){
					jQuery('#one-action').slideDown();
				}
				
				jQuery('#multi-actions-two').slideUp(1);
				
				jQuery('#multi-actions-one-ticket').slideDown('100');
				jQuery('.card-box-all').slideDown('100');
		}
		else {
			jQuery('.box-select-card-title').slideUp();
			jQuery('.box-select-card-li').slideUp();
			
			if(type_payment == 'credit'){
				//jQuery('#one-action').slideDown();
			}
			
			jQuery('.card-box-all').slideDown('100');
		}
	},	
	showPayCard: function(){
		jQuery('#multi-actions').slideUp();
		jQuery('#one-payment-right-empty').slideUp();
		jQuery('#img-flag-card').slideUp();
		
		jQuery('#multi-actions-two').slideDown();
		jQuery('#one-payment-right-full').slideDown();
		jQuery('.aqbank_payment_integral').slideDown();
		
		jQuery('#one-li-form-payment').slideDown('100');
		/*jQuery('#button-finished').slideDown('100');*/
		
		/************/
		jQuery('.grandtotal-box').html(
			jQuery('#iwd_opc_review_totals').html()
		);
		/************/
		
		if(totalSavedCards == 1){
			jQuery('.box-select-card').slideUp();
		}
	},
	setPaymentFlag: function(value){
		var Visa 		= /^4/;
		var Mastercard 	= /^5([1-9]\d{1}|222100|272099)/;
		var Banescard 	= /^(60420[1-9]|6042[1-9][0-9]|6043[0-9]{2}|604400|603182)/;
		var Amex 		= /^3(4|7)/;
		var Discover 	= /^6(011|22[0-9]{1}|4|5)/;
		var HIPERCARD	= /^(38|60\d{2})/;
		var Diners 		= /^(30[0-5]{1}|36(0|[2-9]{1})|3[8-9]{2}|2014|2149|309)\d/;
		var JCB 		= /^(2131|1800|35)/;
		var ELO 		= /^(4011|438935|451416|4576|504175|5066|5067|50900|50904[0-9]|50905(1|2)|509064|50906[6-9]|509074|627780|636297|636368|636505)/;
		var AURA		= /^50\d{4}/;
		var HIPER		= /^637095/;
		
		if(Mastercard.test(value)) {
			return 'mastercard';
		}
		else if(Amex.test(value)) {
			return 'amex';
		}
		else if(Banescard.test(value)) {
			return 'banescard';
		}
		else if(Discover.test(value)) {
			return 'discover';
		}
		else if(HIPERCARD.test(value)) {
			return 'hipercard';
		}		
		else if(JCB.test(value)) {
			return 'jcb';
		}		
		else if(ELO.test(value)) {
			return 'elo';
		}
		else if(AURA.test(value)) {
			return 'aura';
		}		
		else if(HIPER.test(value)) {
			return 'hiper';
		}		
		else if(Visa.test(value)) {
			return 'visa';
		}	
		else if(Diners.test(value)) {
			return 'diners';
		}		
		else {
			return '';
		}		
	},
	customValValidate: function(valOne) {
		if (valOne.toString().indexOf('.') > -1 && valOne.toString().indexOf(',') > -1)  {
			valOne = valOne.replace('.', '');
			valOne = valOne.replace(',', '.');
		}
		else if (!valOne.toString().indexOf('.') > -1 && valOne.toString().indexOf(',') > -1)  {
			valOne = valOne.replace(',', '.');
		}
		
		
		if(valOne <= 0) {
			valOne = (amount_total / 2).toFixed(2);
			toastr.error('Valor não pode ser menor ou igual a zero!','Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});	
		}
		if(parseFloat(valOne).toFixed(2) > parseFloat(amount_total).toFixed(2)) {
			valOne = (amount_total / 2).toFixed(2);
			toastr.error('Valor não pode ser maior que o total do pedido', {extendedTimeOut: 3000,tapToDismiss:true});	
		}
		
		return valOne;
	},
	capitalizeFirstLetter: function(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	},
	onCodehasFocus: function() {
		jQuery(".card-box").removeClass("card-front");
		jQuery(".card-box").addClass("card-back");
		return true;
	},			
	onCodeFocusOut: function() {
		jQuery(".card-box").removeClass("card-back");
		jQuery(".card-box").addClass("card-front");
		return true;
	},
	
	getCode: function () {
		return 'aqpago';
	},
	
	getArrowRight: function() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991"><path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"/></svg>';
	},
	formatPriceWithCurrency: function(price){
		var formated = Intl.NumberFormat('pt-br', {style: 'currency', currency: 'BRL'}).format(price);
		
		return formated;
	},
	formatPrice: function(price){
		var formated = Intl.NumberFormat('pt-br', {minimumFractionDigits: 2}).format(price);
		
		return formated;
	},
	setOrderSum: function(selectClass) {
		
		var after = 1;
		if(jQuery('.' + selectClass).length) {
			var before = jQuery('.' + selectClass).val();
			before = parseInt( before );
			after = before + 1;
			
			if(after > 12) {
				after = 12;
			} 
			if(after <= 0) {
				after = 1;
			}
			
			if(totalInstallmentMax < after) {
				after = totalInstallmentMax;
			}
			
			jQuery('.' + selectClass).val( after ).change();
		}
		
	},
	setOrderSub: function(selectClass) {
		var after = 1;
		if(jQuery('.' + selectClass).length) {
			var before = jQuery('.' + selectClass).val();
			before = parseInt( before );
			after = before - 1;

			if(after > 12) {
				after = 12;
			} 
			if(after <= 0) {
				after = 1;
			} 
			
			if(totalInstallmentMax < after) {
				after = totalInstallmentMax;
			}
			
			jQuery('.' + selectClass).val( after ).change();
		}
	},
	saveCardOneEdit: function(modal = false){
		jQuery('#one-li-form-payment').removeClass('aqpago-erro');
		jQuery('#one-li-form-payment .li-position-card img').attr('src', this.getCardOne());
		jQuery('#onecard-button-modal').attr('src', this.getIconEdit());
		jQuery('#one-li-form-payment .text-edit').html('VOCÊ PODE EDITAR O CARTÃO');
		
		
		if(modal){
			var ccNumber  = jQuery('#' + this.getCode() + '_cc_number_cardOne').val().replace(/[^0-9]/g,'');
			if(ccNumber == '') ccNumber = card_one;
			
			var cardIndex = ccNumber.substr(0, 4) + '' + ccNumber.substr(-4, 4);
			
			if(type_payment == 'credit') jQuery('.box-select-card-custom').slideUp();
		}
		else {
			if(card_one){
				var ccNumber  = card_one;
				var cardIndex = card_one;
			} else {
				var ccNumber  = jQuery('#' + this.getCode() + '_cc_number_cardOne').val().replace(/[^0-9]/g,'');
				var cardIndex = ccNumber.substr(0, 4) + '' + ccNumber.substr(-4, 4);
			}
		}
		
		var installments 		= jQuery('#' + this.getCode() + '_installments_oneCard').val();
		var expiration_month 	= jQuery('#' + this.getCode() + '_expiration_cardOne').val();
		var expiration_year 	= jQuery('#' + this.getCode() + '_expiration_yr_cardOne').val();
		var securityCode	 	= jQuery('#' + this.getCode() + '_cc_cid_cardOne').val();
		var owerName		 	= jQuery('#' + this.getCode() + '_cc_owner_cardOne').val();
		
		var fourDigits			= ccNumber.substr(-4, 4);
		var imOwer 				= (jQuery('#not_cardOne').is(":checked")) ? true : false;
		var flag	 			= this.setPaymentFlag(ccNumber);
		var taxvat		 		= jQuery('#' + this.getCode() + '_documento_cardOne').val().replace(/[^0-9]/g,'');
		
		var card 					= [];		
		card['installment'] 		= installments;
		card['number'] 				= ccNumber;
		card['expiration_month'] 	= expiration_month;
		card['expiration_year'] 	= expiration_year;
		card['securityCode'] 		= securityCode;
		card['owerName'] 			= owerName;
		card['flag'] 				= flag;
		card['imOwer'] 				= imOwer;
		card['taxvat'] 				= taxvat;
		

		if(cards.indexOf(cardIndex) && saved_card_one) {
			card['card_id'] = cards[cardIndex].card_id;
		}
		else {
			card['card_id'] = false;
		}
		
		var validCard = this.validDataCardFull(card);
		if(validCard !== true) {
			toastr.error(validCard,'Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
			return false;
		}
		
		if(type_payment == 'credit_multiple' || type_payment == 'ticket_multiple') {
			var valOne 	= jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val();
			valOne 		= this.customValValidate(valOne);	
			var valTwo 	= amount_total - valOne;
			
			if(type_payment == 'ticket_multiple') {
				valTwo 	= amount_total - valOne;
				
				if(valTwo < 10) {
					var totalVal = amount_total;
					valTwo = 10.00;
					valOne = totalVal - valTwo;
					
					jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val( this.formatPrice(valOne) ).change();
					toastr.error('O valor do boleto não pode ser menor que R$10,00','Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
				}
			}
			
			if(valOne > (amount_total - 1)){
				toastr.error(
					'O valor não pode ser maior que ' + this.formatPriceWithCurrency( (amount_total - 1) ), 
					'Atenção!', 
					{extendedTimeOut: 3000,tapToDismiss:true}
				);
				
				return false;
			}
			else if(valOne < 1){
				toastr.error(
					'O valor não pode ser menor que ' + this.formatPriceWithCurrency( '1.00' ), 
					'Atenção!', 
					{extendedTimeOut: 3000,tapToDismiss:true}
				);
				
				return false;
			}
			
			amount_one 		= valOne;
			amount_two 		= valTwo;
			amount_ticket 	= valTwo;
			
			jQuery('#two-grand-total-view').html(
				this.formatPriceWithCurrency( amount_two )
			);				
			jQuery('#ticket-grand-total-view').html(
				this.formatPriceWithCurrency( amount_ticket )
			);				
			jQuery('#ticket-card-bottom span').html(
				this.formatPriceWithCurrency( amount_ticket )
			);
			
			jQuery('#one-grand-total-view').html( 
				this.formatPriceWithCurrency( amount_one )
			);
			
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val(
				this.formatPrice( amount_two )
			);
			
			jQuery('#' + this.getCode() + '_cc_multiple_val').val(
				this.formatPrice( amount_two )
			);
			
			jQuery('#one-card-bottom span').html( this.formatPriceWithCurrency( (amount_one / installments).toFixed(2) ) );
			jQuery('#ticket-grand-total-view').html( this.formatPriceWithCurrency( amount_ticket ) );
			jQuery('#ticket-card-bottom').html( this.formatPriceWithCurrency( amount_ticket ) );
			
			if(card_two) {
				jQuery('#two-card-bottom strong').html( cards[ card_two ].installment + 'x' );
				if(cards[ card_two ].installment > 1){
					jQuery('#two-card-bottom span').html( this.formatPriceWithCurrency( (amount_two / cards[ card_two ].installment).toFixed(2) ) );
				}
				else {
					jQuery('#two-card-bottom span').html( this.formatPriceWithCurrency( amount_two ) );
				}
			}
			
			var instOne = jQuery('#aqpago_installments_oneCard').val();
			var instTwo = jQuery('#aqpago_installments_twoCard').val();
			
			jQuery("#aqpago_one_installments option, #aqpago_installments_oneCard option, #aqpago_two_installments option, #aqpago_installments_twoCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee1 = ((amount_one / (100 - data.tax)) * 100);
				var vlPc1 = vlFee1 / install;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
				}));
				
				if(instOne == install) {
					jQuery('.description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees);
					jQuery('.one-card-bottom strong').html(instOne + 'x');
					jQuery('.one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc1));
				}
				
				var vlFee2 = ((amount_two / (100 - data.tax)) * 100);
				var vlPc2 = vlFee2 / install;
				
				jQuery('#aqpago_installments, #aqpago_two_installments, #aqpago_installments_twoCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(vlPc2) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(vlFee2) + '</b></p>');
				
				if(instTwo == install) {
					jQuery('.description-installment-2').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);
					jQuery('.two-card-bottom strong').html(instTwo + 'x');
					jQuery('.two-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc2));				
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne).change();
			jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').val(instTwo).change();
		}
		else {
			var valOne = amount_total;
			
			jQuery('#' + this.getCode() + '_cc_multiple_val').val(
				this.formatPrice( amount_total )
			);
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_total )
			);
			
			if(installments > 1) {
				jQuery('#one-card-bottom span').html( this.formatPriceWithCurrency( (amount_total / installments).toFixed(2) ) );
			}
			else {
				jQuery('#one-card-bottom span').html( this.formatPriceWithCurrency( amount_total ) );
			}
			
			
			var instOne = jQuery('#aqpago_installments_oneCard').val();
			jQuery("#aqpago_one_installments option, #aqpago_installments_oneCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee1 = ((amount_total / (100 - data.tax)) * 100);
				var vlPc1 = vlFee1 / install;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
				}));
				
				if(instOne == install) {
					jQuery('.description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees);
					jQuery('.one-card-bottom strong').html(instOne + 'x');
					jQuery('.one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc1));
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne).change();
		}
		
		var bandeira = this.setPaymentFlag(ccNumber);
		this.setBandeiraInfo('#one-li-form-payment .li-number-card .img-flag', bandeira, 'info');
		
		if(modal){
			if(cards.indexOf(cardIndex) && saved_card_one) {
				card['card_id'] = cards[cardIndex].card_id;
			}
			else {
				card['card_id'] = false;
			}
		}
		else {
			card['card_id'] = cards[cardIndex].card_id;
		}
		
		cards[cardIndex] 	= card;
		card_one 			= cardIndex;
		
		jQuery('#' + this.getCode() + '_one_installments').val( installments );
		jQuery('#one-card-bottom strong').html( installments + 'x' );
		jQuery('#one-middle-number-card').html( fourDigits );
		
		return true;
	},
	saveCardTwoEdit: function(modal = false){
		jQuery('#two-li-form-payment').removeClass('aqpago-erro');
		jQuery('#two-li-form-payment .li-position-card img').attr('src', this.getCardTwo());
		jQuery('#twocard-button-modal').attr('src', this.getIconEdit());
		jQuery('#two-li-form-payment .text-edit').html('VOCÊ PODE EDITAR O CARTÃO');
		
		if(modal) {
			var ccNumber  = jQuery('#' + this.getCode() + '_cc_number_cardTwo').val().replace(/[^0-9]/g,'');
			
			if(ccNumber == '') ccNumber = card_two;
			
			var cardIndex = ccNumber.substr(0, 4) + '' + ccNumber.substr(-4, 4);
		}
		else {
			if(card_two) {
				var ccNumber  = card_two;
				var cardIndex = card_two;
				
			} else {
				var ccNumber  = jQuery('#' + this.getCode() + '_cc_number_cardTwo').val().replace(/[^0-9]/g,'');
				var cardIndex = ccNumber.substr(0, 4) + '' + ccNumber.substr(-4, 4);
			}
		}
		
		var installments 		= jQuery('#' + this.getCode() + '_installments_twoCard').val();
		var expiration_month 	= jQuery('#' + this.getCode() + '_expiration_cardTwo').val();
		var expiration_year 	= jQuery('#' + this.getCode() + '_expiration_yr_cardTwo').val();
		var securityCode	 	= jQuery('#' + this.getCode() + '_cc_cid_cardTwo').val();
		var owerName		 	= jQuery('#' + this.getCode() + '_cc_owner_cardTwo').val();
		var fourDigits			= ccNumber.substr(-4, 4);
		var imOwer 				= (jQuery('#not_cardTwo').is(":checked")) ? true : false;
		var flag	 			= this.setPaymentFlag(ccNumber);
		var taxvat 				= jQuery('#' + this.getCode() + '_documento_cardTwo').val().replace(/[^0-9]/g,'');
		
		
		var card 					= [];
		card['card_id'] 			= false;
		card['installment'] 		= installments;
		card['number'] 				= ccNumber;
		card['expiration_month'] 	= expiration_month;
		card['expiration_year'] 	= expiration_year;
		card['securityCode'] 		= securityCode;
		card['owerName'] 			= owerName;
		card['flag'] 				= flag;
		card['imOwer'] 				= imOwer;
		card['taxvat'] 				= taxvat;
		
		if(cards.indexOf(cardIndex) && saved_card_two) {
			card['card_id'] = cards[cardIndex].card_id;
		}
		else {
			card['card_id'] = false;
		}
		
		var validCard = this.validDataCardFull(card);
		if(validCard !== true) {
			toastr.error(validCard,'Atenção!', {extendedTimeOut: 2000,tapToDismiss:true});
			return false;
		}
		
		if(type_payment == 'credit_multiple' || type_payment == 'ticket_multiple') {
			var valTwo 	= jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val();
			valTwo 		= this.customValValidate( valTwo );	
			var valOne 	= amount_total - valTwo;
			
			jQuery('#' + this.getCode() + '_cc_multiple_val_twoCard').val( this.formatPrice(valTwo) ).change();

			if(valTwo > (amount_total - 1)){
				toastr.error('O valor não pode ser maior que ' + this.formatPriceWithCurrency( (amount_total - 1) ), 'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
				return false;
			}
			else if(valOne < 1){
				toastr.error('O valor não pode ser menor que ' + this.formatPriceWithCurrency( '1.00'), 'Atenção!', {extendedTimeOut: 3000,tapToDismiss:true});
				return false;
			}
			
			amount_one 		= valOne;
			amount_two 		= valTwo;
			amount_ticket 	= valTwo;
			
			/** Input do modal do primeiro cartão **/
			jQuery('#' + this.getCode() + '_cc_multiple_val_oneCard').val(
				this.formatPrice( 
					amount_one
				)
			);
			jQuery('#' + this.getCode() + '_cc_multiple_val').val(
				this.formatPrice(
					amount_two
				)
			);
			
			jQuery('#' + this.getCode() + '_two_installments').val( installments );
			jQuery('#two-card-bottom strong').html( installments + 'x' );
				
			jQuery('#two-middle-number-card').html( fourDigits );
			jQuery('#two-grand-total-view').html(
				this.formatPriceWithCurrency( amount_two )
			);
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_one )
			);
			
			/** Cartão dois **/
			if(installments > 1) {
				jQuery('#two-card-bottom span').html( this.formatPriceWithCurrency( (amount_two / installments).toFixed(2) ) );
			}
			else {
				jQuery('#two-card-bottom span').html( this.formatPriceWithCurrency( amount_two ) );
			}
			
			jQuery('#one-card-bottom span').html( this.formatPriceWithCurrency( (amount_one / cards[ card_one ].installment ).toFixed(2) ) );
		
		
			var instOne = jQuery('#aqpago_installments_oneCard').val();
			var instTwo = jQuery('#aqpago_installments_twoCard').val();
			
			jQuery("#aqpago_one_installments option, #aqpago_installments_oneCard option, #aqpago_two_installments option, #aqpago_installments_twoCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee1 = ((amount_one / (100 - data.tax)) * 100);
				var vlPc1 = vlFee1 / install;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees
				}));
				
				if(instOne == install) {
					jQuery('.description-installment-1').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc1) + ' ' + data.fees);
					jQuery('.one-card-bottom strong').html(instOne + 'x');
					jQuery('.one-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc1));
				}
				
				var vlFee2 = ((amount_two / (100 - data.tax)) * 100);
				var vlPc2 = vlFee2 / install;
				
				jQuery('#aqpago_installments, #aqpago_two_installments, #aqpago_installments_twoCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
				}));
				
				jQuery('.installment-view').append( '<p>' + data.option + ' de <b>' + AQPAGO.formatPriceWithCurrency(vlPc2) + '</b> ' + data.fees + ' - Total <b>' + AQPAGO.formatPriceWithCurrency(vlFee2) + '</b></p>');
				
				if(instTwo == install) {
					jQuery('.description-installment-2').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);
					jQuery('.two-card-bottom strong').html(instTwo + 'x');
					jQuery('.two-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc2));				
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instOne).change();
			jQuery('#aqpago_two_installments, #aqpago_installments_twoCard').val(instTwo).change();
		} else {
			var instTwo = jQuery('#aqpago_installments_oneCard').val();
			jQuery("#aqpago_one_installments option, #aqpago_installments_oneCard option").each(function() {
				jQuery(this).remove();
			});
			
			Object.entries(installMap).forEach(([install, data]) => {
				var vlFee2 = ((amount_total / (100 - data.tax)) * 100);
				var vlPc2 = vlFee2 / install;
				
				jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').append(jQuery('<option>', {
					value: install,
					text: data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees
				}));
				
				if(instTwo == install) {
					jQuery('.description-installment-2').html(data.option + ' de ' + AQPAGO.formatPriceWithCurrency(vlPc2) + ' ' + data.fees);
					jQuery('.two-card-bottom strong').html(instTwo + 'x');
					jQuery('.two-card-bottom span').html(AQPAGO.formatPriceWithCurrency(vlPc2));
				}
			});
			
			jQuery('#aqpago_one_installments, #aqpago_installments_oneCard').val(instTwo).change();
		}
		
		var bandeira = this.setPaymentFlag(ccNumber);
		this.setBandeiraInfo('#two-li-form-payment .li-number-card .img-flag', bandeira, 'info');

		//card['taxvat'] = jQuery('#aqpago_documento_cardTwo').val();
		
		if(modal){
			if(cards.indexOf(cardIndex) && saved_card_two) {
				card['card_id'] = cards[cardIndex].card_id;
			}
			else {
				card['card_id'] = false;
			}
		}
		else {
			card['card_id'] = cards[cardIndex].card_id;
		}
		
		cards[cardIndex] = card;

		card_two = cardIndex;
		
		return true;
	},
	getCardOne: function() {
		return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="40.439" height="55" viewBox="0 0 40.439 55"><defs><filter id="Caminho_9599" x="0" y="0" width="40.439" height="55" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"/><feGaussianBlur stdDeviation="3" result="blur"/><feFlood flood-opacity="0.161"/><feComposite operator="in" in2="blur"/><feComposite in="SourceGraphic"/></filter></defs><g id="Grupo_6541" data-name="Grupo 6541" transform="translate(-70.951 -387)"><g transform="matrix(1, 0, 0, 1, 70.95, 387)" filter="url(#Caminho_9599)"><path id="Caminho_9599-2" data-name="Caminho 9599" d="M18.5,0C30.893,0,40.939,8.283,40.939,18.5S30.893,37,18.5,37Z" transform="translate(-9.5 6)" fill="#561271"/></g><text id="_1_" data-name="1ยบ" transform="translate(83 416)" fill="#fff" font-size="15" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">1&ordm;</tspan></text></g></svg>';
	},			
	getCardTwo: function() {
		return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="39.368" height="53.233" viewBox="0 0 39.368 53.233"><defs><filter id="Caminho_9599" x="0" y="0" width="39.368" height="53.233" filterUnits="userSpaceOnUse"><feOffset dy="3" input="SourceAlpha"/><feGaussianBlur stdDeviation="3" result="blur"/><feFlood flood-opacity="0.161"/><feComposite operator="in" in2="blur"/><feComposite in="SourceGraphic"/></filter></defs><g id="Grupo_6541" data-name="Grupo 6541" transform="translate(9 6)"><g transform="matrix(1, 0, 0, 1, -9, -6)" filter="url(#Caminho_9599)"><path id="Caminho_9599-2" data-name="Caminho 9599" d="M18.5,0C30.3,0,39.868,7.887,39.868,17.617S30.3,35.233,18.5,35.233Z" transform="translate(-9.5 6)" fill="#561271"/></g><text id="_2_" data-name="2ยบ" transform="translate(3.172 21.612)" fill="#fff" font-size="13" font-family="SegoeUI-Bold, Segoe UI" font-weight="700"><tspan x="0" y="0">2&ordm;</tspan></text></g></svg>';
	},	
	getIconEdit: function() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="21.711" height="20.482" viewBox="0 0 21.711 20.482"><g id="Edit_icon-icons.com_71853" transform="translate(-110.9 -133.444)"><g id="Grupo_6545" data-name="Grupo 6545" transform="translate(110.9 133.626)"><path id="Caminho_9602" data-name="Caminho 9602" d="M128.454,156.3H114.5a3.535,3.535,0,0,1-3.6-3.456V139.456A3.535,3.535,0,0,1,114.5,136h8.511a1.547,1.547,0,1,1,0,3.092H114.5a.371.371,0,0,0-.379.363v13.382a.371.371,0,0,0,.379.363h13.951a.371.371,0,0,0,.379-.363v-7.932a1.613,1.613,0,0,1,3.224,0v7.939A3.535,3.535,0,0,1,128.454,156.3Z" transform="translate(-110.9 -136)" fill="#561271"/></g><g id="Grupo_6549" data-name="Grupo 6549" transform="translate(120.509 133.444)"><g id="Grupo_6546" data-name="Grupo 6546" transform="translate(1.454 2.003)"><rect id="Retângulo_1078" data-name="Retângulo 1078" width="3.617" height="8.243" transform="translate(2.475 8.279) rotate(-133.189)" fill="#561271"/></g><g id="Grupo_6547" data-name="Grupo 6547" transform="translate(7.895 0)"><path id="Caminho_9603" data-name="Caminho 9603" d="M324.331,133.619l1.727,1.84a.549.549,0,0,1-.021.775l-1.549,1.457L322,135.047l1.549-1.457A.558.558,0,0,1,324.331,133.619Z" transform="translate(-322 -133.444)" fill="#561271"/></g><g id="Grupo_6548" data-name="Grupo 6548" transform="translate(0 8.055)"><path id="Caminho_9604" data-name="Caminho 9604" d="M211.923,246.8l2.473,2.636-3.5.8Z" transform="translate(-210.9 -246.8)" fill="#561271"/></g></g></g></svg>';
	},
	blockMultiCredit: function (disable) {
		if(disable) {
			jQuery('.aqbank_set_multi_credit').addClass("aqbank_disabled"); 
			jQuery('.aqbank_type_payment_li_box.credit_multiple').addClass("aqbank_disable_method"); 
			jQuery('.aqbank_set_multi_credit .ticket-info-tool').slideDown();
		} else {
			jQuery('.aqbank_set_multi_credit').removeClass("aqbank_disabled");
			jQuery('.aqbank_type_payment_li_box.credit_multiple').removeClass("aqbank_disable_method"); 
			jQuery('.aqbank_set_multi_credit .ticket-info-tool').slideUp();
		}
	},
	blockTicket: function (disable) {
		if(disable) {
			jQuery('.aqbank_set_ticket').addClass("aqbank_disabled"); 
			jQuery('.aqbank_type_payment_li_box.ticket').addClass("aqbank_disable_method"); 
			jQuery('.aqbank_set_ticket .ticket-info-tool').slideDown();
			
			if(type_payment == 'ticket'){
				this.setPaymentMethod( type_payment );
			}
		} else {
			jQuery('.aqbank_set_ticket').removeClass("aqbank_disabled");
			jQuery('.aqbank_type_payment_li_box.ticket').removeClass("aqbank_disable_method"); 
			jQuery('.aqbank_set_ticket .ticket-info-tool').slideUp();
		}
		
	},
	blockMultiTicket: function (disable) {
		if(disable) {
			jQuery('.aqbank_set_multi_ticket').addClass("aqbank_disabled"); 
			jQuery('.aqbank_type_payment_li_box.ticket_multiple').addClass("aqbank_disable_method"); 
			jQuery('.aqbank_set_multi_ticket .ticket-info-tool').slideDown();
			
			if(type_payment == 'ticket_multiple'){
				this.setPaymentMethod( type_payment );
			}
		} else {
			jQuery('.aqbank_set_multi_ticket').removeClass("aqbank_disabled"); 
			jQuery('.aqbank_type_payment_li_box.ticket_multiple').removeClass("aqbank_disable_method"); 
			jQuery('.aqbank_set_multi_ticket .ticket-info-tool').slideUp();
		}
		
	},
	savedCardsDetails: function(){
		var self = this;
		var HtmlCard = "";
		
		/** Existe cartões salvos **/
		if(savedCards != 'false'){
			
			Object.entries(savedCards).forEach(([key, value]) => {
				
				HtmlCard = "<div id='list-" + key + "' class='box-select-card-li box-select-card-two two-li-form-payment'>"
								+ "<div class='box-select-card-float box-select-card-li-flag'>"
								+ this.getFlagSvg( value.flag.toLowerCase() )
								+ "</div>"
								+ "<div class='box-select-card-float box-select-card-li-number'>"
								+ value.four_first + " XXXX XXXX " + value.four_last
								+ "</div>"
								+ "<div class='box-select-card-float box-select-card-li-arrow'>"
								+ "<span>EDITAR</span>"
								+ "<div>" + self.getArrowRight() + "</div>"
								+ "</div>"
							+ "</div>";
				
				jQuery('.box-select-card').append( HtmlCard );
				
				var card 					= [];
				card['installment'] 		= 1;
				card['card_id'] 			= value.card_id;
				card['number'] 				= key;
				card['expiration_month'] 	= null;
				card['expiration_year'] 	= null;
				card['securityCode'] 		= null;
				card['owerName'] 			= null;
				card['flag'] 				= value.flag;
				card['imOwer'] 				= null;
				card['taxvat'] 				= null;
				
				cards[key] 					= card;
				
				jQuery('#list-' + key ).on('click', function() {
					card_saved = key;
					return self.showSavedCardId( key );
				});
			});
			
			add_card = true;
			
			jQuery('#list-new').on('click', function() {
				return self.setNewCard();
			});
		}
		
		return true;
	},
	showSavedCardId: function(cardId){
		var self = this;
		show_saved_card = true;
		
		jQuery('.box-select-card-li-arrow span').slideUp('100');
		jQuery('.box-select-card-title').slideUp('100');
		jQuery('.aqbank-add-new-card').slideUp('100');
		jQuery('.box-select-card-li').slideUp('100');
		jQuery('.card-box-all').slideUp('100');
		
		jQuery('.box-select-card-li-arrow').removeClass('active-new');
		jQuery('.box-select-card-li-arrow span').slideUp('100');
		
			

		
		if(set_one_card && type_payment != 'credit_multiple' && type_payment != 'ticket_multiple' && !set_two_card) {
			/** Seleção do 2 cartão **/
			
			/** Existe cartão selecionado abrir listagem e remover cartão **/
			if(card_one && set_one_card) {
				
				show_saved_card = false;
				select_card 	= false;
				card_one 		= false;
				set_one_card 	= false;
				
				jQuery('.box-select-card-li-arrow').removeClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideUp();
				jQuery('#aqpago_saved_first').val( '' );
				jQuery('#one-li-form-payment').slideUp();
				
				setTimeout(function(){ 
					jQuery('.aqbank-add-new-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
					
					/* jQuery('#list-' + card_one ).slideUp('100'); */
				}, 500);
				
				return false;
			}
		}
		else if(set_one_card && type_payment == 'credit_multiple' && !set_two_card) {
			/** Seleção do 2 cartão **/

			/** Existe cartão selecionado abrir listagem e remover cartão **/
			if(card_one && set_one_card && set_credit_multi) {
				
				show_saved_card = false;
				select_card 	= false;
				card_one 		= false;
				set_one_card 	= false;
				
				jQuery('.box-select-card-li-arrow').removeClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideUp();
				jQuery('#aqpago_saved_first').val( '' );
				
				setTimeout(function(){ 
					jQuery('.aqbank-add-new-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
					
					/* jQuery('#list-' + card_one ).slideUp('100'); */
				}, 500);
				
				return false;
			}
		}
		else if(set_two_card && type_payment == 'credit_multiple') {
			/** Seleção do 2 cartão **/
			if(card_two && set_two_card) {
				show_saved_card = false;
				select_card 	= false;
				card_two 		= false;
				
				jQuery('.box-select-card-li-arrow').removeClass('active-new');
				jQuery('.box-select-card-li-arrow span').slideUp();
				jQuery('#aqpago_saved_first').val( '' );
				
				setTimeout(function(){
					jQuery('.aqbank-add-new-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
				}, 500);
				
				return false;
			}
		}
		else if(type_payment == 'ticket_multiple') {
			/** Existe cartão selecionado abrir listagem e remover cartão **/
			if(card_one && set_one_card) {
				select_card 	= false;
				card_one 		= false;
				set_one_card 	= false;
				set_credit_one 	= false;
				
				jQuery('.li-form-payment').slideUp('100');
				jQuery('#button-finished').slideUp('100');
				jQuery('#aqpago_saved_first').val( '' );
				
				setTimeout(function(){ 
					jQuery('.aqbank-add-new-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
				}, 500);
				
				return false;
			}
		}
		else {
			/** Existe cartão selecionado abrir listagem e remover cartão **/
			if((type_payment == 'credit' || type_payment == 'ticket_multiple') && card_one) {
				
				select_card 	= false;
				card_one 		= false;
				set_one_card 	= false;
				set_credit_one 	= false;
				
				jQuery('#aqpago_saved_first').val( '' );
				
				setTimeout(function(){
					jQuery('.aqbank-add-new-card').slideDown('100');
					jQuery('.box-select-card-title').slideDown('100');
					jQuery('.box-select-card-li').slideDown('100');
				}, 500);
				
				return false;
			}
		}
		
		if(type_payment == 'credit_multiple' || type_payment == 'ticket_multiple') {
			
			jQuery('#one-card-bottom span').html(
				this.formatPriceWithCurrency( amount_one )
			);
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_one )
			);
		}
		else {
			jQuery('#one-card-bottom span').html(
				this.formatPriceWithCurrency( amount_total )
			);
			
			jQuery('#one-grand-total-view').html(
				this.formatPriceWithCurrency( amount_total )
			);
		}
		
		select_card = cardId;
		
		jQuery('#list-' + cardId ).slideDown('100');
		
		if(!set_one_card && type_payment != 'credit_multiple' && type_payment != 'ticket_multiple'){
			//jQuery('#one-li-form-payment').slideDown('100');
		}
		
		if(type_payment == 'ticket_multiple') {
			jQuery('#three-li-form-payment').slideUp(1);
			jQuery('#one-li-form-payment').slideUp(1);
		}
		
		if(!card_one) {
			if(type_payment == 'credit' || type_payment == 'ticket_multiple'){
				set_credit_one = true;
			}
			if(type_payment == 'credit_multiple') {
				set_credit_multi = true;
			}
			
			card_one = cardId;
			set_one_card = true;
		}
		else {
			jQuery('.box-select-card-li-arrow').removeClass('active-new');
			jQuery('.box-select-card-li-arrow span').slideUp();
			jQuery('.box-select-card-title').slideDown();
			
			card_two = cardId;
			set_two_card = true;
		}
		
		jQuery('.fieldset.aqbank-checkout').slideUp(1);
		jQuery('.field-name-lastname, .valid_month_checkout, .field-name-name_card, .field-not, .field-name-documento').slideUp(1);
		
		jQuery('#list-' + cardId).slideDown();
		jQuery('.box-select-card-li-arrow span').slideDown();
		jQuery('.card_cvv_img').slideDown();
		jQuery('.box-select-card').slideDown();
		
		jQuery('.box-select-card-li-arrow').addClass('active-new');
		jQuery('.box-select-card-li-arrow span').slideDown('100');
		
		if(type_payment == 'credit' || type_payment == 'ticket_multiple') {
			jQuery('#multi-actions-two').slideUp(1);
		}
		
		setTimeout(function(){
			jQuery('.card-box-all').slideDown('100');
			
			if(type_payment == 'credit') {
				jQuery('#one-action').slideDown('100');
			}
		}, 500);
	},
	
	registerData: function(name, value, daysToLive) {
		var cookie = name + "=" + encodeURIComponent(value);
		
		if(typeof daysToLive === "number") {
			cookie += "; max-age=" + (daysToLive*24*60*60);
			document.cookie = cookie;
		}
	},
	getData: function(name) {
		var cookieArr = document.cookie.split(";");
		
		for(var i = 0; i < cookieArr.length; i++) {
			var cookiePair = cookieArr[i].split("=");
			
			if(name == cookiePair[0].trim()) {
				return decodeURIComponent(cookiePair[1]);
			}
		}
		
		return null;
	}
}
