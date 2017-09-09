<?php

namespace Pugofka\Dpd;

class Dpd
{
    protected $client;

    public function __construct()
    {
        $this->client = new DpdClient();
    }

    public function hello()
    {
        return 'hello';
    }
}