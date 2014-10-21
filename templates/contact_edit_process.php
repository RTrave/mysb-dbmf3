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
        $contact = MySBDBMFContactHelper::create($_POST['lastname'], $_POST['firstname'], $_POST['mail']);
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
    $i_mail = 1;
    $cmails = '';
    while( isset($_POST['mail'.$i_mail]) ) {
        if( $_POST['mail'.$i_mail]!='' ) {
            if( $cmails!='' ) $cmails .= ',';
            $cmails .= $_POST['mail'.$i_mail];
        }
        $i_mail++;
    }
    $contact_datas = array(
        'lastname' => $_POST['lastname'],
        'firstname' => $_POST['firstname'],
        'mail' => $cmails,
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


if(isset($_POST['memento_add'])) {
    if($_POST['memento_type']=='memtype0') $memtype = MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL;
    elseif($_POST['memento_type']=='memtype1') $memtype = MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR;
    $new_memento = MySBDBMFMementoHelper::create($_POST['memento_category'],$_GET['contact_id'],$memtype);
    if($memtype==MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
        $new_memento_date = 
MySBDateTimeHelper::html_formLoad('memento_date_');
        $new_memento->update( array(
            'date_memento' => $new_memento_date->date_string ) );
    } elseif($memtype==MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
        $new_memento->update( array(
            'monthofyear_memento' => $_POST['memento_moy'] ) );
    }
    if( isset($_POST['memento_group_edition']) and 
        $_POST['memento_group_edition']=='on') 
        $group_edition = 1;
    else $group_edition = 0;
    $new_memento->update( array(
        'group_edition' => $group_edition,
        'comments' => $_POST['memento_comments'],
        'comments2' => $_POST['memento_comments2'] ) );
    $app->pushMessage(_G('DBMF_memento_added'));
}

if(isset($_POST['memento_delete'])) {
    MySBDBMFMementoHelper::delete($_POST['memento_delete']);
    $app->pushMessage(_G('DBMF_memento_deleted'));
}

if(isset($_POST['memento_modify'])) {
    if($_POST['memento_type']=='memtype0') $memtype = 0;
    elseif($_POST['memento_type']=='memtype1') $memtype = 1;
    $memento = new MySBDBMFMemento($_POST['memento_modify']);
    $memento->setCategory($_POST['memento_category']);
    if($memtype==MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
        $memento_date = MySBDateTimeHelper::html_formLoad('memento_date_');
        $memento->update( array(
            'date_memento' => $memento_date->date_string,
            'monthofyear_memento' => '' ) );
    } elseif($memtype==MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
        $memento->update( array(
            'monthofyear_memento' => $_POST['memento_moy'],
            'date_memento' => '' ) );
    }
    if( isset($_POST['memento_group_edition']) and 
        $_POST['memento_group_edition']=='on') $group_edition = 1;
    else $group_edition = 0;
    $memento->update( array(
        'type' => $memtype,
        'group_edition' => $group_edition,
        'comments' => $_POST['memento_comments'],
        'comments2' => $_POST['memento_comments2'] ) );
    $app->pushMessage(_G('DBMF_memento_modified'));
}


?>
