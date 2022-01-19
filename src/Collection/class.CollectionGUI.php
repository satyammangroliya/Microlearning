<?php

namespace minervis\ToGo\Collection;

use ilLink;
use ilDashboardGUI;
use ilToGoPlugin;
use ilToGoUIHookGUI;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class CollectionGUI1
 *
 * @package           minervis\ToGo\Collection
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy minervis\ToGo\Collection\CollectionGUI: ilUIPluginRouterGUI
 */
class CollectionGUI
{
    //use DICTrait;
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_LIKE = "";
    const CMD_UNLIKE = "unlike";
    const GET_PARAM_PARENT_REF_ID = "parent_ref_id";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "collection";

    /**
     * @var int
     */
    protected $parent_ref_id;
    /**
     * @var Tile
     */
    protected $tile;


    /**
     * CollectionGUI constructor
     */
    public function __construct()
    {
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->tile = self::togo()->tiles()->getInstanceForObjRefId(intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID)));

        if (!($this->tile->getEnableRating() === Tile::SHOW_TRUE
            && self::togo()->access()->hasReadAccess($this->tile->getObjRefId()))
        ) {
            die();
        }
        self::ildic()->ctrl()->saveParameter($this, self::GET_FILTER_ITEM);
        self::ildic()->ctrl()->saveParameter($this, self::GET_FILTER_BY);

        self::ildic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::ildic()->ctrl()->getNextClass($this);

        switch ($next_class) {
            default:
                $cmd = self::ildic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_LIKE:
                    case self::CMD_UNLIKE:
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
    protected function setTabs()/*:void*/
    {
    }


    /**
     *
     */
    protected function like()/*: void*/
    {
        self::togo()->ratings(self::ildic()->user())->like($this->tile->getObjRefId());

        ilToGoUIHookGUI::askAndDisplayAlertMessage("liked", self::LANG_MODULE);

        if (!empty($this->parent_ref_id)) {
            self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->parent_ref_id));
        } else {
            self::ildic()->ctrl()->redirectByClass(ilDashboardGUI::class, "jumpToSelectedItems");
        }
    }


    /**
     *
     */
    protected function unlike()/*: void*/
    {
        self::togo()->ratings(self::ildic()->user())->unlike($this->tile->getObjRefId());

        ilToGoUIHookGUI::askAndDisplayAlertMessage("unliked", self::LANG_MODULE);

        if (!empty($this->parent_ref_id)) {
            self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->parent_ref_id));
        } else {
            self::ildic()->ctrl()->redirectByClass(ilDashboardGUI::class, "jumpToSelectedItems");
        }
    }
}
