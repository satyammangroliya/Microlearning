<?php

namespace srag\Plugins\ToGo\Collection;

use ActiveRecord;
use arConnector;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class AnonymousSession
 *
 * @package srag\Plugins\ToGo\Collection
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
class AnonymousSession extends ActiveRecord
{
    use DICTrait;
    use SrTileTrait;
    const TABLE_NAME = "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_sess";
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
    protected $row_id;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     * @con_length        256
     */
    protected $sess_id;
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
     * @con_length      2
     * @con_is_notnull  true
     */
    protected $rating;
    /**
     * @var int
     *
     * @con_has_field   true
     * @con_fieldtype   integer
     * @con_length      2
     * @con_is_notnull  true
     */
    protected $view;


    /**
     * AnonymousSession constructor
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
            case "row_id":
            case "obj_id":
            case "views":
            case "rating":
                return intval($field_value);
            case "sess_id":
                return $field_value;

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
     * @param string $sess_id
     */
    public function setSessId(string $sess_id)/*: void*/
    {
        $this->sess_id = $sess_id;
    }

    /**
     * @return string
     */
    public function getSessId() : int
    {
        return $this->sess_id;
    }

    /**
     * @return int
     */
    public function getRating() : int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(int $rating)/*: void*/
    {
        $this->rating = $rating;
    }
    /**
     * @return int
     */
    public function getView() : int
    {
        return $this->view;
    }

    /**
     * @param int $view
     */
    public function setView(int $view)/*: void*/
    {
        $this->view = $view;
    }
    /**
     * @return int
     */
    public function geRowId() : int
    {
        return $this->row_id;
    }

    /**
     * @param int $row_id
     */
    public function setRowId(int $row_id)/*: void*/
    {
        $this->row_id = $row_id;
    }

    public function initializeAnonymSession($sess_id, $obj_id)
    {
        $this->setSessId($sess_id);
        $this->setObjId($obj_id);
        $this->setView(0);
        $this->setRating(0);
        return $this;
    }

}