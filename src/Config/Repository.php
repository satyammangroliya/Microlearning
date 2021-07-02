<?php

namespace srag\Plugins\ToGo\Config;

use ilToGoPlugin;
use srag\ActiveRecordConfig\ToGo\Config\AbstractFactory;
use srag\ActiveRecordConfig\ToGo\Config\AbstractRepository;
use srag\ActiveRecordConfig\ToGo\Config\Config;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\ToGo\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository extends AbstractRepository
{
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
     * Repository constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     *
     * @return Factory
     */
    public function factory() : AbstractFactory
    {
        return Factory::getInstance();
    }


    /**
     * @inheritDoc
     */
    protected function getTableName() : string
    {
        return "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_config";
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            ConfigFormGUI::KEY_BASE_CONTAINER                   => [Config::TYPE_STRING, ""],
            ConfigFormGUI::KEY_UMFRAGE_OBJECT                   => [Config::TYPE_STRING, ""],
            ConfigFormGUI::KEY_WAS_SIND_LINK                   => [Config::TYPE_STRING, ""],
            ConfigFormGUI::KEY_ENABLED_ON_FAVORITES             => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_ENABLED_ON_REPOSITORY            => [Config::TYPE_BOOLEAN, true],
            ConfigFormGUI::KEY_ENABLED_OBJECT_LINKS             => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::KEY_ENABLED_OBJECT_LINKS_ONCE_SELECT => [Config::TYPE_BOOLEAN, false],
            ConfigFormGUI::BAG_IMAGE                   => [Config::TYPE_STRING, ""],
            ConfigFormGUI::BACK_COLOR                   => [Config::TYPE_STRING, ""],
        ];
    }
   
    public function getUmfrageObjRefId()
    {
        $obj_ref_id=self::srTile()->config()->getValue(ConfigFormGUI::KEY_UMFRAGE_OBJECT);
        return $obj_ref_id;
    }
    public function getWasSindObjRefId()
    {
        $obj_ref_id=self::srTile()->config()->getValue(ConfigFormGUI::KEY_WAS_SIND_LINK);
        return $obj_ref_id;
    }


    public function getHomeRefId()
    {
        $obj_ref_id=self::srTile()->config()->getValue(ConfigFormGUI::KEY_BASE_CONTAINER);
        return $obj_ref_id;
    }

    public static function getImagePath():string
    {
        return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/" . self::getImagePathAsRelative();
    }

    /**
     * @param bool $append_filename
     *
     * @return string
     */
    public static function getImagePathAsRelative(bool $append_filename = true) : string
    {
        $image_prefix="bag_image";
        $path = ilToGoPlugin::WEB_DATA_FOLDER . "/" .$image_prefix . "/";

        if ($append_filename) {
            $path .= self::srTile()->config()->getValue(ConfigFormGUI::BAG_IMAGE);
        }

        return $path;
    }
    /**
     * @param string $path_of_new_image
     */
    public static function applyNewImage(string $path_of_new_image)/*: void*/
    {
        if (!empty(self::srTile()->config()->getValue(ConfigFormGUI::BAG_IMAGE))) {
            if (file_exists($image_old_path = self::getImagePath())) {
                unlink($image_old_path);
            }
            self::srTile()->config()->setValue(ConfigFormGUI::BAG_IMAGE, "");

            self::srTile()->colorThiefCaches()->delete($image_old_path);
        }

        if (!empty($path_of_new_image)) {
            if (file_exists($path_of_new_image)) {
                self::srTile()->config()->setValue(ConfigFormGUI::BAG_IMAGE, "".pathinfo($path_of_new_image, PATHINFO_EXTENSION));

                self::dic()->filesystem()->web()->createDir(self::getImagePathAsRelative(false));

                copy($path_of_new_image, self::getImagePath());
            }
        }
    }
}
