<?php

namespace srag\Plugins\ToGo\Access;

use ilConditionHandler;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Courses
 *
 * @package srag\Plugins\ToGo\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Courses
{
    use DICTrait;
    use SrTileTrait;
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
     * Courses constructor
     */
    private function __construct()
    {
    }


    /**
     * @param int $obj_ref_id
     *
     * @return array
     */
    public function getPreconditions(int $obj_ref_id) : array
    {
        return array_map(function (array $precondition) : int {
            return intval($precondition["trigger_ref_id"]);
        }, self::version()->is54()
            ? ilConditionHandler::_getPersistedConditionsOfTarget($obj_ref_id, self::dic()->objDataCache()
                ->lookupObjId($obj_ref_id))
            : ilConditionHandler::_getConditionsOfTarget($obj_ref_id, self::dic()->objDataCache()
                ->lookupObjId($obj_ref_id)));
    }
}
