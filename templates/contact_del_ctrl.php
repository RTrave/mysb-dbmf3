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

if(isset($_POST['dbmf_contact_delete'])) {
    $app->dbmf_hidemementos = array();
    $mementos = MySBDBMFMementoHelper::load($_POST['dbmf_contact_delete']);
    foreach($mementos as $memento) 
        $app->dbmf_hidemementos[] = $memento->id;
    MySBDBMFContactHelper::delete($_POST['dbmf_contact_delete']);
    $app->pushMessage(_G('DBMF_contact_deleted'));
}

include( _pathT('contact_del','dbmf3') );

?>
