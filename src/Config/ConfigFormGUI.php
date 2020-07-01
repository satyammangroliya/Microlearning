<?php

namespace srag\Plugins\SrTile\Config;

use ilCheckboxInputGUI;
use ilSrTileConfigGUI;
use ilSrTilePlugin;
use srag\CustomInputGUIs\SrTile\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\SrTile\Utils\SrTileTrait;

use ilRepositorySearchGUI;
use ilTextInputGUI;
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
    const PLUGIN_CLASS_NAME = ilSrTilePlugin::class;
    const KEY_ENABLED_OBJECT_LINKS = "enabled_object_links";
    const KEY_ENABLED_OBJECT_LINKS_ONCE_SELECT = "enabled_object_links_once_select";
    const KEY_ENABLED_ON_FAVORITES = "enabled_on_favorites";
    const KEY_ENABLED_ON_REPOSITORY = "enabled_on_repository";
    const LANG_MODULE = ilSrTileConfigGUI::LANG_MODULE;
    const KEY_BASE_CONTAINER="base_container";


    /**
     * ConfigFormGUI constructor
     *
     * @param ilSrTileConfigGUI $parent
     */
    public function __construct(ilSrTileConfigGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return self::srTile()->config()->getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilSrTileConfigGUI::CMD_UPDATE_CONFIGURE, $this->txt("save"));
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
            self::KEY_ENABLED_ON_FAVORITES  => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            self::KEY_ENABLED_OBJECT_LINKS  => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_ENABLED_OBJECT_LINKS_ONCE_SELECT => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ]
                ]
            ]
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
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            default:
                self::srTile()->config()->setValue($key, $value);
                break;
        }
    }
    public function getHomeRefId(){
        $query_params="";
        $url = $this->getValue(KEY_BASE_CONTAINER);
        $query_str = parse_url($url, PHP_URL_QUERY);
        parse_str($query_str, $query_params);
        $ref_id=query_params['ref_id'];
        return ref_id;
       
    }
}
