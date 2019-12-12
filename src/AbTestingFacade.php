<?php

namespace Wanderreisen\AbTesting;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wanderreisen\AbTesting\AbTesting
 */
class AbTestingFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ab-testing';
    }
}
