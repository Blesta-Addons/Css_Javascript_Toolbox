<?php
/**
 * css_javascript_toolbox Plugin 
 * 
 * @package blesta
 * @subpackage blesta.plugins.CssJavascriptToolbox
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class AdminManagePlugin extends AppController {
	
	/**
	 * Performs necessary initialization
	 */
	private function init() {
		// Require login
		$this->parent->requireLogin();
		// Set the company ID
		$this->company_id = Configure::get("Blesta.company_id");
		
		$this->uses(array("CssJavascriptToolbox.Toolbox"));
		$this->Date = $this->parent->Date;
		// Set the plugin ID
		$this->plugin_id = (isset($this->get[0]) ? $this->get[0] : null);
		$this->dir = PLUGINDIR ."css_javascript_toolbox/includes/" . $this->company_id . "/";
		// Set the page title
		$this->parent->structure->set("page_title", Language::_("CssJavascriptToolboxPlugin." . Loader::fromCamelCase($this->action ? $this->action : "index") . ".page_title", true));
		
		// Set the view to render for all actions under this controller
		$this->view->setView(null, "CssJavascriptToolbox.default");		
		
		// $directory = CONTROLLERDIR ;
		
		
	}
	
	/**
	 * Returns the view to be rendered when managing this plugin
	 */
	public function index() {
		$this->init();
		// Set vars
		$vars = array(
			'plugin_id' => $this->plugin_id ,
			'files' =>  $this->Toolbox->GetFiles()
		);

		// Set the view to render
		return $this->partial("admin_manage_plugin", $vars);		

	}
	
	/**
	 * Add CSS
	 */	
	public function addCss() {
		$this->init();
		// Set vars
		$vars = array(
			'plugin_id' => $this->plugin_id,
			'ListControllers' => $this->Toolbox->ListControllers(),
			'SelectPages' => $this->Toolbox->SelectPages(),
			'SelectSection' => $this->Toolbox->SelectSection()
		);
		
		
		if (!empty($this->post)) {
			// Set the category this file is to be added in
			$data = array(
				'plugin_id' => $this->plugin_id,
				'company_id' => $this->company_id,
				'pages' => $this->post['pages'],
				'type' => $this->post['type'],
				'section' => $this->post['section'],
				'controllers' => $this->post['controllers'],
				'body' => $this->post['body']
			);
			
			// Set vars according to selected items
			if (isset($this->post['type']) && $this->post['type'] == "advanced")
				$data['pages'] = null ;			

			// Add the Css
			$this->Toolbox->AddCss($data);
			
			if (($errors = $this->Toolbox->errors())) {
				// Error, reset vars	
				$vars['vars'] = (object)$this->post;				
				$this->parent->setMessage("error", $errors);
			}
			else {
				// Success
				$this->parent->flashMessage("message", Language::_("CssJavascriptToolbox.!success.css_added", true , $this->Html->ifSet($this->post['section'])));
				$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");
			}
		}
		
		// Set the view to render
		return $this->partial("addcss", $vars);
	}

	/**
	 * Add JS
	 */
	 
	public function addJs() {
		$this->init();

		// Set vars
		$vars = array(
			'plugin_id' => $this->plugin_id,
			'ListControllers' => $this->Toolbox->ListControllers(),
			'SelectPages' => $this->Toolbox->SelectPages(),
			'SelectSection' => $this->Toolbox->SelectSection()
		);
		
		
		if (!empty($this->post)) {
			// Set the category this file is to be added in
			$data = array(
				'plugin_id' => $this->plugin_id,
				'company_id' => $this->company_id,
				'pages' => $this->post['pages'],
				'type' => $this->post['type'],
				'section' => $this->post['section'],
				'controllers' => $this->post['controllers'],
				'body' => $this->post['body']
			);
			
			// Set vars according to selected items
			if (isset($this->post['type']) && $this->post['type'] == "advanced")
				$data['pages'] = null ;
				
			// Add the Js
			$this->Toolbox->AddJs($data);
			
			if (($errors = $this->Toolbox->errors())) {
				// Error, reset vars	
				$vars['vars'] = (object)$this->post;				
				$this->parent->setMessage("error", $errors);
			}
			else {
				// Success
				$this->parent->flashMessage("message", Language::_("CssJavascriptToolbox.!success.js_added", true , $this->Html->ifSet($this->post['section'])));
				$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");
			}
		}		

		// Set the view to render
		return $this->partial("addjs", $vars);
	}
	
	/**
	 * Add HTML
	 */
	 
	public function addHtml() {
		$this->init();

		// Set vars
		$vars = array(
			'plugin_id' => $this->plugin_id,
			'ListControllers' => $this->Toolbox->ListControllers(),
			'SelectPages' => $this->Toolbox->SelectPages(),
			'SelectSection' => array_diff($this->Toolbox->SelectSection(), array(Language::_("CssJavascriptToolboxPlugin.root.head", true)))
		);
		
		
		if (!empty($this->post)) {
			// Set the category this file is to be added in
			$data = array(
				'plugin_id' => $this->plugin_id,
				'company_id' => $this->company_id,
				'pages' => $this->post['pages'],
				'type' => $this->post['type'],
				'section' => $this->post['section'],
				'controllers' => $this->post['controllers'],
				'body' => $this->post['body']
			);
			
			// Set vars according to selected items
			if (isset($this->post['type']) && $this->post['type'] == "advanced")
				$data['pages'] = null ;

			// Add the HTML
			$this->Toolbox->AddHtml($data);
			
			if (($errors = $this->Toolbox->errors())) {
				// Error, reset vars	
				$vars['vars'] = (object)$this->post;				
				$this->parent->setMessage("error", $errors);
			}
			else {
				// Success
				$this->parent->flashMessage("message", Language::_("CssJavascriptToolbox.!success.html_added", true , $this->Html->ifSet($this->post['section'])));
				$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");
			}
		}		

		// Set the view to render
		return $this->partial("addhtml", $vars);
	}
	
	/**
	 * Add PHP
	 */
	public function addPhp() {
		$this->init();

		// Set vars
		$vars = array(
			'plugin_id' => $this->plugin_id,
			'ListControllers' => $this->Toolbox->ListControllers(),
			'SelectPages' => $this->Toolbox->SelectPages(),
			'SelectSection' => $this->Toolbox->SelectSection()
		);
		
		
		if (!empty($this->post)) {
			// Set the category this file is to be added in
			$data = array(
				'plugin_id' => $this->plugin_id,
				'company_id' => $this->company_id,
				'pages' => $this->post['pages'],
				'type' => $this->post['type'],
				'section' => $this->post['section'],
				'controllers' => $this->post['controllers'],
				'body' => $this->post['body']
			);
			
			// Set vars according to selected items
			if (isset($this->post['type']) && $this->post['type'] == "advanced")
				$data['pages'] = null ;

			// Add the HTML
			$this->Toolbox->AddPhp($data);
			
			if (($errors = $this->Toolbox->errors())) {
				// Error, reset vars	
				$vars['vars'] = (object)$this->post;				
				$this->parent->setMessage("error", $errors);
			}
			else {
				// Success
				$this->parent->flashMessage("message", Language::_("CssJavascriptToolbox.!success.php_added", true , $this->Html->ifSet($this->post['section'])));
				$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");
			}
		}		

		// Set the view to render
		return $this->partial("addphp", $vars);
	}	

	/**
	 * Edit FIle
	 */
	public function edit() {
		$this->init();
		
		// Set vars
		$vars = array(
			'plugin_id' => $this->plugin_id,
			'ListControllers' => $this->Toolbox->ListControllers(),
			'SelectPages' => $this->Toolbox->SelectPages(),
			'SelectSection' => $this->Toolbox->SelectSection(),
			'vars' => (object)$this->Toolbox->GetFile($this->get[1]),
			
		);		
		
		if (!empty($this->post)) {
			// Set the category this file is to be added in
			$data = array(
				'plugin_id' => $this->plugin_id,
				'company_id' => $this->company_id,
				'pages' => $this->post['pages'],
				'type' => $this->post['type'],
				'section' => $this->post['section'],
				'controllers' => $this->post['controllers'],
				'body' => $this->post['body']
			);
			
			// Set vars according to selected items
			if (isset($this->post['type']) && $this->post['type'] == "advanced")
				$data['pages'] = null ;			

			$this->Toolbox->editFile($data , $this->get[1] );
			
			if (($errors = $this->Toolbox->errors())) {
				// Error, reset vars	
				$vars['vars'] = (object)$this->post;				
				$this->parent->setMessage("error", $errors);
			}
			else {
				// Success
				$this->parent->flashMessage("message", Language::_("CssJavascriptToolbox.!success.js_added", true , $this->Html->ifSet($this->post['section'])));
				$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");
			}
		}	
		
		// Set the view to render
		return $this->partial("edit", $vars);
	}	

	/**
	 * Edit FIle
	 */
	public function delete() {
		$this->init();		
		
		if (isset($this->post['file'])) {			
			
			if (!file_exists($this->dir . $this->post['file'])) {			
				$this->parent->setMessage("error", Language::_("CssJavascriptToolbox.!error.delete_file", true , $this->post['file']));
			}
			else {
				// Success
				$this->Toolbox->deleteFile($this->post['file']);
				$this->parent->flashMessage("message", Language::_("CssJavascriptToolbox.!success.delete_file", true , $this->post['file']));
				$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");
			}
		}

		$this->redirect($this->base_uri . "settings/company/plugins/manage/" . $this->plugin_id . "/");	
		
	}	

	
}	
?>