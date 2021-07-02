<?php

namespace srag\DataTableUI\ToGo\Implementation\Column\Formatter;

use srag\DataTableUI\ToGo\Component\Column\Formatter\Formatter;
use srag\DataTableUI\ToGo\Implementation\Utils\DataTableUITrait;
use srag\DIC\ToGo\DICTrait;

/**
 * Class AbstractFormatter
 *
 * @package srag\DataTableUI\ToGo\Implementation\Column\Formatter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFormatter implements Formatter
{

    use DICTrait;
    use DataTableUITrait;

    /**
     * AbstractFormatter constructor
     */
    public function __construct()
    {

    }
}
