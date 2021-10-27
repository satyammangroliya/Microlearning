<?php

namespace minervis\ToGo\Tile\Renderer;

use ilObjUser;
use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Tile\Renderer\Container\ContainerCollectionGUI;
use minervis\ToGo\Tile\Renderer\Desktop\DesktopCollectionGUI;
use minervis\ToGo\Tile\Renderer\Fixed\FixedCollectionGUI;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class CollectionGUIFactory
 *
 * @package minervis\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class CollectionGUIFactory
{
    //use DICTrait;
    use ToGoTrait;
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
     * CollectionGUIFactory constructor
     */
    private function __construct()
    {
    }


    /**
     * @param string $html
     *
     * @return ContainerCollectionGUI
     */
    public function container(string $html) : ContainerCollectionGUI
    {
        $collection_gui = new ContainerCollectionGUI($html);

        return $collection_gui;
    }


    /**
     * @param ilObjUser $user
     *
     * @return DesktopCollectionGUI
     */
    public function desktop(ilObjUser $user) : DesktopCollectionGUI
    {
        $collection_gui = new DesktopCollectionGUI($user);

        return $collection_gui;
    }


    /**
     * @param array $obj_ref_ids
     *
     * @return FixedCollectionGUI
     */
    public function fixed(array $obj_ref_ids) : FixedCollectionGUI
    {
        $collection_gui = new FixedCollectionGUI($obj_ref_ids);

        return $collection_gui;
    }
}
