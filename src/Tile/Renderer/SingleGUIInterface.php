<?php

namespace minervis\ToGo\Tile\Renderer;


/**
 * Interface SingleGUIInterface
 *
 * @package minervis\ToGo\Tile\Renderer
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
