<?php

namespace srag\Plugins\ToGo\Tile\Renderer;

use srag\Plugins\ToGo\ObjectLink\ObjectLink;

/**
 * Interface SingleGUIInterface
 *
 * @package srag\Plugins\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
interface SingleGUIInterface
{

    /**
     * @return string
     */
    public function render() : string;
}
