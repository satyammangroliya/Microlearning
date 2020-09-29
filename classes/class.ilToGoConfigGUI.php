<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\SrTile\DICTrait;
use srag\Notifications4Plugin\SrTile\Notification\NotificationsCtrl;
use srag\Plugins\SrTile\Template\TemplatesConfigGUI;
use srag\Plugins\SrTile\Utils\SrTileTrait;
//use ilPropertyFormGUI;

/**
 * Class ilToGoConfigGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Notifications4Plugin\SrTile\Notification\NotificationsCtrl: ilToGoConfigGUI
 */
class ilToGoConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_CONFIGURE = "configure";
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    const LANG_MODULE = "config";
    const TAB_CONFIGURATION = "configuration";


    /**
     * ilToGoConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/*:void*/
    {
        self::srTile();

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(NotificationsCtrl::class):
                self::dic()->tabs()->activateTab(NotificationsCtrl::TAB_NOTIFICATIONS);
                self::dic()->ctrl()->forwardCommand(new NotificationsCtrl());
                break;

            case strtolower(TemplatesConfigGUI::class):
                self::dic()->ctrl()->forwardCommand(new TemplatesConfigGUI());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
                    case self::CMD_UPDATE_CONFIGURE:
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
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->addTab(self::TAB_CONFIGURATION, self::plugin()->translate("configuration", self::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_CONFIGURE));

        /*
        TemplatesConfigGUI::addTabs();

        self::dic()->tabs()->addTab(NotificationsCtrl::TAB_NOTIFICATIONS, self::plugin()->translate("notifications", NotificationsCtrl::LANG_MODULE), self::dic()->ctrl()
            ->getLinkTargetByClass(NotificationsCtrl::class, NotificationsCtrl::CMD_LIST_NOTIFICATIONS));
        */
        self::dic()->locator()->addItem(ilToGoPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    protected function configure()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = self::srTile()->config()->factory()->newFormInstance($this);

        self::output()->output($form);
    }

    private function selectObj(){
        // link input
        include_once 'Services/Form/classes/class.ilLinkInputGUI.php';
        $ac = new ilLinkInputGUI($this->lng->txt('cont_link'), 'link');
        $ac->setAllowedLinkTypes(ilLinkInputGUI::BOTH);
        $ac->setRequired(false);
        $ac->setInfo($this->lng->txt("copg_sec_link_info"));
        /*$ac->setInternalLinkDefault(
            $this->getPageConfig()->getIntLinkHelpDefaultType(),
            $this->getPageConfig()->getIntLinkHelpDefaultId()
        );*/

    }


    /**
     *
     */
    protected function updateConfigure()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = self::srTile()->config()->factory()->newFormInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("configuration_saved", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this, self::CMD_CONFIGURE);
    }
}