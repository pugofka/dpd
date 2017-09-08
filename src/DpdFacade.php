<?php
/**
 * Created by PhpStorm.
 * User: yushkevichv
 * Date: 08.09.17
 * Time: 1:19
 */

namespace Pugofka\Pdp;

use Illuminate\Support\Facades\Facade;


class DpdFacade extends Facade
{

    protected static function getFacadeAccessor() {
        return 'pugofka-dpd';
    }
}