<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport ('joomla.application.component.controller');

class ijoomlainstallerAdminControllerijoomlainstaller extends InstallerController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask("", "listComponents");
		$this->registerTask("check_license", "checkLicense");
		$this->registerTask("renew", "renew");
	}
	
	function listComponents(){
		$view = $this->getView();
		$view->setModel($this->getModel());
        $view->display();
	}
	
	function checkLicense(){
		$model = $this->getModel("ijoomlainstaller");
		$model->checkLicense();
	}
	
	function renew(){
		$model = $this->getModel("ijoomlainstaller");
		$model->renew();
	}
};
?>