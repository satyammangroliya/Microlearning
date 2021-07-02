<?php

namespace srag\Plugins\ToGo\Tile\Renderer;

use srag\Plugins\ToGo\Tile\Tile;

/**
 * Interface CollectionInterface
 *
 * @package srag\Plugins\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
interface CollectionInterface
{

    /**
     * @param Tile $tile
     */
    public function addTile(Tile $tile)/*: void*/ ;


    /**
     * @param int $tile_id
     */
    public function removeTile(int $tile_id)/*: void*/ ;


    /**
     * @return Tile[]
     */
    public function getTiles($order=array("topic","asc")) : array;

    public function setOrder();
}
