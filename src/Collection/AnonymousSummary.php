<?php

namespace minervis\ToGo\Collection;

use ActiveRecord;
use arConnector;
use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class AnonymousSummary
 *
 * @package minervis\ToGo\Collection
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
class AnonymousSummary extends ActiveRecord
{
    //use DICTrait;
    use ToGoTrait;
    const TABLE_NAME = "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_sum";
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
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      8
     * @con_is_notnull  true
     * @con_is_primary   true
     */
    protected $obj_id;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      2
     * @con_is_notnull  true
     */
    protected $tot_ratings;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      2
     * @con_is_notnull  true
     */
    protected $tot_views;


    /**
     * AnonymousSummary constructor
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
            case "tot_views":
            case "tot_ratings":
                return intval($field_value);

            default:
                return null;
        }
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
    public function getTotRatings() : int
    {
        return $this->tot_ratings;
    }

    /**
     * @param int $rating
     */
    public function setTotRatings(int $tot_ratings)/*: void*/
    {
        $this->tot_ratings = $tot_ratings;
    }
    /**
     * @return int
     */
    public function getTotViews() : int
    {
        return $this->tot_views;
    }

    /**
     * @param int $view
     */
    public function setTotViews(int $views)/*: void*/
    {
        $this->tot_views = $views;
    }

}