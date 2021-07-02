<?php

namespace srag\Plugins\ToGo\ColorThiefCache;

use ActiveRecord;
use arConnector;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class ColorThiefCache
 *
 * @package srag\Plugins\ToGo\ColorThiefCache
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ColorThiefCache extends ActiveRecord
{
    use DICTrait;
    use SrTileTrait;
    const TABLE_NAME = "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_c_t_c";
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return static::TABLE_NAME;
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       255
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $image_path = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $color = "";


    /**
     * ColorThiefCache constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/
        $primary_key_value = 0,
        arConnector $connector = null
    ) {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            default:
                return null;
        }
    }


    /**
     * @return string
     */
    public function getImagePath() : string
    {
        return $this->image_path;
    }


    /**
     * @param string $image_path
     */
    public function setImagePath(string $image_path)/*: void*/
    {
        $this->image_path = $image_path;
    }


    /**
     * @return string
     */
    public function getColor() : string
    {
        return $this->color;
    }


    /**
     * @param string $color
     */
    public function setColor(string $color)/*: void*/
    {
        $this->color = $color;
    }
}
