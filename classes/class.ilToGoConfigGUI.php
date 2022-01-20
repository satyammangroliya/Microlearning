<?php
include_once "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/ToGo/classes/class.ilToGoConfig.php";

use minervis\ToGo\Collection\AnonymousSession;
use minervis\ToGo\Collection\Collection;
use minervis\ToGo\Utils\ToGoTrait;
/**
 * Class ilToGoConfigGUI
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 * @version $Id$
 */
class ilToGoConfigGUI extends ilPluginConfigGUI
{
    
    use ToGoTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    const CMD_CONFIGURE = "configure";
    const CMD_UPDATE_CONFIGURE = "updateConfigure";
    const CMD_DEBUG = "debug";
    const LANG_MODULE = "config";
    const TAB_CONFIGURATION = "configuration";
    const TAB_DEBUG = "debug";
    const CMD_DELETE = 'delete';
    const CMD_RUN = 'run';

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
                    case self::CMD_DEBUG:
                    case self::CMD_DELETE:
                    case self::CMD_RUN:
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
        if ($this->config->isDebugMode()){
            $this->dic->tabs()->addTab(self::TAB_DEBUG, "DebugMode", $this->dic->ctrl()
            ->getLinkTargetByClass(self::class, self::CMD_DEBUG));
        }
        $this->dic['ilLocator']->addItem(ilToGoPlugin::PLUGIN_NAME, $this->dic->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    protected function configure()/*: void*/
    {
        global $tpl;
        //$this->dic->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = $this->initConfigurationForm();

        $tpl->setContent($form->getHTML());
    }

    public function debug()
    {
        global $tpl,$DIC;
        //$this->dic->tabs()->activateTab(self::TAB_DEBUG);
        $this->config->writeLog('');

        $debug_link=self::ildic()->ctrl()->getLinkTargetByClass(self::class,self::CMD_RUN);

        $f = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();
        $logs = nl2br(file_get_contents($this->config->getLog()));
        $download_button = $renderer->render($f->button()->standard("Download debug logs", $this->config->getLog()));

        $run_button = $renderer->render($f->button()->standard("debug", $debug_link));
        $html =  "<div>". $download_button . $run_button ." </div>" . "<div>" . substr($logs, -2300) ."</div>" ;
        $tpl->setContent($html);
    }
    public function run(){
        $this->dic->tabs()->activateTab(self::TAB_DEBUG);
        $this->test_clean_detached();
        $this->test_database();
        if ($this->config->getStage() == 6){
            $this->test_server_response();
        }
        $this->dic->ctrl()->redirect($this, self::CMD_DEBUG);

    }

    public function initDebugForm()
    {
        global $ilCtrl;
        $pl = $this->getPluginObject();
        $values = $this->config->getValues();
        $form = new ilPropertyFormGUI();
		$form->addCommandButton(self::CMD_RUN, 'debug');
	                
		//$form->setTitle($pl->txt("config_configuration"));
		$form->setFormAction($ilCtrl->getFormAction($this));
		
		return $form;
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
        // Umfrage (text)
		$debug = new ilCheckboxInputGUI('Debug mode', "debug");
		$debug->setChecked( (int) intval($values["debug"]));
		

        $stage = new ilRadioGroupInputGUI(
            'Debug stage',
            'stage'
        );
        $stage->setRequired(false);
        $stage->setValue(intval($this->config->getStage()));
        //$form->addItem($stage);

        $default =  new ilRadioOption(
            'Stage 1',
            0,
            'Basic stage: HTML vom Plugin wird generiert und gerendert'
        );
        $stage->addOption($default);

        $stage2 =  new ilRadioOption(
            'Stage 2',
            1,
            'HTML vom Plugin wird nicht generiert und nichts wird gerendert'
        );
        $stage->addOption($stage2);
        $stage3 =  new ilRadioOption(
            'Stage 3',
            2,
            'HTML vom Plugin wird nicht generiert und HTML ohne Plugin'
        );
        $stage->addOption($stage3);
        $stage4 =  new ilRadioOption(
            'fatal errors',
            3,
            'Outsource Memory/time'
        );
        $stage->addOption($stage4);
        $stage5 =  new ilRadioOption(
            'Dauer von Linkgenerierung messen',
            4
        
        );
        $stage->addOption($stage5);
        $stage6 =  new ilRadioOption(
            'Call trace',
            5
        
        );
        $stage->addOption($stage6);
        $stage7 =  new ilRadioOption(
            'Server response(local)',
            6
        
        );
        $stage->addOption($stage7);
        
        $debug->addSubItem($stage);
        $form->addItem($debug);
	
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
        global $DIC;
        $this->dic->tabs()->activateTab(self::TAB_CONFIGURATION);

        $form = $this->initConfigurationForm();
        $values = [];
        if ($form->checkInput()) {
            $values["base_container"] =   $form->getInput("base_container");
            $values["was_sind"] = $form->getInput("was_sind");
            $values["umfrage_object"] = $form->getInput("umfrage_object");
            $values["debug"] = intval($form->getInput("debug"));
            $values ['stage'] = intval($form->getInput("stage"));
            $this->config->setValues($values);
            $this->config->save();
            ilUtil::sendSuccess($this->plugin_object->txt("config_configuration_saved"), true);
            $this->dic->ctrl()->redirect($this, self::CMD_CONFIGURE);
        }else{
            $form->setValuesByPost();
			$tpl->setContent($form->getHTML());
        }
        
    }

    function call_trace()
    {
        debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    function call_parent_container()
    {
        

    }

    function test_database()
    {
        global $ilDB;
        $this->config->writeLog("---------------Test Database access--------------- ");
        //$query = "SELECT DISTINCT(tiles.tile_id) FROM " . "ui_uihk_" . ilToGoPlugin::PLUGIN_ID . "_tile as tiles" ;
        $tiles = self::togo()->tiles()->getTiles();
        $tile_ids= array_map(function($tile) {
            return $tile->getTileId();
        }, $tiles);
        
       
        $this->config->writeLog("total number of distinct tiles in the DB ". count(array_unique(array_filter($tile_ids))));

        //total rows
        $this->config->writeLog("total number of all tiles in the DB ". count($tiles));

        //sessions
        $session_query = AnonymousSession::get() ;
        $this->config->writeLog("total number of Anonymous sessions in the DB ". count($session_query));
        //sessions
        $coll_query = Collection::get() ;
        $this->config->writeLog("total number of collection items ". count($coll_query));
        
        
    }

    public function test_clean_detached(){
        
    }

    function test_server_response()
    {
        $this->config->writeLog("---------------Test Server response--------------- ");
        $starttime = microtime(true);
        
        $response = 'No response';
        $link = ILIAS_HTTP_PATH . '/goto.php?target=cat_' . $this->config->getHomeRefId() . '&client_id='
        . CLIENT_ID;

        //$link = 'https://ilias.bgn-akademie.de/goto.php?target=cat_5338&client_id=bgnakademie';
        $response = file_get_contents($link, false, stream_context_create(array('https'=>
                    array(
                        'timeout' => 1200,  //1200 Seconds is 20 Minutes
                    )
                ))); 

        $stoptime = microtime(true);
        $now = DateTime::createFromFormat('U.u', $stoptime);
        $formatted_stoptime = $now->format("m-d-Y H:i:s.u");
        $server_request_time = DateTime::createFromFormat('U.u', $_SERVER['REQUEST_TIME_FLOAT']);
        $this->config->writeLog("Server Request time: ". $server_request_time->format("m-d-Y H:i:s.u"));
        $this->config->writeLog("First Server response time: " . $formatted_stoptime);
        $this->config->writeLog("First Server response (total duration): " . ($stoptime-$starttime)*1000);
  
    }

    public function delete(){

    }

}
