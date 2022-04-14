<?php

namespace minervis\ToGo\Collection;

use ilObjUser;
use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Utils\ToGoTrait;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Collection\Filter;
use minervis\ToGo\Collection\AnonymousSession;
use minervis\ToGo\Collection\AnonymousSummary;

/**
 * Class Repository
 *
 * @package minervis\ToGo\Collection
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{
    use ToGoTrait;
    //use DICTrait;
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
        self::ildic()->database()->dropTable(Collection::TABLE_NAME, false);
        self::ildic()->database()->dropTable(Filter::TABLE_NAME, false);
        self::ildic()->database()->dropTable(AnonymousSession::TABLE_NAME, false);
        self::ildic()->database()->dropTable(AnonymousSummary::TABLE_NAME, false);
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
        $user_id=self::ildic()->user()->getId();
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
        $query_filter = ['sess_id' =>$sess_id, 'obj_id' =>$obj_id];
        if($obj_id == 0){
            $query_filter = ['sess_id' =>$sess_id];
        }
        $anonymousSession = AnonymousSession::where($query_filter)->first();
        
        if ($anonymousSession == null){
            //create a fresh new one
            $anonymousSession = new AnonymousSession();
            $anonymousSession = $anonymousSession->initializeAnonymSession($sess_id, $obj_id);
            $anonymousSession->create();
            $anonymousSession->save();

            if(AnonymousSession::count() >= 400){
                self::deleteInvalidSessions();
            }
        }
        return $anonymousSession;
    }
    public static function likeAnonymous(string $sess_id, int $obj_id, $rating){
        $session = self::getAnonymousSession($sess_id,$obj_id);
        $session->setRating($rating);
        $session->store();
        
        $summary = self::getAnonymousSummary($obj_id);
        $summary->setTotRatings($summary->getTotRatings() + ($rating ? $rating != 0 : -1));
        $summary->save();
        
    }

    public static function viewAnonymous(string $sess_id, int $obj_id, $view){
        $update = true;
        if(($anonym_sess = AnonymousSession::where(['sess_id' =>$sess_id, 'obj_id' =>$obj_id]))->count() > 0 ){
            if($anonym_sess->first()->getView() == 1) $update = false;           
        }
        $session = self::getAnonymousSession($sess_id,$obj_id);
        $session->setView($view);
        $session->store();

        if($update){
            $summary = self::getAnonymousSummary($obj_id);
            $summary->setTotViews($summary->getTotViews() + $view);
            $summary->save();
        }
    }

    public static function getAnonymousViews(int $obj_id){
        $views = AnonymousSummary::where([
            "obj_id" => $obj_id,
        ])->first();
        if (!$views) return 0;
        return $views->getTotViews();
    }

    public static function getAnonymousRatings($obj_id)
    {
        $ratings = AnonymousSummary::where([
            "obj_id" => $obj_id,
        ])->first();
        if(!$ratings) return 0;
        return $ratings->getTotRatings();   
    }

    public static function getAnonymousSummary($obj_id)
    {
        return AnonymousSummary::findOrGetInstance($obj_id);
    }

    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Collection::updateDB();
        Filter::updateDB();
        AnonymousSession::updateDB();
        AnonymousSummary::updateDB();
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

    public static function deleteInvalidSessions()
    {
        global $DIC;
        $sql = 'DELETE FROM ' . AnonymousSession::returnDbTableName() . ' WHERE sess_id NOT IN (SELECT session_id FROM usr_session)';
        $DIC->database()->manipulate($sql);
    }

}
