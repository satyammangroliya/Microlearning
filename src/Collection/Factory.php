<?php

namespace minervis\ToGo\Collection;

use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class Factory
 *
 * @package minervis\ToGo\Collection
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
final class Factory
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
     * Factory constructor
     */
    private function __construct()
    {
    }


    /**
     * @return Collection
     */
    public function newInstance() : Collection
    {
        $collection = new Collection();

        return $collection;
    }

    /**
     * @return AnonymousSession
     */
    public function newAnonymousSessionInstance() : AnonymousSession
    {
        $anonymousSession = new AnonymousSession();

        return $anonymousSession;
    }
}
