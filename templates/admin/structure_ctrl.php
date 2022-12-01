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

if(!MySBRoleHelper::checkAccess('dbmf_config')) return;

// Block operations
if(isset($_POST['addblock_name']) and !empty($_POST['addblock_name'])) {
    MySBDBMFBlockHelper::create($_POST['addblock_name']);
    $app->pushMessage( _G('BDMF_block_created') );
}

if(isset($_POST['block_edition'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_edition']);
    $block->update( array(
        'lname' => $_POST['lname'],
        'groupedit_id' => $_POST['group_id']
    ) );
}

if(isset($_POST['block_orderup'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_orderup']);
    $block->indexUP();
}

if(isset($_POST['block_orderdown'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_orderdown']);
    $block->indexDOWN();
}

if(isset($_POST['block_del'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_del']);
    $app->pushMessage(_G('DBMF_block_deleted').': '._G($block->lname));
    MySBDBMFBlockHelper::delete($_POST['block_del']);
}

//BlockRef operations
if(isset($_POST['blockref_add'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['blockref_add']);
    $blockref = $block->refAdd($_POST['lname'],$_POST['type']);
    $app->tpl_blockref_edit = $blockref;
}

if(isset($_POST['blockref_del'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_id']);
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_del']);
    $app->pushMessage(_G('DBMF_blockref_deleted').': '._G($blockref->lname));
    $block->refDel($_POST['blockref_del']);
}

if(isset($_POST['blockref_edit_process'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_edit_process']);
    $blockref->update( array( 'lname'=>$_POST['lname'] ) );
    if( ($_POST['switchorderby']!=1 and $blockref->orderby==1) or
        ($_POST['switchorderby']==1 and $blockref->orderby!=1) )
        $blockref->switchOrderBy();
    $blockref->setAlwaysShown($_POST['blockref_alwaysshown']);
    $blockref->setInfos($_POST['infos']);
    if( $blockref->type==MYSB_VALUE_TYPE_DATE or
        $blockref->type==MYSB_VALUE_TYPE_DATETIME ) {
        $params = '';
        if($_POST['blockref_ymin']=='') $params = '2011,';
        else $params = $_POST['blockref_ymin'].',';
        if($_POST['blockref_ymax']=='') $params .= '2030';
        else $params .= $_POST['blockref_ymax'];
        $blockref->update( array('params'=>$params) );
    }
    $app->tpl_blockref_edit = $blockref;
}

if(isset($_GET['blockref_edit'])) {
    $app->tpl_blockref_edit = MySBDBMFBlockRefHelper::getByID($_GET['blockref_edit']);
}

if(isset($_POST['blockref_switchactive'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_switchactive']);
    $blockref->statusSwitch();
}

if(isset($_POST['blockref_mod_option'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_edit']);
    $blockref->modSelectOption($_POST['blockref_option_id'], $_POST['blockref_mod_option']);
    $app->tpl_blockref_edit = $blockref;
}

if(isset($_POST['blockref_del_option'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_edit']);
    $blockref->delSelectOption($_POST['blockref_option_id']);
    $app->tpl_blockref_edit = $blockref;
}

if(isset($_POST['blockref_new_option'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_edit']);
    $blockref->addSelectOption($_POST['blockref_new_option']);
    $app->tpl_blockref_edit = $blockref;
}

if(isset($_POST['blockref_orderup'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_orderup']);
    $blockref->indexUP();
}

if(isset($_POST['blockref_orderdown'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_orderdown']);
    $blockref->indexDOWN();
}

include( _pathT('admin/structure','dbmf3') );

?>
