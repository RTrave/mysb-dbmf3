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



?>
