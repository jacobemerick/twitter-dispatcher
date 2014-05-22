<?php

//require_once __DIR__ . '/vendor/autoload.php';

$config = file_get_contents('config.json');
$config = json_decode($config);

$time = time();

$oauth_hash_array = [
    'count'                   => '100',
    'include_entities'        => 'true',
    'oauth_consumer_key'      => $config->api_key,
    'oauth_nonce'             => $time,
    'oauth_signature_method'  => 'HMAC-SHA1',
    'oauth_timestamp'         => $time,
    'oauth_token'             => $config->access_token,
    'oauth_version'           => '1.0',
];
$oauth_hash = http_build_query($oauth_hash_array);

$base_array = [
    'GET',
    'https://api.twitter.com/1.1/statuses/mentions_timeline.json',
    $oauth_hash,
];
$base_array = array_map('rawurlencode', $base_array);
$base = implode('&', $base_array);

$key_array = [
    $config->api_secret,
    $config->access_token_secret,
];
$key_array = array_map('rawurlencode', $key_array);
$key = implode('&', $key_array);

$signature = base64_encode(hash_hmac('sha1', $base, $key, true));
$signature = rawurlencode($signature);

$oauth_header_array = [
    'count'                   => '100',
    'include_entities'        => 'true',
    'oauth_consumer_key'      => $config->api_key,
    'oauth_nonce'             => $time,
    'oauth_signature'         => $signature,
    'oauth_signature_method'  => 'HMAC-SHA1',
    'oauth_timestamp'         => $time,
    'oauth_token'             => $config->access_token,
    'oauth_version'           => '1.0',
];
$oauth_header = '';
foreach ($oauth_header_array as $oauth_header_key => $oauth_header_value) {
    $oauth_header .= "{$oauth_header_key}=\"{$oauth_header_value}\", ";
}

$curl_handle = curl_init();
curl_setopt($curl_handle, CURLOPT_HTTPHEADER, ["Authorization: Oauth {$oauth_header}", 'Expect:']);
curl_setopt($curl_handle, CURLOPT_HEADER, false);
curl_setopt($curl_handle, CURLOPT_URL, 'https://api.twitter.com/1.1/statuses/mentions_timeline.json?count=100&include_entities=true');
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
$json = curl_exec($curl_handle);
curl_close($curl_handle);

var_dump($json);
exit;
