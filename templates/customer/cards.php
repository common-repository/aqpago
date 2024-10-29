<script type="text/javascript">
var flagVisa = '<img class="visa-flag" style="width:80%;" src="<?php echo esc_url( $flagVisa ) ?>" />';
</script>
<div class="form-card-edit">
	<div class="box-select-card box-select-card-custom">
		<?php if(is_array($cards) && count($cards)): ?>
			<?php foreach ($cards as $card): ?>
				<div id="card-box-<?php echo esc_attr( $card['id'] ) ?>"  class="box-select-card-li box-select-card-two two-li-form-payment">
					<div class="box-select-card-float box-select-card-li-flag <?php echo esc_attr( $card['id'] ) ?>">
					</div>
					<div class="box-select-card-float box-select-card-li-number">
						<?php echo esc_html( $card['first4_digits'] ) . ' **** **** **** ' . esc_html( $card['last4_digits'] )  ?>
						<span id="card-<?php echo esc_attr( $card['id'] ) ?>" class="remove-card">x Remover</span>

						<div id="modal-card-remove-<?php echo esc_attr( $card['id'] ) ?>" class="modal-card-remove">							
							<h3><b><?php echo __('Você tem certeza que gostaria, de') ?></b> <strong><?php echo __('Remover') ?></strong> <b><?php echo __('o cartão ?') ?></b></h3>
							
							<div class="box-li-card-new">
								<div class="box-li-card">
									<div class="box-li-card-new-flag  <?php echo esc_attr( $card['id'] ) ?>"></div>
									<div class="box-li-card-new-number">
										<span><?php echo esc_html( $card['first4_digits'] ) . ' **** **** **** ' . esc_html( $card['last4_digits'] )  ?></span>
									</div>
								</div>
							</div>
							
							<div class="box-li-buttons">
								<button id="remove-<?php echo esc_attr( $card['id'] ) ?>" class="remove-button">
									<span><?php echo __('REMOVER') ?></span>
								</button>
								<button class="back-button">
									<span><?php echo __('VOLTAR') ?></span>
								</button>
							</div>
							
						</div>
					</div>
					<div class="box-select-card-float box-select-card-li-arrow">
						<svg xmlns="http://www.w3.org/2000/svg" width="8" height="15.991" viewBox="0 0 8 15.991">
						  <path id="arrow-right" d="M12.5,6l8,8-8,8,0-2,6-6-6-6Z" transform="translate(-12.5 -6)" fill="#b7b7b7" fill-rule="evenodd"/>
						</svg>
					</div>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						$(".<?php echo esc_attr( $card['id'] ) ?>").html( AQPAGO.getFlagSvg("<?php echo esc_attr(strtolower($card['flag'])) ?>"), true );
					});
				</script>
			<?php endforeach; ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.remove-card').click(function(){
		var cardId = $(this).attr('id').replace('card-', '');
		
		$('.modal-card-remove').fadeOut();
		$('#modal-card-remove-'+cardId).fadeIn('100');
	});
	
	$('.back-button').click(function(){
		$('.modal-card-remove').fadeOut();
	});
	
	$('.remove-button').click(function(){
		var cardId = $(this).attr('id').replace('remove-', '');
		$('.modal-card-remove').fadeOut();
		
		$.ajax({
			showLoader: true,
			url: js_global.xhr_url,
			data: {
				aqpago_remove_card_nonce: js_global.aqpago_remove_card,
				action: 'aqpago_remove_card',
				cardId: cardId
			},
			type: "POST"
		}).done(function(result) {
			$('.modal-card-remove').fadeOut();
			
			if(result.success){
				$('#card-box-'+cardId).remove();
				toastr.success('Cartão removido com sucesso!','Atenção', {extendedTimeOut: 3000,tapToDismiss:true});	
			}
			
		}).fail(function (xhr, status, error) {
			toastr.error('Falha ao deletar cartão!','Atenção', {extendedTimeOut: 3000,tapToDismiss:true});
		});
	});
});
</script>	
			
		<?php else: ?>
			<div class="message info empty"><span><?php echo __('Sem cartão salvo.'); ?></span></div>
		<?php endif ?>
	</div>
</div>
<style>
.form-card-edit .box-select-card .box-select-card-li .modal-card-remove{
	display:none;
	position:absolute;
	top:-184px;
	left:-98px;
	padding:5px;
	width:340px;
	height:200px;
	z-index:3;
	background:url('<?php echo esc_url( $background_remove ) ?>');
	background-size:cover
}
</style>