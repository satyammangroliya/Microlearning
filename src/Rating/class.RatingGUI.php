<?php

namespace minervis\ToGo\Rating;

use ilLink;
use ilDashboardGUI;
use ilToGoPlugin;
use ilToGoUIHookGUI;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class RatingGUI
 *
 * @package           minervis\ToGo\Rating
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @ilCtrl_isCalledBy minervis\ToGo\Rating\RatingGUI: ilUIPluginRouterGUI
 */
class RatingGUI
{
    //use DICTrait;
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_LIKE = "like";
    const CMD_UNLIKE = "unlike";
    const CMD_READ_ANONYMOUS = "readAnonymous";
    const GET_PARAM_PARENT_REF_ID = "parent_ref_id";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "rating";
    /**
     * @var int
     */
    protected $parent_ref_id;
    /**
     * @var Tile
     */
    protected $tile;


    /**
     * RatingGUI constructor
     */
    public function __construct()
    {
    }


    /**
     *
     */
    public function executeCommand()/*: void*/
    {
        $this->parent_ref_id = intval(filter_input(INPUT_GET, self::GET_PARAM_PARENT_REF_ID));
        $this->tile = self::togo()->tiles()->getInstanceForObjRefId(intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID)));

        if (!($this->tile->getEnableRating() === Tile::SHOW_TRUE
            && self::togo()->access()->hasReadAccess($this->tile->getObjRefId()))
        ) {
            die();
        }

        self::ildic()->ctrl()->saveParameter($this, self::GET_PARAM_PARENT_REF_ID);
        self::ildic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::ildic()->ctrl()->getNextClass($this);

        switch ($next_class) {
            default:
                $cmd = self::ildic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_LIKE:
                    case self::CMD_UNLIKE:
                    case self::CMD_READ_ANONYMOUS:
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

        //ilToGoUIHookGUI::askAndDisplayAlertMessage("liked", self::LANG_MODULE);

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

        //ilToGoUIHookGUI::askAndDisplayAlertMessage("unliked", self::LANG_MODULE);

        if (!empty($this->parent_ref_id)) {
            self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->parent_ref_id));
        } else {
            self::ildic()->ctrl()->redirectByClass(ilDashboardGUI::class, "jumpToSelectedItems");
        }
    }
    protected function readAnonymous(){
        $obj_ref_id = $this->tile->getObjRefId();
        self::togo()->ratings(self::ildic()->user())->view($obj_ref_id);
        self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($obj_ref_id));
    }
}
