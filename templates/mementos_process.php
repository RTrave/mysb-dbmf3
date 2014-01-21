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


if(isset($_POST['memento_process'])) {
    //echo 'ID:'.$_POST['memento_process'].'<br>';
    $memento = new MySBDBMFMemento($_POST['memento_process']);
    $memento->process();
    $app->pushMessage(_G('DBMF_memento_processed'));
}

if(isset($_POST['memento_unprocess'])) {
    //echo 'ID:'.$_POST['memento_process'].'<br>';
    $memento = new MySBDBMFMemento($_POST['memento_unprocess']);
    $memento->unprocess();
    $app->pushMessage(_G('DBMF_memento_unprocessed'));
}


?>
