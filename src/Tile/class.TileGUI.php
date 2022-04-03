<?php

namespace minervis\ToGo\Tile;

use ilLink;
use ilToGoPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use minervis\ToGo\Utils\ToGoTrait;
use ilToGoUIHookGUI;

use minervis\ToGo\Collection\Collection;
use minervis\ToGo\Collection\Filter;

/**
 * Class TileGUI
 *
 * @package           minervis\ToGo\Tile
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy minervis\ToGo\Tile\TileGUI: ilUIPluginRouterGUI
 */
class TileGUI
{
    //use DICTrait;
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_BACK_TO_OBJECT = "backToObject";
    const CMD_BACK_TO_PARENT = "backToParent";
    const CMD_EDIT_TILE = "editTile";
    const CMD_UPDATE_TILE = "updateTile";
    const GET_PARAM_REF_ID = "ref_id";
    const LANG_MODULE = "tile";
    const TAB_TILE = "tile";

    //Customized
    const TAB_SORT_BRANCH="sortByBranch";
    const TAB_SORT="sort";

    const CMD_SORT_TOPIC="sortTopic";
    const CMD_SORT_BRANCH="sortBranch";
    const CMD_FILTER="filter";
    const GET_FILTER_ITEM="filter_by";
    const GET_FILTER_BY="by";
    const GET_PARAM_USER_ID="aid";

    const CMD_READ_ANONYMOUS = "readAnonymous";

    //Filter

    /**
     * @var Tile
     */
    protected $tile;


    /**
     * TileGUI constructor
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

        if (!self::togo()->access()->hasWriteAccess($this->tile->getObjRefId())) {
            //die();
        }

        self::ildic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);

        $this->setTabs();

        $next_class = self::ildic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::ildic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK_TO_OBJECT:
                    case self::CMD_BACK_TO_PARENT:
                    case self::CMD_SORT_TOPIC:
                    case self::CMD_SORT_BRANCH:
                    case self::CMD_FILTER:
                    case self::CMD_READ_ANONYMOUS:
                    case 'edit':
                    case 'saveProperties':
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     * @param int $obj_ref_id
     */
    public static function addTabs(int $obj_ref_id)/*:void*/
    {
        self::ildic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
        if (self::togo()->tiles()->isParentAContainer($obj_ref_id)|| self::togo()->config()->getHomeRefId()==$obj_ref_id) {
            self::ildic()->tabs()->addTab(self::TAB_TILE, ilToGoPlugin::PLUGIN_NAME, self::ildic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], 'edit'));

            if (!self::togo()->access()->hasWriteAccess($obj_ref_id)) {
                self::ildic()->tabs()->clearTabs();
            }
        }
    }

    public static function addFilterItem(string $item)
    {
        self::ildic()->ctrl()->setParameterByClass(self::class, self::GET_FILTER_ITEM, $item);
        self::ildic()->ctrl()->saveParameterByClass(self::class, self::GET_FILTER_ITEM);
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::ildic()->tabs()->clearTargets();

        $parent = self::togo()->tiles()->getParentTile($this->tile);
        if (self::togo()->tiles()->isObject($parent->getObjRefId())) {
            self::ildic()->tabs()->setBack2Target($parent->_getTitle(), self::ildic()->ctrl()
                ->getLinkTarget($this, self::CMD_BACK_TO_PARENT));
        }

        self::ildic()->tabs()->setBackTarget($this->tile->_getTitle(), self::ildic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK_TO_OBJECT));
        self::ildic()->tabs()->addTab(self::TAB_TILE, self::togoplugin()->translate("edit_tile", self::LANG_MODULE), self::ildic()->ctrl()->getLinkTargetByClass([
            ilUIPluginRouterGUI::class,
            self::class
        ], 'edit'));
    }


    /**
     *
     */
    protected function backToParent()/*: void*/
    {
        $parent = self::togo()->tiles()->getParentTile($this->tile);

        if (self::togo()->tiles()->isObject($parent->getObjRefId())) {
            self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($parent->getObjRefId()));
        }
    }


    /**
     *
     */
    protected function backToObject()/*: void*/
    {
        self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->tile->getObjRefId()));
    }

    /**
     *
     */
    protected function edit()/*: void*/
    {
        self::ildic()->tabs()->activateTab(self::TAB_TILE);

        $form = self::togo()->tiles()->factory()->newFormInstance($this, $this->tile);
        $a_form = $form->initPropertyForm();
        self::togoplugin()->output($a_form, true);
    }


    /**
     *
     */
    protected function saveProperties()/*: void*/
    {
        self::ildic()->tabs()->activateTab(self::TAB_TILE);

        $form = self::togo()->tiles()->factory()->newFormInstance($this, $this->tile);
        $a_form = $form->initPropertyForm();

        if (!$form->saveProperties($a_form)) {
            self::togoplugin()->output($a_form, true);

            return;
        }
        ilUtil::sendSuccess(self::togoplugin()->translate("saved", self::LANG_MODULE), true);
        self::ildic()->ctrl()->redirect($this, 'edit');
    }


    protected function readAnonymous($ref_id = 0){
        self::ildic()->ctrl()->setParameterByClass(self::class, self::CMD_READ_ANONYMOUS, true);
        self::ildic()->ctrl()->saveParameterByClass(self::class, self::CMD_READ_ANONYMOUS);
        $obj_ref_id = $this->tile->getObjRefId();
        $obj_id = intval(self::togoObjDataCache()->lookupObjId($obj_ref_id));
        self::togo()->collections(self::ildic()->user())->viewAnonymous(self::ildic()['ilAuthSession']->getId(), $obj_id, 1);
        self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($obj_ref_id));
    }

    public function getAnonymousLink(){
        $tile_link=self::ildic()->ctrl()->getLinkTargetByClass([
            ilUIPluginRouterGUI::class,
            TileGUI::class
        ], TileGUI::CMD_READ_ANONYMOUS);

        return $tile_link;
    }
    /**
     * @return Tile
     */
    public function getTile() : Tile
    {
        return $this->tile;
    }

    protected function filter()
    {
        $item_type=filter_input(INPUT_GET, self::GET_FILTER_BY);
        $item_name=filter_input(INPUT_GET, self::GET_FILTER_ITEM);
        $item_name=urldecode($item_name);
        if ($item_type==null||$item_type=="" ||$item_name==null||$item_name=="") {
            $item_type="all";
        }

        //check wether we have items with  this particular item_name
        if ($item_type!="all") {
            $collection=self::togo()->collections(self::ildic()->user());
            $items=$item_type=="branch"?$collection->getBranches():$collection->getTopics();
            //TODO
        }


        $user_id=self::ildic()->user()->getId();

        $filter=Filter::where(["user_id"=>$user_id])->first();
        if ($filter===null) {
            $filter=new Filter();
            $filter->setUserId($user_id);
        }
        $filter->setItemType($item_type);
        $filter->setItemName($item_name);
        $filter->setFlag(1);
        $filter->save();


        self::ildic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->tile->getObjRefId()));
    }
}
