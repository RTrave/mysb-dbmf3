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

if(isset($_POST['group_id'])) {
    $group = new MySBDBMFGroup($_POST['group_id']);
    $group->setPriority($_POST['dbmf_priority']);
}

if(isset($_POST['dbmf_addexport'])) {
    if(!empty($_POST['export_name']) and !empty($_POST['export_comments']))
        MySBDBMFExportHelper::create( $_POST['export_name'], $_POST['export_type'], $_POST['export_comments'], $_POST['export_config'], $_POST['export_groupid'] );
}

?>
