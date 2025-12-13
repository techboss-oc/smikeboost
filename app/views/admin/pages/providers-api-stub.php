<?php
// API stubs / pseudo-endpoints for provider integration (to wire with real HTTP requests)

function provider_create_order($provider, $payload) {
    // TODO: implement HTTP POST to provider create_order_endpoint
    return [
        'status' => 'success',
        'provider_order_id' => 'prov_' . rand(10000, 99999)
    ];
}

function provider_check_status($provider, $provider_order_id) {
    // TODO: implement HTTP GET/POST to provider order_status_endpoint
    return [
        'status' => 'processing',
        'remains' => rand(0, 1000)
    ];
}

function provider_import_services($provider) {
    // TODO: implement HTTP GET to services_import_endpoint
    return [
        ['id' => 2001, 'platform' => 'instagram', 'category' => 'followers', 'name' => 'IG Followers HQ', 'rate' => 2.5, 'min' => 10, 'max' => 50000],
        ['id' => 2002, 'platform' => 'tiktok', 'category' => 'views', 'name' => 'TikTok Views Fast', 'rate' => 0.8, 'min' => 100, 'max' => 500000],
    ];
}

function provider_balance($provider) {
    // TODO: implement HTTP GET to balance_check_endpoint
    return [
        'balance' => 1200.50,
        'currency' => 'USD'
    ];
}
