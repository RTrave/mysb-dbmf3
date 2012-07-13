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


if(isset($_POST['addblock_name']) and !empty($_POST['addblock_name'])) {
    MySBDBMFBlockHelper::create($_POST['addblock_name']);
    $app->pushMessage( _G('BDMF_block_created') );
}

if(isset($_POST['block_edit'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_edit']);
    $block->update( array(
        'lname' => $_POST['lname']
    ) );
}

if(isset($_POST['blockref_add'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['blockref_add']);
    $block->refAdd($_POST['lname'],$_POST['type']);
}

if(isset($_POST['blockref_del'])) {
    $block = MySBDBMFBlockHelper::getByID($_POST['block_id']);
    $block->refDel($_POST['blockref_del']);
}

if(isset($_POST['blockref_rename'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_rename']);
    $blockref->update( array( 'lname'=>$_POST['lname'] ) );
}

if(isset($_POST['blockref_switchactive'])) {
    $blockref = MySBDBMFBlockRefHelper::getByID($_POST['blockref_switchactive']);
    $blockref->statusSwitch();
}

?>
