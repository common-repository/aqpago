<?php
/** load SDK without autload function **/

require_once( 'SellerAqpago.php' );
require_once( 'Environment.php' );
require_once( 'AqpagoEnvironment.php' );
require_once( 'Aqpago/Request/Environment.php' );
require_once( 'Aqpago/Request/AqpagoEnvironment.php' );
require_once( 'Aqpago/Request/AbstractRequest.php' );
require_once( 'Aqpago/Request/Exceptions/AqpagoRequestException.php' );
require_once( 'Aqpago/Request/Exceptions/AqpagoError.php' );
require_once( 'Aqpago/Request/Order/UpdateOrderRequest.php' );
require_once( 'Aqpago/Request/Order/QueryOrderRequest.php' );
require_once( 'Aqpago/Request/Order/CreateOrderRequest.php' );
require_once( 'Aqpago/Request/Order/CancelOrderRequest.php' );
require_once( 'Aqpago/Request/AuthInfos/PublicToken.php' );
require_once( 'Aqpago/Request/Webhook/QueryWebhooksRequest.php' );
require_once( 'Aqpago/Request/Webhook/CreateWebhookRequest.php' );
require_once( 'Aqpago/AqpagoSerializable.php' );
require_once( 'Aqpago/UpdateOrder.php' );
require_once( 'Aqpago/Shipping.php' );
require_once( 'Aqpago/Phones.php' );
require_once( 'Aqpago/Payment.php' );
require_once( 'Aqpago/Order.php' );
require_once( 'Aqpago/Items.php' );
require_once( 'Aqpago/Customer.php' );
require_once( 'Aqpago/CreditCard.php' );
require_once( 'Aqpago/Aqpago.php' );
require_once( 'Aqpago/Address.php' );
require_once( 'Aqpago/Webhook.php' );