<?php

namespace srag\Plugins\ToGo\ObjectLink;

use ActiveRecord;
use arConnector;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Group
 *
 * @package srag\Plugins\ToGo\ObjectLink
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Group extends ActiveRecord
{
    use DICTrait;
    use SrTileTrait;
    const TABLE_NAME = "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_oblngr";
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
    protected $group_id;


    /**
     * Group constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @return int
     */
    public function getGroupId() : int
    {
        return $this->group_id;
    }


    /**
     * @param int $group_id
     */
    public function setGroupId(int $group_id)/*:void*/
    {
        $this->group_id = $group_id;
    }
}
