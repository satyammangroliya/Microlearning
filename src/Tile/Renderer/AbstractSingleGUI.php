<?php

namespace srag\Plugins\SrTile\Tile\Renderer;

use ilToGoPlugin;
use ilToGoUIHookGUI;
use ilUIPluginRouterGUI;
use srag\CustomInputGUIs\SrTile\CustomInputGUIsTrait;
use srag\DIC\SrTile\DICTrait;
use srag\Plugins\SrTile\Rating\RatingGUI;
use srag\Plugins\SrTile\Tile\Tile;
use srag\Plugins\SrTile\Utils\SrTileTrait;

/**
 * Class AbstractSingleGUI
 *
 * @package srag\Plugins\SrTile\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
abstract class AbstractSingleGUI implements SingleGUIInterface
{
    use DICTrait;
    use SrTileTrait;
    use CustomInputGUIsTrait;
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
        self::dic()->ctrl()->setParameterByClass(RatingGUI::class, RatingGUI::GET_PARAM_PARENT_REF_ID, ilToGoUIHookGUI::filterRefId());
        self::dic()->ctrl()->setParameterByClass(RatingGUI::class, RatingGUI::GET_PARAM_REF_ID, $this->tile->getObjRefId());


        $tpl = self::plugin()->template("TileSingle/single.html");
        $tpl->setCurrentBlock("tile");

        $tpl->setVariableEscaped("TILE_ID", $this->tile->getTileId());

        $tpl->setVariableEscaped("OBJECT_TYPE", ($this->tile->_getIlObject() !== null ? $this->tile->_getIlObject()->getType() : ""));

        if ($this->tile->getShowTitle() === Tile::SHOW_TRUE) {
            $tpl->setVariableEscaped("TITLE", $this->tile->_getTitle());
        }
        $tpl->setVariableEscaped("TITLE_HORIZONTAL_ALIGN", $this->tile->getLabelHorizontalAlign());
        $tpl->setVariableEscaped("TITLE_VERTICAL_ALIGN", $this->tile->getLabelVerticalAlign());
        
        $tpl->setVariable("LINK", $this->tile->_getAdvancedLink());

        if (self::srTile()->access()->hasOpenAccess($this->tile)) {
            $tpl->setVariableEscaped("VIEWS_IMAGE_PATH", self::plugin()->directory() . "/templates/images/eye.svg");

            $views_count=$this->getViewsCount($this->tile->_getIlObject()->getID());
            $tpl->setVariableEscaped("VIEWS_COUNT", $views_count);
            //Devices
            if ($this->tile->getShowPhone()===1) {
                $tpl->setVariableEscaped("SHOW_PHONE", "enabled");
            } else {
                $tpl->setVariableEscaped("SHOW_PHONE", "disabled");
            }

            if ($this->tile->getShowTablet()===1) {
                $tpl->setVariableEscaped("SHOW_TABLET", "enabled");
            } else {
                $tpl->setVariableEscaped("SHOW_TABLET", "disabled");
            }

            if ($this->tile->getShowLaptop()===1) {
                $tpl->setVariableEscaped("SHOW_LAPTOP", "enabled");
            } else {
                $tpl->setVariableEscaped("SHOW_LAPTOP", "disabled");
            }

            if ($this->tile->getEnableRating() === Tile::SHOW_TRUE
                && self::srTile()->access()->hasReadAccess($this->tile->getObjRefId())
            ) {
                if (self::srTile()->ratings(self::dic()->user())->hasLike($this->tile->getObjRefId())) {
                    $tpl->setVariable("RATING_LINK", self::dic()->ctrl()->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        RatingGUI::class
                    ], RatingGUI::CMD_UNLIKE));
                    $tpl->setVariableEscaped("RATING_TEXT", self::plugin()->translate("unlike", RatingGUI::LANG_MODULE));
                    $tpl->setVariableEscaped("RATING_IMAGE_PATH", self::plugin()->directory() . "/templates/images/like.svg");
                } else {
                    $tpl->setVariable("RATING_LINK", self::dic()->ctrl()->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        RatingGUI::class
                    ], RatingGUI::CMD_LIKE));
                    $tpl->setVariableEscaped("RATING_TEXT", self::plugin()->translate("like", RatingGUI::LANG_MODULE));
                    $tpl->setVariableEscaped("RATING_IMAGE_PATH", self::plugin()->directory() . "/templates/images/star.svg");
                }

                if ($this->tile->getShowLikesCount() === Tile::SHOW_TRUE) {
                    $likes_count = self::srTile()->ratings(self::dic()->user())->getLikesCount($this->tile->getObjRefId());

                    if ($likes_count > 0) {
                        $tpl->setVariable("LIKES_COUNT", $likes_count);
                    }
                }
            }
        }

        $image = $this->tile->getImagePathWithCheck();
        $tpl_image = self::plugin()->template("TileSingle/image.html");
        $tpl_image->setVariableEscaped("IMAGE", (!empty($image) ? "./" . $image : ""));
        $tpl->setVariable("IMAGE", self::output()->getHTML($tpl_image));

        $tpl->setVariableEscaped("IMAGE_POSITION", $this->tile->getImagePosition());
        $tpl->setVariableEscaped("IMAGE_SHOW_AS_BACKGROUND", $this->tile->getShowImageAsBackground());
        $tpl->setVariableEscaped("SHADOW", $this->tile->getShadow());

        $tpl->parseCurrentBlock();

        return self::output()->getHTML($tpl);
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
       
        if (!$event_active) {
            \ilChangeEvent::_activate();
            $activated_by_the_plugin=true;
            \ilChangeEvent::_activate();
        }
        if ($event_active) {
            if (true) {
                $readEvents = \ilChangeEvent::_lookupReadEvents($a_obj_id);

                foreach ($readEvents as $evt) {
                    if ($evt['usr_id'] == ANONYMOUS_USER_ID) {
                        $count_anonymous_reads += $evt['read_count'];
                        $count_users++;
                    } else {
                        $count_user_reads += $evt['read_count'];
                        $count_users++;
                    }
                    if ($count_anonymous_reads>0) {
                        $count_users+=$count_anonymous_reads;
                    }
                }
            }
        }
        return $count_users;
    }
}
