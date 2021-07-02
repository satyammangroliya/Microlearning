<?php

namespace srag\Plugins\ToGo\Template;

use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class TemplatesConfigGUI
 *
 * @package           srag\Plugins\ToGo\Template
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\ToGo\Template\TemplatesConfigGUI: ilToGoConfigGUI
 */
class TemplatesConfigGUI
{
    use DICTrait;
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_LIST_TEMPLATES = "listTemplates";
    const LANG_MODULE = "template";
    const TAB_LIST_TEMPLATES = "list_templates";


    /**
     * TemplatesConfigGUI constructor
     */
    public function __construct()
    {
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(TemplateConfigGUI::class):
                self::dic()->ctrl()->forwardCommand(new TemplateConfigGUI());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_LIST_TEMPLATES:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    public static function addTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_LIST_TEMPLATES, self::plugin()->translate("templates", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_LIST_TEMPLATES));
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
    }


    /**
     *
     */
    protected function listTemplates()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_LIST_TEMPLATES);

        $table = self::srTile()->templates()->factory()->newTableInstance($this);

        self::output()->output($table);
    }
}
