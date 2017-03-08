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

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_ijoomlainstaller".DS."helpers".DS."change_version.php");

require_once (JPATH_COMPONENT.DS.'controller.php');
$controller = JRequest::getWord('controller');

global $mainframe;
$mainframe = JFactory::getApplication();

if ($controller) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once($path);
	} else {
	 	$controller = '';
	}

}
$classname = "ijoomlainstallerAdminController".$controller;

$controller = new $classname() ;
$task = JRequest::getWord('task');
$controller->execute ($task);
$controller->redirect();

?>