<?php

namespace srag\Plugins\ToGo\Tile;

use ilColorPickerInputGUI;
use ilFormSectionHeaderGUI;
use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\FileUpload\Location;
use ilImageFileInputGUI;
use ilNonEditableValueGUI;
use ilNumberInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use ilToGoPlugin;
use srag\CustomInputGUIs\ToGo\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\ToGo\PropertyFormGUI\PropertyFormGUI;
use srag\Notifications4Plugin\ToGo\Notification\NotificationInterface;
use srag\Notifications4Plugin\ToGo\Notification\NotificationsCtrl;
use srag\Plugins\ToGo\Template\TemplateConfigGUI;
use srag\Plugins\ToGo\Utils\SrTileTrait;
// Customized
use ilCheckboxInputGUI;
use ilTextInputGUI;
use ilUIPluginRouterGUI;

/**
 * Class TileFormGUI
 *
 * @package srag\Plugins\ToGo\Tile
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy TileFormGUI: ilUIPluginRouterGUI, PropertyFormGUI
 * @ilCtrl_Calls ilUIPluginRouterGUI: TileFormGUI, ilLinkInputGUI
 *
 */
class TileFormGUI extends PropertyFormGUI
{
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const LANG_MODULE = TileGUI::LANG_MODULE;
    /**
     * @var Tile
     */
    protected $tile;


    /**
     * TileFormGUI constructor
     *
     * @param TileGUI|TemplateConfigGUI $parent
     * @param Tile                      $tile
     */
    public function __construct($parent, Tile $tile)
    {
        $this->tile = $tile;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
    {
        switch ($key) {
            case "columns_count":
                if ($this->tile->getColumnsType() === Tile::SIZE_TYPE_COUNT) {
                    return Items::getter($this->tile, "columns");
                }
                break;

            case "columns_fix_width":
                if ($this->tile->getColumnsType() === Tile::SIZE_TYPE_PX) {
                    return Items::getter($this->tile, "columns");
                }
                break;

            case "image":
                if (!empty(Items::getter($this->tile, $key))) {
                    return "./" . $this->tile->getImagePath();
                }
                break;
            case "background_image":
                if (!empty(Items::getter($this->tile, $key))) {
                    return "./" . $this->tile->getBackgroundImagePath();
                }
                break;

            default:
                return Items::getter($this->tile, $key);
        }

        return null;
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(TileGUI::CMD_UPDATE_TILE, $this->txt("save"));
    }

    private function shouldHideItem()
    {
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
        "branch_topic"=>[
            self::PROPERTY_CLASS  =>ilFormSectionHeaderGUI::class,
            "setTitle"            =>$this->txt("branches_and_topics")
        ],
        "branch"     =>[
                self::PROPERTY_CLASS=>ilTextInputGUI::class,
                "setTitle"            =>$this->txt("branches")
          ],
        "topic"      =>[
        self::PROPERTY_CLASS=>ilTextInputGUI::class,
        "setTitle"            =>$this->txt("topics")
         ],
        "general_settings"=>[
        self::PROPERTY_CLASS  =>ilFormSectionHeaderGUI::class,
        "setTitle"            =>$this->txt("general_settings")
         ],
            "background_image"                    => [
                self::PROPERTY_CLASS    => ilImageFileInputGUI::class,
                self::PROPERTY_REQUIRED => false
            ],
            "view"                        => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::VIEW_DISABLED => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("view_disabled")
                    ],
                    Tile::VIEW_TILE     => [
                        self::PROPERTY_CLASS    => ilRadioOption::class,
                        self::PROPERTY_SUBITEMS => [
                            "columns_type" => [
                                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                                self::PROPERTY_REQUIRED => false,
                                self::PROPERTY_SUBITEMS => [
                                    Tile::SIZE_TYPE_COUNT => [
                                        self::PROPERTY_CLASS    => ilRadioOption::class,
                                        self::PROPERTY_SUBITEMS => [
                                            "columns_count" => [
                                                self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                                                self::PROPERTY_REQUIRED => false,
                                                "setTitle"              => $this->txt("columns_count")
                                            ]
                                        ],
                                        "setTitle"              => $this->txt("columns_count")
                                    ],
                                    Tile::SIZE_TYPE_PX    => [
                                        self::PROPERTY_CLASS    => ilRadioOption::class,
                                        self::PROPERTY_SUBITEMS => [
                                            "columns_fix_width" => [
                                                self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                                                self::PROPERTY_REQUIRED => false,
                                                "setTitle"              => $this->txt("columns_fix_width"),
                                                "setSuffix"             => "px"
                                            ]
                                        ],
                                        "setTitle"              => $this->txt("columns_fix_width")
                                    ]
                                ],
                                "setTitle"              => $this->txt("columns")
                            ]
                        ],
                        "setTitle"              => $this->txt("view_tile")
                    ],
                ]
            ],
            "margin_type"                 => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::SIZE_TYPE_PX => [
                        self::PROPERTY_CLASS    => ilRadioOption::class,
                        self::PROPERTY_SUBITEMS => [
                            "margin" => [
                                self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                                self::PROPERTY_REQUIRED => false,
                                "setSuffix"             => "px"
                            ]
                        ],
                        "setTitle"              => $this->txt("set")
                    ]
                ],
                "setTitle"              => $this->txt("margin")
            ],
            "show_object_tabs"            => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::SHOW_FALSE => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("show_false")
                    ],
                    Tile::SHOW_TRUE  => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("show_true")
                    ]
                ]
            ],

            "tile"                           => [
                self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class
            ],
            "background_color_type"          => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::COLOR_TYPE_AUTO_FROM_IMAGE => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("color_auto_from_image")
                    ],
                    Tile::COLOR_TYPE_SET             => [
                        self::PROPERTY_CLASS    => ilRadioOption::class,
                        self::PROPERTY_SUBITEMS => [
                            "background_color" => [
                                self::PROPERTY_CLASS    => ilColorPickerInputGUI::class,
                                self::PROPERTY_REQUIRED => false,
                                "setDefaultColor"       => ""
                            ]
                        ],
                        "setTitle"              => $this->txt("set")
                    ]
                ],
                "setTitle"              => $this->txt("background_color")
            ],
            "shadow"                         => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::SHOW_FALSE => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("show_false")
                    ],
                    Tile::SHOW_TRUE  => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("show_true")
                    ]
                ]
            ],
            "open_obj_with_one_child_direct" => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::OPEN_FALSE => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("open_false")
                    ],
                    Tile::OPEN_TRUE  => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("open_true")
                    ]
                ]
            ],

            "image_header"             => [
                self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class,
                "setTitle"           => $this->txt("image")
            ],
            "image"                    => [
                self::PROPERTY_CLASS    => ilImageFileInputGUI::class,
                self::PROPERTY_REQUIRED => false
            ],
            "label" => [
                self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class
            ],
            "font_size_type"         => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::SIZE_TYPE_PX => [
                        self::PROPERTY_CLASS    => ilRadioOption::class,
                        self::PROPERTY_SUBITEMS => [
                            "font_size" => [
                                self::PROPERTY_CLASS    => ilNumberInputGUI::class,
                                self::PROPERTY_REQUIRED => false,
                                "setSuffix"             => "px"
                            ]
                        ],
                        "setTitle"              => $this->txt("set")
                    ]
                ],
                "setTitle"              => $this->txt("font_size")
            ],
            "rating"           => [
                self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class
            ],
            "enable_rating"    => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::SHOW_FALSE => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("disabled")
                    ],
                    Tile::SHOW_TRUE  => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("enabled")
                    ]
                ]
            ],
            "show_likes_count" => [
                self::PROPERTY_CLASS    => ilRadioGroupInputGUI::class,
                self::PROPERTY_REQUIRED => false,
                self::PROPERTY_SUBITEMS => [
                    Tile::SHOW_FALSE => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("show_false")
                    ],
                    Tile::SHOW_TRUE  => [
                        self::PROPERTY_CLASS => ilRadioOption::class,
                        "setTitle"           => $this->txt("show_true")
                    ]
                ]
            ],
       "devices"		    =>[
             self::PROPERTY_CLASS => ilFormSectionHeaderGUI::class,
            
       ],
          "show_tablet"               =>[
              self::PROPERTY_CLASS=>ilCheckboxInputGUI::class,
                "setTitle"             =>"Tablet"
          ],
         "show_phone"               =>[
              self::PROPERTY_CLASS=>ilCheckboxInputGUI::class,
                "setTitle"             =>"Phone"
          ],
    "show_laptop"               =>[
              self::PROPERTY_CLASS=>ilCheckboxInputGUI::class,
                "setTitle"             =>"Laptop"
          ],

        ];

        $this->fields=$this->hideItems();
    }
    

    
    private function hideItems()
    {
        $items=[
            "object_icon_position",
            "label_horizontal_align",
            "label_vertical_align",
            "border", "border_color_type","border_size_type",
            "actions","actions_position", "actions_vertical_align",
            "show_actions" , "online_status",
            "show_online_status_icon","recommendation" ,
            "show_recommend_icon", "recommend_mail_template_type",
            "learning_progress" , "learning_progress_disabled_hint",
            "show_learning_progress" ,"learning_progress_position",
            "show_learning_progress_legend","show_learning_progress_filter" ,
            "preconditions" , "show_preconditions", "certificate",
            "certificate_hint" ,"show_download_certificate","language" ,
            "show_language_flag","language_flag_position",
            "image_position","show_image_as_background",
            
            
        ];



        if (self::srTile()->config()->getHomeRefId()==$this->tile->getObjRefId()) {
            array_push($items, "image_header", "image", "branch_topic", "branch", "topic");
        }
        
        foreach ($items as $item) {
            unset($this->fields[$item]);
        }
        if (self::srTile()->config()->getHomeRefId()!=$this->tile->getObjRefId()) {
            unset($this->fields["background_image"]);
        }


        return $this->fields;
    }

    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {
        $this->setId("tile_form");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->tile->_getTitle());
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        if (empty($this->tile->getTileId())) {
            self::srTile()->tiles()->storeTile($this->tile);
        }

        if (!parent::storeForm()) {
            return false;
        }

        self::srTile()->tiles()->storeTile($this->tile);

        return true;
    }


    /**
     * @inheritDoc
     */
    public function checkInput() : bool
    {
        if (intval(filter_input(INPUT_POST, "view") === Tile::VIEW_DISABLED)) {
            // Allows incomplete configuration if the tile is disabled
            parent::checkInput();

            return true;
        } else {
            return parent::checkInput();
        }
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value) /*: void*/
    {
        switch ($key) {
            case "columns_count":
                if ($this->tile->getColumnsType() === Tile::SIZE_TYPE_COUNT) {
                    Items::setter($this->tile, "columns", $value);
                }
                break;

            case "columns_fix_width":
                if ($this->tile->getColumnsType() === Tile::SIZE_TYPE_PX) {
                    Items::setter($this->tile, "columns", $value);
                }
                break;
            

            case "image":
                if (!self::dic()->upload()->hasBeenProcessed()) {
                    self::dic()->upload()->process();
                }

                /** @var UploadResult $result */
                $result = array_pop(self::dic()->upload()->getResults());

                if ($this->getInput("image_delete") || $result->getSize() > 0) {
                    $this->tile->applyNewImage("");
                }

                if (intval($result->getSize()) === 0) {
                    break;
                }

                $file_name = $this->tile->getTileId() . "." . pathinfo($result->getName(), PATHINFO_EXTENSION);

                self::dic()->upload()->moveOneFileTo($result, $this->tile->getImagePathAsRelative(false), Location::WEB, $file_name, true);

                Items::setter($this->tile, $key, $file_name);

                $this->tile->_getImageDominantColor();
                break;
            case "background_image":
                if (!self::dic()->upload()->hasBeenProcessed()) {
                    self::dic()->upload()->process();
                }

                /** @var UploadResult $result */
                $result = array_pop(self::dic()->upload()->getResults());
                if ($this->getInput("image_delete") || $result->getSize() > 0) {
                    $this->tile->applyNewBackgroundImage("");
                }
                if (intval($result->getSize()) === 0) {
                    $logger=self::dic()->logger()->root();
                    $logger->info("image Not found...");
                    break;
                }

                $file_name = "background_image." . pathinfo($result->getName(), PATHINFO_EXTENSION);

                self::dic()->upload()->moveOneFileTo($result, $this->tile->getBackgroundImagePathAsRelative(false), Location::WEB, $file_name, true);

                Items::setter($this->tile, $key, $file_name);
                break;

            default:
                Items::setter($this->tile, $key, $value);
                break;
        }
    }
}
