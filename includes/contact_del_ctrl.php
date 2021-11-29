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

if(isset($_GET['contact_delete'])) {
  echo '
<script>
desactiveOverlay();';
  $mementos = MySBDBMFMementoHelper::load($_GET['contact_delete']);
  foreach($mementos as $memento) {
    echo '
hide("memento'.$memento->id.'");';
  }
  MySBDBMFContactHelper::delete($_GET['contact_delete']);
  $app->pushMessage(_G('DBMF_contact_deleted'));
  echo '
slide_hide("contact'.$_GET['contact_delete'].'");
</script>';
}

if(isset($_GET['callback'])) {
    echo '
<script>
loadItem("'.$_GET['callback_inc'].$_GET['callback_dat'].'",'.
  '"index.php?mod='.$_GET['callback'].
  '&inc='.$_GET['callback_inc'].
  '&id='.$_GET['callback_dat'].'");
</script>';
}

?>
