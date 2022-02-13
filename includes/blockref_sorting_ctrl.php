<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2022
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

$blockref = MySBDBMFBlockRefHelper::getByKeyname($_GET["keyname"]);
//echo $blockref->innerRowWhereClause('br',_G($blockref->lname));
if($blockref)
  echo '
<div class="row">'.
$blockref->innerRowWhereClause('br_',_G($blockref->lname),$blockref->infos,12).'
</div>';
else
  echo _G('DBMF_mementos_bycontact_nokeyname').'<br>';

