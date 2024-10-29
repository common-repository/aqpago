<script defer="defer" src="https://cdn.aqbank.com.br/js/aqpago.min.js"></script>
<script>
jQuery(document).ready(function(){
    window.AQPAGOSECTION.setPublicToken('<?php echo esc_js($public_token) ?>');
});
</script>
