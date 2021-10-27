<?php

namespace minervis\ToGo\Tile\Renderer;

use ilLink;
use ilToGoPlugin;
use ilToGoUIHookGUI;
use ilUIPluginRouterGUI;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Utils\ToGoTrait;
use minervis\ToGo\Tile\TileGUI;

use ilGroupedListGUI;

/**
 * Class AbstractCollectionGUI
 *
 * @package minervis\ToGo\Tile\Renderer
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch> *
 */
abstract class AbstractCollectionGUI implements CollectionGUIInterface
{
    use ToGoTrait;
    //use DICTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    private $test_link="";
    /**
     * @var CollectionInterface $collection
     */
    protected $collection;
    protected $tiles;
    


    /**
     * AbstractCollectionGUI constructor
     *
     * @param mixed $param
     */
    public function __construct($param)
    {
        $this->collection = self::togo()->tiles()->renderer()->factory()->newCollectionInstance($this, $param);
        $this->tiles = $this->collection->getTiles();
    }


    /**
     *
     */
    protected function initJS()/*: void*/
    {
        self::ildic()->ui()->mainTemplate()->addJavascript(self::togoplugin()->directory() . "/js/tiles.js");
        self::ildic()->ui()->mainTemplate()->addJavaScript(self::togoplugin()->directory() . "/node_modules/@iconfu/svg-inject/dist/svg-inject.min.js");
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        global $tpl;
        $this->initJS();

        $collection_html = "";
        

        if (count($this->tiles) > 0) {
            $parent_tile = self::togo()->tiles()->getInstanceForObjRefId(ilToGoUIHookGUI::filterRefId() ?? ROOT_FOLDER_ID);
            
            self::ildic()->ui()->mainTemplate()->addCss(self::togoplugin()->directory() . "/css/togo.css");

            $tpl = self::togoplugin()->template("TileCollection/collection.html");

            self::ildic()->ctrl()->setParameterByClass(TileGUI::class, TileGUI::GET_PARAM_REF_ID, intval(ilToGoUIHookGUI::filterRefId()));            
            
            $home_link=ilLink::_getStaticLink(intval(self::togo()->config()->getHomeRefId()));

            $tpl->setVariable("VIEW", htmlspecialchars($parent_tile->getView()));

            $tile_html = self::togoplugin()->getHTML(array_map(function (Tile $tile) : SingleGUIInterface {
                return self::togo()->tiles()->renderer()->factory()->newSingleGUIInstance($this, $tile);
            }, $this->tiles));

            $tpl->setVariable("TILES", $tile_html);         
            
            $tpl->setVariable("BACK_HOME_LINK", $home_link);
            
            $tpl->setVariable("BACK_COLOR", "#FFFFFF");
            
            

            $umfrage_obj_ref_id=self::togo()->config()->getUmfrageObjRefId();
            $umfrage_link="";
            if ($umfrage_obj_ref_id) {
                $umfrage_link=self::togo()->tiles()->getInstanceForObjRefId(intval($umfrage_obj_ref_id))->_getAdvancedLink();
                $umfrage_link=str_replace("href=", "", $umfrage_link);
                $umfrage_link=str_replace('"', "", $umfrage_link);
            } else {
                $umfrage_link="https://ilias.bgn-akademie.de/goto_bgnakademie_cat_6137.html";
            }
            
            $tpl->setVariable("LS_UMFRAGE", self::togoplugin()->getHTML($this->generateLinks("Umfrage", $umfrage_link)));

            $was_sind_obj_ref_id=self::togo()->config()->getWasSindObjRefId();
            $was_sind_lernsnacks_bgn_link="Was sind Lern-Snacks?";
            if ($was_sind_obj_ref_id) {
                $was_sind_lernsnacks_bgn_link=self::togo()->tiles()->getInstanceForObjRefId(intval($was_sind_obj_ref_id))->_getAdvancedLink();
                $was_sind_lernsnacks_bgn_link=str_replace("href=", "", $was_sind_lernsnacks_bgn_link);
                $was_sind_lernsnacks_bgn_link=str_replace('"', "", $was_sind_lernsnacks_bgn_link);
            } else {
                $was_sind_lernsnacks_bgn_link="https://ilias.bgn-akademie.de/goto_bgnakademie_cat_6136.html";
            }
            
            $tpl->setVariable("LS_WAS_SIND", self::togoplugin()->getHTML($this->generateLinks("Was sind Lern-Snacks?", $was_sind_lernsnacks_bgn_link)));
            $tpl->setVariable("LS_HOME", self::togoplugin()->getHTML($this->generateLinks("Angebot", $home_link)));
            $tpl->setVariable("BRANCH_SEL", $this->getBranchSelection());
            $tpl->setVariable("TOPIC_SEL", $this->getTopicSelection());
            $collection_html = self::togoplugin()->getHTML($tpl);

            $this->hideOriginalRowsOfTiles();
        }

        return $collection_html;
    }


    /**
     * @inheritDoc
     */
    public function hideOriginalRowsOfTiles() /*: void*/
    {
        $css = '';

        $parent_tile = self::togo()->tiles()->getInstanceForObjRefId(ilToGoUIHookGUI::filterRefId() ?? ROOT_FOLDER_ID);

        $css .= '.tile';
        $css .= '{' . $parent_tile->_getLayout() . '}';

        $is_parent_css_rendered = false;
        foreach ($this->tiles as $tile) {
            self::ildic()->event()->raise(IL_COMP_PLUGIN . "/" . ilToGoPlugin::PLUGIN_NAME, ilToGoPlugin::EVENT_CHANGE_TILE_BEFORE_RENDER, [
                "tile" => $tile
            ]);

            $css .= '#sr_tile_' . $tile->getTileId();
            $css .= '{' . $tile->_getSize() . '}';

            $css .= '#sr_tile_' . $tile->getTileId() . ' .card_bottom';
            $css .= '{' . $tile->_getColor(false, true) . '}';

            $css .= '#sr_tile_' . $tile->getTileId() . ' > .card';
            $css .= '{' . $tile->_getColor() . $tile->_getBorder() . '}';

            $css .= '#sr_tile_' . $tile->getTileId() . ' .btn-default, ';
            $css .= '#sr_tile_' . $tile->getTileId() . ' .badge';
            $css .= '{' . $tile->_getColor(true) . '}';

            if (!$is_parent_css_rendered) {
                $is_parent_css_rendered = true;

                if ($parent_tile->getApplyColorsToGlobalSkin() === Tile::SHOW_TRUE) {
                    if (!empty($parent_tile->_getBackgroundColor())) {
                        $css .= 'a#il_mhead_t_focus';
                        $css .= '{color:rgb(' . $parent_tile->_getBackgroundColor() . ')!important;}';
                    }

                    $css .= '.btn-default';
                    $css .= '{' . $tile->_getColor();
                    if (!empty($parent_tile->_getBackgroundColor())) {
                        $css .= 'border-color:rgb(' . $parent_tile->_getBackgroundColor() . ')!important;';
                    }
                    $css .= '}';
                }
            }
        }

        self::ildic()->ui()->mainTemplate()->addInlineCss($css);
    }

    public function generateLinks(string $label, string $obj_link)
    {
        //cut the href out
        
        $ui=self::ildic()->ui()->factory();
        $renderer=self::ildic()->ui()->renderer();
        return $renderer->render($ui->link()->standard($label, $obj_link));
    }

    private function editLink($filter_item, $item_type="topic")
    {
        $filter_item=urlencode($filter_item);

        self::ildic()->ctrl()->saveParameterByClass(TileGUI::class, TileGUI::GET_FILTER_BY);
        self::ildic()->ctrl()->saveParameterByClass(TileGUI::class, TileGUI::GET_FILTER_ITEM);
        self::ildic()->ctrl()->setParameterByClass(TileGUI::class, TileGUI::GET_FILTER_BY, $item_type);
        self::ildic()->ctrl()->setParameterByClass(TileGUI::class, TileGUI::GET_FILTER_ITEM, $filter_item);
        $item_link=self::ildic()->ctrl()->getLinkTargetByClass([
            ilUIPluginRouterGUI::class,
            TileGUI::class
        ], TileGUI::CMD_FILTER);
        return $item_link;
    }

    private function getBranchSelection()
    {
        $branches = array_map(function ($tile){
            return explode(',', $tile->getBranch());
        }, $this->tiles);
        $branches = array_merge(... $branches);
        $branches = array_filter($branches);
        $branch_menu=$this->renderSelection("branch", $branches);
        return $branch_menu;
    }
    private function getTopicSelection()
    {
        $topics= array_map(function(Tile $tile) {
            return $tile->getTopic();
        }, $this->tiles);
        $topics = array_unique(array_filter($topics));
        $topics_menu=$this->renderSelection("topic", $topics);
        return $topics_menu;
    }

    private function renderSelection($sel_name="", $items=null)
    {
        $gr_list=new ilGroupedListGUI();
        $gr_list->setAsDropDown(true);
        if ($items) {
            foreach ($items as $item) {
                $item_link=$this->editLink($item, $item_type=$sel_name);
                $gr_list->addEntry($item, $item_link);
            }
            return $gr_list->getHTML();
        }
        return '';
    }
}
