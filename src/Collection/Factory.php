<?php

namespace srag\Plugins\ToGo\Collection;

use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\ToGo\Collection
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
final class Factory
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
