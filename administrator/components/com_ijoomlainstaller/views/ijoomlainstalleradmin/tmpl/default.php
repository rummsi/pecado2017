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

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_ijoomlainstaller/css/ijoomla_installer.css");

if(version_compare(JVERSION, '3.0', 'ge')){
	$list_components = array("adagency"=>"Ad Agency Pro", "ijoomla_seo"=>"iSEO Pro", "publisher"=>"Publisher Pro", "guru"=>"Guru Pro", "community_std"=>"JomSocial Standard", "community_pro"=>"JomSocial Professional", "community_dev"=>"JomSocial Developer");
}
else{
	// check if is Joomla 2.5 and we have Jomsocial 3.0 or 2.6/2.8
	$component = "community";
	$list_components = array();
	
	if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_std.php")){
		$list_components = array("community_std"=>"JomSocial Standard");
	}
	elseif(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_pro.php")){
		$list_components = array("community_pro"=>"JomSocial Professional");
	}
	elseif(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_".$component.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."community_dev.php")){
		$list_components = array("community_dev"=>"JomSocial Developer");
	}
	else{
		$list_components = array("community_std"=>"JomSocial Standard", "community_pro"=>"JomSocial Professional", "community_dev"=>"JomSocial Developer");
	}
}

$exist_at_least_one_license = FALSE;
$old_jomsocial_version = FALSE;

?>

<style type="text/css">
	.label{
		height: 15px !important;
	}
</style>

<fieldset>
	<form id="adminForm" name="adminForm" method="post" action="index.php">
    	<div class="row-fluid">
        	<div class="span12" id="no_licenses_msg" style="display:none;">
            	<div class="alert alert-error">
					<p><?php
							if(strpos($_SERVER["HTTP_HOST"], "localhost") !== FALSE){
								echo JText::_("IJOOMLA_INSTALLER_NO_LICENSES_FOR_EXTENSIONS_ON_LOCALHOST");
							}
							else{
                    			echo JText::_("IJOOMLA_INSTALLER_NO_LICENSES_FOR_EXTENSIONS");
							}
						?>
					</p>
                </div>
            </div>
        </div>
        
        <div class="row-fluid">
        	<div class="span12" id="upgrade_first_msg" style="display:none;">
            	<div class="alert alert-error upgrade-jomsocial-msg">
					<p>
						<?php
							echo JText::_("IJOOMLA_INSTALLER_UPGRADE_FROM_26_28");
						?>
					</p>
                </div>
            </div>
        </div>
        
        <table class="table table-striped table25">
        	<thead>
            	<tr>
                	<th>
                    	<?php echo JText::_("IJOOMLA_INSTALLER_EXTENSION"); ?>
                    </th>
                    <th>
                    	<?php echo JText::_("IJOOMLA_INSTALLER_INSTALLED_VERSION"); ?>
                    </th>
                    <th>
                    	<?php
                        	if(version_compare(JVERSION, '3.0', 'ge')){
								echo JText::_("IJOOMLA_INSTALLER_LATEST_VERSION");
							}
							else{
								echo JText::_("IJOOMLA_INSTALLER_AVAILABLE_VERSION");
							}
						?>
                    </th>
                    <th>
                    	<?php echo JText::_("IJOOMLA_INSTALLER_STATUS"); ?>
                    </th>
                    <th>
                    	<?php echo JText::_("IJOOMLA_INSTALLER_LICENSE"); ?>
                        <span class="adag_tip">
							<img src="components/com_ijoomlainstaller/images/tooltip.png" border="0" />
							<span><?php echo JText::_('IJOOMLA_INSTALLER_LICENSE_TIP'); ?></span>
                        </span>
                    </th>
                    <th>
                    	<?php echo JText::_("IJOOMLA_INSTALLER_EXPIRE_DATE"); ?>
                    </th>
                    <th>
                    	<?php echo JText::_("IJOOMLA_INSTALLER_INSTALL_UPGRADE"); ?>
                    </th>
                    <th style="text-align:right">
                    	<?php echo JText::_("IJOOMLA_INSTALLER_BUY_RENEW"); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            	<?php
                	foreach($list_components as $key=>$value){
						$installed_version = $this->getInstalledVersion($key);
						$do_not_continue = FALSE;
						$colspan = "1";
						
						if(version_compare(JVERSION, '3.0', 'ge')){
							// do nothing
						}
						else{
							$very_old_version = substr($installed_version, 0, 3);
							$very_old_version = str_replace(".", "", $very_old_version);
							
							if(strpos($key, "community") !== FALSE && (intval($very_old_version) < 25) && (intval($very_old_version) != 0)){
								$do_not_continue = TRUE;
								$colspan = "4";
							}
						}
				?>
                        <tr>
                            <td>
                            	<?php
                                	$link = "http://".$key.".ijoomla.com/";
									if($key == "community_std" || $key == "community_pro" || $key == "community_dev"){
										$link = "http://www.jomsocial.com";
										if(strpos($installed_version, "2.6") !== FALSE){
											$old_jomsocial_version = TRUE;
										}
									}
								?>
                                <a href="<?php echo $link; ?>" target="_blank"><?php echo $value; ?></a>
                            </td>
                            <td>
                            	<?php
                                	echo $installed_version;
								?>
                            </td>
                            <td>
                            	<?php
									$installed_version_var = "0";
									if(version_compare(JVERSION, '3.0', 'ge')){
										$installed_version_var = "0";
									}
									else{
										$installed_version_var = $installed_version;
									}
									
                                	$latest_version = $this->getLatestVersion($key, $installed_version_var);
									echo $latest_version;
								?>
                            </td>
                            <td nowrap="nowrap">
                            	<?php
									if(trim($installed_version) == ""){
										echo '<span class="label label-warning"><span class="span-label pagination-centered">'.JText::_("IJOOMLA_INSTALLER_NOT_INSTALLED").'</span></span>';
									}
									elseif(trim($installed_version) == trim($latest_version)){
										echo '<span class="label label-success"><span class="span-label pagination-centered">'.JText::_("IJOOMLA_INSTALLER_UP_TO_DATE").'</span></span>';
									}
									elseif(trim($installed_version) != trim($latest_version)){
										echo '<span class="label label-important"><span class="span-label pagination-centered">'.JText::_("IJOOMLA_INSTALLER_UPGRADE_REQUIRED").'</span></span>';
									}
								?>
                            </td>
                            <td colspan="<?php echo $colspan; ?>">	
                            	<?php
                                	if($do_not_continue && $installed_version != ""){
										echo '<div class="old-message">'.JText::_("IJOOMLA_INSTALLER_OLD_VERSION")." <a href='http://tiny.cc/upgradeto26' target='_blank'>".JText::_("IJOOMLA_INSTALLER_CONTACT_US").'.</a></div>';
										echo "</td></tr>";
										continue;
									}
									
									$license_details = $this->getLicenseDetails($key);

									if(strpos($_SERVER["HTTP_HOST"], "localhost") !== FALSE){
										$license_details = "[]";
									}
									
									if(trim($license_details) != "[]"){
										$exist_at_least_one_license = TRUE;
									}
									
									$license_number = "";
									$expire_date = "";
									$version = "";
									if(trim($license_details) != "" && trim($license_details) != "[]"){
										$license_details = json_decode($license_details, true);
										
										if(version_compare(JVERSION, '3.0', 'ge')){
											// do nothing
										}
										else{
											if(isset($list_components["community_std"]) && isset($list_components["community_pro"]) && isset($list_components["community_dev"])){
												if($key == "community_std" && $license_details["0"]["productid"] != 1){
													$license_details = array();
												}
												
												if($key == "community_pro" && $license_details["0"]["productid"] != 2){
													$license_details = array();
												}
												
												if($key == "community_dev" && $license_details["0"]["productid"] != 4){
													$license_details = array();
												}
											}
										}
										
										$license_number = $license_details["0"]["licenseid"];
										$expire_date = $license_details["0"]["expires"];
										$version = $installed_version;
									}
									else{
										$license_details = array();
										$version = $installed_version;
									}
									
									$joomla_date = JFactory::getDate();
									$today = $joomla_date->toUnix();
									$expired = FALSE;
									
                                	if($expire_date == "0000-00-00 00:00:00"){
										echo '<input type="text" name="license['.$key.']" value="'.$license_number.'" />';
									}
									elseif($expire_date == ""){
										echo '<input type="text" name="license['.$key.']" value="" />';
									}
									elseif(strtotime($expire_date) < $today){
										echo '<input type="text" name="license['.$key.']" value="'.$license_number.'" />';
									}
									elseif(strtotime($expire_date) >= $today){
										echo '<input type="text" readonly="readonly" name="readonly" value="'.$license_number.'" />';
										echo '<input type="hidden" name="license['.$key.']" value="'.$license_number.'" />';
									}
									
									echo '<input type="hidden" name="version['.$key.']" value="'.$version.'" />';
									echo '<input type="hidden" name="source['.$key.']" value="'.@$license_details["0"]["source"].'" />';
								?>
                            </td>
                            <td>
                            	<?php
									$joomla_date = JFactory::getDate();
									$today = $joomla_date->toUnix();
									$expired = FALSE;
									
                                	if($expire_date == "0000-00-00 00:00:00"){
										echo '<span class="text-gray">'.JText::_("IJOOMLA_INSTALLER_NEVER").'</span>';
									}
									elseif($expire_date == ""){
									}
									elseif(strtotime($expire_date) < $today){
										echo '<span class="text-red">'.date("m-d-Y", strtotime($expire_date)).'</span>';
										$expired = TRUE;
									}
									elseif(strtotime($expire_date) >= $today){
										echo '<span class="text-gray">'.date("m-d-Y", strtotime($expire_date)).'</span>';
									}
								?>
                            </td>
                            <td>
                            	<?php
                                	if($installed_version == ""){// not installed
										echo '<input type="submit" onclick="document.adminForm.component.value=\''.$key.'\'" class="btn btn-primary" value="'.JText::_("IJOOMLA_INSTALLER_INSTALL").'" />';
									}
									elseif(trim($installed_version) == trim($latest_version) && $expired){ // up-to-date and expired
										echo '<input type="submit" onclick="document.adminForm.component.value=\''.$key.'\'" class="btn" value="'.JText::_("IJOOMLA_INSTALLER_INSTALL").'" />';
									}
									elseif(trim($installed_version) != trim($latest_version) && $expired === FALSE){ // not up-to-date and not expired
										echo '<input type="submit" onclick="document.adminForm.component.value=\''.$key.'\'" class="btn btn-danger" value="'.JText::_("IJOOMLA_INSTALLER_UPGRADE").'" />';
									}
									elseif(trim($installed_version) == trim($latest_version) && $expired === FALSE){ // up-to-date and not expired
										echo '<input type="submit" onclick="document.adminForm.component.value=\''.$key.'\'" class="btn btn-primary" value="'.JText::_("IJOOMLA_INSTALLER_OVERRIDE").'" />';
									}
									elseif(trim($installed_version) != trim($latest_version) && $expired === TRUE){ // not up-to-date and expired
										echo '<input type="submit" onclick="document.adminForm.component.value=\''.$key.'\'" class="btn" value="'.JText::_("IJOOMLA_INSTALLER_UPGRADE").'" />';
									}
								?>
                            </td>
                            <td style="text-align:right; vertical-align: middle !important;">
                            	<?php
                                	if($installed_version == ""){// not installed
										echo JText::_("IJOOMLA_INSTALLER_DO_NOT_HAVE")."&nbsp;";
										
										$link = "";
										if($key == "adagency"){
											$link = "http://adagency.ijoomla.com/pricing/";
										}
										elseif($key == "ijoomla_seo"){
											$link="http://seo.ijoomla.com/pricing/";
										}
										elseif($key == "guru"){
											$link="http://guru.ijoomla.com/pricing/";
										}
										elseif($key == "publisher"){
											$link="http://publisher.ijoomla.com/pricing/";
										}
										elseif($key == "community_std"){
											$link = "http://www.jomsocial.com/pricing?utm_source=customer&utm_medium=installer&utm_content=jomsocial&utm_campaign=standard";
										}
										elseif($key == "community_pro"){
											$link = "http://www.jomsocial.com/pricing?utm_source=customer&utm_medium=installer&utm_content=jomsocial&utm_campaign=professional";
										}
										elseif($key == "community_dev"){
											$link = "http://www.jomsocial.com/pricing?utm_source=customer&utm_medium=installer&utm_content=jomsocial&utm_campaign=developer";
										}
										
										echo '<input type="button" class="btn btn-warning buy-now" value="'.JText::_("IJOOMLA_INSTALLER_BUY_NOW").'" onclick="window.open(\''.$link.'\', \'_blank\')" />';
									}
									elseif(trim($installed_version) == trim($latest_version) && $expired){ // up-to-date and expired
										echo JText::_("IJOOMLA_INSTALLER_RENEW_FOR_DISCOUNT")."&nbsp;";
										echo '<input type="button" class="btn btn-warning buy-now" value="'.JText::_("IJOOMLA_INSTALLER_RENEW").'" onclick="window.open(\''.JURI::root().'administrator/index.php?option=com_ijoomlainstaller&controller=ijoomlainstaller&task=renew&license['.$key.']='.$license_number.'&component='.$key.'\', \'_blank\')" />';
									}
									elseif(trim($installed_version) != trim($latest_version) && $expired === FALSE){ // not up-to-date and not expired
										
									}
									elseif(trim($installed_version) == trim($latest_version) && $expired === FALSE){ // up-to-date and not expired
										
									}
									elseif(trim($installed_version) != trim($latest_version) && $expired === TRUE){ // not up-to-date and expired
										echo JText::_("IJOOMLA_INSTALLER_RENEW_FOR_DISCOUNT")."&nbsp;";
										echo '<input type="button" class="btn btn-warning buy-now" value="'.JText::_("IJOOMLA_INSTALLER_RENEW").'" onclick="window.open(\''.JURI::root().'administrator/index.php?option=com_ijoomlainstaller&controller=ijoomlainstaller&task=renew&license['.$key.']='.$license_number.'&component='.$key.'\', \'_blank\')" />';
									}
								?>
                            </td>
                        </tr>
                <?php
                	}
				?>
            </tbody>
        </table>
		
        <?php
        	if(!$exist_at_least_one_license){
		?>        
				<script type="text/javascript" language="javascript">
					document.getElementById("no_licenses_msg").style.display = "block";
                </script>
        <?php
        	}
			
			if($old_jomsocial_version){
		?>
        		<script type="text/javascript" language="javascript">
					document.getElementById("upgrade_first_msg").style.display = "block";
                </script>
        <?php
			}
		?>
        
        <input type="hidden" name="controller" value="ijoomlainstaller" />
        <input type="hidden" name="option" value="com_ijoomlainstaller" />
        <input type="hidden" name="task" value="check_license" />
        <input type="hidden" name="component" value="" />
        <input type="hidden" name="token_installer" value="<?php echo JSession::getFormToken(); ?>" />
	</form>
</fieldset>