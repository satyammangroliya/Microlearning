<?php

namespace srag\Plugins\SrTile\Collection;

use ActiveRecord;
use arConnector;
use ilSrTilePlugin;
use srag\DIC\SrTile\DICTrait;
use srag\Plugins\SrTile\Utils\SrTileTrait;

/**
 * Class Topic
 *
 * @package srag\Plugins\SrTile\Collection
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
class ArBranch extends ActiveRecord
{

    use DICTrait;
    use SrTileTrait;
    const TABLE_NAME = "ui_uihk_" . ilSrTilePlugin::PLUGIN_ID . "_abranch";
    const PLUGIN_CLASS_NAME = ilSrTilePlugin::class;



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
     * @con_sequence     true
     */
    protected $branch_id;

    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $obj_ref_id;
   

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
            case "obj_ref_id":
            case "branch_id":
                return $field_value;

            default:
                return null;
        }
    }


    /**
     * @return string
     */
    public function getBranchId() : string
    {
        return $this->branch_id;
    }


    /**
     * @param int $topic_id
     */
    public function setBranchId(int $branch_id)/*: void*/
    {
        $this->branch_id = $branch_id;
    }


    /**
     * @return int
     */
    public function getRefObjId() : int
    {
        return $this->ref_obj_id;
    }


    /**
     * @param int $branch_id
     */
    public function setObjRefId(int $branch_id)/*: void*/
    {
        $this->branch_id = $branch_id;
    }

}