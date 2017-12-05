<?php

return [
    /*
     * client number from DPD
     */
    'number' => env('DPD_ID'),

    /*
     * integration api key. You can get it from you account
     */
    'key' => env('DPD_KEY'),

    /*
     * test mode. Default enabled
     */
    'test_mode' => env('DPD_TEST_MODE'),

    /*
     * The amount of minutes the DPD API responses with get big data will be cached.
     * If you set this to zero, the responses won't be cached at all.
     */
    'cache_lifetime_in_minutes' => 60 * 8,

    /*
     * Here you may configure the "store" that the underlying DPD_Client will
     * use to store it's data.
     *
     */
    'cache' => [
        'store' => 'file',
    ],
];