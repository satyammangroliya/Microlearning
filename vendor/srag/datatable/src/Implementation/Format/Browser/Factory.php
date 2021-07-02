<?php

namespace srag\DataTableUI\ToGo\Implementation\Format\Browser;

use srag\DataTableUI\ToGo\Component\Format\Browser\BrowserFormat;
use srag\DataTableUI\ToGo\Component\Format\Browser\Factory as FactoryInterface;
use srag\DataTableUI\ToGo\Component\Format\Browser\Filter\Factory as FilterFactoryInterface;
use srag\DataTableUI\ToGo\Implementation\Format\Browser\Filter\Factory as FilterFactory;
use srag\DataTableUI\ToGo\Implementation\Utils\DataTableUITrait;
use srag\DIC\ToGo\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\ToGo\Implementation\Format\Browser
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Factory implements FactoryInterface
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function default() : BrowserFormat
    {
        return new DefaultBrowserFormat();
    }


    /**
     * @inheritDoc
     */
    public function filter() : FilterFactoryInterface
    {
        return FilterFactory::getInstance();
    }
}
