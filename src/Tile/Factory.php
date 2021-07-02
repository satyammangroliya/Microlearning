<?php

namespace srag\Plugins\ToGo\Tile;

use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Template\TemplatesConfigGUI;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\ToGo\Tile
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{
    use DICTrait;
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
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
     * @return Tile
     */
    public function newInstance() : Tile
    {
        $tile = new Tile();

        return $tile;
    }


    /**
     * @param TileGUI|TemplatesConfigGUI $parent
     * @param Tile                       $tile
     *
     * @return TileFormGUI
     */
    public function newFormInstance(TileGUI $parent, Tile $tile) : TileFormGUI
    {
        $form = new TileFormGUI($parent, $tile);

        return $form;
    }
}
