<script type="text/javascript">
jQuery(function () {
	jQuery('#refund_amount').val("<?php echo esc_attr( number_format($response['amount'], 2, ',', '') ) ?>").change();
	jQuery('.wc_input_price').after( "<span><bdi><?php echo esc_attr( get_woocommerce_currency_symbol() ) ?> <?php echo esc_attr( number_format($response['amount'], 2, ',', '') ) ?></bdi></span>" );
	jQuery('.button.button-primary.do-manual-refund').remove();
	jQuery('#refund_amount').hide();
});
</script>

<?php if($type == 'multi_credit'): ?>
	<?php if($status != 'ORDER_NOT_PAID'): ?>
		<?php if($status == 'ORDER_PARTIAL_PAID'): ?>
			<h2 class="woocommerce-order-details__title"><?php echo __( 'Pagamento não concluído.', 'woocommerce' ) ?></h2>
			<h4><?php echo __( 'Em caso de não pagamento total do pedido o valor pago será estornado.', 'woocommerce' ) ?></h4>
		<?php else: ?>
			<h2 class="woocommerce-order-details__title"><?php echo esc_html( $title ) ?></h2>
		<?php endif; ?>
	<?php else: ?>
		<h2 class="woocommerce-order-details__title"><?php echo esc_html( $title ) ?></h2>
	<?php endif; ?>
<?php else: ?>
	<h2 class="woocommerce-order-details__title"><?php echo esc_html( $title ) ?></h2>
<?php endif; ?>

<div class="process-payment">
	<div class="process-text">
		PAGAMENTO PROCESSADO PELA EMPRESA
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
<br/> 