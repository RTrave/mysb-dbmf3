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

if(!MySBRoleHelper::checkAccess('dbmf_editor')) return;

if(isset($_GET['contact_id'])) {
    if($_GET['contact_id']==-1) {
        $new_contact = MySBDBMFContact::create($_GET['lastname'], $_GET['firstname']);
        echo $new_contact->lastname.' '.$new_contact->firstname.' created!<br>';
    } else {
        
    }
}


?>
