<?php

namespace srag\Plugins\SrTile\Collection;

use ilObjUser;
use ilSrTilePlugin;
use srag\DIC\SrTile\DICTrait;
use srag\Plugins\SrTile\Utils\SrTileTrait;
use srag\Plugins\SrTile\Tile\Tile;
use srag\Plugins\SrTile\Collection\Filter;

/**
 * Class Repository
 *
 * @package srag\Plugins\SrTile\Collection
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use SrTileTrait;
    use DICTrait;
    const PLUGIN_CLASS_NAME = ilSrTilePlugin::class;
    /**
     * @var self[]
     */
    protected static $instances = [];


    /**
     * @param ilObjUser $user
     *
     * @return self
     */
    public static function getInstance(ilObjUser $user) : self
    {
        if (!isset(self::$instances[$user->getId()])) {
            self::$instances[$user->getId()] = new self($user);
        }

        return self::$instances[$user->getId()];
    }


    /**
     * @var ilObjUser
     */
    protected $user;


    /**
     * Repository constructor
     *
     * @param ilObjUser $user
     */
    private function __construct(ilObjUser $user)
    {
        $this->user = $user;
    }


    /**
     * @param Collection $collection
     */
    protected function deleteCollection(Collection $collection)/*:void*/
    {
        $collection->delete();
    }

    /**
     * @param Topic $topic
     */
    protected function deleteTopic(Topic $topic)/*:void*/
    {
        $topic->delete();
    }


    /**
     * @param Branch $branch
     */
    protected function deleteBranch(Branch $branch)/*:void*/
    {
        $branch->delete();
    }


    /**
     * @param Filter $filter
     */
    protected function deleteFilter(Filter $filter)/*:void*/
    {
        $filter->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Collection::TABLE_NAME, false);
        self::dic()->database()->dropTable(Topic::TABLE_NAME, false);
        self::dic()->database()->dropTable(Branch::TABLE_NAME, false);
        self::dic()->database()->dropTable(ArBranch::TABLE_NAME, false);
        self::dic()->database()->dropTable(ArTopic::TABLE_NAME, false);
        self::dic()->database()->dropTable(Filter::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


  
    /**
     * @param int $obj_id
     *
     * @return Collection|null
     */
    public function getCollection(int $obj_id)/*: ?Collection*/
    {
        /**
         * @var Collection|null $collection
         */

        $collection = Collection::where([
            "obj_id"  => $obj_id,
            "user_id" => $this->user->getId()
        ])->first();
        

        return $collection;
    }

    /**
     * @param string $topic_name
     *
     * @return Topic|null
     */
    public function getTopic(string $topic_name)/*: ?Topic*/
    {
        /**
         * @var Topic|null $topic
         */

        $topic = Topic::where([
            "topic_name"  => $topic_name
        ])->first();
        

        return $topic;
    }

    /**
     * @param string $branch_name
     *
     * @return Branch|null
     */
    public  function getBranch(string $branch_name)/*: ?Branch*/
    {
        /**
         * @var Branch|null $branch
         */

        $branch = Branch::where([
            "branch_name"  => $branch_name
        ])->first();
        

        return $branch;
    }



    /**
     * @param string $topic_name
     *
     * @return Topic|null
     */
    public  function getAllTopics($topic_name=null)/*: ?Topic*/
    {
        /**
         * @var Topic|null $topic
         */

        $topics = Topic::get();
        

        return $topics;
    }

    /**
     * @param string $branch_name
     *
     * @return Branch|null
     */
    public function getAllBranches(string $branch_name=null)/*: ?Branch*/
    {
        /**
         * @var Branch|null $branch
         */

        $branches = Branch::get();
        

        return $branches;
    }

    public function isFilterItemInDB(string $filter_item_name, string $filter_item_type){

    }

    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Collection::updateDB();
        Branch::updateDB();
        Topic::updateDB();
        $this->initialiseBranches();
        $this->initialiseTopics();
        ArTopic::updateDB();
        ArBranch::updateDB();
        Filter::updateDB();
    }

    /**
     * 
     */
    protected function initialiseTopics(){/*:void*/
        foreach(Topic::get() as $topic){
            $this->deleteTopic($topic);
        }
        $init_topics=array(
            'Lärmschutz',
            'Leitern und Tritte',
            'KommMitMensch',
            'Brandschutz',
            'Stress',
            'Sicher schneiden',
            'Transport',
            'Verkehrssicherheit'
        );


        foreach ($init_topics as $topic_name){
            $topic_query=$this->getTopic($topic_name);
            if($topic_query===null){
                $topic=new Topic();
                $topic->setTopicName($topic_name);
                $this->storeTopic($topic);
            }
            
        }

    }

    protected function initialiseBranches(){/*:void*/
        foreach(Branch::get() as $branch){
            $this->deleteBranch($branch);
        }
        $init_branches=array(
            'Gastgewerbe',
            'Backgewerbe',
            'Nahrungsmittelindustrie',
            'Schausteller',
            'Fleischwirtschaft',
            'Getränkeindustrie'
        );

        foreach ($init_branches as $branch_name){
            //check if the element is new
            $branch_query=$this->getBranch($branch_name);
            if ($branch_query===null){
                $branch=new Branch();
                $branch->setBranchName($branch_name);
                $this->storeBranch($branch);

            }
            
        }

    }
    public static function getFilter(){

        $user_id=self::dic()->user()->getId();
        $filter= Filter::where(['user_id'=>$user_id])->first();
        if($filter==null){
            $filter=new Filter();
            $filter->setUserId($user_id);
            $filter->setFlag(0);
            $filter->save();
            
        }
        return $filter;
    }


    /**
     * @param int $obj_ref_id
     */
    public function sortBy(int $obj_ref_id, $criterium=Collection::SORT_DEFAULT)/*: void*/
    {
        $obj_id = intval(self::dic()->objDataCache()->lookupObjId($obj_ref_id));

        $collection = $this->getCollection($obj_id);

        if ($collection === null) {
            $collection = $this->factory()->newInstance();

            $collection->setObjId($obj_id);

            $collection->setUserId($this->user->getId());

	    $collection->setCollectionId();

	    $collection->setSortCriterion($criterium);

            $this->storeCollection($collection);
        }else{

	    $collection->setSortCriterion($criterium);

	    $this->updateCollection($collection);
        }
    }


   /**
    * @param Collection $collection
    */
    protected function updateCollection(Collection $collection)/*:void*/
    {
        $collection->update();
    }

   /**
    * @param Filter $filter
    */
    protected function updateFilter(Filter $filter)/*:void*/
    {
        $filter->update();
    }


   /**
    * @param Topic $topic
    */
    protected function updateTopic(Topic $topic)/*:void*/
    {
        $topic->update();
    }

   /**
    * @param Branch $branch
    */
    protected function updateBranch(Branch $branch)/*:void*/
    {
        $branch->update();
    }

    
    
    
    /**
    * @param ArTopic $ar_topic
    */
    protected function updateArTopic(ArTopic $ar_topic)/*:void*/
    {
        $ar_topic->update();
    }

   /**
    * @param ArBranch $ar_branch
    */
    protected function updateArBranch(ArBranch $ar_branch)/*:void*/
    {
        $ar_branch->update();
    }


    /**
     * @param Collection $collection
     */
    protected function storeCollection(Collection $collection)/*:void*/
    {
        $collection->store();
    }

    /**
     * @param Filter $filter
     */
    protected function storeFilter(Filter $filter)/*:void*/
    {
        $filter->store();
    }


    /**
     * @param Topic $topic
     */
    protected function storeTopic(Topic $topic)/*:void*/
    {
        $topic->store();
    }

    /**
     * @param Branch $branch
     */
    protected function storeBranch(Branch $branch)/*:void*/
    {
        $branch->store();
    }

        /**
     * @param ArTopic $ar_topic
     */
    protected function storeArTopic(ArTopic $ar_topic)/*:void*/
    {
        $ar_topic->store();
    } 

    /**
     * @param ArBranch $ar_branch
     */
    protected function storeArBranch(ArBranch $ar_branch)/*:void*/
    {
        $ar_branch->store();
    }

    public function getTopics(){
        $all_tiles=Tile::get();
        $all_topics=[];
        foreach ($all_tiles as $tile){
            $topic=$tile->getTopic();
            //check if the topic contains 
            if ($topic==null || $topic=="") continue;
            array_push($all_topics,$topic);
        }

        
        return array_unique($all_topics);
    }

    public function getBranches(){
        $all_tiles=Tile::get();
        $all_branches=[];
        foreach ($all_tiles as $tile){
            $branches=$tile->getBranch();
            
            if ($branches==null || $branches=="") continue;
            $extended_branches=explode(",",$branches);
            foreach($extended_branches as $ext_branch){
                array_push($all_branches,$ext_branch);
            }
            
        }
        //remove empty words
        //$this->debug($all_branches);

        return array_unique($all_branches);

    }

    



    

    public static function getTileIds( $item_type="all", $item_name=""){
        $ids=[];
        $query_result=[];
        switch($item_type){
            case "topic":
                $query_result=Tile::where(['topic'=>$item_name])->get();
                break;
            case "branch":
                $query_result=Tile::where(['branch'=>"%".$item_name."%"], "LIKE")->get();
                break;
            default:
                $query_result=Tile::get();
        }
        //return $query_result->count();
        // if (!is_array($query_result)){
        //     $query_result=array($query_result);
        // }
        //echo "".self::debug(gettype($query_result));
       
       foreach($query_result as $result){
           
           $ids[]=$result->getTileId();
       }
        return $ids;
    }

    public function setFilter(string $item_type, string $item_name){
        
    }



    /**
     * @param int $obj_ref_id
     */
    public function unsort(int $obj_ref_id)/*: void*/
    {
        $obj_id = intval(self::dic()->objDataCache()->lookupObjId($obj_ref_id));

        $collection = $this->getCollection($obj_id);

        if ($collection !== null) {
            $this->deleteCollection($collection);
        }
    }



    public function debug($input){
        $log="<script type='text/javascript'> console.log('";
        if (is_array($input)){
            foreach($input as $item){
                $log.=$item."\n";
            }
        }else{
            $log.=$item;
        }
        return $log."')</script>";

    }
    
    
}

