<?php

namespace srag\DataTableUI\ToGo\Implementation\Settings;

use ILIAS\UI\Component\ViewControl\Pagination;
use srag\DataTableUI\ToGo\Component\Settings\Factory as FactoryInterface;
use srag\DataTableUI\ToGo\Component\Settings\Settings as SettingsInterface;
use srag\DataTableUI\ToGo\Component\Settings\Sort\Factory as SortFactoryInterface;
use srag\DataTableUI\ToGo\Component\Settings\Storage\Factory as StorageFactoryInterface;
use srag\DataTableUI\ToGo\Implementation\Settings\Sort\Factory as SortFactory;
use srag\DataTableUI\ToGo\Implementation\Settings\Storage\Factory as StorageFactory;
use srag\DataTableUI\ToGo\Implementation\Utils\DataTableUITrait;
use srag\DIC\ToGo\DICTrait;

/**
 * Class Factory
 *
 * @package srag\DataTableUI\ToGo\Implementation\Settings
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
    public function settings(Pagination $pagination) : SettingsInterface
    {
        return new Settings($pagination);
    }


    /**
     * @inheritDoc
     */
    public function sort() : SortFactoryInterface
    {
        return SortFactory::getInstance();
    }


    /**
     * @inheritDoc
     */
    public function storage() : StorageFactoryInterface
    {
        return StorageFactory::getInstance();
    }
}
