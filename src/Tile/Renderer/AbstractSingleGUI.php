<?php

namespace minervis\ToGo\Tile\Renderer;

use ilToGoPlugin;
use ilToGoUIHookGUI;
use ilUIPluginRouterGUI;
use minervis\ToGo\Collection\AnonymousSession;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Rating\RatingGUI;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Tile\TileGUI;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class AbstractSingleGUI
 *
 * @package minervis\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
abstract class AbstractSingleGUI implements SingleGUIInterface
{
    //use DICTrait;
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    /**
     * @var Tile
     */
    protected $tile;


    /**
     * AbstractSingleGUI constructor
     *
     * @param Tile $tile
     */
    public function __construct(Tile $tile)
    {
        $this->tile = $tile;
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        self::ildic()->ctrl()->setParameterByClass(RatingGUI::class, RatingGUI::GET_PARAM_PARENT_REF_ID, ilToGoUIHookGUI::filterRefId());
        self::ildic()->ctrl()->setParameterByClass(RatingGUI::class, RatingGUI::GET_PARAM_REF_ID, $this->tile->getObjRefId());

        $current_object = $this->tile->_getIlObject();

        $tpl = self::togoplugin()->template("TileSingle/single.html");
        $tpl->setCurrentBlock("tile");

        $tpl->setVariable("TILE_ID", htmlspecialchars($this->tile->getTileId()));

        $tpl->setVariable("OBJECT_TYPE", htmlspecialchars(($this->tile->_getIlObject() !== null ? $current_object->getType() : "")));

        if ($this->tile->getShowTitle() === Tile::SHOW_TRUE) {
            $tpl->setVariable("TITLE", $this->tile->_getTitle());
        }
        
        $tpl->setVariable("LINK", $this->tile->_getAdvancedLink());
        if (self::ildic()->user()->getId() == ANONYMOUS_USER_ID) {
            $anonymlink_link=self::ildic()->ctrl()->getLinkTargetByClass([
                ilUIPluginRouterGUI::class,
                RatingGUI::class
            ], RatingGUI::CMD_READ_ANONYMOUS);
            $tpl->setVariable("LINK", ' href="' . $anonymlink_link. '"');
        }

        if (self::togo()->access()->hasOpenAccess($this->tile)) {
            $tpl->setVariable("VIEWS_IMAGE_PATH", htmlspecialchars(self::togoplugin()->directory() . "/templates/images/eye.svg"));

            $views_count=$this->getViewsCount($this->tile->_getIlObject()->getID());
            $tpl->setVariable("VIEWS_COUNT", htmlspecialchars($views_count));
            $tpl->setVariable("VIEWS_TEXT", htmlspecialchars(self::togoplugin()->translate("views_text", TileGUI::LANG_MODULE)));
            //Devices
            if ($this->tile->getShowPhone()===1) {
                $tpl->setVariable("SHOW_PHONE", "enabled");
            } else {
                $tpl->setVariable("SHOW_PHONE", "disabled");
            }

            if ($this->tile->getShowTablet()===1) {
                $tpl->setVariable("SHOW_TABLET", "enabled");
            } else {
                $tpl->setVariable("SHOW_TABLET", "disabled");
            }

            if ($this->tile->getShowLaptop()===1) {
                $tpl->setVariable("SHOW_LAPTOP", "enabled");
            } else {
                $tpl->setVariable("SHOW_LAPTOP", "disabled");
            }

            if ($this->tile->getEnableRating() === Tile::SHOW_TRUE
                && self::togo()->access()->hasReadAccess($this->tile->getObjRefId())
            ) {
                if (self::togo()->ratings(self::ildic()->user())->hasLike($this->tile->getObjRefId())) {
                    $tpl->setVariable("RATING_LINK", self::ildic()->ctrl()->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        RatingGUI::class
                    ], RatingGUI::CMD_UNLIKE));
                    $tpl->setVariable("RATING_TEXT", self::togoplugin()->translate("unlike", RatingGUI::LANG_MODULE));
                    $tpl->setVariable("RATING_IMAGE_PATH", self::togoplugin()->directory() . "/templates/images/like.svg");
                } else {
                    $tpl->setVariable("RATING_LINK", self::ildic()->ctrl()->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        RatingGUI::class
                    ], RatingGUI::CMD_LIKE));
                    $tpl->setVariable("RATING_TEXT", self::togoplugin()->translate("like", RatingGUI::LANG_MODULE));
                    $tpl->setVariable("RATING_IMAGE_PATH", self::togoplugin()->directory() . "/templates/images/star.svg");
                }

                if ($this->tile->getShowLikesCount() === Tile::SHOW_TRUE) {
                    $likes_count = self::togo()->ratings(self::ildic()->user())->getLikesCount($this->tile->getObjRefId());

                    if ($likes_count > 0) {
                        $tpl->setVariable("LIKES_COUNT", $likes_count);
                    }
                }
            }
        }

        $image = $this->tile->getImagePathWithCheck();
        if ($image == ""){
            $ilias_img = self::ildic()->object()->commonSettings()->tileImage()->getByObjId($current_object->getId());
            if ($ilias_img->exists()){
                $image = $ilias_img->getFullPath();
            }

        }
        $tpl->setVariable("IMAGE", htmlspecialchars((!empty($image) ? "./" . $image : "")));


        $tpl->parseCurrentBlock();

        return self::togoplugin()->getHTML($tpl);
    }


    /* count views

       author: jephte.abijuru@minervis.com
       reads views
    */
    public function getViewsCount(int $a_obj_id)
    {
        $count_users = 0;
        $count_user_reads = 0;
        $count_anonymous_reads = 0;
        require_once './Services/Tracking/classes/class.ilChangeEvent.php';
        $event_active=\ilChangeEvent::_isActive();
        $count_anonymous_reads = self::togo()->collections(self::ildic()->user())->getAnonymousViews($a_obj_id);
       
        if (!$event_active) {
            \ilChangeEvent::_activate();
        }
        if ($event_active) {
            if (true) {
                $readEvents = \ilChangeEvent::_lookupReadEvents($a_obj_id);

                foreach ($readEvents as $evt) {
                    if ($evt['usr_id'] == ANONYMOUS_USER_ID) {
                    } else {
                        $count_user_reads += $evt['read_count'];
                        $count_users++;
                    }
                    $count_user_reads += $evt['read_count'];
                }
                if ($count_anonymous_reads>0) {
                    $count_users+=$count_anonymous_reads;
                }
            }
        }
        return $count_users;
    }
}
