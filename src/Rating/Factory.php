<?php

namespace srag\Plugins\ToGo\Rating;

use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\ToGo\Rating
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
     * @return Rating
     */
    public function newInstance() : Rating
    {
        $rating = new Rating();

        return $rating;
    }
}
