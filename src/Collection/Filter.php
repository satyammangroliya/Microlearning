<?php

namespace srag\Plugins\ToGo\Collection;

use ActiveRecord;
use arConnector;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Topic
 *
 * @package srag\Plugins\ToGo\Collection
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
class Filter extends ActiveRecord
{
    use DICTrait;
    use SrTileTrait;
    const TABLE_NAME = "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_filter";
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
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $user_id;

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length        256
     */
    protected $item_name="";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length        256
     */
    protected $item_type="";
    
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $flag;
   

    /**
     * Topic constructor
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
            case "user_id":
            case "item_name":
            case "item_type":
                return $field_value;

            default:
                return null;
        }
    }


    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id)/*: void*/
    {
        $this->user_id = $user_id;
    }


    /**
     * @return string
     */
    public function getItemName() : string
    {
        return $this->item_name;
    }


    /**
     * @param string $item_name
     */
    public function setItemName(string $item_name)/*: void*/
    {
        $this->item_name = $item_name;
    }

    /**
     * @return string
     */
    public function getItemType() : string
    {
        return $this->item_type;
    }


    /**
     * @param string $item_type
     */
    public function setItemType(string $item_type)/*: void*/
    {
        $this->item_type = $item_type;
    }

    /**
     * @return int
     */
    public function getFlag() : int
    {
        return $this->flag;
    }


    /**
     * @param int $flag
     */
    public function setFlag(int $flag)/*: void*/
    {
        $this->flag = $flag;
    }
}
