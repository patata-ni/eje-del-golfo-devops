<?php

return [
    'enabled' => (bool) env('DEMO_ACCOUNTS', false),
    'admin_email' => env('DEMO_ADMIN_EMAIL'),
    'admin_password' => env('DEMO_ADMIN_PASSWORD'),
    'mechanic_email' => env('DEMO_MECHANIC_EMAIL'),
    'mechanic_password' => env('DEMO_MECHANIC_PASSWORD'),
    'client_email' => env('DEMO_CLIENT_EMAIL'),
    'client_password' => env('DEMO_CLIENT_PASSWORD'),
];
