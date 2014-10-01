<?php
/**
 * css_javascript_toolbox Plugin 
 * 
 * @package blesta
 * @subpackage blesta.plugins.CssJavascriptToolbox
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class CssJavascriptToolboxPlugin extends Plugin {

	public function __construct() {
		Language::loadLang("admin", null, dirname(__FILE__) . DS . "language" . DS);
		
		// Load components required by this plugin
		Loader::loadComponents($this, array("Input", "Record"));
		
        // Load modules for this plugun
        Loader::loadModels($this, array("ModuleManager"));
		$this->loadConfig(dirname(__FILE__) . DS . "config.json");
		$this->upload_path = PLUGINDIR ."css_javascript_toolbox/includes/" .  Configure::get("Blesta.company_id") . DS;
	}
	
	/**
	 * Performs any necessary bootstraping actions
	 *
	 * @param int $plugin_id The ID of the plugin being installed
	 */
	public function install($plugin_id) {	
			
		// Add the system overview table, *IFF* not already added
		try {
			// Set the working css directory 
			Loader::loadComponents($this, array("Upload"));
			$this->Upload->createUploadPath($this->upload_path, 0777);
			
		}
		catch(Exception $e) {
			// Error adding... no permission?
			// $this->Input->setErrors(array('db'=> array('create'=>$e->getMessage())));
			return;
		}
	}
	
    /**
     * Performs migration of data from $current_version (the current installed version)
     * to the given file set version
     *
     * @param string $current_version The current installed version of this plugin
     * @param int $plugin_id The ID of the plugin being upgraded
     */
	public function upgrade($current_version, $plugin_id) {
		
		// Upgrade if possible
		if (version_compare($this->getVersion(), $current_version, ">")) {
			// Handle the upgrade, set errors using $this->Input->setErrors() if any errors encountered
		}
	}
	
	/**
     * Performs any necessary cleanup actions
     *
     * @param int $plugin_id The ID of the plugin being uninstalled
     * @param boolean $last_instance True if $plugin_id is the last instance across all companies for this plugin, false otherwise
     */
	public function uninstall($plugin_id, $last_instance) {
		if (!isset($this->Record))
			Loader::loadComponents($this, array("Record"));
		
		// Remove all tables *IFF* no other company in the system is using this plugin
		if ($last_instance) {
			try {			
				if (is_dir($this->upload_path)) {
					$this->delTree($this->upload_path);
				}			
			}
			catch (Exception $e) {
				// Error dropping... no permission?
				// $this->Input->setErrors(array('db'=> array('create'=>$e->getMessage())));
				return;
			}
		}
 
	}
 
    public function getEvents() {
        return array(
            array(
                'event' => "Appcontroller.structure",
                'callback' => array("this", "StructureToolbox")
            ), 
			array(
                'event' => "Appcontroller.preAction",
                'callback' => array("this", "preActionToolbox")
            )
            // Add multiple events here
        );
    }
	
    public function preActionToolbox($event) {
		// Nothing TODO NOW .		
	}	
 
    public function StructureToolbox($event) {
	
		Loader::loadModels($this, array("CssJavascriptToolbox.Toolbox"));
		
		$params = $event->getParams();
		$return = $event->getReturnVal();
		
		if (empty($params['action']))
			$action = $params['action'] ;
		else 
			$action = "_". $params['action'] ;
			
		$controller =  $params['controller'] ;
		
		$extention = array (".css", ".js" ,".html" , ".php");
		

			
        // Set return val if not set
        if (!isset($return['head']))
                $return['head'] = null;
				
        if (!isset($return['body_start']))
                $return['body_start'] = null;

        if (!isset($return['body_end']))
                $return['body_end'] = null;
				
				
		foreach ($extention as $ext) {				
			if (file_exists($file_name = $this->upload_path . "head.all" . $ext ))
				$return['head']['toolbox.head.all']= $this->Toolbox->ShowContent($file_name , $ext);
			
			if (file_exists($file_name = $this->upload_path . "body_start.all" . $ext ))
				$return['body_start'] ['toolbox.body_start.all']= $this->Toolbox->ShowContent($file_name , $ext);
			
			if (file_exists($file_name = $this->upload_path . "body_end.all" . $ext ))
				$return['body_end'] ['toolbox.body_end.all']= $this->Toolbox->ShowContent($file_name , $ext);
		}
			
        if ($params['portal'] == "client") {
			foreach ($extention as $ext) {				
				if (file_exists($file_name = $this->upload_path . "head.client_area" . $ext ))
					$return['head'] ['toolbox.head.client_area']= $this->Toolbox->ShowContent($file_name , $ext);
				
				if (file_exists($file_name = $this->upload_path . "body_start.client_area" . $ext ))
					$return['body_start'] ['toolbox.body_start.client_area']= $this->Toolbox->ShowContent($file_name , $ext);
				
				if (file_exists($file_name = $this->upload_path . "body_end.client_area" . $ext ))
					$return['body_end'] ['toolbox.body_end.client_area']= $this->Toolbox->ShowContent($file_name , $ext);
			}
		}
		
        if ($params['portal'] == "admin") {
			foreach ($extention as $ext) {				
				if (file_exists($file_name = $this->upload_path . "head.admin_area" . $ext ))
					$return['head'] ['toolbox.all.admin_area']= $this->Toolbox->ShowContent($file_name , $ext);
				
				if (file_exists($file_name = $this->upload_path . "body_start.admin_area" . $ext ))
					$return['body_start'] ['toolbox.body_start.admin_area']= $this->Toolbox->ShowContent($file_name , $ext);
				
				if (file_exists($file_name = $this->upload_path . "body_end.admin_area" . $ext ))
					$return['body_end'] ['toolbox.body_end.admin_area']= $this->Toolbox->ShowContent($file_name , $ext);
			}
		}				
		
		foreach ($extention as $ext) {
			
			if (file_exists($file_name = $this->upload_path . "head.". $controller . $action . $ext ))
				$return['head']['toolbox.all.' . $controller . $action]= $this->Toolbox->ShowContent($file_name , $ext);
			
			if (file_exists($file_name = $this->upload_path . "body_start.". $controller . $action . $ext ))
				$return['body_start'] ['toolbox.body_start.' . $controller . $action]= $this->Toolbox->ShowContent($file_name , $ext);
			
			if (file_exists($file_name = $this->upload_path . "body_end.". $controller . $action . $ext ))
				$return['body_end'] ['toolbox.body_end.' . $controller . $action]= $this->Toolbox->ShowContent($file_name , $ext);
		}
		$event->setReturnVal($return);		
	}
	
	/**
	 * Returns all actions to be configured for this widget (invoked after install() or upgrade(), overwrites all existing actions)
	 *
	 * @return array A numerically indexed array containing:
	 * 	-action The action to register for
	 * 	-uri The URI to be invoked for the given action
	 * 	-name The name to represent the action (can be language definition)
	 */
	public function getActions() {

	}

	
	/**
	 * Execute the cron task
	 *
	 */

	public function cron($key) {
		// Todo a task 
	}

	
	/**
	 * Attempts to add new cron tasks for this plugin
	 *
	 */

	private function addCronTasks(array $tasks) {
		// TODO
	}	

	/**
	 * Delete Company Direcotry OF files
	 *
	 */	 
	private function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
		  (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
	
}
?>