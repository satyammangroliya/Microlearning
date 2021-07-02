<?php

namespace srag\Plugins\ToGo\Tile\Renderer;

use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Config\ConfigFormGUI;
use srag\Plugins\ToGo\Tile\Tile;
use srag\Plugins\ToGo\Utils\SrTileTrait;

use srag\Plugins\ToGo\Collection\Repository;

/**
 * Class AbstractCollection
 *
 * @package srag\Plugins\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
abstract class AbstractCollection implements CollectionInterface
{
    use DICTrait;
    use SrTileTrait;
    /**
     * @var array
     */
    protected $obj_ref_ids = [];
    /**
     * @var tile[]
     */
    private $tiles = [];


    /**
     *  @var array
     */
    private $order_by=array("item_type"=>"all", "item_name"=>"0","flag"=>0);


    /**
     *  @var int
     */
    private $order=-1;

    /**
     * AbstractCollection constructor
     */
    protected function __construct()
    {
        $this->read();
    }


    /**
     * @inheritDoc
     */
    public function addTile(Tile $tile)/*: void*/
    {
        $this->tiles[$tile->getTileId()] = $tile;
    }


    /**
     * @inheritDoc
     */
    public function removeTile(int $tile_id)/*: void*/
    {
        if (isset($this->tiles[$tile_id])) {
            unset($this->tiles[$tile_id]);
        }
    }


    /**
     * @inheritDoc
     */
    public function getTiles($order=array()) : array
    {

        //$this->sortTiles($this->tiles,$order);
        $coll=null;
        
        $this->setOrder();
        if ($this->order_by['flag']==1) {
            $ids=[];
            //$this->order_by['item_type']="all";
        
            $ids=Repository::getTileIds($item_type=$this->order_by['item_type'], $item_name=$this->order_by['item_name']);
            

            foreach ($this->tiles as $tile) {
                $tile_id=$tile->getTileId();
                if (!in_array($tile_id, $ids)) {
                    $this->removeTile($tile_id);
                }
            }
        }
        

        return $this->tiles;
    }

    private function filterItems($order=array("by"=>"topic", "item"=>"all"))
    {
    }

    public function setOrder()
    {
        $filter_item=Repository::getFilter();
        if ($filter_item===null) {
            $this->order_by["item_type"]="all";
            $this->order_by["item_name"]="";
            $this->order_by["flag"]=0;
        } else {
            $this->order_by["item_type"]=$filter_item->getItemType();
            $this->order_by["item_name"]=$filter_item->getItemName();
            $this->order_by["flag"]=$filter_item->getFlag();
        }
        
        $filter_item->setItemType("all");
        $filter_item->save();
    }
    private function sortTiles($arr, $order)
    {/* order(criterium, order) */
        switch ($order[1]) {
            case "asc":
                $this->order=1;
                break;
            case "desc":
                $this->order=-1;
        }
        switch ($order[0]) {
            case "branch":
                usort($this->tiles, array($this,"compareTilesBranch"));
                break;
            case "topic":
                usort($this->tiles, array($this,"compareTilesBranch"));
                break;
            default:
            
        }
    }


    private function compareTilesThema(Tile $a, Tile $b)
    {
        return $this->order*strcmp(strtolower($a->_getIlObject()->getTitle()), strtolower($b->_getIlObject()->getTitle()));
    }
    private function compareTilesBranch(Tile $a, Tile $b)
    {
        return $this->order*strcmp(strtolower($a->getBranch()), strtolower($b->getBranch()));
    }


    /**
     *
     */
    protected function read() /*: void*/
    {
        $this->initObjRefIds();

        if (self::srTile()->config()->getValue(ConfigFormGUI::KEY_ENABLED_OBJECT_LINKS)) {
            $this->obj_ref_ids = array_filter($this->obj_ref_ids, [self::srTile()->objectLinks(), "shouldShowObjectLink"]);
        }


        foreach ($this->obj_ref_ids as $obj_ref_id) {
            $tile = self::srTile()->tiles()->getInstanceForObjRefId($obj_ref_id);

            if (self::srTile()->access()->hasVisibleAccess($tile->getObjRefId())) {
                $this->addTile($tile);
            }
        }
    }


    /**
     *
     */
    abstract protected function initObjRefIds() /*: void*/
    ;
  
    private function _sortBy()
    {
    }


    private function filterIds($obj_ref_id=null)
    {
        $ids=[];
        $tiles=Tile::innerJoin('ui_uihk_srtile_topic', 'topic_name', 'topic');
        $ids=array_map(function (Tile $tile) {
            return $tile->getObjRefId();
        }, $tiles);
    }
}
