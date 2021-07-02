<?php

namespace srag\Plugins\ToGo\Tile\Renderer\Desktop;

use ilObjUser;
use srag\Plugins\ToGo\Tile\Renderer\AbstractCollectionGUI;

/**
 * Class DesktopCollectionGUI
 *
 * @package srag\Plugins\ToGo\Tile\Renderer\Desktop
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
class DesktopCollectionGUI extends AbstractCollectionGUI
{

    /**
     * @inheritDoc
     */
    public function __construct(ilObjUser $user)
    {
        parent::__construct($user);
    }
}
