<?php

namespace minervis\ToGo\Tile;

use ilFormSectionHeaderGUI;
use ILIAS\FileUpload\DTO\UploadResult;
use ILIAS\FileUpload\Location;
use ilImageFileInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilToGoPlugin;
use minervis\ToGo\Utils\ToGoTrait;
use ilCheckboxInputGUI;
use ilTextInputGUI;
use ilUIPluginRouterGUI;
use ilPropertyFormGUI;
use TypeError;

/**
 * Class TileFormGUI
 *
 * @package minervis\ToGo\Tile
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 *
 * @ilCtrl_isCalledBy TileFormGUI: ilUIPluginRouterGUI, PropertyFormGUI, TileGUI
 * @ilCtrl_Calls ilUIPluginRouterGUI: TileFormGUI, ilLinkInputGUI
 *
 */
class TileFormGUI 
{
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const LANG_MODULE = TileGUI::LANG_MODULE;
    /**
     * @var Tile
     */
    protected $tile;
    protected $parent;
    private static $instance = null;


    /**
     * TileFormGUI constructor
     *
     * @param TileGUI $parent
     * @param Tile                      $tile
     */
    public function __construct($parent, Tile $tile)
    {
        $this->tile = $tile;
        $this->parent = $parent;
    
    }



    public function initPropertyForm()
    {
        global $ilCtrl;
        include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();

        $form->setTitle($this->tile->_getTitle());
        $form->setFormAction(self::ildic()->ctrl()->getFormAction($this->parent));
        

        // branches
        $branches = new ilTextInputGUI($this->txt("branches"), 'branch');
        $branches->setRequired(false);
        $branches->setValue($this->tile->getBranch());
        $form->addItem($branches);

        // topic
        $topic = new ilTextInputGUI($this->txt("topics"), 'topic');
        $topic->setRequired(false);
        $topic->setValue($this->tile->getTopic());
        $form->addItem($topic);

        $settings_title = new ilFormSectionHeaderGUI();
        $settings_title->setTitle($this->txt("general_settings"), 'general_settings');
        $form->addItem($settings_title);

        $view = new ilRadioGroupInputGUI(
            $this->txt('view'),
            'view'
        );
        $view->setRequired(false);
        $view->setValue($this->tile->getView());
        $form->addItem($view);

        $default =  new ilRadioOption(
            $this->txt("view_disabled"),
            Tile::VIEW_DISABLED
        );
        $view->addOption($default);

        $view_tile =  new ilRadioOption(
            $this->txt("view_tile"),
            Tile::VIEW_TILE 
        );
        $view->addOption($view_tile);

        $image = new ilImageFileInputGUI($this->txt("image"), 'image');
        if ($this->tile->getImage() !== '') {
            $image->setImage("./" . $this->tile->getImagePath());
            self::ildic()->logger()->root()->info("path: ". $this->tile->getImagePath());
        }
        $form->addItem($image);

        $devices = new ilFormSectionHeaderGUI();
        $devices->setTitle($this->txt("devices"), 'devices');
        $form->addItem($devices);

        $show_phone = new ilCheckboxInputGUI(
            "Phone",
            'show_phone'
        );
        $show_phone->setChecked($this->tile->getShowPhone());
        $form->addItem($show_phone);
        
        $show_laptop= new ilCheckboxInputGUI(
            "Laptop",
            'show_laptop'
        );
        $show_laptop->setChecked($this->tile->getShowLaptop());
        $form->addItem($show_laptop);

        $show_tablet= new ilCheckboxInputGUI(
            "Tablet",
            'show_tablet'
        );
        $show_tablet->setChecked($this->tile->getShowTablet());
        $form->addItem($show_tablet);

        $form->addCommandButton('saveProperties', $this->txt('save'));

        return $form;

    }

    public function saveProperties($form)
    {       
        if ( self::togo()->access()->hasWriteAccess($this->tile->getObjRefId())){
            if (!$form->checkInput()) {
                $form->setValuesByPost();
                $this->properties($form);
                return false;
            }
            if (empty($this->tile->getTileId())) {
                self::togo()->tiles()->storeTile($this->tile);
            }
            $this->storeProperty('branch', (string) $form->getInput('branch'), $form);
            $this->storeProperty('topic', (string) $form->getInput('topic'), $form);
            $this->storeProperty('show_tablet', (int) $form->getInput('show_tablet'), $form);
            $this->storeProperty('show_phone', (int) $form->getInput('show_phone'), $form);
            $this->storeProperty('show_laptop', (int) $form->getInput('show_laptop'), $form);
            $this->storeProperty('view', (int) $form->getInput('view'), $form);
            $this->storeProperty('image', '', $form);

            self::togo()->tiles()->storeTile($this->tile);
            return true ;
       }
       return false;
    }


   
    public function properties(ilPropertyFormGUI $form)
    {
        if (!$form instanceof ilPropertyFormGUI) {
            $form = $this->initPropertyForm($this->parent);
        }
        self::togoplugin()->output($form, true);
    }


   

    public function txt(string $key,/*?*/ string $default = null) : string
    {
        if ($default !== null) {
            return self::togoplugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
        } else {
            return self::togoplugin()->translate($key, static::LANG_MODULE);
        }
    }

    /**
     * 
     */
    protected function storeProperty(string $key, $value, $form) /*: void*/
    {
        switch ($key) {
            case "image":
                if (!self::ildic()->upload()->hasBeenProcessed()) {
                    self::ildic()->upload()->process();
                }

                /** @var UploadResult $result */
                $result = array_pop(self::ildic()->upload()->getResults());
                if ($result == null) break;

                if ($form->getInput("image_delete") || $result->getSize() > 0) {
                    $this->tile->applyNewImage("");
                }

                if (intval($result->getSize()) === 0) {
                    break;
                }

                $file_name = $this->tile->getTileId() . "." . pathinfo($result->getName(), PATHINFO_EXTENSION);

                self::ildic()->upload()->moveOneFileTo($result, $this->tile->getImagePathAsRelative(false), Location::WEB, $file_name, true);

                $this->setter($this->tile, $key, $file_name);

                break;
                case "view":
                    $value = $value == 0 ? 4: $value;                    
                    $this->setter($this->tile, $key, $value);
                    break;
            default:
                $this->setter($this->tile, $key, $value);
                break;
        }
    }
        /**
     * @param object $object
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function setter(/*object*/ $object, string $property, $value)
        {
            $res = null;
    
            if (method_exists($object, $method = "with" . self::strToCamelCase($property)) || method_exists($object, $method = "set" . self::strToCamelCase($property))) {
                try {
                    $res = $object->{$method}($value);
                } catch (TypeError $ex) {
                    try {
                        $res = $object->{$method}(intval($value));
                    } catch (TypeError $ex) {
                        $res = $object->{$method}(boolval($value));
                    }
                }
            }
    
            return $res;
        }
    
    /**
     * @param string $string
     *
     * @return string
     */
    public static function strToCamelCase(string $string) : string
    {
        return str_replace("_", "", ucwords($string, "_"));
    }
}
