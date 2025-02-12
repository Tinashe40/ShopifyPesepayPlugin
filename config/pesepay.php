<?php
return [ 'callback_url'=> env('CALLBACK_URL', 'https://plugins.pesepay.com/api/webhook'),
'return_url' => env('RETURN_URL', 'https://your-shopify-store.com/checkout'),
];