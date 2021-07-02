<?php

namespace srag\Plugins\ToGo\Collection;

use ilObjUser;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;
use srag\Plugins\ToGo\Tile\Tile;
use srag\Plugins\ToGo\Collection\Filter;
use srag\Plugins\ToGo\Collection\AnonymousSession;

/**
 * Class Repository
 *
 * @package srag\Plugins\ToGo\Collection
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{
    use SrTileTrait;
    use DICTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
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
     * @param Filter $filter
     */
    protected function deleteFilter(Filter $filter)/*:void*/
    {
        $filter->delete();
    }

    /**
     * @param AnonymousSession $anonymousSession
     */
    protected function deleteAnonymousSession(AnonymousSession $anonymousSession)/*:void*/
    {
        $anonymousSession->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Collection::TABLE_NAME, false);
        self::dic()->database()->dropTable(Filter::TABLE_NAME, false);
        self::dic()->database()->dropTable(AnonymousSession::TABLE_NAME, false);
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
    public static function getFilter()
    {
        $user_id=self::dic()->user()->getId();
        $filter= Filter::where(['user_id'=>$user_id])->first();
        if ($filter==null) {
            $filter=new Filter();
            $filter->setUserId($user_id);
            $filter->setFlag(0);
            $filter->save();
        }
        return $filter;
    }

    public static function getAnonymousSession(string $sess_id, int $obj_id = 0)
    {
        global $DIC;
        $logger = $DIC->logger()->root();
        $query_filter = ['sess_id' =>$sess_id, 'obj_id' =>$obj_id];
        if($obj_id == 0){
            $query_filter = ['sess_id' =>$sess_id];
        }
        $anonymousSession = AnonymousSession::where($query_filter)->first();
        
        if ($anonymousSession == null){
            //create a fresh new one
            $latest_row_id= AnonymousSession::orderBy('row_id')->last();
            if($latest_row_id == null){
                $latest_row_id = 0;
            }else{
                $latest_row_id = $latest_row_id->getRowId();
            }
            if ($rows_count >= 500){
                $first = AnonymousSession::first();
                $first->delete();
            }
            $anonymousSession = new AnonymousSession();
            $anonymousSession = $anonymousSession->initializeAnonymSession($sess_id, $obj_id);
            $anonymousSession->setRowId($latest_row_id+1);
            $anonymousSession->save();
        }
        return $anonymousSession;
    }
    public static function likeAnonymous(string $sess_id, int $obj_id, $rating){
        $session = self::getAnonymousSession($sess_id,$obj_id);
        $session->setRating($rating);
        $session->store();
    }
    public static function viewAnonymous(string $sess_id, int $obj_id, $view){
        $session = self::getAnonymousSession($sess_id,$obj_id);
        $session->setView($view);
        $session->store();
    }

    public static function getAnonymousViews(int $obj_id){
        $views = AnonymousSession::where([
            "obj_id" => $obj_id,
            "view" => 1
        ]);
        
        return $views->count();
    }
    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Collection::updateDB();
        Filter::updateDB();
        AnonymousSession::updateDB();
    }

    /**
     *
     */
    protected function initialiseTopics()
    {/*:void*/
        foreach (Topic::get() as $topic) {
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


        foreach ($init_topics as $topic_name) {
            $topic_query=$this->getTopic($topic_name);
            if ($topic_query===null) {
                $topic=new Topic();
                $topic->setTopicName($topic_name);
                $this->storeTopic($topic);
            }
        }
    }

    protected function initialiseBranches()
    {/*:void*/
        foreach (Branch::get() as $branch) {
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

        foreach ($init_branches as $branch_name) {
            //check if the element is new
            $branch_query=$this->getBranch($branch_name);
            if ($branch_query===null) {
                $branch=new Branch();
                $branch->setBranchName($branch_name);
                $this->storeBranch($branch);
            }
        }
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
        } else {
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
     * @param AnonymousSession $anonymousSession
     */
    protected function updateAnonymousSession(AnonymousSession $anonymousSession)/*:void*/
    {
        $anonymousSession->update();
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
     * @param AnonymousSession $anonymousSession
     */
    protected function storeAnonymousSession(AnonymousSession $anonymousSession)/*:void*/
    {
        $anonymousSession->store();
    }

    public function getTopics()
    {
        $all_tiles=Tile::get();
        $all_topics=[];
        foreach ($all_tiles as $tile) {
            $topic=$tile->getTopic();
            //check if the topic contains
            if ($topic==null || $topic=="") {
                continue;
            }
            array_push($all_topics, $topic);
        }

        
        return array_unique($all_topics);
    }

    public function getBranches()
    {
        $all_tiles=Tile::get();
        $all_branches=[];
        foreach ($all_tiles as $tile) {
            $branches=$tile->getBranch();
            
            if ($branches==null || $branches=="") {
                continue;
            }
            $extended_branches=explode(",", $branches);
            foreach ($extended_branches as $ext_branch) {
                array_push($all_branches, $ext_branch);
            }
        }

        return array_unique($all_branches);
    }

    



    

    public static function getTileIds($item_type="all", $item_name="")
    {
        $ids=[];
        $query_result=[];
        switch ($item_type) {
            case "topic":
                $query_result=Tile::where(['topic'=>$item_name])->get();
                break;
            case "branch":
                $query_result=Tile::where(['branch'=>"%".$item_name."%"], "LIKE")->get();
                break;
            default:
                $query_result=Tile::get();
        }
       
        foreach ($query_result as $result) {
            $ids[]=$result->getTileId();
        }
        return $ids;
    }

    public function setFilter(string $item_type, string $item_name)
    {
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


}
