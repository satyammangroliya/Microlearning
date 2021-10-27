<?php

namespace minervis\ToGo\Tile\Renderer\Container;

use minervis\ToGo\Tile\Renderer\AbstractCollectionGUI;

/**
 * Class ContainerCollectionGUI
 *
 * @package minervis\ToGo\Tile\Renderer\Container
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
class ContainerCollectionGUI extends AbstractCollectionGUI
{

    /**
     * @inheritDoc
     */
    public function __construct(string $html)
    {
        parent::__construct($html);
    }
}
