<?php

namespace minervis\ToGo\Access;

use ilObjCourse;
use ilObjCourseAccess;
use ilObjGroup;
use ilObjGroupAccess;
use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class Access
 *
 * @package minervis\ToGo\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class TogoAccess
{
    //use DICTrait;
    use ToGoTrait;
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
     * @var bool[]
     */
    protected static $has_open_access = [];
    /**
     * @var bool[]
     */
    protected static $has_read_access = [];
    /**
     * @var bool[]
     */
    protected static $has_visible_access = [];
    /**
     * @var bool[]
     */
    protected static $has_write_access = [];


    /**
     * Access constructor
     */
    private function __construct()
    {
    }


    /**
     * @param Tile $tile
     *
     * @return bool
     */
    public function hasOpenAccess(Tile $tile) : bool
    {
        if (!isset(self::$has_open_access[$tile->getObjRefId()])) {
            if ($this->hasReadAccess($tile->getObjRefId())) {
                self::$has_open_access[$tile->getObjRefId()] = true;
            } else {
                if ($tile->_getIlObject() instanceof ilObjCourse) {
                    self::$has_open_access[$tile->getObjRefId()] = (new ilObjCourseAccess())->_checkAccess("join", "join", $tile->getObjRefId(), self::togoObjDataCache()->lookupObjId($tile->getObjRefId()));
                } else {
                    if ($tile->_getIlObject() instanceof ilObjGroup) {
                        self::$has_open_access[$tile->getObjRefId()] = (new ilObjGroupAccess())->_checkAccess("join", "join", $tile->getObjRefId(), self::togoObjDataCache()->lookupObjId($tile->getObjRefId()));
                    } else {
                        self::$has_open_access[$tile->getObjRefId()] = false;
                    }
                }
            }
        }

        return self::$has_open_access[$tile->getObjRefId()];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasReadAccess(int $obj_ref_id) : bool
    {
        if (!isset(self::$has_read_access[$obj_ref_id])) {
            self::$has_read_access[$obj_ref_id] = self::ildic()->access()->checkAccess("read", "", $obj_ref_id);
        }

        return self::$has_read_access[$obj_ref_id];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasVisibleAccess(int $obj_ref_id) : bool
    {
        if (!isset(self::$has_visible_access[$obj_ref_id])) {
            self::$has_visible_access[$obj_ref_id] = self::ildic()->access()->checkAccess("visible", "", $obj_ref_id);
        }

        return self::$has_visible_access[$obj_ref_id];
    }


    /**
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasWriteAccess(int $obj_ref_id) : bool
    {
        if (!isset(self::$has_write_access[$obj_ref_id])) {
            self::$has_write_access[$obj_ref_id] = self::ildic()->access()->checkAccess("write", "", $obj_ref_id);
        }

        return self::$has_write_access[$obj_ref_id];
    }
}
