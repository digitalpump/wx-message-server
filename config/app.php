<?php
/**
 * Created by PhpStorm.
 * User: jeffrey
 * Date: 2018/3/4
 * Time: 01:15
 */
return [
    'log_channel' => env('APP_LOG_CHANNEL', 'jeff_channel'),
    'refresh_token_key_prefix' => 'jwt_refresh_token_did_',
    'token_ttl' => 60,
    'refresh_token_delay' => 6,
    'refresh_token_ttl' => 43200, //30 days
];