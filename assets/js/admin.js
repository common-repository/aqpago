jQuery(window).on('load', function(){
	if (jQuery('#woocommerce_aqpago_installment_up_to').val() == '0') {
		if (jQuery('#woocommerce_aqpago_installment_type').val() == 'aqpago') {
			jQuery('.aqpago-display-none').parent().parent().parent().hide();
		} else {
			jQuery('.aqpago-display-none').parent().parent().parent().show();
		}
	} else {
		jQuery('.aqpago-display-none').parent().parent().parent().hide();
		if (jQuery('#woocommerce_aqpago_installment_type').val() != 'aqpago') {
			jQuery('.aqpago-display-none').parent().parent().parent().each(function (index, value) {
				if( index >= (jQuery('#woocommerce_aqpago_installment_up_to').val() - 1)) {
					console.log( index );
					jQuery(this).show();
				}
			});
		}
	} 
	
	/** Pagar com juros **/
	if (jQuery('#woocommerce_aqpago_installment_interest_free').val() == 0) {
		jQuery('.aqpago-display-none').parent().parent().parent().hide();
		jQuery('#woocommerce_aqpago_installment_up_to').parent().parent().parent().hide();
		jQuery('#woocommerce_aqpago_installment_type').parent().parent().parent().hide();
	} else {
		jQuery('#woocommerce_aqpago_installment_up_to').parent().parent().parent().show();
		jQuery('#woocommerce_aqpago_installment_type').parent().parent().parent().show();
	}
	
	jQuery('#woocommerce_aqpago_installment_interest_free').on('change', function(){
		if (jQuery(this).val() == 1) {
			jQuery('#woocommerce_aqpago_installment_up_to').parent().parent().parent().show();
			jQuery('#woocommerce_aqpago_installment_type').parent().parent().parent().show();
			
			if (jQuery('#woocommerce_aqpago_installment_type').val() == 'aqpago') {
				jQuery('.aqpago-display-none').parent().parent().parent().hide();
			} else {
				jQuery('.aqpago-display-none').parent().parent().parent().hide();
				jQuery('.aqpago-display-none').parent().parent().parent().each(function (index, value) {
					if( index >= (jQuery('#woocommerce_aqpago_installment_up_to').val() - 1)) {
						console.log( index );
						jQuery(this).show();
					}
				});
			}
		} else {
			jQuery('.aqpago-display-none').parent().parent().parent().hide();
			jQuery('#woocommerce_aqpago_installment_up_to').parent().parent().parent().hide();
			jQuery('#woocommerce_aqpago_installment_type').parent().parent().parent().hide();
		}
	});	
	jQuery('#woocommerce_aqpago_installment_type').on('change', function(){
		if (jQuery(this).val() == 'aqpago') {
			jQuery('.aqpago-display-none').parent().parent().parent().hide();
		} else {
			jQuery('.aqpago-display-none').parent().parent().parent().hide();
			jQuery('.aqpago-display-none').parent().parent().parent().each(function (index, value) {
				if( index >= (jQuery('#woocommerce_aqpago_installment_up_to').val() - 1)) {
					console.log( index );
					jQuery(this).show();
				}
			});
		}
	});
	jQuery('#woocommerce_aqpago_installment_up_to').on('change', function(){
		if (jQuery('#woocommerce_aqpago_installment_type').val() == 'aqpago') {
			jQuery('.aqpago-display-none').parent().parent().parent().hide();
		} else {
			jQuery('.aqpago-display-none').parent().parent().parent().hide();
			jQuery('.aqpago-display-none').parent().parent().parent().each(function (index, value) {
				if( index >= (jQuery('#woocommerce_aqpago_installment_up_to').val() - 1)) {
					console.log( index );
					jQuery(this).show();
				}
			});
		}
	});
});