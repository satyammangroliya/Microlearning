<?php

namespace minervis\ToGo\Tile\Renderer\Desktop;

use ilObjUser;
use minervis\ToGo\Tile\Renderer\AbstractCollectionGUI;

/**
 * Class DesktopCollectionGUI
 *
 * @package minervis\ToGo\Tile\Renderer\Desktop
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
