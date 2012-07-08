<?php 
/***************************************************************************
 *
 *   phpMySandBox/RSVP module - TRoman<abadcafe@free.fr> - 2012
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if(!MySBRoleHelper::checkAccess('dbmf_editor')) return;

if(isset($_POST['lastname'])) {
if(empty($_POST['lastname'])) {
    $app->pushMessage(_G("DBMF_addcontact_lastname_required"));
} else {
    $sql_wcheck = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts '.
        'WHERE lastname RLIKE \''.MySBUtil::str2whereclause($_POST['lastname']).'\' ';
	if($_POST['firstname']!='') 
	    $sql_wcheck = $sql_wcheck.' OR '.
	        'firstname RLIKE \''.MySBUtil::str2whereclause($_POST['firstname']).'\' ';
	$sql_wcheck = $sql_wcheck.' ORDER by lastname;';
	$app->dbmf_req_wcheck = MySBDB::query($sql_wcheck,
	    "addcontact_process.php",
	    true, "dbmf3");
}
}

?>
