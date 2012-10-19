<?php 
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2012
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

if(isset($_POST['contact_delete'])) {
    MySBDBMFContactHelper::delete($_POST['contact_delete']);
    $app->pushMessage(_G('DBMF_contact_deleted'));
}

if(isset($_GET['contact_id'])) {
    if($_GET['contact_id']==-1 and MySBRoleHelper::checkAccess('dbmf_editor',false)) {
        $contact = MySBDBMFContactHelper::create($_GET['lastname'], $_GET['firstname']);
        $app->pushMessage(_G('DBMF_contact_added'));
    } else {
        $contact = new MySBDBMFContact($_GET['contact_id']);
    }
    $app->tpl_currentcontact = $contact;
} else {
    
}

if(isset($_POST['contact_edit']) and MySBRoleHelper::checkAccess('dbmf_editor',false)) {

    $pluginsEvent = MySBPluginHelper::loadByType('DBMFEvent');
    foreach($pluginsEvent as $plugin) 
        $plugin->contactUpdate($app->tpl_currentcontact);

    $today = getdate();
    $today_date = $today['year'].'-'.$today['mon'].'-'.$today['mday'].' '.
                  $today['hours'].':'.$today['minutes'].':'.$today['seconds'];
    $contact_datas = array(
        'lastname' => $_POST['lastname'],
        'firstname' => $_POST['firstname'],
        'date_modif' => $today_date );
    $blocks = MySBDBMFBlockHelper::load();
    foreach($blocks as $block) {
        $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
        foreach($block->blockrefs as $blockref) {
            $contact_datas[$blockref->keyname] = $blockref->htmlProcessValue('blockref');
        }
    }
    $contact->update($contact_datas);
    $app->pushMessage(_G('DBMF_contact_modified'));
}


?>
