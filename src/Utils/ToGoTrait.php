<?php

namespace minervis\ToGo\Utils;

use minervis\ToGo\Repository;
use ilObjectDataCache;
use ilToGoPlugin;
use minervis\ToGo\Utils\Plugin;

/**
 * Trait ToGoTrait
 *
 * @package minervis\ToGo\Utils
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
trait ToGoTrait
{

    /**
     * @return Repository
     */
    protected static function togo() : Repository
    {
        return Repository::getInstance();
    }
    /**
     * 
     */
    protected static function ildic()
    {
        global $DIC;
        return $DIC;
    }
    /**
     * 
     */
    protected static function togoObjDataCache()
    {
        global $DIC;
        return $DIC['ilObjDataCache'];
    }
    public static function togoplugin()
    {
        $instance = ilToGoPlugin::getInstance();
        return new Plugin($instance);
    }

}
