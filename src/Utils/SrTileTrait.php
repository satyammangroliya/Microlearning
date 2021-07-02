<?php

namespace srag\Plugins\ToGo\Utils;

use srag\Plugins\ToGo\Repository;

/**
 * Trait SrTileTrait
 *
 * @package srag\Plugins\ToGo\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait SrTileTrait
{

    /**
     * @return Repository
     */
    protected static function srTile() : Repository
    {
        return Repository::getInstance();
    }
}
