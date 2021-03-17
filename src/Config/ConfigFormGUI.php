<?php

namespace srag\Plugins\SrTile\Config;

use ilCheckboxInputGUI;
use ilToGoConfigGUI;
use ilToGoPlugin;
use srag\CustomInputGUIs\SrTile\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrTile\Utils\SrTileTrait;
use srag\Plugins\SrTile\Config\Repository as Conf;

use ilRepositorySearchGUI;
use ilTextInputGUI;

use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\FileUpload\Location;
use ilImageFileInputGUI;

/**
 * Class ConfigFormGUI
 *
 * @package srag\Plugins\SrTile\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ConfigFormGUI extends PropertyFormGUI
{
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const KEY_ENABLED_OBJECT_LINKS = "enabled_object_links";
    const KEY_ENABLED_OBJECT_LINKS_ONCE_SELECT = "enabled_object_links_once_select";
    const KEY_ENABLED_ON_FAVORITES = "enabled_on_favorites";
    const KEY_ENABLED_ON_REPOSITORY = "enabled_on_repository";
    const LANG_MODULE = ilToGoConfigGUI::LANG_MODULE;
    const KEY_BASE_CONTAINER="base_container";
    const KEY_UMFRAGE_OBJECT="umfrage_object";
    const KEY_WAS_SIND_LINK="was_sind";
    const BAG_IMAGE="bag_image";
    const BACK_COLOR="back_color";


    /**
     * ConfigFormGUI constructor
     *
     * @param ilToGoConfigGUI $parent
     */
    public function __construct(ilToGoConfigGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
    {
        switch ($key) {
            case self::BAG_IMAGE:
                if (!empty(self::srTile()->config()->getValue(self::BAG_IMAGE))) {
                    return "./" . Conf::getImagePath();
                }
                break;
             
            default:
                return self::srTile()->config()->getValue($key);
        }
    }

    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilToGoConfigGUI::CMD_UPDATE_CONFIGURE, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            self::KEY_BASE_CONTAINER => [
                self::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            self::KEY_ENABLED_ON_REPOSITORY => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            self::KEY_UMFRAGE_OBJECT => [
                self::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            self::KEY_WAS_SIND_LINK => [
                self::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            self::BACK_COLOR =>[
                self::PROPERTY_CLASS    => ilTextInputGUI::class
            ],
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt("configuration"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value)/*: void*/
    {
        switch ($key) {
            case self::BAG_IMAGE:
                if (!self::dic()->upload()->hasBeenProcessed()) {
                    self::dic()->upload()->process();
                }

                /** @var UploadResult $result */
                $result = array_pop(self::dic()->upload()->getResults());

                if ($this->getInput("image_delete") || $result->getSize() > 0) {
                    Conf::applyNewImage("");
                }

                if (intval($result->getSize()) === 0) {
                    //break;
                }

                $file_name = "bag_image." . pathinfo($result->getName(), PATHINFO_EXTENSION);

                self::dic()->upload()->moveOneFileTo($result, Conf::getImagePathAsRelative(false), Location::WEB, $file_name, true);

                self::srTile()->config()->setValue($key, $file_name);
                break;
            default:
                self::srTile()->config()->setValue($key, $value);
                break;
        }
    }

    /**
     * @return string
     */
    public static function getImagePathWithCheck() : string
    {
        if (!empty(self::srTile()->config()->getValue(self::BAG_IMAGE))) {
            if (file_exists($image_path = Conf::getImagePath())) {
                return $image_path;
            }
        }

        return "";
    }
}
