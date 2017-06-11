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

if(!MySBRoleHelper::checkAccess('admin')) return;

if(isset($_POST['group_id'])) {
    $group = MySBDBMFGroupHelper::getByID($_POST['group_id']);
    $group->setPriority($_POST['dbmf_priority']);
}

if(isset($_POST['dbmf_editexport_process'])) {
    $export = MySBDBMFExportHelper::getByID($_POST['dbmf_editexport_process']);
    $export_config = $export->htmlConfigProcess();
    $export->update( array(
        'name' => $_POST['export_name'],
        'comments' => $_POST['export_comments'],
        'group_id' => $_POST['export_groupid'],
        'config' => $export_config
    ));
}

if(isset($_POST['dbmf_addexport'])) {
    if(!empty($_POST['export_name']) and !empty($_POST['export_comments']))
        MySBDBMFExportHelper::create( $_POST['export_name'], $_POST['export_type'], $_POST['export_comments'], '', $_POST['export_groupid'] );
}

if(isset($_POST['dbmf_contact_delete'])) {
    MySBDBMFContactHelper::delete($_POST['dbmf_contact_delete']);
}

if(isset($_POST['dbmf_orphans']) or isset($_POST['dbmf_contact_delete'])) {
    $sql_r = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts ';
    $blockrefs = MySBDBMFBlockRefHelper::load();
    $clause_owner = '';
    foreach($blockrefs as $blockref) {
        if($blockref->block_id!=1) {
            if($clause_owner!='')  $clause_owner .= ' and ';
            $clause_owner .= "(".$blockref->keyname." is null or ".$blockref->keyname."='' )";
        }
    }
	$_SESSION['dbmf_query_select'] = $sql_r;
	$_SESSION['dbmf_query_where'] = $clause_owner;
}

if(isset($_POST['dbmf_autosubs'])) {
    $blockrefs = MySBDBMFBlockRefHelper::load();
    foreach( $blockrefs as $blockref ) {
    //print_r($_POST);
        //echo $blockref->keyname.':'.$_POST[$blockref->keyname].' / ';
        if($blockref->autosubs==1) $autosubs='on';
        else $autosubs='';
        if(isset($_POST[$blockref->keyname]) and $_POST[$blockref->keyname]!=$autosubs)
            $blockref->update( array('autosubs'=>'1') );
        if(!isset($_POST[$blockref->keyname]) and $autosubs=='on')
            $blockref->update( array('autosubs'=>'') );
    }
}

?>
