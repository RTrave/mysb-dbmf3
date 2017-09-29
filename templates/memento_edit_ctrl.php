<?php 
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2013
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if( !MySBRoleHelper::checkAccess('dbmf_editor') ) return;

if(isset($_POST['memento_add'])) {

    if($_POST['memento_type']=='memtype0') $memtype = MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL;
    elseif($_POST['memento_type']=='memtype1') $memtype = MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR;
    $memento = MySBDBMFMementoHelper::create($_POST['memento_category'],$_POST['memento_add'],$memtype);
    if($memtype==MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
        $new_memento_date = 
MySBDateTimeHelper::html_formLoad('memento_date_');
        $memento->update( array(
            'date_memento' => $new_memento_date->date_string,
            'monthofyear_memento' => '' ) );
    } elseif($memtype==MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
        $memento->update( array(
            'date_memento' => '',
            'monthofyear_memento' => $_POST['memento_moy'] ) );
    }
    if( isset($_POST['memento_group_edition']) and 
        $_POST['memento_group_edition']=='on') 
        $group_edition = 1;
    else $group_edition = 0;
    $memento->update( array(
        'group_edition' => $group_edition,
        'comments' => $_POST['memento_comments'],
        'comments2' => $_POST['memento_comments2'] ) );
    $app->pushMessage(_G('DBMF_memento_added'));
    $contact = new MySBDBMFContact($memento->contact_id);

}elseif( isset($_GET['memento_id']) ) {
    $memento = new MySBDBMFMemento($_GET['memento_id']);
    $contact = new MySBDBMFContact($memento->contact_id);
} elseif( isset($_GET['contact_id']) ) {
    $contact = new MySBDBMFContact($_GET['contact_id']);
    $memento = new MySBDBMFMemento(null,array("user_id"=>$contact->id));
    $memento->id = -1;
}


if(isset($_POST['memento_delete'])) {
    MySBDBMFMementoHelper::delete($_POST['memento_delete']);
    $app->pushMessage(_G('DBMF_memento_deleted'));
}

if( isset($_POST['memento_modify']) ) {
    if($_POST['memento_type']=='memtype0') $memtype = 0;
    elseif($_POST['memento_type']=='memtype1') $memtype = 1;
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

if(isset($_POST['memento_process'])) {
    $memento->process();
    $app->pushMessage(_G('DBMF_memento_processed'));
}

if(isset($_POST['memento_unprocess'])) {
    $memento->unprocess();
    $app->pushMessage(_G('DBMF_memento_unprocessed'));
}

$app->tpl_dbmf_currentmemento = $memento;
$app->tpl_dbmf_currentcontact = $contact;

include( _pathT('memento_edit','dbmf3') );

?>
