<?php

namespace srag\DataTableUI\ToGo\Implementation\Utils;

use srag\DataTableUI\ToGo\Component\Factory as FactoryInterface;
use srag\DataTableUI\ToGo\Implementation\Factory;

/**
 * Trait DataTableUITrait
 *
 * @package srag\DataTableUI\ToGo\Implementation\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DataTableUITrait
{

    /**
     * @return FactoryInterface
     */
    protected static function dataTableUI() : FactoryInterface
    {
        return Factory::getInstance();
    }
}
