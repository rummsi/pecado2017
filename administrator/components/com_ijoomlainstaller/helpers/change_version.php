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

if(version_compare(JVERSION, '3.0', 'ge')){
	class InstallerModel extends JModelLegacy{}
	class InstallerController extends JControllerLegacy{}
	class InstallerView extends JViewLegacy{}
}
else{
	$current_version = JVERSION;
	$current_version = str_replace("2.5", "25", $current_version);
	
	$compare = "25.5";
	$current_version_array = explode(".", $current_version);
	if(strlen($current_version_array["1"]) > 1){
		$compare = "25.05";
	}
	
	if((float)$current_version > (float)$compare){
		jimport('joomla.application.component.modeladmin');
		jimport('joomla.application.component.controllerform');
		
		class InstallerModel extends JModelAdmin{
			public function getForm($data = array(), $loadData = true){
				// do nothing
			}
		}
		
		class InstallerController extends JControllerForm{
			
		}
		
		class InstallerView extends JViewLegacy{
		
		}
	}
	else{
		class InstallerModel extends JModel{}
		class InstallerController extends JController{}
		class InstallerView extends JView{}
	}
}

?>