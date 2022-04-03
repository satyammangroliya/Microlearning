<?php

namespace minervis\ToGo;

use ilObject;
use ilObjUser;
use ilToGoPlugin;
use ilUtil;
use ilToGoConfig;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Access\Access;
use minervis\ToGo\Rating\Repository as RatingsRepository;
use minervis\ToGo\Tile\Repository as TilesRepository;
use minervis\ToGo\Utils\ToGoTrait;
use minervis\ToGo\Collection\Repository as CollectionsRepository;

/**
 * Class Repository
 *
 * @package minervis\ToGo
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
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
     * Repository constructor
     */
    private function __construct()
    {
    }


    /**
     * @return Access
     */
    public function access() : Access
    {
        return Access::getInstance();
    }


    /**
     * 
     */
    public function config() 
    {
        return ilToGoConfig::getInstance();
    }


    /**
     *
     */
    public function dropTables()/*:void*/
    {
        ilUtil::delDir(ILIAS_WEB_DIR . "/" . CLIENT_ID . "/" . ilToGoPlugin::WEB_DATA_FOLDER);
        $this->config()->dropTables();
        $this->ratings(self::ildic()->user())->dropTables();
        $this->tiles()->dropTables();
        $this->collections(self::ildic()->user())->dropTables();
    }

    public function createSessSequence()
    {
        
    }



    /**
     *
     */
    public function installTables()/*:void*/
    {
        ilToGoConfig::installTables();
        $this->ratings(self::ildic()->user())->installTables();
        $this->tiles()->installTables();
        $this->collections(self::ildic()->user())->installTables();
    }




    /**
     * @param ilObjUser $user
     *
     * @return RatingsRepository
     */
    public function ratings(ilObjUser $user) : RatingsRepository
    {
        return RatingsRepository::getInstance($user);
    }



    /**
     * @return TilesRepository
     */
    public function tiles() : TilesRepository
    {
        return TilesRepository::getInstance();
    }

    /**
     * @return CollectionsRepository
     */
    public function collections(ilObjUser $user) : CollectionsRepository
    {
        return CollectionsRepository::getInstance($user);
    }
}
