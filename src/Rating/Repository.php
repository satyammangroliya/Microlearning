<?php

namespace minervis\ToGo\Rating;

use ilObjUser;
use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Utils\ToGoTrait;
use minervis\ToGo\Collection\AnonymousSession;
use minervis\ToGo\Collection\AnonymousSummary;

/**
 * Class Repository
 *
 * @package minervis\ToGo\Rating
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
     * @param Rating $rating
     */
    protected function deleteRating(Rating $rating)/*:void*/
    {
        $rating->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::ildic()->database()->dropTable(Rating::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @param int $obj_ref_id
     *
     * @return int
     */
    public function getLikesCount($obj_ref_id) : int
    {
        $obj_id = intval(self::togoObjDataCache()->lookupObjId($obj_ref_id));
        $likes_count = 0;
        $authenticated_rating = Rating::where([
            "obj_id" => $obj_id
        ])->where(['user_id' => ANONYMOUS_USER_ID], '!=');
        if ($authenticated_rating){
            $likes_count = $authenticated_rating->count();
        }
        $anonymous_rating = AnonymousSummary::where([
            "obj_id" => $obj_id,
        ])->last();
        $likes_count = $likes_count + (!$anonymous_rating ? 0 : $anonymous_rating->getTotRatings());
        return $likes_count;
    }


    /**
     * @param int $obj_id
     *
     * @return Rating|null
     */
    public function getRating(int $obj_id)/*: ?Rating*/
    {
        /**
         * @var Rating|null $rating
         */

        $rating = Rating::where([
            "obj_id"  => $obj_id,
            "user_id" => $this->user->getId()
        ])->first();

        return $rating;
    }


    /**
     * @param int $obj_ref_id
     *
     * @return bool
     */
    public function hasLike(int $obj_ref_id) : bool
    {
        $obj_id = intval(self::togoObjDataCache()->lookupObjId($obj_ref_id));
        
        if ( $this->user->getId() == ANONYMOUS_USER_ID){
            
            $session = self::togo()->collections($this->user)->getAnonymousSession(self::ildic()['ilAuthSession']->getId(), $obj_id);
            return $session->getRating() !== 0;
        }else{

        }

        return ($this->getRating($obj_id) !== null);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Rating::updateDB();
    }


    /**
     * @param int $obj_ref_id
     */
    public function like(int $obj_ref_id)/*: void*/
    {
        $obj_id = intval(self::togoObjDataCache()->lookupObjId($obj_ref_id));
        if($this->user->getId() == ANONYMOUS_USER_ID){
            $sess_id = self::ildic()['ilAuthSession']->getId();
            self::togo()->collections($this->user)->likeAnonymous($sess_id, $obj_id, 1);
        }
        

        $rating = $this->getRating($obj_id);

        if ($rating === null) {
            $rating = $this->factory()->newInstance();

            $rating->setObjId($obj_id);

            $rating->setUserId($this->user->getId());

            $this->storeRating($rating);
        }
    }


    /**
     * @param Rating $rating
     */
    protected function storeRating(Rating $rating)/*:void*/
    {
        $rating->store();
    }


    /**
     * @param int $obj_ref_id
     */
    public function unlike(int $obj_ref_id)/*: void*/
    {
        $obj_id = intval(self::togoObjDataCache()->lookupObjId($obj_ref_id));
        if($this->user->getId() == ANONYMOUS_USER_ID){
            $sess_id = self::ildic()['ilAuthSession']->getId();
            self::togo()->collections($this->user)->likeAnonymous($sess_id, $obj_id, 0);
            
        }

        $rating = $this->getRating($obj_id);

        if ($rating !== null) {
            $this->deleteRating($rating);
        }
    }
    public function view(int $obj_ref_id)
    {
        $obj_id = intval(self::togoObjDataCache()->lookupObjId($obj_ref_id));
        if($this->user->getId() == ANONYMOUS_USER_ID){
            $sess_id = self::ildic()['ilAuthSession']->getId();
            self::togo()->collections($this->user)->viewAnonymous($sess_id, $obj_id, 1);            
        }


    }
}
