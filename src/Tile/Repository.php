<?php

namespace minervis\ToGo\Tile;

use Closure;
use ilContainerReference;
use ilObjectFactory;
use ilObjOrgUnit;
use ilToGoPlugin;
//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Tile\Renderer\Repository as RendererRepository;
use minervis\ToGo\Utils\ToGoTrait;
use Throwable;

/**
 * Class Repository
 *
 * @package minervis\ToGo\Tile
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @ilCtrl_isCalledBy minervis\ToGo\Tile\TileGUI: ilUIPluginRouterGUI
 */
final class Repository
{
    //use DICTrait;
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @var Tile[]
     *
     * @deprecated
     */
    protected static $instances_by_ref_id = [];
    /**
     * @var Tile[]
     *
     * @deprecated
     */
    protected static $parent_tile_cache = [];
    /**
     * @var bool[]
     *
     * @deprecated
     */
    protected static $is_object_cache = [];
    /**
     * @var array
     */
    protected $clone_tile_cache = [];


    /**
     * Repository constructor
     */
    private function __construct()
    {
    }


    /**
     * @param int $org_obj_ref_id
     * @param int $clone_obj_ref_id
     */
    public function cloneTile(int $org_obj_ref_id, int $clone_obj_ref_id)/*:void*/
    {
        $org_tile = $this->getInstanceForObjRefId($org_obj_ref_id);

        $clone_tile = $this->getInstanceForObjRefId($clone_obj_ref_id);

        $properties = Closure::bind(function () : array {
            return get_object_vars($this);
        }, $org_tile, Tile::class)();
        $properties = array_filter($properties, function (string $property) : bool {
            return ($property !== "tile_id"
                && $property !== "obj_ref_id"
                && $property !== "il_object"
                && $property !== "ar_safe_read"
                && $property !== "connector_container_name");
        }, ARRAY_FILTER_USE_KEY);

        // Delete old image
        $clone_tile->applyNewImage("");

        foreach ($properties as $key => $value) {
            Closure::bind(function ($key, $value)/*:void*/ {
                $this->{$key} = $value;
            }, $clone_tile, Tile::class)($key, $value);
        }

        // Copy template image
        $clone_tile->applyNewImage($org_tile->getImagePathWithCheck());

        $this->storeTile($clone_tile);

        if (self::togo()->config()->getValue('enabled_on_object_links')) {
            if (!isset($this->clone_tile_cache[$org_obj_ref_id])) {
                $this->clone_tile_cache[$org_obj_ref_id] = [];
            }
            $this->clone_tile_cache[$org_obj_ref_id][] = $clone_obj_ref_id;

        }
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::ildic()->database()->dropTable(Tile::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return Tile[]
     */
    public function getTiles() : array
    {
        return Tile::get();
    }


    /**
     * @param int|null $obj_ref_id
     *
     * @return Tile
     *
     * @deprecated
     */
    public function getInstanceForObjRefId(int $obj_ref_id = null) : Tile
    {
        /**
         * @var Tile $tile
         * @var Tile $tile_class
         */
        if (ilObjectFactory::getInstanceByRefId($obj_ref_id, false) instanceof ilContainerReference) {
            $tile_class = TileReference::class;
        } else {
            $tile_class = Tile::class;
        }

        if (!isset(self::$instances_by_ref_id[$obj_ref_id])) {
            $obj_ref_id_modified_for_read = $tile_class::modifyTileRefIdForRead($obj_ref_id);

            $tile = $tile_class::where(["obj_ref_id" => $obj_ref_id_modified_for_read])->first();

            if ($tile === null) {
                $tile = new $tile_class();

                if ($obj_ref_id_modified_for_read !== null) {
                    $tile->setObjRefId($obj_ref_id_modified_for_read);

                    $this->storeTile($tile); // Ensure tile id

                }
            }

            if ($tile instanceof TileReference) {
                $tile->setSourceObjRefId($obj_ref_id);
            }

            self::$instances_by_ref_id[$obj_ref_id] = $tile;
        }

        return self::$instances_by_ref_id[$obj_ref_id];
    }


    /**
     * @param Tile $tile
     *
     * @return Tile|null
     *
     * @deprecated
     */
    public function getParentTile(Tile $tile)/*:?Tile*/
    {
        if (!isset(self::$parent_tile_cache[$tile->getObjRefId()])) {
            try {
                self::$parent_tile_cache[$tile->getObjRefId()] = $this->getInstanceForObjRefId(self::ildic()->repositoryTree()
                    ->getParentId($tile->getObjRefId()));
            } catch (Throwable $ex) {
                // Fix No node_id given!
                self::$parent_tile_cache[$tile->getObjRefId()] = null;
            }
        }

        return self::$parent_tile_cache[$tile->getObjRefId()];
    }

    public function isParentAContainer($ref_id)
    {
        $home=self::togo()->config()->getHomeRefId();
        if ($home=="") {
            return false;
        }
        $parent_id=self::ildic()->repositoryTree()->getParentId($ref_id);
        return $home==$parent_id;
    }
    
    public function getParentId($ref_id)
    {
        return self::ildic()->repositoryTree()->getParentId($ref_id);
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Tile::updateDB();

        foreach (Tile::orderBy("obj_ref_id", "asc")->get() as $tile) {
            /**
             * @var Tile $tile
             */

            $parent = $this->getParentTile($tile);
            if ($parent !== null) {
                if ($tile->getActionsPosition() === Tile::POSITION_PARENT) {
                    $tile->setActionsPosition($parent->getActionsPosition());
                }

                if ($tile->getActionsVerticalAlign() === Tile::VERTICAL_ALIGN_PARENT) {
                    $tile->setActionsVerticalAlign($parent->getActionsVerticalAlign());
                }

                if ($tile->getApplyColorsToGlobalSkin() === Tile::SHOW_PARENT) {
                    $tile->setApplyColorsToGlobalSkin($parent->getApplyColorsToGlobalSkin());
                }

                if ($tile->getBackgroundColorType() === Tile::COLOR_TYPE_PARENT) {
                    $tile->setBackgroundColorType($parent->getBackgroundColorType());

                    if (empty($tile->getBackgroundColor())) {
                        $tile->setBackgroundColor($parent->getBackgroundColor());
                    }
                }

                if ($tile->getBorderColorType() === Tile::COLOR_TYPE_PARENT) {
                    $tile->setBorderColorType($parent->getBorderColorType());

                    if (empty($tile->getBorderColor())) {
                        $tile->setBorderColor($parent->getBorderColor());
                    }
                }

                if ($tile->getBorderSizeType() === Tile::SIZE_TYPE_PARENT) {
                    $tile->setBorderSizeType($parent->getBorderSizeType());

                    if (empty($tile->getBorderSize())) {
                        $tile->setBorderSize($parent->getBorderSize());
                    }
                }

                if ($tile->getEnableRating() === Tile::SHOW_PARENT) {
                    $tile->setEnableRating($parent->getEnableRating());
                }

                if ($tile->getFontColorType() === Tile::COLOR_TYPE_PARENT) {
                    $tile->setFontColorType($parent->getFontColorType());

                    if (empty($tile->getFontColor())) {
                        $tile->setFontColor($parent->getFontColor());
                    }
                }

                if ($tile->getFontSizeType() === Tile::SIZE_TYPE_PARENT) {
                    $tile->setFontSizeType($parent->getFontSizeType());

                    if (empty($tile->getFontSize())) {
                        $tile->setFontSize($parent->getFontSize());
                    }
                }

                if ($tile->getImagePosition() === Tile::POSITION_PARENT) {
                    $tile->setImagePosition($parent->getImagePosition());
                }

                if ($tile->getLabelHorizontalAlign() === Tile::HORIZONTAL_ALIGN_PARENT) {
                    $tile->setLabelHorizontalAlign($parent->getLabelHorizontalAlign());
                }

                if ($tile->getLabelVerticalAlign() === Tile::VERTICAL_ALIGN_PARENT) {
                    $tile->setLabelVerticalAlign($parent->getLabelVerticalAlign());
                }

                if ($tile->getLearningProgressPosition() === Tile::POSITION_PARENT) {
                    $tile->setLearningProgressPosition($parent->getLearningProgressPosition());
                }

                if ($tile->getMarginType() === Tile::SIZE_TYPE_PARENT) {
                    $tile->setMarginType($parent->getMarginType());

                    if (empty($tile->getMargin())) {
                        $tile->setMargin($parent->getMargin());
                    }
                }

                if ($tile->getObjectIconPosition() === Tile::POSITION_PARENT) {
                    $tile->setObjectIconPosition($parent->getObjectIconPosition());
                }

                if ($tile->getOpenObjWithOneChildDirect() === Tile::OPEN_PARENT) {
                    $tile->setOpenObjWithOneChildDirect($parent->getOpenObjWithOneChildDirect());
                }

                if ($tile->getRecommendMailTemplateType() === Tile::MAIL_TEMPLATE_PARENT) {
                    $tile->setRecommendMailTemplateType($parent->getRecommendMailTemplateType());

                    if (empty($tile->getRecommendMailTemplate())) {
                        $tile->setRecommendMailTemplate($parent->getRecommendMailTemplate());
                    }
                }

                if ($tile->getShadow() === Tile::SHOW_PARENT) {
                    $tile->setShadow($parent->getShadow());
                }

                if ($tile->getShowActions() === Tile::SHOW_PARENT) {
                    $tile->setShowActions($parent->getShowActions());
                }

                if ($tile->getShowDownloadCertificate() === Tile::SHOW_PARENT) {
                    $tile->setShowDownloadCertificate($parent->getShowDownloadCertificate());
                }

                if ($tile->getShowFavoritesIcon() === Tile::SHOW_PARENT) {
                    $tile->setShowFavoritesIcon($parent->getShowFavoritesIcon());
                }

                if ($tile->getShowImageAsBackground() === Tile::SHOW_PARENT) {
                    $tile->setShowImageAsBackground($parent->getShowImageAsBackground());
                }

                if ($tile->getShowLearningProgress() === Tile::LEARNING_PROGRESS_PARENT) {
                    $tile->setShowLearningProgress($parent->getShowLearningProgress());
                }

                if ($tile->getShowLearningProgressFilter() === Tile::SHOW_PARENT) {
                    $tile->setShowLearningProgressFilter($parent->getShowLearningProgressFilter());
                }

                if ($tile->getShowLearningProgressLegend() === Tile::SHOW_PARENT) {
                    $tile->setShowLearningProgressLegend($parent->getShowLearningProgressLegend());
                }

                if ($tile->getShowLikesCount() === Tile::SHOW_PARENT) {
                    $tile->setShowLikesCount($parent->getShowLikesCount());
                }

                if ($tile->getShowObjectTabs() === Tile::SHOW_PARENT) {
                    $tile->setShowObjectTabs($parent->getShowObjectTabs());
                }

                if ($tile->getShowPreconditions() === Tile::SHOW_PARENT) {
                    $tile->setShowPreconditions($parent->getShowPreconditions());
                }

                if ($tile->getShowRecommendIcon() === Tile::SHOW_PARENT) {
                    $tile->setShowRecommendIcon($parent->getShowRecommendIcon());
                }

                if ($tile->getShowTitle() === Tile::SHOW_PARENT) {
                    $tile->setShowTitle($parent->getShowTitle());
                }

                if ($tile->getView() === Tile::VIEW_PARENT) {
                    $tile->setView($parent->getView());
                }
                //devices

                if ($tile->getShowPhone()===1) {
                    $tile->setShowPhone($parent->getShowPhone());
                }

                if ($tile->getShowTablet()===1) {
                    $tile->setShowTablet($parent->getShowTablet());
                }

                if ($tile->getShowLaptop()===1) {
                    $tile->setShowLaptop($parent->getShowLaptop());
                }
                //END: devices
        
                //Branch AND TOPIC
                if ($tile->getTopic()==="") {
                    $tile->setTopic($parent->getTopic());
                }
                if ($tile->getBranch()==="") {
                    $tile->setBranch($parent->getBranch());
                }

                //END: branch_and_topic
            }

            if (isset($tile->tile_enabled_children) && !boolval($tile->tile_enabled_children)) {
                $tile->setView(Tile::VIEW_DISABLED);
            }

            $this->storeTile($tile);
        }

        foreach ($this->getTiles() as $tile) {
            /**
             * @var Tile $tile
             */

            if (empty($tile->getColumnsType())) {
                $tile->setColumnsType(Tile::DEFAULT_COLUMNS_TYPE);
            }

            if (empty($tile->getColumns())) {
                $tile->setColumns(Tile::DEFAULT_COLUMNS);
            }

            if (empty($tile->getShowLanguageFlag())) {
                $tile->setShowLanguageFlag(Tile::DEFAULT_SHOW_LANGUAGE_FLAG);
            }

            if (empty($tile->getLanguageFlagPosition())) {
                $tile->setLanguageFlagPosition(Tile::DEFAULT_LANGUAGE_FLAG_POSITION);
            }

            //Devices
            if (empty($tile->getShowPhone())) {
                $tile->setShowPhone(1);
            }

            if (empty($tile->getShowTablet())) {
                $tile->setShowTablet(1);
            }

            if (empty($tile->getShowLaptop())) {
                $tile->setShowLaptop(1);
            }
            //END: Devices
            //Branch and Topic
            if (empty($tile->getTopic())) {
                $tile->setTopic("");
            }
            if (empty($tile->getBranch())) {
                $tile->setBranch("");
            }

            $this->storeTile($tile);
        }
    }


    /**
     * @param int|null $obj_ref_id
     *
     * @return bool
     *
     * @deprecated
     */
    public function isObject(/*?*/ int $obj_ref_id = null) : bool
    {
        if (!isset(self::$is_object_cache[$obj_ref_id])) {
            self::$is_object_cache[$obj_ref_id] = ($obj_ref_id !== null && $obj_ref_id > 0 && $obj_ref_id !== intval(SYSTEM_FOLDER_ID)
                && ($obj_ref_id === intval(ROOT_FOLDER_ID) || ($object = ilObjectFactory::getInstanceByRefId($obj_ref_id, false)) !== false)
                && !($object instanceof ilObjOrgUnit));
        }

        return self::$is_object_cache[$obj_ref_id];
    }


    /**
     * @return RendererRepository
     */
    public function renderer() : RendererRepository
    {
        return RendererRepository::getInstance();
    }


    /**
     * @param Tile $tile
     */
    public function storeTile(Tile $tile)/*:void*/
    {
        $tile->store();
    }
}
