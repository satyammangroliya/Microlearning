<?php

namespace srag\DataTableUI\ToGo\Implementation\Data\Fetcher;

use srag\DataTableUI\ToGo\Component\Data\Fetcher\DataFetcher;
use srag\DataTableUI\ToGo\Component\Table;
use srag\DataTableUI\ToGo\Implementation\Utils\DataTableUITrait;
use srag\DIC\ToGo\DICTrait;

/**
 * Class AbstractDataFetcher
 *
 * @package srag\DataTableUI\ToGo\Implementation\Data\Fetcher
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDataFetcher implements DataFetcher
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * AbstractDataFetcher constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getNoDataText(Table $component) : string
    {
        return $component->getPlugin()->translate("no_data", Table::LANG_MODULE);
    }


    /**
     * @inheritDoc
     */
    public function isFetchDataNeedsFilterFirstSet() : bool
    {
        return false;
    }
}
