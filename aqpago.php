<?php
/**
 * Plugin Name: AQPago - Pagamento para WooCommerce
 * Plugin URI: https://github.com/aqbank/aqpago-woocommerce
 * Description: Nossa solução de pagamento é a mais completa do Brasil, com a menor tarifa nas vendas por cartão de crédito, pix e boleto, e podendo contar com nosso MULTIPAGAMENTO, seu cliente pode efetuar o pagamento por dois meios, trazendo vantagens e praticidade, facilitando sua venda, nossas operações financeiras faz tudo isso acontecer, e o seu recebimento é com 1 dia útil está na conta.
 * Version: 1.3.27
 * Author: AQPago
 * Author URI: https://www.aqpago.com.br
 * Text Domain: aqpago
 * Domain Path: /i18n/languages/
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * WC requires at least: 3.0.0
 * WC tested up to:      6.1.1
 *
 * AQPago - Payment for WooCommerce is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with AQPago - Payment for WooCommerce. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
 *
 * @package AQPago
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants.
define( 'WC_AQPAGO_VERSION', '1.0.1' );
define( 'WC_AQPAGO_PLUGIN_FILE', __FILE__ );
define( 'WC_AQPAGO_PLUGIN_DIR', __DIR__ );

if ( ! class_exists( 'WC_Aqpago' ) ) {
	include_once plugin_dir_path(__FILE__) . 'includes/Aqpago.php';
	add_action( 'plugins_loaded', array( 'WC_Aqpago', 'init' ) );
}
