<?php

//use srag\DIC\ToGo\DICTrait;
use minervis\ToGo\Tile\Tile;
use minervis\ToGo\Tile\TileGUI;
use minervis\ToGo\Utils\ToGoTrait;

/**
 * Class ilToGoUIHookGUI
 *
 * @author Jephte Abijuru <jephte.abijuru@minervis.com>
 *
 * @ilCtrl_isCalledBy ilToGoUIHookGUI: ilUIPluginRouterGUI
 */
class ilToGoUIHookGUI extends ilUIHookPluginGUI
{
    //use DICTrait;
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const PAR_TABS = "tabs";
    const TEMPLATE_GET = "template_get";
    const TOOLBAR_LOADER = "tile_toolbar_loader";
    const REPOSITORY_LOADER = "tile_repository_loader";
    const TAB_PERM_ID = "perm";
    const ADMIN_FOOTER_TPL_ID = "tpl.adm_content.html";
    const GET_PARAM_REF_ID = "ref_id";
    const GET_PARAM_TARGET = "target";
    const GET_RENDER_EDIT_TILE_ACTION = "render_edit_tile_action";
    const TEMPLATE_ID_REPOSITORY = "Services/Container/tpl.container_list_block.html";
    /**
     * @var bool[]
     */
    protected static $load
        = [
            self::TOOLBAR_LOADER         => false,
            self::REPOSITORY_LOADER      => false,
        ];


    /**
     * @return int|null
     *
     * @deprecated
     */
    public static function filterRefId()/*: ?int*/
    {
        $obj_ref_id = filter_input(INPUT_GET, self::GET_PARAM_REF_ID);

        if ($obj_ref_id === null) {
            $param_target = filter_input(INPUT_GET, self::GET_PARAM_TARGET);

            $obj_ref_id = explode("_", $param_target)[1];
        }

        $obj_ref_id = intval($obj_ref_id);

        if ($obj_ref_id > 0) {
            return $obj_ref_id;
        } else {
            return null;
        }
    }


    /**
     * ilToGoUIHookGUI constructor
     */
    public function __construct()
    {
    }


    /**
     * @inheritDoc
     */
    public function getHTML(/*string*/ $a_comp, /*string*/ $a_part, $a_par = []) : array
    {
        if ($this->matchRepository($a_part, $a_par)) {
            return [
                "mode" => self::REPLACE,
                "html" => self::togoplugin()->getHTML(self::togo()->tiles()->renderer()->factory()->newCollectionGUIInstance()->container($a_par["html"]))
            ];
        }

        return parent::getHTML($a_comp, $a_part, $a_par);
    }


    /**
     * @inheritDoc
     */
    public function modifyGUI(/*string*/ $a_comp, /*string*/ $a_part, /*array*/ $a_par = [])/*: void*/
    {
        $obj_ref_id = self::filterRefId();

        if ($this->matchToolbar($a_part)) {
            if (!self::togo()->access()->hasWriteAccess($obj_ref_id)) {
                if (self::togo()->tiles()->getInstanceForObjRefId($obj_ref_id)->getShowObjectTabs() === Tile::SHOW_FALSE) {
                    self::ildic()->tabs()->clearTargets();
                    self::ildic()->tabs()->clearSubTabs();
                }
                return;
            }
 


            if (count(array_filter(self::ildic()->tabs()->target, function (array $tab) : bool {
                return (strpos($tab["id"], self::TAB_PERM_ID) !== false);
            })) > 0
            ) {
                TileGUI::addTabs($obj_ref_id);
            }
        }
    }


    /**
     * @param string $a_part
     *
     * @return bool
     */
    protected function matchToolbar(string $a_part) : bool
    {
        $baseClass = strtolower(filter_input(INPUT_GET, "baseClass"));
        $obj_ref_id = self::filterRefId();

        return (!self::$load[self::TOOLBAR_LOADER]
            && $baseClass !== strtolower(ilAdministrationGUI::class)
            && $a_part === self::PAR_TABS
            && (self::$load[self::TOOLBAR_LOADER] = true)
            && self::togo()->tiles()->isObject($obj_ref_id));
    }


    /**
     * @param string $a_part
     * @param array  $a_par
     *
     * @return bool
     */
    protected function matchRepository(string $a_part, array $a_par) : bool
    {
        $obj_ref_id = self::filterRefId();

        return (!self::$load[self::REPOSITORY_LOADER]
            && $a_part === self::TEMPLATE_GET
            && $a_par["tpl_id"] === self::TEMPLATE_ID_REPOSITORY
            && (self::$load[self::REPOSITORY_LOADER] = true)
            && (true == true)
            && !in_array(self::ildic()->ctrl()->getCmd(), ["editOrder"])
            && !in_array(self::ildic()->ctrl()->getCallHistory()[0]["cmd"], ["editOrder"])
            && !$_SESSION["il_cont_admin_panel"]
            && self::togo()->tiles()->isObject($obj_ref_id)
            && self::togo()->tiles()->getInstanceForObjRefId($obj_ref_id)->getView() !== Tile::VIEW_DISABLED);
    }

    /**
     * @param string $key
     * @param string $module
     * @param string $alert_type
     * @param bool   $keep
     */
    public static function askAndDisplayAlertMessage(string $key, string $module, string $alert_type = "success", bool $keep = true)/*: void*/
    {
        $should_not_display = [];

        self::ildic()->appEventHandler()->raise(IL_COMP_PLUGIN . "/" . ilToGoPlugin::PLUGIN_NAME, ilToGoPlugin::EVENT_SHOULD_NOT_DISPLAY_ALERT_MESSAGE, [
            "lang_module"        => $module,
            "lang_key"           => $key,
            "alert_type"         => $alert_type,
            "should_not_display" => &$should_not_display // Unfortunately ILIAS Raise Event System not supports return results so use a referenced variable
        ]);

        if (empty($should_not_display)) {
            ilUtil::{"send" . ucfirst($alert_type)}(self::togoplugin()->translate($key, $module), $keep);
        }
    }
}
