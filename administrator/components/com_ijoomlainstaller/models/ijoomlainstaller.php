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

jimport ("joomla.aplication.component.model");

class ijoomlainstallerAdminModelijoomlainstaller extends InstallerModel {
	
	function __construct () {
		parent::__construct();
	}
	
	/// strart function added on 08-07-2013 to extract domain
	function extract_domain($domain){
		if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches)){
			return $matches['domain'];
		} 
		else{
			return $domain;
		}
	}
	/// end function
	
	function skipHttp($domain){
		$domain = str_replace("https://", "", $domain);
		$domain = str_replace("http://", "", $domain);
		return $domain;
	}
	
	function getLicenseDetails($component){
		$page_content = "";
		
		$domain = $_SERVER['HTTP_HOST'];
		$domain = $this->skipHttp($domain);
		
		$joomla = "2.5";
		if(version_compare(JVERSION, '3.0', 'ge')){
			$joomla = "3.0";
		}
		
		if(strpos($component, "community") !== FALSE){
			$url_first = "https://www.jomsocial.com/";
			$url = $url_first."index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_details&tmpl=component&format=raw&component=".$component."&domain=localhost&joomla=".$joomla;
			$page_content = file_get_contents($url);
		}
		else{
			$url_first = "https://www.ijoomla.com/";
			$url = $url_first."index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_details&tmpl=component&format=raw&component=".$component."&domain=localhost&joomla=".$joomla;
			$ijoomla_page_content = file_get_contents($url);
			
			if(!isset($ijoomla_page_content) || $ijoomla_page_content == "null"){
				$ijoomla_page_content = "[]";
			}
			
			//-------------------------------------------------------------------------------------
			
			$url_first = "https://www.jomsocial.com/";
			$url = $url_first."index.php?option=com_digistore&controller=digistoreAutoinstaller&task=get_license_details&tmpl=component&format=raw&component=".$component."&domain=localhost&joomla=".$joomla;
			$jomsocial_page_content = file_get_contents($url);
			
			if(!isset($jomsocial_page_content) || $jomsocial_page_content == "null"){
				$jomsocial_page_content = "[]";
			}
			
			
			if(trim($ijoomla_page_content) == "[]" && trim($jomsocial_page_content) == "[]"){
				// no license on none of sites
				$page_content = "[]";
			}
			elseif(trim($ijoomla_page_content) != "[]" && trim($jomsocial_page_content) == "[]"){
				// one license on ijoomla
				$ijoomla_page_content = json_decode($ijoomla_page_content, true);
				$ijoomla_page_content["0"]["source"] = "ijoomla";
				$ijoomla_page_content = json_encode($ijoomla_page_content);
				
				$page_content = $ijoomla_page_content;
			}
			elseif(trim($ijoomla_page_content) == "[]" && trim($jomsocial_page_content) != "[]"){
				// one license on jomsocial
				$jomsocial_page_content = json_decode($jomsocial_page_content, true);
				$jomsocial_page_content["0"]["source"] = "jomsocial";
				$jomsocial_page_content = json_encode($jomsocial_page_content);
				
				$page_content = $jomsocial_page_content;
			}
			elseif(trim($ijoomla_page_content) != "[]" && trim($jomsocial_page_content) != "[]"){
				// two licenses on ijoomla and jomsocial
				$ijoomla_page_content = json_decode($ijoomla_page_content, true);
				$jomsocial_page_content = json_decode($jomsocial_page_content, true);
				
				if($ijoomla_page_content["0"]["published"] == "0" && $jomsocial_page_content["0"]["published"] != "0"){
					// license on ijoomla is unpublished, and on jomsocial is published
					$jomsocial_page_content["0"]["source"] = "jomsocial";
					$page_content = json_encode($jomsocial_page_content);
				}
				elseif($ijoomla_page_content["0"]["published"] != "0" && $jomsocial_page_content["0"]["published"] == "0"){
					// license on ijoomla is published, and on jomsocial is unpublished
					$ijoomla_page_content["0"]["source"] = "ijoomla";
					$page_content = json_encode($ijoomla_page_content);
				}
				elseif($ijoomla_page_content["0"]["published"] == "0" && $jomsocial_page_content["0"]["published"] == "0"){
					// both licenses are unpublished
					$page_content = "[]";
				}
				elseif($ijoomla_page_content["0"]["published"] != "0" && $jomsocial_page_content["0"]["published"] != "0"){
					// both licenses are published
					$joomla_date = JFactory::getDate();
					$today = $joomla_date->toUnix();
					
					$ijoomla_expires = $ijoomla_page_content["0"]["expires"];
					$jomsocial_expires = $ijoomla_page_content["0"]["expires"];
					
					if($ijoomla_expires == "0000-00-00 00:00:00"){
						$ijoomla_page_content["0"]["source"] = "ijoomla";
						$page_content = json_encode($ijoomla_page_content);
					}
					elseif($ijoomla_expires == ""){
						$jomsocial_page_content["0"]["source"] = "jomsocial";
						$page_content = json_encode($jomsocial_page_content);
					}
					elseif(strtotime($ijoomla_expires) < $today){
						$jomsocial_page_content["0"]["source"] = "jomsocial";
						$page_content = json_encode($jomsocial_page_content);
					}
					elseif(strtotime($ijoomla_expires) >= $today){
						$ijoomla_page_content["0"]["source"] = "ijoomla";
						$page_content = json_encode($ijoomla_page_content);
					}
				}
			}
		}
		
		if($page_content == "CURL_NOT_ENABLED"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_CURL_NOT_ENABLED"), 'error');
		}
		elseif($page_content == "NO_LICENCE"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_NO_VALID_LICENSE"), 'error');
		}
		else{
			return $page_content;
		}
	}
	
	function checkLicense(){
		$page_content = "";

		$component = JRequest::getVar("component", "");
			
		$license = JRequest::getVar("license", array(), "post", "array");
		$license = $license[$component];
		
		$version = JRequest::getVar("version", array(), "post", "array");
		$version = $version[$component];
		
		$source = JRequest::getVar("source", array(), "post", "array");
		$source = $source[$component];
		
		$token_installer = JRequest::getVar("token_installer", "");
		
		if(trim($component) == "" || trim($license) == ""){
			$page_content = "NO_LICENCE";
		}
		else{
			$domain = $_SERVER['HTTP_HOST'];
			$domain = $this->skipHttp($domain);
			
			$url_first = "";
			if(strpos($component, "community") !== FALSE){
				$url_first = "https://www.jomsocial.com/";
			}
			else{
				if(trim($source) == "ijoomla"){
					$url_first = "https://www.ijoomla.com/";
				}
				elseif(trim($source) == "jomsocial"){
					$url_first = "https://www.jomsocial.com/";
				}
			}
			
			$joomla = "2.5";
			if(version_compare(JVERSION, '3.0', 'ge')){
				$joomla = "3.0";
			}
			
			$url = $url_first."index.php?option=com_digistore&controller=digistoreAutoinstaller&task=check_license&tmpl=component&component=".$component."&license=".$license."&domain=localhost&token_installer=".$token_installer."&joomla=".$joomla."&version=".$version;
			$page_content = file_get_contents($url);
		}	
		
		if($page_content == "LICENSE_EXPIRED"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_LICENSE_EXPIRED"), 'error');
		}
		elseif($page_content == "CURL_NOT_ENABLED"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_CURL_NOT_ENABLED"), 'error');
		}
		elseif($page_content == "CURL_EXEC_NOT_ENABLED"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_CURL_EXEC_NOT_ENABLED"), 'error');
		}
		elseif($page_content == "NO_LICENSE_FOR_DOMAIN"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_NO_LICENSE_FOR_DOMAIN"), 'error');
		}
		elseif($page_content == "NO_DOMAIN_FOR_LICENSE"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_NO_DOMAIN_FOR_LICENSE"), 'error');
		}
		elseif($page_content == "NO_LICENCE"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_NO_VALID_LICENSE"), 'error');
		}
		else{
			$_SESSION["licenseid"] = trim($license);
			echo $page_content;
		}
	}
	
	function renew(){
		$page_content = "";
		
		$component = JRequest::getVar("component", "");
		$license = JRequest::getVar("license", array(), "request", "array");
		$license = $license[$component];
		$token_installer = JRequest::getVar("token_installer", "");
		
		if(trim($component) == "" || trim($license) == ""){
			$page_content = "NO_LICENCE";
		}
		else{
			$domain = $_SERVER['HTTP_HOST'];
			$domain = $this->skipHttp($domain);
			//$domain = $this->extract_domain($domain);

			$url_first = "";
			if(strpos($component, "community") !== FALSE){
				$url_first = "https://www.jomsocial.com/";
			}
			else{
				if(trim($source) == "ijoomla"){
					$url_first = "https://www.ijoomla.com/";
				}
				elseif(trim($source) == "jomsocial"){
					$url_first = "https://www.jomsocial.com/";
				}
			}

			$url = $url_first."index.php?option=com_digistore&controller=digistoreAutoinstaller&task=renew&tmpl=component&component=".$component."&license=".$license."&domain=localhost&token_installer=".$token_installer;
			$page_content = file_get_contents($url);
		}
		
		if($page_content == "CURL_NOT_ENABLED"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_CURL_NOT_ENABLED"), 'error');
		}
		elseif($page_content == "NO_LICENCE"){
			$app = JFactory::getApplication("admin");
			$app->redirect("index.php?option=com_ijoomlainstaller", JText::_("IJOOMLA_INSTALLER_NO_VALID_LICENSE"), 'error');
		}
		else{
			echo $page_content;
		}
	}

};
?>