<?php

namespace minervis\ToGo\Tile\Renderer;

//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Utils\ToGoTrait;

use minervis\ToGo\Collection\Repository;

/**
 * Class AbstractCollection
 *
 * @package minervis\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
abstract class AbstractCollection implements CollectionInterface
{
    //use DICTrait;
    use ToGoTrait;
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
        $this->setOrder();
        if ($this->order_by['flag']==1) {
            $item_name = $item_name=$this->order_by['item_name'];
            $item_type = $this->order_by['item_type'];
            
            if ($item_type == 'topic') {
                $this->tiles = array_filter($this->tiles, function($tile) use ($item_name){
                    return  $tile->getTopic() == $item_name;
                });
            }else if ($item_type == 'branch') {
                $this->tiles = array_filter($this->tiles, function($tile) use ($item_name){
                    
                    return (stripos($tile->getBranch(), $item_name) !==  false) ;
                });
            }
        }
        return $this->tiles;
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

    /**
     *
     */
    protected function read() /*: void*/
    {
        $this->initObjRefIds();

        foreach ($this->obj_ref_ids as $obj_ref_id) {
            $tile = self::togo()->tiles()->getInstanceForObjRefId($obj_ref_id);

            if (self::togo()->access()->hasVisibleAccess($tile->getObjRefId())) {
                $this->addTile($tile);
            }
        }
    }
    /**
     *
     */
    abstract protected function initObjRefIds() /*: void*/
    ;
}
