<?php

namespace Pugofka\Dpd;

class Dpd
{
    protected $number;
    protected $key;
    protected $testMode;

    public function __construct()
    {
        $this->number = config('dpd.number');
        $this->key = config('dpd.key');
        $this->testMode = config('dpd.testMode');

    }

    public function hello()
    {
        return 'hello';
    }
}