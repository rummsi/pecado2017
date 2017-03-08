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

jimport ("joomla.application.component.view");

class ijoomlainstallerAdminViewijoomlainstallerAdmin extends InstallerView {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('COM_IJOOMLAINSTALLER'), 'generic.png');
		parent::display($tpl);
	}
	
	function getInstalledVersion($component){
		$xml_file = "";
		switch($component){
			case "adagency" : {
				$xml_file = "adagency.xml";
				break;
			}
			case "guru" : {
				$xml_file = "install.xml";
				break;
			}
			case "ijoomla_seo" : {
				$xml_file = "ijoomla_seo.xml";
				break;
			}
			case "publisher" : {
				$xml_file = "install.publisher.xml";
				break;
			}
			case "community_std" : {
				$component = "community";
				
				if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_version.php")){
					// Joomla 2.5, do nothing
				}
				else{ // Joomla 3.0
					include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_version.php");
					if(COMMUNITY_INSTALLER_VERSION != "std"){
						return "";
					}
				}
				$xml_file = "community.xml";
				break;
			}
			case "community_pro" : {
				$component = "community";
				
				if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_version.php")){
					// Joomla 2.5, do nothing
				}
				else{ // Joomla 3.0
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_version.php");
					if(COMMUNITY_INSTALLER_VERSION != "pro"){
						return "";
					}
				}
				
				$xml_file = "community.xml";
				break;
			}
			case "community_dev" : {
				$component = "community";
				
				if(!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_version.php")){
					// Joomla 2.5, do nothing
				}
				else{ // Joomla 3.0
					include_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_version.php");
					if(COMMUNITY_INSTALLER_VERSION != "dev"){
						return "";
					}
				}
				
				$xml_file = "community.xml";
				break;
			}
		}
		$path = JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR.$xml_file;

		if(file_exists($path)){
			$data = implode("", file($path));
			$pos1 = strpos($data,"<version>");
			$pos2 = strpos($data,"</version>");
			$version = substr($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));
			return $version;
		}
		return "";
	}
	
	function getLatestVersion($component, $installed_version){
		$component_name = "";
		$data = "";
		
		switch($component){
			case "adagency" : {
				$component_name = "com_adagency";
				$data = 'http://www.ijoomla.com/ijoomla_latest_version.txt';
				break;
			}
			case "guru" : {
				$component_name = "com_guru";
				$data = 'http://www.ijoomla.com/ijoomla_latest_version.txt';
				break;
			}
			case "ijoomla_seo" : {
				$component_name = "com_ijoomla_seo";
				$data = 'http://www.ijoomla.com/ijoomla_latest_version.txt';
				break;
			}
			case "publisher" : {
				$component_name = "com_publisher";
				$data = 'http://www.ijoomla.com/ijoomla_latest_version.txt';
				break;
			}
			case "community_std" : {
				$component_name = "com_community_std";
				$data = 'http://www.jomsocial.com/ijoomla_latest_version.txt';
				break;
			}
			case "community_pro" : {
				$component_name = "com_community_pro";
				$data = 'http://www.jomsocial.com/ijoomla_latest_version.txt';
				break;
			}
			case "community_dev" : {
				$component_name = "com_community_dev";
				$data = 'http://www.jomsocial.com/ijoomla_latest_version.txt';
				break;
			}
		}
		
		$version = "";
		$version = file_get_contents($data);
		
		if(isset($version) && trim($version) != ""){
			$pattern = "";
			if(version_compare(JVERSION, '3.0', 'ge')){
				$pattern = "/3.0_".$component_name."=(.*);/msU";
			}
			else{
				$pattern = "/1.6_com_community=(.*);/msU";
			}

			if($installed_version != 0 && $installed_version != ""){// on Joomla 2.5 and need to check available version, 2.8 or 3.0
				if(strpos($installed_version, "2.6") !== FALSE){
					$pattern = "/1.6_com_community=(.*);/msU";
				}
				elseif(strpos($installed_version, "2.8") !== FALSE){
					$pattern = "/3.0_com_community_std=(.*);/msU";
				}
				else{
					$pattern = "/3.0_".$component_name."=(.*);/msU";
				}
			}
			else{
				$pattern = "/3.0_".$component_name."=(.*);/msU";
			}
			
			preg_match($pattern, $version, $result);
			
			if(is_array($result) && count($result) > 0){
				$version = trim($result["1"]);
			}
			else{
				$version = "";
			}
			
			if(version_compare(JVERSION, '3.0', '<')){
				if($component == "community_std" || $component == "community_pro" || $component == "community_dev"){
					$version = "4.1.5";
				}
			}
			
			return $version;
		}
		return false;
	}
	
	function getLicenseDetails($component){
		JLoader::import('joomla.application.component.model');
		$model = NULL;
		if(version_compare(JVERSION, '3.0', 'ge')){
			$model = JModelLegacy::getInstance('ijoomlainstaller', 'ijoomlainstallerAdminModel');
		}
		else{
			$model = JModel::getInstance('ijoomlainstaller', 'ijoomlainstallerAdminModel');
		}
		$license_details = $model->getLicenseDetails($component);
		return $license_details;
	}

}

?>