<?php

namespace srag\Plugins\ToGo\Collection;

use ActiveRecord;
use arConnector;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Collection
 *
 * @package srag\Plugins\ToGo\Collection
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Collection extends ActiveRecord
{
    use DICTrait;
    use SrTileTrait;
    const TABLE_NAME = "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_coll";
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;

    const SORT_BY_BRANCH=1;
    const SORT_BY_TOPIC=2;
    const SORT_DEFAULT=0;


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
     * @con_is_notnull   true
     * @con_length        256
     * @con_is_primary   true
     */
    protected $collection_id;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     */
    protected $obj_id;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     */
    protected $user_id;

    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_is_notnull  true
     */
    protected $sort_criterion = self::SORT_DEFAULT;


    /**
     * Collection constructor
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
            case "obj_id":
            case "sort_criterion":
            case "user_id":
                return intval($field_value);
            case "collection_id":
                return $field_value;

            default:
                return null;
        }
    }


    /**
     * @return string
     */
    public function getCollectionId() : string
    {
        return $this->collection_id;
    }


    /**
     */
    public function setCollectionId()/*: void*/
    {
        $this->collection_id = "". $this->getObjId() . $this->getUserId();
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id)/*: void*/
    {
        $this->obj_id = $obj_id;
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
     * @return int
     */
    public function getSortCriterion() : int
    {
        return $this->sort_criterion;
    }

    /**
     * @param int $sort_criterion
     */
    public function setSortCriterion(int $sort_criterion)/*: void*/
    {
        $this->sort_criterion = $sort_criterion;
    }
}
