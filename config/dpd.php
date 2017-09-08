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
];