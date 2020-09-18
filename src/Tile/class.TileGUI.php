<?php

namespace srag\Plugins\SrTile\Tile;

use ilLink;
use ilToGoPlugin;
use ilUIPluginRouterGUI;
use ilUtil;
use srag\DIC\SrTile\DICTrait;
use srag\Plugins\SrTile\ObjectLink\ObjectLinksGUI;
use srag\Plugins\SrTile\Utils\SrTileTrait;
use ilToGoUIHookGUI;

use srag\Plugins\SrTile\Collection\Collection;
use srag\Plugins\SrTile\Collection\Filter;
/**
 * Class TileGUI
 *
 * @package           srag\Plugins\SrTile\Tile
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\SrTile\Tile\TileGUI: ilUIPluginRouterGUI
 */
class TileGUI
{

    use DICTrait;
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_BACK_TO_OBJECT = "backToObject";
    const CMD_BACK_TO_PARENT = "backToParent";
    const CMD_EDIT_TILE = "editTile";
    const CMD_GET_PRECONDITIONS = "getPreconditions";
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
        $this->tile = self::srTile()->tiles()->getInstanceForObjRefId(intval(filter_input(INPUT_GET, self::GET_PARAM_REF_ID)));

        if (!self::srTile()->access()->hasWriteAccess($this->tile->getObjRefId())) {
            //die();
            
        }

        self::dic()->ctrl()->saveParameter($this, self::GET_PARAM_REF_ID);
        //self::dic()->ctrl()->saveParameter($this, self::GET_FILTER_ITEM);
        //self::dic()->ctrl()->saveParameter($this, self::GET_FILTER_BY);


        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ObjectLinksGUI::class):
                self::dic()->ctrl()->forwardCommand(new ObjectLinksGUI($this));
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_BACK_TO_OBJECT:
                    case self::CMD_BACK_TO_PARENT:
                    case self::CMD_EDIT_TILE:
                    case self::CMD_GET_PRECONDITIONS:
                    case self::CMD_UPDATE_TILE:
                    case self::CMD_SORT_TOPIC:
                    case self::CMD_SORT_BRANCH:
                    case self::CMD_FILTER;
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
        
        self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_REF_ID, $obj_ref_id);
        


        
        if(self::srTile()->tiles()->isParentAContainer($obj_ref_id)|| self::srTile()->config()->getHomeRefId()==$obj_ref_id){
            self::dic()->tabs()->addTab(self::TAB_TILE, ilToGoPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                self::class
            ], self::CMD_EDIT_TILE));  

            if (!self::srTile()->access()->hasWriteAccess($obj_ref_id)){
                self::dic()->tabs()->clearTabs();
            }
        }
        
       

    }

    public static function addFilterItem(string $item){
        self::dic()->ctrl()->setParameterByClass(self::class, self::GET_FILTER_ITEM, $item);
        self::dic()->ctrl()->saveParameterByClass(self::class, self::GET_FILTER_ITEM);

    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        self::dic()->tabs()->clearTargets();

        $parent = self::srTile()->tiles()->getParentTile($this->tile);
        if (self::srTile()->tiles()->isObject($parent->getObjRefId())) {
            self::dic()->tabs()->setBack2Target($parent->_getTitle(), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_BACK_TO_PARENT));
        }

        self::dic()->tabs()->setBackTarget($this->tile->_getTitle(), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_BACK_TO_OBJECT));

        self::dic()->tabs()->addTab(self::TAB_TILE, self::plugin()->translate("edit_tile", self::LANG_MODULE), self::dic()->ctrl()->getLinkTargetByClass([
            ilUIPluginRouterGUI::class,
            self::class
        ], self::CMD_EDIT_TILE));

      //  self::dic()->tabs()->addNonTabbedLink(self::TAB_SORT, "Nach Branch", self::dic()->ctrl()->getLinkTarget($this, self::CMD_SORT_BRANCH));

        ObjectLinksGUI::addTabs();
    }


    /**
     *
     */
    protected function backToParent()/*: void*/
    {
        $parent = self::srTile()->tiles()->getParentTile($this->tile);

        if (self::srTile()->tiles()->isObject($parent->getObjRefId())) {
            self::dic()->ctrl()->redirectToURL(ilLink::_getStaticLink($parent->getObjRefId()));
        }
    }


    /**
     *
     */
    protected function backToObject()/*: void*/

    {
        


        self::dic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->tile->getObjRefId()));
    }


    /**
     *
     */
    protected function editTile()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_TILE);

        $form = self::srTile()->tiles()->factory()->newFormInstance($this, $this->tile);

        self::output()->output($form, true);
    }


    /**
     *
     */
    protected function updateTile()/*: void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_TILE);

        $form = self::srTile()->tiles()->factory()->newFormInstance($this, $this->tile);

        if (!$form->storeForm()) {
            self::output()->output($form, true);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved", self::LANG_MODULE), true);
        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_TILE);
    }


    /**
     *
     */
    protected function getPreconditions()/*: void*/
    {
        $preconditions = self::srTile()->ilias()->courses()->getPreconditions($this->tile->getObjRefId());

        self::output()->output(self::srTile()->tiles()->renderer()->factory()->newCollectionGUIInstance()->fixed($preconditions));
    }


    /**
     * @return Tile
     */
    public function getTile() : Tile
    {
        return $this->tile;
    }

    /**
     *
     */
    protected function sortTopic()/*: void*/
    {

      //self::srTile()->collections(self::dic()->user())->setSortCriterion(Collection::SORT_BY_TOPIC);
      self::srTile()->collections(self::dic()->user())->sortBy($this->tile->getObjRefId(),Collection::SORT_BY_TOPIC);
      $this->backToObject();

    }

   protected function sortBranch()/*: void*/
    {
       //echo "<script type='text/javascript'>". "SortTOpic".  "</script>";
       //self::dic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->tile->getObjRefId()));
      self::srTile()->collections(self::dic()->user())->sortBy($this->tile->getObjRefId(),Collection::SORT_BY_BRANCH);
      $this->backToObject();

    }

    protected function filter(){
        echo "<script type='text/javascript'> console.log('".self::dic()->user()->getId() .  "')</script>";
        $item_type=filter_input(INPUT_GET, self::GET_FILTER_BY);
        $item_name=filter_input(INPUT_GET, self::GET_FILTER_ITEM);
        if($item_type==null||$item_type=="" ||$item_name==null||$item_name==""){
            $item_type="all";
        }

        //check wether we have items with  this particular item_name
        if ($item_type!="all"){
            $collection=self::srTile()->collections(self::dic()->user()); 
            $items=$item_type=="branch"?$collection->getBranches():$collection->getTopics();
            //TODO
        }


        $user_id=self::dic()->user()->getId();

        $filter=Filter::where(["user_id"=>$user_id])->first();
        if($filter===null){
            $filter=Filter();
            $filter->setUserId($user_id);
            
        }
        $filter->setItemType($item_type);
        $filter->setItemName($item_name);
        $filter->setFlag(1);
        $filter->save();


        self::dic()->ctrl()->redirectToURL(ilLink::_getStaticLink($this->tile->getObjRefId()));
    }

}
