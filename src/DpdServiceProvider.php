<?php

namespace Pugofka\Pdp;

use Illuminate\Support\ServiceProvider;


class DpdServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('pugofka-dpd', function() {
            return new Dpd;
        });
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';
    }


}