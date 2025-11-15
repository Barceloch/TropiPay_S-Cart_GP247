<?php
return [
    'name' => 'TropiPay',
    'code' => 'TropiPay',
    'client_id' => env('TROPIPAY_CLIENT_ID', ''),
    'client_secret' => env('TROPIPAY_CLIENT_SECRET', ''),
    'server_mode' => env('TROPIPAY_SERVER_MODE', 'Development'),
    'api_url_development' => 'https://tropipay-dev.herokuapp.com/api/v2',
    'api_url_production' => 'https://www.tropipay.com/api/v2',
    'currency' => 'USD',
];