<?php
/**
 * css_javascript_toolbox Plugin 
 * 
 * @package blesta
 * @subpackage blesta.plugins.CssJavascriptToolbox
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */

class Toolbox extends CssJavascriptToolboxModel {
	
	/**
	 * Initialize
	 */
	public function __construct() {
		parent::__construct();
		
		// Set the company ID
		$this->company_id = Configure::get("Blesta.company_id");		
		$this->dir = PLUGINDIR ."css_javascript_toolbox/includes/" . $this->company_id . "/";
		Language::loadLang("model", null, PLUGINDIR . "css_javascript_toolbox" . DS . "language" . DS);
	}
		
	/**
	 * Simple View List
	 */			
	public function SelectPages() {
		return array(
			'all' => Language::_("CssJavascriptToolboxPlugin.root.all", true),
			'client_area' => Language::_("CssJavascriptToolboxPlugin.root.client_area", true),
			'admin_area' => Language::_("CssJavascriptToolboxPlugin.root.admin_area", true)			
			// 'client_login' => Language::_("CssJavascriptToolboxPlugin.root.login_form_client", true),
			// 'admin_login' => Language::_("CssJavascriptToolboxPlugin.root.login_form_staff", true),
			// 'order' => Language::_("CssJavascriptToolboxPlugin.root.order", true),
			// 'support_manager' => Language::_("CssJavascriptToolboxPlugin.root.support_manager", true),
			// 'cms' => Language::_("CssJavascriptToolboxPlugin.root.cms", true)
		);
		// 
	}

	/**
	 * Advanced View List
	 */			
	public function SelectSection() {
		return array(
			'head' => Language::_("CssJavascriptToolboxPlugin.root.head", true),
			'body_start' => Language::_("CssJavascriptToolboxPlugin.root.body_start", true),
			'body_end' => Language::_("CssJavascriptToolboxPlugin.root.body_end", true)
		);
		// 
	}	
	
	/**
	 * Controller List
	 */			
	public function ListControllers() {
		$result = null;
		$controller_name = null;
		
		// Scan Core Controllers
		$controller_dir = array_diff(scandir(CONTROLLERDIR), array('..', '.'));	
		// Begin Core Controllers List
		$controller_name[] = Language::_("CssJavascriptToolboxPlugin.root.system", true);
		
		foreach ($controller_dir as $dirfile) {
			$file_path = CONTROLLERDIR . $dirfile ; 
			$controller =  strstr($dirfile, '.', true); 
			
			if (file_exists($file_path)) {
				if (!in_array($controller, $this->ExcludeControllers())) {
					require_once($file_path);
					$class_methods = get_class_methods(Loader::toCamelCase($controller));
					if (is_array($class_methods)) {
						foreach ($class_methods as $method_name => $value) {
							if (!in_array($value, $this->ExcludeFunctions())) {
							
								$action = "_" . strtolower($value) ;
								
								if ($controller == "admin_main" AND $action == "_index")
									$controller_name["admin"] = Language::_("CssJavascriptToolboxPlugin.root.admin_dashboard", true) ;
									
								else if ($controller == "client_main" AND $action == "_index")
									$controller_name["client"] = Language::_("CssJavascriptToolboxPlugin.root.client_dashboard", true) ;
									
								else if ($action == "_index")
									$controller_name[$controller] = $controller ;
									
								else
									$controller_name[$controller . $action] = $controller . $action ;
							}
						}
					}
				}	
			}
		}	
		
		// Scan Plugins Controllers
		// $plugins_dir = array_diff(scandir(PLUGINDIR), array('..', '.'));
		// foreach ($plugins_dir as $dirfile) {
			// $file_path = PLUGINDIR . $dirfile . DS . "controllers"; 
			// $controller =  strstr($dirfile, '.', true); 
			// $controller_name[] .= Language::_("CssJavascriptToolboxPlugin.root.system", true);
			// if (file_exists($file_path)) {
				// if (!in_array($controller, $this->ExcludeControllers())) {
					// require_once($file_path);
					// $class_methods = get_class_methods(Loader::toCamelCase($controller));
						// if (is_array($class_methods)) {
							// foreach ($class_methods as $method_name => $value) {
								// if (!in_array($value, $this->ExcludeFunctions()))
									// $controller_name[$controller ."_". $value] = $controller ."_". $value;
							// }
						// }				
					
				// }	
			// }
		// }
		
		return  $controller_name ;
	}
	
	/**
	 * Exclude public Functions from Controller list
	 */			
	private function ExcludeFunctions() {
		return array("preAction", "__construct", "init" ,"postAction", "reorderWidgets", "toggleWidget", "getWidgets" );
	}
	
	/**
	 * Exclude Controllers from Controller list
	 */		
	private function ExcludeControllers() {
		return array("main", "api", "callback", "install", "cron", "404", "uploads");
	}	
		
	/**
	 * List JS/CSS files in the admin list page
	 */				
	public function GetFiles() {		

		$data = array_diff(scandir($this->dir), array('..', '.'));
		$files = array();
		$i=0 ; 
		// for ($i=0; $i<count($data); $i++) {
		foreach ($data as $filename) {			
			$files[$i]['section'] = strstr($filename, '.', true) ;
			$files[$i]['ext'] = pathinfo($filename, PATHINFO_EXTENSION);  
			$files[$i]['name'] = $filename ;
			$files[$i]['last_update'] = date ("F d Y H:i:s.", filemtime($this->dir . $filename));
			$i++;
		}	
		
		return $files ;
		// 
	}	
	
	/**
	 * Add CSS
	 */		
	public function AddCss($vars) {
		$this->Input->setRules($this->getRules($vars, false));
		
		if ($this->Input->validates($vars)) {
		
			if ($vars['type'] == "simple") 
				$file_name = $vars['section'] .".". $vars['pages'] .".css";
			else 
				$file_name = $vars['section'] .".". $vars['controllers'] .".css";
			// Write the contents back to the file
			file_put_contents($this->dir . $file_name, $vars['body']."\n" , FILE_APPEND | LOCK_EX);
		}
		// 
	}		
	
	/**
	 * Add JS
	 */		
	public function AddJs($vars) {
		$this->Input->setRules($this->getRules($vars, false));
		
		if ($this->Input->validates($vars)) {
		
			if ($vars['type'] == "simple") 
				$file_name = $vars['section'] .".". $vars['pages'] .".js";
			else 
				$file_name = $vars['section'] .".". $vars['controllers'] .".js";		
			// Write the contents back to the file
			file_put_contents($this->dir . $file_name, $vars['body']."\n" , FILE_APPEND | LOCK_EX);
		}
		// 
	}
	
	/**
	 * Add HTML
	 */		
	public function AddHtml($vars) {
		$this->Input->setRules($this->getRules($vars, false));
		
		if ($this->Input->validates($vars)) {
			
			if ($vars['type'] == "basic") 
				$file_name = $vars['section'] .".". $vars['pages'] .".html";
			else 
				$file_name = $vars['section'] .".". $vars['controllers'] .".html";		
			// Write the contents back to the file
			file_put_contents($this->dir . $file_name, $vars['body']."\n" , FILE_APPEND | LOCK_EX);
		}
		// 
	}		
	
	/**
	 * Add PHP
	 */		
	public function AddPhp($vars) {
		$this->Input->setRules($this->getRules($vars, false));
		
		if ($this->Input->validates($vars)) {
		
			// $dir = PLUGINDIR ."css_javascript_toolbox/includes/" . $this->company_id . "/";
			if ($vars['type'] == "simple") 
				$file_name = $vars['section'] .".". $vars['pages'] .".php";
			else 
				$file_name = $vars['section'] .".". $vars['controllers'] .".php";		
			// Write the contents back to the file
			file_put_contents($this->dir . $file_name, $vars['body']."\n" , FILE_APPEND | LOCK_EX);
		}
		// 
	}	

	/**
	 * Get File Info
	 */	
	public function GetFile($vars) {
		$file = array() ;
		
		if (file_exists($this->dir . $vars)) {
	
			$file['section'] = strstr($vars, '.', true) ;
			$file['ext'] = pathinfo($vars, PATHINFO_EXTENSION);  
			$file['name'] = $vars ;
			$file['body'] = file_get_contents($this->dir . $vars);
			$file['controllers'] = substr(strrchr(basename($vars, ".".$file['ext']), '.'), 1)  ; // $file['type']; 
		}
		
		return $file ;		
	}
	
	/**
	 * Edit File
	 */	
	public function editFile($vars , $file_name) {
	
		$ext  = pathinfo($file_name, PATHINFO_EXTENSION); 
		// $old_file = $file_name ;
		
		if ($vars['type'] == "simple") 
			$new_file = $vars['section'] .".". $vars['pages'] . "." . $ext;
		else 
			$new_file = $vars['section'] .".". $vars['controllers'] ."." . $ext;	
	
		$this->Input->setRules($this->getRules($vars, false));
		
		if ($this->Input->validates($vars)) {
			
			if ($file_name == $new_file )
				file_put_contents($this->dir . $file_name , $vars['body']."\n" , LOCK_EX);
				
			else {
				file_put_contents($this->dir . $new_file, $vars['body']."\n" , FILE_APPEND | LOCK_EX);
				// remove the old file
				unlink($this->dir . $file_name); 
			}	
		}
	}
	
	/**
	 * Delete File
	 */	
	public function deleteFile($file_name) {
	
		if (file_exists($this->dir . $file_name))
			unlink($this->dir . $file_name);	
		
	}	
	
	/**
	 * Render CSS & JS in HTML
	 */		
	public function ShowContent($file_name , $ext) {
		switch ($ext) {
			case ".css":
				$return = "<style type=\"text/css\">". file_get_contents($file_name) ."</style>";
				break;
			case ".js":
				$return = "<script type=\"text/javascript\"> ". file_get_contents($file_name) ."</script>";
				break;
			case ".html":
				$return =  file_get_contents($file_name) ;
				break;
			case ".php":
				$return =  include_once($file_name) ;
				break;						
		}
		return 	$return ; 
	}

	/**
	 * Retrieves a list of rules to validate add/editing files
	 *
	 * @param array $vars A list of input vars to validate
	 * @param array $files A list of files in the format of post data $_FILES
	 * @param boolean $edit True to fetch the edit rules, false to fetch the add rules (optional, default false)
	 * @return array A list of rules
	 */
	private function getRules(array $vars, $edit = false) {
		$rules = array(
			'company_id' => array(
				'exists' => array(
					'rule' => array(array($this, "validateExists"), "id", "companies"),
					'message' => $this->_("CssJavascriptToolboxPlugin.!error.company_id.exists")
				)
			),
			'pages' => array(
				'format' => array(
					'if_set' => true,
					'rule' => array("in_array", array("all", "client_area","admin_area")),
					'message' => $this->_("CssJavascriptToolboxPlugin.!error.pages.empty")
				)			
			),
			'section' => array(
				'empty' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => $this->_("CssJavascriptToolboxPlugin.!error.section.format")
				)
			),	
			'body' => array(
				'empty' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => $this->_("CssJavascriptToolboxPlugin.!error.body.empty")
				)
			)
			// ,			
			// 'file_name' => array(
				// 'format' => array(
					// 'rule' => array(array($this, "validateFile"), $files),
					// 'message' => $this->_("CssJavascriptToolboxPlugin.!error.file_name.format")
				// )
			// )
		);
		
		if ($edit) {
			// Update rules, check that the file exists
			// $rules['file_name'] = array(
				// 'exists' => array(
					// 'rule' => array(array($this, "validateExists"), "file_name", "download_files"),
					// 'message' => $this->_("CssJavascriptToolboxPlugin.!error.file_name.exists")
				// )
			// );
		}
		
		return $rules;
	}	




	
	
	
	
	
	/**
	 * Converts the given file name into an appropriate file name to store to disk
	 *
	 * @param string $file_name The name of the file to rename
	 * @return string The rewritten file name in the format of YmdTHisO_[hash][ext] (e.g. 20121009T154802+0000_1f3870be274f6c49b3e31a0c6728957f.txt)
	 */
	public function makeFileName($file_name) {
		$ext = strrchr($file_name, ".");
		$file_name = md5($file_name . uniqid()) . $ext;
		
		return $this->dateToUtc(date("c"), "Ymd\THisO") . "_" . $file_name;
	}	
	
	/**
	 * Returns a suggested file name with a number appended at the end so that it is unique in the upload path
	 *
	 * @param string $file_name The name of the file to append a count to
	 * @return string The suggested file name
	 */
	public function appendCount($file_name) {
		$new_file_name = $file_name;
		$ext = strrchr($file_name, ".");
		$file_name_no_ext = substr($file_name, 0, -strlen($ext));
		
		for ($i=1; file_exists($this->upload_path . $new_file_name); $i++) {
			$new_file_name = $file_name_no_ext . $i . $ext;
		}
		return $new_file_name;
	}
	
		
}
?>