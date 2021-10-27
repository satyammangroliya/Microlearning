<?php
include_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ToGo/classes/class.ilToGoConfig.php";
/**
 * Class ilToGoConfigGUI
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 * @version $Id$
 */
class ilToGoConfigGUI extends ilPluginConfigGUI
{
    
    
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_CONFIGURE = "configure";
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    const LANG_MODULE = "config";
    const TAB_CONFIGURATION = "configuration";

    private $dic;
    private $config;
    /**
     * ilToGoConfigGUI constructor
     */
    public function __construct()
    {
        global $DIC;
        $this->dic= $DIC;
        $this->config = ilToGoConfig::getInstance();

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/*:void*/
    {
        $this->setTabs();
        $next_class = $this->dic->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = $this->dic->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_CONFIGURE:
                    case self::CMD_UPDATE_CONFIGURE:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function setTabs()/*: void*/
    {
        $this->dic->tabs()->addTab(self::TAB_CONFIGURATION, $this->plugin_object->txt("config_configuration"), $this->dic->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_CONFIGURE));
        $this->dic['ilLocator']->addItem(ilToGoPlugin::PLUGIN_NAME, $this->dic->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    protected function configure()/*: void*/
    {
        global $tpl;
        $this->dic->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = $this->initConfigurationForm();

        $tpl->setContent($form->getHTML());;
    }

    public function initConfigurationForm()
    {
        global $ilCtrl;
        $pl = $this->getPluginObject();
        $values = $this->config->getValues();
        $form = new ilPropertyFormGUI();


        $ti = new ilTextInputGUI($pl->txt("config_base_container"), "base_container");
		$ti->setMaxLength(256);
		$ti->setSize(60);
		$ti->setValue($values["base_container"]);
		$form->addItem($ti);
		
		// Was-Sind  (text)
		$ti = new ilTextInputGUI($pl->txt("config_was_sind"), "was_sind");
		$ti->setRequired(false);
		$ti->setMaxLength(256);
		$ti->setSize(60);
		$ti->setValue($values["was_sind"]);
		$form->addItem($ti);

		// Umfrage (text)
		$ti = new ilTextInputGUI($pl->txt("config_umfrage_object"), "umfrage_object");
		$ti->setMaxLength(256);
		$ti->setSize(40);
		$ti->setValue( $values["umfrage_object"]);
		$form->addItem($ti);
	
		$form->addCommandButton(self::CMD_UPDATE_CONFIGURE, $pl->txt("config_save"));
	                
		$form->setTitle($pl->txt("config_configuration"));
		$form->setFormAction($ilCtrl->getFormAction($this));
		
		return $form;
    }

    /**
     *
     */
    protected function updateConfigure()/*: void*/
    {
        global $tpl;
        $this->dic->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = $this->initConfigurationForm();
        $values = [];
        if ($form->checkInput()) {
            $values["base_container"] =   $form->getInput("base_container");
            $values["was_sind"] = $form->getInput("was_sind");
            $values["umfrage_object"] = $form->getInput("umfrage_object");
            $this->config->setValues($values);
            $this->config->save();
            ilUtil::sendSuccess($this->plugin_object->txt("config_configuration_saved"), true);
            $this->dic->ctrl()->redirect($this, self::CMD_CONFIGURE);
        }else{
            $form->setValuesByPost();
			$tpl->setContent($form->getHtml());
        }
        
    }
}
